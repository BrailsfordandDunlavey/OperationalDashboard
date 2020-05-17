<?php
function billyB_time_reports()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select sphere,team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $rights_results[0]->sphere;
	$team = $rights_results[0]->team;
	$current_month = date('Y-m-01');
	$start_month = strtotime(date("Y-m-d", strtotime($current_month)) . " -1 month");
	$end_month = strtotime(date("Y-m-d", strtotime($current_month)) . " -1 day");
	
	$allowed_teams = array('Finance');
 
	if(!in_array($team,$allowed_teams))
	{wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	?>   
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php
			$staff_query = $wpdb->prepare("select ".$wpdb->prefix."timesheets.display_name,user_id,project_id,timesheet_hours from ".$wpdb->prefix."timesheets inner join ".$wpdb->prefix."useradd 
				on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id inner join ".$wpdb->prefix."users 
				on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
				where timesheet_date >=%d and timesheet_date<=%d and sphere=%s",$start_month,$end_month,$sphere);
			$staff_results = $wpdb->get_results($staff_query);
			
			$overhead_array = array("0001","0001AC","0001AD2012","0001HR","0001IT");
			$general_marketing_array = array("0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
				"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O");
			$marketing_conf_array = array("0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
				"0006CONF","0007CONF","0008CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF");
			$marketing_intv_array = array("0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
				"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV");
			$time_off_array = array('Vacation','Holiday','Sick','Float','Bereav','Jury','Mat/Pat');
			$overhead_hours = 0;
			$general_marketing_hours = 0;
			$marketing_conf_hours = 0;
			$marketing_intv_hours = 0;
			$time_off_hours = 0;
			$centers_support_hours = 0;
			$billable_hours = 0;
			
			echo '<li><table width="100%">
				<tr>
				<th>Employee</th>
				<th>Admin Hours</th>
				<th>Marketing Hours</th>
				<th>Billable Hours</th>
				</tr>';
			foreach($staff_results as $time)
			{
				if(in_array($time->project_id,$overhead_array)){$project = "Overhead"; $overhead_hours += $time->timesheet_hours;}
				elseif(in_array($time->project_id,$general_marketing_array)){$project = "General Marketing";}
				elseif(in_array($time->project_id,$marketing_conf_array)){$project = "Marketing Conferences";}
				elseif(in_array($time->project_id,$marketing_intv_array)){$project = "Marketing Interviews";}
				elseif(in_array($time->project_id,$time_off_array)){$project = "Time Off";}
				elseif($time->project_id == "8000"){$project = "Centers";}
				else{$project = "Billable";}
			}
			echo '</table></li>';
			?>
			</ul>
			</div>
			</div>						
		</div>		
	<?php
}
add_shortcode('time_reports','billyB_time_reports')
?>