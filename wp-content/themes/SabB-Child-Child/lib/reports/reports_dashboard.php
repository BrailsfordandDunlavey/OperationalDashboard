<?php
function billyB_reports_dashboard()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere,team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	$team = $sphereresult[0]->team;
	$display_name = $current_user->display_name;
	$allowed_teams = array('Finance','Human Resources','Administrative','Executive');
	$sphere_leaders = array('Will Mangrum','Brian Hanlon','Bill Mykins','Brad Noyes','Jeff Turner','Laura Cosenzo','Pam Smith','Folakemi Gbadamosi');
	$group_leaders = array('Pete Isaac','Katie Karp','Joe Winters','Matt Bohannon','Ann Drummie');
		
	if($uid == 11)
	{		
		echo '<div id="content">
				<div class="my_box3">
				<div class="padd10"><h3><b>Metrics</b></h3>
					<ul class="other-dets_m">';
									
		echo 'In Development';
		//add revenue this month, YTD, compare to last year same month, last year YTD
		echo '</ul>
				</div>
				</div>						
			</div>';
	}
	?>	
		<div id="content">
			<div class="my_box3">
			<div class="padd10"><h2><b>Reports</b></h2>
			<ul class="other-dets_m">
						
	<?php						
		
		if(in_array($team,$allowed_teams) or in_array($display_name,$sphere_leaders) or in_array($display_name,$group_leaders))
		{
			echo '<li><h3><u>Time Management:</u></h3></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/operational-dashboard-projects/">Actual vs. Scoped Hours by Project</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/operational-dashboard/">Actual vs. Scoped Hours by Person</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/targeted-hours/">Actual vs Capacity Hours</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/projected-targeted-time/">Scoped vs Capacity Hours</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/staff-availability">Staff Availability</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/time-report">Time Report</a></p></li>';
		}
		if($team == 'Human Resources')
		{
			echo '<li><b><a href="any-hours-report/">Any Hours Report</b></a></li>';
			echo '<li><b><a href="time-off-report/">Time Off Report</a></b></li>';
		}
		if($team == 'Business Development' or $team == 'Finance')
		{
			echo '<li><h3><u>Contracts:</u></h3></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/contracts-report/">Contracts</a></p></li>';
			
		}
		if($team == 'Finance')
		{
			echo '<li><h3><u>Accounting:</u></h3></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/projects-with-billable-expenses">Billable Expenses</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/import-cc">Import MasterCards</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="'.get_bloginfo('siteurl').'/unprocessed-expenses/">Unprocessed Expenses</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/time-report">Time Report</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/vendor-payables-management">Vendor Payables Management</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/any-expenses-report">Any Expenses Report</a></p></li>';
		}
		
		if($uid==11)
		{
			echo '<li><h3>&nbsp;</h3><p><a href="/operational-dashboard-revenue/">WUC</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/ar-report/">AR Report</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/set-capacity/">Set Capacity</a></p></li>';
			echo '<li><h3>&nbsp;</h3><p><a href="/employees-report/">Employees Report</a></p></li>';
		}
		
	?>

			</ul>
			</div>
			</div>						
		</div>

<?php } 
add_shortcode('reports_dashboard','billyB_reports_dashboard')
?>