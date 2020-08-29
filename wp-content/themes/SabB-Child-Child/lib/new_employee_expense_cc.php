<?php
function billyB_new_employee_expense_cc()
{ 
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl').'/wp-login.php?redirect_to="corporate-visa-entry"'); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if(isset($_POST['change_kesha'])){$uid = $_POST['kesha_change_field'];}
	if(isset($_POST['change_bev'])){$uid = $_POST['bev_change_field'];}
	if(isset($_POST['change_user'])){$uid = $_POST['changed_user'];}
	
	$employeegpidresult = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$employee_gp_id = $employeegpidresult[0]->gp_id;
	$sphere = $employeegpidresult[0]->sphere;
	$team = $employeegpidresult[0]->team;
	$personal_project = $employeegpidresult[0]->personal_project;
	
	if(isset($_POST['submit-info-one']) or isset($_POST['submit-info-two']) or isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		if($current_user->ID == 41){$uid = $_POST['kesha_current'];}
		if($current_user->ID == 85){$uid = $_POST['bev_current'];}
		if($current_user->ID == 11 or $current_user->ID==94 or $current_user->ID==103 or $current_user->ID==112 or $current_user->ID==102){$uid = $_POST['changed_user'];}//112=Deisy Brangman, 102=Sarah Pearlstein
		
		$records = ($_POST['record']);
		$splits = $_POST['split'];
	
		$submit_date = time();
		
		$project_error = 0;
		$expense_error = 0;
		
		foreach($records as $r)
		{
			if(empty($r['project'])){$project_error=1;}if(empty($r['expense'])){$expense_error =1;}
		}
		$file_name_array = array();
		
		foreach($records as $record)
		{
			$expense_id = $record['id'];
			$project = $record['project'];
			$expense = $record['expense'];
			$billable = $record['billable'];
			if(empty($billable)){$billable=$record['billable_a'];}
			$status = 0;
			$notes = $record['notes'];
			
			if(($project_error + $expense_error == 0) and (isset($_POST['submit-info-one']) or isset($_POST['submit-info-two']))){$status = 1;}
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set project_id=%s,expense_type_id=%d,expense_billable=%d,
				expense_submit_date=%d,employee_expense_status=%d,employee_expense_notes=%s	where employee_expense_id=%d",
				$project,$expense,$billable,$submit_date,$status,$notes,$expense_id));
				
			//Files**************************************************************************************************************************************
			$current_dir = getcwd();
			$target_dir = $current_dir."/wp-content/expense_backup";
			
			foreach($_FILES['file'.$expense_id]['name'] as $f => $name)
			{
				
				$file_name = $submit_date." - ".basename($name);
				
				if(in_array($file_name,$file_name_array))
				{
					for($i=1;$i<1000;$i++)
					{
						$edited_file_name = $file_name.' ('.$i.')';
						if(!in_array($edited_file_name,$file_name_array))
						{
							array_push($file_name_array,$edited_file_name);
							$file_name = $edited_file_name;
							break;
						}
					}
				}
				else{array_push($file_name_array,$file_name);}
				
				$target_file = $target_dir . "/" . $file_name;
				if (move_uploaded_file($_FILES["file".$expense_id]["tmp_name"][$f], $target_file))
				{
					echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
		
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_filename,user_id) values (%d,%s,%d)",
						$expense_id,$file_name,$uid));
				}
				else
				{
					foreach($_FILES['file'.$expense_id]['error'] as $error)
					{
						if($error == 1){echo "File (".$name.") was not uploaded because it was too large.<br/><br/>"; }
						if($error == 2){echo "File (".$name.") was not uploaded because it was too large.<br/><br/>"; }
						if($error == 3){echo "File (".$name.") was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
						//if($error == 4){echo "No attachement.<br/><br/>"; }
						if($error > 4 or $error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
					}
				}
			}
		}
		foreach($splits as $split)
		{
			if($split['percent'] != 0)
			{
				$expense_id = $split['id'];
				$split_amount = $split['split_amount'];
				$project = $split['project'];
				$expense = $split['expense'];
				$billable = $split['billable_a'];
				if(!empty($split['billable'])){$billable = $split['billable'];}
				$notes = $split['notes'];
				$status = 0;
				//get original expense entry
				$expense_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."employee_expenses where employee_expense_id=%d",$expense_id));
				
				if(empty($project)){$project_error=1;}if(empty($expense)){$expense_error =1;}
			
				if(($project_error + $expense_error == 0) and (isset($_POST['submit-info-one']) or isset($_POST['submit-info-two']))){$status = 1;}
				
				$date = $expense_results[0]->expense_date;
				$report = $expense_results[0]->expense_report_id;
				$employee_gp_id = $expense_results[0]->employee_gp_id;
				$amount = $expense_results[0]->expense_amount;
				$new_amount = $amount - $split_amount;
				//modify original expense entry
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_amount=%f,set_for_approval=0,employee_expense_status=%d 
					where employee_expense_id=%d",$split_amount,$status,$expense_id));
				//insert new record
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,
					expense_type_id,expense_quantity,expense_amount,expense_billable,ee_mastercard,expense_submit_date,employee_expense_notes,employee_expense_status) 
					values (%d,%d,%s,%d,%s,%d,1,%f,%d,1,%d,%s,%d)",$report,$uid,$employee_gp_id,$date,$project,$expense,$new_amount,$billable,$submit_date,$notes,$status));
				$insert_id = $wpdb->insert_id;
				//get backup attached to original entry
				$backup_results = $wpdb->get_results($wpdb->prepare("select expense_filename from ".$wpdb->prefix."expense_backup where expense_id=%d",$expense_id));
				//link backup to new expense record
				if(!empty($backup_results))
				{
					foreach($backup_results as $b)
					{
						$file = $b->expense_filename;
						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_report_id,expense_filename,user_id)
							values (%d,%d,%s,%d)",$insert_id,$report,$file,$uid));
					}
				}
			}
		}
		if($project_error + $expense_error == 0)
		{
			
				if(isset($_POST['submit-info-one']) or isset($_POST['submit-info-two'])){echo "The expense report has been submitted for processing.<br/><br/>";}
				if(isset($_POST['save-info']) or isset($_POST['save-info-two'])){echo "The expense report has been saved.  You will still need to submit when finished.<br/><br/>";}
			
	
			echo '<a href="'.get_bloginfo('siteurl').'/new-employee-expense/">Enter a new Expense Report</a><br/><br/>
				<a href="'.get_bloginfo('siteurl').'/my-employee-expenses/">View all your saved and submitted expenses</a><br/><br/>
				<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>
				</div></div></div></div>';
			echo '<div id="content">
					<div class="my_box3">
						<div class="padd10">';
			$_POST = array();
			
		}
		if($project_error + $expense_error != 0)
		{
			echo '<font size="3" color="red"><b><u>Sorry, your report has the following errors:</u></b></font><br/><br/>
					'.($project_error != 0 ? "Missing Project Selection<br/><br/><strong>Please select a Project for each line item.</strong><br/>" : "").'
					'.($expense_error != 0 ? "Missing Expense Code Selection<br/><br/><strong>Please select an Expense code for each line item.</strong><br/>" : "").'
					<br/>
					Your expense report was saved, but not submitted.
					</div></div></div></div>';
			echo '<div id="content">
					<div class="my_box3">
						<div class="padd10">';
		}
		$_POST = array();
	}
	$employeegpidresult = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$employee_gp_id = $employeegpidresult[0]->gp_id;
	$sphere = $employeegpidresult[0]->sphere;
	$team = $employeegpidresult[0]->team;
	$personal_project = $employeegpidresult[0]->personal_project;
	
	$expense_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."employee_expenses
		where ee_mastercard=1 and employee_id=%d and employee_expense_status<2
		order by expense_date",$uid));
?>
					
		<form name="new_exp" method="post" id="my_form" enctype="multipart/form-data" >				
			<ul class="other-dets_m">
<?php
				if(empty($expense_results))
				{
					echo 'You don\'t have any pending Corporate Card expenses at this time';
				}
				else
				{
					$checker = 0;
					foreach($expense_results as $error_check)
					{
						if(empty($error_check->project_id) or $error_check->expense_type_id==0){$checker=1;}
					}
?>
				<li><p><input type="submit" name="save-info" class="my-buttons" value="Save for Later" />&nbsp;&nbsp;
				<input type="submit" name="submit-info-one" class="my-buttons-submit" value="Submit" <?php if($checker == 1){echo 'disabled';}?> /></p></li>
				<li>&nbsp;</li>
				<style>
				input[type=number]{width:95px;}
				</style>
						
<?php 					
				$queryactive = "select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,project_name,gp_id,expense_type,receipt
					from ".$wpdb->prefix."projects 
					inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
					where ".$wpdb->prefix."project_user.user_id ='$uid' and ".$wpdb->prefix."projects.status =2 and project_parent=0";
				$resultactive = $wpdb->get_results($queryactive);							
				
				$othercodesquery = "select * from ".$wpdb->prefix."other_project_codes where expense_available=1 order by other_project_code_name";
				$othercodesresults = $wpdb->get_results($othercodesquery);
				
				$overhead_array =  array();
				foreach($othercodesresults as $o){array_push($overhead_array,$o->other_project_code_value);}
				
				$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights='Project' order by expense_code_name asc";
				if($sphere == 'Functional'){$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights in ('Project','Functional') order by expense_code_name asc";}
				$expensecoderesults = $wpdb->get_results($expensecodequery);
				
				foreach($expense_results as $e)
				{
					$date = $e->expense_date;
					$e_id = $e->employee_expense_id;
					$notes = $e->employee_expense_notes;
					
					echo '<input type="hidden" name="record['.$e_id.'][id]" value="'.$e_id.'" />';
					echo '<font size="3">';
					echo '<div id="d3" style="display:inline-block;">Date: <b>'.date('m-d-y',$date).'</b></div>';
					echo '<div id="d3" style="display:inline-block;">&nbsp;</div>';echo '<div id="d3" style="display:inline-block;">&nbsp;</div>';
					echo '<div id="d3" style="display:inline-block;">Quantity: <b>'.$e->expense_quantity.'</b></div>';
					echo '<div id="d3" style="display:inline-block;">&nbsp;</div>';echo '<div id="d3" style="display:inline-block;">&nbsp;</div>';
					echo '<div id="d3" style="display:inline-block;">Amount: <b>'.number_format($e->expense_amount,2).'</b></div><br/>';
					echo '</font>';
					
					//Split Charges***************************************************************************

					echo '<input type="button" id="split'.$e_id.'" onclick="split('.$e_id.');" value="Split" /><br/>';

					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$e_id.'][project]" 
						'.(empty($e->project_id) ? 'style="border-width:medium;border-color:red;"' : '').'
						onChange="checkProject('.$e_id.',\'record\'); checkForm(); budget('.$e_id.',\'record\');">
						
						<option value="">Project</option>';
					foreach ($resultactive as $active)
					{
						echo '<option value="'.$active->ID.'" '.($e->project_id == $active->ID ? 'selected="selected"' : '').'>'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
						if($active->expense_type=="No-Bill"){array_push($overhead_array,$active->ID);}
					}
					foreach ($othercodesresults	as $othercode)
					{
						echo '<option value="'.$othercode->other_project_code_value.'"'.($e->project_id == $othercode->other_project_code_value ? 
							'selected="selected"' : '').'>'.$othercode->other_project_code_name.'</option>';
					}
					
					if($sphere == "Higher Ed")
					{
						echo '<option value="BRDU2008TTEAMB" '.($e->project_id == "BRDU2008TTEAMB" ? "selected='selected'" : "").'>Team Discretionary</option>';
						array_push($overhead_array,"BRDU2008TTEAMB");
					}
					elseif($sphere =="Sphere KMV")
					{
						echo '<option value="BRDU2008TTEAMW" '.($e->project_id=="BRDU2008TTEAMW" ? "selected='selected'" : "").'>Team Discretionary</option>';
						array_push($overhead_array,"BRDU2008TTEAMW");
					}
					if(!empty($personal_project))
					{
						echo '<option value="'.$personal_project.'" '.($e->project_id==$personal_project ? "selected='selected'" : "").'>Personal</option>';
					}
					
					echo '</select></div>';
					echo '<div id="d3" style="display:inline-block;">
						<select class="do_input_new" name="record['.$e_id.'][expense]" 
							'.(empty($e->expense_type_id) ? 'style="border-width:medium;border-color:red;"' : '').'
							onChange="checkExpense('.$e_id.',\'record\');checkForm();">
						<option value="">Expense</option>';
					
					if(in_array($e->project_id,$overhead_array)){$no_bill = "yes";}else{$no_bill = "no";}
					
					
						
					foreach($expensecoderesults as $code)
					{echo '<option value="'.$code->expense_code_id.'" '.($e->expense_type_id == $code->expense_code_id ? 'selected="selected"' : '').'>'.$code->expense_code_name.'</option>';}
					
					echo '</select></div>';
					echo '<input type="hidden" name="record['.$e_id.'][billable_a]" value="'.($no_bill=="yes" ? 3 : "").'" >';
					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$e_id.'][billable]" '.($no_bill=="yes" ? "disabled" : "").'>
						<option value="1" '.($e->expense_billable == 1 ? 'selected="selected"' : '').'>Billable</option>
						<option value="3" '.($e->expense_billable == 3 ? 'selected="selected"' : '').'>No-Bill</option></select></div>';
					echo '<div id="d3" style="display:inline-block;"><input type="text" size="25" class="do_input_new" name="record['.$e_id.'][notes]" 
						'.(!empty($notes) ? 'value="'.$notes.'"' : '').' placeholder="Notes"/></div><br/>';
					
					$files_query = "select expense_filename from ".$wpdb->prefix."expense_backup where expense_id='$e_id'";
					$files_results = $wpdb->get_results($files_query);
					
					if(!empty($files_results))
					{
						echo '<div id="d3" style="display:inline-block;">';
						foreach($files_results as $f)
						{
							echo '<a href="/wp-content/expense_backup/'.$f->expense_filename.'" target="_blank" class="nice_link">'
							.$f->expense_filename.'</a><br/>';
						}
						echo '</div>';
					}

					echo '<div><input class="my-buttons" type="file" id="file'.$e_id.'" name="file'.$e_id.'[]" multiple="multiple" 
						 onChange="checkFilesize();" />&nbsp;&nbsp;<input type="button" onClick="removeFile('.$e_id.');" value="Clear Files" /></div><br/>';
					
					echo '<span id="span'.$e_id.'" style="display:none;">';
					echo '<input type="hidden" name="split['.$e_id.'][id]" value="'.$e_id.'" />';
					echo 'Percent Split: <input type="number" min="0" max="99" step="1" class="do_input_new" name="split['.$e_id.'][percent]" 
						onchange="split_amount_percent('.$e_id.',\'percent\');" />';
					echo 'Amount Split: <input type="number" min="0" step=".01" class="do_input_new" name="split['.$e_id.'][split_amount]" 
						onchange="split_amount_percent('.$e_id.',\'amount\');" />';
					echo '<br/>';
					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="split['.$e_id.'][project]" 
					style="border-width:medium;border-color:red;"
					onChange="checkProject('.$e_id.',\'split\'); checkForm(); budget('.$e_id.',\'split\');">
					
					<option value="">Project</option>';
					foreach ($resultactive as $active)
					{
						echo '<option value="'.$active->ID.'" >'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
					}
					foreach ($othercodesresults	as $othercode)
					{
						echo '<option value="'.$othercode->other_project_code_value.'">'.$othercode->other_project_code_name.'</option>';
					}
					
					if($sphere == "Higher Ed")
					{
						echo '<option value="BRDU2008TTEAMB" >Team Discretionary</option>';
						array_push($overhead_array,"BRDU2008TTEAMB");
					}
					elseif($sphere == "Sphere KMV")
					{
						echo '<option value="BRDU2008TTEAMW" >Team Discretionary</option>';
						array_push($overhead_array,"BRDU2008TTEAMW");
					}
					if(!empty($personal_project))
					{
						echo '<option value="'.$personal_project.'" >Personal</option>';
					}
					
					echo '</select></div>';
					//need to change the checkExpense function to update correct field
					echo '<div id="d3" style="display:inline-block;">
						<select class="do_input_new" name="split['.$e_id.'][expense]" 
							style="border-width:medium;border-color:red;"
							onChange="checkExpense('.$e_id.',\'split\');checkForm();">
						<option value="">Expense</option>';
					
					foreach($expensecoderesults as $code)
					{echo '<option value="'.$code->expense_code_id.'" >'.$code->expense_code_name.'</option>';}
					
					echo '</select></div>';
					echo '<input type="hidden" name="split['.$e_id.'][billable_a]" value="" />';
					echo '<input type="hidden" name="split['.$e_id.'][orig_amount]" value="'.$e->expense_amount.'" />';
					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="split['.$e_id.'][billable]" '.($no_bill=="yes" ? "disabled" : "").'>
						<option value="1" >Billable</option>
						<option value="3" >No-Bill</option></select></div>';
					echo '<div id="d3" style="display:inline-block;"><input type="text" size="25" class="do_input_new" name="split['.$e_id.'][notes]" 
						'.(!empty($notes) ? 'value="Split: '.$notes.'"' : '').' placeholder="Notes"/></div><br/>';
					echo '</span>';
					echo '<hr><br/>';
				}
?>
				<li>&nbsp;</li>
				<li><input type="submit" name="save-info-two" class="my-buttons" value="Save for Later" />&nbsp;&nbsp;
				<input type="submit" name="submit-info-two" class="my-buttons-submit" value="Submit" <?php if($checker == 1){echo 'disabled';}?> /></li>	
				
				<li>&nbsp;</li>
<?php
				}
?>
			</ul>	
		
			<script language="javascript" type="text/javascript">
				
				function split(x){
					
					var myForm = document.forms.new_exp;
					var button = document.getElementById('split' + x);
					var span = document.getElementById('span' + x);
					var percent = myForm.elements['split[' + x + '][percent]'];
					
					if(button.value == "Split"){
						button.value = "Do Not Split";
						button.style.backgroundColor = "red";
						span.style.display = 'block';
					}
					else{
						button.value = "Split";
						button.style.backgroundColor = "transparent";
						span.style.display = 'none';
						percent.value = 0;
					}
					
				}
				function split_amount_percent(x,field){
					
					var myForm = document.forms.new_exp;
					var percent = myForm.elements['split[' + x + '][percent]'];
					var amount = myForm.elements['split[' + x + '][split_amount]'];
					var original = myForm.elements['split[' + x + '][orig_amount]'];
					if(field=='amount'){
						percent.value = Math.round((amount.value/original.value)*100);
						
					}
					else{
						amount.value = Math.round((percent.value*original.value))/100;
						
					}
				}
				function checkForm(){
					var issue = "";	
					var myForm = document.forms.new_exp;
					var submitOne = myForm.elements['submit-info-one'];
					var submitTwo = myForm.elements['submit-info-two'];
					
					var projectFields = document.querySelectorAll("[name$='[project]']");
					for(i=0;i<projectFields.length;i++){
						if(projectFields[i].value == "" && projectFields[i].style.display != 'none'){
							//issue = "yes";
						}
					}
					
					var expenseFields = document.querySelectorAll("[name$='[expense]']");
					for(i=0;i<expenseFields.length;i++){
						if(expenseFields[i].value == "" && expenseFields[i].style.display != 'none'){
							//issue = "yes";
						}
					}
					
					if(issue == "yes"){
						submitOne.disabled = true;
						submitTwo.disabled = true;
					}
					else{
						submitOne.disabled = false;
						submitTwo.disabled = false;
					}
				}
				function checkProject(x,type){
					var myForm = document.forms.new_exp;
					if(type=='record'){	
						var projectField = myForm.elements['record[' + x + '][project]'];
					}
					else{
						var projectField = myForm.elements['split[' + x + '][project]'];
					}
					if(projectField.value != ""){
						projectField.style.borderColor = "#ccc";
						projectField.style.borderWidth = "1px";
					}
					else{
						projectField.style.borderColor = "red";
						projectField.style.borderWidth = "medium";
					}
				}
				function checkExpense(x,type){
					var myForm = document.forms.new_exp;
					if(type=='record'){
						var expenseField = myForm.elements['record[' + x + '][expense]'];
					}
					else{
						var expenseField = myForm.elements['split[' + x + '][expense]'];
					}
					if(expenseField.value !=""){
						expenseField.style.borderColor = "#ccc";
						expenseField.style.borderWidth = "1px";
					}
					else{
						expenseField.style.borderColor = "red";
						expenseField.style.borderWidth = "medium";
					}
				}				
				function checkFilesize(){
					var myForm = document.forms.new_exp;
					var fileFields = document.querySelectorAll("[name^='file']");//get all elements with name starting with "file"
					var totalSize = 0;
					var saveInfo = myForm.elements['save-info'];
					var saveInfoTwo = myForm.elements['save-info-two'];
					var submitInfo = myForm.elements['submit-info-one'];
					var submitInfoTwo = myForm.elements['submit-info-two'];
					
					for(i=0;i<fileFields.length;i++){
						for(t=0;t<fileFields[i].files.length;t++){
							totalSize += fileFields[i].files[t].size;
						}
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
				
				function budget(x,type)
				{
					var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0001RE","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
						"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
						"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
						"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
						"0008-INTV","0008FINTV","0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAMB","BRDU2008TTEAMW","BRDU2008TTEAM2"
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
					var receiptsRequired = [<?php
						for($i=0,$b=0;$i<count($resultactive);$i++)
						{
							if($resultactive[$i]->receipt == 1)
							{
								if($b>0){echo ",";}
								echo '"'.$resultactive[$i]->ID.'"';
								$b++;
							}
						}
						?>];
					var opportunities = [<?php
						for($i=0,$b=0;$i<count($resultactive);$i++)
						{
							if($resultactive[$i]->status >3)
							{
								if($b>0){echo ",";}
								echo '"'.$resultactive[$i]->ID.'"';
								$b++;
							}
						}
						?>];
					
					var myForm = document.forms.new_exp;
					if(type == 'record'){
						var myProject = myForm.elements['record[' + x + '][project]'];
						var myBillable = myForm.elements['record[' + x + '][billable]'];
						var myBillableA = myForm.elements['record[' + x + '][billable_a]'];
					}
					else{
						var myProject = myForm.elements['split[' + x + '][project]'];
						var myBillable = myForm.elements['split[' + x + '][billable]'];
						var myBillableA = myForm.elements['split[' + x + '][billable_a]'];
					}
					if(overhead.indexOf(myProject.value) == -1){
						if(noBill.indexOf(myProject.value) == -1){
							myBillable.value="1"; myBillableA.value = 1;
							if(myProject.value == "<?php echo $personal_project;?>"){
								myBillable.disabled = true;
							}
							else{
								myBillable.disabled = false;
							}
						}
						if(noBill.indexOf(myProject.value) != -1){
							myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;
						}
					}
					
					if(overhead.indexOf(myProject.value) != -1){
						myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;
					}
					
					if(noBill.indexOf(myProject.value) != -1){
						myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;
					}
					/*
					var myFileInput = myForm.elements['file' + x + '[]'];
					if(receiptsRequired.indexOf(myProject.value) != -1){
						alert('in array');
						myFileInput.style.backgroundColor = "red";						
					}
					else{
						alert('not in array');
						myFileInput.style.backgroundColor = '#222222';
						myFileInput.style.border-bottom = '4px solid #525252';
					}
					*/
				}
				function removeFile(x){
					var fileField = document.getElementById('file' + x);
					fileField.value = "";
					checkFilesize();
				}
				</script>
				</div></div></div></div>
<?php
				if($date_error + $project_error + $expense_error != 0)
				{
					echo '</div></div>';
				}
				
//BillyB add onsubmit to form to check for all expense types and project codes
?>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Tips";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
<?php
							if($current_user->ID == 41)
							{
								//41
								if($uid == 41){$name = 'Chris';}else{$name='Kesha';}
								echo '<input type="hidden" name="kesha_change_field" value="'.($uid == 41 ? 38 : 41).'" />';
								echo '<input type="hidden" name="kesha_current" value="'.$uid.'" />';
								echo '<input type="submit" name="change_kesha" value="Change to '.$name.'" class="my-buttons" />';
								echo '<li>&nbsp;</li>';
							}
							if($current_user->ID == 85)
							{
								//85
								if($uid == 85){$name = 'Paul';}else{$name = 'Bev';}
								echo '<input type="hidden" name="bev_change_field" value="'.($uid == 85 ? 37 : 85).'" />';
								echo '<input type="hidden" name="bev_current" value="'.$uid.'" />';
								echo '<input type="submit" name="change_bev" value="Change to '.$name.'" class="my-buttons" />';
								echo '<li>&nbsp;</li>';
							}
							
							if($current_user->ID==11 or $current_user->ID==94 or $current_user->ID==103 or $current_user->ID==112 or $current_user->ID==102)
							{
								$sarahs_list = array(102,205);//205=Dan Nebhut
								$deisys_list = array(112,173);
								$lauras_list = array(103,216,98,160,133);
								echo '<select name="changed_user">';
								$users_query = "select user_id,display_name from ".$wpdb->prefix."useradd
									inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
									where status>=0
									order by display_name";
								$users_results = $wpdb->get_results($users_query);
								foreach($users_results as $ur)
								{
									if(($current_user->ID !=103 and $current_user->ID!=112 and $current_user->ID!=102) or ($current_user->ID==103 and in_array($ur->user_id,$lauras_list)))
									{
										echo '<option value="'.$ur->user_id.'" '.($uid==$ur->user_id ? "selected='selected'" : "" ).'>'.$ur->display_name.'</option>';
									}
									if($current_user->ID==112 and in_array($ur->user_id,$deisys_list))
									{
										echo '<option value="'.$ur->user_id.'" '.($uid==$ur->user_id ? "selected='selected'" : "" ).'>'.$ur->display_name.'</option>';
									}
									if($current_user->ID==102 and in_array($ur->user_id,$sarahs_list))
									{
										echo '<option value="'.$ur->user_id.'" '.($uid==$ur->user_id ? "selected='selected'" : "" ).'>'.$ur->display_name.'</option>';
									}
								}
								echo '</select>';
								echo '<input type="submit" name="change_user" value="change user" />';
							}
								
							echo '<li>You can attach individual backup by line item.  Please attach receipts for any billable expenses.</li>';
							echo '<li>Notes are pre-populated from your statement.  You can override/edit as you see fit.</li>.';
?>
							</ul>
						</li>
					</ul>
				</div></div>
				</form>
				
<?php  }
add_shortcode('new_employee_expense_cc','billyB_new_employee_expense_cc')
?>