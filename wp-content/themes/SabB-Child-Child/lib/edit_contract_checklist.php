<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	function sitemile_filter_ttl($title){return "Edit Contract Checklist";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	$all_teams = $wpdb->get_results("select distinct team,sphere from ".$wpdb->prefix."useradd where team!='' and sphere!='functional' order by sphere,team");
	
	$checklist = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d",$checklist));
	
	$gp_id = $details[0]->gp_id;
	$author = $details[0]->project_author;
	$gp_project_number = $details[0]->gp_project_number;
	$client_id = $details[0]->client_id;
	$prime_id = $details[0]->prime_id;
	$project_name = $details[0]->project_name;
	$abb_name = $details[0]->abbreviated_name;
	$sphere = $details[0]->sphere;
	$project_group = $details[0]->project_group;
	$project_manager = $details[0]->project_manager;
	$project_parent = $details[0]->project_parent;
	$fee_type = $details[0]->fee_type;  $original_fee_type = $details[0]->fee_type;
	$initiation_document = $details[0]->initiation_document;$original_initiation_document = $details[0]->initiation_document;
	$document_number = $details[0]->document_number;$original_document_number = $details[0]->document_number;
	$estimated_start = date('m/d/Y',$details[0]->estimated_start);$original_estimated_start = $details[0]->estimated_start;
	$project_type = $details[0]->project_type;$original_project_type = $details[0]->project_type;
	$fee_amount = $details[0]->fee_amount;$original_fee_amount = $details[0]->fee_amount;
	$sub_fee = $details[0]->sub_fee_amount;$original_sub_fee = $details[0]->sub_fee_amount;
	$expense_amount = $details[0]->expense_amount;$original_expense_amount = $details[0]->expense_amount;
	$expense_type = $details[0]->expense_type;$original_expense_type = $details[0]->expense_type;
	$receipts = $details[0]->receipt;
	$return = $details[0]->return_destroy;
	$does = $details[0]->does;
	$background = $details[0]->background;
	$market = $details[0]->market;
	$submarket = $details[0]->submarket;
	$confidential = $details[0]->confidential;
	$venues = $details[0]->venues;
	$per_diem = $details[0]->per_diem;$original_per_diem = $details[0]->per_diem;
	$perdiem_notes = $details[0]->perdiem;$original_perdiem_notes = $details[0]->perdiem;
	$markup = $details[0]->markup;
	$retainer = $details[0]->retainer;
	$retention = $details[0]->retention;
	$contact = $details[0]->contact;
	$address = $details[0]->address;
	$city = $details[0]->city;
	$state_id = $details[0]->state;
	$zip = $details[0]->zip;
	$email = $details[0]->email;
	$phone = $details[0]->phone;
	$delivery_type = $details[0]->delivery_type;
	$notes = $details[0]->notes;
	$status = $details[0]->status;$original_status = $details[0]->status;
	$accounting_notes = $details[0]->accounting_notes;
	$client_portal = $details[0]->client_portal;
	$submit_date = $details[0]->submitted_date;
	
	if(!empty($project_parent))
	{
		$parent_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d",$project_parent));
	}
	
	if($status > 3 and $_SESSION['stay_on_page'] != 'remain'){wp_redirect(get_bloginfo('siteurl')."/?p_action=edit_opportunity&ID=".$checklist); exit; }
	
	if(!empty($client_id))
	{
		$client_name_result = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."clients where client_id=%d",$client_id));
		$client_name = $client_name_result[0]->client_name;
	}
	if(!empty($prime_id))
	{
		$prime_name_result = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."clients where client_id=%d",$prime_id));
		$prime_name = $prime_name_result[0]->client_name;
	}	
	if(!empty($state_id))
	{
		$state_result = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."states where state_id=%d",$state_id));
		$state = $state_result[0]->state_abbreviation;
	}
	
	$project_team =array();
	$resultsteam = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."project_user where project_id=%d",$checklist));
	foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
	
	if($status==3 or (($status==1 or $status==2) and $team != "Finance"))
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
		<a href="<?php bloginfo('siteurl');?>/contract-checklist/"><?php echo "Enter a new Contract Checklist";?></a><br/><br/>
								
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div></div></div>

		<?php 	
		get_footer();
		exit;
	}
	if(isset($_POST['save-info']) or isset($_POST['submit-info']) or isset($_POST['approve-info']))
	{
		$gp_id = trim($_POST['gp_id']);
		$gp_project_number = trim($_POST['gp_project_number']);
		$client_id = trim($_POST['client_id']);
		$prime_id = trim($_POST['prime_id']);
		$project_name = trim($_POST['project_name']);
		$abb_name = trim($_POST['abb_name']);
		$sphere = trim($_POST['sphere']);
		$project_group = $_POST['project_group'];
		$project_manager = trim($_POST['project_manager']);
		if(empty($project_group))
		{
			$group_search = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$project_manager));
			$project_group = $group_search[0]->team;
			if(empty($project_group) and isset($_POST['submit-info']))
			{
				if($sphere == 'Higher Ed'){$project_group = 'East';}
				elseif($sphere == 'Sphere KMV'){$project_group = 'MDAS';} 
			}
		}
		$project_team = $_POST['project_team'];							
		$fee_type = trim($_POST['fee_type']);
		$initiation_document = trim($_POST['initiation_document']);
		$document_number = $_POST['document_number'];
		$estimated_start = strtotime($_POST['estimated_start']);
		$project_type = trim($_POST['project_type']);
		$fee_amount = trim($_POST['fee_amount']);
		$sub_fee = trim($_POST['sub_fee']);
		$expense_amount = trim($_POST['expense_amount']);
		$expense_type = trim($_POST['expense_type']);
		$receipts = $_POST['receipts'];if($receipts == "on"){$receipts_required=1;}else{$receipts_required=0;}
		$return = 0; if($_POST['return']=="on"){$return = 1;}
		$does = 0; if($_POST['does']=="on"){$does=1;}
		$background = 0; if($_POST['background']="on"){$background=1;}
		$market = trim($_POST['project_cat_cat']);
		$submarket = trim($_POST['subcat']);
		$confidential = trim($_POST['confidential']);
		$venues = trim($_POST['venues']);
		$per_diem = $_POST['per_diem'];
		if($per_diem=="on"){$per_diem=1;}else{$per_diem=0;}
		$perdiem_notes = trim($_POST['perdiem_notes']);
		$markup = trim($_POST['markup']);
		$retainer = trim($_POST['retainer']);
		$retention = trim($_POST['retention']);
		$contact = trim($_POST['contact']);
		$address = trim($_POST['address']);
		$city = trim($_POST['city']);
		$state = trim($_POST['state']);
		$zip = trim($_POST['zip']);
		$email = trim($_POST['email']);
		$phone = trim($_POST['phone']);
		$delivery_type = trim($_POST['delivery_type']);
		$notes = trim($_POST['notes']);
		$accounting_notes = trim($_POST['accounting_notes']);
		$client_portal = $_POST['client_portal'];
		$approve_date = 0;
		if(isset($_POST['submit-info'])){$status = 1; $submit_date = time();}
		elseif(isset($_POST['approve-info'])){$status = 2; $approve_date = time();}
		else{$status = 0;}
		
		if(empty($client_id) and !empty($_POST['named_client']))
		{
			$name_check = $wpdb->get_results($wpdb->prepare("select client_id from ".$wpdb->prefix."clients where client_name=%s",$_POST['named_client']));
			if(empty($name_check))
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values (%s)",$_POST['named_client']));
				$client_id = $wpdb->insert_id;
			}
			else{$client_id=$name_check[0]->client_id;}
		}
		
		$querya = $wpdb->prepare("update ".$wpdb->prefix."projects set gp_id=%s,gp_project_number=%s,client_id=%d,prime_id=%d,project_name=%s,
			abbreviated_name=%s,sphere=%s,project_group=%s,project_manager=%d,fee_type=%s,initiation_document=%s,current_document=%s,document_number=%s,estimated_start=%d,project_type=%s,
			fee_amount=%f,expense_amount=%f,expense_type=%s,market=%d,submarket=%d,confidential=%s,venues=%s,contact=%s,address=%s,city=%s,state=%d,
			zip=%s,email=%s,phone=%s,delivery_type=%s,notes=%s,accounting_notes=%s,status=%d,markup=%s,retainer=%f,retention=%f,per_diem=%d,perdiem=%s,sub_fee_amount=%f,
			submitted_date=%d,approved_date=%d,client_portal=%s,receipt=%d,win_loss=1,return_destroy=%d,does=%d,background=%d 
			where ID=%d",
			$gp_id,$gp_project_number,$client_id,$prime_id,$project_name,$abb_name,$sphere,$project_group,$project_manager,$fee_type,$initiation_document,$initiation_document,$document_number,
			$estimated_start,$project_type,$fee_amount,$expense_amount,$expense_type,$market,$submarket,$confidential,$venues,$contact,$address,$city,$state,$zip,
			$email,$phone,$delivery_type,$notes,$accounting_notes,$status,$markup,$retainer,$retention,$per_diem,$perdiem_notes,$sub_fee,$submit_date,$approve_date,$client_portal,
			$receipts_required,$return,$does,$background,$checklist);
		$wpdb->query($querya);
				
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."project_user where project_id=%d",$checklist));
		
		if(!empty($project_manager) and $project_manager != 0)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id) values (%d,%d)",$checklist,$project_manager));
		}
				
		if(!empty($project_team))
		{
			foreach ($project_team as $key => $value)
			{
				if($value != $project_manager)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) 
						values (%d,%d,'Work Team')",$checklist,$value));
				}
			}
		}
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
					values (%d,%s,'Contract')",$checklist,$file_name));
			}
			else
			{
				foreach($_FILES['fileToUpload']['error'] as $error)
				{
					if($error == 1){echo "The Contract document was not uploaded because it was too large.<br/><br/>"; }
					if($error == 2){echo "The Contract document was not uploaded because it was too large.<br/><br/>"; }
					if($error == 3){echo "The Contract document was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					//if($error == 4){echo "No attachement.<br/><br/>"; }
					if($error > 4 or $error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
				}
			}
		}
		//Proposal Docs***************************************************************************************
		foreach($_FILES['proposalUpload']['name'] as $f=> $name)
		{
			$proposal = time()." - ".basename($name);
			$proposal_file = $target_dir . "/" . $proposal;
			if (move_uploaded_file($_FILES["proposalUpload"]["tmp_name"][$f], $proposal_file))
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
					values (%d,%s,'Proposal')",$checklist,$proposal));
				
				echo "The Proposal: ".$proposal. " has been uploaded.<br/><br/>";
			}
			else
			{
				foreach($_FILES['proposalUpload']['error'] as $error)
				{
					if($error == 1){echo "The Proposal document was not uploaded because it was too large.<br/><br/>"; }
					if($error == 2){echo "The Proposal document was not uploaded because it was too large.<br/><br/>"; }
					if($error == 3){echo "The Proposal document was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					//if($error == 4){echo "No new attachements were selected.<br/><br/>"; }
				}
			}
		}
		//************************************************************************************************
		$link = get_bloginfo('siteurl').'/?p_action=edit_checklist&ID='.$checklist;	
		if($team=="Finance" and isset($_POST['save-info']))
		{
			$email_results = $wpdb->get_results($wpdb->prepare("select user_email from ".$wpdb->prefix."users where ID=%d",$author));
			$er = $email_results[0]->user_email;
			wp_mail($er,"Checklist needs edits","Please revise the contract checklist per the comments provided at the bottom of the form.  
				You can access the checklist here: ".$link);
		}
		if(isset($_POST['submit-info']))
		{
			//BillyB update "to" once projects are setup
			$to = array('bbannister@programmanagers.com','mmitchell@programmanagers.com','npereira@programmanagers.com');
			wp_mail($to,"New Contract Checklist",$user_name.' has submitted a new contract checklist for your reivew.
				You can review the checklist here: '.$link);
			$message = "Thank you.  The contract checklist has been submitted for processing.<br/><br/>";
		}
		elseif(isset($_POST['save-info']))
		{
			$message = "The checklist has been saved for future use.<br/>You will still need to submit the checklist before it can be processed.<br/><br/>";
		}
		elseif(isset($_POST['approve-info']))
		{
			//wp_mail('bbannister@programmanagers.com',"New Contract Checklist",'what what what');
			$message = "Thank you.  The project has been approved.<br/><br/>";
		}
		//add email notice if some items change
		$changes_array = array();
		if($original_status > 0)
		{
			if($fee_type!=$original_fee_type){array_push($changes_array,array("Fee Type",$original_fee_type,$fee_type));}
			if($initiation_document!=$original_initiation_document){array_push($changes_array,array("Initiation Document",$original_initiation_document,$initiation_document));}
			if($document_number!=$original_document_number){array_push($changes_array,array("Document Number",$original_document_number,$document_number));}
			if($estimated_start!=$original_estimated_start){array_push($changes_array,array("Estimated Start",date('m-d-Y',$original_estimated_start),date('m-d-Y',$estimated_start)));}
			if($project_type!=$original_project_type){array_push($changes_array,array("Project Type",$original_project_type,$project_type));}
			if($fee_amount!=$original_fee_amount){array_push($changes_array,array("Fee Amount","$".number_format($original_fee_amount,2),"$".number_format($fee_amount,2)));}
			if($sub_fee!=$original_sub_fee){array_push($changes_array,array("Sub Fee","$".number_format($original_sub_fee,2),"$".number_format($sub_fee,2)));}
			if($expense_amount!=$original_expense_amount){array_push($changes_array,array("Expense Amount","$".number_format($original_expense_amount,2),"$".number_format($expense_amount,2)));}
			if($expense_type!=$original_expense_type){array_push($changes_array,array("Expense Type",$original_expense_type,$expense_type));}
		}
		if(!empty($changes_array))//add per diem and receipts required filter to the array
		{
			$to = array('bbannister@programmanagers.com','mleizear@programmanagers.com','npereira@programmanagers.com','lharville@programmanagers.com','fgbadamosi@programmanagers.com','lcosenzo@programmanagers.com');
			$email_results = $wpdb->get_results($wpdb->prepare("select user_email from ".$wpdb->prefix."users where ID=%d",$project_manager));
			foreach($email_results as $er)
			{
				array_push($to,$er->user_email);
			}
			$subject = "Contract Checklist Changed";
			$message = $user_name.' has edited the checklist for <a href="'.get_bloginfo('siteurl').'/?p_action=project_card&ID='.$checklist.'">'.$client_name.'</a> as following:<br/><br/>';
			foreach($changes_array as $ca)
			{
				$message .= $ca[0].' changed from '.(empty($ca[1]) ? 'blank' : $ca[1] ).' to '.(empty($ca[2]) ? 'blank' : $ca[2]).'<br/>';
			}
			$message .= '<br/><br/>Click <a href="'.get_bloginfo('siteurl').'/?p_action=project_card&ID='.$checklist.'">Here</a> to review the Project card.';
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail($to,$subject,$message,$headers);
		}
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php echo $message; ?>
		<a href="<?php echo $link;?>">Edit this Checklist</a><br/><br/>
		<a href="<?php bloginfo('siteurl');?>/contract-checklist/">Enter a new Contract Checklist</a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>
		<?php 	
		get_footer();
	}
	else
	{ 
		?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="edit_contract_checklist" enctype="multipart/form-data">
			<div id="content"><h3>Project Details</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<?php 
					if(!empty($project_parent))
					{					
						echo '<li><h3><font color="red">This is an Adserv to:</font></h3>
							<p><a href="?p_action=project_card&ID='.$parent_results[0]->ID.'" target="_blank">
							'.$parent_results[0]->gp_project_number.'</a></p></li>';
					}
					if($team=="Finance")
					{
						$name_results = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$author));
						if($submit_date == 0){$submitted = '';}else{$submitted = ' on '.date('m-d-Y',$submit_date);}
						echo '<li>Project Submitted By:  '.$name_results[0]->display_name.$submitted.'</li>';
						echo '<li><h3>Project Number:</h3><p><input type="text" class="do_input_new full_wdth_me" name="gp_project_number" 
								placeholder="Enter full Project Number here" value="'.$gp_project_number.'"/></p></li>
							<li><h3>Project ID:</h3><p><input type="text" class="do_input_new full_wdth_me" name="gp_id" 
								placeholder="Enter only the GP ID" value="'.$gp_id.'"/></p></li>';
					}
					?>
					<input hidden="hidden" type="text" name="client_id" value="<?php echo $client_id;?>" />
					<li><h3>Client Name:</h3><p><input type="text" class="do_input_new full_wdth_me" name="named_client" value="<?php echo $client_name;?>"
						onkeyup="checkCustomer(this.value);" /></p></li>
					<span id="client_buttons"></span>
						<script type="text/javascript">
						function checkCustomer(vals){
							var span = document.getElementById('client_buttons');
							span.style.display = 'block';
							jQuery.post("<?php bloginfo('siteurl'); ?>/?check_customers=1", {search_term: ""+vals+""}, function(data){
								if(data.length >0) {
									jQuery('#client_buttons').html(data);
								}
							});
						}
						function setCustomer(id,name){
							var myForm = document.forms.edit_contract_checklist;
							var clientId = myForm.elements['client_id'];
							var clientName = myForm.elements['named_client'];
							var span = document.getElementById('client_buttons');
							clientId.value = id;
							clientName.value = name;
							span.style.display = 'none';
						}
						</script>
					<input hidden="hidden" type="text" name="prime_id" value="<?php echo $prime_id;?>" />
					<li><h3>Prime Name:<br/>(enter if contract is not with the client)</h3><p><input type="text" class="do_input_new full_wdth_me" 
						name="prime_name" value="<?php echo $prime_name;?>" onkeyup="checkPrime(this.value);" /></p></li>
					<span id="prime_buttons"></span>
						<script type="text/javascript">
						function checkPrime(vals){
							var span = document.getElementById('prime_buttons');
							span.style.display = 'block';
							jQuery.post("<?php bloginfo('siteurl'); ?>/?check_prime=1", {search_term: ""+vals+""}, function(data){
								if(data.length >0){
									jQuery('#prime_buttons').html(data);
								}
							});
						}
						function setPrime(id,name){
							var myForm = document.forms.edit_contract_checklist;
							var clientId = myForm.elements['prime_id'];
							var clientName = myForm.elements['prime_name'];
							var span = document.getElementById('prime_buttons');
							clientId.value = id;
							clientName.value = name;
							span.style.display = 'none';
						}
						</script>
					<li><h3>Project Name:</h3><p><input type="text" name="project_name" class="do_input_new full_wdth_me" value="<?php echo $project_name;?>"/></p></li>
					<li><h3>Project Abbreviated Name:<br/>(max 25 characters)</h3>
						<p><input type="text" name="abb_name" class="do_input_new full_wdth_me"value="<?php echo $abb_name;?>" maxlength="25"/></p>
					</li>
					<li>
						<h3>Sphere:</h3>
						<p><select class ="do_input_new" name="sphere">
						<option <?php if($sphere=="Higher Ed"){echo "selected=selected";}?>>Higher Ed</option>
						<option <?php if($sphere=="Sphere KMV"){echo "selected=selected";}?>>Sphere KMV</option>
						</select>
						</p>
					</li>
					<li>
						<h3>Group/Cluster:</h3>
						<p><select class="do_input_new" name="project_group">
						<?php
						foreach($all_teams as $at)
						{
							echo '<option value="'.$at->team.'" '.($at->team==$project_group ? 'selected="selected"' : '').'>'.$at->sphere.':  '.$at->team.'</option>';
						}
						?>
						</select></p>
					</li>
					<li>
						<h3>Project Manager:</h3>
						<?php
						$userquery = "select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users 
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where display_name not in ('admin','bbannister','test') and status=1 order by display_name";
						$users = $wpdb->get_results($userquery);
						?>
						<p>
						<?php
						echo '<select name="project_manager" class="do_input_new">';
						echo '<option value="">Select Project Manager</option>';
						foreach($users as $user)
						{
							echo '<option '.($project_manager == $user->ID ? "selected='selected'" : "" ).' value="'.$user->ID.'">'.$user->display_name.'</option>';
						}
						echo '</select>';
						?>
						</p>
					</li>							
					<li>
						<h3>Team Members>:<br/>Hold ctrl to select multiple</h3>
						<p><?php
						echo '<select name="project_team[]" size="10" multiple="multiple">';							
						foreach($users as $user)
						{
							echo '<option '.(in_array($user->ID,$project_team) ? "selected='selected'" : " " ).' value="'.$user->ID.'">'.$user->display_name.'</option>';
						}
						echo '</select>';
						?>
						</p>
					</li>
					<li>
						<h3>Fee Type:</h3>
						<p><select class ="do_input_new" name="fee_type">
						<option <?php if($fee_type=="Fixed Fee"){echo "selected=selected";}?>>Fixed Fee</option>
						<option <?php if($fee_type=="T&M (as used)"){echo "selected=selected";}?>>T&M (as used)</option>
						<option <?php if($fee_type=="T&M (to maximum)"){echo "selected=selected";}?>>T&M (to maximum)</option>
						<option <?php if($fee_type=="Percent Complete"){echo "selected=selected";}?>>Percent Complete</option>
						</p>
						</select>
					</li>
					<li>
						<h3>Initiation Document:</h3>
						<p><select class ="do_input_new" name="initiation_document">
						<?php
						if(empty($initiation_document)){echo '<option selected="selected" value="">Select Document</option>';}?>
						<option <?php if($initiation_document=="Contract"){echo "selected=selected";}?>>Contract</option>
						<option <?php if($initiation_document=="Purchase Order"){echo "selected=selected";}?>>Purchase Order</option>
						<option <?php if($initiation_document=="Letter of Intent"){echo "selected=selected";}?>>Letter of Intent</option>
						<option <?php if($initiation_document=="Executive Override"){echo "selected=selected";}?>>Executive Override</option>								
						</select>
						</p>
					</li>
					<li><h3>Contract:</h3><p><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></p></li>
					<li><h3>Proposal:</h3><p><input class="my-buttons" type="file" name="proposalUpload[]" multiple="multiple" id="proposalUpload" /></p></li>
					<li><h3>Document Number (if any):</h3>
						<p><input type="text" name="document_number" class="do_input_new full_wdth_me"value="<?php echo $document_number;?>"/></p>
					</li>							
					<li><h3>Estimated Start:</h3>
						<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
						<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
						<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
						<p><input type="text" id="start" name="estimated_start" class="full_wdth_me do_input_new" value="<?php echo $estimated_start;?>"/></p>
					</li>
						<script>
						<?php
						$start = date_i18n('m-d-Y',$estimated_start);
						$dd = 180;
						?>
						var myDate=new Date();
						myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
	
						$(document).ready(function() {
							$('#start').datetimepicker({
								showSecond: false,
								timeFormat: 'hh:mm:ss',
								currentText: '<?php _e('Now','ProjectTheme'); ?>',
								closeText: '<?php _e('Done','ProjectTheme'); ?>',
								ampm: false,
								dateFormat: 'mm/dd/yy',
								timeFormat: 'hh:mm tt',
								timeSuffix: '',
								maxDateTime: myDate,
								timeOnlyTitle: '<?php _e('Choose Time','ProjectTheme'); ?>',
								timeText: '<?php _e('Time','ProjectTheme'); ?>',
								hourText: '<?php _e('Hour','ProjectTheme'); ?>',
								minuteText: '<?php _e('Minute','ProjectTheme'); ?>',
								secondText: '<?php _e('Second','ProjectTheme'); ?>',
								timezoneText: '<?php _e('Time Zone','ProjectTheme'); ?>'
							});
						});
						</script>
					<li><h3>Project Type:</h3>
						<p><select class ="do_input_new" name="project_type">
						<option value=""><?php echo "Select Project Type";?></option>
						<option <?php if($project_type=="Project Definition"){echo "selected=selected";}?>>Project Definition</option>
						<option <?php if($project_type=="MAS Planning"){echo "selected=selected";}?>>MAS Planning</option>
						<option <?php if($project_type=="P3 Advisory"){echo "selected=selected";}?>>P3 Advisory</option>
						<option <?php if($project_type=="P3 Owner's Rep"){echo "selected=selected";}?>>P3 Owner's Rep</option>
						<option <?php if($project_type=="Full Service PM"){echo "selected=selected";}?>>Full Service PM</option>
						</select>
						</p>
					</li>
					<li><h3>Fee Amount:</h3>
						<p>$<input type="number" step=".01" min="0" name="fee_amount" class="do_input_new" value="<?php echo $fee_amount;?>"
							onblur="updateTotal();"/></p>
					</li>	
					<li><h3>Sub Fee Amount:</h3>
						<p>$<input type="number" step=".01" min="0" name="sub_fee" class="do_input_new" value="<?php echo $sub_fee;?>"
							onblur="updateTotal();"/></p>
					</li>
					<li><h3>Expense Amount:</h3>
						<p>$<input type="number" step=".01" min="0" name="expense_amount" class="do_input_new" value="<?php echo $expense_amount;?>"
							onblur="updateTotal();"/></p>
					</li>
					<li><h3>Total Contract:</h3><p>$<input type="number" name="total_contract" class="do_input_new"
						value="<?php echo ($fee_amount + $sub_fee + $expense_amount);?>" disabled /></p></li>
					<script type="text/javascript">
					function updateTotal(){
						var myForm = document.forms.edit_contract_checklist;
						var feeBD = myForm.elements['fee_amount'];
						var feeSub = myForm.elements['sub_fee'];
						var expense = myForm.elements['expense_amount'];
						var total = myForm.elements['total_contract'];
						var amount = ((feeBD.value*100)+(feeSub.value*100)+(expense.value*100))/100;
						
						total.value = amount;
					}
					</script>
					<li>
						<h3>Expense Type:</h3>
						<p><select class ="do_input_new" name="expense_type">
						<option <?php if(empty($expense_type)){echo "value='' selected='selected'";}?>>Select Expense Type</option>
						<option <?php if($expense_type=="Reimbursable"){echo "selected=selected";}?>>Reimbursable</option>
						<option <?php if($expense_type=="No-Bill"){echo "selected=selected";}?>>No-Bill</option>
						<option <?php if($expense_type=="Unlimited"){echo "selected=selected";}?>>Unlimited</option>
						</select>
						</p>
					</li>
					<li><h3>Market:</h3>
						<script>
						function display_subcat(vals){
							jQuery.post("<?php bloginfo('siteurl'); ?>/?get_subcats_for_me=1", {queryString: ""+vals+""}, function(data){
								if(data.length >0) {
									jQuery('#sub_market').html(data); 
								}
							});	
						}
						</script>
						<p>
						<?php
						$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
						$terms = get_terms( "project_cat", $args );

						echo '<select name="project_cat_cat" class="do_input_new" id="do_input_new" onchange="display_subcat(this.value)">';
						echo "<option value=''>Select Market</option>";

						foreach($terms as $term)
						{
							echo '<option '.($market == $term->term_id ? "selected='selected'" : " " ).' value="'.$term->term_id.'">'.$term->name.'</option>';

						}
						echo '</select>';
															
						echo '<br/><span id="sub_market">';
						if(!empty($market))
						{
							$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$market;
							$sub_terms2 = get_terms( 'project_cat', $args2 );	
							
							echo '<select class="do_input_new" name="subcat">';
							echo '<option value="">Select Subcategory</option>';
							$selected1 = $submarket;
									
							foreach($sub_terms2 as $sub)
							{
								echo '<option '.($submarket == $sub->term_id ? "selected='selected'" : " " ).' value="'.$sub->term_id.'">'.$sub->name.'</option>';
							}
							echo "</select>";
						}	
						echo '</span>';
						?>
						</p>
					</li>
					<li><h3>Confidential:</h3>
						<p><input type="checkbox" name="confidential" <?php if($confidential=="on"){echo "checked=checked";}?>></p>
					</li>
					<li><h3>Venues:</h3>
						<p><input type="checkbox" name="venues"<?php if($venues=="on"){echo "checked=checked";}?>></p>
					</li>
					<li><h3>Per Diem</h3><p><input type="checkbox" name="per_diem"<?php if($per_diem==1){echo " checked=checked";}?> onclick="showHidePerDiemNotes();"></p></li>
					<script type="text/javascript">
					function showHidePerDiemNotes(){
						var myForm = document.forms.edit_contract_checklist;
						var perDiemNotes = document.getElementById('123');
						var perDiemBox = myForm.elements['per_diem'];
						
						if(perDiemBox.checked==true){
							perDiemNotes.style.display = 'inline';
						}
						else{
							perDiemNotes.style.display = "none";
						}
					}
					</script>
					<span id="123" style="display:none;">
					<li><h3>Per Diem Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
							placeholder="<?php echo "Reference each per diem limitation"; ?>"  name="perdiem_notes">
							<?php if(!empty($perdiem_notes)){echo $perdiem_notes;}?></textarea></p>
					
					</li></span>
					<li><h3>Receipts Required:</h3>
						<p><input type="checkbox" name="receipts" <?php if($receipts==1){echo 'checked="checked"';}?> ></p>
					</li>
					<li><h3>Return and/or Destroy Requirement:</h3>
						<p><input type="checkbox" name="return" <?php if($return==1){echo 'checked="checked"';}?> ></p>
					</li>
					<li><h3>DOES:</h3>
						<p><input type="checkbox" name="does" <?php if($does==1){echo 'checked="checked"';}?> ></p>
					</li>
					<li><h3>Background Checks Required:</h3>
						<p><input type="checkbox" name="background" <?php if($background==1){echo 'checked="checked"';}?> ></p>
					</li>
					<li><h3>10% Markup:</h3>
						<p><input type="checkbox" name="markup"<?php if($markup=="on"){echo 'checked="checked"';}?>></p>
					</li>
					<li><h3>Retainer:</h3>
						<p>$<input type="number" step=".01" class="do_input_new" name="retainer" value="<?php echo $retainer;?>" /></p>
					</li>
					<li><h3>Retention:</h3>
						<p><input type="number" step=".01" min="0" max="100" class="do_input_new" name="retention" value="<?php echo $retention.'%';?>" /></p>
					</li>
					<li><h3>Client Portal:</h3>
						<p><input type="text" class="do_input_new full_wdth_me" name="client_portal" value="<?php echo $client_portal;?>" /></p>
					</li>
				</ul>
				</div>
				</div>
			</div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Documents";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						$document_results = $wpdb->get_results($wpdb->prepare("select project_doc_name from ".$wpdb->prefix."project_docs where project_id=%d",$checklist));
						if(!empty($document_results))
						{
							foreach($document_results as $doc)
							{
								echo '<li><a href="/wp-content/project_docs/'.rawurlencode($doc->project_doc_name).'" target="_blank" >'
									.$doc->project_doc_name.'</a></li>';
							}
						}
						else{echo "No documents attached.";}
						?>
						</ul>
					</li>
				</ul>
			</div></div>
			<div id="content"><h3><?php echo "Invoicing Details";?></h3><br/>
				<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
					<li><h3>Contact:</h3><p><input type="text" name="contact" class="do_input_new full_wdth_me" value="<?php echo $contact;?>"/></p></li>
					<li><h3>Address:</h3><p><input type="text" name="address" class="do_input_new full_wdth_me" value="<?php echo $address;?>"/></p></li>
					<li><h3>City:</h3><p><input type="text" name="city" class="do_input_new full_wdth_me" value="<?php echo $city;?>"/></p></li>
					<li><h3>State:</h3>
						<p><select name="state" class="do_input_new">
						<?php
						$states_query = "select * from ".$wpdb->prefix."states order by state_abbreviation";
						$states_result = $wpdb->get_results($states_query);
						echo '<option value="">Select State</option>';
						foreach($states_result as $state)
						{
							echo '<option value="'.$state->state_id.'" '.($state->state_id == $state_id ? "selected='selected'" : "" ).'>'.$state->state_abbreviation.'</option>';
						}
						?>
						</select>
						</p>
					</li>
					<li><h3>Zip:</h3><p><input type="text" name="zip" class="do_input_new full_wdth_me" value="<?php echo $zip;?>"/></p></li>
					<li><h3>Email:</h3><p><input type="text" name="email" class="do_input_new full_wdth_me" value="<?php echo $email;?>"/></p></li>
					<li><h3>Phone:</h3><p><input id="phone" type="text" name="phone" class="do_input_new full_wdth_me" value="<?php echo $phone;?>"/></p></li>				
					<li><h3>Delivery Type:</h3>
						<p><select class ="do_input_new" name="delivery_type">
						<option value="">Select Delivery</option>
						<option <?php if($delivery_type=="Email"){echo "selected=selected";}?>>Email</option>
						<option <?php if($delivery_type=="Hard Copy"){echo "selected=selected";}?>>Hard Copy</option>
						<option <?php if($delivery_type=="Both"){echo "selected=selected";}?>>Both</option>
						</select>
						</p>
					</li>
					</ul>
				</div>
				</div>
			</div>	
			<div id="content"><h3>Project Details</h3><br/>
				<div class="my_box3">
				<div class="padd10">							
					<ul class="other-dets_m">
					<li><h3>Internal Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
							placeholder="Make any notes about this project that other staff should know" name="notes"><?php echo $notes;?></textarea>
						</p>
					</li>
					<?php if($team == "Finance" or !empty($accounting_notes)):?>
					<li><h3>Accounting Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" name="accounting_notes"
						placeholder="Make any comments about necessary edits for processing"><?php echo $accounting_notes;?></textarea>
						</p>
					</li>
					<?php endif;?>
					</ul>        
				</div>
				</div>
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li>
					<?php 
					if($team =="Finance")
					{
						?>
						<p><input type="submit" name="save-info" class="my-buttons" value="Request Edits" />
						&nbsp;
						<input type="submit" name="submit-info" class="my-buttons" value="Submit Project" />
						&nbsp;
						<?php
						if($status <3)
						{
							echo '<input type="submit" name="approve-info" class="my-buttons" value="Approve Project" />';
						}
						?>
						</p?
						<?php
						
					}
					else
					{
						if($team != 'Information Technology')
						{ 
							?>
							<p><input type="submit" name="save-info" class="my-buttons" value="Save for Later" />
							&nbsp;
							<input type="submit" name="submit-info" class="my-buttons" value="Submit Project" />
							</p>
							<?php 
						} 
					} 
					?>
					</li>
				</ul>
			</div>
		</form>
		</div>
		</div>
		<?php 
	}
	get_footer(); ?>