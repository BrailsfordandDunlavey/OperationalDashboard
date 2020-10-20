<?php
function billyB_projected_time_projects()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$spherequery = $wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$sphereresult = $wpdb->get_results($spherequery);
	$sphere = $sphereresult[0]->sphere;
	
	$current_month = date('Y-m-01');
	$set_month = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -1 month"));
	
	if(isset($_POST['sphere_kmv'])){$sphere = "Sphere KMV"; $set_month = $_POST['select_start'];}
	if(isset($_POST['functional_sphere'])){$sphere = "Functional"; $set_month = $_POST['select_start'];}
	if(isset($_POST['higher_ed'])){$sphere = "Higher Ed"; $set_month = $_POST['select_start'];}
	?>
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10" style="overflow-x: auto;">
			<ul class="other-dets_m">
			<?php
			if(isset($_POST['set_start'])){$set_month = ($_POST['select_start']); $sphere = $_POST['hidden_sphere'];}
			
			echo '<li><h2>'.$sphere.'</h2></li>';
			
			echo '<input type="hidden" value="'.$sphere.'" name="hidden_sphere" />';
			
			$previous_months = 12;
			$start_months = array(strtotime($set_month));
			for($i=1;$i<$previous_months;$i++)
			{$prev_month = strtotime(date("Y-m-d",strtotime($set_month)) . " -".$i." months");array_push($start_months,$prev_month);}
			echo '<li><select class="do_input_new" name="select_start">';
			foreach($start_months as $start)
			{echo '<option '.($start == $set_month ? "selected='selected'" : "").' value="'.date('Y-m-d',$start).'">'.date('m-d-Y',$start).'</option>';}
			echo '</select>';
			echo '<input type="submit" name="set_start" value="Set Start" class="my-buttons"/></li>';
			?>
			</ul></div></div></div>
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10" style="overflow-x: auto;">
			<ul class="other-dets_m">
			<?php
			$total_months = 12;
			$months = array($set_month);
			for($i=1,$m=1;$i<$total_months;$i++,$m++)
			{$month = date('Y-m-d',strtotime(date("Y-m-d",strtotime($set_month)) . " +".$i." months"));array_push($months,$month);}

			echo '<li><table width="100%"><tr><th>&nbsp;</th>';
			foreach ($months as $month){echo '<th>'.date('m-Y',strtotime($month)).'</th>';}
			echo '</tr><tr><th><u>Project</u></th>';
			foreach($months as $month){echo '<th><u>Act./ Proj.</u></th>';}
			echo '</tr>';
			
			$months_time = array();
			foreach($months as $month){array_push($months_time,strtotime($month));}

			$sphere_members_query = $wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."useradd inner join ".$wpdb->prefix."users on 
				".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."useradd.status=1
				order by ".$wpdb->prefix."users.display_name",$sphere);
			
			if($current_user->ID==103 or $current_user->ID==245)
			{
				$sphere_members_query = $wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."useradd inner join ".$wpdb->prefix."users on 
				".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID where sphere=%s and ".$wpdb->prefix."useradd.status=1
				order by ".$wpdb->prefix."users.display_name",$sphere);
			}

			if(isset($_POST['firm_wide'])){$sphere_members_query = "select user_id,display_name from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.status=1 order by ".$wpdb->prefix."users.display_name";}
				
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
			
			$projects_array = array();
			
			foreach($sphere_members_results as $sphere_member)
			{
				$user_id = $sphere_member->user_id;
				
				$projects_query = $wpdb->prepare("select project_id from ".$wpdb->prefix."project_user where user_id=%d",$user_id);
				$projects_results = $wpdb->get_results($projects_query);
				
				foreach($projects_results as $project)
				{
					$project_id = $project->project_id;
					if(!in_array($project_id,$projects_array)){array_push($projects_array,$project_id);}
				}
			}
			foreach($projects_array as $projects)
			{
				$project_number_query = $wpdb->prepare("select gp_id,abbreviated_name from ".$wpdb->prefix."projects where ID=%s",$projects);
				$project_number_results = $wpdb->get_results($project_number_query);
				if(empty($project_number_results[0]->abbreviated_name)){$project_number = $project_number_results[0]->gp_id;}else{$project_number = $project_number_results[0]->abbreviated_name;}
				if(!empty($project_number)){echo '<tr><th><a href="/?p_action=project_projected_hours&ID='.$projects.'" >'
					.$project_number.'</a></th>';}
				else{echo '<tr><th><a href="/?p_action=edit_checklist&ID='.$projects.'" >Inactive Project</a></th>';}
				foreach($months_time as $month_time)
				{
					$end_of_month = strtotime(date('Y-m-t',$month_time));
					$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as actual_hours from ".$wpdb->prefix."timesheets where project_id=%s and timesheet_date>=%d
						and timesheet_date<=%d",$projects,$month_time,$end_of_month);
					$actual_hours_results = $wpdb->get_results($actual_hours_query);
				
					$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time 
						where project_id=%s and projected_month=%d",$projects,$month_time);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					
					echo '<th>'.$actual_hours_results[0]->actual_hours.' / '.$projected_hours_results[0]->projected.'</th>';
				}
				echo '</tr>';
			}
			echo '</table></li>';
			?>
			</ul>
			</div>
			</div>						
		</div>
		
		<?php
			$team_query = $wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid);
			$team_results = $wpdb->get_results($team_query);			
			
			$firm_wide_teams = array("Executive","Finance","Human Resources");
			if(in_array($team_results[0]->team,$firm_wide_teams) or $uid==103)
			{?>
		
		<div id="content-full" >
		<div class="my_box3">
			<div class="padd10"><h3><?php echo "More Groups";?></h3>
			<ul class="other-dets_m">
					<?php
					echo '<li><input type="submit" name="higher_ed" class="my-buttons" value="Higher Ed" /></li>';
					echo '<li><input type="submit" name="sphere_kmv" class="my-buttons" value="Sphere KMV" /></li>';
					if($uid != 103)
					{
						echo '<li><input type="submit" name="funtional_sphere" class="my-buttons" value="Functional" /></li>';
						echo '<li><input type="submit" name="firm_wide" class="my-buttons" value="Firm Wide" /></li>';
					}
					?>
			</ul>
		</div></div></div>
			<?php } 
			?>		
	</form>

<?php } 
add_shortcode('projected_time_projects','billyB_projected_time_projects')
?>