<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$name = $current_user->display_name;
	if(isset($_POST['set_id'])){$uid = $_POST['change_id'];}
 	$timesheet = $_GET['ID'];
	$now = time();
	$days_back = 60;
	$deadline = $now - (86400 * $days_back);
	$report_to_result = $wpdb->get_results($wpdb->prepare("select user_email from ".$wpdb->prefix."users inner join ".$wpdb->prefix."useradd on 
		".$wpdb->prefix."useradd.reports_to=".$wpdb->prefix."users.ID where user_id=%d",$uid));
	$report_to = $report_to_result[0]->user_email;
	
	function sitemile_filter_ttl($title){return ("Edit Timesheet");}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	get_header();
	
	if($deadline > $timesheet)
	{
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">	
		<div id="content">
		<div class="my_box3">
			<div class="padd10">
			Sorry, but this time period is now closed.  If you feel you still need to make edits, please contact Bill Bannister.  Thank you.
			</div>
		</div>
		</div>
			</div>
		</div>
		<?php
		 get_footer();exit;
	}
	?>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
	<?php
	if(isset($_POST['save-info']))
	{
		?>
		<div id="content">
		<div class="my_box3">
			<div class="padd10">
			<?php
					
			$records = ($_POST['record']);
			$timeoff_array = array('Vacation','Sick','Float','Bereav','Jury','Mat/Pat');
			$status = 0; if($uid==37 or $uid==38){$status = 1;}
					
			foreach($records as $record)
			{
				$original_project = $record['original'];
				$original_hours = $record['orig_hours'];
				$timesheet_id = $record['id'];
				$submitted = time();
				$project = $record['project'];
				if(empty($project)){$project = $original_project;}
				$hours = $record['hours'];
				$notes = $record['notes'];
				
				if(in_array($original_project,$timeoff_array) or in_array($project,$timeoff_array))
				{
					$time_off_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where employee_id=%d and request_type=%s and request_date=%d 
						and request_hours=%f",$uid,$original_project,$timesheet,$original_hours));
						
					if(!empty($time_off_results)){$timeoff_duplicate="yes";}else{$timeoff_duplicate="no";}
				}
				if($hours == 0)//delete timeoff request, or back out, then delete timesheet
				{
					if($timeoff_duplicate=="yes")
					{
						if($time_off_results[0]->request_status<=2)
						{
							$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."request_timeoff where request_id=%d",$time_off_results[0]->request_id));
						}
						else
						{
							$negative_hours = $original_hours*-1;
							$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff (request_report_id,employee_id,request_date,request_type,request_code,request_hours,
								request_status,date_requested,origination) values(%d,%d,%d,%s,%s,%f,1,%d,%s)",$time_off_results[0]->request_report_id,$time_off_results[0]->employee_id,
								$time_off_results[0]->request_date,$time_off_results[0]->request_type,$time_off_results[0]->request_code,$negative_hours,$submitted,'Edit Timesheet'));
						}
					}
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."timesheets where timesheet_id=%d",$timesheet_id));
				}
				else//if hours != 0
				{
					//do this for if the original project was timeoff - editing a time off request
					if(in_array($original_project,$timeoff_array) and ($original_project!=$project or $original_hours!=$hours))//if the orignal project was a time-off request and the project or hours changed
					{
						if($time_off_results[0]->request_status<=2)//simply update the request if it's not been uploaded to ADP yet
						{
							$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_type=%s,request_hours=%f
								where request_id=%d",$project,$hours,$time_off_results[0]->request_id)); 
						}
						else
						{
							//enter a reversal of the original entry
							$reverse_hours = $original_hours * -1;
							
							$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff (request_report_id,employee_id,request_date,request_type,request_code,request_hours,
								request_status,date_requested,origination) values(%d,%d,%d,%s,%s,%f,1,%d,%s)",$time_off_results[0]->request_report_id,$time_off_results[0]->employee_id,
								$time_off_results[0]->request_date,$original_project,$time_off_results[0]->request_code,$reverse_hours,$submitted,'Edit Timesheet'));
							
							//enter a new entry if the new entry is another request for timeoff
							if(in_array($project,$timeoff_array))
							{
								if($project == 'Vacation'){$code='V';}elseif($project=='Sick'){$code='S';}elseif($project=='Holiday'){$code='H';}
									elseif($project=='Bereav'){$code='B';}elseif($project=='Float'){$code='F';}elseif($project=='Jury'){$code='J';}else{$code='M';}
									
								$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff ((request_report_id,employee_id,request_date,request_type,request_code,request_hours,
									request_status,date_requested,origination) values(%d,%d,%d,%s,%s,%f,1,%d,%s)",$time_off_results[0]->request_report_id,$time_off_results[0]->employee_id,
									$time_off_results[0]->request_date,$project,$code,$hours,$submitted,'Edit Timesheet'));
							}
						}
					}
					//do this if it's a new timeoff request
					if(in_array($project,$timeoff_array) and !in_array($original_project,$timeoff_array))
					{
						if($project == 'Vacation'){$code='V';}elseif($project=='Sick'){$code='S';}elseif($project=='Holiday'){$code='H';}
							elseif($project=='Bereav'){$code='B';}elseif($project=='Float'){$code='F';}elseif($project=='Jury'){$code='J';}else{$code='M';}
						$report_id = $uid.time();
							
						if($timeoff_duplicate!="yes")
						{
							$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff (request_report_id,employee_id,request_date,request_type,request_code,request_hours,
								request_status,date_requested,origination) values(%d,%d,%d,%s,%s,%f,1,%d,%s)",$report_id,$uid,$timesheet,$project,$code,$hours,$submitted,'Edit Timesheet'));
						
							$subject = $name.' has submitted a time off request for your review';
							$link = 'http://opdash.programmanagers.com/approve-time-off/';
							$message = 'Please go to '.$link.' to review all time off requests pending your review';
							wp_mail($report_to,$subject,$message);
						
						}
					}
					
					if($timesheet_id!="new")
					{
						$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set submitted_date=%d,project_id=%s,timesheet_hours=%f,timesheet_notes=%s,approved_by=0,
							approved_date=0,timesheet_status=%d where timesheet_id=%d",$submitted,$project,$hours,$notes,$status,$timesheet_id));
					}
					elseif($timesheet_id=="new" and $timeoff_duplicate!="yes")
					{
						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."timesheets (user_id,submitted_date,timesheet_date,project_id,timesheet_hours,timesheet_notes,timesheet_status)
							values(%d,%d,%d,%s,%f,%s,%d)",$uid,$submitted,$timesheet,$project,$hours,$notes,$status));
					}
				}
			}
			echo "The timesheet has been saved.<br/><br/>";
			?>
			<a href="<?php bloginfo('siteurl');?>/new-timesheet/"><?php echo "Enter a new Timesheet";?></a><br/><br/>
			<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
			</div>
		</div>
		</div>
		<?php 
		$records = array();
	} ?> 
	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
					<?php
					if($current_user->ID==11)
					{
						echo '<li><select class="do_input_new" name="change_id">';
						$users_results = $wpdb->get_results("select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where ".$wpdb->prefix."useradd.status=1 order by display_name");
						foreach($users_results as $user)
						{
							echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
						}
						echo '</select></li>';
						echo '<li><input type="submit" name="set_id" class="my-buttons" value="Change ID" /></li>';
					}
					?>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
					<li>&nbsp;</li>
					<li>
					<style>input[type=number]{width:70px;}</style>
					<?php 
					$hours = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."timesheets where timesheet_date=%d and user_id=%d",$timesheet,$uid));
					if(!empty($hours))
					{
						$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,client_name,abbreviated_name,project_name,gp_id 
							from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
							left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
							where ".$wpdb->prefix."project_user.user_id=%d and (".$wpdb->prefix."projects.status=2 or ".$wpdb->prefix."projects.status>3) and project_parent=0",$uid));
						
						$admin_results = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where timesheet_available=1 order by other_project_code_name");
						
						for($i=0;$i<=count($hours);$i++)
						{
							$date = date('m-d',$hours[0]->timesheet_date);
							echo '<div id="'.$i.'" style="display:inline-block;">Date: <select class="do_input_new"><option>'.$date.'</option></select></div>';
							echo '<div id="'.$i.'" style="display:inline-block;">Project: <select class="do_input_new" name="record['.$i.'][project]"
								'.($hours[$i]->timesheet_status > 0 ? "disabled='disabled'" : "" ).' >';
							echo '<option value="">Select Project</option>';
							foreach ($resultactive as $active)
							{
								$abb_name = $active->abbreviated_name;
								if(empty($abb_name))
								{
									$abb_name = $active->gp_id;
									if(empty($abb_name))
									{
										$abb_name = $active->project_name;
										if(empty($abb_name))
										{
											$abb_name = "Unnamed Project: ".$active->client_name;
										}
									}
								}
								
								$p = '<option value="'.$active->ID.'" ';
								if($hours[$i]->project_id == $active->ID){$p .= 'selected="selected" ';}
								$p .= '>'.$abb_name.'</option>';
								echo $p;
							}
							
							foreach($admin_results as $admin)
							{
								echo '<option value="'.$admin->other_project_code_value.'" '.
									($hours[$i]->project_id == $admin->other_project_code_value ? "selected='selected'" : "").'
									>'.$admin->other_project_code_name.'</option>';
							}
							
							$othercodes = array('Vacation','Sick','Holiday','Bereav','Float','Jury','Mat/Pat');
							foreach($othercodes as $code)
							{
								echo '<option ';
								if($hours[$i]->project_id == $code){echo 'selected="selected"';}
								echo ' >'.$code.'</option>';
							}
							echo '</select></div>';
							echo '<input type="hidden" name="record['.$i.'][id]" value="'.(empty($hours[$i]->timesheet_id) ? 'new' : $hours[$i]->timesheet_id).'" />';
							echo '<input type="hidden" name="record['.$i.'][original]" value="'.(empty($hours[$i]->project_id) ? 'none' : $hours[$i]->project_id).'" />';
							echo '<input type="hidden" name="record['.$i.'][orig_hours]" value="'.(empty($hours[$i]->timesheet_hours) ? '0' : $hours[$i]->timesheet_hours).'" />';
							echo '<div id="'.$i.'" style="display:inline-block;"> Hours: <input type="number" step=".25" min="0" max="24" class="do_input_new" 
								value ="'.(empty($hours[$i]->timesheet_hours)? 0 : floatval($hours[$i]->timesheet_hours)).'"
								name="record['.$i.'][hours]" '.($hours[$i]->timesheet_status > 1 ? "readonly" : "" ).' /></div>';
							echo '<div id="'.$i.'" style="display:inline-block;"> Notes: <textarea rows="1" cols="60" class="full_wdth_me do_input_new description_edit"
								name="record['.$i.'][notes]">'.$hours[$i]->timesheet_notes.'</textarea></div></li><br/><hr><br/>';
						}
					}
					else{echo '<li>There is no data for this timesheet.</li>';}
					?>
					</li>
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></li>
					</ul>
				</div>
			</div>
		</div>
	</form>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Tips";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<li><?php echo 'To delete a line item, enter 0 (zero) hours and click "SAVE"';?></li>
				<li>You cannot edit a timeoff request if it has been uploaded to ADP.  Please enter a <a href="<?php echo get_bloginfo('siteurl')."/request-time-off/";?>" >
					new request</a> for timeoff if you need to edit an existing entry.</li>
				</ul>
			</li>
		</ul>
	</div></div>
		</div>
	</div>
<?php get_footer(); ?>