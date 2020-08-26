<?php
function billyB_expense_management_unprocessed()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	
	function sitemile_filter_ttl($title){return "Expense Approval";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11,293,94,235); //Bill, Peter, Maresha, Tash
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard/"); exit;}
 	
	if(isset($_POST['save-info']) or isset($_POST['save-info-one']) or isset($_POST['save-info-two']) or isset($_POST['save-info-vendor']))
	{
		if(isset($_POST['save-info'])){$process = 0;}//employee expenses
		elseif(isset($_POST['save-info-one'])){$process = 1;}//mastercard charges
		elseif(isset($_POST['save-info-two'])){$process = 2;}//amex charges
		else{$process = 3;}
		
		$records = ($_POST['record']);
		
		if($process < 3)
		{
			foreach($records as $record)
			{
				$details = explode(',,,',$record['details']);//id,set,type
				$expense_report_id = $details[0];
				$delete = $record['box'];
				$set = $details[1];
				$type = $details[2];
				
				if($delete == "on" and $type==$process)
				{
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$expense_report_id));
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
				$delete = $v['box'];
				$set = $v['set'];//check to see if transaction was previously marked for approval
				
				if($delete == "on")
				{
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."vendor_payables where vendor_payable_id=%d",$id));
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
						where employee_expense_status=0
						group by expense_report_id 
						order by ee_mastercard,expense_submit_date asc";
					$expense_results = $wpdb->get_results($expense_query);
					
					$vendor_expenses = $wpdb->get_results("select vendor_payable_id,vendor_name,vendor_fee,vendor_expense,set_for_approval,submit_date
						from ".$wpdb->prefix."vendor_payables
						inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
						where expense_status=0
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
								<th><u>Delete</u></th>
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
									if($expense->ee_mastercard==1)
									{
										echo '<div id="content">
												<div class="my_box3">
													<div class="padd10">';
										echo '<ul class="other-dets_m"><li><h2>MasterCards</h2></li><li><table width="100%">';
										echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Delete</u></th></tr>';
									}
									else
									{
										echo '<div id="content">
												<div class="my_box3">
													<div class="padd10">';
										echo '<ul class="other-dets_m"><li><h2>AMEXs</h2></li><li><table width="100%">';
										echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Delete</u></th></tr>';
									}
								}
								if($expense_type==1 and $expense->ee_mastercard > 1)
								{
									echo '</table></li><li>&nbsp;</li>';
									echo '<li><input type="submit" name="save-info-one" class="my-buttons" value="Save" /></li>';//save button for MasterCards
									echo '</ul></div></div></div>';
									echo '<div id="content">
											<div class="my_box3">
												<div class="padd10">';
									echo '<ul class="other-dets_m"><li><h2>AMEXs</h2></li><li><table width="100%">';
									echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Delete</u></th></tr>';
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
								echo '<th><input type="checkbox" name="record['.$report_id.'][box]" ';
								echo '</tr>';					
							}
							echo '</table></li>';
							echo '<li>&nbsp;</li>';
							if($expense_type == 0){echo '<li><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>';}//save for EE
							elseif($expense_type == 1){echo '<li><input type="submit" name="save-info-one" class="my-buttons" value="Save" /></li>';}//save for MC
							elseif($expense_type == 2){echo '<li><input type="submit" name="save-info-two" class="my-buttons" value="Save" /></li>';}//save for AMEX
							echo '</ul></div></div></div>';
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
									<td><input type="checkbox" name="vendor['.$v->vendor_payable_id.'][box]" /></td>
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
		</form>			
<?php } }
add_shortcode('expense_management_unprocessed','billyB_expense_management_unprocessed')
?>