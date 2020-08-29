<?php
function billyB_add_user_project()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
	if(isset($_POST['save-info']))
	{

	?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
	<?php
		$projects = $_POST['projects'];
		
		foreach($projects as $project)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values(%s,%d,%s)",$project,$uid,'Work Team'));
		}
						
		echo "You have been added to the selected projects.<br/><br/>";
	?>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div>

	<?php }
	else{	
	?>   
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
						<li>&nbsp;</li>
						<li><?php echo 'The list below is in the format: Client Name - Project Name - Project Number';?></li>
						<li><?php echo "Hold ctl to select multiple projects";?></li>
						<li><select multiple="multiple" name="projects[]" size="20">
						<?php 
							
							
							$projects_query = "select ".$wpdb->prefix."projects.ID,".$wpdb->prefix."projects.gp_id,".$wpdb->prefix."projects.client_id,
								".$wpdb->prefix."projects.project_name,".$wpdb->prefix."clients.client_name from ".$wpdb->prefix."projects 
								inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
								where not exists 
								(select * from ".$wpdb->prefix."project_user where user_id='$uid' and ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."projects.ID)
								and status in (0,1,2,4,5,6) and confidential!='on' and project_parent=0 
								order by ".$wpdb->prefix."clients.client_name";
							
							$projects_results = $wpdb->get_results($projects_query);
							
							foreach($projects_results as $projects)
							{
								echo '<option value="'.$projects->ID.'">'.$projects->client_name.' - '.
								(strlen($projects->project_name)>25 ? substr($projects->project_name,0,25).'...' : $projects->project_name).' - '.$projects->gp_id.'</option>';
							}
						?>
						</select></li>
						<li>&nbsp;</li>
						<!--<li><input type="submit" name="add_rows" value="<?php echo "Add ten (10) rows";?>" /></li>-->
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
					</ul>
				</div>	
			</div> 
		</div>			
		</form>
				
<?php } 

}
add_shortcode('add_user_project','billyB_add_user_project')
?>