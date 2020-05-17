<?php
function billyB_projected_time()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	
	if(isset($_POST['higher_ed'])){$sphere = "Higher Ed";}
	if(isset($_POST['sphere_kmv'])){$sphere = "Sphere KMV";}
	if(isset($_POST['functional_sphere'])){$sphere = "Functional";}	
	?>   
	<script type="text/javascript">
	function hideRows(){
		var button = document.forms.projected_time.elements['hide_rows'];
		//var allRows = document.querySelectorAll("[id*='status']");
		var inactiveRows = document.querySelectorAll("[id*='status0']");
		
		if(button.value == 'Show Inactive'){
			for(i=0;i<inactiveRows.length;i++){
				inactiveRows[i].style.display = 'table-row';
			}
			button.value = 'Hide Inactive';
		}
		else{
			for(i=0;i<inactiveRows.length;i++){
				inactiveRows[i].style.display = 'none';
			}
			button.value = 'Show Inactive';
		}
	}
	</script>
	<form method="post" name="projected_time" enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
						
	<?php						
			echo '<li><h2>'.$sphere.'</h2></li>';
			echo '<li><input type="button" name="hide_rows" value="Show Inactive" onClick="hideRows();" /></li>';
			//billyB rework the months into one section
			$current_month = date('Y-m-01');
			$month2 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " +1 month"));
			$month3 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month2)) . " +1 month"));
			$month4 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month3)) . " +1 month"));
			$month5 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month4)) . " +1 month"));
			$month6 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month5)) . " +1 month"));
			$months = array($current_month,$month2,$month3,$month4,$month5,$month6);
							
			echo '<li style="overflow-x: auto;"><table width="100%"><tr><th>Team Member</th>';
			foreach ($months as $month){echo '<th>'.date('m-Y',strtotime($month)).'</th>';}
			echo '</tr><tr><th>&nbsp;</th><th><u>Act. / Proj.</u></th><th><u>Act. / Proj.</u></th><th><u>Act. / Proj.</u></th><th><u>Act. / Proj.</u></th>
				<th><u>Act. / Proj.</u></th><th><u>Act. / Proj.</u></th></tr>';
				
			$current_month_time = strtotime($current_month);
			$month2_time = strtotime($month2);
			$month3_time = strtotime($month3);
			$month4_time = strtotime($month4);
			$month5_time = strtotime($month5);
			$month6_time = strtotime($month6);
							
			$months_time = array($current_month_time,$month2_time,$month3_time,$month4_time,$month5_time,$month6_time);							
			
			//billyb rework queries, loop and create one array that can be then sorted
			
			$sphere_members_query = $wpdb->prepare("select user_id,display_name,status from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.sphere=%s
				order by ".$wpdb->prefix."users.display_name",$sphere);
				
			if(isset($_POST['firm_wide'])){$sphere_members_query = "select user_id,display_name,".$wpdb->prefix."useradd.status from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				order by ".$wpdb->prefix."users.display_name";}
			
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
			
			foreach($sphere_members_results as $sphere_member)
			{
				$sphere_member_id = $sphere_member->user_id;
				$member_name = $sphere_member->display_name;
				if($sphere_member->status==0){$display = 'style="display:none;"';}else{$display ='';}
				echo '<tr id="status'.$sphere_member->status.'" '.$display.'><th><a href="/?p_action=employee_projected_hours&ID='.$sphere_member_id.'" >'.$member_name.'</a></th>';
								
				foreach($months_time as $month_time)
				{
					$end_of_month = strtotime(date('Y-m-t',$month_time));
					
					$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as timesheet_hours,project_id from ".$wpdb->prefix."timesheets 
						where user_id=%d and timesheet_date >=%d and timesheet_date<=%d
						group by project_id",$sphere_member_id,$month_time,$end_of_month);
					$actual_hours_results = $wpdb->get_results($actual_hours_query);
					$actual_hours = 0;
					$project_array = array();
					foreach($actual_hours_results as $ahr)
					{
						$actual_hours += $ahr->timesheet_hours;
						$record_array = array($ahr->project_id,$ahr->timesheet_hours);
						array_push($project_array,$record_array);
					}
					
					$projected_hours_query = $wpdb->prepare("select projected_hours,project_id,fee_type from ".$wpdb->prefix."projected_time 
						inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."projected_time.project_id=".$wpdb->prefix."projects.ID
						where user_id=%d and projected_month=%d",$sphere_member_id,$month_time);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					$projected_hours = 0;
					foreach($projected_hours_results as $phr)
					{
						if($phr->projected_hours == 0)
						{
							foreach($project_array as $pa)
							{
								if($pa[0] == $phr->project_id and substr($phr->fee_type,3) == "T&M")
								{
									$hours = $pa[1];
								}
							}
						}
						else{$hours = $phr->projected_hours;}
						$projected_hours += $hours;
					}
				
					echo '<th>'.$actual_hours.' / '.$projected_hours.'</th>';
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
			$team_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
			
			$firm_wide_teams = array("Executive","Finance","Human Resources");
			if(in_array($team_results[0]->team,$firm_wide_teams) or $uid==103)
			{	
				?>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "More Groups";?></h3>
				<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
					echo '<li><input type="submit" name="higher_ed" class="my-buttons" value="Sphere Higher Ed" /></li>';
					echo '<li><input type="submit" name="sphere_kmv" class="my-buttons" value="Sphere KMV" /></li>';
					if($uid !=103)
					{
						echo '<li><input type="submit" name="funtional_sphere" class="my-buttons" value="Functional" /></li>';
						echo '<li><input type="submit" name="firm_wide" class="my-buttons" value="Firm Wide" /></li>';
					}
					?>
					</ul>
				</li>
				</ul>
				</div></div>
				<?php 
			}?>
	</form>

<?php } 
add_shortcode('projected_time','billyB_projected_time')
?>