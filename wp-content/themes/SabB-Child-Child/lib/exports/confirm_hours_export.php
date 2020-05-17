<?php
function billyb_confirm_hours_export()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if($uid != 11 and $uid != 66 and $uid != 235){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$timesheet_id = $record['id'];
			$now = time();
			if($record['box'] == "on")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."timesheets set timesheet_status=3,exported=%d,exported_by=%d 
					where timesheet_id=%d",$now,$uid,$timesheet_id));
			}
		}
		echo '<div id="main_wrapper">
				<div id="main" class="wrapper">
					<div id="content">
						<div class="my_box3">
							<div class="padd10">
							Thank you, the selected hours are now confirmed as uploaded.
							</div>
						</div>
					</div>
				</div>
			</div>';
	}
	if(isset($_POST['export-info']))
	{
		ob_end_clean();
		$filename = "hours_".time().".csv";
		$output = @fopen('php://output', 'w');
		fputcsv($output, array('co code','batch id','file #','Temp Dept','Temp Rate','Reg Hours'));
		$records = $_POST['record'];

		foreach($records as $record)
		{
			if($record['box'] == "on")
			{
				$adp_id = $record['adp_id'];
				$department = $record['department'];
				$hours = $record['hours'];
				$wage = $record['wage'];
			
				$csv = array('D97','1',$adp_id,$department,$wage,$hours);
			
				fputcsv($output,$csv);
			}
		}
		fclose($output);
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: text/csv");
		exit();
	}
	else{
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post" name="hours" enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
										
						<ul class="other-dets_m">
							<li><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Confirm ADP Upload"; ?>" />
							<input type="submit" name="export-info" class="my-buttons" value="<?php echo "Export Again"; ?>" /></li>
							<li>&nbsp;</li>
							<li>
							<table width="100%">
							<tr>
							<th><?php echo "Name";?></th>
							<th><?php echo "Date";?></th>
							<th><?php echo "File #";?></th>
							<th><?php echo "Dept";?></th>
							<th>Rate</th>
							<th><?php echo "Hours";?></th>
							<th><a onclick="javascript:checkAll('hours',true);" href="javascript:void();">check all</a> / 
								<a onclick="javascript:checkAll('hours',false);" href="javascript:void();">uncheck all</a></th>
							</tr>
							<?php
							$hours_query = "select timesheet_id,".$wpdb->prefix."timesheets.user_id,display_name,timesheet_date,timesheet_hours,adp_id,adp_department,user_wage from ".$wpdb->prefix."timesheets 
								inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
								where timesheet_status=2 order by display_name";
							$hours_results = $wpdb->get_results($hours_query);
							
							foreach($hours_results as $employee)
							{							
								$timesheet_id = $employee->timesheet_id;
								$wage = $employee->user_wage;
								$user_id = $employee->user_id;
								$date = date('m-d',$employee->timesheet_date);
								$adp_id = $employee->adp_id;
								$department = $employee->adp_department;
								$hours = $employee->timesheet_hours;
								$display_name = $employee->display_name;
								
								echo '<tr><th>'.$display_name.'</th><th>'.$date.'</th><th>'.$adp_id.'</th><th>'.$department.'</th><th>'.ProjectTheme_get_show_price($wage).'</th>
									<th>'.$hours.'</th><th><input type="checkbox" name="record['.$timesheet_id.'][box]" />
									<input type="hidden" value="'.$timesheet_id.'" name="record['.$timesheet_id.'][id]" />
									<input type="hidden" value="'.$adp_id.'" name="record['.$timesheet_id.'][adp_id]" />
									<input type="hidden" value="'.$department.'" name="record['.$timesheet_id.'][department]" />
									<input type="hidden" value="'.$wage.'" name="record['.$timesheet_id.'][wage]" />
									<input type="hidden" value="'.$hours.'" name="record['.$timesheet_id.'][hours]" />
									</th></tr>';
							}
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
							function approveAll()
							{
								var myForm = document.forms.team-time;
								var myControls = myForm.elements['approve_all'];
								var checkboxes = new Array();
								checkboxes = myForm.getElementsByTagName('input');
								for (var i=0; i<checkboxes.length; i++)  {
								if (checkboxes[i].type == 'checkbox')   {
								  checkboxes[i].checked = checktoggle;
									}
								}							
							}
							</script>
							</table>
							</li>
							<li>&nbsp;</li>
							<li><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Confirm ADP Upload"; ?>" />
							<input type="submit" name="export-info" class="my-buttons" value="<?php echo "Export Again"; ?>" /></li>
							
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>
<?php }
}
add_shortcode('confirm_hours_export','billyB_confirm_hours_export') ?>