<?php
function billyB_targeted_projected_time()
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
			$months_to_show = 6;
			$start = strtotime(date("Y-m-d", strtotime(date('Y-m-01'))) . " -3 months");
			$months_array = array($start);
			
			for($i=1;$i<=$months_to_show;$i++)
			{
				$month = strtotime(date("Y-m-d",$start) . " +".$i." months");
				array_push($months_array,$month);
			}			
			echo '<li><table width="100%"><tr><th>Team Member</th>';
			foreach ($months_array as $month){echo '<th>'.date('m-Y',$month).'</th>';}
			echo '</tr>';
			echo '<tr><th>&nbsp;</th>';
			foreach ($months_array as $month){echo '<th><u>Proj / Tar</u></th>';}
			echo '</tr>';
			
			if(isset($_POST['sphere_higher_ed'])){$sphere = "Higher Ed";}
			if(isset($_POST['sphere_kmv'])){$sphere = "Sphere KMV";}
			if(isset($_POST['food_team'])){$sphere = "Food";}
			if(isset($_POST['functional_sphere'])){$sphere = "Functional";}
			
			//BillyB revise query to pick up all projected time within the months array
			$end = end($months_array);
			
			
			$sphere_members_query = $wpdb->prepare("select user_id,target_billable,display_name,status from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."useradd.user_id!=29
				order by ".$wpdb->prefix."users.display_name",$sphere);
				
			if(isset($_POST['firm_wide']))
			{
				$sphere_members_query = "select user_id,target_billable,display_name,status from ".$wpdb->prefix."useradd 
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
					where ".$wpdb->prefix."useradd.user_id!=29 
					order by ".$wpdb->prefix."users.display_name";
			}
			
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
				
			foreach($sphere_members_results as $sphere_member)
			{
				$sphere_member_id = $sphere_member->user_id;
				$member_name = $sphere_member->display_name;
				$target_billable = $sphere_member->target_billable;
				$status = $sphere_member->status;
				
				//BillyB edit the below to link to an actual vs targeted page
				echo '<tr><th><a href="/?p_action=employee_projected_hours&ID='.$sphere_member_id.'" >'.$member_name.'</a></th>';
								
				foreach($months_array as $month_time)
				{
					$available = 0;
					$month_end = strtotime(date('Y-m-t',$month_time));
					
					for($i=$month_time;$i<$month_end;$i = $i + 86400)
					{
						if(date('D',$i) != 'Sun' and date('D',$i) != 'Sat'){$available += 8;}
					}
					
					$targeted_hours = round(($available *($target_billable/100)),0);
					$targeted_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time 
						where user_id=%d and projected_month=%d",$sphere_member_id,$month_time);
					$targeted_hours_results = $wpdb->get_results($targeted_hours_query);
					
					$projected_hours = $targeted_hours_results[0]->projected;
					if(empty($projected_hours)){$projected_hours = '0.00';}
					
					$percentage = round(($projected_hours/$targeted_hours),2)*100;
					if($percentage > 100){$font = '<font color="red"><strong>';$font_end = '</strong></font>';}
					else{$font = "";$font_end = "";}
					echo '<th>'.$projected_hours.' / '.$targeted_hours.' ('.$font.$percentage.'%'.$font_end.')</th>';
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
					echo '<li><input type="submit" name="sphere_higher_ed" class="my-buttons-sidebar" value="Higher Ed" /></li>';
					echo '<li><input type="submit" name="sphere_kmv" class="my-buttons-sidebar" value="Sphere KMV" /></li>';
					echo '<li><input type="submit" name="funtional_sphere" class="my-buttons-sidebar" value="Functional" /></li>';
					echo '<li><input type="submit" name="firm_wide" class="my-buttons-sidebar" value="Firm Wide" /></li>';
					?>
					</ul>
				</li>
			</ul>
		</div></div></div>
			<?php } 
			?>
	</form>

<?php } 
add_shortcode('targeted_projected_time','billyB_targeted_projected_time')
?>