<?php
function billyB_approve_timeoff()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
	if(isset($_POST['save-info']))
	{
	?>
	<div id="content">
		<div class="my_box3">
			<div class="padd10">
			<?php	
			$records = ($_POST['record']);
			$now = time();
			
			foreach($records as $record)
			{
				if($record['box']== "approve")
				{
					$email = $record['email'];
					$request_id = $record['id'];
					$status = 1;
						
					$request_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where request_id=%d",$request_id));
					
					if($request_results[0]->request_status ==0)
					{
						$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=%d,date_approved=%d,approved_by=%d
							where request_id=%d",$status,$now,$uid,$request_id));
						
						$link = get_bloginfo('siteurl').'/request-time-off/';
						$mail = 'yes';
					}
					
					$request_type = $request_results[0]->request_type;
					$request_hours = $request_results[0]->request_hours;
					$request_date = $request_results[0]->request_date;
					
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=1,approved_by=%d,approved_date=%d 
						where project_id=%s and timesheet_hours=%f and timesheet_date=%d",$uid,$now,$request_type,$request_hours,$request_date));
				}
				if($record['box']== "reject")
				{
					$email = $record['email'];
					$request_id = $record['id'];
					$status = 2;
						
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=%d,date_approved=%d,approved_by=%d
						where request_id=%d",$status,$now,$uid,$request_id));
					
					$link = get_bloginfo('siteurl').'/request-time-off/';
					$mail = 'no';
					
					$request_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where request_id=%d",$request_id));
					$request_type = $request_results[0]->request_type;
					$request_hours = $request_results[0]->request_hours;
					$request_date = $request_results[0]->request_date;
					
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."timesheets where project_id=%s and timesheet_hours=%f	
						and timesheet_date=%d",$request_type,$request_hours,$request_date));
					$wpdb->query($timesheet_update);
				}
			}
			
			if($mail == 'yes'){wp_mail($email,"A Time Off Request has been approved","You can review your time off requests here:  ".$link);}
			if($mail == 'no'){wp_mail($email,"A Time Off Request has been rejected","You can review your time off requests here:  ".$link);}
			echo "The requests have been processed.<br/><br/>";
			?>
			<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
			</div>
		</div>
	</div>
	<?php 
		$_POST = array();
	}
	else{
	?>   
	<form method="post"  enctype="multipart/form-data">
	<div id="content">
		<div class="my_box3">
			<div class="padd10">
				<ul class="other-dets_m">
					<li>
						<table width="100%">
							<tr>
								<th><b><u><?php echo "Employee";?></u></b></th>
								<th><b><u><?php echo "Date";?></u></b></th>
								<th><b><u><?php echo "Request Type";?></u></b></th>
								<th><b><u><?php echo "Hours";?></u></b></th>
								<th><b><u><?php echo "Notes";?></u></b></th>
								<th><b><u><?php echo "Approve";?></u></b></th>
								<th><b><u><?php echo "Reject";?></u></b></th>
								<th><b><u><?php echo "Do Nothing";?></u></b></th>
							</tr>
							<tr>
							<?php
							$total_query = $wpdb->prepare("select employee_id,display_name,request_date,request_type,request_hours,notes,request_id,user_email 
								from ".$wpdb->prefix."request_timeoff inner join ".$wpdb->prefix."users 
								on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID inner join ".$wpdb->prefix."useradd
								on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
								where ".$wpdb->prefix."request_timeoff.request_status=0 and ".$wpdb->prefix."useradd.reports_to=%d",$uid);
							$total_results = $wpdb->get_results($total_query);
							foreach($total_results as $request)
							{
								$employee_name = $request->display_name;
								$employee_email = $request->user_email;
								
								echo '<tr>
									<th>'.$employee_name.'</th><th>'.date('m-d',$request->request_date).'</th><th>'.$request->request_type.'</th>
									<th>'.$request->request_hours.'</th><th>'.$request->notes.'</th><th>
									<input type="hidden" name="record['.$request->request_id.'][email]" value="'.$employee_email.'" />
									<input type="hidden" name="record['.$request->request_id.'][id]" value="'.$request->request_id.'" />
									<input type="radio" name="record['.$request->request_id.'][box]" value="approve"/></th>
									<th><input type="radio" name="record['.$request->request_id.'][box]" value="reject" /></th>
									<th><input type="radio" name="record['.$request->request_id.'][box]" value="nothing" /></th>
									</tr>';
							}
							echo '</table></li>';
							if(empty($total_results)){echo '<li>No pending requests for time off</li>';}	
							?>
					</li>
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Submit"; ?>" /></p></li>
				</ul>
			</div>
		</div>
	</div>
	</form>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Upcoming Time Off";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<?php
				$now = time();
				$upcoming_query = $wpdb->prepare("select request_date,display_name,request_type,request_hours,request_status from ".$wpdb->prefix."request_timeoff
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
					where request_date>%d and request_status !=2 and reports_to=%d order by request_date",$now,$uid);
				$upcoming_results = $wpdb->get_results($upcoming_query);
				
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

}
add_shortcode('approve_timeoff','billyB_approve_timeoff')
?>