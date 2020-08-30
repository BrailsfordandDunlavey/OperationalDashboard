<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	
	$checklist = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare("select ".$wpdb->prefix."projects.ID,project_name,client_name,abbreviated_name,gp_project_number,estimated_start 
		from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where ".$wpdb->prefix."projects.ID=%d",$checklist));
	$start = $details[0]->estimated_start;
	$client_name = $details[0]->client_name;
	if(!empty($details[0]->abbreviated_name)){$project_name = $details[0]->abbreviated_name;}
	elseif(!empty($details[0]->project_name)){$project_name = $details[0]->project_name;}
	else{$project_name = $details[0]->gp_project_number;}
	
	$tasks_results = $wpdb->get_results($wpdb->prepare("select task_id,task_name,task_description from ".$wpdb->prefix."tasks 
		where project_id=%d order by task_start,task_id",$checklist));
	
	$project_team = array();
	$resultsteam = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."project_user where project_id=%d",$checklist));
	foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
	
	get_header();
	
	if(isset($_POST['save-info']))
	{
		$task_array = array();
		$tasks = $_POST['task'];//manage tasks
		foreach($tasks as $t)
		{
			$task_id = $t['id'];
			$task_name = $t['task_name'];
			if(empty($task_name)){$task_name = "No name yet";}
			$task_desc = $t['task_desc'];
			if(empty($task_desc)){$task_desc = "No description yet";}
			$task_hours = $_POST['task_hours'];
			
			if($task_id == 0 and $task_hours != 0)
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."tasks (task_name,task_description,task_start,project_id) values (%s,%s,%d,%d)",
					$task_name,	$task_desc,$start,$checklist));
				array_push($task_array,$wpdb->insert_id);
			}
			elseif($task_hours != 0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."tasks set task_name=%s,task_description=%s,task_start=%d,project_id=%d where task_id=%d",
					$task_name,$task_desc,$start,$checklist,$task_id));
				array_push($task_array,$task_id);
			}
		}
		$info = $_POST['info'];//manage projected time
		$t = 0;
		$month = strtotime(date('Y-m-01',$start));
		foreach($info as $d)
		{
			$dd = explode(",,,",$d['info']);
			$user_id = $dd[0];
			$task = $dd[1];
			$hours = $dd[2];
			
			if(empty($task) and $hours != 0)//insert projected time with all hours in the first month - presuming this will be split later
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_time (user_id,project_id,task_id,projected_month,projected_hours) 
					values (%d,%d,%d,%d,%d)",$user_id,$checklist,$task_array[$t],$month,$hours));
				$t++;
			}
			elseif($hours != 0)//update projected hours, adjust hours for the first month
			{
				$r = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projected_time where task_id=%d and user_id=%d order by projected_month",$task,$user_id));
				$total_projected = 0;
				foreach($r as $row)
				{
					$total_projected += $row->projected_hours;
				}
				$adjustment = $total_projected - $hours;
				$first_month_adjustment = $r[0]->projected_hours + $adjustment;
				$projected_id = $r[0]->projected_time_id;
				
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projected_time set projected_hours=%f where projected_time_id=%d",
					$first_month_adjustment,$projected_id));
			}
		}
	}
 ?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="new_scope" enctype="multipart/form-data">
			<div id="content-full"><h3><?php echo "Scope for:  ".$client_name." - ".$project_name;?></h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<?php
				$year = date('Y');
				$team_member_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name,user_role,planning_rate,rate,sum(projected_hours) as hours 
					from ".$wpdb->prefix."project_user
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."useradd.user_id
					inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
					left join ".$wpdb->prefix."project_rates on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."project_rates.project_id
						and ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."project_rates.user_id
					left join ".$wpdb->prefix."projected_time on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projected_time.project_id
						and ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."projected_time.user_id
					where ".$wpdb->prefix."project_user.project_id=%d and year=%d
					group by ".$wpdb->prefix."users.ID",$checklist,$year);
				$team_member_results = $wpdb->get_results($team_member_query);
				
				echo '<style>input[type=number]{width:70px;}</style>';
				
				echo '<li style="overflow-x:auto;height:700px;"><table>';
				for($i=0;$i<=count($team_member_results);$i++)
				{
					echo '<col class="col'.$i.'" />';
				}
				echo '<tr><th style="width:250px;">&nbsp;</th><th style="width:250px;">&nbsp;</th>';
				
				foreach($team_member_results as $tmr)
				{
					echo '<th style="text-align: center;"><strong>'.$tmr->display_name.'</strong></th>';
				}
				echo '</tr>';
				echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';
				
				$projected_hours_total = 0;
				foreach($team_member_results as $tmr)
				{
					echo '<th style="text-align: center;" title="User Role" ><strong>'.$tmr->user_role.'</strong></th>';
					$projected_hours_total += $tmr->hours;
				}
				echo '</tr>';
				
				if($projected_hours_total != 0)
				{
					echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';
					foreach($team_member_results as $tmr)
					{
						echo '<th style="text-align: center;" title="Total Projected Hours">'.($tmr->hours==0 ? 0 : $tmr->hours).'</th>';
					}
					echo '<th>&nbsp;</th></tr>';
				}
				
				echo '<style>input[type=number]{width:90px;}</style>';
				
				echo '<tr><th><strong>Task Name</strong></th><th><strong>Task Description</strong></th>';
				foreach($team_member_results as $tmr)
				{
					echo '<th style="text-align: center;" title="Billing Rate" ><strong>$'.(!empty($tmr->rate) ? $tmr->rate : $tmr->planning_rate).'</strong>
						<input type="hidden" name="rate'.$tmr->ID.'" value="'.(!empty($tmr->rate) ? $tmr->rate : $tmr->planning_rate).'" /></th>';
				}
				echo '<th style="text-align: center;"><strong>Task Total</strong></th>';
				echo '</tr>';
				
				if(empty($tasks_results)){$total_rows = 10;}
				else{$total_rows=count($tasks_results);}
				
				for($t=0; $t<$total_rows; $t++)
				{
					if(empty($tasks_results)){$id = $t;}
					else{$id = $tasks_results[$t]->task_id;}
					echo '<tr>
						<input type="hidden" name="task['.$id.'][id]" value="'.$tasks_results[$t]->task_id.'" />
						<td><input type="text" class="do_input_new" name="task['.$id.'][task_name]" value="'.$tasks_results[$t]->task_name.'" /></td>
						<td><textarea rows="2" class="full_wdth_me do_input_new description_edit" name="task['.$id.'][task_desc]"></textarea></td>';
					$task_hours = 0;
					foreach($team_member_results as $tmr)
					{
						echo '<td style="text-align: right;">
							<input type="number" name="record['.$id.']['.$tmr->ID.'][hours]"
								onchange="checkPerson('.$tmr->ID.','.$id.');checkTask('.$id.');" value="'.$tmr->hours.'"/><br/>
							$<input type="text" size="7" name="value['.$id.']['.$tmr->ID.']" readonly />
							<input type="hidden" name="info['.$id.$tmr->ID.'][info]" value="'.$tmr->ID.',,,'.$id.',,,'.$tmr->hours.'" />
							</td>';
						$task_hours += $tmr->hours;
					}
					echo '<td style="text-align: right;"><input type="number" name="total_task'.$id.'" readonly /><br/>
						$<input type="text" size="7" name="total_value'.$id.'" readonly /></td>';
					echo '<input type="hidden" name="task['.$id.'][task_hours]" value="'.$task_hours.'" />';
					echo '</tr>';
				}
				echo '<tr><td>&nbsp;</td></tr>';
				echo '<tr><td><strong>Total:</strong></td><td>&nbsp;</td>';
				foreach($team_member_results as $tmr)
				{
					echo '<td style="text-align: right;"><input type="text" size="7" name="total_staff'.$tmr->ID.'" readonly /><br/>
						$<input type="text" size="7" name="staff_value'.$tmr->ID.'" readonly /></td>';
				}
				echo '<td style="text-align: right;"><input type="text" size="7" name="total_hours" readonly /><br/>
					$<input type="text" size="7" name="total_scope" readonly /></td>';
				echo '</table></li>';
				echo '<li>&nbsp;</li>';
				echo '<li><input type="submit" name="save-info" value="save" class="my-buttons-submit" /></li>';
				?>
				</ul>
			</div></div></div>
				<script type="text/javascript">
					function checkPerson(user,task){
						var myForm = document.forms.new_scope;
						var personHours = myForm.elements['total_staff' + user ];
						var personRate = myForm.elements['rate' + user ];
						var hourField = myForm.elements['record[' + task + '][' + user + '][hours]'];
						var taskValue = myForm.elements['value[' + task + '][' + user + ']'];
						var personTotalValue = myForm.elements['staff_value' + user];
						var info = myForm.elements['info[' + task + user + '][info]'];
						info.value = user + ",,," + task + ",,," + hourField.value;
						
						var personFields = document.querySelectorAll("[name$='[" + user + "][hours]']");
						var z = 0;
						
						var b = personRate.value * hourField.value;
						taskValue.value = b.toFixed(2);
						
						for(i=0;i<personFields.length;i++){
							z += personFields[i].value*1;
						}
						personHours.value = z;
						personValue = z * personRate.value;
						personTotalValue.value = personValue.toFixed(2);
					}
					function checkTask(x){
						var myForm = document.forms.new_scope;
						var taskTotal = myForm.elements['total_task' + x ];
						var taskHours = myForm.elements['task[' + x + '][task_hours]'];
						var totalHours = myForm.elements['total_hours'];
						var taskFields = document.querySelectorAll("[name^='record[" + x + "]']");
						var z = 0;
						
						for(i=0;i<taskFields.length;i++){
							z += taskFields[i].value*1;
						}
						taskTotal.value = z;
						taskHours.value = z;
						
						var taskTotalValue = myForm.elements['total_value' + x];
						var valueFields = document.querySelectorAll("[name^='value[" + x + "]']");
						var sum = 0;
						
						for(i=0;i<valueFields.length;i++){
							sum += valueFields[i].value*1;
						}
						taskTotalValue.value = sum.toFixed(2);
						
						var y = 0;
						var allHours = document.querySelectorAll("[name^='record']");
						for(i=0;i<allHours.length;i++){
							y += allHours[i].value*1;
						}
						totalHours.value = y;
						
						var tv = 0;
						var totalValue = myForm.elements['total_scope'];
						var allValues = document.querySelectorAll("[name^='value']");
						for(i=0;i<allValues.length;i++){
							tv += allValues[i].value*1;
						}
						totalValue.value = tv.toFixed(2);
					}
				</script>
		</form>
</div></div>
<?php get_footer(); ?>