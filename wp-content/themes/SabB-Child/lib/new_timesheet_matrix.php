<?php
function billyB_new_timesheet_matrix()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	$uid = $current_user->ID;
	$name = $current_user->display_name;
	
	if($current_user->ID == 11)
	{
		$wpdb->show_errors = true;
		$wpdb->suppress_errors = false;
		$wpdb->print_error();
	
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);
	}

	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		
		$timeperiod1 = $_POST['time_period'];
		$status = 0; if($uid==37 or $uid==38){$status = 1;}
		
		$records = ($_POST['record']);
		
		foreach($records as $record)
		{
			$details = explode(",",$record['details']);
			if($record['time'] != 0)
			{
				$submitted = time();
				$project = $details[0];
				$date = $details[1];
				$task = $details[3];
				if(!empty($details[2])){$original_value = $details[2];}else{$original_value=0;}
				
				$hours = $record['time'];
				if($original_value == 0)
				{
					$recordquery = $wpdb->prepare("insert into ".$wpdb->prefix."timesheets (user_id,submitted_date,timesheet_date,project_id,task_id,timesheet_hours,timesheet_status,origination)
						values(%d,%d,%d,%s,%d,%f,%d,%s)",$uid,$submitted,$date,$project,$task,$hours,$status,'Timesheet Matrix');
					$wpdb->query($recordquery);
				}
				if($hours != $original_value)
				{
					$update_query = $wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_hours=%f,submitted_date=%d,timesheet_status=0 where project_id=%s 
						and task_id=%d and timesheet_date=%d and user_id=%d",$hours,$submitted,$project,$task,$date,$uid);
					$wpdb->query($update_query);
				}
				
				$timeoff_array = array('Vacation','Sick','Float','Bereav','Jury','Mat/Pat','Holiday');
				if(in_array($project,$timeoff_array) and $hours != $original_value)
				{
					//BillyB excluded Holiday from the timeoff_array
					if($original_value !=0)
					{
						$delete_timeoff = $wpdb->prepare("delete from ".$wpdb->prefix."request_timeoff where employee_id=%d and request_date=%d and request_type=%s
							and request_hours=%f",$uid,$date,$project,$original_value);
						$wpdb->query($delete_timeoff);
					}
					$report_id = $uid.$submitted;
					if($project == 'Vacation'){$code='V';}elseif($project == 'Sick'){$code='S';}elseif($project == 'Holiday'){$code='H';}
						elseif($project == 'Float'){$code='F';}elseif($project == 'Bereav'){$code='G';}elseif($project == 'Jury'){$code='J';}
						else{$code='M';}
					
					$timeoff_entry = $wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff (request_report_id,employee_id,request_date,request_type,request_code,request_hours,
						request_status,date_requested,origination) values (%d,%d,%d,%s,%s,%f,0,%d,%s)",$report_id,$uid,$date,$project,$code,$hours,$submitted,'Timesheet Matrix');
					$wpdb->query($timeoff_entry);
					
					$report_to_email_query = $wpdb->prepare("select user_email from ".$wpdb->prefix."users 
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."useradd.reports_to=".$wpdb->prefix."users.ID
						where ".$wpdb->prefix."useradd.user_id=%d",$uid);
					$report_to_email_results = $wpdb->get_results($report_to_email_query);
					$report_to = $report_to_email_results[0]->user_email;
					$subject = $name.' has submitted a time off request for your review';
					$link = 'http://opdash.programmanagers.com/approve-time-off/';
					$message = 'Please go to '.$link.' to review all time off requests pending your review';
					wp_mail($report_to,$subject,$message);
				}
			}
			elseif($record['time'] == 0 and $details[2] !=0)
			{
				$project = $details[0];
				$date = $details[1];
				$task = $details[3];
				if(!empty($details[2])){$original_value = $details[2];}else{$original_value=0;}
				
				$delete_query = $wpdb->prepare("delete from ".$wpdb->prefix."timesheets where timesheet_date=%d and project_id=%s
					and task_id=%d and user_id=%d",$date,$project,$task,$uid);
				$wpdb->query($delete_query);
				
				$timeoff_array = array('Vacation','Sick','Float','Bereav','Jury','Mat/Pat','Holiday');
				if(in_array($project,$timeoff_array))
				{
					$delete_timeoff = $wpdb->prepare("delete from ".$wpdb->prefix."request_timeoff where employee_id=%d and request_date=%d and request_type=%s
						and request_hours=%f",$uid,$date,$project,$original_value);
					$wpdb->query($delete_timeoff);
				}
			}
		}
		echo "The timesheet has been saved.<br/><br/>";
		?>
		</div></div></div>

		<?php
		wp_redirect(get_bloginfo('siteurl')."/timesheet-matrix/"); exit; 
	}
	?>   
		<script type="text/javascript">
				var form_being_submitted = false;
				function checkForm(){
					var myForm = document.forms.timesheet_matrix;
					var saveInfo = myForm.elements['save-info'];
					var saveInfoTwo = myForm.elements['save-info-two'];
					
					if(form_being_submitted){
					alert('The form is being submitted, please wait a moment...');
					saveInfo.disabled = true;
					saveInfoTwo.disabled = true;
					return false;
					}
					saveInfo.value = 'Submitting form...';
					saveInfoTwo.value = 'Submitting form...';
					
					form_being_submitted = true;
					return true;
					
				}
			</script>
		<form method="post" name="timesheet_matrix" enctype="multipart/form-data" onsubmit="checkForm();">
			<div id="content-full">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<li><h3><?php echo "Time Period Start:";?></h3>
				<p><select class="do_input_new" name="time_period">
				<?php
					$change_array = array(11,60,74,66,104,221,235);//allow users to change the view to someone else
					if(isset($_POST['update-uid']))
					{
						$timeperiod1 = trim($_POST['time_period']);
						$uid = $_POST['uid'];
					}
					
					$periodsquery = "select * from ".$wpdb->prefix."time_periods where active='on' order by start_date desc";
					$periodsresult = $wpdb->get_results($periodsquery);
					if(isset($_POST['set-time-period']))
					{
						$timeperiod1 = trim($_POST['time_period']);
						if(in_array($current_user->ID,$change_array))
						{
							$uid = $_POST['uid'];
						}
					}
					$now = time();
					foreach($periodsresult as $period)
					{
						if(empty($timeperiod1) and strtotime($period->start_date) < $now and strtotime($period->end_date)+86399 > $now){$timeperiod1=$period->time_period_id;}
						echo '<option '.($period->time_period_id==$timeperiod1 ? 'selected="selected"' : '').' 
							value="'.$period->time_period_id.'">'.date('m-d-Y',strtotime($period->start_date)).'</option>';
					}
					?>
					</select>
					<input type="submit" name="set-time-period" class="my-buttons" value="<?php echo "Update Period"; ?>" /></p></li>
					<?php
					if(in_array($current_user->ID,$change_array))
					{
						echo '<li><h3>&nbsp;</h3><p><select class="do_input_new" name="uid">';
						$users_query = "select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where ".$wpdb->prefix."useradd.status=1 order by display_name";
						if($current_user->ID==11)
						{
							$users_query = "select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users order by display_name";
						}
						$user_results = $wpdb->get_results($users_query);
						foreach($user_results as $user)
						{
							echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
						}
						echo '</select>';
						echo '<input type="submit" class="my-buttons" value="Change User" name="update-uid" /></p>';
					}
					?>
				</div>
				</div>						
			</div>
			<div id="content-full">
				<div class="my_box3">
				<div class="padd10">
								
				<ul class="other-dets_m">
					<?php
					if($current_user->ID==$uid)
					{
						echo '<li><p><input type="submit" name="save-info-two" id="save-info-two" class="my-buttons" value="Save" /></p></li>';
						echo '<li>&nbsp;</li>';
					}
					
					echo '<style>input[type=number]{width:50px;}</style>';
					 
					$periodquery = $wpdb->prepare("select * from ".$wpdb->prefix."time_periods where time_period_id=%d",$timeperiod1);
					$periodresults = $wpdb->get_results($periodquery);
					
					$start = strtotime($periodresults[0]->start_date);
					$end = strtotime($periodresults[0]->end_date);							
					
					/*
					Should run query to see all projects->task as active or in the the timesheet table, sort by project, task, date
					can then exclude other projects and timeoff queries
					
					*/
					
					$queryactive = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,".$wpdb->prefix."projects.client_id,
						".$wpdb->prefix."projects.project_name,".$wpdb->prefix."projects.gp_id,abbreviated_name,gp_id,client_name,
						status
						from ".$wpdb->prefix."projects 
						inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id
						inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
						where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status in (2,4,5,6) and project_parent=0
						order by ".$wpdb->prefix."projects.status,client_name,".$wpdb->prefix."projects.ID",$uid);
					$resultactive = $wpdb->get_results($queryactive);
					/*  EDITED QUERY... this excludes the project-task combination of task_id=0 when other tasks exist on the project (like when tasks are created after project time has been recorded to task_id=0)
					$queryactive = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,".$wpdb->prefix."projects.client_id,
						".$wpdb->prefix."projects.project_name,".$wpdb->prefix."projects.gp_id,abbreviated_name,gp_id,client_name,
						status,".$wpdb->prefix."tasks.task_id,task_description,task_name
						from ".$wpdb->prefix."projects 
						inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id
						inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
						left join ".$wpdb->prefix."tasks on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."tasks.project_id
						where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status in (2,4,5,6) and project_parent=0
						order by ".$wpdb->prefix."projects.status,client_name,".$wpdb->prefix."projects.ID",$uid);
					$resultactive = $wpdb->get_results($queryactive);
					*/
					echo '<li style="overflow-x: auto;"><table class="timesheet-matrix-table"><thead><tr><th>Project</th>';
					
					for($i = $start, $t = 1; $i <= $end; $i = $i + 86400, $t++)
					{
						echo '<th>'.((date('D',$i) != 'Sun' and date('D',$i) != 'Sat') ? '<b>'.date('D', $i).'<br/><u>'.date('m-d',$i).'</u></b>' : date('D', $i).'<br/>'.date('m-d',$i)).'</th>';
					}
					echo '</tr></thead><tbody>';
					for($i = $start, $t =0; $i <= $end; $i = $i + 86400, $t++)
					{ ${"variable$t"} = 0;}
					
					foreach($resultactive as $project)
					{
						if($project->status==2){$string = "project_card";}else{$string = "edit_opportunity";}
						
						$project_id = $project->ID;
						
						$task_query = $wpdb->prepare("select * from ".$wpdb->prefix."tasks where project_id=%s and time_entry=1 and task_status=0",$project_id);
						$task_results = $wpdb->get_results($task_query);
						
						$abb_name = $project->abbreviated_name;
						
						if(empty($abb_name))
						{
							$abb_name = $project->project_name;
							if(empty($abb_name))
							{
								$abb_name = $project->gp_id;
								if(empty($abb_name))
								{
									$abb_name = "Unnamed Project:  ".$project->client_name;
								}
							}
						}
						if(empty($task_results))
						{
							echo '<tr><th><b><u><a href="/?p_action='.$string.'&ID='.$project_id.'" target="_blank" >'.$abb_name.'</a></u></b></th>';
							$task = 0;
							for($i = $start, $t =0; $i <= $end; $i = $i + 86400, $t++)
							{
								$get_hours_query = $wpdb->prepare("select sum(timesheet_hours) as sum,timesheet_status from ".$wpdb->prefix."timesheets 
									where project_id=%s and timesheet_date=%d and user_id=%d",$project_id,$i,$uid);
								$get_hours_results = $wpdb->get_results($get_hours_query);
								if(!empty($get_hours_results)){$hours = $get_hours_results[0]->sum;}else{$hours="";}
								echo '<input type="hidden" name="record['.$project_id.'day'.$i.'][details]" value="'.$project_id.','.$i.','.$hours.','.$task.'" />';
								echo '<th><input type="number" name="record['.$project_id.'day'.$i.'][time]" min="0" max="24" step=".25" value="'.$hours.'" 
									'.($get_hours_results[0]->timesheet_status > 1 ? "readonly" : "" ).'/></th>';
								${"variable$t"} += $hours;
							}
							echo '</tr>';
						}
						else
						{
							foreach($task_results as $task)
							{
								$task_id = $task->task_id;
								echo '<tr><th><b><u><a href="/?p_action='.$string.'&ID='.$project_id.'" target="_blank" 
									title="'.$task->task_description.'">'.$abb_name.': '.$task->task_name.'</a></u></b></th>';
								
								for($i = $start, $t =0; $i <= $end; $i = $i + 86400, $t++)
								{
									$get_hours_query = $wpdb->prepare("select sum(timesheet_hours) as sum from ".$wpdb->prefix."timesheets where project_id=%s
										and timesheet_date=%d and user_id=%d and task_id=%d",$project_id,$i,$uid,$task_id);
									$get_hours_results = $wpdb->get_results($get_hours_query);
									if(!empty($get_hours_results)){$hours = $get_hours_results[0]->sum;}else{$hours="";}
									echo '<input type="hidden" name="record['.$project_id.$task_id.'day'.$i.'][details]" value="'.$project_id.','.$i.','.$hours.','.$task_id.'" />';
									echo '<th><input type="number" name="record['.$project_id.$task_id.'day'.$i.'][time]" min="0" max="24" step=".25" value="'.$hours.'" /></th>';
								${"variable$t"} += $hours;
								}
								echo '</tr>';
							}
						}
					}
					$query_admin = "select * from ".$wpdb->prefix."other_project_codes where timesheet_available=1";
					$result_admin = $wpdb->get_results($query_admin);
					
					$add_projects_array = array();
					
					foreach($result_admin as $project)
					{
						$project_id = $project->other_project_code_value;
						$abb_name = $project->other_project_code_name;
						
						$total_hours_check = $wpdb->prepare("select timesheet_hours from ".$wpdb->prefix."timesheets where project_id=%s and timesheet_date>=%d
							and timesheet_date<=%d and user_id=%d",$project_id,$start,$end,$uid);
						$total_hours_results = $wpdb->get_results($total_hours_check);
						if(count($total_hours_results) > 0 or $project_id!='8000')
						{
							echo '<tr><th><b><u>'.$abb_name.'</u></b></th>';
							$task = 0;
							for($i=$start, $t=0; $i<=$end; $i=$i+86400, $t++)
							{
								$get_admin_query = $wpdb->prepare("select sum(timesheet_hours) as sum,timesheet_status from ".$wpdb->prefix."timesheets 
									where project_id=%s and timesheet_date=%d and user_id=%d",$project_id,$i,$uid);
								$get_admin_results = $wpdb->get_results($get_admin_query);
								if(!empty($get_admin_results)){$hours = $get_admin_results[0]->sum;}else{$hours="";}
								{
									echo '<input type="hidden" name="record['.$project_id.'day'.$i.'][details]" value="'.$project_id.','.$i.','.$hours.','.$task.'" />';
									echo '<th><input type="number" name="record['.$project_id.'day'.$i.'][time]" min="0" max="24" step=".25" value="'.$hours.'" 
										'.($get_admin_results[0]->timesheet_status > 1 ? "readonly" : "" ).' /></th>';
								}
								${"variable$t"} += $hours;
							}
							echo '</tr>';
						}
					}
					$othercodes = array('Vacation','Sick','Holiday','Bereav','Float','Jury','Mat/Pat');
					
					foreach($othercodes as $other)
					{
						$abb_name = $other;
						$total_hours_check = $wpdb->prepare("select timesheet_hours from ".$wpdb->prefix."timesheets 
							where project_id=%s and timesheet_date>=%d
							and timesheet_date<=%d and user_id=%d",$other,$start,$end,$uid);
						$total_hours_results = $wpdb->get_results($total_hours_check);
						
						$task = 0;
						if(count($total_hours_results) > 0)
						{
							echo '<tr><th><b><u>'.$abb_name.'</u></b></th>';
							for($i=$start, $t=0; $i<=$end; $i=$i+86400, $t++)
							{
								$end_of_day = $i+86399;
								$get_other_query = $wpdb->prepare("select timesheet_id,timesheet_hours,timesheet_status,request_status 
									from ".$wpdb->prefix."timesheets 
									left join ".$wpdb->prefix."request_timeoff on user_id=employee_id and timesheet_date=request_date 
										and timesheet_hours=request_hours
									where project_id=%s and timesheet_date>=%d
										and timesheet_date<=%d and user_id=%d",$other,$i,$end_of_day,$uid);
								$get_other_results = $wpdb->get_results($get_other_query);
								$hours = 0;
								$edit = "";
								if(!empty($get_other_results)){$hours = $get_other_results[0]->sum;}else{$hours="";}
								{
									$timesheet_array = array();
									foreach($get_other_results as $gor)
									{
										if($gor->request_status > 1){$edit = 'no';}
										if(!in_array($gor->timesheet_id,$timesheet_array))
										{
											$hours += $gor->timesheet_hours;
											array_push($timesheet_array,$gor->timesheet_id);
										}
									}
									if($hours == 0){$hours = "";}
									echo '<input type="hidden" name="record['.$abb_name.'day'.$i.'][details]" value="'.$abb_name.','.$i.','.$hours.','.$task.'" />';
									echo '<th><input type="number" name="record['.$abb_name.'day'.$i.'][time]" min="0" max="24" step=".25" value="'.$hours.'" 
										'.($edit == 'no' ? "readonly" : "" ).' /></th>';
								}
								${"variable$t"} += $hours;
							}
							echo '</tr>';
						}
						if(count($total_hours_results) == 0){array_push($add_projects_array,$other);}
					}
					//adding Total***************************************************************
					
					echo '<tr><th>Daily Total</th>';
					for($i = $start, $t = 0; $i <= $end; $i = $i + 86400, $t++)
					{
						echo '<th '.(($i < ($now-86399) and ${"variable$t"} < 8 and date('D',$i) !="Sun" and date('D',$i)!="Sat") ? 'bgcolor="f68286"' : 'bgcolor="f6ee82"').'><b><font size="2">'.${"variable$t"}.'</font></b></th>';
					}
					echo '</tr>';
				
					//extra rows
					$extra_rows = 2;
					
					echo '</tbody></table></li>';
					if($current_user->ID==$uid)
					{
						echo '<li><p><input type="submit" name="save-info" id="save-info" class="my-buttons" value="Save" /></p></li>';
					}
					?>
				</ul>
				</div>
				</div>
			</div>
			
			</form>
			<div id="content-full">
				<div class="my_box3">
				<div class="padd10"><h2><?php echo "Tips";?></h2>
								
				<ul class="other-dets_m">

						<?php
						echo '<li><b>If you need to add notes to your time, you can do so via the <a href="/my-time/">My Time</a> page</b></li>';
						echo '<li>&nbsp;</li>';
						echo "<li><b>If you don't see a project in your matrix, please check the current project listing and add yourself if it's available.
								Click <a href='/add-to-projects/' target='_blank' >Add Yourself to a Project</a> to view current project listings.  Once you add the project in the new window, click \"Save\" here to refresh.</b></li>";
						echo '<li>&nbsp;</li>';
						echo '<li><b>If you need to add an overhead code, go to <a href="/new-timesheet/" target="_blank">New Timesheet</a> and enter the line item there.
								Any code with existing hours will show here for the duration of the time period.  Once entered on the New Timesheet, click "Save" here to refresh.</b></li>';
						echo '<li>&nbsp;</li>';
						echo '<li><b>If you enter new hours or adjust hours to a time off code (Vacation, Sick, etc.) a notice will automatically be sent to your 
							manager - no need to submit a separate request for time off.  Likewise, deleting (or zeroing) hours for a time off code will delete
							the associated request.</b></li>,';
						?>
						</ul>
					
				</div>
			</div></div>
		
<?php 
}
add_shortcode('new_timesheet_matrix','billyB_new_timesheet_matrix')
?>