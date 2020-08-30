<?php
function billyb_timeoff_export()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$today = time();
	if($today <= strtotime(date('Y-m-15',$today))){$cutoff = strtotime(date('Y-m-15',$today));}else{$cutoff = strtotime(date('Y-m-t',$today));}
	
	if($uid!=235 and $uid!=11 and $uid!=66){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	if(isset($_POST['save-info']))
	{
		$timeoff_results = $wpdb->get_results($wpdb->prepare("select request_id from ".$wpdb->prefix."request_timeoff 
			where request_status=1 and request_date<=%d",$cutoff));
		
		foreach($timeoff_results as $employee)
		{
			$request_id = $employee->request_id;
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=3,exported=%d,exported_by=%d where 
				request_id=%d",$today,$uid,$request_id));
			$wpdb->query($updatestatusquery);
		}
		ob_end_clean();
		$filename = "timeoff".time().".csv";
		$output = @fopen('php://output', 'w');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv");
		fputcsv($output, array('co code','batch id','file #','HOURS 3 CODE','HOURS 3 AMOUNT'));
		
		$timeoff_results = $wpdb->get_results("select * from ".$wpdb->prefix."request_timeoff inner join ".$wpdb->prefix."useradd on 
			".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id where 
			".$wpdb->prefix."request_timeoff.request_status=3 and request_code !='H'");

		foreach($timeoff_results as $employee)
		{							
			$user_id = $employee->user_id;
			$adp_id = $employee->adp_id;
			$hours = $employee->request_hours;
			$code = $employee->request_code;
			
			$csv = array('D97','1',$adp_id,$code,$hours);
			
			fputcsv($output,$csv);	
		}
		fclose($output);
		
		exit();
	}
	else{
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">		
						<ul class="other-dets_m">
							<li><input type="submit" name="save-info" class="my-buttons" value="Download Export" />
							<a href="/confirm-time-off-export" class="my_buttons">Confirm Time Off Upload</a></li>
							<li>&nbsp;</li>
							<li>
							<table width="100%">
							<tr>
							<th>Name</th>
							<th>Date</th>
							<th>File #</th>
							<th>Dept</th>
							<th>Time Code</th>
							<th>Hours</th>
							</tr>
							<?php
							$timeoff_results = $wpdb->get_results($wpdb->prepare("select employee_id,request_date,request_code,request_hours,adp_id,adp_department,display_name 
								from ".$wpdb->prefix."request_timeoff 
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
								where request_status=1 and request_date<=%d",$cutoff));
							
							foreach($timeoff_results as $employee)
							{							
								$user_id = $employee->employee_id;
								$date = date('m-d-Y',$employee->request_date);
								$adp_id = $employee->adp_id;
								$department = $employee->adp_department;
								$code = $employee->request_code;
								$hours = $employee->request_hours;
								$display_name = $employee->display_name;
								
								echo '<tr><th>'.$display_name.'</th><th>'.$date.'</th><th>'.$adp_id.'</th><th>'.$department.'</th><th>'.$code.'</th>
									<th>'.$hours.'</th></tr>';
							}
							?>
							</table>
							</li>
							<li>&nbsp;</li>
							<li><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Download Export"; ?>" />
							<a href="/confirm-time-off-export" class="my_buttons"><?php echo "Confirm Time Off Upload";?></a></li>
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>
<?php }
}
add_shortcode('timeoff_export','billyB_timeoff_export') ?>