<?php
function billyB_my_team_time()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $useradd[0]->team;
	
	//run once
	/*
	$results = $wpdb->get_results("select user_id from ".$wpdb->prefix."useradd where status=1 
		and user_id not in (select user_id from ".$wpdb->prefix."timesheets where timesheet_date=1599177600)");
	foreach($results as $r)
	{
		$wpdb->query("insert into ".$wpdb->prefix."timesheets 
		(user_id,submitted_date,timesheet_date,project_id,task_id,timesheet_hours,timesheet_notes,timesheet_status,origination)
		values (".$r->user_id.",1599177600,1599177600,'Holiday',0,8,'Labor Day',1,'BillyB Query')");
	}
	*/
	//
	
	$timeoff_array = array('Vacation','Sick','Float','BEREAV','JURY','MATPAT','Holiday');
	
	if(isset($_POST['save-info']))
	{
		$timeperiod1 = $_POST['time_period'];
		$team_member_id = trim($_POST['team_member']);
		$team_member_add = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$team_member_id));
		$comp_type = $team_member_add[0]->user_comp_type;
		$reportsto = $team_member_add[0]->reports_to;
		$approved_date = time();
		
		$records = ($_POST['record']);
		
		foreach($records as $record)
		{
			$timesheet_id = $record['id'];
			$approved = $record['box'];
			
			$previousstatusresult = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."timesheets where timesheet_id=%d",$timesheet_id));
			$project = $previousstatusresult[0]->project_id;
			$hours = $previousstatusresult[0]->timesheet_hours;
			$date = $previousstatusresult[0]->timesheet_date;
			$employee = $previousstatusresult[0]->user_id;
			
			if($approved == "on" and $previousstatusresult[0]->timesheet_status ==0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=1,approved_by=%d,approved_date=%d 
					where timesheet_id=%d",$uid,$approved_date,$timesheet_id));
			}
			elseif($approved != "on" and $previousstatusresult[0]->timesheet_status >0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=0,approved_by=0,approved_date=0 
					where timesheet_id=%d",$timesheet_id));
			}
			
			if(in_array($project,$timeoff_array))
			{
				$time_off_request = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where request_type=%s and request_hours=%f
					and request_date=%d and employee_id=%d",$project,$hours,$date,$employee));
				$request_id = $time_off_request[0]->request_id;
				$request_status = $time_off_request[0]->request_status;
				if($approved == "on" and $request_status=0)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=1 where request_id=%d",$request_id));
				}
				elseif($approved!="on" and $request_status=1)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=0 where request_id=%d",$request_id));
				}
			}
		}
	}
	?>   

	<form method="post" name="team-time" enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<li><h3><?php echo "Time Period Start:";?></h3>
				<p><select class="do_input_new" name="time_period">
				<?php
				if(isset($_POST['set-time-period']))
				{
					$timeperiod1 = $_POST['time_period'];
					$team_member_id = trim($_POST['team_member']);
					$team_member_add = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$team_member_id));
					$comp_type = $team_member_add[0]->user_comp_type;
					$reportsto = $team_member_add[0]->reports_to;
				}
				if(isset($_POST['set-team-member']))
				{
					$team_member_id = trim($_POST['team_member']);
					$timeperiod1 = $_POST['time_period'];
					$team_member_add = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$team_member_id));
					$comp_type = $team_member_add[0]->user_comp_type;
					$reportsto = $team_member_add[0]->reports_to;
				}
				$now = time();
				$now_date = date('Y-m-d',$now);
				$periodsresult = $wpdb->get_results("select * from ".$wpdb->prefix."time_periods where start_date<'$now_date' order by start_date desc limit 24");
				
				foreach($periodsresult as $period)
				{
					if(empty($timeperiod1) and strtotime($period->start_date) < $now and strtotime($period->end_date) > $now){$timeperiod1=$period->time_period_id;}
					if(strtotime($period->start_date) < $now)
					{
						echo '<option value="'.$period->time_period_id.'" ';
						if(($period->time_period_id)==$timeperiod1){echo 'selected="selected" ';}
						echo '>'.date('m-d-Y',strtotime($period->start_date)).'</option>';
					}
				}
				?>
				</select>
				<input type="submit" name="set-time-period" class="my-buttons" value="<?php echo "Update Period"; ?>" /></p>
				</li>
			</ul>
			</div>
			<div class="padd10">
			<ul class="other-dets_m">
				<li><h3><?php echo "Team Member:";?></h3>
			<p><select class="do_input_new" name="team_member">
			<?php
				$reports_array = array();
				$reporttoquery = $wpdb->prepare("select ".$wpdb->prefix."useradd.user_id,display_name,reports_to from ".$wpdb->prefix."useradd 
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
					where (".$wpdb->prefix."useradd.reports_to=%d and ".$wpdb->prefix."useradd.status=1)
					order by ".$wpdb->prefix."users.display_name asc",$uid);
				if($uid == 103 or $uid==245 or $uid==94)//laura cosenzo and fola gbadamosi
				{
					$reporttoquery = $wpdb->prepare("select user_id,display_name,reports_to from ".$wpdb->prefix."useradd 
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
						where ".$wpdb->prefix."useradd.status=1 
							and (".$wpdb->prefix."useradd.sphere='Higher Ed' or ".$wpdb->prefix."useradd.sphere='Sphere KMV' or ".$wpdb->prefix."useradd.reports_to=%d)  order by display_name",$uid);
				}
				
				$reporttoresult = $wpdb->get_results($reporttoquery);
				foreach($reporttoresult as $rtr)
				{
					array_push($reports_array,array($rtr->user_id,$rtr->display_name,$rtr->reports_to));
				}
				$indirect_reports = $wpdb->get_results($wpdb->prepare("select employee,display_name,manager from ".$wpdb->prefix."indirect_reports
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."indirect_reports.employee=".$wpdb->prefix."users.ID
					where manager=%d and active=1",$uid));
				foreach($indirect_reports as $ir)
				{
					array_push($reports_array,array($ir->employee,$ir->display_name,$ir->manager,"indirect"));
				}
				if(empty($team_member_id))
				{
					$team_member_id = $reports_array[0][0];
					$team_member_add = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$team_member_id));
					$comp_type = $team_member_add[0]->user_comp_type;
					$reportsto = $team_member_add->reports_to;
				}
				
				foreach($reports_array as $team_member)
				{
					echo '<option '.($team_member[0]==$team_member_id ? 'selected="selected"' : '' ).' value="'.$team_member[0].'">'.$team_member[1].'</option>';
				}
				?>
				</select>
				<input type="submit" name="set-team-member" class="my-buttons" value="<?php echo "Update Team Member"; ?>" /></p>
				</li>
				
			</ul>	
			</div>
			</div>						
		</div>
	
	<div id="content">
		<div class="my_box3">
		<div class="padd10">
			
		<ul class="other-dets_m">
			<li>&nbsp;</li>
			<li><table width="100%">
			<?php 
			$periodresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."time_periods where time_period_id=%d",$timeperiod1));
			
			$start = strtotime($periodresults[0]->start_date);
			$end = strtotime($periodresults[0]->end_date);

			$timesheetresults = $wpdb->get_results($wpdb->prepare("select timesheet_date,project_id,timesheet_hours,timesheet_notes,timesheet_id,timesheet_status,
				gp_id,abbreviated_name 
				from ".$wpdb->prefix."timesheets 
				left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
				where user_id=%d and timesheet_date<=%d and timesheet_date>=%d order by timesheet_date",$team_member_id,$end,$start));
				
			if($comp_type!="hourly" and $uid!=50)//looking for salaried folks, but not Stacey Kennedy
			{
				$min_hours = 8;
			}
			else{$min_hours = 0;}
			
			$dates_array = array();
			
			if(!empty($timesheetresults))
			{
				if($uid != 103 and $uid !=245 and $uid!=94 and $reportsto==$current_user->ID)
				{
					echo '<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>';?>
						<a onclick="javascript:checkAll('team-time',true);" href="javascript:void();">check all</a> / 
						<a onclick="javascript:checkAll('team-time',false);" href="javascript:void();">uncheck all</a>
						<?php echo '</th><tr>';
				}
				echo '<tr>
					<th><b><u>Date</u></b></th>
					<th><b><u>Project</u></b></th>
					<th><u><b>Hours</b></u></th>
					<th><b><u>Notes</u></b></th>
					<th><b><u>'.((($uid != 103 and $uid !=245 and $uid!=94 and $uid==$reportsto) 
						or ($uid==103 and $reportsto==103) 
						or ($uid==94 and $reportsto==94))? "Approve" : "").'</u></b></th></tr>';
				$t = -1;
				$total = 0;
				$total_total = 0;
				foreach($timesheetresults as $time)
				{
					array_push($dates_array,$time->timesheet_date);
					if($time->timesheet_date != $timesheetresults[$t]->timesheet_date and $t!=-1)
					{
						if($total < $min_hours)
						{
							$beg_font = '<font color="red"><u>';
							$end_font = '</u></font>';
						}
						else
						{
							$beg_font ="";
							$end_font ="";
						}
						echo '<tr><td><b>Total</b></td><td>&nbsp;</td><td><b>'.$beg_font.$total.$end_font.'</b></td></tr>';
						$total = 0;
					}
					echo '<tr><th>'.date('D m-d-Y',$time->timesheet_date).'</th>';
					$projectid = $time->project_id;
					
					$project_number = $time->abbreviated_name;
					if(empty($project_number)){$project_number = $time->gp_id;}
					if(empty($project_number)){$project_number = $time->project_id;}
					
					echo '<th>'.$project_number.'</th>';
					echo '<th>'.$time->timesheet_hours.'</th>';
					echo '<th>'.$time->timesheet_notes.'</th>';
					echo '<input readonly type="hidden" name="record['.$time->timesheet_id.'][id]" value="'.$time->timesheet_id.'" />';
					echo '<th>';
					if(($uid != 103 and $uid!=245 and $uid!=94 and $reportsto==$current_user->ID) or ($uid==103 and $reportsto==103)or ($uid==94 and $reportsto==94))
					{
						echo '<input type="checkbox" name="record['.$time->timesheet_id.'][box]" ';
						if($time->timesheet_status>0){echo 'checked="checked" /></th>';}else{echo '/></th>';}
					}
					else{echo "</th>";}
					echo '</tr>';							
					
					$t++;
					$total += $time->timesheet_hours;
					$total_total += $time->timesheet_hours;
					
					if($total < $min_hours)
					{
						$beg_font = '<font color="red"><u>';
						$end_font = '</u></font>';
					}
					else
					{
						$beg_font ="";
						$end_font ="";
					}
					if($t == count($timesheetresults)-1)
					{
						echo '<tr><td><b>Total</b></td><td>&nbsp;</td><td><b>'.$beg_font.$total.$end_font.'</b></td></tr>';
					}
				}
				
				echo '</table></li><li>&nbsp;</li>';
				echo '<li><h3>Total for the Period:</h3><p>'.number_format($total_total,2).'</p></li>';
				//dates missing
				echo '<li><h3>Dates Missing:</h3><p>';
				
				$b=0;
				
				for($i=$start;$i<=$end;$i=$i+86400)
				{
					if(!in_array($i,$dates_array) and date('w',$i)!=0 and date('w',$i)!=6)
					{
						if($b>0){echo ", ";}
						if($i<time()-86400){echo '<font color="red">';}
						echo date('m-d-Y',$i);
						if($i<time()-86400){echo '</font>';}
						$b++;
					}
				}
				if($b==0){echo 'No Dates Missing';}
				echo '</p></li>';
			}
			else{echo '</table></li><li>No Timesheet Information yet.</li><li>&nbsp;</li>';}
			?>
			<script language="javascript" type="text/javascript">
				function checkAll(formname, checktoggle)
				{
				  var checkboxes = new Array(); 
				  checkboxes = document[formname].getElementsByTagName('input');
				 
				  for (var i=0; i<checkboxes.length; i++)  {
					if (checkboxes[i].type == 'checkbox')   {
					  checkboxes[i].checked = checktoggle;
					}
				  }
				}
			</script>
			<li><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></li>
		</ul>
		</div>
		</div>
	</div>
	</form>
	<?php //BillyB add code to have sidebar with upcoming time off requests (approved or pending approval) for the team members?>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Upcoming Time Off";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<?php
				$upcoming_results = $wpdb->get_results($wpdb->prepare("select request_date,display_name,request_type,request_hours,request_status 
					from ".$wpdb->prefix."request_timeoff
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
					where request_date>%d and request_status!=2 and reports_to=%d order by request_date",$now,$uid));
				
				foreach($upcoming_results as $ur)
				{
					echo '<li>'.date('m-d-Y',$ur->request_date).' '.$ur->display_name.' '.$ur->request_type.' '.$ur->request_hours.' hours '.
					($ur->request_status == 0 ? '<a href="/approve-time-off">(unapproved)</a>' : '').'</li>';
				}
				if(empty($upcoming_results)){echo '<li>Currently, there is no upcoming time off requests for your group.</li>';}
				?>
				</ul>
			</li>
		</ul>
	</div></div>
<?php } 
add_shortcode('my_team_time','billyB_my_team_time')
?>