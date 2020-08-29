<?php
function billyb_terminate_employee()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$allowed_array = array(11);//Bill B
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	//run once importing holidays
	/*
	$full_date_array = array(1577836800,1579478400,1581897600,1590364800,1599436800,1606348800,1606435200,1608854400);
	$partial_date_array = array(1590105600,1593734400,1599177600,1606262400,1608768000,1609372800);
	
	$active_users_query = $wpdb->get_results($wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."users 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where ".$wpdb->prefix."useradd.status=1"));
	
	//$active_users_query = array(11);
	
	foreach($active_users_query as $auq)
	{
		foreach($full_date_array as $fda)
		{
			//echo $auq->user_id." full ".$fda;
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."timesheets (user_id,timesheet_date,project_id,timesheet_hours,timesheet_status)
				values(%d,%d,'Holiday',8,1)",$auq->user_id,$fda));
		}
		foreach($partial_date_array as $pda)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."timesheets (user_id,timesheet_date,project_id,timesheet_hours,timesheet_status)
				values(%d,%d,'Holiday',4,1)",$auq->user_id,$pda));
		}
	}
	*/
	if(isset($_POST['save-info']))
	{
		$generic_pass = md5(wp_generate_password( 12, false));
		$email = 'bbannister@programmanagers.com';
		$date = strtotime($_POST['date']);
		$user_id = $_POST['user'];
		
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set status=0,end_date=%d where user_id=%d",$date,$user_id));
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."users set user_email=%s,user_pass=%s where ID=%d",$email,$generic_pass,$user_id));
		
		$pm_check = $wpdb->get_results($wpdb->prepare("select display_name,".$wpdb->prefix."projects.ID,gp_project_number from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."users.ID=".$wpdb->prefix."projects.project_manager
			where project_manager=%d",$user_id));
		
		if(!empty($pm_check))
		{
			$to = array('bbannister@programmanagers.com','mmitchell@programmanagers.com','npereira@programmanagers.com');
			$headers = array('Content-Type: text/html; charset=UTF-8');
			$subject = "Need to assign a PM to projects";
			$message = $pm_check[0]->display_name.' has left B&D as of '.date('m-d-Y',$date).' and the following projects need a PM assigned to them:<br/><br/>';
			foreach($pm_check as $pc)
			{
				$message .= '<a href="'.get_bloginfo('siteurl').'/?p_action=edit_checklist&ID='.$pc->ID.'">'.$pc->gp_project_number.'</a><br/>';
			}
			wp_mail($to,$subject,$message,$headers);
		}
		
		//delete timesheet entries past termination date
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."timesheets where user_id=%d and timesheet_date>%d",$user_id,$date));
		
		$_POST = array();
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
			<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">
						Thank you.  The employee has been inactivated in the OpDash.
						</ul>
					</div>
				</div>
			</div>
			</div>
		</div>
		<?php
	}
	else
	{
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
						
						$query = $wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."users 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where ".$wpdb->prefix."useradd.status=1 order by display_name");
						$all_users = $wpdb->get_results($query);
						
						echo '<li><h3>Select Employee:</h3>
							<select name="user" class="do_input_new">';
						foreach($all_users as $u)
						{
							echo '<option value="'.$u->user_id.'" >'.$u->display_name.'</option>';
						}
						echo '</select></li>';
						echo '<li><h3>Termination Date</h3><p><input type="text" id="start" class="do_input_new" value="'.date('m/d/Y',time()).'" name="date" /></p></li>';
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
						</script>
						<?php
						echo '<li>&nbsp;</li>
							<li><h3>&nbsp;</h3><p><input type="submit" name="save-info" value="SAVE" class="my-buttons-submit" /></p></li>';
						?>
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>	
		<?php 
	}		
}
add_shortcode('terminate_employee','billyB_terminate_employee') ?>