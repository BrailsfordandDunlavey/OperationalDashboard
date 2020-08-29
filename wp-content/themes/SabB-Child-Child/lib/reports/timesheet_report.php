<?php
function billyB_timesheet_report()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$display_name = $current_user->display_name; 
	$rights_results = $wpdb->get_results($wpdb->prepare("select sphere,team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $rights_results[0]->sphere;
	$team = $rights_results[0]->team;
	$allowed_teams = array('Finance','Human Resources','Administrative','Executive');
	$sphere_leaders = array('Will Mangrum','Brian Hanlon','Bill Mykins','Brad Noyes','Jeff Turner','Laura Cosenzo','Pam Smith','Folakemi Gbadamosi');
	$group_leaders = array('Katie Karp','Joe Winters','Matt Bohannon');
	
	if(!in_array($team,$allowed_teams) and !in_array($display_name,$sphere_leaders) and !in_array($display_name,$group_leaders))
	{wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}

	if(isset($_POST['change_user']))
	{$display_name = $_POST['user_name'];$set_month = $_POST['selected_month'];}
	
	$current_month = date('Y-m-01',time());
	$set_month = strtotime(date("Y-m-01", strtotime($current_month))." -1 months");
	
	if(isset($_POST['set_month'])){$set_month = $_POST['selected_month'];}
	if(isset($_POST['inactive'])){$status = 0; $set_month = $_POST['selected_month'];}
	else{
		$status = 1; 
		if(!empty($_POST['selected_month'])){$set_month = $_POST['selected_month'];}
		}
	
	if(in_array($team,$allowed_teams))
	{
		$user_query = "select ".$wpdb->prefix."users.ID,display_name,position_title,sphere,team,user_comp_type from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
			where status>='$status' order by display_name";
	}
	if(in_array($display_name,$sphere_leaders))
	{
		$user_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name,position_title,sphere,team,user_comp_type 
			from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
			where status>='$status' and sphere=%s order by display_name",$sphere);
	}
	if($uid==103 or $uid==65 or $uid==245)
	{
		$user_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name,position_title,sphere,team,user_comp_type 
			from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
			where status>='$status' and (sphere='Sphere KMV' or sphere='Higher Ed') order by display_name",$sphere);
	}
	if(in_array($display_name,$group_leaders))
	{
		$user_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name,position_title,sphere,team,user_comp_type 
			from ".$wpdb->prefix."users
			inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
			inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
			where status>='$status' and team=%s order by display_name",$team);
	}
	$user_results = $wpdb->get_results($user_query);
	
	$mid_month = strtotime(date("Y-m-15",$set_month));
	$end_month = strtotime(date("Y-m-t", $set_month));
	
	$months_available = 24;
	$months_array = array(strtotime($current_month));
	
	for($i=1;$i<$months_available;$i++)
	{
		$month = strtotime(date('Y-m-d',strtotime($current_month)). "-".$i." months");
		array_push($months_array,$month);
	}
	$min_hours = 0;//so we can evaluate if time entries are missing
	$max_hours = 0;//so we can evaluate staff working "too hard"
	for($m=$set_month;$m<=$end_month;$m = $m + 86400)
	{
		if(date('D',$m) != "Sun" and date('D',$m) != "Sat")
		{
			$min_hours += 8;
			$max_hours += 10;
		}
	}
	?>
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<li><h3><?php echo "Set Time Period:";?></h3>
			<p><select class="do_input_new" name="selected_month">
			<?php
				foreach($months_array as $month)
				{echo '<option value="'.$month.'" '.($month==$set_month ? 'selected="selected"' : "").'>'.date('F Y',$month).'</option>';}
			?>
				</select>
				<input type="submit" name="set_month" class="my-buttons" value="<?php echo "Update Period"; ?>" /></p></li>
			<?php
				if($current_user->ID == 11)
				{
					echo '<li><select name ="user_name" class="do_input_new">';
					$select_users = "select display_name from ".$wpdb->prefix."users
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
						where status=1
						order by display_name";
					$select_results = $wpdb->get_results($select_users);
					foreach($select_results as $sr)
					{
						echo '<option '.($sr->display_name == $display_name ? 'selected="selected"' : '').'>'.$sr->display_name.'</option>';
					}
					echo '</select><input type="submit" name="change_user" class="my-buttons" value="Change User" /></li>';
				}
				
				if(in_array($team,$allowed_teams) or $uid==103 or $uid==245)
				{
					if(isset($_POST['inactive']))
					{
						echo '<li><input type="submit" value="Exclude Inactive Staff" name="active" class="my-buttons" /></li>';
					}
					else
					{
						echo '<li><input type="submit" value="Include Inactive Staff" name="inactive" class="my-buttons" /></li>';
					}
				} 
			?>
			</div>
			</div>						
		</div>
	</form>		
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<li><font size="3"><b>Time Report for <?php echo date('F Y',$set_month);?></b></font></li>
			<li><div  style="overflow-x: auto;overflow-y: scroll;height:500px;"><table width="100%">
			
			<?php
			echo '<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th style="text-align:center;"><b>Hours Worked</b></th><th>&nbsp;</th><th>&nbsp;</th>
				<th style="text-align:center;"><b>Time Off</b></th></tr>';
			echo '<tr><th><b><u>Name</u></b></th>
				<th><b><u>Position</u></b></th>
				<th><b><u>Sphere</u></b></th>
				<th><b><u>Team</u></b></th>
				<th><b><u>Status</u></b></th>
				<th style="text-align:center;"><b><u>Projects</u></b></th>
				<th style="text-align:center;"><b><u>HR</u></b></th>
				<th style="text-align:center;"><b><u>Farming</u></b></th>
				<th style="text-align:center;"><b><u>Admin</u></b></th>
				<th style="text-align:center;"><b><u>Subtotal</u></b></th>
				<th style="text-align:center;"><b><u>Vacation</u></b></th>
				<th style="text-align:center;"><b><u>Personal</u></b></th>
				<th style="text-align:center;"><b><u>Other</u></b></th>
				<th style="text-align:center;"><b><u>Subtotal</u></b></th>
				<th style="text-align:center;"><b><u>Total Hours Recorded</u></b></th>
				</tr>';
			
			
			
			
			$admin_code_query = "select other_project_code_value from ".$wpdb->prefix."other_project_codes";
			$admin_code_results = $wpdb->get_results($admin_code_query);
			$admin_array = array();
			foreach($admin_code_results as $a){array_push($admin_array,$a->other_project_code_value);}
			
			$time_off_array = array("Vacation","Sick","Holiday","Float","BEREAV","JURY","Mat/Pat");
			
			
			foreach($user_results as $u)
			{
				$user_id = $u->ID;
				
				$time_query = $wpdb->prepare("select user_id,project_id,timesheet_hours from ".$wpdb->prefix."timesheets 
					where timesheet_date>=%d and timesheet_date<=%d and user_id=%d",$set_month,$end_month,$user_id);
				$time_results = $wpdb->get_results($time_query);
				
				
				$user_id = $u->ID;
				$billable_time = 0;
				$farming = 0;
				$hr = 0;
				$admin_time = 0;
				$time_off = 0;
				$vacation = 0;
				$personal = 0;
				$total_time = 0;
				foreach($time_results as $t)
				{
					$time_user = $t->user_id;
					if($user_id==$time_user){$total_time += $t->timesheet_hours;}
					if($t->project_id == "Vacation" and $user_id==$time_user){$vacation += $t->timesheet_hours;}
					if($t->project_id == "Sick" and $user_id==$time_user){$personal += $t->timesheet_hours;}
					if(in_array($t->project_id,$time_off_array) and $t->project_id != "Vacation" and $t->project_id != "Sick" and $user_id==$time_user){$time_off += $t->timesheet_hours;}
					if($t->project_id=='0001HR' and $user_id==$time_user){$hr += $t->timesheet_hours;}
					if($t->project_id=='0001MK' and $user_id==$time_user){$farming += $t->timesheet_hours;}
					if(in_array($t->project_id,$admin_array) and $user_id==$time_user and $t->project_id!='0001HR' and $t->project_id!='0001MK'){$admin_time += $t->timesheet_hours;}
					if(!in_array($t->project_id,$time_off_array) and !in_array($t->project_id,$admin_array) and $user_id==$time_user){$billable_time += $t->timesheet_hours;}
				}
				echo '<tr><th>'.$u->display_name.'</th><th>'.$u->position_title.'</th><th>'.$u->sphere.'</th><th>'.$u->team.'</th>
					<th  style="text-align:center;" '.($u->user_comp_type=="Hourly" ? 'bgcolor="77A8BA"' : "").'>'.($u->user_comp_type=="Hourly" ? "Part-time" : "Full-time").'</th>
					<th style="text-align:center;">'.$billable_time.'</th>
					<th style="text-align:center;">'.$hr.'</th>
					<th style="text-align:center;">'.$farming.'</th>
					<th style="text-align:center;">'.$admin_time.'</th>
					<th style="text-align:center;"><b>'.($billable_time + $admin_time + $farming + $hr).'</b></th>
					<th style="text-align:center;">'.$vacation.'</th><th>'.$personal.'</th>
					<th style="text-align:center;">'.$time_off.'</th>
					<th style="text-align:center;"><b>'.($vacation + $personal + $time_off).'</b></th>
					<th style="text-align:center;" '.($total_time > $max_hours ? 'bgcolor="fa6767"' : (($total_time < $min_hours and $u->user_comp_type !="Hourly") ? 'bgcolor="e5f408"' : "")).'><b>'.($total_time).'</b></th>
					</tr>';
			}
			?>
			</table>
			</div>
			</li>
			</ul>
			</div>
			</div>						
		</div>		
	<?php
}
add_shortcode('timesheet_report','billyB_timesheet_report')
?>