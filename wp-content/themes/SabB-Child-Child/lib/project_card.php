<?php
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	function sitemile_filter_ttl($title){return "Project Card";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	
	$rightsquery = $wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$rightsresults = $wpdb->get_results($rightsquery);
	$team = $rightsresults[0]->team;
	
	$checklist = $_GET['ID'];
	$link = get_bloginfo('siteurl').'/?p_action=project_card&ID='.$checklist;
	
	if(!is_user_logged_in())
	{ 
		wp_redirect(get_bloginfo('siteurl').'/wp-login.php?redirect_to='.$link); 
		$_SESSION['redirect_me_back'] = $link;
		exit;
	}
	
	if(isset($_POST['reactivate']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set status=2 where ID=%d",$checklist));
		
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set status=2 where project_parent=%d",$checklist));
	}
	
	if(isset($_POST['update-info']))
	{
		$abb_name = trim($_POST['abb_name']);
		$project_group = $_POST['project_group'];
		$contact = trim($_POST['contact']);
		$address = trim($_POST['address']);
		$city = trim($_POST['city']);
		$state = trim($_POST['state']);
		$zip = trim($_POST['zip']);
		$email = trim($_POST['email']);
		$phone = trim($_POST['phone']);
		$delivery_type = trim($_POST['delivery_type']);
		$notes = trim($_POST['notes']);
		
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set abbreviated_name=%s,contact=%s,address=%s,city=%s,state=%d,
			zip=%s,email=%s,phone=%s,delivery_type=%s,notes=%s,project_group=%s where ID=%d",
			$abb_name,$contact,$address,$city,$state,$zip,$email,$phone,$delivery_type,$notes,$project_group,$checklist));
	}
	$queryedit = $wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d",$checklist);
	$details = $wpdb->get_results($queryedit);
	
		$gp_id = $details[0]->gp_id;
		$client_id = $details[0]->client_id;
		
		$client_name_query = $wpdb->prepare("select client_name from ".$wpdb->prefix."clients where client_id=%d",$client_id);
		$client_name_result = $wpdb->get_results($client_name_query);
		$client_name = $client_name_result[0]->client_name;
		
		$prime_id = $details[0]->prime_id;
		if(!empty($prime_id))
		{
			$prime_name_query = $wpdb->prepare("select client_name from ".$wpdb->prefix."clients where client_id=%d",$prime_id);
			$prime_name_result = $wpdb->get_results($prime_name_query);
			$prime_name = $prime_name_result[0]->client_name;
		}
		$project_name = $details[0]->project_name;
		$abb_name = $details[0]->abbreviated_name;
		$sphere = $details[0]->sphere;
		$project_group = $details[0]->project_group;
		$project_manager = $details[0]->project_manager;	
		$fee_type = $details[0]->fee_type;
		$current_document = $details[0]->current_document;
		$document_number = $details[0]->document_number;
		$estimated_start = $details[0]->estimated_start;
		$project_type = $details[0]->project_type;
		$fee_amount = $details[0]->fee_amount;
		$expense_amount = $details[0]->expense_amount;
		$sub_fee_amount = $details[0]->sub_fee_amount;
		$expense_type = $details[0]->expense_type;
		$market = $details[0]->market;
		$submarket = $details[0]->submarket;
		$confidential = $details[0]->confidential;
		$venues = $details[0]->venues;
		$contact = $details[0]->contact;
		$address = $details[0]->address;
		$city = $details[0]->city;
		$state_id = $details[0]->state;
		$zip = $details[0]->zip;
		$email = $details[0]->email;
		$phone = $details[0]->phone;
		$delivery_type = $details[0]->delivery_type;
		$notes = $details[0]->notes;
		//add perdiem,markup,retainer,retention,client_portal
		$client_portal = $details[0]->client_portal;
		$status = $details[0]->status;
		$accounting_notes = $details[0]->accounting_notes;
		
		if($status == 0){$status_description = "Unsubmitted";}
		elseif($status == 1){$status_description = "Processing Checklist";}
		elseif($status == 2){$status_description = "Active";}
		elseif($status == 3)
		{
			$status_description = "<strong><font color='red'>Closed</font></strong>";
			if($uid==11 or $uid==94 or $uid==235)
			{
				$status_description .= '&nbsp;&nbsp;<input type="submit" name="reactivate" value="Reactivate" />';
			}
		}
		elseif($status == 4){$status_description = "Opportunity: Farming";}
		elseif($status == 5){$status_description = "Opportunity: 50/50";}
		elseif($status == 6){$status_description = "Opportunity: 95%";}
		
		$adserv_query = $wpdb->prepare("select * from ".$wpdb->prefix."projects where project_parent=%d",$checklist);
		$adserv_results = $wpdb->get_results($adserv_query);
		
		foreach($adserv_results as $adserv)
		{
			$fee_amount += $adserv->fee_amount;
			$expense_amount += $adserv->expense_amount;
			$sub_fee_amount += $adserv->sub_fee_amount;
		}
		
		$project_team =array();
		$queryteam = $wpdb->prepare("select distinct user_id from ".$wpdb->prefix."project_user where project_id=%d",$checklist);
		$resultsteam = $wpdb->get_results($queryteam);
		foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
		?>
		<div class="page_heading_me">
			<div class="page_heading_me_inner">
				<div class="main-pg-title">
					<div class="mm_inn"><?php echo $client_name." - ".$project_name;?> 
		</div></div></div></div>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
                	<div id="content">
						<div class="my_box3">
						<div class="padd10"><h3><u><?php echo "Project Details";?></u></h3>
						<ul class="other-dets_m">
							<li><h3>Status:</h3><p><?php echo $status_description;?></p></li>
							<li><h3><?php echo "Project ID:";?></h3><p><?php echo $gp_id;?></p></li>
							<li><h3><?php echo "Client Name:";?></h3>
								<p><?php echo '<a href="'.get_bloginfo('siteurl').'/?p_action=farm_view&ID='.$client_id.'" target="_blank">'.$client_name.'</a>';?></p></li>
							<?php
							if(!empty($prime_id))
							{echo '<li><h3>Prime Name (if sub):</h3>
								<p><a href="'.get_bloginfo('siteurl').'?p_action=farm_view&ID='.$prime_id.'" target="_blank">'.$prime_name.'</a></p></li>';}
							?>
							<li><h3><?php echo "Project Name"; ?>:</h3><p><?php echo $project_name;?></p></li>
							<li><h3><?php echo "Project Abbreviated Name"; ?>:</h3>
								<p><input type="text" name="abb_name" maxlength="25" value="<?php echo $abb_name;?>"
									<?php 
									$allowed_edits = array($project_manager,103,11,65,107);
									if(!in_array($uid,$allowed_edits)){echo 'readonly';}?> /></p></li>
							<li><h3><?php echo "Market"; ?>:</h3>
								<p>
								<?php
								$marketquery = $wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$market);
								$marketresult = $wpdb->get_results($marketquery);
								$marketname = $marketresult[0]->name;
								
								$submarketquery = $wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$submarket);
								$submarketresult = $wpdb->get_results($submarketquery);
								$submarketname = $submarketresult[0]->name;
																
								echo $marketname." - ".$submarketname;
								?>
								</p>
							</li>
							<?php if($sphere=="Higher Ed"){$group_type="Cluster";}else{$group_type="Group";}?>
							<li><h3><?php echo "Sphere:  ".$group_type; ?>:</h3><p><?php echo $sphere.':  '.$project_group;?></p></li>
							<li><h3><?php echo "Project Manager"; ?>:</h3><p>
							<?php 
							$pmquery = $wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$project_manager);
							$pmresult = $wpdb->get_results($pmquery);
							echo $pmresult[0]->display_name;?>
							</p></li>							

							<?php echo '<li><h3>Team Members:</h3>';
							$assign_roles_array = array(11,$project_manager,65,103);
							if(in_array($uid,$assign_roles_array) and $status!=3){echo '<p><a class="nice_link" href="/?p_action=assign_role&ID='.$checklist.'" >Assign Roles</a></p>';}?>
					        <li><table width="100%">
								<?php echo '<tr><th>&nbsp;</th><th><u>Role</u></th><th><u>Hours Worked</u></th><th><u>Remaining Hours</u></th><th><u>Percent Complete</u></th></th>';
										$totalhours =0;
										$totalprojectedvalue =0;
										$totalprojected =0;
										$this_month = strtotime(date('Y-m-01',time()));
										$year = date('Y',time());
										
									foreach($project_team as $user)
									{
										$details_query = $wpdb->prepare("select sum(timesheet_hours) as hours,display_name,planning_rate,user_role,rate from ".$wpdb->prefix."timesheets
											inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
											inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
											left join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
											left join ".$wpdb->prefix."project_user on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."project_user.user_id
											left join ".$wpdb->prefix."project_rates on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."project_rates.project_id
												and ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."project_rates.user_id
											where ".$wpdb->prefix."timesheets.project_id=%d and ".$wpdb->prefix."timesheets.user_id=%d and year=%d
												and ".$wpdb->prefix."project_user.project_id=%d and ".$wpdb->prefix."project_user.user_id=%d",$checklist,$user,$year,$checklist,$user);
										$details_results = $wpdb->get_results($details_query);
										
										$name = $details_results[0]->display_name;
										$rate = $details_results[0]->planning_rate;
										$hours = $details_results[0]->hours;
										if(empty($hours)){$hours = 0;}
										$role = $details_results[0]->user_role;
										if(!empty($details_results[0]->rate)){$rate = $details_results[0]->rate;}
										
										$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where user_id=%d and project_id=%d
											and projected_month>=%d",$user,$checklist,$this_month);
										$projected_hours_results = $wpdb->get_results($projected_hours_query);
										$projected = $projected_hours_results[0]->projected;
										if(empty($projected)){$projected = 0;}
										
										$value = ($hours+$projected) * $rate;
										
										$totalhours += $hours;
										$totalprojected += $projected;
										$totalprojectedvalue += $value;
										
										echo "<tr>
											<th>".$name."</th>
											<th>".$role."</th>
											<th>".number_format($hours,2)."</th>
											<th>".number_format($projected,2)."</th>
											<th>".(round($hours/($hours+$projected),2)*100)."%</th>
											</tr>";	
									}
									?>
								<tr><th>&nbsp;</th><th>&nbsp;</th><th><?php echo "_______</th><th>_______</th><th>_______";?></th></tr>
								<tr>
									<td><?php echo "Total</td>
									<td>&nbsp;</td>
									<td>".number_format($totalhours,2)."</td>
									<td>".number_format($totalprojected,2)."</td>
									<td>".(round($totalhours/($totalhours+$totalprojected),2)*100)."%</td>
									</tr>";?>
							</table></li>
							<li><a href="<?php echo get_bloginfo('siteurl').'/?p_action=detailed_project_hours&ID='.strtotime(date('Y-m-t',time())).'&project='.$checklist;?>" class="nice_link">Get Detailed Hours</a></li>
							</li>
							<li><h3><?php echo "Financial Status";?>:</h3><p>
								<table width="100%"><tr><th>&nbsp;</th><th><?php echo "<u>Billed to-date</u></th><th><u>Remaining</u></th><th><u>Projected Total</u></th><th><u>Over/Under Scope</u></th></tr>";?>
									<tr><th><?php echo "Fees";?></th><th>
									<?php 
									$feesquery = $wpdb->prepare("select sum(invoice_fee_amount) as sum from ".$wpdb->prefix."invoices where project_id=%d and invoice_fee_amount>0",$checklist);
									$feesresult = $wpdb->get_results($feesquery);
									echo "$".number_format($feesresult[0]->sum,2)."</th>";									
									
									echo '<th>$'.number_format($fee_amount-$feesresult[0]->sum,2).'</th>';
									echo '<th>$'.number_format($totalprojectedvalue,2).'</th>';
									echo '<th>'.($totalprojectedvalue > $fee_amount ? "<font color='red'><b>$".number_format(($fee_amount-$totalprojectedvalue)*-1,2)."</b></font>" : "$".number_format($fee_amount-$totalprojectedvalue,2)).'</th>';
									?>
									</tr>
									<tr><th>
									<?php echo "Expenses";?></th><th>
									<?php
									$expensesquery = $wpdb->prepare("select sum(invoice_expense_amount) as sum from ".$wpdb->prefix."invoices 
										where project_id=%d and invoice_expense_amount>0",$checklist);
									$expensesresult = $wpdb->get_results($expensesquery);
									echo "$".number_format($expensesresult[0]->sum,2);
									$pending_expenses_query = $wpdb->prepare("select expense_quantity,expense_amount from ".$wpdb->prefix."employee_expenses 
										where project_id=%d and expense_billable=1 and billed_status<2",$checklist);
									$pending_expenses_results = $wpdb->get_results($pending_expenses_query);
									$pending = 0;
									foreach($pending_expenses_results as $per)
									{
										$pending += ($per->expense_quantity * $per->expense_amount);
									}
									$vendor_expenses = $wpdb->get_results($wpdb->prepare("select sum(vendor_fee) as fee,sum(vendor_expense) as expense from ".$wpdb->prefix."vendor_payables
										where project_id=%d and billed_status<2 and expense_status=1",$checklist));
									$pending += ($vendor_expenses->fee + $vendor_expenses->expense);
									$no_bill = 0;
									$no_bill_expenses = $wpdb->get_results($wpdb->prepare("select expense_quantity,expense_amount from ".$wpdb->prefix."employee_expenses
										where project_id=%d and expense_billable=3",$checklist));
									foreach($no_bill_expenses as $nbe)
									{
										$no_bill += ($nbe->expense_quantity * $nbe->expense_amount);
									}
									$no_bill_vendor_fees = $wpdb->get_results($wpdb->prepare("select sum(vendor_fee) as vendor_fee from ".$wpdb->prefix."vendor_payables 
										where project_id=%d and fee_billable=3",$checklist));
									$no_bill_vendor_exp = $wpdb->get_results($wpdb->prepare("select sum(vendor_expense) as vendor_expense from ".$wpdb->prefix."vendor_payables 
										where project_id=%d and expense_billable=3",$checklist));
									$no_bill += $no_bill_vendor_fees[0]->vendor_fee;
									$no_bill += $no_bill_vendor_exp[0]->vendor_expense;
									?>
									</th><th>
									<?php 
									if($expense_type == 'unlimited'){echo 'Unlimited';}
									else
									{
										if($expense_amount - $expensesresult[0]->sum - $pending >=0)
										{
											echo "$".number_format((($expense_amount - $expensesresult[0]->sum) - $pending),2);
										}
										else
										{
											echo '<font color="red"><b>$'.number_format((($expense_amount - $expensesresult[0]->sum) - $pending),2).'</b></font>';
										}	
									}
									?>
									</th></tr>
									<?php
									if($no_bill > 0)
									{
										echo '<tr><th>No Bill</th><th>$'.number_format($no_bill,2).'</th></tr>';
									}
									
									?>
								</table>	
							</li>
							<li><a href="/?p_action=detailed_project_expenses&ID=<?php echo $checklist;?>" class="nice_link">Get Expense Details</a></li>
							</ul>
							</div></div>
							</div>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Other Project Details";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<li><h3><?php echo "Estimated Start"; ?>:</h3><p><?php echo date('m-d-Y',strtotime(date('Y-m-d',$estimated_start)));?></p></li>
							<li><h3><?php echo "Project Type"; ?>:</h3><p><?php echo $project_type;?></p></li>
							<li><h3><?php echo "Fee Type"; ?>:</h3><p><?php echo $fee_type;?></p></li>							
							<li><h3><?php echo "Expense Type"; ?>:</h3><p><?php echo $expense_type;?></p></li>
							<li><h3><?php echo "Fee Amount"; ?>:</h3><p><?php echo(ProjectTheme_get_show_price($fee_amount));?></p></li>	
							<li><h3><?php echo "Expense Amount"; ?>:</h3><p><?php echo (ProjectTheme_get_show_price($expense_amount));?></p></li>
							<li><h3><?php echo "Sub Fees"; ?>:</h3><p><?php echo (ProjectTheme_get_show_price($sub_fee_amount));?></p></li>
							<?php if(!empty($client_portal))
							{
								echo '<li><b><a href="'.$client_portal.'" target="_blank" >Client Portal</a></b></li>';
							}?>
							
							<?php 
							if($confidential=="on"){echo "<li><h3>Confidential</h3></li>";}
							if($venues =="on"){echo "<li><h3>Venues</h3></li>";}
							echo '<li><a href="/?p_action=project_projected_hours&ID='.$checklist.'" class="nice_link">Projected Hours</a></li>';
							echo '<li><a href="/?p_action=add_project_docs&ID='.$checklist.'" class="nice_link">Project Documents</a> (Proposal, Contract, etc.)</li>';
							$allowed_array = array(103,65,107,11,$project_manager);
							if(in_array($uid,$allowed_array) and $status!=3)
							{
								echo '<li><a href="/?p_action=add_another_user&ID='.$checklist.'" class="nice_link">Add someone to this Project</a></li>';
								echo '<li><a href="/?p_action=project_responsibilities&ID='.$checklist.'" class="nice_link">Manage Responsibilities</a></li>';
								echo '<li><a href="/?p_action=edit_project_rates&ID='.$checklist.'" class="nice_link">Edit Billing Rates</a></li>';
							}
							if(!empty($adserv_results) and $status!=3){echo '<li><a href="?p_action=adservs&ID='.$checklist.'" class="nice_link">Ad Servs</a></li>';}
							if($current_user->ID==11 and $status!=3){echo '<li><a href="/?p_action=edit_scope&ID='.$checklist.'" class="nice_link">Edit Scope</a></li>';}
							?>
							</ul>
						</li>
					</ul>
				</div></div>
						<div id="content">
						<div class="my_box3">
						<div class="padd10"><h3><u><?php echo "Invoicing Details";?></u></h3>
						
							<ul class="other-dets_m">
							<li>
					        	<h3><?php echo "Contact"; ?>:</h3>
					        	<p><input type="text" name="contact" class="do_input_new full_wdth_me" value="<?php echo $contact;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
							<li>
					        	<h3><?php echo "Address"; ?>:</h3>
					        	<p><input type="text" name="address" class="do_input_new full_wdth_me" value="<?php echo $address;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
							<li>
					        	<h3><?php echo "City"; ?>:</h3>
					        	<p><input type="text" name="city" class="do_input_new full_wdth_me"value="<?php echo $city;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
			                <li>
					        	<h3><?php echo "State"; ?>:</h3>
					        	<p><select name="state" class="do_input_new" <?php if($status==3){echo 'disabled';} ?> >
								<?php
									$state_query = "select * from ".$wpdb->prefix."states";
									$state_results = $wpdb->get_results($state_query);
									
									foreach($state_results as $state)
									{
										echo '<option value="'.$state->state_id.'" '.($state_id==$state->state_id ? 'selected="selected"' : "").' >'.$state->state_abbreviation.'</option>';
									
									}?>
								</select></p>
							</li>
							<li>
					        	<h3><?php echo "Zip"; ?>:</h3>
					        	<p><input type="text" name="zip" class="do_input_new full_wdth_me"value="<?php echo $zip;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
							<li>
					        	<h3><?php echo "Email"; ?>:</h3>
					        	<p><input type="text" name="email"class="do_input_new full_wdth_me"value="<?php echo $email;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
			                <li>
					        	<h3><?php echo "Phone"; ?>:</h3>
					        	<p><input type="text" name="phone" class="do_input_new full_wdth_me"value="<?php echo $phone;?>"
									<?php if($status==3){echo 'disabled';} ?> /></p>
							</li>
							<li>
					        	<h3><?php echo "Delivery Type"; ?>:</h3>
					        	<p><select class ="do_input_new" name="delivery_type" <?php if($status==3){echo 'disabled';} ?>> 
								<option <?php if($delivery_type == "Email"){echo "selected=selected";}?>><?php echo "Email";?></option>
								<option <?php if($delivery_type == "Hard Copy"){echo "selected=selected";}?>><?php echo "Hard Copy";?></option>
								<option <?php if($delivery_type == "Both"){echo "selected=selected";}?>><?php echo "Both";?></option>
								</select>
								</p>
							</li>
							
							</ul>
							</div> </div></div>
						<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Other Invoicing Details";?></h3>
						<ul class="xoxo">
							<li class="widget-container widget_text" id="ad-other-details">
								<ul class="other-dets other-dets2">
								<li><h3>Contract Document:</h3><p><?php if($current_document!="Contract"){echo '<font color="red">'.$current_document.'</font>';}else{echo $current_document;}?></p></li>
								<li><h3>Document Number (P.O., etc.):</h3><p><?php if(empty($document_number)){echo "None";}else {echo $document_number;}?></p></li>
								<li><h3>Receipts Required:</h3><p><?php if($details[0]->receipt==1){echo 'Yes';}else{echo 'No';}?></p></li>
								<li><h3>Return/Destroy Requirement:</h3><p><?php if($details[0]->return_destroy==1){echo 'Yes';}else{echo 'No';}?></p></li>
								<li><h3>Background Checks:</h3><p><?php if($details[0]->background==1){echo 'Yes';}else{echo 'No';}?></p></li>
								<li><h3>DOES:</h3><p><?php if($details[0]->does==1){echo 'Yes';}else{echo 'No';}?></p></li>
								<li><h3>10% Markup:</h3><p><?php if($details[0]->markup==1){echo 'Yes';}else{echo 'No';}?></p></li>
								</ul>
							</li>
						</ul>
						</div></div>
                	<div id="content">
						<div class="my_box3">
						<div class="padd10"><h3><u><?php echo "Project Notes";?></u></h3>							
							<ul class="other-dets_m">
							<li>
								<h3><?php echo "Internal Notes"; ?></h3>
								<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
								placeholder="<?php echo "Make any notes about this project that other staff should know"; ?>" name="notes">
								<?php echo $notes;?></textarea></p>
							</li>
							<li><p><input type="submit" name="update-info" class="my-buttons" value="<?php echo "Update Information"; ?>" /></p></li>
							</ul>        
						</div>
						</div>
					</div>
				<?php if($status != 3)
				{
					?>
					<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Other Project Actions";?></h3>
						<ul class="xoxo">
							<li class="widget-container widget_text" id="ad-other-details">	
					<ul class="other-dets_m">
							<li>&nbsp;</li>
							<li><a href="/?p_action=project_invoice&ID=<?php echo $checklist;?>" class="post_bid_btn"><?php echo "Enter Invoice"; ?></a></li>
							<li>&nbsp;</li>
							<li>&nbsp;</li>
							<?php echo '<li><a href="/?p_action=adserv_checklist&ID='.$checklist.'" class="post_bid_btn">Add Ad Serv</a></li>'; ?>
							<li>&nbsp;</li>
							<li>&nbsp;</li>
							<li><a href="/?p_action=close_project&ID=<?php echo $checklist;?>" class="post_bid_btn"><?php echo "Close Project"; ?></a></li>							</li>
							<li>&nbsp;</li>
					</ul>
					</li></ul></div></div>
					<?php
				}
				?>
				</form>
</div>
</div>
<?php get_footer();?>