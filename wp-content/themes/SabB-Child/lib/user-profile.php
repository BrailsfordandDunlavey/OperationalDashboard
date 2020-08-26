<?php

	global $wpdb, $current_user;
	get_currentuserinfo();
	$cid = $current_user->ID;
	
	$employee_id = $_GET['ID'];
	
	$user_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."users where ID=%d",$employee_id));
	$email = $user_results[0]->user_email;
	$employee_name = $user_results[0]->display_name;
	
	function sitemile_filter_ttl($title){return __("User Profile",'ProjectTheme');}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$employee_id));
	$position_id = $useradd_results[0]->position;
	$sphere = $useradd_results[0]->sphere;
	$office_phone = $useradd_results[0]->office_phone;
	$cell_phone = $useradd_results[0]->cell_phone;
	
	if(isset($_POST['save-info']))
	{
		if(!empty($_POST['change_date']))
		{
			$date = strtotime($_POST['change_date']);
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set position=%d 
				where user_id=%d",$_POST['new_position'],$employee_id));
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_changes (user_id,change_type,change_date,changed_by,previous_value,effective_date)
				values(%d,'position',%d,%d,%d,%d)",$employee_id,time(),$current_user->ID,$_POST['old_position'],$date));
			$position_id = $_POST['new_position'];
		}
	}
	
	$position_results = $wpdb->get_results($wpdb->prepare("select position_title from ".$wpdb->prefix."position where ID=%d",$position_id));
	$position = $position_results[0]->position_title;
	if($sphere == "Higher Ed")
	{
		if($position == "Assistant Project Manager"){$position = "Senior Analyst";}
		elseif($position == "Project Manager"){$position = "Associate";}
		elseif($position == "Senior Project Manager"){$postion = "Senior Associate";}
		elseif($position == "Regional Vice President"){$position = "Director";}
	}
	
	$year = date('Y',time());
	$assumptions_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."position_assumptions where position_id=%d and year=%d",$position_id,$year));
	$planning_rate = $assumptions_results[0]->planning_rate;
	$implementation_rate = $assumptions_results[0]->implementation_rate;
	
get_header();
?>
    <div class="page_heading_me">
		<div class="page_heading_me_inner"> 
            <div class="mm_inn"><?php printf(__("User Profile - %s", 'ProjectTheme'), $employee_name); ?>   </div>                            
        </div>            
    </div>
<div id="main_wrapper">
	<div id="main" class="wrapper">
		<div id="content">
    		<div class="my_box3">
            <div class="padd10">
            	<div class="box_content">	
                <div class="user-profile-description">                     
                	<p>
						<ul class="other-dets_m">
							<?php
							echo '<li><h3>Email:</h3><p><a href="mailto:'.$email.'">'.$email.'</a></p></li>';
							echo '<li><h3>Office Phone:</h3><p>'.($office_phone != 0 ? '('.substr($office_phone, 0, 3).') '.substr($office_phone, 3, 3).'-'.substr($office_phone,6) 
								: 'Not Listed').'</p></li>';
							echo '<li><h3>Cell Phone:</h3><p>'.($cell_phone != 0 ? '('.substr($cell_phone, 0, 3).') '.substr($cell_phone, 3, 3).'-'.substr($cell_phone,6) 
								: 'Not Listed').'</p></li>';
							echo '<li>&nbsp;</li>';
							echo '<li><h3>Sphere:</h3><p>'.$sphere.'</p></li>';
							echo '<li><h3>Position:</h3><p>'.$position.'</p></li>';
							echo '<li><h3>Planning Rate:</h3><p>$'.number_format($planning_rate,2).'</p></li>';
							echo '<li><h3>Implementation Rate:</h3><p>$'.number_format($implementation_rate,2).'</p></li>';
							?>
			             </ul>
					</p>
                </div>
                </div>
            </div>
            </div>
		</div>
		<?php
		if($current_user->ID==11)
		{
			$employee_details = $wpdb->get_results($wpdb->prepare("select change_type,change_date,display_name,previous_value,effective_date
				from ".$wpdb->prefix."employee_changes
				left join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_changes.changed_by=".$wpdb->prefix."users.ID
				where ".$wpdb->prefix."employee_changes.user_id=%d
				order by change_date",$employee_id));
				
			$positions = $wpdb->get_results("select * from ".$wpdb->prefix."position order by rank");
			
			?>
			<form method="post" name="update_user" enctype="multipart/form-data" >
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
            	<div class="box_content">
				<div class="user-profile-description">
				<p>
				<ul class="other-dets_m">
				<?php
				foreach($employee_details as $detail)
				{
					if($detail->change_type=="reports_to"){$previous_value = $detail->display_name;}
					elseif($detail->change_type=="wage"){$pevious_value = '$'.number_format($detail->previous_value,2);}
					elseif($detail->change_type=="position")
					{
						foreach($positions as $p)
						{
							if($p->ID==$detail->previous_value){$previous_value = $p->position_title;}
						}
					}
					else{$previous_value = $detail->previous_value;}
					if($detail->change_type == "position"){$date = $detail->effective_date;}else{$date = $detail->change_date;}
					echo '<li><h3>'.$detail->change_type.'</h3><p>Was: '.$previous_value.' on '.date('m-d-Y',$date).'</p></li>';
				}
				?>
				<li>&nbsp;</li>
				<input type="hidden" name="old_position" value="<?php echo $position_id;?>" />
				<li><h3>New Position:</h3><p><select name="new_position" class="do_input_new">
					<p>
					<?php
					foreach($positions as $position)
					{
						echo '<option value="'.$position->ID.'" '.($position->ID==$position_id ? 'selected="selected"' : '').'>'.$position->position_title.'</option>';
					}
					?>
					</select>
					</p>
				</li>
				<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
					<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
					<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
					<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
					<link rel="stylesheet" href="/resources/demos/style.css">
					<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
					<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
				<li><h3>Effective Date:</h3><p><input type="text" id="start" class="do_input_new full_wdth_me" name="change_date"/></p></li>
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
				<li>&nbsp;</li>
				<li><h3></h3><p><input type="submit" name="save-info" value="save" class="my-buttons" /></p></li>
				</ul>
				</p>
				</div>
				</div>
				</div>
				</div>
			</div>
			</form>
			<?php
		}
		?>
	</div>
</div> 
<?php
	get_footer();
?>
