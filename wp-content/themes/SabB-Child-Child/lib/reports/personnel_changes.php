<?php
function billyB_personnel_changes()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_rights_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	//if($user_rights_results[0]->team != 'Finance' or $user_rights_results[0]->team != 'Human Resources'){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
 
	if(isset($_POST['save-info']))
	{
		

	
	}
	else
	{	
		?>
		<form method="post"  enctype="multipart/form-data">
			<div id="content-full"><h3><?php echo "Personnel Changes";?></h3><br/>
				<div class="my_box3">
				<div class="padd10">
								
				<ul class="other-dets_m">
				<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
				<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
				<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
				<?php
				$users_query = "select ".$wpdb->prefix."users.ID,display_name,sphere,team,position,reports_to from ".$wpdb->prefix."users
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
					where ".$wpdb->prefix."useradd.status=1 and end_date=0 order by display_name";
				$users_results = $wpdb->get_results($users_query);
				
				$positions_query = "select ID,position_title from ".$wpdb->prefix."position";
				$positions_results = $wpdb->get_results($positions_query);
				
				$sphere_array = array();
				$team_array = array();
				$report_to = array();
				foreach($users_results as $result)
				{
					if(!in_array($result->sphere,$sphere_array)){array_push($sphere_array,$result->sphere);}
					if(!in_array($result->team,$team_array)){array_push($team_array,$result->team);}
					if(!in_array($result->reports_to,$report_to)){$report_to[$result->ID]=$result->display_name;}
				}
				
				echo '<li><table width="100%"><tr><th><b><u>Name</u></b></th><th><b><u>Sphere</u></b></th><th><b><u>Team</u></b></th>
					<th><b><u>Reports To</u></b></th></tr><tr><th>&nbsp;</th><th><b><u>Position</u></b></th><th><b><u>Effective Date</u></b></th></tr>';
				
				foreach($users_results as $user)
				{
					$user_sphere = $user->sphere;
					$supervisor = $user->reports_to;
					echo '<th>'.$user->display_name.'</th>';
					echo '<th><select class="do_input_new" name="record['.$user->ID.'][sphere]">';
					foreach($sphere_array as $sphere)
					{
						echo '<option '.($sphere==$user_sphere ? "selected='selected'" : "" ).'>'.$sphere.'</option>';
					}
					echo '</select></th>';
					
					echo '<th><select name="record['.$user->ID.'][team]" class="do_input_new"';
					foreach($team_array as $team)
					{
						echo '<option '.($team == $user->team ? "selected='selected'" : "" ).'>'.$team.'</option>';
					}
					echo '</select></th>';
					echo '<th><select class="do_input_new" name="record['.$user->ID.'][report]">';
					foreach($report_to as $key => $value)
					{
						echo '<option value="'.$key.'"';
						if($key == $supervisor){echo 'selected="selected"';}
						echo '>'.$value.'</option>';
						
					}
					echo '<option value="" '.($supervisor == 0 ? 'selected="selected"' : "" ).'>None Set</option>';
					echo '</select></th>';
					echo '<input type="hidden" name="record['.$user->ID.'][orig_sphere]" value="'.$user_sphere.'" />';
					echo '<input type="hidden" name="record['.$user->ID.'][orig_team]" value="'.$user->team.'" />';
					echo '<input type="hidden" name="record['.$user->ID.'][orig_report]" value="'.$supervisor.'" />';
					echo '<input type="hidden" name="record['.$user->ID.'][orig_position]" value="'.$user->position.'" />';
					echo '</tr>';
					
					echo '<tr><th>&nbsp;</th>';

					echo '<th><select class="do_input_new" name="record['.$user->ID.'][position]">';
					foreach($positions_results as $position)
					{
						echo '<option value="'.$position->ID.'" '.($position->ID == $user->position ? "selected='selected'" : "" ).'>'.$position->position_title.'</option>';
					}
					echo '</select></th>';
					echo '<th><input type="text" name="record['.$user->ID.'][date]" id="end'.$user->ID.'" class="do_input_new" /></th></tr>';
				
					?>
					<script>
					<?php $dd = 180; ?>
					var myDate=new Date();
					myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
					
					$(document).ready(function() {
						$('#end<?php echo $user->ID;?>').datetimepicker({
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
							timezoneText: 'Time Zone','ProjectTheme'); ?>'
						});
					});
					</script>
					<?php
				}
				echo '</table></li>';
				echo '<li><input type="submit" name="save-info" value="SAVE" class="my-buttons" /></li>';
				echo '<li>&nbsp;</li>';
				?>
				</ul>
				<ul class="other-dets_m">
				<li>&nbsp;</li>
				<li><p><input type="submit" name="save-info" class="my-buttons" value="Submit Employee" /></p></li>
				</ul>
				</div>
				</div>
			</div>
		</form>
		<?php 
	}
}
add_shortcode('personnel_changes','billyB_personnel_changes') ?>