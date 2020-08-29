<?php
function billyB_projects_dashboard()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	?>
	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">	
			<?php						
			$current_month = date('Y-m-01');
			$month2 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -1 month"));
			
			echo '<li><table width="100%"><tr><th><b><u>Project</u></b></th><th><b><u>Actual Last Month</u></b></th>
				<th><b><u>Projected This Month</u></b></th><th><b><u>Profitability</u></b></th></tr>';
				
			$current_month_time = strtotime($current_month);
			$month2_time = strtotime($month2);
			$month2_end = strtotime(date('Y-m-t',$month2_time));
										
			$months_time = array($month2_time,$current_month_time);
			if(isset($_POST['higher_ed'])){$sphere = "Higher Ed";}
			if(isset($_POST['sphere_kmv'])){$sphere = "Sphere KMV";}
			if(isset($_POST['functional_sphere'])){$sphere = "Functional";}
			
			$keys = array('project','actual','projected','profitability');
			
			$projects_query = $wpdb->prepare("select ID,abbreviated_name,project_name,client_name from ".$wpdb->prefix."projects 
				inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
				where sphere=%s and project_parent=0",$sphere);
			$projects_results = $wpdb->get_results($projects_query);
			$a = array();
			foreach($projects_results as $project)
			{
				$b = array();
				$project_id = $project->ID;
				$abbreviated_name = $project->abbreviated_name;
				if(empty($abbreviated_name)){$abbreviated_name = $project->client_name.' - '.$project->project_name;}
				$abbreviated_name = str_ireplace('University of ','',$abbreviated_name);
				$total_fee_query = $wpdb->prepare("select sum(fee_amount) as fee_amount from ".$wpdb->prefix."invoices where project_id=%d",$project_id);
				$total_fee_results = $wpdb->get_results($total_fee_query);
				$total_fee = $total_fee_results[0]->fee_amount;
				
				$last_month_fee_query = $wpdb->prepare("select fee_amount from ".$wpdb->prefix."invoices 
					where project_id=%d and invoice_date=%d",$project_id,$month2_time);
				$last_month_fee_results = $wpdb->get_results($last_month_fee_query);
				$last_month_fee = $last_month_fee_results[0]->fee_amount;
				
				$projected_time_query = $wpdb->prepare("select sum(projected_hours) as projected_hours,planning_rate from ".$wpdb->prefix."projected_time 
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projected_time.user_id=".$wpdb->prefix."useradd.user_id
					inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
					where projected_month=%d",$current_month_time);
				$projected_time_results = $wpdb->get_results($projected_time_query);
				
				$projected_value =0;
				foreach($projected_time_results as $projected)
				{
					$projected_value += $projected->projected_hours * $projected->planning_rate;
				}
				
				$time_spent_query = $wpdb->prepare("select sum(timesheet_hours) as hours,planning_rate from ".$wpdb->prefix."timesheets 
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
					inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
					where project_id=%d and timesheet_date<=%d and timesheet_date>=%d",$project_id,$month2_end,$month2_time);
				$time_spent_results = $wpdb->get_results($time_spent_query);
				
				$actual_value = 0;
				foreach($time_spent_results as $time)
				{
					$actual_value += $time->hours * $time->planning_rate;
				}
				$profitability = $total_fee - $actual_value;
				
				echo '<tr><th><a href="/?p_action=project_card&ID='.$project_id.'">'.$abbreviated_name.'</a></th>
					<th>$'.number_format($last_month_fee,2).'</th>
					<th>$'.number_format($projected_value,2).'</th>
					<th>'.($profitability < 0 ? '<font color="red">$'.number_format($profitability,2) : '$'.number_format($profitability,2)).'</th></tr>';
				
				array_push($b,$abbreviated_name,$last_month_fee,$projected_value,$profitability);
				$c = array_combine($keys,$b);
				array_push($a,$c);
			}
			//usort($a,function($x){return $x['profitability'];});
			
			echo '</table></li>';
			//print_r($a);
	?>

			</ul>
			</div>
			</div>						
		</div>
		<?php
			$team_query = $wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid);
			$team_results = $wpdb->get_results($team_query);			
			
			$firm_wide_teams = array("Executive","Finance","Human Resources");
			if(in_array($team_results[0]->team,$firm_wide_teams))
			{?>
		
		
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "More Groups";?></h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
					echo '<li><input type="submit" name="higher_ed" class="my-buttons-sidebar" value="Higher Ed" /></li>';
					echo '<li><input type="submit" name="sphere_kmv" class="my-buttons-sidebar" value="Sphere KMV" /></li>';
					echo '<li><input type="submit" name="functional_sphere" class="my-buttons-sidebar" value="Functional" /></li>';
					echo '<li><input type="submit" name="firm_wide" class="my-buttons-sidebar" value="Firm Wide" /></li>';
					?>
					</ul>
				</li>
			</ul>
		</div></div>
			<?php } 
			?>
	</form>

<?php } 
add_shortcode('projects_dashboard','billyB_projects_dashboard')
?>