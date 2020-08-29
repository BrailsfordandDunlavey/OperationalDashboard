<?php
function billyb_users_with_edited_timeoff()
{ 
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11,60,66,104,177);//Bill B, Jessica Kelley, Cho Smith, Caitlin Sanchez
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	$selected_users = array();
	
	if(isset($_POST['save-info']))
	{
		
	}
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						echo '<li><table width="100%">';
						$year = strtotime(date('Y-01-01',time()));
						echo '<tr><th>Employee</th><th>Date</th><th>Timesheet Type</th><th>Timesheet Hours</th><th>Request Type</th><th>Request Hours</th></tr>';
						$vacation_query = $wpdb->get_results($wpdb->prepare("select timesheet_date,timesheet_hours,project_id,user_id,display_name from ".$wpdb->prefix."timesheets
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
							where project_id in ('Vacation','Sick','Float') and timesheet_date>=%d
							order by user_id,timesheet_date",$year));
						foreach($vacation_query as $v)
						{
							$date = $v->timesheet_date;
							$t_hours = $v->timesheet_hours;
							$project = $v->project_id;
							$user = $v->user_id;
							
							$timeoff_query = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff
								where employee_id=%d and request_date=%d",$user,$date));
							$r_hours = $timeoff_query[0]->request_hours;
							
							if($t_hours != $r_hours)
							{
								$font = '<font color="red">';
								echo '<tr>
									<td>'.$v->display_name.' ('.$v->user_id.')</td>
									<td>'.date('m-d-Y',$date).' ('.$date.')</td>
									<td>'.$project.'</td><td>'.$t_hours.'</td>
									<td>'.$timeoff_query[0]->request_type.'</td>
									<td>'.$font.$r_hours.'</font></td>
									</tr>';
							}
							else{$font = '';}
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
<?php  
}
add_shortcode('users_with_edited_timeoff','billyb_users_with_edited_timeoff') ?>