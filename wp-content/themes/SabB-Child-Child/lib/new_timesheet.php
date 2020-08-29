<?php
function billyB_new_timesheet()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$name = $current_user->display_name;
	
	if(isset($_POST['change-user']));
	{
		$orig_uid = $_POST['user_id'];
		if($orig_uid == 37){$uid = 85;}
		if($orig_uid == 85){$uid = 37;}
		if($orig_uid == 38){$uid = 41;}
		if($orig_uid == 41){$uid = 38;}
		$timeperiod1 = trim($_POST['time_period']);
	}
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		if(!empty($_POST['user_id'])){$uid = $_POST['user_id'];}
		$records = ($_POST['record']);
		$timeperiod1 = trim($_POST['time_period']);
		$status = 0; if($uid==37 or $uid==38){$status = 1;}
		
		foreach($records as $record)
		{
			if(!empty($record['hours']))
			{
				$submitted = time();	
				$date = $record['date'];
				if(empty($record['task_project'])){$project = $record['project'];}else{$project = $record['task_project'];}
				if(empty($record['task'])){$task = 0;}else{$task = $record['task'];}
				$hours = $record['hours'];
				$notes = $record['notes'];
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."timesheets 
					(user_id,submitted_date,timesheet_date,project_id,task_id,timesheet_hours,timesheet_notes,timesheet_status,origination)
					values(%d,%d,%d,%s,%d,%f,%s,%d,'New Timesheet')",$uid,$submitted,$date,$project,$task,$hours,$notes,$status));
			
				$timeoff_array = array('Vacation','Sick','Float','Bereav','Jury','Mat/Pat');//BillyB excluded Holiday from the timeoff_array
				if(in_array($project,$timeoff_array))
				{
					$report_id = $uid.$submitted;
					if($project == 'Vacation'){$code='V';}elseif($project == 'Sick'){$code='S';}elseif($project == 'Holiday'){$code='H';}
						elseif($project == 'Float'){$code='F';}elseif($project == 'Bereav'){$code='G';}elseif($project == 'Jury'){$code='J';}
						else{$code='M';}
					
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff 
						(request_report_id,employee_id,request_date,request_type,request_code,request_hours,request_status,date_requested,origination)
						values (%d,%d,%d,%s,%s,%f,%d,%d,'New Timesheet')",$report_id,$uid,$date,$project,$code,$hours,$status,$submitted));
					
					$report_to_email_results = $wpdb->get_results($wpdb->prepare("select user_email from ".$wpdb->prefix."users 
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."useradd.reports_to=".$wpdb->prefix."users.ID
						where ".$wpdb->prefix."useradd.user_id=%d",$uid));
					$report_to = $report_to_email_results[0]->user_email;
					$subject = $name.' has submitted a time off request for your review';
					$link = get_bloginfo('siteurl').'/approve-time-off/';
					$message = 'Please go to '.$link.' to review all time off requests pending your review';
					wp_mail($report_to,$subject,$message);
				}
				$entered = 'yes';
			}
		}
		if($entered == 'yes'){
			echo "The timesheet has been saved.  You can see the recorded hours in the lower right side bar.<br/><br/>";
		}
		else{
			echo "No hours were input, so nothing was saved.<br/><br/>";
		}
		?>
		</div></div></div>

		<?php
		wp_redirect(get_bloginfo('siteurl')."/new-timesheet/"); exit;
	}	
	?>
	<script type="text/javascript">
		var form_being_submitted = false;
		var myForm = document.forms.new_timesheet;
		function checkForm(){
			var saveInfo = myForm.elements['save-info'];
			var saveInfoTwo = myForm.elements['save-info-two'];
			
			if(form_being_submitted){
				alert('The form is being submitted, please wait a moment...');
				saveInfo.disabled = true;
				saveInfoTwo.disabled = true;
				return false;
			}
			saveInfo.value = 'Saving form...';
			saveInfoTwo.value = 'Saving form...';
			form_being_submitted = true;
			return true;
		}
	</script>
		<form method="post"  enctype="multipart/form-data" name="new_timesheet" onsubmit="checkForm();">
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<li><h3><?php echo "Time Period Start:";?></h3>
				<p><select class="do_input_new" name="time_period">
				<?php
					$periodsresult = $wpdb->get_results("select * from ".$wpdb->prefix."time_periods where active ='on' order by start_date desc");
					if(isset($_POST['set-time-period']))
					{
						$timeperiod1 = trim($_POST['time_period']);
						//BillyB add code to allow user change to stay put for Kesha and Bev changing periods
					}
					$now = time();
					foreach($periodsresult as $period)
					{
						if(empty($timeperiod1) and strtotime($period->start_date) < $now and strtotime($period->end_date)+86399 > $now){$timeperiod1=$period->time_period_id;}
						echo '<option ';
						if($period->time_period_id==$timeperiod1){echo "selected='selected'";}
						echo 'value="'.$period->time_period_id.'">'.date('m-d-Y',strtotime($period->start_date)).'</option>';
					}
					?>
					</select>
					<input type="submit" name="set-time-period" class="my-buttons" value="<?php echo "Update Period"; ?>" /></p>
				</div>
				</div>						
			</div>
			<?php
			if($current_user->ID == 85 or $current_user->ID == 41)
			{
				?>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Change User";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						if($uid == 85)
						{
							echo "<li>You are entering time as Beverly Cobb</li>";
							echo '<input type="hidden" name="user_id" value="'.$uid.'" />';
							echo '<li><input type="submit" value="Change to Paul" name="change-user" class="my-buttons" /></li>';
						}
						if($current_user->ID==85 and $uid == 37)
						{
							echo "<li>You are entering time as Paul Brailsford</li>";
							echo '<input type="hidden" name="user_id" value="'.$uid.'" />';
							echo '<li><input type="submit" value="Change to Beverly" name="change-user" class="my-buttons" /></li>';
						}
						if($current_user->ID==41 and $uid == 38)
						{
							echo "<li>You are entering time as Chris Dunlavey</li>";
							echo '<input type="hidden" name="user_id" value="'.$uid.'" />';
							echo '<li><input type="submit" value="Change to Kesha" name="change-user" class="my-buttons" /></li>';
						}
						if($uid == 41)
						{
							echo "<li>You are entering time as Kesha Hall</li>";
							echo '<input type="hidden" name="user_id" value="'.$uid.'" />';
							echo '<li><input type="submit" value="Change to Chris" name="change-user" class="my-buttons" /></li>';
						}
						?>
						</ul>
					</li>
				</ul>
				</div></div>
				<?php 
			}
			?>
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
								
				<ul class="other-dets_m">
					<li><p><input type="submit" name="save-info" class="my-buttons" disabled value="<?php echo "Save"; ?>" /></p></li>
					<li>&nbsp;</li>
					<li><?php echo "Note: If you need more lines, just save and enter a new timesheet";?></li>
					<style>input[type=number]{width:80px;}</style>
					
					<?php 
					$periodresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."time_periods where time_period_id=%d",$timeperiod1));
					
					$start = strtotime($periodresults[0]->start_date);
					$end = strtotime($periodresults[0]->end_date);							
					
					$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,client_name,project_name,gp_id 
						from ".$wpdb->prefix."projects 
						inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
						left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
						where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status in (2,4,5,6) and project_parent=0 order by abbreviated_name,gp_id",$uid));
					
					$result_admin = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where timesheet_available=1 order by other_project_code_name");
					
					$rowstart = 19;
					$rowadd = 10;
					
					if(isset($_POST['add_rows'])){$rowstoadd = $rowstoadd + 1;}
					$totalrows = $rowstart + ($rowadd * $rowstoadd);
					for ($t= 0;$t<=$totalrows;$t++)
					{
						echo '<li><div id="d3" style="display:inline-block;f="><select class="do_input_new" name="record['.$t.'][date]" onChange="dateClear('.$t.')">';
						echo '<option value="">Date</option>';
						for ($i = $start; $i <= $end; $i = $i + 86400)
						{
							echo '<option value="'.$i.'">'.date( 'D m-d', $i).'</option>';
						}
						echo '</select></div>';
						echo '<div id="d3" style="display:inline-block;">
							<select class="do_input_new" name="record['.$t.'][project]" onChange="projectSelect('.$t.');" >';
						echo '<option value="">Project</option>';
						foreach ($resultactive as $active)
						{
							$active_id = $active->ID;
							//Check for sub projects
							$task_results = $wpdb->get_results($wpdb->prepare("select task_id,task_name,task_description from ".$wpdb->prefix."tasks
								where project_id=%s and time_entry=1 and task_status=0 order by task_start",$active_id));
							$abb_name = $active->abbreviated_name;
							if(empty($abb_name))
							{	
								$abb_name = $active->project_name;
								if(empty($abb_name))
								{	
									$abb_name = $active->gp_id;
									if(empty($abb_name))
									{
										$abb_name = "Unnamed Project:  ".$active->client_name;
									}
								}
							
							}
							if(empty($task_results))
							{
								echo '<option value="'.$active->ID.'">'.$abb_name.'</option>';
							}
							else
							{
								foreach($task_results as $task)
								{
									$task_id = $task->task_id;
									echo '<option value="project '.$active->ID.' task '.$task_id.'">'.$abb_name.': '.$task->task_name.'</option>';
								}
							}
						}
						foreach ($result_admin as $admin)
						{echo '<option value="'.$admin->other_project_code_value.'">'.$admin->other_project_code_name.'</option>';}
						$othercodes = array('Vacation','Sick','Holiday','Bereav','Float','Jury','Mat/Pat');
						foreach($othercodes as $code){echo '<option>'.$code.'</option>';}
						
						echo '</select></div>';
						echo '<input type="hidden" name="record['.$t.'][task]" value="" />';
						echo '<input type="hidden" name="record['.$t.'][task_project]" value="" />';
						echo '<div id="d3" style="display:inline-block;"><input type="number" step=".25" min="-24" max="24" placeholder="Hours"
							class="do_input_new" name="record['.$t.'][hours]" onblur="checkProject('.$t.');"/></div>';
						echo '<div id="d3" style="display:inline-block;"><textarea rows="1" cols="60" class="full_wdth_me do_input_new description_edit" placeholder="Notes"
							size="20" name="record['.$t.'][notes]"></textarea></div></li><li>&nbsp;</li>';
					}?>
					<li><p><input type="submit" name="save-info-two" class="my-buttons" disabled value="<?php echo "Save"; ?>" /></p></li>
				<script type="text/javascript">
					var myForm = document.forms.new_timesheet;
					function projectSelect(x){
						taskUpdate(x);
						projectClear(x);
					}
					function taskUpdate(x){
						var taskField = myForm.elements['record[' + x + '][task]'];
						var taskProject = myForm.elements['record[' + x + '][task_project]'];
						var projectField = myForm.elements['record[' + x + '][project]'];
						var value = projectField.value;
						var string = value.split(" ");
						
						if(string[0] == "project"){
							taskProject.value = string[1];
							taskField.value = string[3];
						}
						else{
							taskProject.value = '';
							taskField.value = '';
						}
					}
					function checkProject(x){
						var myForm = document.forms.new_timesheet;
						var saveInfo = myForm.elements['save-info'];
						var saveInfoTwo = myForm.elements['save-info-two'];
						var hoursField = myForm.elements['record[' + x + '][hours]'];
						var projectField = myForm.elements['record[' + x + '][project]'];
						var dateField = myForm.elements['record[' + x + '][date]'];
						if((projectField.value == '' || dateField.value == '') && hoursField.value != 0){
							if(projectField.value == '' && dateField.value != ''){	
								alert('Please select a Project');
								projectField.focus();
								projectField.style.backgroundColor = "red";
								dateField.style.backgroundColor = "initial";
								saveInfo.disabled = true;
								saveInfoTwo.disabled = true;
							}
							else if(projectField.value == '' && dateField.value == ''){
								alert('Please select a Project and a Date');
								dateField.focus();
								dateField.style.backgroundColor = "red";
								projectField.style.backgroundColor = "red";
								saveInfo.disabled = true;
								saveInfoTwo.disabled = true;
							}
							else{
								alert('Please select a Date');
								dateField.focus();
								dateField.style.backgroundColor = "red";
								projectField.style.backgroundColor = "initial";
								saveInfo.disabled = true;
								saveInfoTwo.disabled = true;
							}	
						}
						if((projectField.value != '' && dateField.value != '') || hoursField.value == 0){
							projectField.style.backgroundColor = "initial";
							dateField.style.backgroundColor = "initial";
							saveInfo.disabled = false;
							saveInfoTwo.disabled = false;
						}
					}
					function projectClear(x){
						var myForm = document.forms.new_timesheet;
						var saveInfo = myForm.elements['save-info'];
						var saveInfoTwo = myForm.elements['save-info-two'];
						var projectField = myForm.elements['record[' + x + '][project]'];
						var hoursField = myForm.elements['record[' + x + '][hours]'];
						var dateField = myForm.elements['record[' + x + '][date]'];
						if(projectField.value != ''){
							projectField.style.backgroundColor = "initial";
							if(dateField.value != '' || hoursField.value==0){
								saveInfo.disabled = false;
								saveInfoTwo.disabled = false;
							}
						}
						else{
							if(hoursField.value >0){
								projectField.style.backgroundColor = "red";
								saveInfo.disabled = true;
								saveInfoTwo.disabled = true;
							}
						}
					}
					function dateClear(x){
						var myForm = document.forms.new_timesheet;
						var saveInfo = myForm.elements['save-info'];
						var saveInfoTwo = myForm.elements['save-info-two'];
						var dateField = myForm.elements['record[' + x + '][date]'];
						var projectField = myForm.elements['record[' + x + '][project]'];
						var hoursField = myForm.elements['record[' + x + '][hours]'];
						if(dateField.value != ''){
							dateField.style.backgroundColor = "initial";
							if(projectField.value != '' || hoursField.value == 0){
								saveInfo.disabled = false;
								saveInfoTwo.disabled = false;
							}
						}
						else{
							if(hoursField.value >0){
								dateField.style.backgroundColor = "red";
								saveInfo.disabled = true;
								saveInfoTwo.disabled = true;
							}
						}
					}
				</script>	
				</ul>
				</div>
				</div>
			</div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Previously Saved Time (by day)";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
							$timesheetdailyresult = $wpdb->get_results($wpdb->prepare("select timesheet_date,sum(timesheet_hours) as sum 
								from ".$wpdb->prefix."timesheets 
								where user_id=%d and timesheet_date<=%d and timesheet_date>=%d group by timesheet_date order by timesheet_date",$uid,$end,$start));
							if(empty($timesheetdailyresult)){echo "No Timesheet data yet";}
							$total =0;
							foreach ($timesheetdailyresult as $dataone)
							{
								$timesheetdate = $dataone->timesheet_date;
								$total += $dataone->sum;
								echo '<li>'.date('D m-d',$timesheetdate).' - '.$dataone->sum.' hours - <a href="/?p_action=edit_timesheet&ID='.$timesheetdate.'" class="nice_link">Edit</a></li>';
							}
							echo '<li>Total for the Period: '.$total.' hours</li>';
						?>
						</ul>
					</li>
				</ul>
			</div></div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Tips";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						echo "<li>If you don't see a project in your drop down, please check the current project listing and add yourself if it's available.
								Click <a href='/add-to-projects/' target='_blank' >Add Yourself to a Project</a> to view current project listings.</li>";
						echo '<li>To edit a time entry, look below in the Previously Saved Time (by day) section and click "edit" next to the entry you want to edit.</li>';
						?>
						</ul>
					</li>
				</ul>
			</div></div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Previously Saved Time (details)";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
							$timesheetresult = $wpdb->get_results($wpdb->prepare("select project_id,timesheet_date,timesheet_hours,gp_id from ".$wpdb->prefix."timesheets
								left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
								where user_id=%d and timesheet_date<=%d and timesheet_date>=%d order by timesheet_date",$uid,$end,$start));
							if(empty($timesheetresult)){echo "No Timesheet data yet";}
							foreach ($timesheetresult as $data)
							{
								echo '<li>'.date('m-d',$data->timesheet_date).' - '.$data->timesheet_hours;
								if(empty($data->gp_id)){$gp_id = $data->project_id;}
								else{$gp_id = $data->gp_id;}
								echo ' - '.$gp_id.'</li>';
							}
						?>
						</ul>
					</li>
				</ul>
			</div></div>
		</form>
<?php 
}
add_shortcode('new_timesheet','billyB_new_timesheet')
?>