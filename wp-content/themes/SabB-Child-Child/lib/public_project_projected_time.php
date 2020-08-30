<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$project_id = $_GET['ID'];
	
	if(isset($_POST['update-info']))
	{
		$records = ($_POST['record']);
		$timestamp = time();
		
		foreach($records as $record)
		{	
			$projected_id = $record['id'];
			$employee = $record['employee'];
			$month = $record['month'];
			$hours = $record['hours'];
			$previous = $record['previous'];
			
			if($previous != $hours)
			{
				if(empty($previous))
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_time (user_id,project_id,projected_month,projected_hours)
						values (%d,%s,%d,%f)",$employee,$project_id,$month,$hours));
				}
				else
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projected_time set user_id=%d,projected_hours=%f,projected_month=%d,project_id=%s 
						where projected_time_id=%d",$employee,$hours,$month,$project_id,$projected_id));
				}
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_time_alterations (user_id_making,user_id_affected,project_id,projected_month,
					projected_hours,change_date) values (%d,%d,%s,%d,%f,%d)",$uid,$employee,$project_id,$month,$hours,$timestamp));
			}
		}
	}
		?>   
<div id="main_wrapper">
<div id="main" class="wrapper">	
	
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				$current_month = date('Y-m-01');
				$set_month = date('Y-m-d',strtotime(date("Y-m-d",strtotime($current_month)) . " -1 month"));
				if(isset($_POST['set_start'])){$set_month = ($_POST['select_start']);}
				$previous_months = 12;
				$start_months = array(strtotime($set_month));
				for($i=1;$i<$previous_months;$i++)
				{$prev_month = strtotime(date("Y-m-d",strtotime($set_month)) . " -".$i." months");array_push($start_months,$prev_month);}
				echo '<li><select class="do_input_new" name="select_start">';
				foreach($start_months as $start)
				{echo '<option '.($start == $set_month ? "selected='selected'" : "").' value="'.date('Y-m-d',$start).'">'.date('m-d-Y',$start).'</option>';}
				echo '</select>';
				?>
				&nbsp;&nbsp;
				<input type="submit" name="set_start" class="my-buttons-submit" value="<?php echo "Set Start"; ?>" /></li>
			</ul>
			</div>
			</div>
		</div>
	</form>
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php						
			$total_months = 12;
			$months = array($set_month);
			for($i=1,$m=1;$i<$total_months;$i++,$m++)
			{$month = date('Y-m-d',strtotime(date("Y-m-d",strtotime($set_month)) . " +".$i." months"));array_push($months,$month);}
		
			$project_number_query = $wpdb->prepare("select ".$wpdb->prefix."projects.gp_id,project_manager,user_id 
				from ".$wpdb->prefix."projects 
				left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_group=".$wpdb->prefix."useradd.team
				where ".$wpdb->prefix."projects.ID=%s and group_leader=1",$project_id);
			$project_number_results = $wpdb->get_results($project_number_query);
			$project_number = $project_number_results[0]->gp_id;
			$project_manager = $project_number_results[0]->project_manager;
			$group_leader = $project_number_results[0]->user_id;
			
			echo '<li><table width="100%"><tr><th><a href="/?p_action=project_card&ID='.$project_id.'" class="nice_link">'.$project_number.'</a></th>';
			foreach ($months as $month){echo '<th colspan="2">'.date('m-Y',strtotime($month)).'</th>';}
			echo '</tr><tr><th>&nbsp;</th>';
			foreach($months as $month){echo '<th><u>Act.</u></th><th><u>Proj.</u></th>';}
			echo '<th><u>Total</u></th></tr>';
			
			$months_time = array();
			$set_month_time = strtotime($set_month);
			foreach($months as $month)
			{$month_time = strtotime($month);array_push($months_time,$month_time);}					
			
			$team_member_query = $wpdb->prepare("select distinct user_id,display_name from ".$wpdb->prefix."project_user 
				inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."users.ID
				where ".$wpdb->prefix."projects.status=2 and ".$wpdb->prefix."project_user.project_id=%s",$project_id);
			$team_member_results = $wpdb->get_results($team_member_query);
			$t = -1;
			foreach($team_member_results as $member)
			{	
				$member_id = $member->user_id;
				$ok = 0;
				if($project_manager == $uid  or $uid == 103 or $uid==$group_leader){$ok = 1;}
				$member_name = $member->display_name;
				
				echo '<tr><th><a href="/?p_action=employee_projected_hours&ID='.$member_id.'" >'.$member_name.'</a></th>';
				$total_actual = 0;
				$total_projected = 0;
				$m = -1;
				foreach($months_time as $month_time)
				{					
					$t++;
					$m++;
					$end_of_month = strtotime(date('Y-m-t',$month_time));
					$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets where user_id=%d and timesheet_date>=%d
						and timesheet_date<=%d and project_id=%s",$member_id,$month_time,$end_of_month,$project_id);
					$actual_hours_results = $wpdb->get_results($actual_hours_query);
					$actual_hours = $actual_hours_results[0]->actual;					
					
					$projected_hours_query = $wpdb->prepare("select projected_time_id,sum(projected_hours) as projected from ".$wpdb->prefix."projected_time 
						where user_id=%d and projected_month=%d and project_id=%s",$member_id,$month_time,$project_id);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					$projected_hours = $projected_hours_results[0]->projected;
					$projected_id = $projected_hours_results[0]->projected_time_id;
					echo '<input type="hidden" name="record['.$t.'][id]" value="'.$projected_id.'" />';
					echo '<input type="hidden" name="record['.$t.'][employee]" value="'.$member_id.'" />';
					echo '<input type="hidden" name="record['.$t.'][month]" value="'.$months_time[$m].'" />';
					echo '<input type="hidden" name="record['.$t.'][previous]" value="'.$projected_hours.'" />';
					echo '<td>'.$actual_hours.'</td><td><input '.($months_time[$m] < strtotime($current_month) ? "readonly" : ($ok == 0 ? "readonly" : "")).'
						type="text" size="1" name="record['.$t.'][hours]" value="'.$projected_hours.'" /></td>';
					
					$total_actual += $actual_hours;
					$total_projected += $projected_hours;				
				}
				echo '<th colspan="2">'.$total_actual.' / '.$total_projected.'</th>';
				echo '</tr>';			
			}
			echo '<tr><th>&nbsp;</th></tr>';
			echo '<tr><th>Total</th>';
			foreach($months_time as $month_time)
			{					
				$end_of_month = strtotime(date('Y-m-t',$month_time));
				$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets where project_id=%s and timesheet_date>=%d
					and timesheet_date<=%d",$project_id,$month_time,$end_of_month);
				$actual_hours_results = $wpdb->get_results($actual_hours_query);
				$actual_hours = $actual_hours_results[0]->actual;					
				
				$projected_hours_query = $wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where project_id=%s and 
					projected_month=%d",$project_id,$month_time);
				$projected_hours_results = $wpdb->get_results($projected_hours_query);
				$projected_hours = $projected_hours_results[0]->projected;
			
				echo '<th colspan="2"><b>'.$actual_hours.' / '.$projected_hours.'</b></th>';	
			}
			echo '</tr></table></li>';
			?>
			<li>&nbsp;</li>
			<li><input type="submit" name="update-info" class="my-buttons-submit" value="<?php echo "update"; ?>" /></li>
			</ul>
			</div>
			</div>						
		</div>
	</div>
	</div>
	</form>
<?php get_footer(); ?>	