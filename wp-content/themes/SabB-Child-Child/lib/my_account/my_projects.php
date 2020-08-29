<?php
function billyB_my_projects()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	
	$uid = $current_user->ID;
	$change_array = array(11);
	
	if(in_array($current_user->ID,$change_array))
	{if(isset($_POST['set_id'])){$uid = $_POST['change_id'];}}
	else{$uid = $current_user->ID;}
	
	if(in_array($current_user->ID,$change_array))
	{
		?>
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				echo '<li><h3>Change Employee:</h3><p><select class="do_input_new" name="change_id">';
				$users_results = $wpdb->get_results("select * from ".$wpdb->prefix."users 
					where display_name!='admin' and display_name!='bbannister' and display_name!='TEST'	order by display_name");
				foreach($users_results as $user)
				{
					echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
				}
				echo '</select>';
				echo '<input type="submit" name="set_id" class="my-buttons" value="Change ID" /></p></li>';
				?>
				</ul>
			</div>
			</div>						
		</div>
		</form>
		<?php
	}
	$p = $wpdb->get_results($wpdb->prepare("select project_id,project_name,abbreviated_name,".$wpdb->prefix."projects.gp_id,display_name,project_manager,
		sum(timesheet_hours) as hours,".$wpdb->prefix."projects.status,name,submarket
		from ".$wpdb->prefix."projects
		inner join ".$wpdb->prefix."timesheets on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."timesheets.project_id
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."users.ID
		left join ".$wpdb->prefix."terms on ".$wpdb->prefix."projects.market=".$wpdb->prefix."terms.term_id
		where user_id=%d
		group by project_id",$uid));
	?>
	<div id="content">
		<div class="my_box3">
		<div class="padd10"><h3>My Projects</h3>
		<ul class="other-dets_m">
			<li>&nbsp;</li>
			<?php
			if(!empty($p))
			{
				echo '<li><table width="100%">';
				echo '<tr>
					<th><b><u>Project</u></b></th>
					<th><b><u>Hours Worked</u></b></th>
					<th><b><u>Project Market</u></b></th>
					<th><b><u>Project Manager</u></b></th>
					<th><b><u>Project Status</u></b></th>
					</tr>';
				
				foreach($p as $pp)
				{
					if(!empty($pp->abbreviated_name)){$project_name = $pp->abbreviated_name;}
					elseif(!empty($pp->project_name)){$project_name = $pp->project_name;}
					else{$project_name = $pp->gp_id;}
					
					if($pp->status==2){$status = "Active";}
					elseif($pp->status==3){$status = "Closed";}
					else{$status = "Opportunity";}
					
					$submarket = $wpdb->get_results($wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$pp->submarket));
					
					echo '<tr>
						<td><a href="'.get_bloginfo('siteurl').'/?p_action=project_card&ID='.$pp->project_id.'">'.$project_name.'</a></td>
						<td style="text-align:center;">'.number_format($pp->hours,2).'</td>
						<td>'.$pp->name.': '.$submarket[0]->name.'</td>
						<td><a href="'.get_bloginfo('siteurl').'?p_action=user_profile&ID='.$pp->project_manager.'">'.$pp->display_name.'</a></td>
						<td style="text-align:center;">'.$status.'</td>
						</tr>';
				}
				echo '</table></li>';
			}
			else
			{
				echo '<li>You have no time recorded on any Projects</li>';
			}
			?>
			<li>&nbsp;</li>
		</ul>	
		</div>
		</div>
	</div>
	
<?php } 
add_shortcode('my_projects','billyB_my_projects')
?>