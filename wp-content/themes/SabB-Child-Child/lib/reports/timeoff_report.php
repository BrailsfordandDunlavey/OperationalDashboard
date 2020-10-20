<?php
function billyb_timeoff_report()
{ 
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if(isset($_POST['export-info']))
	{
		ob_end_clean();
		$filename = "time_off_".time().".csv";
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		
		$output = @fopen('php://output', 'w');
							
		fputcsv($output, array('Name','Date','Date Requested','Type','Code','Hours','Department','Status'));
		
		$selected_month = $_POST['select_period'];
		$beg_month = strtotime(date('Y-m-01',$selected_month));
		
		$all_time_off_query = $wpdb->prepare("select request_date,request_type,request_code,request_hours,display_name,date_requested,request_status,adp_department 
			from ".$wpdb->prefix."request_timeoff
			inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID 
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
			where request_date<=%d and request_date>=%d",$selected_month,$beg_month);
		$all_time_off_results = $wpdb->get_results($all_time_off_query);
		
		foreach($all_time_off_results as $r)
		{
			if($r->request_status==0){$status = 'Unaddressed';}
			if($r->request_status==1){$status = 'Approved, not in ADP';}
			if($r->request_status>2){$status = 'Approved and in ADP';}
			
			$csv = array($r->display_name,date('m/d/Y',$r->request_date),date('m/d/Y',$r->date_requested),$r->request_type,$r->request_code,$r->request_hours,$r->adp_department,$status);
			
			fputcsv($output,$csv);
		}
		
		fclose($output);
		
		exit();
	}
 	$now = time();
	$current_month = date('Y-m-t',$now);
	$months_time = array(strtotime($current_month));
	$future_months = 6;
	$opdash_start = strtotime('1 October 2016');
	$numberOfMonths = abs((date('Y', $now) - date('Y', $opdash_start))*12 + (date('m', $now) - date('m', $opdash_start)))+1;
	
	for($i=1;$i<$numberOfMonths;$i++)
	{
		$month = strtotime(date('Y-m-t',strtotime(date("Y-m-01",strtotime($current_month)) . "-".$i." months")));
		array_push($months_time,$month);
	}
	
	
	for($i=1;$i<=$future_months;$i++)
	{
		$month = strtotime(date('Y-m-t',strtotime(date("Y-m-01",strtotime($current_month)) . "+".$i." months")));
		array_push($months_time,$month);
	}
	asort($months_time);
	
	$selected_month = strtotime(date('Y-m-t',strtotime(date("Y-m-01",strtotime($current_month)) . "-1 month")));
	
	if(isset($_POST['save-info']));
	{
		$records = $_POST['record'];
		$link = get_bloginfo('siteurl').'/request-time-off/';
		
		foreach($records as $record)
		{
			$request_id = $record['id'];
			$email = $record['email'];
			
			if($record['box'] == "on")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=1,date_approved=%d,
					approved_by=%d where request_id=%d",$now,$uid,$request_id));
				
				wp_mail($email,"Your Time Off Request has been approved","You can review your time off requests here:  ".$link);
			}
		}
		
	}
	if(isset($_POST['update-info']))
	{
		$selected_month = $_POST['select_period'];
	}
	?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
			<div id="content-full">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<li><select name="select_period" class="do_input_new">
				<?php
				foreach($months_time as $month_time)
				{
					echo '<option value="'.$month_time.'" '.($month_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month_time).'</option>';
				}
				?>
				</select>&nbsp;
				<input type="submit" name="update-info" value="Update Period" class="my-buttons" />
				<input type="submit" name="export-info" value="Export" class="my-buttons" />
				</li>
				</ul>
				</div>
				</div>
			</div>
			<div id="content-full"><h3>Unaddressed Time Off Requests</h3>
				<div class="my_box3">
				<div class="padd10">					
				<ul class="other-dets_m">
				<li>&nbsp;</li>
					<li>
					<table width="100%">
					<tr>
					<th><b><u><?php echo "Name";?></u></b></th>
					<th><b><u><?php echo "Date";?></u></b></th>
					<th><b><u><?php echo "Date Requested";?></u></b></th>
					<th><b><u><?php echo "Type";?></u></b></th>
					<th><b><u><?php echo "Code";?></u></b></th>
					<th><b><u><?php echo "Hours";?></u></b></th>
					<th><b><u><?php echo "Approve";?></u></b></th>
					</tr>
					<?php
					$overdue = strtotime(date('Y-m-d',$now).'- 6 days');
					$beg_month = strtotime(date('Y-m-01',$selected_month));
					$unaddressed_time_off_query = $wpdb->prepare("select user_email,request_id,request_date,request_type,request_code,request_hours,display_name,date_requested from ".$wpdb->prefix."request_timeoff
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID 
						where request_date<=%d and request_date>=%d and request_status =0
						order by display_name,request_date",$selected_month,$beg_month);
					$unaddressed_time_off_results = $wpdb->get_results($unaddressed_time_off_query);
					$save = 0;
					if(count($unaddressed_time_off_results)>0)
					{
						foreach($unaddressed_time_off_results as $unaddressed)
						{
							echo '<tr><th>'.$unaddressed->display_name.'</th>
								<th>'.date('m-d-Y',$unaddressed->request_date).'</th>
								<th>'.date('m-d-Y',$unaddressed->date_requested).'</th>
								<th>'.$unaddressed->request_type.'</th>
								<th>'.$unaddressed->request_code.'</th>
								<th>'.$unaddressed->request_hours.'</th>';
							if($unaddressed->date_requested < $overdue)
							{
								echo '<input type="hidden" value="'.$unaddressed->request_id.'" name="record['.$unaddressed->request_id.'][id]" />
									<input type="hidden" value="'.$unaddressed->user_email.'" name="record['.$unaddressed->request_id.'][email]" />
									<th><input type="checkbox" name="record['.$unaddressed->request_id.'][box]" /></th></tr>';
								$save = 1;
							}
							else{echo '<th>&nbsp;</th></tr>';}
						}
						echo '</table></li>';
						if($save == 1){echo '<input type="submit" name="save-info" value="Save" class="my-buttons-submit" /></li>';}
					}
					if(count($unaddressed_time_off_results)==0)
					{echo '</table>There are no unaddressed requests for this period</li>';}
					?>
				</ul>
				</div>
				</div>
			</div>
			<div id="content-full"><h3>Approved Time Off Requests (Awaiting Export)</h3>
				<div class="my_box3">
				<div class="padd10">					
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li>
					<table width="100%">
					<tr>
					<th><b><u><?php echo "Name";?></u></b></th>
					<th><b><u><?php echo "Date";?></u></b></th>
					<th><b><u><?php echo "Type";?></u></b></th>
					<th><b><u><?php echo "Code";?></u></b></th>
					<th><b><u><?php echo "Dept";?></u></b></th>
					<th><b><u><?php echo "Hours";?></u></b></th>
					</tr>
					<?php
					
					$time_off_query = $wpdb->prepare("select request_id,adp_department,request_date,request_type,request_code,request_hours,display_name from ".$wpdb->prefix."request_timeoff
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID 
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
						where request_date<=%d and request_date>=%d and request_status=1
						order by display_name,request_date",$selected_month,$beg_month);
					$time_off_results = $wpdb->get_results($time_off_query);
					
					foreach($time_off_results as $time)
					{
						echo '<tr><th>'.$time->display_name.'</th><th>'.date('m-d-Y',$time->request_date).'</th><th>'.$time->request_type.'</th>
							<th>'.$time->request_code.'</th><th>'.$time->adp_department.'</th><th>'.$time->request_hours.'</th></tr>';
					}
					?>
					</table>
					</li>
					<li>&nbsp;</li>
				</ul>
				</div>	
				</div>
			</div>
			<div id="content-full"><h3>Approved and Exported Time Off Requests</h3>
				<div class="my_box3">
				<div class="padd10">					
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li>
					<table width="100%">
					<tr>
					<th><b><u><?php echo "Name";?></u></b></th>
					<th><b><u><?php echo "Date";?></u></b></th>
					<th><b><u><?php echo "Type";?></u></b></th>
					<th><b><u><?php echo "Code";?></u></b></th>
					<th><b><u><?php echo "Dept";?></u></b></th>
					<th><b><u><?php echo "Hours";?></u></b></th>
					</tr>
					<?php
					$beg_month = strtotime(date('Y-m-01',$selected_month));
					$time_off_query = $wpdb->prepare("select request_id,adp_department,request_date,request_type,request_code,request_hours,display_name from ".$wpdb->prefix."request_timeoff
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."users.ID 
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
						where request_date<=%d and request_date>=%d and request_status >2
						order by display_name,request_date",$selected_month,$beg_month);
					$time_off_results = $wpdb->get_results($time_off_query);
					
					foreach($time_off_results as $time)
					{
						echo '<tr><th>'.$time->display_name.'</th>
							<th>'.date('m-d-Y',$time->request_date).'</th>
							<th>'.$time->request_type.'</th>
							<th>'.$time->request_code.'</th>
							<th>'.$time->adp_department.'</th>
							<th>'.$time->request_hours.'</th>
							</tr>';
					}
					?>
					</table>
					</li>
					<li>&nbsp;</li>
				</ul>
				</div>	
				</div>
			</div>
		</form>
		</div>
		</div>
	<?php 
}
add_shortcode('timeoff_report','billyB_timeoff_report') ?>