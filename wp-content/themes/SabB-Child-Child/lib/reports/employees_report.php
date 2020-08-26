<?php
function billyb_employees_report()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$allowed_array = array('Human Resources','Finance','Executive');
	if($uid!=103)
	{
		if(!in_array($rights_results[0]->team,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard");}
	}

	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$now = time();
		$records = $_POST['record'];
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$user_id = $details[0];
			$prev_rate = $details[1];
			$prev_report_to = $details[2];
			$prev_sphere = $details[3];
			$comp_type = $details[4];
			$old_position = $details[5];
			$reports_to = $record['report'];
			$sphere = $record['sphere'];
			$rate = $record['wage'];
			if(empty($rate)){$rate = $prev_rate;}
			$new_position = $record['position'];
			
			if(($prev_rate != $rate and ($comp_type =="Hourly" or $comp_type=="Non-exempt")) or $prev_report_to != $reports_to or $sphere != $prev_sphere)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set sphere=%s,reports_to=%d,user_wage=%f where user_id=%d",$sphere,$reports_to,$rate,$user_id));
				
				if($prev_rate != $rate and ($comp_type=="Hourly" or $comp_type=="Non-exempt"))
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_changes (user_id,change_type,change_date,changed_by,previous_value)
						values(%d,%s,%d,%d,%s)",$user_id,"wage",$now,$uid,$prev_rate));
				}
				if($prev_report_to != $reports_to)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_changes (user_id,change_type,change_date,changed_by,previous_value)
						values(%d,%s,%d,%d,%s)",$user_id,"reports_to",$now,$uid,$prev_report_to));
				}
				if($sphere != $prev_sphere)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_changes (user_id,change_type,change_date,changed_by,previous_value)
						values(%d,%s,%d,%d,%s)",$user_id,"sphere",$now,$uid,$prev_sphere));
				}
			}
			if(($old_position!=$new_position) and !empty($new_position))
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set position=%d where user_id=%d",$new_position,$user_id));
			}
		}
	}
	$positions = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."position"));
	?>   
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post" name="employee_report" enctype="multipart/form-data">
				<script type="text/javascript">
				function filter(x){
					var myForm = document.forms.empoyee_report;
					var allFields = document.querySelectorAll("[id^='filter ']");
					var showFields = document.querySelectorAll("[id*='" + x + "']");
					if(x != "all"){
						for(i=0;i<allFields.length;i++){
							allFields[i].style.display = 'none';
						}
						for(i=0;i<showFields.length;i++){
							showFields[i].style.display = 'table-row';
						}
					}
					else{
						for(i=0;i<allFields.length;i++){
							allFields[i].style.display = 'table-row';
						}
					}
				}
				</script>
				<div id="content">
					<div class="my_box3">
					<div class="padd10">					
					<ul class="other-dets_m">
					<style>
					input[type=number]{width:75px;}
					</style>
					<li><input type="submit" name="save-info" value="save" class="my-buttons" /></li>
					<li>&nbsp;</li>
					<li>
					<table width="100%">
					<tr>
					<th><b><u>Name</u></b></th>
					<?php 
					if($uid==94 or $uid==103)
					{
						echo '<th><b><u>Position</u></b></th>';
					}
					else
					{
						echo '<th><b><u>Pay Type</u></b></th>';
						echo '<th><b><u>Rate</u></b></th>';
					}
					?>
					<th><b><u>Sphere</u></b></th>
					<th><b><u>Reports To</u></b></th>
					</tr>
					<?php
						$employees_query = "select display_name,sphere,team,reports_to,user_comp_type,user_wage,user_id,position from ".$wpdb->prefix."users
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							left join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
							where status=1 order by display_name";
						$employees_results = $wpdb->get_results($employees_query);
						
						//create list of spheres
						$sphere_array = array();
						foreach($employees_results as $sphere)
						{
							if(!in_array($sphere->sphere,$sphere_array)){array_push($sphere_array,$sphere->sphere);}
						}
						//create reports_to list
						$reports_to_array = array();
						$holding_array = array();
						foreach($employees_results as $report)
						{
							if(!in_array($report->reports_to,$holding_array)){array_push($holding_array,$report->reports_to);}
						}
						foreach($holding_array as $h)
						{
							foreach($employees_results as $e)
							{
								if($h == $e->user_id){$record_array = array($e->user_id,$e->display_name);array_push($reports_to_array,$record_array);}
							}
						}
						foreach($employees_results as $employee)
						{
							$reports_to = $employee->reports_to;
							$reports_to_name = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users 
								where ID=%d",$reports_to));
							$reports_to_name = $reports_to_name[0]->display_name;
							echo '<tr id="filter '.$employee->sphere.' '.$reports_to.' '.(empty($employee->user_comp_type) ? "Salaried" : $employee->user_comp_type).' ">
								<input type="hidden" name="record['.$employee->user_id.'][details]" value="'.$employee->user_id.',,,'.$employee->user_wage.',,,'.$reports_to.',,,'.$employee->sphere.',,,'.$employee->user_comp_type.',,,'.$employee->position.'" />
								<td>'.$employee->display_name.'</td>';
							
							if($uid==94 or $uid==103)
							{
								echo '<td><select name="record['.$employee->user_id.'][position]" class="do_input_new">';
								foreach($positions as $p)
								{
									echo '<option value="'.$p->ID.'" '.($p->ID==$employee->position ? 'selected="selected"' : '' ).'>'.$p->position_title.'</option>';
								}
								echo '</select></td>';
							}
							
							else
							{
								echo '<td>'.(empty($employee->user_comp_type) ? "Salaried" : $employee->user_comp_type).'</td>
									<td>'.(empty($employee->user_comp_type) ? "&nbsp;" : '<input type="number" name="record['.$employee->user_id.'][wage]" step=".01" value="'.$employee->user_wage.'" />').'</td>';
							}
							echo '<td><select name="record['.$employee->user_id.'][sphere]" class="do_input_new">';
							foreach($sphere_array as $sa)
							{
								echo '<option value="'.$sa.'" '.($sa==$employee->sphere ? 'selected="selected"' : '' ).'>'.$sa.'</option>';
							}
							echo '</select></td>
							<td><select name="record['.$employee->user_id.'][report]" class="do_input_new">';
							echo '<option value="0">No Manager</option>';
							foreach($employees_results as $e)
							{
								echo '<option value="'.$e->user_id.'" '.($e->user_id == $reports_to ? 'selected="selected"' : '' ).'>'.$e->display_name.'</option>';
							}
							echo '</select></td>
								</tr>';
						}
						?>
						</table>
						</li>
						<li>&nbsp;</li>
						<li><input type="submit" name="save-info-two" value="save" class="my-buttons" /></li>
					</ul>
					</div>
					</div>
				</div>
				<?php 
				if($uid!=94 and $uid!=103)
				{
					?>
					<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h2>Filters</h2>
					<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
					echo '<li><h3>Pay Type</h3><p><select name="pay_filter" onchange="filter(this.value);">
						<option value="all">All Pay Types</option>
						<option>Non-exempt</option><option>Hourly</option>
						<option>Salaried</option></select></p></li>';
					echo '<li><h3>Sphere</h3><p><select name="sphere_filter" onchange="filter(this.value);"><option value="all">All Spheres</option>';
					foreach($sphere_array as $sa)
					{
						echo '<option>'.$sa.'</option>';
					}
					echo '</select></p></li>';
					echo '<li><h3>Reports To</h3><p><select name="reports_to_filter" onchange="filter(this.value);"><option value="all">All</option>';
					foreach($reports_to_array as $e)
					{
						echo '<option value="'.$e[0].'">'.$e[1].'</option>';
					}
					echo '</select></p></li>';
					?>
					</ul>
					</li>
					</ul>
					</div>
					</div>
					<?php
				}
				?>
			</form>
		</div>
	</div>
<?php }

add_shortcode('employees_report','billyb_employees_report') ?>