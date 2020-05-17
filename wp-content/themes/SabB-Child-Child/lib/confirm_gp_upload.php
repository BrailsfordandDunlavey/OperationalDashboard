<?php
function billyB_confirm_gp_upload()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$allowed_array = array(11,94,293);
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard/"); exit;}
 	
	if(isset($_POST['export-info']))
	{				
		ob_end_clean();
		
		$records = ($_POST['record']);
		$csv = array();
		$filename = "bd_employee_expense_export - ".time().".csv";
		
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		
		$output = @fopen('php://output', 'w');
								
		fputcsv($output, array('ID','ACC.ACCOUNT NAME','FIN.POSTING DATE','FIN.TRANSACTION DATE','FIN.ACCOUNTING CODE 01 VALUE',
		'FIN.ACCOUNTING CODE 02 VALUE','FIN.ACCOUNTING CODE 03 VALUE','FIN.EXPENSE DESCRIPTION','FIN.ITEM QUANTITY','FIN.ITEM COST',
		'FIN.TRANSACTION AMOUNT','Start Date','End Date'));			
		
		$other_project_code_results = $wpdb->get_results("select other_project_code_value from ".$wpdb->prefix."other_project_codes");
		$other_code_array = array('BRDU2008TTEAMW','BRDU2008TTEAM2','BRDU2008TTEAMB');
		
		foreach($other_project_code_results as $other)
		{array_push($other_code_array,$other->other_project_code_value);}
		
		foreach($records as $record)
		{
			$expense_report_id = $record['id'];												
			$approved_date = time();
			$approved = $record['box'];
			$return = $record['return'];
						
			if($approved == "on")
			{
				$report_detail_results = $wpdb->get_results($wpdb->prepare("select display_name,project_id,".$wpdb->prefix."projects.gp_project_number,expense_code_gp,
					expense_date,expense_billable,employee_expense_notes,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,employee_gp_id,personal_project
					from ".$wpdb->prefix."employee_expenses
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
					left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
					left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where expense_report_id=%d
					order by expense_date",$expense_report_id));
							
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_export_date=%d 
					where expense_report_id=%d",$approved_date,$expense_report_id));
							
				for($i=0;$i<count($report_detail_results);$i++)
				{									
					$project_id = $report_detail_results[$i]->project_id;
					if(in_array($project_id,$other_code_array) or ($project_id==$report_detail_results[0]->personal_project and !empty($project_id)))
					{
						$project_code = (string)$project_id;
					}
					else
					{
						$project_code = $report_detail_results[$i]->gp_project_number;
					}
								
					$gp_id = (string)$report_detail_results[$i]->employee_gp_id;
					$employee_name = $report_detail_results[$i]->display_name;
					$posting_date = date('m-d-Y',$report_detail_results[$i]->expense_date);
					$transaction_date = date('m-d-Y',$report_detail_results[$i]->expense_date);
					$expense_code = $report_detail_results[$i]->expense_code_gp;
					$billable = $report_detail_results[$i]->expense_billable;
					$description = $report_detail_results[$i]->employee_expense_notes;
					$quantity = $report_detail_results[$i]->expense_quantity;
					$amount = $report_detail_results[$i]->expense_amount;
					if($amount < 0)
					{
						$quantity = -1 * $quantity;
						$amount = -1 * $amount;
					}
					$total_transaction = round($quantity * $amount,2);
					if($total_transaction < 0){$total_transaction = -1 * $total_transaction;}
					$start_date = date('m-d-Y',$report_detail_results[0]->expense_date);
					$end_date = date('m-d-Y',$report_detail_results[count($report_detail_results)-1]->expense_date);
							
					fputcsv($output,array($gp_id,$employee_name,$posting_date,$transaction_date,$project_code,$expense_code,$billable,$description,$quantity,
					$amount,$total_transaction,$start_date,$end_date));
				}
			}
		}
		fclose($output);
		exit();
	}
	if(isset($_POST['confirm-info']))
	{
		$records = $_POST['record'];
		foreach($records as $record)
		{
			$expense_report_id = $record['id'];												
			$approved_date = time();
			$approved = $record['box'];
			$return = $record['return'];
						
			if($approved == "on")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set employee_expense_status=4 where expense_report_id=%d",$expense_report_id));
			}
			elseif($return=='on')
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set employee_expense_status=1 where expense_report_id=%d",$expense_report_id));
			}
		}
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
				<?php echo "Thank you.  The expenses have been marked as confirmed."?>
				<a href="<?php bloginfo('siteurl');?>/expense-approvals/"><?php echo "Approve more expenses";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
				</div>
			</div>
		</div>
		<?php
	}
	else
	{
		?>   
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<form method="post"  enctype="multipart/form-data">
						<ul class="other-dets_m">
							<li>&nbsp;</li>
							<li><table width="100%">
							<?php 
							$expense_results = $wpdb->get_results("select expense_report_id,expense_submit_date,display_name
								from ".$wpdb->prefix."employee_expenses 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
								where employee_expense_status=3 group by expense_report_id order by expense_submit_date");
							
							if(!empty($expense_results))
							{
								echo '<tr>
									<th><b><u>Employee</u></b></th>
									<th><b><u>Submit Date</u></b></th>
									<th><b><u>Report ID</u></b></th>
									<th><b><u>Amount</u></b></th>
									<th style="text-align:center;"><b><u>Confirm/Re-Export</u></b></th>
									<th style="text-align:center;"><b><u>Return</u></b></th>
									</tr>';
								foreach($expense_results as $expense)
								{
									$employee_name = $expense->display_name;
									$report_id = $expense->expense_report_id;
									
									$sumresults = $wpdb->get_results($wpdb->prepare("select expense_quantity,expense_amount from ".$wpdb->prefix."employee_expenses 
										where expense_report_id=%d",$report_id));
									$sum = 0;
									for($i=0;$i<=count($sumresults);$i++)
									{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
									
									echo '<tr><td>'.$employee_name.'</td>';
									echo '<td>'.date('m-d-Y',$expense->expense_submit_date).'</td>';
									echo '<td><a href="/?p_action=employee_expense_view&ID='.$report_id.'" >'.$report_id.'</td>';
									echo '<td>$'.number_format($sum,2).'</td>';
									echo '<input type="hidden" name="record['.$report_id.'][id]" value="'.$report_id.'" />';
									echo '<td style="text-align:center;"><input type="checkbox" name="record['.$report_id.'][box]" />';
									echo '<td style="text-align:center;"><input type="checkbox" name="record['.$report_id.'][return]" />';
									echo '</tr>';							
								}
								echo '</table></li><li>&nbsp;</li>';
								?>
								<li><input type="submit" name="confirm-info" class="my-buttons-submit" value="Process Confirm/Return" />
								&nbsp;&nbsp;
								<input type="submit" name="export-info" class="my-buttons" value="Re-Export" /></li>							
								<?php
							}
							else{echo '</table></li><li>There are no expenses pending GP upload confirmation at this time.</li><li>&nbsp;</li>';}
							?>
						</ul>
					</form>						
				</div>
			</div>
		</div>
<?php } }
add_shortcode('confirm_gp_upload','billyB_confirm_gp_upload')
?>