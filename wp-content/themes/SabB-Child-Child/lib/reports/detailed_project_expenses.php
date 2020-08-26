<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 	$project = $_GET['ID'];
	
	$project_query = $wpdb->prepare("select client_name,project_name from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
		where ID='$project'",$project);
	$project_results = $wpdb->get_results($project_query);
	$project_name = $project_results[0]->project_name;
	$client_name = $project_results[0]->client_name;
	function sitemile_filter_ttl($title){return __("Detailed Expenses Report",'ProjectTheme');}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	?>
	<div class="page_heading_me">
		<div class="page_heading_me_inner"> 
            <div class="mm_inn"><?php echo $client_name.":  ".$project_name; ?></div>                            
        </div>            
    </div> 
	<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content_full"><h2><?php echo "Detailed Expenses";?></h2>
					<div class="my_box3">
						<div class="padd10">
							<ul class="other-dets_m">
							<?php
							$expense_query = $wpdb->prepare("select display_name,expense_report_id,expense_date,expense_quantity,expense_amount,expense_billable,employee_expense_status,
								billed_status,expense_code_name
								from ".$wpdb->prefix."employee_expenses 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
								left join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
								where project_id=%d order by expense_date,display_name",$project);
							$expense_results = $wpdb->get_results($expense_query);
							
							$vendor_expenses = $wpdb->get_results($wpdb->prepare("select expense_date,vendor_name,vendor_payable_id,vendor_fee,vendor_expense,expense_status,expense_billable,billed_status,fee_billable,v_exp_name
								from ".$wpdb->prefix."vendor_payables
								inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
								left join ".$wpdb->prefix."vendor_expense_codes on ".$wpdb->prefix."vendor_payables.expense_type_id=".$wpdb->prefix."vendor_expense_codes.vendor_exp_code_id
								where project_id=%d
								order by expense_date,vendor_name",$project));
								
							if(empty($expense_results) and empty($vendor_expenses)){echo 'There are no expenses booked to this project yet';}
							else
							{
								if(!empty($expense_results))
								{
									echo '<li><h2>Employee Expenses</h2></li>';
									echo '<li><table width="100%">';
									echo '<tr>
										<th><b><u>Date</u></b></th>
										<th><b><u>Employee</u></b></th>
										<th><b><u>Expense Total</u></b></th>
										<th><b><u>Expense Type</u></b></th>
										<th><b><u>Status</u></b></th>
										<th><b><u>Billable</u></b></th>
										<th><b><u>Billed Status</u></b></th>
										</tr>';
									$expense_total = 0;
									$expense_billed = 0;
									$no_bill = 0;
									foreach($expense_results as $e)
									{
										$status = $e->employee_expense_status;
										echo '<tr>
											<th>'.date('m-d-Y',$e->expense_date).'</th>
											<th>'.$e->display_name.'</th>
											<th><a href="/?p_action=employee_expense_view&ID='.$e->expense_report_id.'">'.projecttheme_get_show_price($e->expense_quantity * $e->expense_amount).'</a></th>
											<th>'.$e->expense_code_name.'</th>
											<th>'.($status==0 ? "Unsubmitted" : ($status==1 ? "Submitted" : "Processed")).'</th>
											<th>'.($e->expense_billable==1 ? "Billable" : "No-Bill").'</th>
											<th>'.($e->expense_billable==3 ? "" : ($e->billed_status<2 ? "To be billed" : "Billed")).'</th></tr>';
										$expense_total += ($e->expense_quantity * $e->expense_amount);
										if($e->billed_status >=2){$expense_billed += ($e->expense_quantity * $e->expense_amount);}
										if($e->expense_billable==3){$no_bill += ($e->expense_quantity * $e->expense_amount);}
									}
									echo '</table></li>';
									echo '<li>&nbsp;</li>';
								}
								if(!empty($vendor_expenses))
								{
									
									echo '<li><h2>Vendor Expenses</h2></li>';
									echo '<li><table width="100%">
										<tr>
										<th><b><u>Date</u></b></th>
										<th><b><u>Vendor</u></b></th>
										<th><b><u>Amount</u></b></th>
										<th><b><u>Expense Type</u></b></th>
										<th><b><u>Status</u></b></th>
										<th><b><u>Billable</u></b></th>
										<th><b><u>Billed Status</u></b></th>
										</tr>';
									foreach($vendor_expenses as $v)
									{
										if($v->expense_billable==1 and $v->fee_billable==1)
										{
											$bill_no_bill = "Billable";
										}
										elseif($v->expense_billable==1 or $v->fee_billable==1)
										{
											if($v->expense_billable==1){$bill_no_bill = "Expense Billable, Fee No-Bill";}
											else{$bill_no_bill = "Fee Billable, Expense No-Bill";}
										}
										else{$bill_no_bill = "No-Bill";}
										$amount = $v->vendor_fee + $v->vendor_expense;
										echo '<tr>
											<td>'.date('m-d-Y',$v->expense_date).'</td>
											<td>'.$v->vendor_name.'</td>
											<td><a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$v->vendor_payable_id.'">
												$'.number_format($amount,2).'</a></td>
											<td>'.$v->v_exp_name.'</td>
											<td>'.($v->expense_status == 0 ? "Unsubmitted" : ($v->expense_status==1 ? "Submitted" : "Processed")).'</td>
											<td>'.$bill_no_bill.'</td>
											<td>'.(($v->expense_billable == 3 and $v->fee_billable==3) ? "" : ($v->billed_status<2 ? "To be billed" : "Billed")).'</td>
											</tr>';
										$expense_total += ($v->vendor_fee + $v->vendor_expense);
										if($v->billed_status >=2){$expense_billed += ($v->vendor_fee + $v->vendor_expense);}
										if($v->expense_billable==3){$no_bill += ($v->vendor_fee + $v->vendor_expense);}
									}
									echo '</table></li>';
									echo '<li>&nbsp;</li>';
								}
								echo '<li><h3>Total Expenses</h3><p>$'.number_format($expense_total,2).'</p></li>';
								echo '<li>&nbsp;</li>';
								echo '<li><h3>Total No-Bill</h3><p>$'.number_format($no_bill,2).'</p></li>';
								echo '<li><h3>Billed Expenses</h3><p>$'.number_format($expense_billed,2).'</p></li>';
								echo '<li><h3>To be Billed</h3><p>$'.number_format($expense_total - $expense_billed - $no_bill,2).'</p></li>';
							}
							?>
							<li>&nbsp;</li>
							<li><a href="/?p_action=project_card&ID=<?php echo $project;?>" class="nice_link">Project Card</a>&nbsp;
								<a href="/?p_action=project_invoice&ID=<?php echo $project;?>" class="nice_link">Project Invoice</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php get_footer(); ?>