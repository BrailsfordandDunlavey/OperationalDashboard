<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;
$user_name = $current_user->display_name;

get_header();

$project_id = $_GET['ID'];

$project_details = $wpdb->get_results($wpdb->prepare("select current_document,project_description,client_portal from ".$wpdb->prefix."projects where ID=%s",$project_id));
$current_document = $project_details[0]->current_document;

?>
<div id="main_wrapper">
<div id="main" class="wrapper">
<?php
if(isset($_POST['save-info']))
{
	echo '<div id="content">
		<div class="my_box3">
		<div class="padd10">';
	if(!empty($_POST['client_portal']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set client_portal=%s where ID=%d",$_POST['client_portal'],$project_id));
	}
	$current_dir = getcwd();
	$target_dir = $current_dir."/wp-content/project_docs";
	//Contract Docs
	$contract = time()." - ".basename($_FILES["fileToUpload"]["name"]);
	$target_file = $target_dir . "/" . $contract;
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
	{
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
			values (%s,%s,'Contract')",$project_id,$contract));
			
		echo "The Contract: ".$contract. " has been uploaded.<br/><br/>";
		$email = "yes";
	}
	else
	{
		foreach($_FILES['fileToUpload']['error'] as $error)
		{
			if($error == 1){echo "The Contract file - ".$contract." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 2){echo "The Contract file - ".$contract." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 3){echo "The Contract file - ".$contract." - was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
			//if($error == 4){echo "No new attachements were selected.<br/><br/>"; }
		}
	}
	//Proposal Docs
	$proposal = time()." - ".basename($_FILES["proposalUpload"]["name"]);
	$proposal_file = $target_dir . "/" . $proposal;
	if (move_uploaded_file($_FILES["proposalUpload"]["tmp_name"], $proposal_file))
	{
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
			values (%s,%s,'Proposal')",$project_id,$proposal));
		
		echo "The Proposal: ".$proposal. " has been uploaded.<br/><br/>";
		$email = "yes";
	}
	else
	{
		foreach($_FILES['proposalUpload']['error'] as $error)
		{
			if($error == 1){echo "The Proposal file - ".$proposal." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 2){echo "The Proposal file - ".$proposal." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 3){echo "The Proposal file - ".$proposal." - was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
			//if($error == 4){echo "No new attachements were selected.<br/><br/>"; }
		}
	}
	//Report Docs
	$report = time()." - ".basename($_FILES["reportToUpload"]["name"]);
	$report_file = $target_dir . "/" . $report;
	if (move_uploaded_file($_FILES["reportToUpload"]["tmp_name"], $report_file))
	{
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
			values (%s,%s,'Report')",$project_id,$report));
		
		echo "The Report: ".$report. " has been uploaded.<br/><br/>";
		$email = "yes";
	}
	else
	{
		foreach($_FILES['reportToUpload']['error'] as $error)
		{
			if($error == 1){echo "The Report file - ".$report." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 2){echo "The Report file - ".$report." - was not uploaded because it was too large.<br/><br/>"; }
			if($error == 3){echo "The Report file - ".$report." - was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
			//if($error == 4){echo "No new attachements were selected.<br/><br/>"; }
		}
	}
	$description = $_POST['description'];
	$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set project_description=%s where ID=%s",$description,$project_id));
	
	echo 'Description saved.';
	echo '</div></div></div>';
	
	if($email == "yes")
	{
		$link = get_bloginfo('siteurl').'/?p_action=project_card&ID='.$project_id;
		$to = array('npereira@programmanagers.com','mmitchell@programmanagers.com');
		wp_mail($to,'Project Document Added',$user_name.' has added a document to: '.$link);
	}
}
elseif(isset($_POST['change_contract']))
{
	$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set current_document=%s where ID=%d",$_POST['contract_status'],$project_id));
	$current_document = $_POST['contract_status'];
}
?>
	<form method="post"  enctype="multipart/form-data">
	<div id="content"><h2>Project Documents</h2>
		<div class="my_box3">
		<div class="padd10">
		<ul class="other-dets_m">
			<?php
			$doc_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."project_docs where project_id=%s order by project_doc_type",$project_id));
			if(!empty($doc_results))
			{
				foreach($doc_results as $doc)
				{
					$parsed_name = explode(" - ",$doc->project_doc_name);
					echo '<li><h3>'.$doc->project_doc_type.':</h3><p><a href="/wp-content/project_docs/'.$doc->project_doc_name.'" target="_blank" >'.$doc->project_doc_name.'</a> ('.date('m-d-Y',$parsed_name[0]).')
					
					</p></li>';
				}
			}
			else{echo '<li>No Documents attached to the project yet</li>';}
			?>
		</ul>
		</div>
		</div>
	</div>
	<div id="content"><h2>Add New Documents</h2>
		<div class="my_box3">
		<div class="padd10">
		<ul class="other-dets_m">
			<li><h3>Client Portal:</h3><p><input class="do_input_new full_wdth_me" type="text" name="client_portal" value="<?php echo $project_details[0]->client_portal;?>" /></p></li>
			<li><h3>Proposal:</h3><p><input class="my-buttons" type="file" name="proposalUpload" id="proposalUpload" /></p></li>
			<li><h3>Contract:</h3><p><input class="my-buttons" type="file" name="fileToUpload" id="fileToUpload" /></p></li>
			<li><h3>Final Report:</h3><p><input class="my-buttons" type="file" name="reportToUpload" id="reportToUpload" /></p></li>
			<li><h3>Project Description</h3>
				<p><textarea rows="12" cols="60" class="full_wdth_me do_input_new description_edit" 
					placeholder="<?php echo "Please enter the project decription"; ?>"  name="description"><?php echo $project_details[0]->project_description;?></textarea></p>
			<li><h3>&nbsp;</h3><p><input type="submit" name="save-info" class="my-buttons-submit" value="<?php echo "Save"; ?>" /></p></li>
		</ul>
		</div>
		</div>
	</div>
	<?php if($uid==94 or $uid==11 or $uid==235)
	{ ?>
	<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Change Contract Status";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<li><select name="contract_status" class="do_input_new">
					<option <?php if($current_document=="Contract"){echo 'selected="selected"';}?>>Contract</option>
					<option <?php if($current_document=="Purchase Order"){echo 'selected="selected"';}?>>Purchase Order</option>
					<option <?php if($current_document=="Letter of Intent"){echo 'selected="selected"';}?>>Letter of Intent</option>
					<option <?php if($current_document=="Executive Override"){echo 'selected="selected"';}?>>Executive Override</option>
					</select>
				</li>
				<li><input name="change_contract" type="submit" class="my-buttons-submit" value="Update Contract Status" /></li>
				</ul>
			</li>
		</ul>
	</div></div>
	<?php } ?>
	</form>
</div>
</div>
<?php   
	get_footer();
?>	