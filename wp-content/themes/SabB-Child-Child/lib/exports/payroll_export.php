<?php
function billyb_payroll_export()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11,60,66,104,230,235);//Bill B, Jessica Kelley, Cho Smith, Caitlin Sanchez, Natasha Pereira
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	if(isset($_POST['save-info']))
	{
		$wpdb->query("update ".$wpdb->prefix."timesheets 
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
			set timesheet_status=2 
			where timesheet_status=1 and user_comp_type in ('hourly','Non-exempt')");
		
		$records = $_POST['record'];
		foreach($records as $record)
		{
			$ids = explode(",",$record['ids']);
			for($i=0;$i<count($ids);$i++)
			{
				$id = $ids[$i];
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=2 where timesheet_id=%d",$id));
			}
		}
		
		ob_end_clean();
		$filename = "adp".time().".csv";
		$output = @fopen('php://output', 'w');
		fputcsv($output, array('co code','batch id','file #','Temp Dept','Temp Rate','Reg Hours'));
							
		$hourlyresults = $wpdb->get_results("select user_id,adp_id,adp_department,user_wage from ".$wpdb->prefix."useradd where 
			user_comp_type='hourly'");
							
		foreach($hourlyresults as $a)
		{
			$user_id = $a->user_id;
			$adp_id = $a->adp_id;
			$wage = $a->user_wage;
			$department = $a->adp_department;
			$beg_period = strtotime(date('Y-m-01',time()). " -2 months");
			$time_off_array = array('Vacation','Float','MATPAT','BEREAV','Sick','JURY','Holiday');
			
			$query_results = $wpdb->get_results($wpdb->prepare("select timesheet_id,timesheet_date,timesheet_hours from ".$wpdb->prefix."timesheets
				where user_id=%d and timesheet_status=2 and timesheet_hours>0 and timesheet_date>=%d order by timesheet_date",$user_id,$beg_period));
			
			$week = date('W',$beg_period);
			$worked_hours = 0;
			$total_hours = 0;
			$overtime = 0;
			
			foreach($query_results as $qa)
			{
				if(!in_array($qa->project_id,$time_off_array))
				{
					//Need to add 1 day to get php week to align with B&D payroll of Saturday to Friday
					if($week == date('W',strtotime(date('Y-m-d',$qa->timesheet_date). ' + 1 day')))
					{
						$worked_hours += $qa->timesheet_hours;
					}
					else
					{
						if($worked_hours > 40)
						{
							$overtime += ($worked_hours-40); $t++;
						}
						$worked_hours = $qa->timesheet_hours;//reset worked hours to new value
					}
					$week = date('W',strtotime(date('Y-m-d',$qa->timesheet_date). ' + 1 day'));
				}
				$total_hours += $qa->timesheet_hours;
			}
			if($total_hours >0)
			{
				if($overtime > 0)
				{
					$csv = array('D97','1',$adp_id,$department,$wage,$total_hours-$overtime);
					fputcsv($output,$csv);
					
					$csv = array('D97','1',$adp_id,$department,$wage * 1.5,$overtime);
					fputcsv($output,$csv);
				}
				else
				{
					$csv = array('D97','1',$adp_id,$department,$wage,$total_hours);
					fputcsv($output,$csv);
				}
			}
		}
		
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$csv = array('D97','1',$details[0],$details[1],$details[2],$details[3]);
			
			fputcsv($output,$csv);
		}
		fclose($output);
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv");
		exit();
	}
	else{
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
							<?php
							if($uid == 11 or $uid == 66 or $uid==235)
							{
								echo '<li>
									<input type="submit" name="save-info" class="my-buttons" value="Download Export" />
									<a href="/confirm-adp-upload" class="my_buttons">Confirm ADP Upload</a></li>
									<li>&nbsp;</li>';
							}
							?>
							<li><h2>Hourly</h2></li>
							<?php
							//BillyB modify query to be inner join and display "No Results" if empty
							$hourlyresults = $wpdb->get_results("select display_name,user_id,adp_id,user_wage,adp_department from ".$wpdb->prefix."useradd 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
								where user_comp_type='hourly' order by display_name");
							
							echo '<li><table width="100%">
								<tr>
								<th>Name</th>
								<th>File #</th>
								<th>Dept</th>
								<th>Rate</th>
								<th>Hours</th>
								</tr>';
							foreach($hourlyresults as $h)
							{
								$user_id = $h->user_id;
								$adp_id = $h->adp_id;
								$wage = $h->user_wage;
								$display_name = $h->display_name;
								$department = $h->adp_department;
								$beg_period = strtotime(date('Y-m-01',time()). " -2 months");
								$time_off_array = array('Vacation','Float','MATPAT','BEREAV','Sick','JURY','Holiday');
								
								$query_results = $wpdb->get_results($wpdb->prepare("select timesheet_id,timesheet_date,timesheet_hours 
									from ".$wpdb->prefix."timesheets
									where user_id=%d and timesheet_status=1 and timesheet_hours>0 and timesheet_date>=%d
									order by timesheet_date",$user_id,$beg_period));
								
								$week = date('W',$beg_period);
								$worked_hours = 0;
								$total_hours = 0;
								$overtime = 0;
								
								foreach($query_results as $qr)
								{
									if(!in_array($qr->project_id,$time_off_array))
									{
										//Need to add 1 day to get php week to align with B&D payroll of Saturday to Friday
										if($week == date('W',strtotime(date('Y-m-d',$qr->timesheet_date). ' + 1 day')))
										{
											$worked_hours += $qr->timesheet_hours;
										}
										else
										{
											if($worked_hours > 40)
											{
												$overtime += ($worked_hours-40); $t++;
											}
											$worked_hours = $qr->timesheet_hours;//reset worked hours to new value
										}
										$week = date('W',strtotime(date('Y-m-d',$qr->timesheet_date). ' + 1 day'));
									}
									$total_hours += $qr->timesheet_hours;
								}
								if($total_hours >0)
								{
									if($overtime > 0)
									{
										echo '<tr>
											<td>'.$display_name.'</td>
											<td>'.$adp_id.'</td>
											<td>'.$department.'</td>
											<td>'.number_format($wage,2).'</td>
											<td>'.number_format($total_hours-$overtime,2).'</td>
											</tr>';
										echo '<tr>
											<td>'.$display_name.' (overtime)</td>
											<td>'.$adp_id.'</td>
											<td>'.$department.'</td>
											<td>'.number_format($wage * 1.5,2).'</td>
											<td>'.number_format($overtime,2).'</td>
											</tr>';	
									}
									else
									{
										echo '<tr>
											<td>'.$display_name.'</td>
											<td>'.$adp_id.'</td>
											<td>'.$department.'</td>
											<td>'.number_format($wage,2).'</td>
											<td>'.number_format($total_hours,2).'</td>
											</tr>';
									}
								}
							}
							echo '</table></li>';
							echo '<li>&nbsp;</li>';
							
							
							$detailed_hourly_query = $wpdb->get_results("select display_name,".$wpdb->prefix."timesheets.user_id,timesheet_date,timesheet_hours,abbreviated_name,
								project_name,".$wpdb->prefix."projects.gp_id,".$wpdb->prefix."timesheets.project_id 
								from ".$wpdb->prefix."useradd 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
								inner join ".$wpdb->prefix."timesheets on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."timesheets.user_id
								left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
								where user_comp_type='hourly' and timesheet_status=1 and timesheet_date>='$beg_period'
								order by display_name,timesheet_date,timesheet_hours");
							if(!empty($detailed_hourly_query))
							{
								echo '<li><input type="button" id="show_details" value="Show Details" onClick="showDetails();" /></li>';
								echo '<div><span id="hourly_span" style="display:none;">';
								?>
								<script language="javascript" type="text/javascript">
				
								function showDetails(){
									var span = document.getElementById('hourly_span');
									var button = document.getElementById('show_details');
									if(button.value == 'Show Details'){
										button.value = 'Hide Details';
										span.style.display = "block";
									}
									else{
										button.value = 'Show Details';
										span.style.display = "none";
									}
								}
								</script>
								<?php
								echo '<li><table width="100%">';
								echo '<tr><th><strong><u>Name</u></strong></th>
									<th><strong><u>Date</u></strong></th>
									<th><strong><u>Hours</u></strong></th>
									<th><strong><u>Project</u></strong></th>
									</tr>';
								
								$name = "";
								foreach($detailed_hourly_query as $d)
								{
									if(!empty($d->abbreviated_name)){$project_name = $d->abbreviated_name;}
									elseif(!empty($d->project_name)){$project_name = $d->project_name;}
									elseif(!empty($d->gp_id)){$project_name = $d->gp_id;}
									else{$project_name = $d->project_id;}
									
									echo '<tr><td>'.($d->display_name!=$name ? $d->display_name : "").'</td>
										<td>'.date('m-d-Y',$d->timesheet_date).'</td>
										<td>'.$d->timesheet_hours.'</td>
										<td>'.$project_name.'</td>
										</tr>';
									$name = $d->display_name;
								}
								echo '</table></li></span></div>';
							}
							?>
							<li><h2>Non-Exempt</h2></li>
							<li>
							<table width="100%">
							<tr>
							<th><?php echo "Name";?></th>
							<th><?php echo "File #";?></th>
							<th><?php echo "Dept";?></th>
							<th><?php echo "Rate";?></th>
							<th><?php echo "Hours";?></th>
							</tr>
							<?php
							//BillyB modify query to be inner join and display "No Results" if empty
							$ne_results = $wpdb->get_results("select display_name,user_id,adp_id,user_wage,adp_department from ".$wpdb->prefix."useradd 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
								where user_comp_type='Non-exempt'");
							
							$beg_period = strtotime(date('Y-m-01',time()). " -2 months");
							$time_off_array = array('Vacation','Float','MATPAT','BEREAV','Sick','JURY','Holiday');
							$t = 0;
							$timesheets_array = array();
							
							foreach($ne_results as $ne_employee)
							{
								$user_id = $ne_employee->user_id;
								$adp_id = $ne_employee->adp_id;
								//BillyB need to input hourly wage rate then multiply by 1.5 for overtime hours
								$wage = $ne_employee->user_wage;
								$display_name = $ne_employee->display_name;
								$department = $ne_employee->adp_department;
								
								$hours_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."timesheets
									where user_id=%d and timesheet_status=1 and timesheet_hours>0 and timesheet_date>=%d
									order by timesheet_date",$user_id,$beg_period));
								
								$week = date('W',$beg_period);
								$worked_hours = 0;
								$payable = 0;
								
								if($user_id==263)//run for overtime calculation by day
								{
									$date = $hours_results[0]->timesheet_date;
									foreach($hours_results as $hr)
									{
										if($date==$hr->timesheet_date)
										{
											$worked_hours += $hr->timesheet_hours;
											if($worked_hours > 8){array_push($timesheets_array,$hr->timesheet_id);}
										}
										else
										{
											if($worked_hours>8)
											{
												$payable += ($worked_hours-8); $t++;
												if($hr->timesheet_id==$hours_results[count($hours_results)-1]->timesheet_id)
												{
													array_push($timesheets_array,$hr->timesheet_id);
												}
											}
											$worked_hours = $hr->timesheet_hours;
										}
										$date = $hr->timesheet_date;
									}
								}
								
								else//run for overtime calculation by week
								{
									foreach($hours_results as $hr)
									{
										if(!in_array($hr->project_id,$time_off_array))
										{
											//Need to add 2 days to get php week to align with B&D payroll of Saturday to Friday
											if($week == date('W',strtotime(date('Y-m-d',$hr->timesheet_date). ' + 2 days')))
											{
												$worked_hours += $hr->timesheet_hours;
												if($worked_hours > 40){array_push($timesheets_array,$hr->timesheet_id);}
											}
											else
											{
												if($worked_hours > 40)
												{
													$payable += ($worked_hours-40); $t++;
													if($hr->timesheet_id==$hours_results[count($hours_results)-1]->timesheet_id)
													{
														array_push($timesheets_array,$hr->timesheet_id);
													}
												}
												$worked_hours = $hr->timesheet_hours;
											}
											$week = date('W',strtotime(date('Y-m-d',$hr->timesheet_date). ' + 2 days'));
										}
									}
									if($worked_hours > 40){$payable += ($worked_hours-40);}
								}
								
								if($payable > 0)
								{
									$string = "";
									for($i=0;$i<count($timesheets_array);$i++)
									{
										if($i<(count($timesheets_array)-1)){$string .= $timesheets_array[$i].",";}
											else{$string .= $timesheets_array[$i];}
									}
									
									echo '<tr><th>'.$display_name.'</th>
										<th>'.$adp_id.'</th>
										<th>'.$department.'</th>
										<th>'.$wage.'</th>
										<th>'.$payable.'</th>
										<input type="hidden" value="'.$string.'" name="record['.$user_id.'][ids]" />
										<input type="hidden" value="'.$adp_id.',,,'.$department.',,,'.$wage.',,,'.$payable.'" name="record['.$user_id.'][details]" /></tr>';
										
								}
							}
							?>
							
							</table>
							</li>
							<?php
							if(!empty($timesheets_array))
							{
								echo '<li>&nbsp;</li>';
								echo '<li><input type="button" id="show_ne_details" value="Show Details" onClick="showNEDetails();" /></li>';
								echo '<div><span id="ne_span" style="display:none;">';
								?>
								<script language="javascript" type="text/javascript">
				
								function showNEDetails(){
									var span = document.getElementById('ne_span');
									var button = document.getElementById('show_ne_details');
									if(button.value == 'Show Details'){
										button.value = 'Hide Details';
										span.style.display = "block";
									}
									else{
										button.value = 'Show Details';
										span.style.display = "none";
									}
								}
								</script>
								<?php
								echo '<li><table width="100%">';
								echo '<tr><th><strong><u>Name</u></strong></th>
									<th><strong><u>Date</u></strong></th>
									</tr>';
								
								$name = "";
								foreach($timesheets_array as $t)
								{
									$results = $wpdb->get_results($wpdb->prepare("select display_name,timesheet_date
										from ".$wpdb->prefix."timesheets
										inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
										where timesheet_id=%d",$t));
									
									echo '<tr><td>'.($results[0]->display_name!=$name ? $results[0]->display_name : "").'</td>
										<td>'.date('m-d-Y',$results[0]->timesheet_date).'</td>
										</tr>';
									$name = $results[0]->display_name;
								}
								echo '</table></li></span></div>';
								echo '<li>&nbsp;</li>';
							}
							if($t==0)
							{
								echo 'There are no approved overtime hours that need to be paid';
								echo '<li>&nbsp;</li>';
							}
							
							if($uid ==11 or $uid == 66 or $uid==235)
							{
								echo '<li><input type="submit" name="save-info" class="my-buttons" value="Download Export" />
									<a href="/confirm-adp-upload" class="my_buttons">Confirm ADP Upload</a></li>';
							}
							echo '<li>&nbsp;</li>';
							echo '<li><h2>Unapproved Time</h2></li>';
							echo '<li><table width="100%">
								<tr>
								<th><strong>Name</strong></th>
								<th><strong>Date</strong></th>
								<th><strong>Hours</strong></th>
								<th><strong>Project</strong></th>
								</tr>';
							
							$unapproved_query = "select display_name,timesheet_date,timesheet_hours,project_id,project_name,abbreviated_name
								from ".$wpdb->prefix."timesheets
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
								left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
								where user_comp_type!='' and timesheet_status=0
								order by display_name,timesheet_date";
							$unapproved_results = $wpdb->get_results($unapproved_query);
							
							$users_array = array();
							
							foreach($unapproved_results as $ur)
							{
								if(!empty($ur->abbreviated_name)){$project = $ur->abbreviated_name;}
								elseif(!empty($ur->project_name)){$project = $ur->project_name;}
								else{$project = $ur->project_id;}
								if(!in_array($ur->display_name,$users_array))
								{
									$name = $ur->display_name;
									array_push($users_array,$ur->display_name);
								}
								else{$name = "&nbsp;";}
								echo '<tr>
									<td>'.$name.'</td>
									<td>'.date('m-d-Y',$ur->timesheet_date).'</td>
									<td>'.$ur->timesheet_hours.'</td>
									<td>'.$project.'</td>
									</tr>';
							}
							echo '</table></li>';
							?>
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>	
<?php  }
}
add_shortcode('payroll_export','billyB_payroll_export') ?>