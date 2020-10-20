<?php
function billyb_project_expenses_report()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	if($uid != 11 and $uid != 94 and $uid!=235){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}

	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];//employee expenses
		$now = time();
		$vendors = $_POST['vendor'];//vendor payables
		
		foreach($records as $record)
		{
			$details = explode(',,,',$record['details']);
			$expense_id = $details[0];
			$original_status = $details[1];
			if($record['bill'] != "no_bill")
			{
				if($record['bill'] == "billed"){$status = 2;}
				elseif($record['bill'] == "to_bill"){$status = 1;}
				elseif($record['bill'] == "confirmed"){$status = 3;}
				else{$status = 0;}
				if($status != $original_status)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=%d,billed_by=%d,billed_date=%d where employee_expense_id=%d",
						$status,$uid,$now,$expense_id));
				}
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=0,expense_billable=3 where employee_expense_id=%d",$expense_id));
			}
		}
		foreach($vendors as $vendor)
		{
			$details = explode(',,,',$vendor['details']);
			$payable_id = $details[0];
			$original_status = $details[1];
			if($vendor['bill'] != "no_bill")
			{
				if($vendor['bill'] == "billed"){$status = 2;}
				elseif($vendor['bill'] == "to_bill"){$status = 1;}
				elseif($vendor['bill'] == "confirmed"){$status = 3;}
				else{$status = 0;}
				if($status != $original_status)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set billed_status=%d,billed_by=%d,billed_date=%d where vendor_payable_id=%d",
						$status,$uid,$now,$payable_id));
				}
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set expense_billable=3,billed_status=0 where vendor_payable_id=%d",$payable_id));
			}
		}
	}
	?>   
	<script type="text/javascript">
	function hideRows(){
		var myForm = document.forms.billable_expenses;
		var billableFields = document.querySelectorAll("[id^='s0']");
		var button = myForm.elements['Unsubmitted'];
		
		if(button.value == "Hide Unsubmitted"){
			button.value = "Show Unsubmitted";
			for(i=0;i<billableFields.length;i++){
				billableFields[i].style.display = "none";
			}
		}
		else{
			button.value = "Hide Unsubmitted";
			for(i=0;i<billableFields.length;i++){
				billableFields[i].style.display = "table-row";
			}
		}
	}
	function bill_all(){
		var myForm = document.forms.billable_expenses;
		var to_bill_fields = document.querySelectorAll("[name$='[bill]']");
		var button = myForm.elements['all_bill'];
		
		if(button.value == "Mark all to be billed"){
			button.value = "Unmark all to be billed";
			for(i=0;i<to_bill_fields.length;i++){
				if(to_bill_fields[i].value=="nothing" && to_bill_fields[i].checked == true){
					to_bill_fields[i-4].checked=true;
				}
			}
		}
		else{
			button.value = "Mark all to be billed";
			for(i=0;i<to_bill_fields.length;i++){
				if(to_bill_fields[i].value == "to_bill" && to_bill_fields[i].checked == true){
					to_bill_fields[i+4].checked = true;
				}
			}
		}
	}
	</script>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post" name="billable_expenses" enctype="multipart/form-data">
				<div id="content_full"><h3>Projects with Billable Expenses</h3>
					<div class="my_box3">
					<div class="padd10">					
					<ul class="other-dets_m">
					<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li>
					<li>&nbsp;</li>
					<li>
						<p><input type="button" name="Unsubmitted" value="Hide Unsubmitted" onclick="hideRows();"/></p>
						<p><input type="button" name="all_bill" value="Mark all to be billed" onclick="bill_all();"/></p>
					</li>
					<li>&nbsp;</li>
						<li>
						<table width="100%">
						<tr>
						<th><b><u><?php echo "Project";?></u></b></th>
						<th><b><u>Employee/Vendor</u></b></th>
						<th><b><u>Date</u></b></th>
						<th style="text-align:center;"><b><u>Expense Total</u></b></th>
						<th><b><u>Status</u></b></th>
						<th style="text-align:center;"><b><u>To Be Billed</u></b></th>
						<th style="text-align:center;"><b><u>On Invoice</u></b></th>
						<th style="text-align:center;"><b><u>Confirmed Invoiced</u></b></th>
						<th style="text-align:center;"><b><u>Move to No-Bill</u></b></th>
						<th style="text-align:center;"><b><u>Do Nothing</u></b></th>
						</tr>
						<?php
						$beg_month = strtotime(date('Y-m-01',$selected_month));
						$billable_query = "select display_name,expense_date,employee_expense_id,project_id,gp_project_number,expense_quantity,expense_report_id,
							".$wpdb->prefix."employee_expenses.expense_amount,employee_expense_status,billed_status 
							from ".$wpdb->prefix."employee_expenses
							inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
							where billed_status<3 and expense_billable=1 and employee_expense_status>0 and project_id REGEXP '^[0-9]+$'
							order by gp_project_number,employee_expense_status desc";
						$billable_results = $wpdb->get_results($billable_query);
						$projects_array = array();
						$v=0;
						for($i=0;$i<count($billable_results);$i++)
						{
							$project_id = $billable_results[$i]->project_id;
							$last_project = $billable_results[$i-1]->project_id;
							if($project_id != $last_project)
							{
								array_push($projects_array,$project_id);
								$vendor_payables = $wpdb->get_results($wpdb->prepare("select vendor_payable_id,vendor_name,expense_date,vendor_fee,vendor_expense,expense_status,billed_status,fee_billable
									from ".$wpdb->prefix."vendor_payables
									inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
									where project_id=%s and billed_status<3 and (expense_billable=1 or fee_billable=1) order by expense_status desc",$project_id));
								if(!empty($vendor_payables))
								{
									$count = $v+count($vendor_payables);
									for($v=$v++,$a=0;$v<$count;$v++,$a++)
									{
										$id = $vendor_payables[$a]->vendor_payable_id;
										if($vendor_payables[$a]->expense_billable==1)
										{
											$amount = $vendor_payables[$a]->vendor_expense;
											if($vendor_payables[$a]->fee_billable==1)
											{
												$amount += $vendor_payables[$a]->vendor_fee;
											}
										}
										else{$amount = $vendor_payables[$a]->vendor_fee;}
										if($a==0)
										{
											echo '<tr id="s'.$vendor_payables[$a]->expense_status.'"><td><a href="'.get_bloginfo('siteurl').'?p_action=project_invoice&ID='.$project_id.'">'.$billable_results[$i]->gp_project_number.'</td>';
										}
										else{echo '<tr id="s'.$vendor_payables[$a]->expense_status.'"><td>&nbsp;</td>';}
										echo '<input type="hidden" name="vendor['.$v.'][details]" value="'.$id.',,,'.$vendor_payables[$a]->billed_status.'" />';
										echo '<td><a href="'.get_bloginfo('siteurl').'?p_action=edit_vendor_payable&ID='.$id.'" >'.$vendor_payables[$a]->vendor_name.'</a></td>
											<td>'.date('m-d-Y',$vendor_payables[$a]->expense_date).'</td>
											<td style="text-align:center;">$'.number_format($amount,2).'</td>
											<td>'.($vendor_payables[$a]->expense_status < 3 ? "Not in GP" : "in GP" ).'</td>
											<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="to_bill" '.($vendor_payables[$a]->billed_status==1 ? 'checked="checked"' : '').' /></td>
											<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="billed" '.($vendor_payables[$a]->billed_status==2 ? 'checked="checked"' : '').'/></td>
											<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="confirmed" /></td>
											<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="no_bill" /></td>
											<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="nothing" '.($vendor_payables[$a]->billed_status==0 ? 'checked="checked"' : '').' /></td>
											</tr>';
									}
									echo '<tr id="s'.$billable_results[$i]->employee_expense_status.'"><td>&nbsp;</td>';
								}
								else{echo '<tr id="s'.$billable_results[$i]->employee_expense_status.'"><td><a href="'.get_bloginfo('siteurl').'?p_action=project_invoice&ID='.$project_id.'">'.$billable_results[$i]->gp_project_number.'</td>';}
							}
							else{echo '<tr id="s'.$billable_results[$i]->employee_expense_status.'"><td>&nbsp;</td>';}
							
							$status = $billable_results[$i]->employee_expense_status;
							if($status == 0){$in_gp="Unsubmitted";}elseif($status < 3){$in_gp = "Not in GP";}else{$in_gp = "in GP";}
							
							
							echo '<td><input type="hidden" name="record['.$i.'][details]" value="'.$billable_results[$i]->employee_expense_id.',,,'.$billable_results[$i]->billed_status.'" />
								<a href="'.get_bloginfo('siteurl').'?p_action=employee_expense_view&ID='.$billable_results[$i]->expense_report_id.'" target="_blank">
								'.$billable_results[$i]->display_name.'</a></td>';
							echo '<td>'.date('m-d-Y',$billable_results[$i]->expense_date).'</td>';
							echo '<td style="text-align:center;">$'.number_format($billable_results[$i]->expense_quantity * $billable_results[$i]->expense_amount,2).'</td>';
							echo '<td>'.$in_gp.'</td>';
							echo '<td style="text-align:center;"><input type="radio" name="record['.$i.'][bill]" value="to_bill" '.($billable_results[$i]->billed_status == 1 ? 'checked="checked"' : '').'/></td>';
							echo '<td style="text-align:center;"><input type="radio" name="record['.$i.'][bill]" value="billed" '.($billable_results[$i]->billed_status == 2 ? 'checked="checked"' : '').'/></td>';
							echo '<td style="text-align:center;"><input type="radio" name="record['.$i.'][bill]" value="confirmed" /></td>';
							echo '<td style="text-align:center;"><input type="radio" name="record['.$i.'][bill]" value="no_bill" /></td>';
							echo '<td style="text-align:center;"><input type="radio" name="record['.$i.'][bill]" value="nothing" '.($billable_results[$i]->billed_status==0 ? 'checked="checked"' : '').'/></td>';
							echo '</tr>';
						}
						$vendors_only = $wpdb->get_results($wpdb->prepare("select vendor_payable_id,vendor_name,expense_date,vendor_fee,vendor_expense,expense_status,billed_status,gp_project_number,project_id
							from ".$wpdb->prefix."vendor_payables
							inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
							inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."vendor_payables.project_id=".$wpdb->prefix."projects.ID
							where billed_status<3 and (expense_billable=1 or fee_billable=1)
							and project_id not in (select distinct project_id from ".$wpdb->prefix."employee_expenses where billed_status<3 and expense_billable=1 and employee_expense_status>0 and project_id REGEXP '^[0-9]+$')
							order by project_id,expense_status desc"));
						if(!empty($vendors_only))
						{
							$previous_id = "";
							$count = $v+count($vendors_only);
							for($v=$v++,$a=0;$v<$count;$v++,$a++)
							{
								$project_id = $vendors_only[$a]->project_id;
								$id = $vendors_only[$a]->vendor_payable_id;
								if($a==0 or $previous_id!=$project_id)
								{
									if(empty($vendors_only[$a]->gp_project_number)){$project_name = 'No GP Project Number';}else{$project_name=$vendors_only[$a]->gp_project_number;}
									echo '<tr id="s'.$vendors_only[$a]->expense_status.'"><td><a href="'.get_bloginfo('siteurl').'?p_action=project_invoice&ID='.$vendors_only[$a]->project_id.'">'.$project_name.'</a></td>';
								}
								else{echo '<tr id="s'.$vendors_only[$a]->expense_status.'"><td>&nbsp;</td>';}
								$previous_id = $project_id;
								if($vendors_only[$a]->expense_billable==1)
								{
									$amount = $vendors_only[$a]->vendor_expense;
									if($vendors_only[$a]->fee_billable==1)
									{
										$amount += $vendors_only[$a]->vendor_fee;
									}
								}
								else{$amount = $vendors_only[$a]->vendor_fee;}
								echo '<input type="hidden" name="vendor['.$v.'][details]" value="'.$id.',,,'.$vendors_only[$a]->billed_status.'" />';
								echo '<td><a href="'.get_bloginfo('siteurl').'?p_action=edit_vendor_payable&ID='.$id.'" >'.$vendors_only[$a]->vendor_name.'</a></td>
									<td>'.date('m-d-Y',$vendors_only[$a]->expense_date).'</td>
									<td style="text-align:center;">$'.number_format($amount,2).'</td>
									<td>'.($vendors_only[$a]->expense_status < 3 ? "Not in GP" : "in GP" ).'</td>
									<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="to_bill" '.($vendors_only[$a]->billed_status==1 ? 'checked="checked"' : '').' /></td>
									<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="billed" '.($vendors_only[$a]->billed_status==2 ? 'checked="checked"' : '').'/></td>
									<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="confirmed" /></td>
									<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="no_bill" /></td>
									<td style="text-align:center;"><input type="radio" name="vendor['.$v.'][bill]" value="nothing" '.($vendors_only[$a]->billed_status==0 ? 'checked="checked"' : '').'/></td>
									</tr>';
							}
						}
						?>
						</table>
						</li>
						<li>&nbsp;</li>
						<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li>
					</ul>
					</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php 
}

add_shortcode('project_expenses_report','billyb_project_expenses_report') ?>