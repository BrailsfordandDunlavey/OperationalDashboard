<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	
	function sitemile_filter_ttl($title){return "Close Project";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	$date = time();
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	
	$checklist = $_GET['ID'];
	
	$details = $wpdb->get_results($wpdb->prepare("select gp_id,client_name,project_name,prime_id,project_description,gp_project_number,status from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where ID=%s",$checklist));
	
	if(!empty($details[0]->prime_id))
	{
		$prime_id = $details[0]->prime_id;
		$prime_results = $wpdb->get_results($wpdb->prepare("select client_name from ".$wpdb->prefix."clients where client_id=%d",$prime_id));
	}
	
	$gp_project_number = $details[0]->gp_project_number;
	$gp_id = $details[0]->gp_id;
	$client_name = $details[0]->client_name;
	$prime_name = $prime_results[0]->client_name;
	$project_name = $details[0]->project_name;
	$description = $details[0]->project_description;
	$status = $details[0]->status;
	
	$project_team =array();
	$resultsteam = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."project_user where project_id=%s",$checklist));
	foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
	
	if((in_array($uid,$project_team) or $team == "Finance" or $uid==103) and $status!=3)
	{
		if(isset($_POST['save-info']))
		{
			$complete = 0;
			if(!empty($_POST['description'])){$description = $_POST['description']; $complete ++;}
			if(!empty($_POST['role'])){$role = $_POST['role']; $complete ++;}
			if(!empty($_POST['lessons'])){$lessons = $_POST['lessons']; $complete ++;}
			if(!empty($_POST['forward'])){$forward = $_POST['forward']; $complete ++;}
			
			if($complete==4){$status=3;}else{$status=2;}
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set status=%d,close_date=%d,closed_by=%d,project_description=%s,role=%s,
				lessons=%s,forward=%s where ID=%s",$status,$date,$uid,$description,$role,$lessons,$forward,$checklist));
			
			if($complete ==4)
			{
				$message = "The following project has been set as closed:  ".get_bloginfo('siteurl')."/?p_action=edit_checklist&ID=".$checklist.'
				Closed By:  '.$user_name.'
				Closed On:  '.date('m-d-Y',$date).'
				GP ID:  '.$gp_project_number;
				
				$email = array('mmitchell@programmanagers.com','bbannister@programmanagers.com','it@programmanagers.com','lharville@programmanagers.com','mdonovan@programmanagers.com');
				wp_mail($email,"Project Closed in OpDash",$message);
				
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set status=3,close_date=%d,closed_by=%d where project_parent=%s",$date,$uid,$checklist));
			}
			?>
			<div id="main_wrapper">
			<div id="main" class="wrapper">
			<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<?php
			$current_dir = getcwd();
			$target_dir = $current_dir."/wp-content/project_docs";
						
			foreach ($_FILES['fileToUpload']['name'] as $f => $name)
			{
				$file_name = time()." - ".basename($name);
				$target_file = $target_dir . "/" . $file_name;
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$f], $target_file))
				{
					echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
		
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
						values (%s,%s,'Final')",$checklist,$file_name));
				}
				else
				{
					foreach($_FILES['fileToUpload']['error'] as $error)
					{
						if($error == 1){echo "File was not uploaded because it was too large.<br/><br/>"; }
						if($error == 2){echo "File was not uploaded because it was too large.<br/><br/>"; }
						if($error == 3){echo "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
						if($error == 4){echo "No attachement.<br/><br/>"; }
						if($error > 4 or $error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
					}
				}
			}
			if($complete == 4){echo "Thank you.  The project has been closed.";}
			else{echo "Thank you.  Your responses have been saved, but there are missing entries below.  Please make all entries in order to complete the close.";}
			echo '<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>
				</div></div></div></div></div>';
			get_footer(); exit;
		}
	?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
			<div id="content"><h3>Project Details</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<?php if(empty($description)){ ?>
					<li><h3>Project Description</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" name="description" ></textarea></p></li>
					<?php }
						else{echo '<input type="hidden" value="'.$description.'" name="description" />';}?>
					<li><h3>How did B&D add value to the project in a strategic Partner role?</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" name="role"></textarea></p>
					</li>
					<li><h3>What lessons-learned can B&D take from this project?</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" name="lessons"></textarea></p>
					</li>
					<li><h3>What role does B&D play moving forward?</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" name="forward"></textarea></p>
					</li>
					<li><h3>Final Deliverable</h3><p><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></p></li>
					<li>&nbsp;</li>
					<li>
						<?php echo "Closing the project will remove it from your active projects and will not be able to have any time, expense, or billings entered to it.  
						<br/>
						The project will remain on the AR report and be available for collections so long as there is a balance.";?>
					</li>
					<li>&nbsp;</li>	
					<li><p><?php echo "Are you sure you want to close: <b><u>".$project_name."</b></u>?";?></p></li>
					<li>&nbsp;</li>
					<li>
						<p><input type="submit" name="save-info" class="my-buttons-submit" value="Yes, close the project" />
						&nbsp;
						<a href="/?p_action=project_card&ID=<?php echo $checklist;?>" class="my-buttons" style="color:#ffffff;">No, Return to Project</a>
						</p>							
					</li>
				</ul>
			</div></div></div>
		</form>
		</div></div>
<?php 
		get_footer();
	}
	else
	{
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		echo "This checklist is not currently available for edits.<br/><br/>";
		?>
		<a href="<?php bloginfo('siteurl');?>/contract-checklist/">Enter a new Contract Checklist</a><br/><br/>
								
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>

		<?php 	
		get_footer();
	}
?>