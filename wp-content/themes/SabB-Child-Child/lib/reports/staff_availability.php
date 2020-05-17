<?php
function billyb_staff_availability()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	if(isset($_POST['submit_uid'])){$uid = $_POST['select_uid'];}
	$rights_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $rights_results[0]->sphere;
	if($sphere != "Higher Ed" and $sphere != "Sphere KMV"){$sphere = "Sphere KMV";}

	$months_array = array();
	$num_months = 12;
	$start_month = strtotime(date('Y-m-01',time()));

	$start_months_array = array();
	$go_back_months = 6;
	$go_forward_months = 6;
	for($i=$go_back_months;$i>0;$i--)
	{
		array_push($start_months_array,strtotime(date('Y-m-01',time())." - ".$i." months"));
	}
	for($i=0;$i<$go_forward_months;$i++)
	{
		array_push($start_months_array,strtotime(date('Y-m-01',time())." + ".$i." months"));
	}
	$show_projects = "No";
	$group = "";

	if(isset($_POST['change_start'])){$start_month = $_POST['start_month']; $sphere = $_POST['select_sphere'];}
	if(isset($_POST['submit_sphere'])){$sphere = $_POST['select_sphere']; $start_month = $_POST['start_month'];}
	if($sphere != "Sphere KMV" and $sphere != "Higher Ed"){$sphere = "Firm Wide";}

	for($i=0;$i<$num_months;$i++)
	{
		array_push($months_array,strtotime(date('Y-m-01',$start_month). '+ '.$i.' months'));
		if($i == $num_months - 1){$end_month = strtotime(date('Y-m-01',$start_month). '+ '.$i.' months');}
	}

	$available_hours_array = array();

	foreach($months_array as $ma)
	{
		$available_hours = 0;
		for($i=$ma;$i<=strtotime(date('Y-m-t',$ma));$i = $i + 86400)
		{
			if(date('D',$i)!= 'Sun' and date('D',$i)!= 'Sat'){$available_hours += 8;}
		}
		array_push($available_hours_array,$available_hours);
	}
	$min_month = min($months_array);
	$max_month = max($months_array);

	$total_records_array = array();//This is the main array to store all data and then echo later

	if($sphere == "Firm Wide")
	{
		$sphere_member_query = "select display_name,".$wpdb->prefix."users.ID,project_id,project_name,abbreviated_name,gp_project_number,".$wpdb->prefix."useradd.team
			from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."users.ID=".$wpdb->prefix."project_user.user_id
			inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID
			where ".$wpdb->prefix."projects.status=2 and ".$wpdb->prefix."useradd.status=1
			order by ".$wpdb->prefix."useradd.team,display_name,project_id";
	}
	else
	{
		$sphere_member_query = $wpdb->prepare("select display_name,".$wpdb->prefix."users.ID,project_id,project_name,abbreviated_name,gp_project_number,".$wpdb->prefix."useradd.team 
			from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."users.ID=".$wpdb->prefix."project_user.user_id
			inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID
			where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."projects.status=2 and ".$wpdb->prefix."useradd.status=1
			order by ".$wpdb->prefix."useradd.sphere,".$wpdb->prefix."useradd.team,display_name,project_id",$sphere);
	}

	$sphere_member_results = $wpdb->get_results($sphere_member_query);

	$sphere_member_array = array($sphere_member_results[0]->ID);//creates array to check each user as unique

	$month_total_array = array();//sets array with placeholders for user_id and display_name
	foreach($months_array as $ma)
	{
		array_push($month_total_array,0);//sets clean record NOT to be overwritten
	}
	$user_total_array = $month_total_array;//sets a blank array to store users monthly totals to be added to total_records_array later, but before user_projects_array
	$user_projects_array = array();//creates blank array to store all the users project details to load into total_records_array later, but after user_total_array
	$group_array = array();//set array of unique groups/clusters



	/*  This works, but is HIGHLY inefficient with all the queries.*/
	foreach($sphere_member_results as $smr)
	{
		if(!in_array($smr->team,$group_array))
		{
			array_push($group_array,$smr->team);
			
		}
		if(!in_array($smr->ID,$sphere_member_array))
		{
			$record_array = array($user_array,$user_total_array,$user_projects_array);
			array_push($total_records_array,$record_array);
			$user_total_array = $month_total_array;//reset the user_total_array
			$user_projects_array = array();//reset the user_projects_array
			array_push($sphere_member_array,$smr->ID);
		}
		$user_array = array($smr->ID,$smr->display_name,$smr->team);
		$project = $smr->project_id;
		if(!empty($smr->abbreviated_name))
		{$project_name = $smr->abbreviated_name;}
		elseif(!empty($smr->project_name))
		{$project_name = $smr->project_name;}
		else{$project_name = $smr->gp_project_number;}
		$project_record_array = array($project,$project_name);
		for($i=0;$i<count($months_array);$i++)
		{
			$ma = $months_array[$i];
			$user = $smr->ID;
			$projected_time = $wpdb->prepare("select * from ".$wpdb->prefix."projected_time where user_id=%d
				and projected_month=%d and project_id=%d",$user,$ma,$project);
			$projected_results = $wpdb->get_results($projected_time);
			$hours = $projected_results[0]->projected_hours;
			if($hours == 0){$hours = 0;}
			array_push($project_record_array,$hours);
			$user_total_array[$i] = $user_total_array[$i] + $hours;
		}
		array_push($user_projects_array,$project_record_array);
		
	}

	?>   	
		<script language="javascript" type="text/javascript">
			function hideRow(x){
				var myForm = document.forms.staff_availability;
				var button = myForm.elements['select_group'];
				var projectButton = myForm.elements['show_projects'];
				var allRows = document.querySelectorAll("[id*='group']");
				var userButton = myForm.elements['select_users'];
				var boxes = document.querySelectorAll("[name='user_box']");
				
				if(x=="projects"){
					if(projectButton.value == "Show Projects"){
						projectButton.value = "Hide Projects";
					}
					else{
						projectButton.value = "Show Projects";
					}
				}
				else if(x=="users"){
					button.value = "";
					if(userButton.value == "Show Only Selected Users"){
						userButton.value = "Show All Users";
					}
					else{
						userButton.value = "Show Only Selected Users";
					}
				}
				else if(x=="groups"){
					userButton.value = "Show Only Selected Users";
				}
				//Hide all the rows
				for(i=0;i<allRows.length;i++){
					allRows[i].style.display = 'none';
				}
				//Define conditions for showing rows
				if(userButton.value=="Show Only Selected Users" && projectButton.value=="Show Projects"){
					var showRows = document.querySelectorAll("[id^='group" + button.value + "']");//get the rows with id starting with "group" for user totals
					var leaderRows = document.querySelectorAll("[id^='groupLeadership']");//get the leadership totals
					for(i=0;i<showRows.length;i++){
						showRows[i].style.display = 'table-row';
					}
					for(i=0;i<leaderRows.length;i++){
						leaderRows[i].style.display = 'table-row';
					}
				}
				else if(userButton.value=="Show Only Selected Users" && projectButton.value!="Show Projects"){
					var showRows = document.querySelectorAll("[id*='group" + button.value + "']");//get the rows with id containing "group" for user projects
					var leaderRows = document.querySelectorAll("[id*='groupLeadership']");//get leadership details
					for(i=0;i<showRows.length;i++){
						showRows[i].style.display = 'table-row';
					}
					for(i=0;i<leaderRows.length;i++){
						leaderRows[i].style.display = 'table-row';
					}
				}
				else if(userButton.value!="Show Only Selected Users"){
					
					for(i=0;i<boxes.length;i++){
						if(boxes[i].checked == true){
							var value = boxes[i].value;
							if(projectButton.value == "Show Projects"){
								var showRows = document.querySelectorAll("[id*='user" + boxes[i].value + "']");
								for(j=0;j<showRows.length;j++){
									if(showRows[j].id.charAt(0) != "p"){
										showRows[j].style.display = 'table-row';
									}
								}
							}
							else{
								var showRows = document.querySelectorAll("[id*='user" + boxes[i].value + "']");
								for(j=0;j<showRows.length;j++){
									showRows[j].style.display = 'table-row';
								}
							}
						}
					}
				}
			}
		</script>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post" name="staff_availability" enctype="multipart/form-data">
					<div id="content_full">
						<div class="my_box3">
						<div class="padd10">					
						<ul class="other-dets_m">
						<?php
						
						echo '<li><select name="start_month" class="do_input_new">';
						for($i=0;$i<count($start_months_array);$i++)
						{echo '<option value="'.$start_months_array[$i].'" '.($start_month==$start_months_array[$i] ? 'selected="selected"' : '').'>'.date('m-d-Y',$start_months_array[$i]).'</option>';}
						echo'</select><input type="submit" value="Change Start" name="change_start" class="my-buttons" /></li>';
						echo '<li>&nbsp;</li>';
						
						if($current_user->ID == 11)
						{
							echo '<li><select name="select_sphere" class="do_input_new">
								<option value="Higher Ed" '.($sphere == "Higher Ed" ? "selected='selected'" : "").'>Higher Ed</option>
								<option value="Sphere KMV" '.($sphere == "Sphere KMV" ? "selected='selected'" : "").'>Sphere KMV</option>
								<option value="Firm Wide" '.(($sphere != "Higher Ed" and $sphere != "Sphere KMV") ? "selected='selected'" : "").'>Firm Wide</option>
								</select>
								<input type="submit" value="Change Sphere" name="submit_sphere" class="my-buttons" />
								
								</li>
								<li>&nbsp;</li>';
							echo '<li><select name="select_uid" class="do_input_new">';
							$users = $wpdb->get_results("select display_name,ID from ".$wpdb->prefix."users");
							foreach($users as $u)
							{
								echo '<option value="'.$u->ID.'" '.($u->ID==$uid ? 'selected="selected"' : '' ).'>'.$u->display_name.'</option>';
							}
							echo '</select><input type="submit" name="submit_uid" class="my-buttons" value="Change User" /></li>';
						}
						
						echo '<li><select name="select_group" class="do_input_new" onchange="hideRow(\'groups\');" >';
						$group_name = "Group";
						if($sphere == "Higher Ed"){$group_name = "Cluster";}
						echo '<option value="">All '.$group_name.'s</option>';
						foreach($group_array as $ga)
						{
							if($ga != "")
							{
								echo '<option '.($ga == $group ? "selected='selected'" : "" ).'>'.$ga.'</option>';
							}
						}
						
						echo '</select>
							<li>&nbsp;</li>';
						
						
						echo '<li><input type="button" name="show_projects" value="Show Projects" onClick="hideRow(\'projects\');"/>
							<input type="button" name="select_users" value="Show Only Selected Users" onClick="hideRow(\'users\');"/></li>';
						echo '<li>&nbsp;</li>';
						echo '<li style="overflow-x:auto;"><table width="100%">
							<tr>
							<th><strong><font size="2">Staff Member</font></strong></th>';
						
						echo '<th id="projectcolumn"><strong><font size="2">Project</font></strong></th>';
						
						for($i=0;$i<count($months_array);$i++)
						{echo '<th><strong><font size="2">'.date('M Y',$months_array[$i]).'</font></strong></th>';}
						echo '</tr>';
						
						
						foreach($total_records_array as $tra)
						{
							echo '<tr id="group'.$tra[0][2].'user'.$tra[0][0].'" >
								<td><input type="checkbox" name="user_box" value="'.$tra[0][0].'"  />
									<strong><a href="/?p_action=employee_projected_hours&ID='.$tra[0][0].'">'.$tra[0][1].'</a></strong>
									<br/>
									('.(!empty($tra[0][2]) ? $tra[0][2] : "Group Unknown" ).')
									</td>';
							
							echo '<td id="projecttotal"><strong>Total</strong></td>';
								
							foreach($tra[1] as $mt)
							{
								$m = 0;
								if($mt/$available_hours_array[$m] > 1){$font_beg = '<font color="red">'; $font = 1;}
								elseif($mt/$available_hours_array[$m] > .9){$font_beg = '<font color="orange">'; $font = 1;}
								else{$font_beg = '<font color="green">'; $font = 1;}
								if($font == 1)
								{
									echo '<td><strong>'.$font_beg.number_format($mt,2).'</font></strong><br/>
									('.number_format(($mt/$available_hours_array[$m])*100,0).'%)</td>';
								}
								else
								{
									echo '<td><strong>'.number_format($mt,2).'</strong></td>';
								}
								$m++;
							}
							echo '</tr>';
							foreach($tra[2] as $p)
							{
								echo '<tr id="project'.$p[0].' group'.$tra[0][2].' user'.$tra[0][0].'" style="display:none;">
									<td>&nbsp;</td>';
								for($i=1;$i<count($p);$i++)
								{
									if($i==1)
									{
										echo '<td><a href="/?p_action=project_projected_hours&ID='.$p[0].'">'.$p[$i].'</a></td>';
									}
									else
									{
										echo '<td>'.$p[$i].'</td>';
									}
								}
								echo '</tr>';
							}
						}
						
						echo '</table></li>';
						?>	
						</ul>
						</div>
						</div>
					</div>
					
					</form>
				</div></div>
				
				
<?php }

add_shortcode('staff_availability','billyb_staff_availability') ?>