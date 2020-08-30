<?php
function sitemile_filter_ttl($title){return("Employee Expense View");}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	
	$get = $_GET['ID'];
	$details = explode("-",$get);
	$expense_report = $details[0];
	$billable = $details[2];
	$project = $details[1];

	if(isset($_POST['delete-info']))
	{
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$expense_report));
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
							<?php 
							echo "This expense has been deleted<br/><br/>";
							echo '<a href="/new-employee-expense/">Enter a new Expense Report</a><br/><br/>';
							echo '<a href="/my-employee-expenses/">View all your saved and submitted expenses.</a><br/><br/>';
							echo '<a href="/dashboard/">Return to your Dashboard</a>';
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
	}
	elseif(isset($_POST['approve_info']) or isset($_POST['reject-info']))
	{
		if(isset($_POST['approve_info']))
		{
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set set_for_approval=1 
				where expense_report_id=%d",$expense_report));
		}
		else
		{
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set set_for_approval=0,employee_expense_status=0 
				where expense_report_id=%d",$expense_report));
		}
		wp_redirect("/expense-approvals/");
	}
	else
	{ ?> 
		<form method="post"  enctype="multipart/form-data">
		<div id="main_wrapper">
			<div id="main" class="wrapper">
			<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">
						<?php
						$queryedit = $wpdb->prepare("select employee_id,employee_expense_status,ee_mastercard,display_name,expense_date,project_id,expense_submit_date,
							employee_expense_id,expense_type_id,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,expense_billable,
							employee_expense_notes,personal_project,".$wpdb->prefix."projects.gp_id,expense_code_name
							from ".$wpdb->prefix."employee_expenses 
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
							inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
							left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."useradd.user_id
							left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
							where expense_report_id=%d",$expense_report);
						if(!empty($billable) and !empty($project) and ($uid==94 or $uid==235))
						{
							$queryedit = $wpdb->prepare("select employee_id,employee_expense_status,ee_mastercard,display_name,expense_date,project_id,expense_submit_date,
							employee_expense_id,expense_type_id,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,expense_billable,
							employee_expense_notes,personal_project,".$wpdb->prefix."projects.gp_id,expense_code_name
							from ".$wpdb->prefix."employee_expenses 
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
							inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
							left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."useradd.user_id
							left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
							where expense_report_id=%d and project_id=%s and expense_billable=%d and billed_status=1",$expense_report,$project,$billable);
						}
						elseif(!empty($billable) and !empty($project) and $uid!=94)
						{
							$queryedit = $wpdb->prepare("select employee_id,employee_expense_status,ee_mastercard,display_name,expense_date,project_id,expense_submit_date,
							employee_expense_id,expense_type_id,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,expense_billable,
							employee_expense_notes,personal_project,".$wpdb->prefix."projects.gp_id,expense_code_name
							from ".$wpdb->prefix."employee_expenses 
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
							inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
							left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."useradd.user_id
							left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
							where expense_report_id=%d and project_id=%s and expense_billable=%d",$expense_report,$project,$billable);
						}
						$details = $wpdb->get_results($queryedit);
						$expense_owner = $details[0]->employee_id;
						$expense_status = $details[0]->employee_expense_status;
						$display_name = $details[0]->display_name;
						
						if(!empty($details[0]->personal_project)){$personal_project = $details[0]->personal_project;}
						$total = 0;
						$admins = array(94,11);
						if(in_array($uid,$admins))
						{
							echo '<h3><a href="'.get_bloginfo('siteurl').'/?p_action=edit_employee_expense_admin&ID='.$expense_report.'" 
								class="my-buttons-submit" style="color:white;">Edit Expense Report</a></h3>';
						}
						?>
						<h3><?php echo "Employee:  ".$display_name;?></h3>
						<h3><u><?php echo "Report ID: ".$expense_report.($details[0]->ee_mastercard ==1 ? " (MasterCard)" : 
							($details[0]->ee_mastercard==2 ? " (AMEX)" : ""));?></u></h3>
							<li><table width="100%">
								<tr>
								<th><u>Date</u></th>
								<th><u>Project</u></th>
								<th><u>Expense</u></th></tr>
								<tr><th>Quantity</th>
								<th>Amount</th>
								<th>Billable</th></tr>
							</table></li>
							<li>Notes</li>							
					<?php 
					foreach($details as $detail)
					{
						$expense_id = $detail->employee_expense_id;
						$date = date('m-d-Y',$detail->expense_date);
						$project_id = $detail->project_id;
						$expense_type_id = $detail->expense_type_id;
						$expense_quantity = $detail->expense_quantity;
						if($expense_type_id == 28){$expense_amount = "$".$detail->expense_amount;}
						else{$expense_amount = "$".number_format($detail->expense_amount,2);}
						$expense_billable = $detail->expense_billable;
						$expense_notes = $detail->employee_expense_notes;
						$expense_name = $detail->expense_code_name;
						$total = ($detail->expense_amount * $expense_quantity);
						$total_total += $total;
						
						echo '<li>&nbsp;</li>';
						echo '<li><table width = "100%">';
						echo '<tr><th><input type="text" size="7" class="do_input_new" disabled value="'.$date.'" /></th>';
						
						if(empty($detail->gp_id)){$project_number=$project_id;}
						elseif($project_id==$personal_project){$project_number = $personal_project;}
						else{$project_number=$detail->gp_id;}
						
						echo '<th><input type="text" size="9" class="do_input_new" disabled value="'.$project_number.'" /></th>';
						echo '<th><input type="text" size="9" class="do_input_new" disabled value="'.$expense_name.'" /></th></tr>';
						echo '<tr><th><input type="text" size="7" class="do_input_new" disabled value="'.$expense_quantity.'" /></th>';
						echo '<th><input type="text" size="9" class="do_input_new" disabled value="'.$expense_amount.'" /></th>';
						if($expense_billable == 1){echo '<th><input type="Text" size="9" class="do_input_new" disabled value="Billable" /></th></tr></table></li>';}
						else{echo '<th><input type="text" size="9" class="do_input_new" disabled value="No-Bill" /></th></tr></table></li>';}
						echo '<li><textarea rows="1" class="do_input_new full_wdth_me" disabled >'.$expense_notes.'</textarea></li>';
						
						$ind_backup_results = $wpdb->get_results($wpdb->prepare("select expense_filename from ".$wpdb->prefix."expense_backup 
							where expense_id=%d",$expense_id));
						if(!empty($ind_backup_results))
						{
							foreach($ind_backup_results as $ibr)
							{
								echo '<li><font size="3"><a href="/wp-content/expense_backup/'.rawurlencode($ibr->expense_filename).'" target="_blank" >'.$ibr->expense_filename.'</a></font></li>';
							}
						}
						echo '<li><strong><font size="2">Item Total:  $'.number_format($total,2).'</strong></font></li>';
						echo '<li>&nbsp;</li>';
						echo '<hr>';
					}
					echo '<li>&nbsp;</li>';
					echo '<li><h2>Total Report:  $'.number_format($total_total,2).'</h2></li>';
					if($expense_status==0){$created = "Created";}else{$created = "Submitted";}
					echo '<li><h3>Date '.$created.':  '.date('m-d-Y',$details[0]->expense_submit_date).'</h3></li>';
					echo '<li>&nbsp;</li>';
					echo '<li>';
					if($expense_owner == $uid and $expense_status < 2)
					{
						if($details[0]->ee_mastercard == 0)
						{
							echo '&nbsp;&nbsp;<input type="submit" name="delete-info" class="my-buttons-submit" value="Delete" />';
						}
						echo '&nbsp;&nbsp;<a href="/?p_action=edit_employee_expense&ID='.$expense_report.'" class="nice_link" >Edit</a></li>';
						echo '<li>&nbsp;</li>';
					}
					if(($uid == 11 or $uid == 293 or $uid==94 or $uid==235) and $expense_status==1)
					{
						echo '&nbsp;&nbsp;<input type="submit" name="approve_info" class="my-buttons" value="Mark Approved" />';
						echo '&nbsp;&nbsp;<input type="submit" name="reject-info" class="my-buttons-submit" value="Reject" />';
						echo '&nbsp;&nbsp;<a href="expense-approvals/" class="my-buttons-submit" style="color:#ffffff;" >Go Back to Approvals</a></li>';
					}
					else{echo '</li>';}
					?>
						</ul>
					</div>
				</div>
			</div>
		</form>	
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Attached Backup";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						$backup_result = $wpdb->get_results($wpdb->prepare("select expense_filename from ".$wpdb->prefix."expense_backup 
							where expense_report_id=%d",$expense_report));
						if(empty($backup_result)){echo "You don't have any backup attached yet.";}
						else
						{
							$current_dir = getcwd();
							$backup_dir = $current_dir."/wp-content/expense_backup/";
							echo '<table width="100%">';
							foreach ($backup_result as $backup)
							{
								echo '<tr><th><a href="/wp-content/expense_backup/'.rawurlencode($backup->expense_filename).'" target="_blank" >'
								.$backup->expense_filename.'</a></th></tr>';
								if($uid == 11)
								{
									chdir($backup_dir);
									echo '<tr><th>'.mime_content_type($backup->expense_filename).'</th></tr>';
									chdir($current_dir);
								}
							}
							echo '</table>';
						}?>
						</ul>
					</li>
				</ul>
				</div>
			</div>
		</div>
	</div>
<?php }  
	get_footer();
?>