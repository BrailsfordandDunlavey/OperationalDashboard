<?php
function billyB_my_time()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	
	$uid = $current_user->ID;
	$change_array = array(11,104,66,74,230);//allow Jessica,Ann,Caitlin,Cho, and Bill to change the user
	
	$today = date('Y-m-d',time());
	$days_back = 60;
	$deadline = time() -($days_back * 86400);
	$now_period_query = "select time_period_id from ".$wpdb->prefix."time_periods where start_date<='$today' and end_date>='$today'";
	$now_period_results = $wpdb->get_results($now_period_query);
	$now_period = $now_period_results[0]->time_period_id;
	$timeperiod1=$now_period;
	$periods = 12;
	
	if(isset($_POST['save-info']))
	{
		$timeperiod1 = trim($_POST['time_period']);
		$records = $_POST['record'];
		$periods = $_POST['num_periods'];
		
		foreach($records as $r)
		{
			$timesheet_id = $r['id'];
			$note = $r['note'];
			
			if($note != $r['orig'])
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_notes=%s where timesheet_id=%d",$note,$timesheet_id));
			}
		}
	}
	
	if(in_array($current_user->ID,$change_array))
	{if(isset($_POST['set_id']))
	{
		$uid = $_POST['change_id'];
		$timeperiod1 = trim($_POST['time_period']);
		$periods = $_POST['num_periods'];
	}}
	else{$uid = $current_user->ID;}
	?>   

	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<li><h3>Number of Periods</h3><p><input type="number" min="0" max="100" step="1" name="num_periods" class="do_input_new" value="<?php echo $periods;?>" /></p></li>
			<li><h3>Time Period Start</h3>
			<p><select class="do_input_new" name="time_period">
			<?php
				
				if(isset($_POST['set-time-period']))
				{
					$timeperiod1 = $_POST['time_period'];
					$periods = $_POST['num_periods'];
					if(in_array($current_user->ID,$change_array)){$uid=$_POST['change_id'];}
				}
				$now = time();
				$periodsresult = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."time_periods where start_date<=%s order by start_date desc limit %d",$today,$periods));
				
				foreach($periodsresult as $period)
				{
					if(empty($timeperiod1) and strtotime($period->start_date) < $now and strtotime($period->end_date) > $now){$timeperiod1=$period->time_period_id;}
					echo '<option ';
					if($period->time_period_id==$timeperiod1){echo "selected='selected'";}
					echo 'value="'.$period->time_period_id.'">'.date('m-d-Y',strtotime($period->start_date)).'</option>';
				}
				
				?>
				</select>
				<input type="submit" name="set-time-period" class="my-buttons" value="Update Period" /></p>
				</li>
				
				<?php
					if(in_array($current_user->ID,$change_array))
					{
						echo '<li><h3>Change Employee:</h3><p><select class="do_input_new" name="change_id">';
						$users_results = $wpdb->get_results("select * from ".$wpdb->prefix."users 
							where display_name!='admin' and display_name!='bbannister' and display_name!='TEST'	order by display_name");
						foreach($users_results as $user)
						{
							echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
						}
						echo '</select> ';
						echo '<input type="submit" name="set_id" class="my-buttons" value="Change ID" /></p></li>';
					}
				?>
				</ul>
			</div>
			</div>						
		</div>
	
	<div id="content">
		<div class="my_box3">
		<div class="padd10"><h3>Timesheet Details</h3>
						
		<ul class="other-dets_m">
			<li>&nbsp;</li>
			<li>
			<table width="100%">
			<tr>
			<th><u><b>Date</b></u></th>
			<th><u><b>Project</b></u></th>
			<th><u><b>Hours</b></u></th>
			<th><u><b>Notes</b></u></th>
			<th><u><b>Status</b></u></th>
			<th>&nbsp;</th>
			</tr>
			<?php
			
			$other_project_results = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes");
			$other_project_array = array();
			foreach($other_project_results as $other)
			{
				array_push($other_project_array,$other->other_project_code_value);
			}
			
			$periodresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."time_periods where time_period_id=%d",$timeperiod1));
			
			$start = strtotime($periodresults[0]->start_date);
			$end = strtotime($periodresults[0]->end_date);							
			
			$timesheetresults = $wpdb->get_results($wpdb->prepare("select timesheet_id,timesheet_date,project_id,task_id,timesheet_hours,timesheet_notes,timesheet_status,
				abbreviated_name,project_name,gp_id
				from ".$wpdb->prefix."timesheets 
				left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
				where user_id=%d and timesheet_date<=%d and timesheet_date>=%d order by timesheet_date",$uid,$end,$start));
			
			if(!empty($timesheetresults))
			{
				foreach($timesheetresults as $time)
				{
					echo '<tr><th>'.date('m-d-Y',$time->timesheet_date).'
						<input type="hidden" value="'.$time->timesheet_id.'" name="record['.$time->timesheet_id.'][id]" /></th>';
					$task = $time->task_id;
					if($task != 0)
					{
						$task_results = $wpdb->get_results($wpdb->prepare("select task_name from ".$wpdb->prefix."tasks where task_id=%d",$task));
						$task_name = $task_results[0]->task_name;
					}
					else{$task_name = "";}
					if(!empty($time->abbreviated_name)){$project_number = $time->abbreviated_name;}
					elseif(!empty($time->project_name)){$project_number = $time->project_name;}
					elseif(!empty($time->gp_id)){$project_number = $time->gp_id;}
					else{$project_number = $time->project_id;}
					echo '<th>'.$project_number.(!empty($task_name) ? ": ".$task_name : "").'</th>';
					echo '<th>'.$time->timesheet_hours.'</th>';
					echo '<th>'.($uid==$current_user->ID ? 
						'<input type="text" name="record['.$time->timesheet_id.'][note]" value="'.$time->timesheet_notes.'"/>
						<input type="hidden" name="record['.$time->timesheet_id.'][orig]" value="'.$time->timesheet_notes.'"/>'
						: $time->timesheet_notes).'</th>';
					if($time->timesheet_status >0){echo '<th>Approved</th>';}else{echo '<th>Unapproved</th>';}
					if($deadline < $time->timesheet_date){echo '<th><a href="/?p_action=edit_timesheet&ID='.$time->timesheet_date.'" class="nice_link">Edit</a></th>';}
					else{echo '<th>&nbsp;</th>';}
					echo '</tr>';
				}
				echo '</table></li>';
				echo '<li>&nbsp;</li>';
				echo '<li><input type="submit" name="save-info" value="SAVE" class="my-buttons-submit" /></li>';
			}
			else{echo '</table></li><li>No Timesheet Information yet.</li>';}
			?>
			<li>&nbsp;</li>
		</ul>	
		</div>
		</div>
	</div>
	</form>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3>Daily Totals</h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
			<li>
			<table width="100%">
			<tr>
			<th><u><b>Date</b></u></th>
			<th style="text-align:center"><u><b>Daily Total Hours</b></u></th></tr>
			<th>&nbsp;</th>
			</tr>
			<?php
			$daily_results = $wpdb->get_results($wpdb->prepare("select distinct timesheet_date from ".$wpdb->prefix."timesheets 
				where user_id=%d and timesheet_date<=%d and timesheet_date>=%d order by timesheet_date",$uid,$end,$start));
			$total_total = 0;
			foreach($daily_results as $day)
			{
				$date = $day->timesheet_date;
				echo '<tr><td>'.date('m-d-Y',$date).'</td>';
				$total_hours_results = $wpdb->get_results($wpdb->prepare("select sum(timesheet_hours) as sum from ".$wpdb->prefix."timesheets 
					where user_id=%d and timesheet_date=%d",$uid,$date));
				$total = $total_hours_results[0]->sum;
				$total_total += $total;
				echo '<td style="text-align:center">'.number_format($total,2).'</td></tr>';
			}
			echo '<tr>&nbsp;</tr>';
			echo '<tr><td><b>Total</b></td><td style="text-align:center"><b>'.number_format($total_total,2).'</b></td></tr>';
			?>
			</table>
			</li>
				</ul>
			</li>
		</ul>
		</div>
	</div>
<?php } 
add_shortcode('my_time','billyB_my_time')
?>