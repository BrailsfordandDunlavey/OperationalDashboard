<?php
function billyB_remove_user_project()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
	if(isset($_POST['delete-info']))
	{
		echo '<div id="content">
			<div class="my_box3">
			<div class="padd10">';

		$projects = $_POST['projects'];
		
		foreach($projects as $project)
		{
			$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."project_user where project_id=%s and user_id=%d",$project,$uid));
		}			
		echo "You have been removed from the selected projects.<br/><br/>";
		echo '<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>
			</div></div></div>';
	}
	else{	
	?>   
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li><p><input type="submit" name="delete-info" class="my-buttons" value="Delete" /></p></li>
						<li>&nbsp;</li>
						<li>The list below is in the format: Client Name - Project Name - Project Number</li>
						<li>Hold ctl to select multiple projects</li>
						<li><select multiple="multiple" name="projects[]" size="20">
						<?php
						$projects_query = $wpdb->prepare("select ".$wpdb->prefix."projects.ID,gp_id,project_name,client_name from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
							where exists (select * from ".$wpdb->prefix."project_user where 
								user_id=%d and ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID) 
							and project_manager !=%d order by ".$wpdb->prefix."clients.client_name",$uid,$uid);
						
						$projects_results = $wpdb->get_results($projects_query);
						
						foreach($projects_results as $projects)
						{
							echo '<option value="'.$projects->ID.'">'.$projects->client_name.' - '
							.(strlen($projects->project_name) > 25 ? substr($projects->project_name,0,25).'...' : $projects->project_name).' - '.$projects->gp_id.'</option>';
						}
						?>
						</select></li>
						<li>&nbsp;</li>
						<li><p><input type="submit" name="delete-info" class="my-buttons" value="Delete" /></p></li>
					</ul>
				</div>	
			</div> 
		</div>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3>Tips</h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					You cannot remove yourself from a project where you are the Project Manager.<br/><br/>
					If the project is complete, please go to the Project Card, and click the "Close Project" button in the lower-right.<br/><br/>
					If the project needs to be assigned to another PM, please contact Maresha Mitchell.
					</ul>
				</li>
			</ul>
			</div>
		</div>
		</form>
<?php }
}
add_shortcode('remove_user_project','billyB_remove_user_project')
?>