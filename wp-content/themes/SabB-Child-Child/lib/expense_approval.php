<?php
function billyB_sidebar_missing_mastercards()
{
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	echo '<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3>Missing MasterCards</h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">';
				
				$mc_query = "select distinct employee_id,display_name from ".$wpdb->prefix."employee_expenses
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
					where employee_expense_status=0 and ee_mastercard=1
					order by display_name";
				$mc_results = $wpdb->get_results($mc_query);
				$count = 0;
				
				foreach($mc_results as $mcr)
				{
					echo '<li>'.$mcr->display_name.'</li>';
					$count ++;
				}
				echo '<li><strong>Total:  '.$count.'</strong></li>';
				if($count > 0)
				{
					echo '<li>&nbsp;</li>';
					echo '<li><input type="submit" name="send_reminders" value="Email Reminder" class="my-buttons-submit" /></li>';
				}
	echo '</ul>
				</li>
			</ul>
		</div></div>';
}
function billyB_expense_approval()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	
	function sitemile_filter_ttl($title){return "Expense Approval";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$allowed_array = array(11,293,94,235);//Bill, Peter Kroeger, Maresha Leizear, Natasha Pereira
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard/"); exit;}
 	
	if(isset($_POST['save-info']) or isset($_POST['save-info-one']) or isset($_POST['save-info-two']) or isset($_POST['save-info-vendor']))
	{
		if(isset($_POST['save-info'])){$process = 0;}//employee expenses
		elseif(isset($_POST['save-info-one'])){$process = 1;}//mastercard charges
		elseif(isset($_POST['save-info-two'])){$process = 2;}//amex charges
		else{$process = 3;}
		
		$records = ($_POST['record']);
		$approved_date = time();
		
		if($process < 3)
		{
			foreach($records as $record)
			{
				$details = explode(',,,',$record['details']);//id,set,type
				$expense_report_id = $details[0];
				$approved = $record['box'];
				$set = $details[1];
				$type = $details[2];
				
				if($approved == "on" and $type==$process)
				{
					$report_detail_results = $wpdb->get_results($wpdb->prepare("select employee_expense_id from ".$wpdb->prefix."employee_expenses 
						where expense_report_id=%d",$expense_report_id));
								
					foreach($report_detail_results as $expense_id)
					{
						$expense_detail = $expense_id->employee_expense_id;
						$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set employee_expense_status=2,expense_approved_by=%d,
							expense_approved_date=%d where employee_expense_id=%d",$uid,$approved_date,$expense_detail));
					}
				}
				elseif($approved != "on" and $set == 1 and $type==$process)
				{
					$report_detail_results = $wpdb->get_results($wpdb->prepare("select employee_expense_id from ".$wpdb->prefix."employee_expenses 
						where expense_report_id=%d",$expense_report_id));
					
					foreach($report_detail_results as $expense_id)
					{
						$expense_detail = $expense_id->employee_expense_id;
						$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set set_for_approval=0 
							where employee_expense_id=%d",$expense_detail));
					}
				}
			}
		}
		else
		{
			$vendors = $_POST['vendor'];
		
			foreach($vendors as $v)
			{
				$details = explode(',,,',$v['details']);
				$id = $v['id'];
				$approval = $v['box'];
				$set = $v['set'];//check to see if transaction was previously marked for approval
				
				if($approval == "on")
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set expense_status=2,approved_date=%d,approved_by=%d 
						where vendor_payable_id=%d",$approved_date,$uid,$id));
				}
				elseif($approval != "on" and $set == 1)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set set_for_approval=0 where vendor_payable_id=%d",$id));
				}
			}
		}
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
				<?php echo "Thank you.  The approved expenses are set for export.<br/><br/>";?>
				<a href="<?php bloginfo('siteurl');?>/expense-export/"><?php echo "Export approved expenses";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
				</div>
			</div>
		</div>
		<?php	
	}
	if(isset($_POST['send_reminders']))
	{
		$mc_query = "select distinct employee_id,display_name,user_email from ".$wpdb->prefix."employee_expenses
			inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
			where employee_expense_status=0 and ee_mastercard=1
			order by display_name";
		$mc_results = $wpdb->get_results($mc_query);
		
		$link = get_bloginfo('siteurl').'/corporate-visa-entry';
		
		foreach($mc_results as $mcr)
		{
			$to = $mcr->user_email;
			$subject = "Please submit your Visa information on the OpDash";
			$message = 'Please login to the OpDash and go to Expenses->Corporate Visa Entry to submit your codes.<br/><br/><a href="'.$link.'">Corporate Visa Entry Form</a><br/><br/>Thank You';
			if($to == 'bbannister@programmanagers.com'){$message .= '<br/><br/>'.$mcr->display_name;}
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail($to,$subject,$message,$headers);
		}
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
				<?php echo "Reminders have been sent.<br/><br/>";?>
				<a href="<?php bloginfo('siteurl');?>/expense-approvals/"><?php echo "Refresh Expenses for Approval";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl');?>/expense-export/"><?php echo "Export approved expenses";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
				</div>
			</div>
		</div>
		<?php	
	}
	else{
	?>   
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<?php 
					$expense_query = "select expense_quantity,expense_report_id,set_for_approval,employee_id,expense_submit_date,
						expense_amount,ee_mastercard,display_name
						from ".$wpdb->prefix."employee_expenses 
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
						where employee_expense_status=1
						group by expense_report_id 
						order by ee_mastercard,expense_submit_date asc";
					$expense_results = $wpdb->get_results($expense_query);
					
					$vendor_expenses = $wpdb->get_results("select vendor_payable_id,vendor_name,vendor_fee,vendor_expense,set_for_approval,submit_date,vendor_expense
						from ".$wpdb->prefix."vendor_payables
						inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
						where expense_status=1
						order by vendor_name");
					
					if(!empty($expense_results) or !empty($vendor_expenses))
					{
						if(!empty($expense_results))
						{
							echo '<li><h2>Employee Expenses</h2></li>
								<li><table width="100%">';
							echo '<tr>
								<th><u>Employee</u></th>
								<th><u>Submit Date</u></th>
								<th><u>Report ID</u></th>
								<th><u>Amount</u></th>
								<th><u>Approve</u></th>
								</tr>';
							foreach($expense_results as $expense)
							{
								$expense_owner = $expense->employee_id;
								$employee_name = $expense->display_name;
								
								if($expense_type==0 and $expense->ee_mastercard >0)
								{
									echo '</table></li><li>&nbsp;</li>';
									echo '<li><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>';//save button for employee expenses
									echo '</ul></div></div></div>';
									billyB_sidebar_missing_mastercards(); $called = "yes";
									if($expense->ee_mastercard==1)
									{
										echo '<div id="content">
												<div class="my_box3">
													<div class="padd10">';
										echo '<ul class="other-dets_m"><li><h2>MasterCards</h2></li><li><table width="100%">';
										echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Approve</u></th></tr>';
									}
									else
									{
										echo '<div id="content">
												<div class="my_box3">
													<div class="padd10">';
										echo '<ul class="other-dets_m"><li><h2>AMEXs</h2></li><li><table width="100%">';
										echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Approve</u></th></tr>';
									}
								}
								if($expense_type==1 and $expense->ee_mastercard > 1)
								{
									echo '</table></li><li>&nbsp;</li>';
									echo '<li><input type="submit" name="save-info-one" class="my-buttons" value="Save" /></li>';//save button for MasterCards
									echo '</ul></div></div></div>';
									if($called != "yes"){billyB_sidebar_missing_mastercards(); $called = "yes";}
									echo '<div id="content">
											<div class="my_box3">
												<div class="padd10">';
									echo '<ul class="other-dets_m"><li><h2>AMEXs</h2></li><li><table width="100%">';
									echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Approve</u></th></tr>';
								}
								$expense_type = $expense->ee_mastercard;
								$report_id = $expense->expense_report_id;
								$sum = 0;
								$total_report = $wpdb->get_results($wpdb->prepare("select expense_amount,expense_quantity from ".$wpdb->prefix."employee_expenses
									where expense_report_id=%d",$report_id));
								foreach($total_report as $e)
								{
									$sum += ($e->expense_quantity*$e->expense_amount);
								}
								echo '<tr><th>'.$employee_name.'</th>';
								echo '<th>'.date('m-d-Y',$expense->expense_submit_date).'</th>';
								echo '<th><a href="/?p_action=employee_expense_view&ID='.$report_id.'" >'.$report_id.'</th>';
								echo '<th>$'.number_format($sum,2).'</th>';
								echo '<input type="hidden" name="record['.$report_id.'][details]" 
									value="'.$report_id.',,,'.$expense->set_for_approval.',,,'.$expense_type.'" />';
								echo '<th><input type="checkbox" name="record['.$report_id.'][box]" '.($expense->set_for_approval == 1 ? "checked='checked'" : "");
								echo '</tr>';					
							}
							echo '</table></li>';
							echo '<li>&nbsp;</li>';
							if($expense_type == 0){echo '<li><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>';}//save for EE
							elseif($expense_type == 1){echo '<li><input type="submit" name="save-info-one" class="my-buttons" value="Save" /></li>';}//save for MC
							elseif($expense_type == 2){echo '<li><input type="submit" name="save-info-two" class="my-buttons" value="Save" /></li>';}//save for AMEX
							echo '</ul></div></div></div>';
							if($called != "yes"){billyB_sidebar_missing_mastercards(); $called = "yes";}
						}
						if(!empty($vendor_expenses))
						{
							echo '<div id="content">
											<div class="my_box3">
												<div class="padd10">';
							echo '<ul class="other-dets_m"><li><h2>Vendor Payables</h2></li>';
							echo '<li><table width="100%">
								<tr>
								<th><u>Vendor</u></th>
								<th><u>Submit Date</u></th>
								<th><u>Fee Amount</u></th>
								<th><u>Expense Amount</u></th>
								<th><u>Approve</u></th>
								</tr>';
							foreach($vendor_expenses as $v)
							{
								echo '<tr>
									<input type="hidden" name="vendor['.$v->vendor_payable_id.'][id]" value="'.$v->vendor_payable_id.'" />
									<input type="hidden" name="vendor['.$v->vendor_payable_id.'][set]" value="'.$v->set_for_approval.'" />
									<td><a href="'.get_bloginfo('siteurl').'?p_action=edit_vendor_payable&ID='.$v->vendor_payable_id.'" >'.$v->vendor_name.'</a></td>
									<td>'.date('m-d-Y',$v->submit_date).'</td>
									<td>$'.number_format($v->vendor_fee,2).'</td>
									<td>$'.number_format($v->vendor_expense,2).'</td>
									<td><input type="checkbox" name="vendor['.$v->vendor_payable_id.'][box]"
										'.($v->set_for_approval==1 ? 'checked="checked"' : '' ).' /></td>
									</tr>';
							}
							echo '</table></li>';
							echo '<li>&nbsp;</li>';
							echo '<li><input type="submit" name="save-info-vendor" class="my-buttons" value="Save" /></li>';//save for Vendor Expenses
						}
					}
					else{echo '<li>No Expenses pending approval at this time.</li>';}
					?>
				</ul>		
				</div>
			</div>
		</div>
		<?php if($called != "yes"){billyB_sidebar_missing_mastercards(); $called = "yes";} ?>
		</form>			
<?php } }
add_shortcode('expense_approval','billyB_expense_approval')
?>