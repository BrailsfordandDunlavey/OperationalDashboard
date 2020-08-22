<?php

function ProjectTheme_my_account_area_main_function()
{	
	global $current_user, $wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$pm_results = $wpdb->get_results("select project_manager from ".$wpdb->prefix."projects");
	$pm_array = array();
	foreach($pm_results as $pm_r)
	{
		array_push($pm_array,$pm_r->project_manager);
	}
	
	if(isset($_POST['change_user'])){$uid = $_POST['select_user'];}

	$today = time();
	
	//Cron Job management
	$run_crons = array();
	$cron_job_query = "select * from ".$wpdb->prefix."cron_jobs";
	$cron_job_results = $wpdb->get_results($cron_job_query);
	foreach($cron_job_results as $cjr)
	{
		if($cjr->frequency == 1 and date('Y',$today) < date('Y',$cjr->cron_job_last_run))
		{array_push($run_crons,$cjr->cron_job_id);}
		if($cjr->frequency == 12 and strtotime(date('Y-m-01',$today)) < $cjr->cron_job_last_run)
		{array_push($run_crons,$cjr->cron_job_id);}
	}
	if(!empty($run_crons))
	{
		$current_dir = getcwd();
		$accrual_dir = $current_dir."/wp-content/themes/SabB-Child-Child/lib/";
		chdir($accrual_dir);
		include 'run_accruals.php';
	}
	//End Cron Management
	
	$time_period_query = "select * from ".$wpdb->prefix."time_periods";
	$time_periods = $wpdb->get_results($time_period_query);
	$first_open_period = strtotime(date('Y-m-d',$today).'- 2 months');
	foreach($time_periods as $time_period)
	{
		$period_id = $time_period->time_period_id;
		if(strtotime($time_period->start_date) >= $first_open_period and strtotime($time_period->start_date) <= $today)
		{
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."time_periods set active='on' where time_period_id=%d",$period_id));
		}
		else
		{
			//$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."time_periods set active='' where time_period_id=%d",$period_id)); //REMOVE comment to RESET LOCKED PERIODS!!!!!
		}
	}
	
	?>
		
		<div id="content" class="account-main-area">		
			<div class ="box_title"><b>My Activity</b></div>
				<div class="my_box3">
					<div class ="padd10">
						<div id="content" class="account-main-area">
						<div class="box_title"><div class="padd10"><?php echo "Welcome ".$current_user->display_name." to the Operational Dashboard!!!";?></div></div>	
						<?php require_once 'timesheetdataproject.php'; ?>	
						<?php require_once 'timesheetdatapro.php'; ?>			 
					</div>				
				</div>
		</div>
	</div>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10">

	<div class ="box_title"><b><?php echo "Notices";?></b></div>
	<div id="content" class="account-main-area">
		<div class="my_box3"><div class ="box_content">	
	
	<?php
	$today = time();
	$notices = 0;
	//notices for all
	$my_vendor_payables = $wpdb->get_results($wpdb->prepare("select vendor_payable_id from ".$wpdb->prefix."vendor_payables 
		where assigned_to=%d and expense_status=0",$uid));
	if(!empty($my_vendor_payables))
	{
		$notices++;
		echo '<p><a href="'.get_bloginfo('siteurl').'/my-vendor-payables/"><b>There are Vendor Payables assigned to you</b></a></p>';
	}
	//Notices for all managers************************************************************************************
	$report_to_results = $wpdb->get_results($wpdb->prepare("select user_id,display_name,user_comp_type from ".$wpdb->prefix."useradd 
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
		where reports_to=%d and ".$wpdb->prefix."useradd.status=1",$uid));
	if(!empty($report_to_results))
	{
		$sum = 0;
		$time_off_hours = 0;
		foreach($report_to_results as $employee)
		{
			$user_id = $employee->user_id;
			$pending_timeoff_query = $wpdb->prepare("select sum(request_hours) as sum from ".$wpdb->prefix."request_timeoff 
				where employee_id=%d and request_status=0",$user_id);
			$pending_timeoff_results = $wpdb->get_results($pending_timeoff_query);
			$sum += $pending_timeoff_results[0]->sum;
			$time_sheet_query = $wpdb->prepare("select timesheet_hours from ".$wpdb->prefix."timesheets 
				where user_id=%d and timesheet_status=0",$user_id);
			$time_sheet_results = $wpdb->get_results($time_sheet_query);
			$time_off_hours += $time_sheet_results[0]->timesheet_hours;
		}
		if($sum != 0)
		{
			echo '<p><a href="approve-time-off/" ><b>There are requests for time off that are awaiting your approval</b></a></p>';
			$notices++;
		}
		if($time_off_hours != 0)
		{
			echo '<p><a href="all-time-pending-approval/" ><b><font color="red">There are timesheets that are awaiting your approval</font></b></a></p>';
			$notices++;
		}
		
		$prev_month_start = strtotime(date('Y-m-01',time()). ' -1 month');
		$prev_month_end = strtotime(date('Y-m-t',$prev_month_start))+86399;
		
		foreach($report_to_results as $e)
		{
			$user_id = $e->user_id;
			$name = $e->display_name;
			if($user_id==50){$min_hours = 6;}else{$min_hours=8;}
			if($e->user_comp_type!='hourly')
			{
				$wh = $wpdb->get_results($wpdb->prepare("select timesheet_date,timesheet_hours from ".$wpdb->prefix."timesheets 
					where user_id=%d and timesheet_date>=%d and timesheet_date<=%d
					order by timesheet_date",$user_id,$prev_month_start,$prev_month_end));
				$incomplete = "no";
				if(!empty($wh))
				{
					$wha = array();
					$daily = array();
					
					for($i=0;$i<count($wh);$i++)
					{
						if(!in_array($wh[$i]->timesheet_date,$daily))
						{
							if($i>0){array_push($wha,array($wh[$i-1]->timesheet_date,$daily_hours));}
							$daily_hours = $wh[$i]->timesheet_hours;
							array_push($daily,$wh[$i]->timesheet_date);
							
						}
						else
						{
							$daily_hours += $wh[$i]->timesheet_hours;
						}
					}
					for($i=$prev_month_start;$i<=$prev_month_end;$i = $i+86400)
					{
						if(date('D',$i) != 'Sun' and date('D',$i) != 'Sat')
						{
							for($t=0;$t<count($wha);$t++)
							{
								if($wha[$t][0]==$i)
								{
									if($wha[$t][1] < $min_hours){$incomplete = "yes";}
									break 2;
								}
							}
						}
					}
					if($incomplete=="yes")
					{
						echo '<p>'.$name.' has incomplete time for the month of '.date('F Y',$prev_month_start).'</p>';
					}
				}
				else
				{
					echo '<p>'.$name.' has incomplete time for the month of '.date('F Y',$prev_month_start).'</p>';
				}
			}
		}
	}
	$pending_mastercard_report_to = $wpdb->get_results($wpdb->prepare("select distinct display_name from ".$wpdb->prefix."users
		inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
		inner join ".$wpdb->prefix."employee_expenses on ".$wpdb->prefix."users.ID=".$wpdb->prefix."employee_expenses.employee_id
		where reports_to=%d and ee_mastercard=1 and employee_expense_status=0",$uid));
	if(!empty($pending_mastercard_report_to))
	{
		echo "<strong>The following have unsubmitted MasterCard transactions:</strong><br/>";
		foreach($pending_mastercard_report_to as $p)
		{
			echo $p->display_name."<br/>";
		}
	}
	//Notices for payables that need accounting to process*********************************************************
	
	if($uid == 11 or $uid == 293 or $uid==94 or $uid==235)
	{
		$expense_approval_query = "select distinct expense_report_id,employee_expense_status from ".$wpdb->prefix."employee_expenses 
			where employee_expense_status=1 or employee_expense_status=2 or employee_expense_status=3";
		$expense_approval_results = $wpdb->get_results($expense_approval_query);
		foreach($expense_approval_results as $ear)
		{
			if($ear->employee_expense_status == 1){$awaiting_approval = 1;}
			elseif($ear->employee_expense_status == 2){$awaiting_export = 1;}
			else{$awaiting_confirmation = 1;}
		}
		/* Remove the check for vendor expenses for now since there's no way to confirm upload to GP
		$vendor_expenses = $wpdb->get_results("select expense_status from ".$wpdb->prefix."vendor_payables where expense_status>0 and expense_status<4");
		foreach($vendor_expenses as $ve)
		{
			if($ve->expense_status == 1){$awaiting_approval = 1;}
			elseif($ve->expense_status == 2){$awaiting_export = 1;}
			else{$awaiting_confirmation =1;}
		}
		*/
		if($awaiting_approval == 1){echo '<p><a href="expense-approvals/" ><b>There are submitted expenses awaiting approval</b></a></p>';$notices++;}
		if($awaiting_export == 1){echo '<p><a href="expense-export/" ><b>There are approved expenses awaiting export</b></a></p>';$notices++;}
		if($awaiting_confirmation == 1 and ($uid==11 or $uid==94)){echo '<p><a href="confirm-gp-upload/" ><b>There are exported expenses awaiting confirmation of upload to GP</b></a></p>';$notices++;}
	}
	
	//Notices for timekeeping that need accounting to process***************************************************************
	
	if($uid == 11 or $uid == 66 or $uid==235)
	{
		$time_off_query = "select request_status from ".$wpdb->prefix."request_timeoff where request_status=1 or request_status=3";
		$time_off_results = $wpdb->get_results($time_off_query);
		
		foreach($time_off_results as $tor)
		{
			if($tor->request_status == 1){$need_to_export = 1;}
			if($tor->request_status == 3){$need_to_confirm = 1;}
		}
		if($need_to_export == 1){echo '<p><a href="time-off-export/" ><b>There are approved time off requests that need to be exported</b></a></p>';$notices++;}
		if($need_to_confirm == 1){echo '<p><a href="confirm-time-off-export/" ><b>There are exported time off requests that need to be confirmed</b></a></p>';$notices++;}
	}
	
	if($uid == 74 or $uid==11 or $uid == 60 or $uid==221 or $uid==104)
	{
		$overdue = strtotime(date('Y-m-d',$today).'- 6 days');
		$time_off_query = $wpdb->prepare("select request_hours from ".$wpdb->prefix."request_timeoff 
			where request_status=0 and date_requested<%d",$overdue);
		$time_off_results = $wpdb->get_results($time_off_query);
		if(!empty($time_off_results)){echo '<p><a href="time-off-report/" ><b>There are <font color="red">overdue</font> time off requests that need to be approved</b></a></p>';$notices++;}
	}
	if($uid == 11 or $uid == 66 or $uid == 104 or $uid==60 or $uid==221 or $uid==235)
	{
		$hourly_payroll_query = "select timesheet_status from ".$wpdb->prefix."useradd 
			inner join ".$wpdb->prefix."timesheets on ".$wpdb->prefix."useradd.user_id= ".$wpdb->prefix."timesheets.user_id 
			where ".$wpdb->prefix."useradd.user_comp_type='Hourly' and (timesheet_status=1 or timesheet_status=2)";
		$hourly_payroll_results = $wpdb->get_results($hourly_payroll_query);
		
		foreach($hourly_payroll_results as $hpr)
		{
			if($hpr->timesheet_status == 1){$need_paid = 1;}
			if($hpr->timesheet_status == 2){$need_export = 1;}
		}
		if($need_paid == 1 and $uid !=104 and $uid!=221){echo '<p><a href="payroll-export/" ><b>There are approved hours that need to be paid</b></a></p>';$notices++;}
		if($need_export == 1 and $uid !=104 and $uid!=221){echo '<p><a href="confirm-adp-upload/" ><b>There are exported hours that need to be confirmed</b></a></p>';$notices++;}
	}
	
	//Notices for billing managers************************************************************************************
	
	if($uid == 1 or $uid==11 or $uid==94 or $uid==235)
	{
		$client_no_gp_id_query = "select ".$wpdb->prefix."clients.client_id from ".$wpdb->prefix."clients 
			inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."clients.client_id=".$wpdb->prefix."projects.client_id
			where client_gp_id='' and status in (1,2)";
		$client_no_gp_id_results = $wpdb->get_results($client_no_gp_id_query);
		if(count($client_no_gp_id_results) >0){echo '<p><a href="/clients-with-gp-id/"><b>There are clients that need to be assigned a GP Customer ID</b></a></p>';$notices++;}
	}
	if($uid == 1 or $uid==11 or $uid==94 or $uid==235)
	{
		$submitted_project_query = "select ID,client_name,project_name from ".$wpdb->prefix."projects 
			inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
			where status=1";
		$submitted_project_result = $wpdb->get_results($submitted_project_query);
		if(count($submitted_project_result) > 0)
		{
			echo '<p><b><u>There are projects awaiting processing</u></b></p>';$notices++;
			foreach($submitted_project_result as $project)
			{
				echo '<p><a href="/?p_action=edit_checklist&ID='.$project->ID.'">'.$project->client_name.' - '.$project->project_name.'</a></p>';
			}
		}
	}
	
	//-----------------------------------------------------------------------------------------------------------------
	if($notices == 0){echo "No pending notices at this time";}
	
	
	?>
     
	</div></div>
	</div> 

	<div class="my_box3">
		<div class ="box_content">	
			<div class ="box_title"><b>Team Member Activity</b></div>
			<?php //echo '<a href="/?p_action=employee_projected_hours&ID='.$uid,'" class="my-buttons-submit" style="color:#ffffff;">Team Member Activity</a>';?>
			<?php require_once 'accordion.php'; ?>
		</div>
	</div>

	<div class="my_box3"><div class ="box_content">	
	<!--<div id="right-sidebar" class="page-sidebar"><div class="padd10">--><div class ="box_title"><b>New Transactions</b></div><h3><?php //echo "New Transactions";?></h3>
	
	<ul class="xoxo">
		<li class="widget-container widget_text" id="ad-other-details">
			<ul class="other-dets other-dets2">	
			<li>Post a <a href="contract-checklist/"><b>New Contract Checklist</b></a></li>
			<li>Post a <a href="new-timesheet/"><b>New Timesheet</b></a></li>
			<li>Post a <a href="timesheet-matrix/"><b>Timesheet Matrix</b></a></li>
			<li>Post a <a href="new-employee-expense/"><b>New Expense Report</b></a></li>
			<?php
			$team_query = $wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid);
			$team_results = $wpdb->get_results($team_query);
			$team = $team_results[0]->team;
			$sphere_leaders = array(37,38,39,40,103,245,107,139,180,52,58,11,215,65);
			$all_projects = array(103,116,107,65,245,261,293,40);
			if($team == 'Finance' or in_array($uid,$all_projects)){echo '<li><b><a href="all-projects">All Projects</b></a></li>';}
			if($uid == 11 or $uid == 66 or $uid==235 or $team == 'Human Resources')
			{
				echo '<li><b><a href="new-employee">New Employee</b></a></li>';
				echo '<li><b><a href="any-hours-report/">Any Hours Report</b></a></li>';
				echo '<li><b><a href="time-off-report/">Time Off Report</a></b></li>';
			}
			$reports_array = array(37,38,103,245,107,39,40,107,65,58,139,293);
			if(in_array($uid,$reports_array) or $team == 'Finance' or $team=='Human Resources'){echo '<li><b><a href="reports/">Reports</a></b></li>';}
			if(in_array($uid,$pm_array) or in_array($uid,$sphere_leaders) or $uid==94 or $uid==235)
			{
				echo '<li><b><a href="'.get_bloginfo('siteurl').'/wuc-dashboard">WUC Dashboard</a></b></li>';
				echo '<li><b><a href="'.get_bloginfo('siteurl').'/capacity-analysis">Capacity Analysis</a></b></li>';
				echo '<li><b><a href="'.get_bloginfo("siteurl").'/staff-availability/">Staff Availability</a></b?></li>';
			}
			if($uid==11)
			{
				echo '<li><b><a href="'.get_bloginfo('siteurl').'/terminate-employee">Terminate Employee</a></b></li>';
			}
			if($uid==293 or $uid==11 or $uid==94 or $uid==235)
			{
				echo '<li><b><a href="'.get_bloginfo('siteurl').'/new-vendor">Setup New Vendor</a></b></li>';
			}
			?>
			</ul>
		</li>
	</ul>
	
	
	<div style="margin-left: -370px;">
		<?php //require_once 'timesheetdataprojectemps.html'; ?>
	</div>
	<div id="content" class="account-main-area">		
	<div class ="box_title"><b>My Activity</b></div>
	<div class="my_box3"><div class ="padd10" style="width: 140%;">
	<?php
	$this_month = strtotime(date('Y-m-01',$today));
	$last_month = strtotime(date('Y-m-01',$this_month). '-1 month');
	$two_months_ago = strtotime(date('Y-m-01',$last_month). '-1 month');
	$three_months_ago = strtotime(date('Y-m-01',$two_months_ago). '-1 month');
	
	$time_query = $wpdb->prepare("select timesheet_date,timesheet_hours,project_id from ".$wpdb->prefix."timesheets 
		where timesheet_date<%d and timesheet_date>=%d and user_id=%d
		order by timesheet_date",$this_month,$three_months_ago,$uid);
	$time_results = $wpdb->get_results($time_query);
	
	$three_months_ago_time = 0; $three_months_ago_available = 0; $three_months_ago_max = 0; $three_months_ago_projects = 0; $three_months_ago_billable = 0;
	$two_months_ago_time = 0; $two_months_ago_available = 0; $two_months_ago_max = 0; $two_months_ago_projects = 0; $two_months_ago_billable = 0;
	$last_month_time = 0; $last_month_available = 0; $last_month_max = 0; $last_month_projects = 0; $last_month_billable = 0;
	
	$projects_array_default = array('Sick','Holiday','Vacation','Bereav','Float','Jury','Mat/Pat');
	$other_project_codes_query = "select other_project_code_value from ".$wpdb->prefix."other_project_codes";
	$other_project_codes_results = $wpdb->get_results($other_project_codes_query);
	
	foreach($other_project_codes_results as $opcr)
	{
		array_push($projects_array_default,$opcr->other_project_code_value);
	}
	$projects_array = $projects_array_default;
	$t = -1;
	
	foreach($time_results as $tr)
	{
		if($tr->timesheet_date < $two_months_ago)
		{
			$three_months_ago_time += $tr->timesheet_hours;
			if(!in_array($tr->project_id,$projects_array)){$three_months_ago_projects++;array_push($projects_array,$tr->project_id);}
			if(!in_array($tr->project_id,$projects_array_default)){$three_months_ago_billable += $tr->timesheet_hours;}
		}
		elseif($tr->timesheet_date < $last_month)
		{
			if($time_results[$t]->timesheet_date < $two_months_ago){$projects_array = $projects_array_default;}
			$two_months_ago_time += $tr->timesheet_hours;
			if(!in_array($tr->project_id,$projects_array)){$two_months_ago_projects++;array_push($projects_array,$tr->project_id);}
			if(!in_array($tr->project_id,$projects_array_default)){$two_months_ago_billable += $tr->timesheet_hours;}
		}
		else
		{
			if($time_results[$t]->timesheet_date < $last_month){$projects_array = $projects_array_default;}
			$last_month_time += $tr->timesheet_hours;
			if(!in_array($tr->project_id,$projects_array)){$last_month_projects++;array_push($projects_array,$tr->project_id);}
			if(!in_array($tr->project_id,$projects_array_default)){$last_month_billable += $tr->timesheet_hours;}
		}
		$t++;
	}
	for($i=$three_months_ago;$i<$two_months_ago;$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$three_months_ago_available += 8;$three_months_ago_max += 10;}
	}
	for($i=$two_months_ago;$i<$last_month;$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$two_months_ago_available += 8; $two_months_ago_max += 10;}
	}
	for($i=$last_month;$i<$this_month;$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$last_month_available += 8; $last_month_max += 10;}
	}
	echo '<div><h3>Hours Worked</h3></div>';
	if($last_month_time < $last_month_available){$lm_font = "blue";}elseif($last_month_time > $last_month_max){$lm_font = "red";}else{$lm_font = "green";}
	if($two_months_ago_time < $two_months_ago_available){$two_font = "blue";}elseif($two_months_ago_time > $two_months_ago_max){$two_font = "red";}else{$two_font = "green";}
	if($three_months_ago_time < $three_months_ago_available){$three_font = "blue";}elseif($three_months_ago_time > $three_months_ago_max){$three_font = "red";}else{$three_font = "green";}
	echo '<div><div>'.date('F Y',$last_month).': <strong><font color="'.$lm_font.'">'.$last_month_time.'</font></strong> hours 
			(of '.$last_month_available.' hours) on <b>'.$last_month_projects.'</b> projects; 
			<b>'.round($last_month_billable/$last_month_time,2)*100 .'%</b> on projects</div>
		<div>'.date('F Y',$two_months_ago).': <strong><font color="'.$two_font.'">'.$two_months_ago_time.'</font></strong> hours 
			(of '.$two_months_ago_available.' hours) on <b>'.$two_months_ago_projects.'</b> projects;
			<b>'.round($two_months_ago_billable/$two_months_ago_time,2) *100 .'%</b> on projects</div>
		<div>'.date('F Y',$three_months_ago).': <strong><font color="'.$three_font.'">'.$three_months_ago_time.'</font></strong> hours 
			(of '.$three_months_ago_available.' hours) on <b>'.$three_months_ago_projects.'</b> projects;
			<b>'.round($three_months_ago_billable/$three_months_ago_time,2)*100 .'%</b> on projects</div></div>';
	
	if($current_user->ID == 11)
	{
		echo '<div>&nbsp;</div>';
		echo '<a href="/?p_action=detailed_employee_activity&ID='.$this_month.'&employee='.$uid,'" class="my-buttons-submit" style="color:#ffffff;">See Details</a>';
	}
	
	$this_month = strtotime(date('Y-m-01',$today));
	$next_month = strtotime(date('Y-m-01',$this_month). '+1 month');
	$two_months = strtotime(date('Y-m-01',$next_month). '+1 month');
	
	$projected_query = $wpdb->prepare("select projected_month,projected_hours,project_id from ".$wpdb->prefix."projected_time 
		where projected_month<=%d and projected_month>=%d and user_id=%d
		order by projected_month,project_id",$two_months,$this_month,$uid);
	$projected_results = $wpdb->get_results($projected_query);
	
	$this_month_projection = 0; $this_month_available = 0; $this_month_projects = 0;
	$next_month_projection = 0; $next_month_available = 0; $next_month_projects = 0;
	$two_months_projection = 0; $two_months_available = 0; $two_months_projects = 0;
	
	$t = -1;
	
	foreach($projected_results as $pr)
	{
		if($pr->projected_month < $next_month)
		{
			$this_month_projection += $pr->projected_hours;
			if($pr->project_id != $project_id){$this_month_projects++;}
			$project_id = $pr->project_id;
		}
		elseif($pr->projected_month < $two_months)
		{
			if($projected_results[$t]->projected_month < $next_month){$project_id = 0;}
			$next_month_projection += $pr->projected_hours;
			if($pr->project_id != $project_id){$next_month_projects++;}
			$project_id = $pr->project_id;
		}
		else
		{
			if($projected_results[$t]->projected_month < $two_months){$project_id = 0;}
			$two_months_projection += $pr->projected_hours;
			if($pr->project_id != $project_id){$two_months_projects++;}
			$project_id = $pr->project_id;
		}
		$t++;
	}
	for($i=$this_month;$i<$next_month;$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$this_month_available += 8;}
	}
	for($i=$next_month;$i<$two_months;$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$next_month_available += 8;}
	}
	for($i=$two_months;$i<=strtotime(date('Y-m-t',$two_months));$i = $i + 86400)
	{
		if(date('D',$i) != 'Sat' and date('D',$i) != 'Sun'){$two_months_available += 8;}
	}
	echo '<div><h3>Capacity Overview</h3></div>';
	echo '<div><div>'.date('F Y',$this_month).': <strong>'.round($this_month_projection/$this_month_available*100) .'% Committed</strong> 
			('.$this_month_projection.' of '.$this_month_available.' hours) on <b>'.$this_month_projects.'</b> projects</div>
		<div>'.date('F Y',$next_month).': <strong>'.round($next_month_projection/$next_month_available,2)*100 .'% Committed</strong> 
			('.$next_month_projection.' of '.$next_month_available.' hours) on <b>'.$next_month_projects.'</b> projects</div>
		<div>'.date('F Y',$two_months).': <strong>'.round($two_months_projection/$two_months_available,2)*100 .'% Committed</strong> 
			('.$two_months_projection.' of '.$two_months_available.' hours) on <b>'.$two_months_projects.'</b> projects</div></div>';
	echo '<div>&nbsp;</div>';
	echo '<a href="/?p_action=employee_projected_hours&ID='.$uid,'" class="my-buttons-submit" style="color:#ffffff;">See/Edit your projections</a>';
	?>
	</div></div></div>
	</div></div>
	</div></div>
	<?php
	if($current_user->ID == 11)
	{
		?>
		<form method="post"  enctype="multipart/form-data">
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Change User";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<?php
				$user_query = "select user_id,display_name from ".$wpdb->prefix."users
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where status=1
					order by display_name";
				$user_results = $wpdb->get_results($user_query);
				
				echo '<select name="select_user">';
				foreach($user_results as $ur)
				{
					echo '<option value="'.$ur->user_id.'" '.($uid==$ur->user_id ? 'selected="selected"' : '' ).'>
						'.$ur->display_name.'</option>';
				}
				echo '</select>';
				echo '<input type="submit" name="change_user" value="change" class="my-buttons-submit" />';
				?>	
				</ul>
			</li>
		</ul>
		</div></div>
		</form>
		<?php
	}
	?>
	<div id="content" class="account-main-area">		
	<div class ="box_title"><b><?php echo "My Projects";?></b></div>
	<div class="my_box3"><div class ="padd10">
	<?php
	$queryactive = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,client_name,".$wpdb->prefix."projects.gp_id,
		abbreviated_name,project_name,".$wpdb->prefix."clients.client_id
		from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status=2 and project_parent=0
		order by client_name",$uid);
	$resultactive = $wpdb->get_results($queryactive);
	
	echo "<h3>Active Projects</h3>";
	
	if(!empty($resultactive))
	{
		echo '<table width="100%">';
		echo '<tr><th><b><u>Client Name</u></b></th>
			<th><b><u>Project</u></b></th>
			<th><b><u>Project Number</u></b></th></tr>';
		foreach ($resultactive as $active)
		{
			if(empty($active->abbreviated_name)){$name = $active->project_name;}else{$name = $active->abbreviated_name;}
			echo '<tr>
				<th><a href="?p_action=farm_view&ID='.$active->client_id.'">'.$active->client_name.'</a></th>
				<th><a href="'.get_bloginfo('siteurl').'/?p_action=project_card&ID='.$active->ID.'">'.$name.'</a></th>
				<th>'.$active->gp_id.'</th>
				</tr>';
		}
		echo '</table>';
	}
	else
	{
		echo "You don't have any Active Projects";
	}	
	
	$querysubmitted = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,client_name,".$wpdb->prefix."projects.project_name,project_parent,abbreviated_name,
		project_name,".$wpdb->prefix."clients.client_id
		from ".$wpdb->prefix."projects 
		left join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
		left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where (".$wpdb->prefix."project_user.user_id=%d or ".$wpdb->prefix."projects.project_author=%d) and ".$wpdb->prefix."projects.status=1
		order by client_name",$uid,$uid);
	$resultsubmitted = $wpdb->get_results($querysubmitted);
	
	if(!empty($resultsubmitted))
	{
		echo '<br/><br>';
		echo "<h3>Projects awaiting processing</h3>";
		echo '<table width="100%">';
		echo '<tr><th><b><u>Client Name</u></b></th><th><b><u>Project Name</u></b></th></tr>';
		foreach ($resultsubmitted as $submitted)
		{
			if(empty($submitted->abbreviated_name))
			{
				if(empty($submitted->project_name)){$name="None";}else{$name = $submitted->project_name;}
			}
			else{$name = $submitted->abbreviated_name;}
			echo '<tr><th><a href="?p_action=farm_view&ID='.$submitted->client_id.'">'.$submitted->client_name.'</a></th>';
			echo '<th>'.$name.($submitted->project_parent != 0 ? " (adserv)" : "").'</th>';
			echo '</tr>';
		}
		echo '</table>';
	}
	
	$queryunsubmitted = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,client_name,".$wpdb->prefix."projects.project_name,project_parent,abbreviated_name,
		project_name,".$wpdb->prefix."clients.client_id
		from ".$wpdb->prefix."projects 
		left join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
		left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where (".$wpdb->prefix."project_user.user_id=%d or project_author=%d) and ".$wpdb->prefix."projects.status=0
		order by client_name",$uid,$uid);
	$resultunsubmitted = $wpdb->get_results($queryunsubmitted);
	
	if(!empty($resultunsubmitted))
	{
		echo '<br/><br/>';
		echo "<h3>Unsubmitted Checklists</h3>";
		echo '<table width="100%"><tr><th><b><u>Client Name</u></b></th><th><b><u>Project Name</u></b></th></tr>';
		
		foreach ($resultunsubmitted as $unsubmitted)
		{
			if(empty($unsubmitted->abbreviated_name))
			{
				if(empty($unsubmitted->project_name)){$name="None";}else{$name = $unsubmitted->project_name;}
			}
			else{$name = $unsubmitted->abbreviated_name;}
			echo '<tr><th><a href="?p_action=farm_view&ID='.$unsubmitted->client_id.'">'.$unsubmitted->client_name.'</a></th>';
			echo '<th>'.$name.($unsubmitted->project_parent != 0 ? " (adserv)" : "").'</th>';
			echo '<th><a href="/?p_action=edit_checklist&ID='.$unsubmitted->ID.'">Edit/Process Checklist</a></th></tr>';
		}
		echo '</table>';
	}
	
	$opportunities = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,client_name,".$wpdb->prefix."projects.project_name,project_parent,abbreviated_name,
		project_name,".$wpdb->prefix."clients.client_id
		from ".$wpdb->prefix."projects 
		left join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
		left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where (".$wpdb->prefix."project_user.user_id=%d or project_author=%d) and ".$wpdb->prefix."projects.status>3
		order by client_name",$uid,$uid);
	$opportunities_results = $wpdb->get_results($opportunities);
	
	if(!empty($opportunities_results))
	{
		echo '<br/><br/>';
		echo '<h3>Opportunities</h3>';
		echo '<table width="100%"><tr><th><b><u>Client Name</u></b></th><th><b><u>Project Name</u></b></th></tr>';
		
		foreach ($opportunities_results as $opp)
		{
			if(empty($opp->abbreviated_name)){$name = $opp->project_name;}else{$name = $opp->abbreviated_name;}
			echo '<tr><th><a href="?p_action=farm_view&ID='.$opp->client_id.'">'.$opp->client_name.'</a></th>';
			echo '<th>'.$name.($opp->project_parent != 0 ? " (adserv)" : "").'</th>';
			echo '<th><a href="/?p_action=edit_opportunity&ID='.$opp->ID.'">Edit Opportunity</a></th></tr>';
		}
		echo '</table>';
	}
	?>
</div></div></div>
		       

        
        <?php 
} 
?>