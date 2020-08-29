<?php
function billyB_expense_export()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 						
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
		
		$other_project_codes = $wpdb->get_results("select other_project_code_value from ".$wpdb->prefix."other_project_codes");
		$other_code_array = array('BRDU2008TTEAMW','BRDU2008TTEAM2','BRDU2008TTEAMB');
		
		foreach($other_project_codes as $other)
		{
			array_push($other_code_array,$other->other_project_code_value);
		}
		
		foreach($records as $record)
		{
			$expense_report_id = $record['id'];												
			$approved_date = time();
			$approved = $record['box'];
						
			if($approved == "on")
			{
				$report_detail_results = $wpdb->get_results($wpdb->prepare("select employee_id,display_name,project_id,".$wpdb->prefix."projects.gp_project_number,
					expense_code_gp,expense_date,expense_billable,employee_expense_notes,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,employee_gp_id,personal_project
					from ".$wpdb->prefix."employee_expenses
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
					left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
					left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where expense_report_id=%d
					order by expense_date",$expense_report_id));
							
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set employee_expense_status=3,expense_export_date=%d 
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
	?>   
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<form method="post"  enctype="multipart/form-data">
						<ul class="other-dets_m">
							<li>&nbsp;</li>
							<li><table width="100%">
							<?php 
							$expense_results = $wpdb->get_results("select expense_quantity,expense_report_id,employee_id,expense_submit_date,expense_amount 
								from ".$wpdb->prefix."employee_expenses 
								where employee_expense_status=2 group by expense_report_id order by expense_submit_date");
							
							if(!empty($expense_results))
							{
								echo '<tr><th><u>Employee</u></th><th><u>Submit Date</u></th><th><u>Report ID</u></th><th><u>Amount</u></th><th><u>Export</u></th></tr>';
								foreach($expense_results as $expense)
								{
									$expense_owner = $expense->employee_id;
									$employee_query = "select display_name from ".$wpdb->prefix."users where ID='$expense_owner'";
									$employee_result = $wpdb->get_results($employee_query);
									$employee_name = $employee_result[0]->display_name;
									
									$report_id = $expense->expense_report_id;
									$sumresults = $wpdb->get_results($wpdb->prepare("select expense_quantity, expense_amount from ".$wpdb->prefix."employee_expenses 
										where expense_report_id=%d",$report_id));
									$sum = 0;
									for($i=0;$i<=count($sumresults);$i++)
									{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
									
									echo '<tr><th>'.$employee_name.'</th>';
									echo '<th>'.date('m-d-Y',$expense->expense_submit_date).'</th>';
									echo '<th><a href="/?p_action=employee_expense_view&ID='.$report_id.'" >'.$report_id.'</th>';
									echo '<th>$'.number_format($sum,2).'</th>';
									echo '<th hidden><input type="text" name="record['.$report_id.'][id]" value="'.$report_id.'" /></th>';
									echo '<th><input type="checkbox" name="record['.$report_id.'][box]" ';
									echo '</tr>';							
								}
								echo '</table></li><li>&nbsp;</li>';	
							}
							else{echo '</table></li><li>There are no approved expenses pending export at this time.</li><li>&nbsp;</li>';}
							?>
							<li><input type="submit" name="export-info" class="my-buttons" value="<?php echo "Export"; ?>" />
								&nbsp;&nbsp;<a href="/expense-export/" class="nice_link" ><?php echo "Refresh";?></a>
								&nbsp;&nbsp;<a href="/confirm-gp-upload/" class="nice_link" ><?php echo "Confirm GP uploads";?></a>
							</li>
						</ul>
					</form>						
				</div>
			</div>
		</div>
<?php } 
add_shortcode('expense_export','billyB_expense_export')
?>
