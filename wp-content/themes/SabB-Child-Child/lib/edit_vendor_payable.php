<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	function sitemile_filter_ttl($title){return "Edit Vendor Payable";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	$sphere = $rightsresults[0]->sphere;
	$personal_project = $rightsresults[0]->personal_project;
	
	$expense_report = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare(
		"select ".$wpdb->prefix."vendors.vendor_id,project_id,expense_billable,fee_billable,expense_date,vendor_fee,vendor_name,vendor_expense,project_name,abbreviated_name,gp_id,
			assigned_to,billed_month,invoice_number,expense_type_id,expense_type,expense_status,".$wpdb->prefix."vendor_payables.notes,
			".$wpdb->prefix."vendor_payables.submitted_by,".$wpdb->prefix."vendor_payables.submit_date,".$wpdb->prefix."vendor_payables.approved_date,set_for_approval,
			".$wpdb->prefix."vendor_payables.approved_by
		from ".$wpdb->prefix."vendor_payables
		left join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
		left join ".$wpdb->prefix."projects on ".$wpdb->prefix."vendor_payables.project_id=".$wpdb->prefix."projects.ID
		where vendor_payable_id=%d",
		$expense_report));
	$vendor_id = $details[0]->vendor_id;
	$vendor_name = $details[0]->vendor_name;
	$invoice_number = $details[0]->invoice_number;
	$project_id = $details[0]->project_id;
	$date = $details[0]->expense_date;
	$expense_type_id = $details[0]->expense_type_id;
	$fee_amount = $details[0]->vendor_fee;
	$expense_amount = $details[0]->vendor_expense;
	$expense_type = $details[0]->expense_type;
	$expense_notes = $details[0]->notes;
	$expense_billable = $details[0]->expense_billable;
	$fee_billable = $details[0]->fee_billable;
	$submitted_by = $details[0]->submitted_by;
	$submit_date = $details[0]->submit_date;
	$assigned_to = $details[0]->assigned_to;
	$month_billed = $details[0]->billed_month;
	
	$overhead_array = array("0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
		"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
		"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
		"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
		"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV");
	if(in_array($project_id,$overhead_array)){$expense_type = "No-Bill";}
	
	$editors = array(11,94,293,235);
	
	if($details[0]->expense_status >= 2 and !in_array($uid,$editors)){$disabled = "disabled"; $delete='no';}
	elseif($uid!=$assigned_to and $uid!=293 and $uid!=94 and $uid!=11 and $uid!=235){$disabled = "disabled";}
	else{$disabled = "";}
	if(isset($_POST['approve-info']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set set_for_approval=1 where vendor_payable_id=%d",$expense_report));
		wp_redirect(get_bloginfo('siteurl')."/expense-approvals/"); 
		exit;
	}
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']) or isset($_POST['submit-info']) or isset($_POST['submit-info-two']) or isset($_POST['save-info-three']))
	{
		if(isset($_POST['submit-info']) or isset($_POST['submit-info-two'])){$status = 1;}else{$status = 0;}
		
		$vendor_id = $_POST['vendor_id'];
		$date = $_POST['date'];if(empty($date)){$date = $details[0]->expense_date;}
		$invoice = $_POST['invoice'];
		$project_id = $_POST['project'];if(empty($project_id)){$project_id=$details[0]->project_id;}
		$expense = $_POST['expense'];
		$billable = $_POST['billable'];
		if(empty($billable)){$billable = $_POST['billable_a'];}
		$fee_bill = $_POST['fee_billable'];
		if(empty($fee_bill)){$fee_bill = $_POST['fee_billable_a'];}
		$fee = $_POST['fee_amount'];
		if($fee==0){$fee_bill=3;}
		$expense_amount = $_POST['expense_amount'];
		if($expense_amount==0){$billable=3;}
		$billed_month = $_POST['billed_month'];
		$notes = $_POST['notes'];
		$assignment = $_POST['assign_to'];
		$submitted_by = $uid;
		$submit_date = time();
		$approved_date = 0;
		$set_for_approval = 0;
		$approved_by = 0;
		
		if($status == 1)
		{
			if(empty($vendor_id) or empty($invoice) or empty($project_id) or ($fee+$expense_amount == 0))
			{
				$status = 0;
			}
		}
		if(isset($_POST['save-info-three']))
		{
			$status = $details[0]->expense_status;
			$submit_date = $details[0]->submit_date;
			$submitted_by = $details[0]->submitted_by;
			$approved_date = $details[0]->approved_date;
			$set_for_approval = $details[0]->set_for_approval;
			$approved_by = $details[0]->approved_by;
		}
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set vendor_id=%d,expense_date=%d,invoice_number=%s,project_id=%s,expense_type_id=%d,vendor_fee=%f,vendor_expense=%f,
			expense_billable=%d,fee_billable=%d,submit_date=%d,submitted_by=%d,approved_date=%d,set_for_approval=%d,approved_by=%d,expense_status=%d,assigned_to=%d,billed_month=%d,notes=%s where vendor_payable_id=%d",
			$vendor_id,$date,$invoice,$project_id,$expense,$fee,$expense_amount,$billable,$fee_bill,$submit_date,$submitted_by,$approved_date,$set_for_approval,$approved_by,$status,$assignment,$billed_month,$notes,$expense_report));
			
		if($assignment != $assigned_to)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendor_payables_status (assigned_by,assigned_to,status_date,payable_id)
				values(%d,%d,%d,%d)",$uid,$assignment,$now,$expense_report));
		}
		
		echo '<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">';
		$current_dir = getcwd();
		$target_dir = $current_dir."/wp-content/expense_backup";
		foreach ($_FILES['fileToUpload']['name'] as $f => $name)
		{
			$file_name = time()." - ".basename($name);
			$target_file = $target_dir . "/" . $file_name;
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$f], $target_file))
			{
				$file_message = "The file: ".$file_name. " has been uploaded.";
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendor_backup (expense_id,expense_filename) values (%d,%s)",$expense_report,$file_name));
			}
			else
			{
				foreach($_FILES['fileToUpload']['error'] as $error)
				{
					if($error == 1){$file_message = "File was not uploaded because it was too large.<br/><br/>"; }
					if($error == 2){$file_message = "File was not uploaded because it was too large.<br/><br/>"; }
					if($error == 3){$file_message = "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					if($error == 4){$file_message = "No new attachements were selected.<br/><br/>"; }
				}
			}
		}
		
		if($status == 0)
		{
			$message = "Your transaction has been SAVED.";
			if(empty($date)){$message .= "<br/>You will need to enter a date before submitting.";}
			if(empty($vendor_id)){$message .= "<br/>You will need to select a vendor before submitting.";}
			if(empty($invoice)){$message .= "<br/>You will need to enter an invoice number before submitting.";}
			if(empty($project_id)){$message .= "<br/>You will need to select a project before submitting.";}
			if(empty($expense)){$message .= "<br/>You will need to select an expense before submitting.";}
			if(empty($billable)){$message .= "<br/>You will need to select billable or no-bill for the expense line before submitting.";}
			if(empty($fee_bill)){$message .= "<br/>You will need to select whether the fee is billable or no-bill before submitting.";}
			if($fee+$expense_amount == 0){$message .= "<br/>You will need to enter a fee or expense amount before submitting.";}
			echo $message;
			echo '<br/>Files:  '.$file_message;
		}
		else
		{
			echo "The Payable has been submitted for processing.<br/><br/>";
			echo '<br/>Files:  '.$file_message;
		}
		echo '<br/><br/><a href="'.get_bloginfo('siteurl').'/vendor-payable/">Enter a new Payable</a><br/><br/>
			<a href="'.get_bloginfo('siteurl').'/my-vendor-payables/">My Vendor Submissions</a><br/><br/>
			<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>
			</div></div></div></div></div>';
		get_footer();
		$_POST = array();
	}
	elseif(isset($_POST['reject-info']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set expense_status=0 where vendor_payable_id=%d",$expense_report));
		
		wp_redirect(get_bloginfo('siteurl').'/expense-approvals/'); 
	}
	elseif(isset($_POST['delete-info']))
	{
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."vendor_payables where vendor_payable_id=%d",$expense_report));
		
		echo '<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">';
		echo 'The transaction has been deleted.';				
		echo '<br/><br/><a href="'.get_bloginfo('siteurl').'/vendor-payable/">Enter a new Payable</a><br/><br/>
			<a href="'.get_bloginfo('siteurl').'/my-vendor-payables/">My Vendor Submissions</a><br/><br/>
			<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>
			</div></div></div></div></div>';
		get_footer();				
		$_POST = array();
	}
	else
	{
?> 	
		<form name="edit_vendor_exp" id="edit_vendor_exp" method="post"  enctype="multipart/form-data">
			<div id="main_wrapper">
				<div id="main" class="wrapper">
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<?php
				$all_users_results = $wpdb->get_results("select display_name,user_id from ".$wpdb->prefix."users
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where status=1 order by display_name");
					
				if($disabled == "" and $uid==$assigned_to)
				{
					echo '<li><p><input type="submit" name="save-info" class="my-buttons" value="Save" />&nbsp;&nbsp;
					<input type="submit" name="submit-info" class="my-buttons-submit" value="Submit" /></p></li>
					<li>&nbsp;</li>';
				}
				echo '<style>input[type=number]{width:200px;}</style>';
				$queryactive = $wpdb->prepare("select ".$wpdb->prefix."projects.ID,abbreviated_name,".$wpdb->prefix."projects.gp_id,expense_type 
					from ".$wpdb->prefix."projects 
					inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
					where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status=2 and project_parent=0",$uid);
				if($uid==293 or $uid==94 or $uid==11 or $uid==235)
				{
					$queryactive = $wpdb->prepare("select ".$wpdb->prefix."projects.ID,abbreviated_name,".$wpdb->prefix."projects.gp_id,expense_type 
					from ".$wpdb->prefix."projects 
					where project_parent=0
					order by ".$wpdb->prefix."projects.gp_id");
				}
				
				$resultactive = $wpdb->get_results($queryactive);
				
				$othercodesquery = "select * from ".$wpdb->prefix."other_project_codes where other_project_code_id!=1 and expense_available=1
					order by other_project_code_name";
				$othercodesresults = $wpdb->get_results($othercodesquery);
				
				$expense_query = "select * from ".$wpdb->prefix."vendor_expense_codes order by v_exp_name";
				if($sphere != "functional"){$expense_query = "select * from ".$wpdb->prefix."vendor_expense_codes where rights='project' order by v_exp_name";}
				$expense_results = $wpdb->get_results($expense_query);
				
				$end = strtotime(date('Y-m-d'));
				$start = $end - 86400 * 60;
				
				if($details[0]->expense_status < 2)
				{
					echo '<li><h3>Assign to</h3><p><select name="assign_to" class="do_input_new" '.$disabled.' >';
					foreach($all_users_results as $aur)
					{
						echo '<option value="'.$aur->user_id.'" '.($aur->user_id==$assigned_to ? "selected='selected'" : "").'>'.$aur->display_name.'</option>';
					}
					echo '</select></p></li>';
				}
				if($details[0]->expense_status > 1)
				{
					echo '<li><h3>Invoice Date</h3><p>'.date('m-d-Y',$date).'</p></li>';
				}
				else{
					echo '<li><h3>Invoice Date</h3><p><select name="date" class="do_input_new" '.$disabled.' >';
					for ($i = $end; $i >= $start; $i = $i - 86400)
					{echo '<option value="'.$i.'" '.($i==$date ? 'selected="selected"' : '' ).'>'.date( 'm-d', $i).'</option>';}
					echo '</select></li>';
				}
				echo '<input type="hidden" name="vendor_id" value="'.$vendor_id.'" />
					<li><h3>Vendor</h3><p><input type="text" name="vendor_name" value="'.$vendor_name.'" class="do_input_new full_wdth_me" 
						onkeyup="checkVendor(this.value);" '.$disabled.' title="You must click a vendor from the list"/></p></li>';
					?>
					<span id="vendor_buttons"></span>
					<script type="text/javascript">
						function checkVendor(vals)
						{
							var myForm = document.forms.edit_vendor_exp;
							var vendorName = myForm.elements['vendor_name'];
							var span = document.getElementById('vendor_buttons');
							span.style.display = 'block';
							jQuery.post("<?php bloginfo('siteurl'); ?>/?check_vendor=1", {search_term: ""+vals+""}, function(data){
								if(data.length >0) {
									jQuery('#vendor_buttons').html(data);
								}
							});
							vendorName.style.borderColor = "red";
							vendorName.style.borderWidth = "medium";
						}
						function setVendor(id,name){
							var myForm = document.forms.edit_vendor_exp;
							var vendorId = myForm.elements['vendor_id'];
							var vendorName = myForm.elements['vendor_name'];
							var span = document.getElementById('vendor_buttons');
							vendorId.value = id;
							vendorName.value = name;
							span.style.display = 'none';
							vendorName.style.borderColor = "#ccc";
							vendorName.style.borderWidth = "1px";
						}
					</script>
					<?php
				echo '<li><h3>Vendor Invoice Number</h3><p><input type="text" name="invoice" value="'.$invoice_number.'" class="do_input_new full_wdth_me" '.$disabled.' /></p></li>';	
				echo '<li><h3>Project</h3><p><select class="do_input_new" name="project" onChange="budget();" '.$disabled.' ><option value="">Select Project</option>';
				if($disabled=="")
				{
					foreach ($resultactive as $active)
					{
						echo '<option value="'.$active->ID.'" '.($active->ID == $project_id ? 'selected="selected"' : '' ).'>
							'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
					}
					foreach ($othercodesresults	as $othercode)
					{
						echo '<option value="'.$othercode->other_project_code_value.'" '.($project_id===$othercode->other_project_code_value ? 'selected="selected"' : '' ).' >
						'.$othercode->other_project_code_name.'</option>';
					}
				}
				else
				{
					if(!empty($details[0]->abbreviated_name)){$p_name = $details[0]->abbreviated_name;}
					elseif(!empty($details[0]->project_name)){$p_name = $details[0]->project_name;}
					elseif(!empty($details[0]->gp_id)){$p_name = $details[0]->gp_id;}
					else{$p_name = $details[0]->project_id;}
					echo '<option selected="selected">'.$p_name.'</option>';
				}
				echo '</select></li>';
				echo '<li><h3>Expense</h3><p><select class ="do_input_new" name="expense" '.$disabled.' ><option value="">Select Expense</option>';
				foreach($expense_results as $code)
				{
					echo '<option value="'.$code->vendor_exp_code_id.'" '.($code->vendor_exp_code_id == $expense_type_id ? 'selected="selected"' : '' ).'>
						'.$code->v_exp_name.'</option>';
				}
				echo '</select>';
				
				echo '<input type="hidden" name="fee_billable_a" value="'.$fee_billable.'" />';
				echo '<li><h3>Fee Amount</h3><p><input type="number" class="do_input_new" step=".01" name="fee_amount" value="'.floatval($fee_amount).'" '.$disabled.' />
					<select class="do_input_new" name="fee_billable" '.$disabled.' >
					<option value="1" '.($fee_billable == 1 ? 'selected="selected"' : '' ).' >Billable</option>
					<option value="3" '.($fee_billable == 3 ? 'selected="selected"' : '' ).' >No-Bill</option>
					</select></p>
					</li>';
				echo '<li><h3>Expense Amount</h3><p><input type="number" class="do_input_new" step=".01" name="expense_amount" value="'.floatval($expense_amount).'" '.$disabled.' />';
				echo '<input type="hidden" name="billable_a" value="'.$expense_billable.'" />';
				echo '<select class="do_input_new" name="billable" '.$disabled.' >
					<option value="1" '.($expense_billable == 1 ? 'selected="selected"' : '' ).' >Billable</option>
					<option value="3" '.($expense_billable == 3 ? 'selected="selected"' : '' ).' >No-Bill</option>
					</select></p></li>';
				echo '<li><h3>Month Billed to Client<br/>(month services being billed<br/>not month actually billing)</h3>
						<p><select name="billed_month" class="do_input_new" >';
						
				for($i=0;$i<6;$i++)
				{
					$date = strtotime(date('Y-m-01',time()) .' - '.$i.' months');
					echo '<option value="'.$date.'" '.($month_billed==$date ? 'selected="selected"' : '').'>'.date('m-Y',$date).'</option>';
				}
				
				echo '</select></p></li>';
				echo '<li><h3>Notes</h3><p><textarea rows="6" cols="60" class="full_wdth_me do_input_new" name="notes" '.$disabled.' >'.$expense_notes.'</textarea></li>';
				echo '<li>&nbsp;</li>';
			?>
			<script language="javascript" type="text/javascript">
				function budget()
				{
					var myForm = document.forms.edit_vendor_exp;
					var myProject = myForm.elements['project'];
					var myBillable = myForm.elements['billable'];
					var myBillableA = myForm.elements['billable_a'];
					var feeBillable = myForm.elements['fee_billable'];
					var feeBillableA = myForm.elements['fee_billable_a'];
					
					var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
						"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
						"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
						"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
						"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV"
					];
					var noBill = [<?php
						for($i=0,$b=0;$i<count($resultactive);$i++)
						{
							if($resultactive[$i]->expense_type == "No-Bill")
							{
								if($b>0){echo ",";}
								echo '"'.$resultactive[$i]->ID.'"';
								$b++;
							}
						}
						?>];
						
					if(overhead.indexOf(myProject.value) != -1 || noBill.indexOf(myProject.value) != -1){
						myBillable.value="3"; myBillable.disabled = true; myBillableA.value = "3";
						feeBillable.value="3"; feeBillable.disabled = true; feeBillableA.value = "3";
					}
					else{
						myBillable.disabled = false;
						feeBillable.disabled = false;
					}
				}
				function checkFilesize(){
					var myForm = document.forms.edit_vendor_exp;
					var myControls = myForm.elements['fileToUpload[]'];
					var totalSize = 0;
					var saveInfo = myForm.elements['save-info'];
					var saveInfoTwo = myForm.elements['save-info-two'];
					var submitInfo = myForm.elements['submit-info'];
					var submitInfoTwo = myForm.elements['submit-info-two'];
					
					for(i=0;i<myControls.files.length;i++){
						totalSize += myControls.files[i].size;
					}
					if(totalSize > (8 * 1024 * 1024)){
						saveInfo.disabled = true;
						saveInfoTwo.disabled = true;
						submitInfo.disabled = true;
						submitInfoTwo.disabled = true;
						saveInfo.value = 'Files too big';
						saveInfoTwo.value = 'Files too big';
						submitInfo.value = 'Files too big';
						submitInfoTwo.value = 'Files too big';
						alert('Your total size of your files is too large (8MB max).  Please reduce file sizes or upload files a few at a time');
					}
					else{
						saveInfo.disabled = false;
						saveInfoTwo.disabled = false;
						submitInfo.disabled = false;
						submitInfoTwo.disabled = false;
						saveInfo.value = 'SAVE FOR LATER';
						saveInfoTwo.value = 'SAVE FOR LATER';
						submitInfo.value = 'SUBMIT';
						submitInfoTwo.value = 'SUBMIT';
					}
				}
				function confirmGP(){
					var myForm = document.forms.edit_vendor_exp;
					var saveInfoThree = myForm.elements['save-info-three'];
					var confirmButton = myForm.elements['confirm_gp'];
					
					if(confirmButton.checked==true){
						saveInfoThree.disabled = false;
					}
					else{
						saveInfoThree.disabled = true;
					}
				}
			</script>
			<?php
			if($details[0]->expense_status==0){$created = "Created";}else{$created="Submitted";}
			if($disabled == "")
			{
				if($uid==$assigned_to)	
				{	
					echo '<li><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" onChange="checkFilesize();" /></li>
						<li>&nbsp;</li>
						<li><input type="submit" name="save-info-two" class="my-buttons" value="Save" />&nbsp;&nbsp;';
					if($delete != "no")
					{
						echo '<input type="submit" name="delete-info" class="my-buttons" value="Delete" />&nbsp;&nbsp;';
					}
					echo '<input type="submit" name="submit-info-two" class="my-buttons-submit" value="Submit" /></li>';
				}
				if($current_user->ID==11 or $current_user->ID==293 or $current_user->ID==94 or $current_user->ID==235)
				{
					if(($current_user->ID==11 or $current_user->ID==293 or $current_user->ID==94 or $current_user->ID==235) and $details[0]->expense_status==1)
					{
						echo '<li>&nbsp;</li>';
						echo '<li><input type="submit" name="approve-info" class="my-buttons" value="Approve" />&nbsp;&nbsp;';
						if($delete != "no")
						{
							echo '<input type="submit" name="delete-info" class="my-buttons" value="Delete" />&nbsp;&nbsp;';
						}
						echo '<input type="submit" name="reject-info" class="my-buttons-submit" value="Reject" /></li>';
					}
					echo '<li><h3>Confirm Updates are recorded in GP:</h3><input type="checkbox" name="confirm_gp" class="do_input_new" onclick="confirmGP();"/></p></li>';
					echo '<li><input type="submit" name="save-info-three" value="save" class="my-buttons" disabled="disabled" /></li>';
					$submitted_by_user = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$submitted_by));
					echo '<li>&nbsp;</li>';
					echo '<li>'.$created.' By:  '.$submitted_by_user[0]->display_name.' on '.date('m-d-Y',$submit_date).'</li>';
				}
			}
			elseif($current_user->ID==11 or $current_user->ID==293 or $current_user->ID==94 or $current_user->ID==235)
			{
				$submitted_by_user = $wpdb->get_results($wpdb->prepare("select display_name from ".$wpdb->prefix."users where ID=%d",$submitted_by));
				echo '<li>&nbsp;</li>';
				echo '<li>'.$created.' By:  '.$submitted_by_user[0]->display_name.' on '.date('m-d-Y',$submit_date).'</li>';
			}
			?>
		</ul>
</div>
</div></div>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10">
			<h3>Tips:</h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					You must select a vendor the populates from the search.  If the vendor doesn't exist in the list, contact Maresha Leizear before proceeding.
					</ul>
				</li>
			</ul>
			<h3><?php echo "Attached Backup";?></h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
						$backup_result = $wpdb->get_results($wpdb->prepare("select vendor_backup_id,expense_filename from ".$wpdb->prefix."vendor_backup 
							where expense_id=%d",$expense_report));
						if(empty($backup_result)){echo "You don't have any backup attached yet.";}
						else
						{	
							echo '<table width="100%">';
							foreach ($backup_result as $backup)
							{
								echo '<tr><th><a href="/wp-content/expense_backup/'.rawurlencode($backup->expense_filename).'" target="_blank" >'
									.$backup->expense_filename.'</a></th>';
								//echo '<th><a href="/?p_action=delete_backup&ID='.$backup->expense_backup_id.'" class="my-buttons" style="color:#ffffff;" >Delete</a></th>';
								echo '</tr>';
							}
							echo '</table>';
						}
					?>
					</ul>
				</li>
			</ul>
		</div></div>
		</div></div>
				</form>
<?php }  
	get_footer();
?>