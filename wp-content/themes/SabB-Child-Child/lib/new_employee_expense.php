<?php
function billyB_new_employee_expense()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if(isset($_POST['change_kesha'])){$uid = $_POST['kesha_change_field'];}
	if(isset($_POST['change_bev'])){$uid = $_POST['bev_change_field'];}
	if(isset($_POST['change_user'])){$uid = $_POST['changed_user'];}
 
 	$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$employee_gp_id = $useradd_results[0]->gp_id;
	$sphere = $useradd_results[0]->sphere;
	$team = $useradd_results[0]->team;
	$amex = $useradd_results[0]->amex;
	$personal_project = $useradd_results[0]->personal_project;
	
	$today = time();
	$cutoff = strtotime(date("Y-m-d", strtotime($today)) . " -60 days");
	if(isset($_POST['submit-info']) or isset($_POST['submit-info-two']))
	{
		if(!empty($_POST['kesha_change_field'])){$uid = $_POST['kesha_change_field'];}
		if(!empty($_POST['bev_change_field'])){$uid = $_POST['bev_change_field'];}
		if(!empty($_POST['change_user'])){$uid = $_POST['changed_user'];}
		
		if(!empty($_POST['kesha_change_field']) or !empty($_POST['bev_change_field']) or !empty($_POST['change_user']))
		{
			$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
			$employee_gp_id = $useradd_results[0]->gp_id;
		}
		
		?>
		<div id="content">
		<div class="my_box3">
			<div class="padd10">
		<?php
		$records = ($_POST['record']);
		$user = $current_user->ID;
		$submit_date = time();
		$report = $user.$submit_date;
		$amex_select = $_POST['amex'];
		if($amex_select == "on"){$ee_mastercard = 2;}else{$ee_mastercard = 0;}
		
		$date_error = 0;
		$project_error = 0;
		$expense_error = 0;
		$billable_error = 0;
		$total = 0;
		$increment = 0;
		$none = 0;
		$error = 0;
		
		$file_name_array = array();
		
		foreach($records as $record)
		{
			if(!empty($record['amount']))
			{
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				
				if(empty($date)){$date_error = 1;$error=1;}
				if(empty($project)){$project_error=1;$error=1;}
				if(empty($expense)){$expense_error =1;$error=1;}
				if(empty($billable)){$billable_error=1;$error=1;}
			}
		}
		if($error==0){$status = 1;}else{$status = 0;}
		foreach($records as $record)
		{		
			if(!empty($record['amount']))
			{
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				if(empty($quantity)){$quantity = 1;}
				$amount = $record['amount'];
				if($expense == 28){$amount = 0.575;}//make sure mileage isn't reset if user disables JavaScript
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$notes = $record['notes'];
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,expense_quantity,expense_amount,
					expense_billable,expense_submit_date,expense_approved_date,expense_approved_by,employee_expense_status,employee_expense_notes,ee_mastercard)
					values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,0,0,%d,%s,%d)",
					$report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,$status,$notes,$ee_mastercard));
				$expense_id = $wpdb->insert_id;
				
				$total += $amount*$quantity;
				
				$current_dir = getcwd();
				$target_dir = $current_dir."/wp-content/expense_backup";
						
				foreach ($_FILES['file'.$increment]['name'] as $f => $name)
				{
					$file_name = time()." - ".basename($name);
					
					if(in_array($file_name,$file_name_array))
					{
						for($i=1;$i<1000;$i++)
						{
							$edited_file_name = $i.$file_name;
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
					
					if (move_uploaded_file($_FILES["file".$increment]["tmp_name"][$f], $target_file))
					{
						echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
			
						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_filename) values (%d,%s)",$expense_id,$file_name));
						$none++;
					}
					else
					{
						foreach($_FILES['file'.$increment]['error'] as $file_error)
						{
							if($file_error == 1){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($file_error == 2){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($file_error == 3){echo "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
							if($file_error > 4 or $file_error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>"; }
						}
					}
				}
			}
			$increment++;
		}
		if($none==0){echo "No attachments were provided.<br/><br/>";}		
		if($error == 0)
		{
			if($total != 0){echo "The expense report has been submitted for processing.<br/><br/>";}
			else{echo "You did not enter any amounts, so no expense was processed.<br/><br/>";}
			?>
			<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
			<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View all your saved and submitted expenses";?></a><br/><br/>
			<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
			</div></div></div></div></div>
			<?php 
		}
		if($error != 0)
		{
			echo '<font size="3" color="red"><b><u>Sorry, your report has the following errors:</u></b></font><br/><br/>
					'.($date_error !=0 ? "Missing Date Selection<br/>" : "").'
					'.($project_error != 0 ? "Missing Project Selection<br/>" : "").'
					'.($expense_error != 0 ? "Missing Expense Code Selection<br/>" : "").'
					'.($billable_error != 0 ? "Missing Billable Selection<br/>" : "").'
					<br/>
					Your expense report was saved, but not submitted.<br/>  
					To make changes and submit, please see your unsubmitted expenses <a href="/my-employee-expenses/"><b>here</b></a>.
					</div></div></div>';
		}
		$_POST = array();
		get_footer(); exit; 
	}
	elseif(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		if(!empty($_POST['kesha_change_field'])){$uid = $_POST['kesha_change_field'];}
		if(!empty($_POST['bev_change_field'])){$uid = $_POST['bev_change_field'];}
		if(!empty($_POST['change_user'])){$uid = $_POST['changed_user'];}
		
		if(!empty($_POST['kesha_change_field']) or !empty($_POST['bev_change_field']) or !empty($_POST['change_user']))
		{
			$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
			$employee_gp_id = $useradd_results[0]->gp_id;
		}
		
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
		<?php		
		$records = ($_POST['record']);
		$submit_date = time();
		$user = $current_user->ID;
		$id_for_report = $user.$submit_date;
		$total =0;
		$amex_select = $_POST['amex'];
		if(!empty($_POST['kesha_change_field'])){$uid = $_POST['kesha_change_field'];}
		if(!empty($_POST['bev_change_field'])){$uid = $_POST['bev_change_field'];}
		if(!empty($_POST['changed_user'])){$uid = $_POST['changed_user'];}
		if($amex_select == "on"){$ee_mastercard = 2;}else{$ee_mastercard = 0;}
		$increment = 0;
		$none = 0;
		
		$file_name_array = array();
		
		foreach($records as $record)
		{
			if(!empty($record['amount']))
			{
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				if(empty($quantity)){$quantity=1;}
				$amount = $record['amount'];
				if($expense == 28){$amount = 0.575;}//make sure mileage rate set in case user disables JavaScript
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$status = 0;
				$notes = $record['notes'];

				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,expense_quantity,expense_amount,
					expense_billable,expense_submit_date,employee_expense_status,employee_expense_notes,ee_mastercard)
					values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,%d,%s,%d)",
					$id_for_report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,$status,$notes,$ee_mastercard));
				$expense_id = $wpdb->insert_id;
				
				$total += $amount*$quantity;
			
				$current_dir = getcwd();
				$target_dir = $current_dir."/wp-content/expense_backup";
						
				foreach ($_FILES['file'.$increment]['name'] as $f => $name)
				{
					$file_name = time()." - ".basename($name);
					
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
					if (move_uploaded_file($_FILES["file".$increment]["tmp_name"][$f], $target_file))
					{
						echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
			
						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_filename) values (%d,%s)",$expense_id,$file_name));
						$none++;
					}
					else
					{
						foreach($_FILES['file'.$increment]['error'] as $file_error)
						{
							if($file_error == 1){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($file_error == 2){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($file_error == 3){echo "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>";}
							//if($file_error == 4){echo "No attachement.<br/><br/>"; }
							if($file_error > 4 or $file_error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
						}
					}
				}
				$increment++;
			}
		}
		if($none==0){echo "No attachments were provided.<br/><br/>";}
		if($total !=0)
		{
			echo "The expense report has been saved for future use.  You will still need to submit the report before it can be processed.<br/><br/>";
		}
		else{echo "You did not enter any amounts, so no expense was processed.<br/><br/>";}
		?>
		<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View all your saved and submitted expenses";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div></div></div>

		<?php 
		$_POST = array();
		get_footer(); exit;
	} ?>
		<script type="text/javascript">
			var form_being_submitted = false;
			function checkForm(){
				var myForm = document.forms.new_exp;
				var saveInfo = myForm.elements['save-info'];
				var saveInfoTwo = myForm.elements['save-info-two'];
				var submitInfo = myForm.elements['submit-info'];
				var submitInfoTwo = myForm.elements['submit-info-two'];
				
				if(form_being_submitted){
				alert('The form is being submitted, please wait a moment...');
				saveInfo.disabled = true;
				saveInfoTwo.disabled = true;
				submitInfo.disabled = true;
				submitInfoTwo.disabled = true;
				return false;
				}
				saveInfo.value = 'Saving form...';
				saveInfoTwo.value = 'Saving form...';
				submitInfo.value = 'Saving form...';
				submitInfoTwo.value = 'Saving form...';
				form_being_submitted = true;
				return true;
				
			}
		</script>			
		<form name="new_exp" method="post"  enctype="multipart/form-data" onsubmit="checkForm();">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">				
					<ul class="other-dets_m">
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save for Later"; ?>" />&nbsp;&nbsp;
						<input type="submit" name="submit-info" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></p></li>
						<li>&nbsp;</li>
						<style>
						input[type=number]{width:95px;}
						</style>
						<?php 							
						if($amex != 0)
						{
							echo '<li><h3>AMEX</h3><p><input type="checkbox" name="amex" /> Check if this is an AMEX entry<p></li>
								<li>&nbsp;</li>';
						}
						$end = strtotime(date('Y-m-d'));
						$start = $end - 86400 * 60;							
							
						$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,gp_id,expense_type 
							from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
							where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status=2 and project_parent=0",$uid));		
							
						$othercodesquery = "select * from ".$wpdb->prefix."other_project_codes where expense_available=1";
						$othercodesresults = $wpdb->get_results($othercodesquery);
							
						$rowstart = 4;
						$rowadd = 5;
						$x = $_POST['y'];
						if(isset($_POST['add_rows']))
						{
							$y = $x+1;
							$records = $_POST['record'];
							$record_array = array();
							for($i=0;$i<count($records);$i++)
							{
								$array = array();
								array_push($array,$records[$i]['date']);
								array_push($array,$records[$i]['project']);
								array_push($array,$records[$i]['expense']);
								array_push($array,$records[$i]['quantity']);
								array_push($array,$records[$i]['amount']);
								if(!empty($records[$i]['billable'])){array_push($array,$records[$i]['billable']);}
								else{array_push($array,$records[$i]['billable_a']);}
								array_push($array,$records[$i]['notes']);
								array_push($array,$records[$i]['billable_a']);
								array_push($record_array,$array);
							}
						}
						$totalrows = $rowstart + ($rowadd * $y);
						for ($t=0;$t<=$totalrows;$t++)
						{
							echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][date]"><option value="">Date</option>';
							for ($i = $end; $i >= $start; $i = $i - 86400)
							{
								echo '<option value="'.$i.'" '.($record_array[$t][0] == $i ? 'selected="selected"' : '').'>'.date( 'm-d', $i).'</option>';
							}
							echo '</select></div><div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][project]" onChange="budget('.$t.');">
								<option value="">Project</option>';
							foreach ($resultactive as $active)
							{
								echo '<option value="'.$active->ID.'" '.($record_array[$t][1] == $active->ID ? 'selected="selected"' : '').'>'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
							}
							foreach ($othercodesresults	as $othercode)
							{
								echo '<option value="'.$othercode->other_project_code_value.'"'.($record_array[$t][1] == $othercode->other_project_code_value ? 
									'selected="selected"' : '').'>'.$othercode->other_project_code_name.'</option>';
							}
							
							if($sphere == "Higher Ed"){echo '<option value="BRDU2008TTEAMB" '.($record_array[$t][1]=="BRDU2008TTEAMB" ? "selected='selected'" : "").'>Team Discretionary</option>';}
							if($sphere == "Sphere KMV"){echo '<option value="BRDU2008TTEAMW" '.($record_array[$t][1]=="BRDU2008TTEAMW" ? "selected='selected'" : "").'>Team Discretionary</option>';}
							if(!empty($personal_project))
							{
								echo '<option value="'.$personal_project.'" >Personal</option>';
							}
							echo '</select></div>';
							echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][expense]" onChange="mileage('.$t.');">
								<option value="">Expense</option>';
							
							$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights='Project' order by expense_code_name";
							if($sphere == 'Functional'){$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights in ('Project','Functional') order by expense_code_name";}
							$expensecoderesults = $wpdb->get_results($expensecodequery);
								
							foreach($expensecoderesults as $code)
							{echo '<option value="'.$code->expense_code_id.'" '.($record_array[$t][2] == $code->expense_code_id ? 'selected="selected"' : '').'>'.$code->expense_code_name.'</option>';}
							
							echo '</select></div>';
							echo '<div id="d3" style="display:inline-block;"><input type="number" class="do_input_new" name="record['.$t.'][quantity]" step=".01" 
								value="'.$record_array[$t][3].'" placeholder="Qty"/></div>';
							echo '<div id="d3" style="display:inline-block;"><input type="number" class="do_input_new" name="record['.$t.'][amount]" step=".01"
								value="'.$record_array[$t][4].'" placeholder="Amt"/></div>';
							echo '<input type="hidden" name="record['.$t.'][billable_a]" value="'.$record_array[$t][7].'" >';
							echo '<div id="d3" style="display:inline-block;">
								<select class="do_input_new" name="record['.$t.'][billable]" '.($record_array[$t][5]=="3" ? 'disabled="disabled"' : '').' >
								<option value="1" '.($record_array[$t][5] == 1 ? 'selected="selected"' : '').'>Billable</option>
								<option value="3" '.($record_array[$t][5] == 3 ? 'selected="selected"' : '').'>No-Bill</option></select></div>';
							echo '<div id="d3" style="display:inline-block;"><input type="text" size="25" class="do_input_new" name="record['.$t.'][notes]" 
								'.(!empty($record_array[$t][6]) ? 'value="'.$record_array[$t][6].'"' : '').' placeholder="Notes"/></div><br/><br/>';
							echo '<div><input class="my-buttons" type="file" id="file'.$t.'" name="file'.$t.'[]" multiple="multiple" 
								onChange="checkFilesize();" />&nbsp;&nbsp;<input type="button" onClick="removeFile('.$t.');" value="Clear Files" /></div><br/><hr><br/>';
						}
						?>
						<script type="text/javascript">
						function budget(x){
							var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0001RE","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
								"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
								"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
								"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
								"0008-INTV","0008FINTV","0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAMB","BRDU2008TTEAMW","BRDU2008TTEAM2"];
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
							var myForm = document.forms.new_exp;
							var myProject = myForm.elements['record[' + x + '][project]'];
							var myBillable = myForm.elements['record[' + x + '][billable]'];
							var myBillableA = myForm.elements['record[' + x + '][billable_a]'];
							var billableFields = document.querySelectorAll("[name$='[billable]']");
							var billableAFields = document.querySelectorAll("[name$='[billable_a]']");
							var projectFields = document.querySelectorAll("[name$='[project]']");
							var personalProject = "<?php echo $personal_project;?>";
							
							if(overhead.indexOf(myProject.value)==-1 && noBill.indexOf(myProject.value)==-1){//if project is not no-bill and not overhead
								myBillable.value="1"; myBillable.disabled = false; myBillableA.value = 1;
								if(myProject.value==personalProject){
									myBillable.disabled = true;
								}
								for(i=0;i<billableFields.length;i++){
									if(projectFields[i].value == 0){
										billableFields[i].value = myBillable.value;
										billableFields[i].disabled = false;
										billableAFields[i].value = myBillable.value;
										projectFields[i].value = myProject.value;
									}
								}
							}
							else{//project is either no-bill or an overhead
								myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;
								for(i=0;i<billableFields.length;i++){
									if(projectFields[i].value == 0){
										billableFields[i].value = myBillable.value;
										billableFields[i].disabled = true;
										billableAFields[i].value = myBillable.value;
										projectFields[i].value = myProject.value;
									}
								}
							}
						}
						function mileage(x){
							var myForm = document.forms.new_exp;
							var myControls = myForm.elements['record[' + x + '][expense]'];
							var myAmount = myForm.elements['record[' + x + '][amount]'];
							var origValue = myAmount.value;
							
							if(myControls.value == 28){myAmount.value=".575"; myAmount.readOnly = true;}
							if(!(myControls.value == 28) && origValue==".575"){myAmount.value=""; myAmount.readOnly = false;}
							if(!(myControls.value == 28) && !(origValue==".575")){myAmount.value=origValue; myAmount.readOnly = false;}
							
						}
						function checkFilesize(){
							var myForm = document.forms.new_exp;
							var fileFields = document.querySelectorAll("[name^='file']");//get all elements with name starting with "file"
							var totalSize = 0;
							var saveInfo = myForm.elements['save-info'];
							var saveInfoTwo = myForm.elements['save-info-two'];
							var submitInfo = myForm.elements['submit-info'];
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
						function removeFile(x){
							var fileField = document.forms.new_exp.elements['file'+ x +'[]'];
							fileField.value = "";
							checkFilesize();
						}
						</script>
							
						<li hidden><input type="text" name="y" value="<?php echo $y;?>"/></li>
						<li>&nbsp;</li>
						<li><p><input type="submit" name="add_rows" class="my-buttons" value="<?php echo "Add Five (5) rows";?>" /></p></li>
						<li>&nbsp;</li>
						<li><input type="submit" name="save-info-two" class="my-buttons" value="<?php echo "Save for Later"; ?>" />&nbsp;&nbsp;
						<input type="submit" name="submit-info-two" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></li>	
						
						<li>&nbsp;</li>
					</ul>
</div>
	
</div> </div>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Tips";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<?php
							if($current_user->ID == 41)
							{
								if($uid == 41){$name = 'Chris';}else{$name='Kesha';}
								echo '<input type="hidden" name="kesha_change_field" value="'.($uid == 41 ? 38 : 41).'" />';
								echo '<input type="hidden" name="kesha_current" value="'.$uid.'" />';
								echo '<input type="submit" name="change_kesha" value="Change to '.$name.'" class="my-buttons" />';
								echo '<li>&nbsp;</li>';
							}
							if($current_user->ID == 85)
							{
								if($uid == 85){$name = 'Paul';}else{$name = 'Bev';}
								echo '<input type="hidden" name="bev_change_field" value="'.($uid == 85 ? 37 : 85).'" />';
								echo '<input type="hidden" name="bev_current" value="'.$uid.'" />';
								echo '<input type="submit" name="change_bev" value="Change to '.$name.'" class="my-buttons" />';
								echo '<li>&nbsp;</li>';
							}
							echo '<li>The Date field should be the date of the expense, not the date of entry.</li>';
							echo '<li>Format is Date, Project, Expense, Quantity, Amount, Billable Status, Notes.</li>';
							echo '<li>You can save and come back to the report later, see below.  You can edit an expense up until the time it is approved.</li>.';
							?>
							</ul>
						</li>
					</ul>
				</div></div></form>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Unsubmitted Expenses";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<?php
								$expensehistoryresult = $wpdb->get_results($wpdb->prepare("select expense_quantity,expense_amount,expense_report_id,expense_date 
									from ".$wpdb->prefix."employee_expenses where 
									employee_id=%d
									and employee_expense_status=0 group by expense_report_id order by expense_date desc limit 10",$uid));
								if(empty($expensehistoryresult)){echo "You don't have any unsubmitted expenses";}
								else
								{
								echo '<table width ="100%"><tr><th><u>Report ID</u></th><th><u>Date</u></th><th><u>Total</u></th><th>&nbsp;</th></tr>';
								foreach ($expensehistoryresult as $data)
								{
									$report_id = $data->expense_report_id;
									$sumquery = $wpdb->prepare("select expense_quantity,expense_amount from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$report_id);
									$sumresults = $wpdb->get_results($sumquery);
									$sum = 0;
									for($i=0;$i<=count($sumresults);$i++)
									{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
									
									echo '<tr><th>'.$data->expense_report_id.'</th><th>'.date('m-d',$data->expense_date).'</th><th>'.ProjectTheme_get_show_price($sum).'</th>
									<th><a href="/?p_action=edit_employee_expense&ID='.$data->expense_report_id.'" class="nice_link">Edit</a></th></tr>';}
								echo '</table>';
								}
							?>
							</ul>
						</li>
					</ul>
				</div></div>
								<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Previously Submitted Expenses";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<?php
								$expensehistoryquery = $wpdb->prepare("select expense_quantity,expense_amount, expense_report_id, expense_date from ".$wpdb->prefix."employee_expenses where employee_id=%d
									and employee_expense_status>0 group by expense_report_id order by expense_date desc limit 10",$uid);
								$expensehistoryresult = $wpdb->get_results($expensehistoryquery);
								if(empty($expensehistoryresult)){echo "You don't have any unsubmitted expenses";}
								else
								{
									echo '<table width ="100%"><tr><th><u>Report ID</u></th><th><u>Date</u></th><th><u>Total</u></th><th>&nbsp;</th></tr>';
									foreach ($expensehistoryresult as $data)
									{
										$report_id = $data->expense_report_id;
										$sumquery = $wpdb->prepare("select expense_quantity,expense_amount from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$report_id);
										$sumresults = $wpdb->get_results($sumquery);
										$sum = 0;
										for($i=0;$i<=count($sumresults);$i++)
										{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
										
										echo '<tr><th>'.$data->expense_report_id.'</th><th>'.date('m-d',$data->expense_date).'</th><th>'.ProjectTheme_get_show_price($sum).'</th>
										'.($cutoff > $data->expense_date ? '<th><a href="/?p_action=edit_employee_expense&ID='.$data->expense_report_id.'" class="nice_link">Edit</a></th></tr>' : '&nbsp;');
									}
									echo '</table>';
								}
							?>
							</ul>
						</li>
					</ul>
				</div></div>
				
<?php }
add_shortcode('new_employee_expense','billyB_new_employee_expense')
?>