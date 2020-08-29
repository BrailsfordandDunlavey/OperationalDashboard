<?php
function billyb_any_hours_details()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11,60,66,104,177,103,221);//Bill B, Jessica Kelley, Cho Smith, Caitlin Sanchez, Laura Cosenzo, Brittany Martin
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	$selected_users = array();
	
	if(isset($_POST['save-info']))
	{
		$users = $_POST['users'];
		$start = strtotime($_POST['start']);
		$end = strtotime($_POST['end']);
		
		foreach($users as $user)
		{
			array_push($selected_users,$user);
		}
	}
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
					<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
					<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
					<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
					<link rel="stylesheet" href="/resources/demos/style.css">
					<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						if($uid!=103)
						{
							$query = $wpdb->prepare("select ID,display_name from ".$wpdb->prefix."users 
								where ID not in (1,2,29) order by display_name");
						}
						if($uid==103)
						{
							$query = $wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."users
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
								where sphere in ('Sphere KMV','Higher Ed') order by display_name");
						}
						$all_users = $wpdb->get_results($query);
						if(empty($start)){$start="";}
						if(empty($end)){$end="";}
						echo '<li><h3>Select Employees:<br/>(Hold Ctrl to select multiple employees)</h3>
							<select name="users[]" multiple="multiple" size="20">';
						foreach($all_users as $u)
						{
							echo '<option value="'.($uid==103 ? $u->user_id : $u->ID).'" '.(in_array(($uid==103 ? $u->user_id : $u->ID),$selected_users) ? 'selected="selected"' : '').'>'.$u->display_name.'</option>';
						}
						echo '</select></li>';
						echo '<li><h3>Begin Date</h3><p><input type="text" id="start" class="do_input_new" value="'.date('m/d/Y',$start).'" name="start" /></p></li>';
						echo '<li><h3>End Date</h3><p><input type="text" id="end" class="do_input_new" value="'.date('m/d/Y',$end).'" name="end" /></p></li>';
						?>
						<script>
						<?php $dd = 180; ?>

						var myDate=new Date();
						myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
	
						$(document).ready(function() {
							$('#start').datepicker({
								showSecond: false,
								timeFormat: 'hh:mm:ss',
								currentText: '<?php _e('Now','ProjectTheme'); ?>',
								closeText: '<?php _e('Done','ProjectTheme'); ?>',
								ampm: false,
								dateFormat: 'mm/dd/yy',
								timeFormat: 'hh:mm tt',
								timeSuffix: '',
								maxDateTime: myDate,
								timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
								timeText: '<?php _e('Time','ProjectTheme'); ?>',
								hourText: '<?php _e('Hour','ProjectTheme'); ?>',
								minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
								secondText: '<?php _e('Second','ProjectTheme'); ?>',
								timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
							});
						});
						$(document).ready(function() {
							$('#end').datepicker({
								showSecond: false,
								timeFormat: 'hh:mm:ss',
								currentText: '<?php _e('Now','ProjectTheme'); ?>',
								closeText: '<?php _e('Done','ProjectTheme'); ?>',
								ampm: false,
								dateFormat: 'mm/dd/yy',
								timeFormat: 'hh:mm tt',
								timeSuffix: '',
								maxDateTime: myDate,
								timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
								timeText: '<?php _e('Time','ProjectTheme'); ?>',
								hourText: '<?php _e('Hour','ProjectTheme'); ?>',
								minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
								secondText: '<?php _e('Second','ProjectTheme'); ?>',
								timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
							});
						});
						</script>
						<?php
						echo '<li>&nbsp;</li>
							<li><h3>&nbsp;</h3><p><input type="submit" name="save-info" value="SAVE" class="my-buttons-submit" /></p></li>';
						?>
						</ul>
						</div>
						</div>
					</div>
				<?php
				if(!empty($selected_users))
				{
					?>
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						foreach($selected_users as $u)
						{
							$user_details = $wpdb->get_results($wpdb->prepare("select display_name,timesheet_date,project_id,timesheet_hours,timesheet_status,
								timesheet_notes,gp_id,project_name,abbreviated_name
								from ".$wpdb->prefix."users
								inner join ".$wpdb->prefix."timesheets on ".$wpdb->prefix."users.ID=".$wpdb->prefix."timesheets.user_id
								left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
								where timesheet_date>=%d and timesheet_date<=%d and user_id=%d order by timesheet_date",$start,$end,$u));
							echo '<li><h3>'.$user_details[0]->display_name.'</h3></li>';
							echo '<li><table width="100%">
								<tr>
								<th><b><u>Date</u></b></th>
								<th><b><u>Project</u></b></th>
								<th><u><b>Hours</u></b></th>
								<th><u><b>Notes</u></b></th>
								<th><u><b>Status</u></b></th>
								</tr>';
							$sum=0;
							$project_array = array();
							$projects = array();
							$project_hours_array = array();
							foreach($user_details as $ud)
							{
								if(!empty($ud->abbreviated_name)){$project = $ud->abbreviated_name;}
								elseif(!empty($ud->project_name)){$project = $ud->project_name;}
								elseif(!empty($ud->gp_id)){$project = $ud->gp_id;}
								else{$project = $ud->project_id;}
								echo '<tr id="p'.$ud->project_id.'">
									<td>'.date('m-d-Y',$ud->timesheet_date).'</td>
									<td>'.$project.'</td>
									<td>'.number_format($ud->timesheet_hours,2).'</td>
									<td>'.$ud->timesheet_notes.'</td>
									<td>'.($ud->timesheet_status>0 ? "Approved" : "Unapproved").'</td>
									</tr>';
								$sum += $ud->timesheet_hours;
								if(!in_array($project,$project_array))
								{
									$record = array($ud->project_id,$project);
									array_push($projects,$record);
									array_push($project_array,$project);
									array_push($project_hours_array,array($project,$ud->timesheet_hours));
								}
								else
								{
									for($i=0;$i<count($project_hours_array);$i++)
									{
										if($project_hours_array[$i][0] == $project)
										{
											$project_hours_array[$i][1] = $project_hours_array[$i][1] + $ud->timesheet_hours;
										}
									}
								}
							}
							echo '<tr><td>&nbsp;</td></tr>';
							echo '<tr><td>&nbsp;</td><td><b>Total</b></td><td><b>'.$sum.'</b></td></tr>';
							echo '<tr><td>&nbsp;</td></tr>';
							foreach($project_hours_array as $pha)
							{
								echo '<tr><td>&nbsp;</td><td><b>'.$pha[0].'</b></td><td><b>'.number_format($pha[1],2).'</b></td></tr>';
							}
							echo '</table></li>';
						}
						echo '<li>&nbsp;</li>';
						?>
						<script language="javascript" type="text/javascript">
						function hideRows(){
							var x = document.getElementById('select_group').value;
							var allRows = document.querySelectorAll("[id^='p']");
							if(x != "all"){
								var showRows = document.querySelectorAll("[id*='p" + x + "']");
							}else{
								var showRows = allRows;
							}
							for(i=0;i<allRows.length;i++){
								allRows[i].style.display = 'none';
							}
							for(i=0;i<showRows.length;i++){
								showRows[i].style.display = 'table-row';
							}
						}
						</script>
						</ul>
						</div>
						</div>
					</div>
					<div id="right-sidebar" class="page-sidebar"><div class="padd10">
						<ul class="xoxo">
							<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<?php
							echo '<h3>Filter</h3>';
							echo '<p><select id="select_group" class="do_input_new" onchange="hideRows();">';
							echo '<option value="all">All</option>';
							foreach($projects as $ps)
							{
								echo '<option value="'.$ps[0].'">'.$ps[1].'</option>';
							}
							echo '</select></p>';
							?>
							</ul>
							</li>
						</ul>
					</div></div>
					<?php
				}
				?>
				</form>
			</div>
		</div>	
<?php  
}
add_shortcode('any_hours_details','billyB_any_hours_details') ?>