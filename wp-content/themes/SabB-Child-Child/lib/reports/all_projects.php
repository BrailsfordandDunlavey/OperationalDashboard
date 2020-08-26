<?php
function billyB_all_projects()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rights_results[0]->team;
	
	//if($uid==11){$uid=40;}//test Jeff Turner access

	$allowed_teams = array('Finance');
	$other_allowed_users = array(103,245,65,107,116,261,279,40,65,47);
 
	if(!in_array($team,$allowed_teams) and !in_array($uid,$other_allowed_users))
	{ wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit; }
	
	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$abb_name = $record['abb_name'];
			$orig_name = $record['orig'];
			$project_id = $record['project'];
			$project_manager = $record['project_manager'];
			$orig_pm = $record['orig_pm'];
			
			if($abb_name != $orig_name or ($project_manager != $orig_pm and !empty($project_manager)))
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set abbreviated_name=%s,project_manager=%d where ID=%s",
					$abb_name,$project_manager,$project_id));
			}
		}
	}
	
		?>   
		<form method="post" enctype="multipart/form-data">
		<div id="content_full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php
			$filter = "all";
			$statuses = "(0,1,2,3)";
			if($uid==40){$sphere_filter = " and sphere='Higher Ed' ";}else{$sphere_filter = "";}
			if(isset($_POST['submit_document']))
			{
				$filter = $_POST['select_document']; 
				if($_POST['active_filter']=="on")
				{
					$active="yes";
					if($_POST['include_opportunities']=="on")
					{
						$statuses = "(0,1,2,4,5,6)";
					}
					else{$statuses = "(0,1,2)";}
				}
				else
				{
					$active="no"; 
					if($_POST['include_opportunities']=="on")
					{
						$statuses = "(0,1,2,3,4,5,6)";
					}
				}
			}
			if($_POST['ad_servs']=="on")
			{
				$parent = '';
				$adservs= 'yes';
			}
			else{$parent = ' and project_parent=0 ';$adservs='no';}
			$active_projects_query = "select ID,project_name,gp_id,client_name,abbreviated_name,sphere,current_document,project_manager,status,project_parent 
				from ".$wpdb->prefix."projects 
				inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
				where status in ".$statuses." ".$parent." ".$sphere_filter."
				order by sphere,-abbreviated_name desc,gp_id";
			$filtered_projects_query = "select ID,project_name,gp_id,client_name,abbreviated_name,sphere,current_document,project_manager,status,project_parent 
				from ".$wpdb->prefix."projects 
				inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id 
				where status in ".$statuses." ".$parent." and current_document='$filter' ".$sphere_filter."
				order by sphere,-abbreviated_name desc,gp_id";
			
			if(isset($_POST['submit_document']) and $filter !="all"){$active_projects_results = $wpdb->get_results($filtered_projects_query);}
			else{$active_projects_results = $wpdb->get_results($active_projects_query);}
			
			$active_users = $wpdb->get_results($wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."users
				inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
				where status=1 order by display_name"));
			
			if($uid == 11 or $uid == 103 or $uid==245 or $uid==65 or $uid==107 or $uid==94){echo '<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li><li>&nbsp;</li>';}
			if($uid == 11){echo '<li>&nbsp;</li>';}
			if($uid == 11 or $uid == 94 or $uid==235 or $uid==103 or $uid==245 or $uid==40 or $uid==65)
			{
				$document_array = array('Purchase Order','Contract','Letter of Intent','Executive Override');
				foreach($active_projects_results as $apr)
				{
					if(!in_array($apr->current_document,$document_array) and !empty($apr->current_document))
					{
						array_push($document_array,$apr->current_document);
					}
				}
				echo '<li><select name="select_document" class="do_input_new">
					<option value="all" '.($filter=="all" ? 'selected="selected"' : '' ).'>All Contracts</option>
					<option value="" '.($filter=="" ? 'selected="selected"' : '' ).'>No Document Identified</option>';
				foreach($document_array as $da)
				{
					echo '<option '.($filter==$da ? 'selected="selected"' : '' ).'>'.$da.'</option>';
				}
				echo '</select>
					<input type="checkbox" name="active_filter" '.($active=="yes"? 'checked="checked"' : '').' />Only Active Projects
					<input type="checkbox" name="ad_servs" '.($adservs=="yes"? 'checked="checked"' : '').' />Include Adservs
					<input type="checkbox" name="include_opportunities" '.($_POST['include_opportunities']=="on" ? 'checked="checked"' : '').' />Include Opportunities
					</li>
					<li>
					<input type="submit" name="submit_document" value="Filter Contracts" class="my-buttons" />
					</li>';
				echo '<li><h3>Projects Without PM</h3><p><input type="checkbox" id="no_pm_box" onchange="noPM();"/></p></li>';
				echo '<li>&nbsp;</li>';
			}
			
			echo '<li><table width=100%">
				<tr>
				<th><b><u>Project</u></b></th>
				<th><b><u>Sphere</u></b></th>
				'.(($uid==11 or $uid==103 or $uid==245 or $uid == 94 or $uid==235) ? '<th><b><u>Project Manager</u></b></th>' : '' ).'
				'.(($uid == 11 or $uid == 103 or $uid==245 or $uid==65 or $uid==94 or $uid==107 or $uid==235) ? '<th><b><u>Abbreviated Name</u></b></th>' : '').'
				'.(($uid == 11 or $uid == 94 or $uid==235) ? '<th><b><u>Checklist</u></b></th>' : '' ).'
				</tr>';
			
			
			
			foreach($active_projects_results as $project)
			{
				if($project->status==3){$project_status=" (Closed)";}
				elseif($project->status > 3){$project_status = " (Opportunity)";}else{$project_status="";}
				if($project->project_parent==0){$parent="";}else{$parent=" (Adserv)";}
				$project_manager = $project->project_manager;
				if(empty($project_manager)){$project_manager=0;}
				$name = "".$project->client_name;
				if(!empty($project->project_name)){$name .= " - ".$project->project_name;}
				if(!empty($project->gp_id)){$name .= " - ".$project->gp_id;}
				if($uid==11 or $uid==94 or $uid==235)
				{
					if($project->status>3)
					{
						$link = '<th><a href="/?p_action=edit_opportunity&ID='.$project->ID.'">View Checklist</a></th>';
					}
					else
					{
						$link = '<th><a href="/?p_action=edit_checklist&ID='.$project->ID.'">View Checklist</a></th>';
					}
				}
				else{$link = '';}
				echo '<tr id="pm'.$project_manager.'">
					<th><a href="/?p_action=project_card&ID='.$project->ID.'">'.$name.'</a>'.$project_status.$parent.'</th>
					<th>'.$project->sphere.'</th>';
				if($uid==11 or $uid==103 or $uid==245 or $uid==94 or $uid==235)
				{	
					echo '<th><select name="record['.$project->ID.'][project_manager]" class="do_input_new"><option value="">Select PM</option>';
					foreach($active_users as $user)
					{
						echo '<option value="'.$user->user_id.'" '.($project->project_manager==$user->user_id ? 'selected="selected"' : '' ).'>'.$user->display_name.'</option>';
					}
					echo '</select></th>';
				}
					 
				echo (($uid == 11 or $uid == 103 or $uid==245 or $uid==65 or $uid==94 or $uid==107 or $uid==235) ? '<th><input type="text" name="record['.$project->ID.'][abb_name]" class="do_input_new" value="'.$project->abbreviated_name.'" />
					<input type="hidden" name="record['.$project->ID.'][project]" value="'.$project->ID.'" />
					<input type="hidden" name="record['.$project->ID.'][orig]" value="'.$project->abbreviated_name.'" />
					<input type="hidden" name="record['.$project->ID.'][orig_pm]" value="'.$project_manager.'" /></th>' : '').
					$link.'
					</tr>';
			}
			echo '</table></li>';
			if($uid == 11 or $uid == 103 or $uid==245 or $uid==65 or $uid==107 or $uid==94 or $uid==235){echo '<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li>';}
			?>
			</ul>
			<script type="text/javascript">
			function noPM(){
				var box = document.getElementById('no_pm_box');
				var allRows = document.querySelectorAll("[id^='pm']");
				if(box.checked == true){
					showRows = document.querySelectorAll("[id*='pm0']");
				}
				else{
					showRows = allRows;
				}
				for(i=0;i<allRows.length;i++){
					allRows[i].style.display = 'none';
				}
				for(i=0;i<showRows.length;i++){
					showRows[i].style.display = 'table-row';
				}
			}
			</script>
			</div>
			</div>						
		</div>
		</form>
<?php }
add_shortcode('all_projects','billyB_all_projects')
?>