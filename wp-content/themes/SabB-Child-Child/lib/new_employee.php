<?php
function billyB_new_employee()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_rights = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	if($user_rights[0]->team != 'Finance' and $user_rights[0]->team != 'Human Resources'){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
 
	if(isset($_POST['save-info']))
	{
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		$first_name = trim($_POST['first_name']);
		$last_name = trim($_POST['last_name']);
		$address = trim($_POST['address']);
		$city = trim($_POST['city']);
		$state = trim($_POST['user_location']);
		$zip = trim($_POST['zip']);
		$sphere = trim($_POST['sphere']);
		$department = trim($_POST['department']);
		$reportsto = trim($_POST['reports_to']);
		$position = trim($_POST['position']);
		$hire_date = strtotime($_POST['hire_date']);
		$rate = $_POST['rate']; if($rate == 0){$rate = 0;}
		$non_exmpet = $_POST['non_exempt'];
		$gp_id = $_POST['gp_id'];
		$adp_id = trim($_POST['adp_id']);
		$status = 1;
		if($rate > 0){$comp_type = 'Hourly';} elseif($non_exempt == 'on'){$comp_type='Non-exempt';}else{$comp_type='';}
		
		$team_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$reportsto));
		$team = $team_results[0]->team;
		
		$login_name = strtolower(substr($first_name,0,1).$last_name);
		
		$email = $login_name."@bdconnect.com";
		$display_name = $first_name." ".$last_name;
		
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."users (user_login,user_nicename,user_email,display_name) 
			values(%s,%s,%s,%s)",$login_name,$login_name,$email,$display_name));
		
		$user_id = $wpdb->insert_id;
		
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."useradd (user_id,gp_id,adp_id,adp_department,sphere,team,reports_to,position,status,start_date,user_wage,user_comp_type) 
			values (%d,%s,%s,%s,%s,%s,%d,%d,%d,%d,%f,%s)",$user_id,$gp_id,$adp_id,$department,$sphere,$team,$reportsto,$position,$status,$hire_date,$rate,$comp_type));
		
		update_user_meta($user_id,'user_address',$address);
		update_user_meta($user_id,'user_city',$city);
		update_user_meta($user_id,'user_location',$state);
		update_user_meta($user_id,'user_zip',$zip);
		update_user_meta($user_id,'user_tp','service_provider');
		
		$position_results = $wpdb->get_results($wpdb->prepare("select position_title from ".$wpdb->prefix."position where ID=%d",$position));
		$position_title = $position_results[0]->position_title;
		
		$message = "Hello Bill,<br/><br/>".$display_name." was just setup on the OpDash.<br/><br/>
			GP ID:  ".$gp_id."
			ADP ID: ".$adp_id."
			Sphere:  ".$sphere."
			Team:  ".$team."
			Position ".$position_title."
			Hire Date:  ".date('m-d-Y',$hire_date)."
			<br/><br/>Thank you.";
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail('bbannister@programmanagers.com','New Employee Created on OpDash',$message,$headers);
		
		echo "Thank you.  The employee record has been saved.<br/><br/>";
		?>
		<a href="<?php bloginfo('siteurl');?>/new-employee/">Setup a new employee</a><br/><br/>
								
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div>
		<?php 
	}
	else
	{
		?>
		<form method="post" enctype="multipart/form-data">
			<div id="content"><h3>Setup New Employee</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li><h3>First Name:</h3><p><input class="do_input_new full_wdth_me" name="first_name"/></p></li>
					<li><h3>Last Name:</h3><p><input class="do_input_new full_wdth_me" name="last_name"/></p></li>
					<li><h3>Address:</h3><p><input class="do_input_new full_wdth_me" name="address"/></p></li>
					<li><h3>City:</h3><p><input class="do_input_new full_wdth_me" name="city"/></p></li>
					<li><h3>State:</h3>        	
						<p><select class="do_input_new" name="user_location"><option value=""><?php echo "Select State";?></option>
						<?php	
						$stateresults = $wpdb->get_results("select state_id, state_name from ".$wpdb->prefix."states order by state_name");
						foreach ($stateresults as $state)
						{
							echo '<option value="'.$state->state_id.'">'.$state->state_name.'</option>';
						}
						?>
						</select>
						</p>
					</li>
					<li><h3>Zip</h3><p><input class="do_input_new full_wdth_me" name="zip"></p></li>
					<li><h3>Sphere</h3>
						<p><select class="do_input_new" name="sphere"/>
						<option value="">Select Sphere</option>
						<option>Higher Ed</option>
						<option>Sphere KMV</option>
						<option>Functional</option>
						</select></p>
					</li>
					<li><h3>GP ID:</h3><p><input class="do_input_new" name="gp_id" placeholder="00XXX"/></p></li>
					<li><h3>ADP ID:</h3><p><input class="do_input_new" name="adp_id" placeholder="100XXX"/></p></li>
					<li><h3>ADP Department:</h3>
						<p><select class="do_input_new" name="department"/>
						<option value="">Select Department</option>
						<option>B&D - Accounting</option>
						<option>B&D - Administration</option>
						<option>B&D - Human Resources</option>
						<option>B&D - Information Technology</option>
						<option>B&D - Marketing</option>
						<option>B&D - Research & Methods</option>
						<option>B&D - Sphere Higher Ed</option>
						<option>B&D - Sphere Will</option>
						<option>B&D - Team Foodservice</option>
						</select></p>
					</li>
					<li><h3>Reports to:</h3>
						<p><select class="do_input_new" name="reports_to">
						<option value="">Select Employee</option>
						<?php
							$reporttoresults = $wpdb->get_results("select ID,display_name from ".$wpdb->prefix."users 
								where ID>1 and display_name !='test' and display_name!='admin' order by display_name");
							foreach ($reporttoresults as $reportto)
							{echo '<option value="'.$reportto->ID.'">'.$reportto->display_name.'</option>';}
						?>
						</select></p>
					</li>
					<li><h3>Position Level:</h3>
						<p><select class="do_input_new" name="position">
						<option value="">Select Level</option>
						<?php
							$positionresults = $wpdb->get_results("select * from ".$wpdb->prefix."position where position_title !='Executive' order by position_title");
							foreach ($positionresults as $position)
							{echo '<option value="'.$position->ID.'">'.$position->position_title.'</option>';}
							?>
						</select></p>
					</li>
						<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
						<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
						<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>	
					<li><h3>Hourly Rate (only for hourly employees)</h3><p><input type="number" step=".01" name="rate" class="do_input_new" /></p></li>
					<li><h3>Salaried: Non Exempt</h3><p><input type="checkbox" name="non_exempt"/></p></li>
					<li><h3>Start Date:</h3><p><input type="text" id="start" class="do_input_new full_wdth_me" name="hire_date"/></p></li>
						<script>
						<?php
						$now = time();
						$start = date_i18n('m-d-Y',$yes);
						$dd = 90;
						?>
						var myDate=new Date();
						myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);

						$(document).ready(function() {
							$('#start').datetimepicker({
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
						});});
						</script>
					<li><h3>B&D Credit Card"</h3><input type="checkbox" name="credit_card"/>check if the employee is to receive a B&D credit card</li>
				</ul>
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="Submit Employee" /></p></li>
				</ul>
				</div>
				</div>
			</div>
		</form>
<?php } 

}
add_shortcode('new_employee','billyB_new_employee')
?>