<?php

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 	$period = strtotime(date('Y-m-t',$_GET['ID']));
	$project = $_GET['project'];
	
	$project_query = $wpdb->prepare("select client_name,project_name,project_manager from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
		where ID=%d",$project);
	$project_results = $wpdb->get_results($project_query);
	$project_name = $project_results[0]->project_name;
	$client_name = $project_results[0]->client_name;
	$project_manager = $project_results[0]->project_manager;
	
	$task_check_results = $wpdb->get_results($wpdb->prepare("select task_id from ".$wpdb->prefix."tasks 
		where project_id=%d limit 1",$project));
	
	function sitemile_filter_ttl($title){return ("Detailed Hours Report");}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	?>
	<div class="page_heading_me">
		<div class="page_heading_me_inner"> 
            <div class="mm_inn"><?php echo $client_name.":  ".$project_name; ?></div>                            
        </div>            
    </div>
	<div id="main_wrapper">
	<div id="main" class="wrapper">
	<?php
	if(isset($_POST['update_period'])){$period = $_POST['period'];}
	?> 
			<form method="post"  enctype="multipart/form-data">
			<?php
			$prev_months = 12;
			?>
				<div id="content_full"><h2><?php echo "Detailed Hours Report for ".date('F Y',$period);?></h2>
					<div class="my_box3">
						<div class="padd10">
							<ul class="other-dets_m">
							<?php
							$checker = 'task';
							
							if(isset($_POST['by_task'])){$checker = 'task';$period = $_POST['period'];}
							if(isset($_POST['by_employee'])){$checker = 'employee';$period = $_POST['period'];}
							//Removing paramenter for changing the time view to previous months so anyone can look back 2-11-19
							//if($uid == 11 or $uid==94 or $uid==65 or $uid==103 or $uid==$project_manager)
							//{
								echo '<li><select name="period" class="do_input_new">';
								for($i=0;$i<$prev_months;$i++)
								{
									$month = strtotime(date('Y-m-t',strtotime(date('Y-m-01',$period) . "-".$i." months")));
									echo '<option value="'.$month.'" '.($month==$period ? "selected='selected'" : "" ).'>'.date('F Y',$month).'</option>';
								}
								echo '</select><input type="submit" name="update_period" value="Update" /></li>';
							//}
							?>
							<li>&nbsp;</li>
							<?php 
							
							$beg_month = strtotime(date('Y-m-01',$period));
							
							if($checker == 'task')
							{
								$hours_query = $wpdb->prepare("select display_name,timesheet_date,timesheet_hours,timesheet_notes,task_id 
									from ".$wpdb->prefix."timesheets 
									inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
									where ".$wpdb->prefix."timesheets.project_id=%d and timesheet_date<=%d and timesheet_date>=%d
									order by task_id,timesheet_date,display_name",$project,$period,$beg_month);
							}
							if($checker == 'employee')
							{
								$hours_query = $wpdb->prepare("select display_name,timesheet_date,timesheet_hours,timesheet_notes,task_id 
									from ".$wpdb->prefix."timesheets 
									inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
									where ".$wpdb->prefix."timesheets.project_id=%d and timesheet_date<=%d and timesheet_date>=%d
									order by display_name,timesheet_date,task_id",$project,$period,$beg_month);
							}
							$hours_results = $wpdb->get_results($hours_query);
							
							foreach($hours_results as $time)
							{if($time->task_id !=0){$tasks_included = 'yes';}}
							
							if($tasks_included == 'yes')
							{
									echo '<li><input type="submit" name="by_task" '.($checker == 'task' ? 'class="my-buttons-submit"' : 'class="my-buttons"' ).'
										value="by task" />';
									echo '<input type="submit" name="by_employee" '.($checker == 'employee' ? 'class="my-buttons-submit"' : 'class="my-buttons"' ).'
										value="by employee" /></li>';
							}
							echo '<li><table width="100%">';
							
							echo '<tr>
								'.($tasks_included=='yes' ? '<th><b><u>Task</u></b></th>' : "" ).'
								<th><b><u>Name</u></b></th>
								<th style="width:70px;"><b><u>Date</u></b></th>
								<th><b><u>Hours</u></b></th>
								<th><b><u>Notes</u></b></th>
								</tr>';
								
							if($checker == 'task'){$starter = $hours_results[0]->task_id;}
							if($checker == 'employee'){$starter = $hours_results[0]->display_name;}
							
							$subtotal = 0;
							
							foreach($hours_results as $time)
							{
								if($time->task_id != 0)
								{
									$task_id = $time->task_id;
									$task_results = $wpdb->get_results($wpdb->prepare("select task_name from ".$wpdb->prefix."tasks 
										where task_id=%d",$task_id));
								}
								if($checker == 'task')
								{
									if($starter != $time->task_id or $m==count($hours_results))
									{
										//do subtotal
										echo '<tr><th>&nbsp;</th></tr>';
										echo '<tr>
											'.($tasks_included != 'yes' ? "" : '<th>&nbsp;</th>' ).'
											<th><strong>Total</strong></th>
											<th>&nbsp;</th>
											<th><strong>'.$subtotal.'</strong></th></tr>';
										echo '<tr><th>&nbsp;</th></tr>';
										
										$subtotal = 0;
										
										echo '<tr>
											'.($tasks_included !='yes' ? "" : ($time->task_id != 0 ? '<th>'.$task_results[0]->task_name.'</th>' : '<th>&nbsp</th>')).'
											<th>'.$time->display_name.'</th>
											<th>'.date('m-d-Y',$time->timesheet_date).'</th>
											<th>'.$time->timesheet_hours.'</th>
											<th>'.$time->timesheet_notes.'</th>
											</tr>';
										
										$starter = $time->task_id;
										$subtotal += $time->timesheet_hours;
									}
									else
									{
										echo '<tr>
											'.($tasks_included !='yes' ? "" : ($time->task_id != 0 ? '<th>'.$task_results[0]->task_name.'</th>' : '<th>&nbsp</th>')).'
											<th>'.$time->display_name.'</th>
											<th>'.date('m-d-Y',$time->timesheet_date).'</th>
											<th>'.$time->timesheet_hours.'</th>
											<th>'.$time->timesheet_notes.'</th>
											</tr>';
										$starter = $time->task_id;
										$subtotal += $time->timesheet_hours;
									}
								}
								if($checker == 'employee' or $m==count($hours_results))
								{
									if($starter != $time->display_name)
									{
										//do subtotal
										echo '<tr><th>&nbsp;</th></tr>';
										echo '<tr>
											'.($tasks_included != 'yes' ? "" : '<th>&nbsp;</th>' ).'
											<th><strong>Total</strong></th>
											<th>&nbsp;</th>
											<th><strong>'.$subtotal.'</strong></th></tr>';
										echo '<tr><th>&nbsp;</th></tr>';
										
										$subtotal = 0;
										
										echo '<tr>
											'.($tasks_included !='yes' ? "" : ($time->task_id != 0 ? '<th>'.$task_results[0]->task_name.'</th>' : '<th>&nbsp</th>')).'
											<th>'.$time->display_name.'</th>
											<th>'.date('m-d-Y',$time->timesheet_date).'</th>
											<th>'.$time->timesheet_hours.'</th>
											<th>'.$time->timesheet_notes.'</th>
											</tr>';
										
										$starter = $time->display_name;
										$subtotal += $time->timesheet_hours;
									}
									else
									{
										echo '<tr>
											'.($tasks_included !='yes' ? "" : ($time->task_id != 0 ? '<th>'.$task_results[0]->task_name.'</th>' : '<th>&nbsp</th>')).'
											<th>'.$time->display_name.'</th>
											<th>'.date('m-d-Y',$time->timesheet_date).'</th>
											<th>'.$time->timesheet_hours.'</th>
											<th>'.$time->timesheet_notes.'</th>
											</tr>';
											
										$starter = $time->display_name;
										$subtotal += $time->timesheet_hours;
									}
								}
							}
							echo '<tr><th>&nbsp;</th></tr>';
							echo '<tr>
								'.($tasks_included != 'yes' ? "" : '<th>&nbsp;</th>' ).'
								<th><strong>Total</strong></th>
								<th>&nbsp;</th>
								<th><strong>'.$subtotal.'</strong></th></tr>';
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
<?php get_footer(); ?>