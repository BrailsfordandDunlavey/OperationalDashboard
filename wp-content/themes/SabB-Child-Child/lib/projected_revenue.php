<?php
function billyB_projected_revenue()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$spherequery = $wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$sphereresult = $wpdb->get_results($spherequery);
	$sphere = $sphereresult[0]->sphere;
	
		?>   
	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				$current_month = date('Y-m-01');
				$beg_of_year = date('Y-01-01');
				$last_6_months = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -6 months"));
				$six_months_ahead = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " +6 months"));
				$second_half_of_year = date('Y-07-01');
				$set_month = $current_month;
				if(isset($_POST['set_start'])){$set_month = ($_POST['select_start']);}
				?>
				
				<li><select class="do_input_new" name="select_start">
					<?php
					echo '<option value="'.$current_month.'"'.($set_month==$current_month ? "selected='selected'" : "").'>Next 6 Months</option>';
					echo '<option value="'.$six_months_ahead.'"'.($set_month==$six_months_ahead ? "selected='selected'" : "").'>Following 6 Months</option>';
					echo '<option value="'.$last_6_months.'"'.($set_month==$last_6_months ? "selected='selected'" : "").'>Last 6 Months</option>';
					echo '<option value="'.$beg_of_year.'"'.($set_month==$beg_of_year ? "selected='selected'" : "").'>First Half of the Year</option>';
					echo '<option value="'.$second_half_of_year.'"'.($set_month==$second_half_of_year ? "selected='selected'" : "").'>Second Half of the Year</option>';
					?>
				</select>&nbsp;&nbsp;
				<input type="submit" name="set_start" class="my-buttons-submit" value="<?php echo "Set Start"; ?>" /></li>
			</ul>
			</div>
			</div>
		</div>
	</form>

	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
						
	<?php						
			$current_month = date('Y-m-01');
			$month2 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($set_month)) . " +1 month"));
			$month3 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month2)) . " +1 month"));
			$month4 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month3)) . " +1 month"));
			$month5 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month4)) . " +1 month"));
			$month6 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month5)) . " +1 month"));
			$months = array($set_month,$month2,$month3,$month4,$month5,$month6);
							
			echo '<li><table width="100%"><tr><th><u>Team Member</u></th>';
			foreach ($months as $month){echo '<th><u>'.date('m-Y',strtotime($month)).'</u></th>';}
				
			$set_month_time = strtotime($set_month);
			$month2_time = strtotime($month2);
			$month3_time = strtotime($month3);
			$month4_time = strtotime($month4);
			$month5_time = strtotime($month5);
			$month6_time = strtotime($month6);
							
			$months_time = array($set_month_time,$month2_time,$month3_time,$month4_time,$month5_time,$month6_time);							
			
			$sphere_members_query = $wpdb->prepare("select user_id from ".$wpdb->prefix."useradd where sphere=%s and user_id!=29",$sphere);
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
				
			foreach($sphere_members_results as $sphere_member)
			{
				$sphere_member_id = $sphere_member->user_id;

				$member_name_query = $wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$sphere_member_id);
				$member_name_results = $wpdb->get_results($member_name_query);
				$member_name = $member_name_results[0]->display_name;
				
				$position_query = $wpdb->prepare("select position from ".$wpdb->prefix."useradd where user_id=%d",$sphere_member_id);
				$position_results = $wpdb->get_results($position_query);
				$position = $position_results[0]->position;
				
				$rate_query = $wpdb->prepare("select * from ".$wpdb->prefix."position_assumptions where position_id=%d",$position);
				$rate_results = $wpdb->get_results($rate_query);
				$rate = $rate_results[0]->planning_rate;
				
				echo '<tr><th><a href="/?p_action=employee_projected_revenue&ID='.$sphere_member_id.'" >'.$member_name.'</a></th>';
								
				foreach($months_time as $month_time)
				{
					$end_of_month = strtotime(date('Y-m-t',$month_time));			
					
					$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time 
						where user_id=%d and projected_month=%d",$sphere_member_id,$month_time);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					$projected_hours = $projected_hours_results[0]->projected;
				
					echo '<th>$'.number_format($projected_hours * $rate,2).'</th>';
				}
				echo '</tr>';			
			}
			echo '</table></li>';
			?>
			</ul>
			</div>
			</div>						
		</div>
	</form>
<?php } 
add_shortcode('projected_revenue','billyB_projected_revenue') ?>