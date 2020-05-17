<?php
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$employee_id = $_GET['ID'];
	$member_name_results = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$employee_id));
	$member_name = $member_name_results[0]->display_name;
	
	function sitemile_filter_ttl($title)
	{
		global $current_user,$wpdb,$wp_query;
		$employee_id = $_GET['ID'];
		$member_name_results = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$employee_id));
		$member_name = $member_name_results[0]->display_name;
		return $member_name." Projected Hours";
	}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	if(isset($_POST['update-info']))
	{
		$timestamp = time();
		$records = ($_POST['record']);
						
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$projected_id = $details[0];
			$previous = $details[1];
			$project_id = $details[2];
			$month = $details[3];
			
			$hours = $record['hours'];
			if($previous != $hours)
			{
				if($hours == 0)
				{
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."projected_time where projected_time_id=%d",$projected_id));
				}
				elseif(empty($previous))
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_time (user_id,project_id,projected_month,projected_hours) 
						values (%d,%s,%d,%f)",$uid,$project_id,$month,$hours));
				}
				else
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projected_time set user_id=%d,projected_hours=%f,projected_month=%d,project_id=%s 
						where projected_time_id=%d",$uid,$hours,$month,$project_id,$projected_id));
				}
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_time_alterations (user_id_making,user_id_affected,project_id,projected_month,
					projected_hours,change_date) values (%d,%d,%s,%d,%f,%d)",$uid,$employee_id,$project_id,$month,$hours,$timestamp));
			}
		}
	}
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
				$set_month = date('Y-m-d',strtotime(date("Y-m-d", strtotime($current_month)) . " -1 month"));
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
				<input type="submit" name="set_start" class="my-buttons-submit" value="Set Start" /></li>
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
			
			echo '<li style="overflow-x:auto;"><table width="100%"><tr><th><a href="/?p_action=user_profile&ID='.$employee_id.'" class="nice_link">'.$member_name.'</a></th>';
			foreach ($months as $month){echo '<th colspan="2" style="text-align:center;">'.date('m-Y',strtotime($month)).'</th>';}
	
			echo '</tr><tr><th><u>Project</u></th>';
			foreach($months as $month){echo '<th><u>Act</u></th><th><u>Proj</u></th>';}
			echo '<th colspan="2"><u>Total</u></th></tr>';
			
			$months_time = array();
			foreach($months as $month){array_push($months_time,strtotime($month));}							
			
			$active_projects = array();//store all active and overhead codes to query for inactive projects
			$admin_codes = array();//store admin codes for query of overhead time
			//add in opportunities - status of 4, 5, and 6 (farming, 50/50, likely)
			$projects_results = $wpdb->get_results($wpdb->prepare("select distinct project_id,".$wpdb->prefix."projects.gp_id,project_manager,abbreviated_name,".$wpdb->prefix."projects.status,".$wpdb->prefix."useradd.user_id,project_name 
				from ".$wpdb->prefix."project_user 
				inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID
				left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_group=".$wpdb->prefix."useradd.team
				where ".$wpdb->prefix."projects.status in (0,1,2,4,5,6) and ".$wpdb->prefix."project_user.user_id=%d and group_leader=1
				order by status,".$wpdb->prefix."projects.ID",$employee_id));
			
			$admin_results = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where timesheet_available=1 order by other_project_code_name");
			//add overhead array
			//combine project_results projects with overhead array and then query for any projects with actual time "not in" that array
			foreach($admin_results as $ar){array_push($active_projects,$ar->other_project_code_value);array_push($admin_codes,$ar->other_project_code_value);}
			array_push($admin_codes,'Vacation','Sick','Float','BEREAV','JURY','MATPAT','Holiday');
			array_push($active_projects,'Vacation','Sick','Float','BEREAV','JURY','MATPAT','Holiday');
			$admin_array = "";
			for($i=0;$i<count($admin_codes);$i++)
			{
				if($i<count($admin_codes)-1)
				{
					$admin_array .= '"'.$admin_codes[$i].'",';
				}
				else
				{
					$admin_array .= '"'.$admin_codes[$i].'"';
				}
			}
			$all_active_projects = "";
			for($i=0;$i<count($active_projects);$i++)
			{
				if($i<count($active_projects)-1)
				{
					$all_active_projects .= '"'.$active_projects[$i].'",';
				}
				else
				{
					$all_active_projects .= '"'.$active_projects[$i].'"';
				}
			}
			$total_actual_total = 0;
			$total_projected_total = 0;
			if($uid == $employee_id){$oks = 1;}else{$oks = 0;}
			
			$t = -1;
			foreach($projects_results as $project)
			{
				$project_manager = $project->project_manager;
				$group_leader = $project->user_id;
				$ok = 0;
				if($uid==$group_leader or $uid==$project_manager or $uid==103 or $uid==245){$ok =1;$oks++;}
				$project_id = $project->project_id;
				if(!empty($project->abbreviated_name)){$abb_name = $project->abbreviated_name;}
				elseif(!empty($project->project_name)){$abb_name = $project->project_name;}
				elseif(!empty($project->gp_id)){$abb_name = $project->gp_id;}
				else{$abb_name = "No Project Name yet";}
				if($ok==1){$link = '<a href="/?p_action=project_projected_hours&ID='.$project_id.'" >';}
					else{$link = '<a href="/?p_action=project_card&ID='.$project_id.'" >';}
				echo '<tr><th>'.$link.''.$abb_name.'</a></th>';
				
				$total_actual = 0;
				$total_projected = 0;
				$m = -1;
				foreach($months_time as $month_time)
				{	
					$t++;
					$m++;
					$end_of_month = strtotime(date('Y-m-t',$month_time));
					$actual_hours_query = $wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets where user_id=%d and timesheet_date>=%d
						and timesheet_date<=%d and project_id=%s",$employee_id,$month_time,$end_of_month,$project_id);
					$actual_hours_results = $wpdb->get_results($actual_hours_query);
					$actual_hours = $actual_hours_results[0]->actual;
					
					$projected_hours_query = $wpdb->prepare("select projected_time_id,sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where user_id=%d and projected_month=%d
						and project_id=%s",$employee_id,$month_time,$project_id);
					$projected_hours_results = $wpdb->get_results($projected_hours_query);
					$projected_hours = $projected_hours_results[0]->projected;
					$projected_id = $projected_hours_results[0]->projected_time_id;
					echo '<input type="hidden" name="record['.$t.'][details]" value="'.$projected_id.',,,'.$projected_hours.',,,'.$project_id.',,,'.$months_time[$m].'" />';
					echo '<th>'.$actual_hours.'</th><th><input '.($months_time[$m] < strtotime($current_month) ? "readonly" : ($ok == 0 ? "readonly" : "")).'
						type="text" size="2" name="record['.$t.'][hours]" value="'.$projected_hours.'" /></th>';
					
					$total_actual += $actual_hours;
					$total_projected += $projected_hours;
					$total_actual_total += $actual_hours;
					$total_projected_total += $projected_hours;
				}
				echo '<th>'.$total_actual.' / '.$total_projected.'</th>';
				echo '</tr>';
				$all_active_projects .= ',"'.$project_id.'"';
			}
			//add closed projects
			$beg = $months_time[0];
			$end = strtotime(date('Y-m-t',$months_time[count($months_time)-1]));
			$other_projects = $wpdb->get_results($wpdb->prepare("select distinct project_id,project_manager from ".$wpdb->prefix."timesheets 
				where timesheet_date>=%d and timesheet_date<=%d and user_id=%d and project_id not in (".$all_active_projects.")",$beg,$end,$employee_id));
			if(!empty($other_projects))
			{
				
				foreach($other_projects as $op)
				{
					$m = -1;
					$total_actual = 0;
					$total_projected = 0;
					$project_id = $op->project_id;
					$project_manager = $op->project_manager;
					echo '<tr><th><font color="red">'.$op->project_id.'</font></th>';
					
					foreach($months_time as $month_time)
					{
						$t++;
						$m++;
						$end_of_month = strtotime(date('Y-m-t',$month_time));
						$actual_hours_results = $wpdb->get_results($wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets 
							where user_id=%d and timesheet_date >=%d and timesheet_date<=%d and project_id=%s",$employee_id,$month_time,$end_of_month,$project_id));
						$actual_hours = $actual_hours_results[0]->actual;
						
						$projected_hours_results = $wpdb->get_results($wpdb->prepare("select projected_time_id,sum(projected_hours) as projected 
							from ".$wpdb->prefix."projected_time 
							where user_id=%d and projected_month=%d and project_id=%s",$employee_id,$month_time,$project_id));
						$projected_hours = $projected_hours_results[0]->projected;
						$projected_id = $projected_hours_results[0]->projected_time_id;
						echo '<input type="hidden" name="record['.$t.'][details]" value="'.$projected_id.',,,'.$projected_hours.',,,'.$project_id.',,,'.$months_time[$m].'" />';
						echo '<th>'.$actual_hours.'</th><th><input "readonly" type="text" size="2" value="'.$projected_hours.'" /></th>';
						
						$total_actual += $actual_hours;
						$total_projected += $projected_hours;
						$total_actual_total += $actual_hours;
						//$total_projected_total += $projected_hours;
					}
					echo '<th>'.$total_actual.' / '.$total_projected.'</th>';
					echo '</tr>';
					$all_active_projects .= ',"'.$project_id.'"';
				}
			}
			//add projects with projected time, but not listed on account and not on timesheets yet
			$beg = $months_time[0];
			$end = strtotime(date('Y-m-t',$months_time[count($months_time)-1]));
			$other_projects = $wpdb->get_results($wpdb->prepare("select distinct project_id,project_manager 
				from ".$wpdb->prefix."projected_time 
				left join ".$wpdb->prefix."projects on ".$wpdb->prefix."projected_time.project_id=".$wpdb->prefix."projects.ID
				where projected_month=%d and user_id=%d and project_id not in (".$all_active_projects.")",$beg,$employee_id));
			if(!empty($other_projects))
			{
				
				foreach($other_projects as $op)
				{
					$m = -1;
					$total_actual = 0;
					$total_projected = 0;
					$project_id = $op->project_id;
					$project_manager = $op->project_manager;
					if($ok==1){$link = '<a href="/?p_action=project_projected_hours&ID='.$project_id.'" >';}
					else{$link = '<a href="/?p_action=project_card&ID='.$project_id.'" >';}
					
					echo '<tr><th><font color="red">'.$link.$op->project_id.'</a></font></th>';
					
					foreach($months_time as $month_time)
					{
						$t++;
						$m++;
						$end_of_month = strtotime(date('Y-m-t',$month_time));
						$actual_hours_results = $wpdb->get_results($wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets 
							where user_id=%d and timesheet_date >=%d and timesheet_date<=%d and project_id=%s",$employee_id,$month_time,$end_of_month,$project_id));
						$actual_hours = $actual_hours_results[0]->actual;
						
						$projected_hours_results = $wpdb->get_results($wpdb->prepare("select projected_time_id,sum(projected_hours) as projected 
							from ".$wpdb->prefix."projected_time 
							where user_id=%d and projected_month=%d and project_id=%s",$employee_id,$month_time,$project_id));
						$projected_hours = $projected_hours_results[0]->projected;
						$projected_id = $projected_hours_results[0]->projected_time_id;
						echo '<input type="hidden" name="record['.$t.'][details]" value="'.$projected_id.',,,'.$projected_hours.',,,'.$project_id.',,,'.$months_time[$m].'" />';
						echo '<th>'.$actual_hours.'</th><th><input "readonly" type="text" size="2" value="'.$projected_hours.'" /></th>';
						
						$total_actual += $actual_hours;
						$total_projected += $projected_hours;
						$total_actual_total += $actual_hours;
						//$total_projected_total += $projected_hours;
					}
					echo '<th>'.$total_actual.' / '.$total_projected.'</th>';
					echo '</tr>';
				}
			}
			//add overhead
			echo '<tr><th>General Overhead</th>';
			$m = -1;
			$total_actual = 0;
			$total_projected = 0;
			foreach($months_time as $month_time)
			{
				$t++;
				$m++;
				$end_of_month = strtotime(date('Y-m-t',$month_time));
				$actual_hours_results = $wpdb->get_results($wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets 
					where user_id=%d and timesheet_date >=%d and timesheet_date<=%d and project_id in (".$admin_array.")",
					$employee_id,$month_time,$end_of_month));
				$actual_hours = $actual_hours_results[0]->actual;
					
				$overhead_projection_query = $wpdb->prepare("select projected_time_id,sum(projected_hours) as projected from ".$wpdb->prefix."projected_time where user_id=%d and projected_month=%d
					and project_id='0001'",$employee_id,$month_time);
				$overhead_projection_results = $wpdb->get_results($overhead_projection_query);
				$projected_id = $overhead_projection_results[0]->projected_time_id;
				$projected_hours = $overhead_projection_results[0]->projected;
				
				echo '<input type="hidden" name="record['.$t.'][details]" value="'.$projected_id.',,,'.$projected_hours.',,,0001,,,'.$months_time[$m].'" />';
				echo '<th>'.$actual_hours.'</th><th><input '.($months_time[$m] < strtotime($current_month) ? 
					'readonly title="Can only edit for current or future months"' : ($uid != $employee_id ? 'readonly title="Only the user or PM can make this edit"' : "")).'
					type="text" size="2" name="record['.$t.'][hours]" value="'.$projected_hours.'" /></th>';
				
				$total_actual += $actual_hours;
				$total_projected += $projected_hours;
				$total_actual_total += $actual_hours;
				//$total_projected_total += $projected_hours;
			}
			echo '<th>'.$total_actual.' / '.$total_projected.'</th>';
			echo '</tr>';
			echo '<tr><th>&nbsp;</th></tr>';
			echo '<tr><th>Total</th>';
			foreach($months_time as $month_time)
			{
				$end_of_month = strtotime(date('Y-m-t',$month_time));
				$actual_hours_results = $wpdb->get_results($wpdb->prepare("select sum(timesheet_hours) as actual from ".$wpdb->prefix."timesheets 
					where user_id=%d and timesheet_date >=%d and timesheet_date<=%d",$employee_id,$month_time,$end_of_month));
				$actual_hours = $actual_hours_results[0]->actual;					
				
				$projected_hours_results = $wpdb->get_results($wpdb->prepare("select sum(projected_hours) as projected from ".$wpdb->prefix."projected_time 
					where user_id=%d and projected_month=%d",$employee_id,$month_time));
				$projected_hours = $projected_hours_results[0]->projected;
			
				echo '<th colspan="2"><b>'.$actual_hours.' / '.$projected_hours.'</b></th>';
			}
			
		echo '<th colspan="2"><b>'.$total_actual_total.' / '.$total_projected_total.'</b></th></tr></table></li>';
	
		echo '<li>&nbsp;</li>';
	
		if($oks >0){echo '<li><input type="submit" name="update-info" class="my-buttons-submit" value="update" /></li>';}
				?>
			</ul>
			</div>
			</div>						
		</div>
	</div>
	</div>
	</form>
<?php get_footer(); ?>	