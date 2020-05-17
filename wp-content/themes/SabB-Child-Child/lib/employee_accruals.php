<?php
function billyB_employee_accruals()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$name = $current_user->display_name;
	
	$allowed_array = array(11,74,60);
	if(!in_array($current_user->ID,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	$now = time();
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$records = ($_POST['record']);
		
		foreach($records as $record)
		{
			$user_id = $record['user_id'];
			$hours = $record['vacation_accrual'];
			$date = strtotime($record['effective_date']);
			$orignial = $record['original_value'];
			$accrual_id = $record['accrual_id'];
			
			if(empty($accrual_id))
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_accruals (employee_id,vacation_hours,effective_date,edited_by,edited_date)
					values (%d,%f,%d,%d,%d)",$user_id,$hours,$date,$uid,$now));
			}
			if(!empty($accrual_id) and $hours != $original)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_accruals set vacation_hours=%f,effective_date=%d,edited_by=%d,edited_date=%d
					where employee_accrual_id=%d",$hours,$date,$uid,$now,$accrual_id));
			}
		}
	}		
		?>
		<script type="text/javascript">
			var form_being_submitted = false;
			var myForm = document.forms.employee_accruals;
			function checkForm(){
				var saveInfo = myForm.elements['save-info'];
				var saveInfoTwo = myForm.elements['save-info-two'];
				
				if(form_being_submitted){
				alert('The form is being submitted, please wait a moment...');
				saveInfo.disabled = true;
				saveInfoTwo.disabled = true;
				return false;
				}
				saveInfo.value = 'Saving form...';
				saveInfoTwo.value = 'Saving form...';
				form_being_submitted = true;
				return true;
			}
		</script>
		<form method="post"  enctype="multipart/form-data" name="employee_accruals" onsubmit="checkForm();">
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
					<li>&nbsp;</li>
					<li><h3>Employee</h3><p>Vacation Accrual : Effective Date</p></li>
					<li>&nbsp;</li>
					<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
					<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
					<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
					<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
					<link rel="stylesheet" href="/resources/demos/style.css">
					<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
					<?php 
					$employee_query = "select display_name,user_id,employee_accrual_id,vacation_hours,effective_date,team,sphere from ".$wpdb->prefix."useradd
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
						left join ".$wpdb->prefix."employee_accruals on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."employee_accruals.employee_id
						where status=1
						order by display_name";
					$employee_results = $wpdb->get_results($employee_query);
					foreach($employee_results as $er)
					{
						//BillyB add selected=selected fro vacation hours 
						$date = $er->effective_date;
						
						echo '<li><h3><input type="hidden" name="record['.$er->user_id.'][user_id]" value="'.$er->user_id.'" />'.$er->display_name.'</h3>';
						echo '<input type="hidden" name="record['.$er->user_id.'][accrual_id]" value="'.$er->employee_accrual_id.'" />';
						echo '<input type="hidden" name="record['.$er->user_id.'][original_value]" value="'.$er->vacation_hours.'" />';
						echo '<p><select name="record['.$er->user_id.'][vacation_accrual]">';
						echo '<option value="0">Select Vacation</option>';
						echo '<option value="40" '.($er->vacation_hours==40 ? 'selected="selected"' : '' ).'>One Week</option>';
						echo '<option value="80" '.($er->vacation_hours==80 ? 'selected="selected"' : '' ).'>Two Weeks</option>';
						echo '<option value="120" '.($er->vacation_hours==120 ? 'selected="selected"' : '' ).'>Three Weeks</option>';
						echo '<option value="160" '.($er->vacation_hours==160 ? 'selected="selected"' : '' ).'>Four Weeks</option>';
						
						echo '</select>';
						echo '<input type="text" id="start'.$er->user_id.'" name="record['.$er->user_id.'][effective_date]" 
							'.(!empty($date) ? 'value="'.date('m/d/Y',$date).'"' : 'placeholder="Effective Date"').' />';
						?>
						<script>
						<?php
						$dd = 180;
						?>
						var myDate=new Date();
						myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
	
						$(document).ready(function() {
							$('#start<?php echo $er->user_id;?>').datepicker({
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
						<?php
						echo '</p></li>';
					}
					?>
					<li><p><input type="submit" name="save-info-two" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
				</ul>
				</div>
				</div>
			</div>
		</form>
<?php 
}
add_shortcode('employee_accruals','billyB_employee_accruals')
?>