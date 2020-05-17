<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$now = time();
	
	$checklist = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."tasks where project_id=%d",$checklist));
	$pm = $wpdb->get_results($wpdb->prepare("select project_manager from ".$wpdb->prefix."projects where ID=%d",$checklist));
	$project_manager = $pm[0]->project_manager;
	
	$allowed_array = array(103,65,107,11,$project_manager,58);
	
	if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/?p_action=project_card&ID=".$checklist); exit;}
	
	$project_team_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users
		inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."users.ID=".$wpdb->prefix."project_user.user_id
		where project_id=%d
		order by display_name",$checklist);
	$project_team = $wpdb->get_results($project_team_query);
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$task_id = $record['id'];
			$task_name = $record['name'];
			$description = $record['description'];
			if($record['start'] == 0){$start=$now;}else{$start = strtotime($record['start']);}
			if($record['end'] == "perpetual"){$end = 0;} else{$end = strtotime($record['end']);}
			$responsible = $record['responsible'];
			if($record['time_entry'] == "on"){$time_entry=1;}else{$time_entry=0;}
			if($record['expense_entry'] == "on"){$expense_entry=1;}else{$expense_entry=0;}
			if($end !=0 and $end < $now){$status=1;}else{$status=0;}
			if($record['complete'] == "on"){$status=1;}
			
			if(!empty($task_name) and $task_id==0)
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."tasks (task_name,task_description,task_start,task_complete,project_id,user_id,time_entry,expense_entry,task_status)
					values(%s,%s,%d,%d,%d,%d,%d,%d,%d)",$task_name,$description,$start,$end,$checklist,$responsible,$time_entry,$expense_entry,$status));
			}
			if($task_id !=0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."tasks set task_name=%s, task_description=%s,task_start=%d,task_complete=%d,user_id=%d,time_entry=%d,expense_entry=%d,
					task_status=%d where task_id=%d",$task_name,$description,$start,$end,$responsible,$time_entry,$expense_entry,$status,$task_id));
			}
		}
	}
	$task_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."tasks where project_id=%d",$checklist));
	/*Tasks Project
		- Need to be able to add multiple rows of tasks, if PM
		- Need to be able to edit tasks, or complete them, if PM
		- Need to add tasks to projected time allocations
		- Need to add tasks to project reporting
		- Need to add to timesheets
		- May need to identify skills necessary to perform certain tasks
		- Tasks should be re-useable for standardization, maybe utilize a heirarchy
	*/
	?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<script type="text/javascript">
				var form_being_submitted = false;
				function checkForm(){
					var myForm = document.forms.task_mgmt;
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
		<form method="post" name="task_mgmt" enctype="multipart/form-data" onsubmit="checkForm();">
			<div id="content"><h3><?php echo "Task Details"; if(!empty($task_results)){echo '  <a href="#add_tasks">Add Responsibility</a>';}?></h3><br/>
				<div class="my_box3">
				<div class="padd10">	
				<ul class="other-dets_m">
					<?php
					if(!empty($task_results))
					{
						$v = 0;
						foreach($task_results as $task)
						{
							$t_id = $task->task_id;
							$name = $task->task_name;
							$description = $task->task_description;
							$party = $task->user_id;
							$t_start = $task->task_start;
							$t_end = $task->task_complete;
							$t_status = $task->task_status;
							$t_ee = $task->expense_entry;
							$t_te = $task->time_entry;
							
							echo '<li><h3>Name</h3><p><input type="text" class="do_input_new" name="record['.$v.'][name]" value="'.$name.'" /></p></li>';
							echo '<li><h3>Description</h3><p><textarea class="full_wdth_me do_input_new description_edit" name="record['.$v.'][description]" >'.$description.'</textarea></p></li>';
							echo '<li><h3>Start</h3>
								<p><input type="text" name="record['.$v.'][start]" value="'.date('m/d/Y',$t_start).'" class="do_input_new" id="start'.$t_id.'" /></p></li>';
							echo '<li><h3>End</h3>
								<p><input type="text" name="record['.$v.'][end]" value="'.($t_end==0 ? "Perpetual" : date('m/d/Y',$t_end)).'" class="do_input_new" id="end'.$t_id.'" /></p></li>';
							echo '<li><h3>Responsible Party</h3><p><select name="record['.$v.'][responsible]" class="do_input_new">
							<option value="">Select Party</option>';
							foreach($project_team as $pt)
							{
								echo '<option value="'.$pt->ID.'" '.($pt->ID==$party ? "selected='selected'" : "").'>'.$pt->display_name.'</option>';
							}
							echo '</select></p></li>';
							echo '<li><h3>Allow Time Entry</h3><p><input type="checkbox" name="record['.$v.'][time_entry]" '.($t_te==1 ? "checked='checked'" : "").' /></p></li>';
							echo '<li><h3>Allow Expense Entry</h3><p><input type="checkbox" name="record['.$v.'][expense_entry]" '.($t_ee==1 ? "checked='checked'" : "").' /></p></li>';
							echo '<li><h3>Mark Complete</h3><p><input type="checkbox" name="record['.$v.'][complete]" '.($t_status==1 ? "checked='checked'" : "").' /></p></li>';
							echo '<input type="hidden" name="record['.$v.'][id]" value="'.$t_id.'" />';
							echo '<li>&nbsp;</li>';
							?>
							<script>
								<?php $dd = 180; ?>
								var myDate=new Date();
								myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
			
								$(document).ready(function() {
									$('#start<?php echo $t_id;?>').datetimepicker({
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
							<script>
								<?php $dd = 180; ?>
								var myDate=new Date();
								myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
								
								$(document).ready(function() {
									$('#end<?php echo $t_id;?>').datetimepicker({
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
							$v++;
						}
						echo '<li>&nbsp;</li>';
						echo '<li><input type="submit" value="SAVE" name="save-info" class="my-buttons" /></li>';
					}
					else{echo '<li>No tasks have been created for this project yet</li>';}
					?>
				</ul>
			</div></div></div>
			<div id="content"><h3 id="add_tasks" ><?php echo "Add Tasks";?></h3><br/>
				<div class="my_box3">
				<div class="padd10">	
				<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
				<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
				<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
				<ul class="other-dets_m">
					<?php
					if(empty($task_results))
					{
						echo '<li><input type="submit" value="SAVE" name="save-info" class="my-buttons" /></li>';
						echo '<li>&nbsp;</li>';
					}
					$add_tasks = 5;
					$existing_tasks = count($task_results)+1;
					$time = time();
					
					for($i=0,$t=$time,$n=$existing_tasks;$i<$add_tasks,$t<($time + $add_tasks);$t++,$i++,$n++)
					{
						echo '<li><h3>Responsibility '.$n.'</h3></li>';
						echo '<li><h3>Name</h3><p><input type="text" name="record['.$n.'][name]" class="do_input_new" /></p></li>
							<li><h3>Description</h3><p><textarea class="full_wdth_me do_input_new description_edit" 
							name="record['.$n.'][description]"></textarea></p></li>';
						echo '<li><h3>Start</h3><p><input type="text" name="record['.$n.'][start]" id="start'.$t.'" class="do_input_new"</p></li>';
						echo '<li><h3>End</h3><p><input type="text" name="record['.$n.'][end]" id="end'.$t.'" class="do_input_new"</p></li>';
						echo '<li><h3>Responsible Party</h3><p><select name="record['.$n.'][responsible]" class="do_input_new">
							<option value="">Select Party</option>';
						foreach($project_team as $pt)
						{
							echo '<option value="'.$pt->ID.'">'.$pt->display_name.'</option>';
						}
						echo '</select></p></li>';
						echo '<li><h3>Allow Time Entry</h3><p><input type="checkbox" name="record['.$n.'][time_entry]" /></p></li>';
						echo '<li><h3>Allow Expense Entry</h3><p><input type="checkbox" name="record['.$n.'][expense_entry]" /></p></li>';
						echo '<li>&nbsp;</li>';
						?>
						<script>
							<?php $dd = 180; ?>
	
							var myDate=new Date();
							myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
		
							$(document).ready(function() {
								$('#start<?php echo $t;?>').datetimepicker({
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
						<script>
							<?php $dd = 180; ?>
							var myDate=new Date();
							myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
							
							$(document).ready(function() {
								$('#end<?php echo $t;?>').datetimepicker({
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
						echo '<hr>';
					}
					?>
				<li><input type="submit" value="SAVE" name="save-info-two" class="my-buttons" /></li>
				</ul>
			</div></div></div>
		</form>
</div></div>
<?php get_footer(); ?>