<?php
function billyb_confirm_timeoff_export()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if($uid != 11 and $uid != 66){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}

	if(isset($_POST['export-info']) or isset($_POST['export-info-2']))
	{
		ob_end_clean();
		
		$filename = "timeoff".time().".csv";
		$output = @fopen('php://output', 'w');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv");
		fputcsv($output, array('co code','batch id','file #','HOURS 3 CODE','HOURS 3 AMOUNT'));
		
		$batches = $_POST['batch'];
		foreach($batches as $batch)
		{
			if($batch['box'] == "on")
			{
				$exported = $batch['batch_id'];
				$timeoff_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff 
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id 
					where exported=%d and request_code !='H'",$exported));

				foreach($timeoff_results as $employee)
				{							
					$user_id = $employee->user_id;
					$adp_id = $employee->adp_id;
					$hours = $employee->request_hours;
					$code = $employee->request_code;
					
					$csv = array('D97','1',$adp_id,$code,$hours);
					
					fputcsv($output,$csv);	
				}
			}
		}
		fclose($output);
		
		exit();
	}
	elseif(isset($_POST['confirm-upload']) or isset($_POST['confirm-upload-2']))
	{
		$batches = $_POST['batch'];
		
		foreach($batches as $batch)
		{
			$exported = $batch['batch_id'];
			if($batch['box'] == "on")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."request_timeoff set request_status=4 where exported=%d",$exported));
			}
		}
		echo '<div id="main_wrapper">
				<div id="main" class="wrapper">
					<div id="content">
						<div class="my_box3">
							<div class="padd10">
							Thank you, the selected requests are now confirmed as uploaded.
							</div>
						</div>
					</div>
				</div>
			</div>';
	}
	
	else{
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post" name="timeoff" enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						$timeoff_results = $wpdb->get_results("select request_id,employee_id,request_date,request_code,request_hours,adp_id,adp_department,display_name,exported 
							from ".$wpdb->prefix."request_timeoff 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
							where request_status=3
							order by exported");
						
						if(empty($timeoff_results)){echo 'There are no batches pending upload confirmation';}
						else
						{
						?>
							<li><input type="submit" name="confirm-upload" class="my-buttons" value="<?php echo "Confirm ADP Upload"; ?>" />
							<input type="submit" name="export-info" class="my-buttons" value="<?php echo "Export Again"; ?>" /></li>
							<li>&nbsp;</li>
							
							<?php
							$timeoff_results = $wpdb->get_results("select request_id,employee_id,request_date,request_code,request_hours,adp_id,adp_department,display_name,exported 
								from ".$wpdb->prefix."request_timeoff 
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."request_timeoff.employee_id=".$wpdb->prefix."useradd.user_id
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
								where request_status=3
								order by exported");
							
							$batch_array = array();
							
							foreach($timeoff_results as $employee)
							{
								if(!in_array($employee->exported,$batch_array))
								{
									if(!empty($batch_array)){echo '</table></li><li>&nbsp;</li>';}
									echo '<li><h3><strong><font color="blue">'.date('m-d-Y',$employee->exported).'</font></strong></h3>
										<p><input type="checkbox" name="batch['.$employee->exported.'][box]" />
											<input type="hidden" name="batch['.$employee->exported.'][batch_id]" value="'.$employee->exported.'" /></p></li>';
									array_push($batch_array,$employee->exported);
									echo '<li>&nbsp;</li>
											<li>
											<table width="100%">
											<tr>
											<th><strong><u>Name</u></strong></th>
											<th><strong><u>Date</u></strong></th>
											<th><strong><u>File #</u></strong></th>
											<th><strong><u>Dept</u></strong></th>
											<th><strong><u>Time Code</u></strong></th>
											<th><strong><u>Hours</u></strong></th>
											</tr>';
								}
								$request_id = $employee->request_id;
								$user_id = $employee->employee_id;
								$date = date('m-d',$employee->request_date);
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
							<li><input type="submit" name="confirm-upload-2" class="my-buttons" value="<?php echo "Confirm ADP Upload"; ?>" />
							<input type="submit" name="export-info-2" class="my-buttons" value="<?php echo "Export Again"; ?>" /></li>
						<?php
						}
						?>
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>
<?php }
}
add_shortcode('confirm_timeoff_export','billyB_confirm_timeoff_export') ?>