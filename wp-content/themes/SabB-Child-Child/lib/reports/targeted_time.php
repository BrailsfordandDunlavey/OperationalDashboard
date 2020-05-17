<?php
function billyB_targeted_time()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	?>
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">			
	<?php						
			$current_month = date('Y-m-01');
			$month2 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -1 month"));
			$month3 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month2)) . " -1 month"));
			$month4 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month3)) . " -1 month"));
			$month5 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month4)) . " -1 month"));
			$month6 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month5)) . " -1 month"));
			$months = array($month6,$month5,$month4,$month3,$month2,$current_month);
							
			echo '<li><table width="100%"><tr><th><b>Team Member</b></th>';
			foreach ($months as $month){echo '<th><b>'.date('m-Y',strtotime($month)).'</b></th>';}
			echo '</tr><tr><th>&nbsp;</th>';
			foreach ($months as $month){echo '<th><b><u>Act / Cap</u></b></th>';}
			echo '</tr>';
				
			$current_month_time = strtotime($current_month);
			$month2_time = strtotime($month2);
			$month3_time = strtotime($month3);
			$month4_time = strtotime($month4);
			$month5_time = strtotime($month5);
			$month6_time = strtotime($month6);
			
			$year = date('Y',$month6_time);
							
			$months_time = array($month6_time,$month5_time,$month4_time,$month3_time,$month2_time,$current_month_time);
			if(isset($_POST['higher_ed'])){$sphere = "Higher Ed";}
			if(isset($_POST['sphere_kmv'])){$sphere = "Sphere KMV";}
			if(isset($_POST['functional_sphere'])){$sphere = "Functional";}
			
			$sphere_members_query = $wpdb->prepare("select user_id,display_name,position from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."useradd.user_id!=29
				and status=1 order by ".$wpdb->prefix."users.display_name",$sphere);
				
			if(isset($_POST['firm_wide']))
			{
				$sphere_members_query = "select user_id,display_name from ".$wpdb->prefix."useradd 
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
					where ".$wpdb->prefix."useradd.user_id!=29 order by ".$wpdb->prefix."users.display_name";
			}
			
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
				
			foreach($sphere_members_results as $sphere_member)
			{
				$sphere_member_id = $sphere_member->user_id;
				$member_name = $sphere_member->display_name;
				$position = $sphere_member->position;
				//BillyB edit the below to link to an actual vs targeted page
				echo '<tr><td><a href="/?p_action=employee_projected_hours&ID='.$sphere_member_id.'" >'.$member_name.'</a></td>';
								
				foreach($months_time as $month_time)
				{
					$end_of_month = strtotime(date('Y-m-t',$month_time));
					$days = 0;
					for($i=$month_time;$i<=$end_of_month;$i=$i+83600)
					{
						if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$days += 1;}
					}
					$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets where user_id=%d and timesheet_date>=%d
						and timesheet_date<=%d",$sphere_member_id,$month_time,$end_of_month);
					$actual_hours_results = $wpdb->get_results($actual_hours_query);
					$actual_hours = $actual_hours_results[0]->actual;
					if($actual_hours == 0){$actual_hours = 0;}
					
					if($sphere == 'Higher Ed')
					{
						$targeted_hours_query = $wpdb->prepare("select he_total_capacity as targeted from ".$wpdb->prefix."position_assumptions 
							where position_id=%d and year=%d",$position,$year);
						$targeted_hours_results = $wpdb->get_results($targeted_hours_query);
						$targeted_hours = $targeted_hours_results[0]->targeted;
					}
					else{$targeted_hours = 8;}
					echo '<td>'.$actual_hours.' / '.round($targeted_hours*$days,2).'</td>';
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
			if(in_array($team_results[0]->team,$firm_wide_teams))
			{?>
		
		
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10"><h3><?php echo "More Groups";?></h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
					echo '<li><input type="submit" name="higher_ed" class="my-buttons-sidebar" value="Sphere Higher Ed" />&nbsp;&nbsp;';
					echo '<input type="submit" name="sphere_kmv" class="my-buttons-sidebar" value="Sphere KMV" />&nbsp;&nbsp;';
					echo '<input type="submit" name="funtional_sphere" class="my-buttons-sidebar" value="Functional" />&nbsp;&nbsp;';
					echo '<input type="submit" name="firm_wide" class="my-buttons-sidebar" value="Firm Wide" /></li>';
					?>
					</ul>
				</li>
			</ul>
		</div></div></div>
			<?php } 
			?>
	</form>

<?php } 
add_shortcode('targeted_time','billyB_targeted_time')
?>