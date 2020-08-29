<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	get_header();
	
	$project_id = $_GET['ID'];
	
	$pm_results = $wpdb->get_results($wpdb->prepare("select project_manager from ".$wpdb->prefix."projects where ID=%s",$project_id));
	if($pm_results[0]->project_manager != $uid){wp_redirect(get_bloginfo('site_url')."/?p_action=project_card&ID=".$project_id); exit;}
	
	if(isset($_POST['save-info']))
	{
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">	
			<div id="content">
			<div class="my_box3">
			<div class="padd10">
		<?php
		$users = $_POST['users'];
		
		foreach($users as $user)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values(%s,%d,'Work Team')",$project_id,$user));
		}
						
		echo "The selected users have been added to the project.<br/><br/>";
		?>
		<a href="<?php echo "?p_action=project_card&ID=".$project_id;?>"><?php echo "Return to your Project";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div></div></div>

		<?php 
		get_footer();
	}
	else{
		?>   
		<form method="post"  enctype="multipart/form-data">
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
						<li>&nbsp;</li>
						<li><?php echo "Hold ctl to select multiple users";?></li>
						<li><select multiple="multiple" name="users[]" size="20">
						<?php 
						$users_query = $wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where ".$wpdb->prefix."useradd.status=1
							and not exists 
							(select ".$wpdb->prefix."project_user.user_id from ".$wpdb->prefix."project_user 
								where ".$wpdb->prefix."users.ID=".$wpdb->prefix."project_user.user_id
								and ".$wpdb->prefix."project_user.project_id=%s)
							order by display_name",$project_id);
						
						$users_results = $wpdb->get_results($users_query);
						
						foreach($users_results as $users)
						{
							echo '<option value="'.$users->ID.'">'.$users->display_name.'</option>';
						}
						?>
						</select></li>
						<li>&nbsp;</li>
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
					</ul>
				</div>	
			</div> 
		</div>
		</div>
		</div>
		</form>	
<?php }
get_footer();
?>