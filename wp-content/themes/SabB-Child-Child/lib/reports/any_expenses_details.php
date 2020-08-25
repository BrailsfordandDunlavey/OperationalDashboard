<?php
function billyB_any_expenses_details()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl').'/wp-login.php?redirect_to="any-expenses-report"'); exit; }
 
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11,94,235,293);//Bill B, Maresha, Tash, Peter
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $useradd_results[0]->sphere;
	if($current_user->ID==11){$sphere = "Sphere KMV";}
	$selected_users = array();
	$inactive_users = 1;
	
	if(isset($_POST['save-info']))
	{
		$users = $_POST['users'];
		$start = strtotime($_POST['start']);
		$end = strtotime($_POST['end']);
		if($_POST['include_inactive'] == "on"){$inactive_users=0;}else{$inactive_users=1;}//not being used
		
		foreach($users as $user)
		{
			array_push($selected_users,$user);
		}
	}
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
					<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
					<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
					<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
					<link rel="stylesheet" href="/resources/demos/style.css">
					<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						/*save for potential uses of the reports to
						$all_users = $wpdb->get_results($wpdb->prepare("select ID,display_name from ".$wpdb->prefix."users 
							where ID in (select user_id from ".$wpdb->prefix."useradd where reports_to=%d
								or reports_to in (select user_id from ".$wpdb->prefix."useradd where reports_to=%d)
								or reports_to in (select user_id from ".$wpdb->prefix."useradd where reports_to in	
									(select user_id from ".$wpdb->prefix."useradd where reports_to=%d))) 
							order by display_name",$uid,$uid,$uid));
							*/
						$all_users = $wpdb->get_results($wpdb->prepare("select ID,display_name from ".$wpdb->prefix."users
							where ID not in (1,2,29)
							order by display_name"));
						$size = count($all_users);if($size>20){$size=20;}
						if(empty($start)){$start=0;}
						if(empty($end)){$end=time();}
						echo '<li><h3>Select Employees:<br/>(Hold Ctrl to select multiple employees)</h3>
							<select name="users[]" multiple="multiple" size="'.$size.'">';
						foreach($all_users as $u)
						{
							echo '<option value="'.$u->ID.'" '.(in_array($u->ID,$selected_users) ? 'selected="selected"' : '').'>'.$u->display_name.'</option>';
						}
						echo '</select></li>';
						echo '<li><h3>Begin Date</h3><p><input type="text" id="start" class="do_input_new" value="'.date('m/d/Y',$start).'" name="start" /></p></li>';
						echo '<li><h3>End Date</h3><p><input type="text" id="end" class="do_input_new" value="'.date('m/d/Y',$end).'" name="end" /></p></li>';
						?>
						<script>
						<?php $dd = 180; ?>

						var myDate=new Date();
						myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
	
						$(document).ready(function() {
							$('#start').datepicker({
								showSecond: false,
								timeFormat: 'hh:mm:ss',
								currentText: '<?php _e('Now','ProjectTheme'); ?>',
								closeText: '<?php _e('Done','ProjectTheme'); ?>',
								ampm: false,
								dateFormat: 'mm/dd/yy',
								timeFormat: 'hh:mm tt',
								timeSuffix: '',
								maxDateTime: myDate,
								timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
								timeText: '<?php _e('Time','ProjectTheme'); ?>',
								hourText: '<?php _e('Hour','ProjectTheme'); ?>',
								minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
								secondText: '<?php _e('Second','ProjectTheme'); ?>',
								timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
							});
						});
						$(document).ready(function() {
							$('#end').datepicker({
								showSecond: false,
								timeFormat: 'hh:mm:ss',
								currentText: '<?php _e('Now','ProjectTheme'); ?>',
								closeText: '<?php _e('Done','ProjectTheme'); ?>',
								ampm: false,
								dateFormat: 'mm/dd/yy',
								timeFormat: 'hh:mm tt',
								timeSuffix: '',
								maxDateTime: myDate,
								timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
								timeText: '<?php _e('Time','ProjectTheme'); ?>',
								hourText: '<?php _e('Hour','ProjectTheme'); ?>',
								minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
								secondText: '<?php _e('Second','ProjectTheme'); ?>',
								timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
							});
						});
						</script>
						<?php
						echo '<li>&nbsp;</li>
							<li><h3>&nbsp;</h3><p><input type="submit" name="save-info" value="SAVE" class="my-buttons-submit" /></p></li>';
						?>
						</ul>
						</div>
						</div>
					</div>
				<?php
				if(!empty($selected_users))
				{
					$number_of_results = 0;
					?>
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<?php
						$all_project_array = array();
						$all_project_details = array();
						foreach($selected_users as $u)
						{
							$user_details = $wpdb->get_results($wpdb->prepare("select display_name,expense_date,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,project_id,expense_billable,
								gp_id,project_name,abbreviated_name,expense_code_name,expense_report_id
								from ".$wpdb->prefix."users
								inner join ".$wpdb->prefix."employee_expenses on ".$wpdb->prefix."users.ID=".$wpdb->prefix."employee_expenses.employee_id
								inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
								left join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
								where expense_date>=%d and expense_date<=%d and employee_id=%d order by project_id",$start,$end,$u));
							if(empty($user_details)){continue;}
							echo '<li><h3>'.$user_details[0]->display_name.'</h3></li>';
							echo '<li><table width="100%">
								<tr>
								<th><b><u>Date</u></b></th>
								<th><b><u>Project</u></b></th>
								<th><u><b>Expense</u></b></th>
								<th><u><b>Amount</u></b></th>
								<th><u><b>Billable/No-Bill</u></b></th>
								</tr>';
							$total_sum=0;
							
							$projects = array();
							$project_array = array();
							$project_amount_array = array();
							foreach($user_details as $ud)
							{
								if(!empty($ud->abbreviated_name)){$project = $ud->abbreviated_name." (".$ud->gp_id.")";}
								elseif(!empty($ud->project_name)){$project = $ud->project_name." (".$ud->gp_id.")";}
								elseif(!empty($ud->gp_id)){$project = $ud->gp_id;}
								else{$project = $ud->project_id;}
								echo '<tr id="p'.$ud->project_id.'">
									<td><a href="/?p_action=edit_employee_expense_admin&ID='.$ud->expense_report_id.'" target="_blank">'.date('m-d-Y',$ud->expense_date).'</a></td>
									<td>'.$project.'</td>
									<td>'.$ud->expense_code_name.'</td>
									<td>$'.number_format($ud->expense_amount*$ud->expense_quantity,2).'</td>
									
									<td>'.($ud->expense_billable==1 ? "Billable" : "No-Bill").'</td>
									</tr>';
								$total_sum += $ud->expense_amount*$ud->expense_quantity;
								$sum = $ud->expense_amount*$ud->expense_quantity;
								if(!in_array($project,$project_array))
								{
									$record = array($ud->project_id,$project);
									if(!in_array($project,$all_project_array))
									{
										array_push($all_project_array,$project);
										array_push($all_project_details,$record);
									}
									array_push($projects,$record);
									array_push($project_array,$project);
									array_push($project_amount_array,array($project,$sum));
								}
								else
								{
									for($i=0;$i<count($project_amount_array);$i++)
									{
										if($project_amount_array[$i][0] == $project)
										{
											$project_amount_array[$i][1] = $project_amount_array[$i][1] + $sum;
										}
									}
								}
								
							}
							if(!empty($user_details)){$number_of_results++;}
							echo '<tr id="ptotal"><td>&nbsp;</td></tr>';
							echo '<tr id="ptotal"><td>&nbsp;</td><td><b>Total</b></td><td><b>$'.number_format($total_sum,2).'</b></td></tr>';
							echo '<tr id="psummary"><td>&nbsp;</td></tr>';
							foreach($project_amount_array as $pha)
							{
								echo '<tr id="psummary"><td>&nbsp;</td><td><b>'.$pha[0].'</b></td><td><b>$'.number_format($pha[1],2).'</b></td></tr>';
							}
							echo '</table></li>';
						}
						if($number_of_results==0){echo 'No Results.';}
						echo '<li>&nbsp;</li>';
						?>
						<script language="javascript" type="text/javascript">
						function hideRows(){
							var x = document.getElementById('select_group').value;
							var allRows = document.querySelectorAll("[id^='p']");
							if(x != "all"){
								var showRows = document.querySelectorAll("[id*='p" + x + "']");
							}else{
								var showRows = allRows;
							}
							for(i=0;i<allRows.length;i++){
								allRows[i].style.display = 'none';
							}
							for(i=0;i<showRows.length;i++){
								showRows[i].style.display = 'table-row';
							}
						}
						</script>
						</ul>
						</div>
						</div>
					</div>
					<?php if($number_of_results!=0)
					{
						?>
						<div id="right-sidebar" class="page-sidebar"><div class="padd10">
							<ul class="xoxo">
								<li class="widget-container widget_text" id="ad-other-details">
								<ul class="other-dets other-dets2">
								<?php
								echo '<h3>Filter</h3>';
								echo '<p><select id="select_group" class="do_input_new" onchange="hideRows();">';
								echo '<option value="all">All</option>';
								foreach($all_project_details as $ps)
								{
									echo '<option value="'.$ps[0].'">'.$ps[1].'</option>';
								}
								echo '</select></p>';
								?>
								</ul>
								</li>
							</ul>
						</div></div>
						<?php
					}
				}
				?>
				</form>
			</div>
		</div>	
<?php  
}
add_shortcode('any_expenses_details','billyB_any_expenses_details') ?>