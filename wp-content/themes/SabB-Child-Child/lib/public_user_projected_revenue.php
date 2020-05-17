<?php

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$employee_id = $_GET['ID'];
	get_header();
	?>   
	<div id="main_wrapper">
	<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				$current_month = date('Y-m-01');
				$first_quarter = date('Y-01-01');
				$second_quarter = date('Y-04-01');
				$third_quarter = date('Y-07-01');
				$fourth_quarter = date('Y-10-01');
				$last_3_months = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -3 months"));
				$six_months_ahead = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " +3 months"));
				$set_month = $current_month;
				if(isset($_POST['set_start'])){$set_month = ($_POST['select_start']);}
				?>
				
				<li><select class="do_input_new" name="select_start">
					<?php
					echo '<option value="'.$current_month.'"'.($set_month==$current_month ? "selected='selected'" : "").'>Next 3 Months</option>';
					echo '<option value="'.$six_months_ahead.'"'.($set_month==$six_months_ahead ? "selected='selected'" : "").'>Following 3 Months</option>';
					echo '<option value="'.$last_3_months.'"'.($set_month==$last_3_months ? "selected='selected'" : "").'>Last 3 Months</option>';
					echo '<option value="'.$first_quarter.'"'.($set_month==$first_quarter ? "selected='selected'" : "").'>First Quarter</option>';
					echo '<option value="'.$second_quarter.'"'.($set_month==$second_quarter ? "selected='selected'" : "").'>Second Quarter</option>';
					echo '<option value="'.$third_quarter.'"'.($set_month==$third_quarter ? "selected='selected'" : "").'>Third Quarter</option>';
					echo '<option value="'.$fourth_quarter.'"'.($set_month==$fourth_quarter ? "selected='selected'" : "").'>Fourth Quarter</option>';
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
			$month2 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($set_month)) . " +1 month"));
			$month3 = date('Y-m-d',strtotime(date("Y-m-d", strtotime($month2)) . " +1 month"));
			$months = array($set_month,$month2,$month3);
			
			$member_name_query = $wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$employee_id);
			$member_name_results = $wpdb->get_results($member_name_query);
			$member_name = $member_name_results[0]->display_name;
			
			$position_query = $wpdb->prepare("select position from ".$wpdb->prefix."useradd where user_id=%d",$employee_id);
			$position_results = $wpdb->get_results($position_query);
			$position = $position_results[0]->position;
				
			$rate_query = $wpdb->prepare("select * from ".$wpdb->prefix."position_assumptions where position_id=%d",$position);
			$rate_results = $wpdb->get_results($rate_query);
			$rate = $rate_results[0]->planning_rate;
			
			echo '<li><table width="100%"><tr><th><a href="/?p_action=user_profile&ID='.$employee_id.'" class="nice_link">'.$member_name.'</a></th>';
			foreach ($months as $month){echo '<th><u>'.date('m-Y',strtotime($month)).'</u></th>';}
			echo '<th><u>Total</u></th></tr>';
				
			$set_month_time = strtotime($set_month);
			$month2_time = strtotime($month2);
			$month3_time = strtotime($month3);
							
			$months_time = array($set_month_time,$month2_time,$month3_time);							
			
			$projects_query = $wpdb->prepare("select distinct project_id,".$wpdb->prefix."projects.gp_id from ".$wpdb->prefix."project_user 
				inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID
				where ".$wpdb->prefix."projects.status=2 and ".$wpdb->prefix."project_user.user_id=%d",$employee_id);
			$projects_results = $wpdb->get_results($projects_query);
			
			$t = -1;
			$total_total_projected = 0;
			foreach($projects_results as $project)
			{	
				$project_id = $project->project_id;
				echo '<tr><th><a href="/?p_action=project_projected_hours&ID='.$project_id.'" >'.$project->gp_id.'</a></th>';
				
				$total_actual = 0;
				$total_projected = 0;
				$m = -1;
				foreach($months_time as $month_time)
				{		
					$t++;
					$m++;
					$end_of_month = strtotime(date('Y-m-t',$month_time));

					$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where user_id=%d and projected_month=%d
						and project_id=%s",$employee_id,$month_time,$project_id);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					$projected_hours = $projected_hours_results[0]->projected;
					echo '<th>'.ProjectTheme_get_show_price($projected_hours * $rate).'</th>';
					
					$total_projected += $projected_hours;
					$total_total_projected += $projected_hours;
				}
				echo '<th>$'.number_format($total_projected * $rate,2).'</th>';
				echo '</tr>';			
			}
			echo '<tr><th>&nbsp;</th></tr>';
			echo '<tr><th>Total</th>';
			foreach($months_time as $month_time)
			{					
				$end_of_month = strtotime(date('Y-m-t',$month_time));			
				
				$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where user_id=%d 
					and projected_month=%d",$employee_id,$month_time);
				$projected_hours_results = $wpdb->get_results($projected_hours_query);
				$projected_revenue = ($projected_hours_results[0]->projected * $rate);
			
				echo '<th><b>$'.number_format($projected_revenue,2).'</b></th>';
			}
			echo '<th>$'.number_format($total_total_projected * $rate,2).'</th></tr></table></li>';
			?>			
			</ul>
			</div>
			</div>						
		</div>
	</div>
	</div>
	</form>
<?php   
	get_footer();
?>	