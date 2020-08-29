<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	
	$checklist = $_GET['ID'];
 
	$resultsteam = $wpdb->get_results($wpdb->prepare("select user_role,user_id,display_name from ".$wpdb->prefix."project_user 
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."users.ID
		where project_id=%s",$checklist));

	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$user_id = $record['id'];
			$role = $record['role'];
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."project_user set user_role=%s where user_id=%d and project_id=%s",$role,$user_id,$checklist));
		}
		wp_redirect(get_bloginfo('siteurl')."/?p_action=project_card&ID=".$checklist);
		exit;
	}
	else{
	?> 
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post"  enctype="multipart/form-data">
               	<div id="content"><h3><?php echo "Project Role Assignments";?></h3><br/>
					<div class="my_box3">
						<div class="padd10">
							<ul class="other-dets_m">
							<?php
							foreach($resultsteam as $r)
							{
								$user_id = $r->user_id;
								$user_name = $r->display_name;
								$role = $r->user_role;
								
								echo '<li hidden><input type="text" name="record['.$user_id.'][id]" value="'.$user_id.'" /></li>';
								echo '<li><h3>'.$user_name.'</h3><p><select class="do_input_new" name="record['.$user_id.'][role]" >
									<option value="">Select Role</option>
									<option '.($role == "Executive" ? 'selected="selected"' : "" ).' >Executive</option>
									<option '.($role == "Team Lead" ? 'selected="selected"' : "" ).' >Team Lead</option>
									<option '.($role == "SME" ? 'selected="selected"' : "" ).' >SME</option>
									<option '.($role == "Work Team" ? 'selected="selected"' : "" ).' >Work Team</option>
									</select></p></li>';
							}
							?>
							<li>&nbsp;</li>
							<li><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>
							</ul>
						</div>
					</div>
				</div>
			</form>	
		</div>
	</div>
<?php } 
	get_footer();
?>