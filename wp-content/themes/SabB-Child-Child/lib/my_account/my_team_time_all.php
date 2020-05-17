<?php
function billyB_my_team_time_all()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 						
	if(isset($_POST['approve_all']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=1
			where timesheet_status=0 and user_id in (		
				select user_id from ".$wpdb->prefix."useradd 
				where reports_to=%d)",$uid));
	}
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-top']))
	{
		$records = ($_POST['record']);
		$approved_date = time();
		
		foreach($records as $record)
		{
			$timesheet_id = $record['id'];
			
			$previousstatusresult = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."timesheets where timesheet_id=%d",$timesheet_id));
			$project = $previousstatusresult[0]->project_id;
			$hours = $previousstatusresult[0]->timesheet_hours;
			$date = $previousstatusresult[0]->timesheet_date;
			$employee = $previousstatusresult[0]->user_id;
			
			$approved = $record['box'];
			
			if($approved == "on" and $previousstatusresult[0]->timesheet_status != "on")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=1,approved_by='$uid',approved_date='$approved_date' 
					where timesheet_id=%d",$timesheet_id));
			}
			elseif($approved != "on" and $previousstatusresult[0]->timesheet_status >0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=0,approved_by=0,approved_date=0 
					where timesheet_id=%d",$timesheet_id));
			}
			$timeoff_array = array('Vacation','Sick','Float','BEREAV','JURY','MATPAT','Holiday');
			if(in_array($project,$timeoff_array))
			{
				$time_off_request = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where request_type=%s and request_hours=%f
					and request_date=%d and employee_id=%d",$project,$hours,$date,$employee));
				$request_id = $time_off_request[0]->request_id;
				if($approved == "on"){$status = 1;}else{$status=0;}
				//print_r($time_off_request);
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=%d where request_id=%d",$status,$request_id));
			}
		}	
	}
	?>
	<div id="content">
		<div class="my_box3">
		<div class="padd10">
			<form name="team-time" method="post"  enctype="multipart/form-data">
		<ul class="other-dets_m">
			<?php
			$timesheetquery = $wpdb->prepare("select display_name,timesheet_id,timesheet_date,project_id,timesheet_hours,timesheet_notes
				from ".$wpdb->prefix."timesheets 
				inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
				where reports_to=%d and timesheet_status=0 order by display_name,timesheet_date",$uid);
			$timesheetresults = $wpdb->get_results($timesheetquery);
			
			if(!empty($timesheetresults))
			{
				echo '<li>&nbsp;</li>
					<li><input type="submit" name="save-info-top" class="my-buttons" value="Save" /></li>';
				
				if($current_user->ID==11 or $current_user->ID==37)
				{
					echo '<li><input type="submit" name="approve_all" class="my-buttons" value="Fuck it - approve them all!" /></li>';
				}
			
				echo '<li><table width="100%">';
				
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th></th>&nbsp;<th>';?>
					<a onclick="javascript:checkAll('team-time',true);" href="javascript:void();">check all</a> / 
					<a onclick="javascript:checkAll('team-time',false);" href="javascript:void();">uncheck all</a>
					<?php echo '</th><tr>';
				
				echo '<tr><th><b><u>Employee</u></b></th><th><b><u>Date</u></b></th><th><b><u>Project</u></b></th><th><b><u>Hours</u></b></th><th><b><u>Notes</u></b></th>
					<th><b><u>Approve</u></b></th></tr>';
				foreach($timesheetresults as $time)
				{
					$project_id = $time->project_id;
					$project_query = "select gp_id,abbreviated_name from ".$wpdb->prefix."projects where ID='$project_id'";
					$project_results = $wpdb->get_results($project_query);
					$abb_name = $project_results[0]->abbreviated_name;
					$gp_id = $project_results[0]->gp_id;
					
					echo '<tr><th>'.$time->display_name.'</th>';
					echo '<th>'.date('D m-d-Y',$time->timesheet_date).'</th>';
					if(empty($project_results)){$project_number = $project_id;}else{if(empty($abb_name)){$project_number=$gp_id;}else{$project_number=$abb_name;}}
					echo '<th>'.$project_number.'</th>';
					echo '<th>'.$time->timesheet_hours.'</th>';
					echo '<th>'.$time->timesheet_notes.'</th>';
					echo '<input readonly type="hidden" name="record['.$time->timesheet_id.'][id]" value="'.$time->timesheet_id.'" />';
					echo '<th>';
					echo '<input type="checkbox" name="record['.$time->timesheet_id.'][box]" ';
					if($time->timesheet_status>0){echo 'checked="checked" /></th>';}else{echo '/></th>';}
					echo '</tr>';							
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
					<?php
				}
				
				echo '</table></li><li>&nbsp;</li>';	
				echo '<li><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>';
			}
			else
			{
				echo '</table></li><li>Currently, there are no timesheets pending your approval.</li><li>&nbsp;</li>';
				echo '<li><a href="'.get_bloginfo('siteurl').'/dashboard">Return to your dashboard</a></li>';
			}
			?>
			
		</ul>
			</form>						
		</div>
		</div>
	</div>
	<?php //BillyB add code to have sidebar with upcoming time off requests (approved or pending approval) for the team members?>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Upcoming Time Off";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<?php
				$now = time();
				$upcoming_query = "select request_date,display_name,request_type,request_hours,request_status from ".$wpdb->prefix."request_timeoff
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
					where request_date > '$now' and request_status !=2 and reports_to='$uid' order by request_date";
				$upcoming_results = $wpdb->get_results($upcoming_query);
				
				foreach($upcoming_results as $ur)
				{
					echo '<li>'.date('m-d-Y',$ur->request_date).' '.$ur->display_name.' '.$ur->request_type.' '.$ur->request_hours.' hours '.
					($ur->request_status == 0 ? '<a href="/approve-time-off">(unapproved)</a>' : '').'</li>';
				}
				if(empty($upcoming_results)){echo '<li>Currently, there is no upcoming time off scheduled for your group.</li>';}
				?>
				</ul>
			</li>
		</ul>
	</div></div>
<?php } 
add_shortcode('my_team_time_all','billyB_my_team_time_all')
?>