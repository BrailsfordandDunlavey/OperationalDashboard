<?php
function billyB_new_employee_amex()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if(isset($_POST['change_user'])){$uid = $_POST['user_to_change'];}
	if(isset($_POST['add_rows'])){$uid = $_POST['user_to_change'];}
 
 	$employeegpidresult = $wpdb->get_results($wpdb->prepare("select sphere,team,gp_id from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$employee_gp_id = $employeegpidresult[0]->gp_id;
	$sphere = $employeegpidresult[0]->sphere;
	$team = $employeegpidresult[0]->team;
	$today = time();
	$cutoff = strtotime(date("Y-m-d", strtotime($today)) . " -60 days");
	if(isset($_POST['submit-info']) or isset($_POST['submit-info-two']))
	{
		$uid = $_POST['user_to_change'];
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
		<?php
		$records = ($_POST['record']);
		
		$submit_date = time();
		$report = $uid.$submit_date;		
		
		$date_error = 0;
		$project_error = 0;
		$expense_error = 0;
		$total = 0;

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
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$approved_date = 0;
				$approved_by = 0;
				$status = 0;
				$notes = $record['notes'];
				
				if(empty($date)){$date_error = 1;}if(empty($project)){$project_error=1;}if(empty($expense)){$expense_error =1;}
				
				if($date_error + $project_error + $expense_error == 0){$status =1;}
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,
					expense_quantity,expense_amount,expense_billable,expense_submit_date,expense_approved_date,expense_approved_by,employee_expense_status,employee_expense_notes,ee_mastercard)
					values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,0,0,%d,%s,2)",
					$report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,$status,$notes));
				
				$total += $amount*$quantity;
			}
		}
		$current_dir = getcwd();
		$target_dir = $current_dir."/wp-content/expense_backup";
					
		foreach ($_FILES['fileToUpload']['name'] as $f => $name)
		{
			$file_name = time()." - ".basename($name);
			$target_file = $target_dir . "/" . $file_name;
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$f], $target_file))
			{
				echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
	
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_report_id,expense_filename) 
					values (%d,%s)",$report,$file_name));
				$wpdb->query($attachment_query);
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
		if($$date_error + $project_error + $expense_error == 0)
		{
			if($total != 0){echo "The expense report has been submitted for processing.<br/><br/>";}
			else{echo "You did not enter any amounts, so no expense was processed.<br/><br/>";}
			?>
			<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
			<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View all your saved and submitted expenses";?></a><br/><br/>
			<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
			</div></div></div></div></div>
			<?php 
			get_footer(); exit; 
		}
		if($date_error + $project_error + $expense_error != 0)
		{
			echo '<font size="3" color="red"><b><u>Sorry, your report has the following errors:</u></b></font><br/><br/>
					'.($date_error !=0 ? "Missing Date Selection<br/>" : "").'
					'.($project_error != 0 ? "Missing Project Selection<br/>" : "").'
					'.($expense_error != 0 ? "Missing Expense Code Selection<br/>" : "").'
					<br/>
					Your expense report was saved, but not submitted.<br/>  
					To make changes and submit, please see your unsubmitted expenses <a href="/my-employee-expenses/"><b>here</b></a>.
					</div></div></div>';
		}
	}
	elseif(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$uid = $_POST['user_to_change'];
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
		<?php		
				$records = ($_POST['record']);
				$submit_date = time();
				$id_for_report = $uid.$submit_date;
				$total =0;
						
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
						$billable = $record['billable'];
						if(empty($billable)){$billable=$record['billable_a'];}
						$status = 0;
						$notes = $record['notes'];

						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,expense_quantity,
							expense_amount,expense_billable,expense_submit_date,expense_approved_date,expense_approved_by,employee_expense_status,employee_expense_notes,ee_mastercard)
							values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,0,0,%d,%s,2)",
							$id_for_report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,$status,$notes));
						
						$total += $amount*$quantity;
					}
				}
				$current_dir = getcwd();
				$target_dir = $current_dir."/wp-content/expense_backup";
							
				foreach ($_FILES['fileToUpload']['name'] as $f => $name)
				{
					$file_name = time()." - ".basename($name);
					$target_file = $target_dir . "/" . $file_name;
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$f], $target_file))
					{
						echo "The file: ".$file_name. " has been uploaded.<br/><br/>";
						
						$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_report_id,expense_filename) 
							values (%d,%s)",$id_for_report,$file_name));
					}
					else
					{
						foreach($_FILES['fileToUpload']['error'] as $error)
						{
							if($error == 1){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($error == 2){echo "File was not uploaded because it was too large.<br/><br/>"; }
							if($error == 3){echo "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
							if($error == 4){echo "No attachement.<br/><br/>"; }
						}
					}
				}
				if($total !=0){echo "The expense report has been saved for future use.  You will still need to submit the report before it can be processed.<br/><br/>";}
				else{echo "You did not enter any amounts, so no expense was processed.<br/><br/>";}
				?>
				<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View all your saved and submitted expenses";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
				</div></div></div></div></div>

			<?php get_footer(); exit;
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
		<form name="new_exp" method="post" enctype="multipart/form-data" onsubmit="checkForm();">
		<div id="content">
			<div class="my_box3">
				<div class="padd10"><!--  -->
					<ul class="other-dets_m">
					<?php
					$users_query = "select user_id,display_name from ".$wpdb->prefix."useradd
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
						where status=1
						order by display_name";
					$users_results = $wpdb->get_results($users_query);
					echo '<li><select name="user_to_change" class="do_input_new">';
					foreach($users_results as $ur)
					{
						echo '<option value="'.$ur->user_id.'" '.($uid==$ur->user_id ? 'selected="selected"' : '').'>'.$ur->display_name.'</option>';
					}
					echo '</select>';
					echo '<p><input type="submit" name="change_user" value="change user" class="my-buttons-submit" /></p></li>';
					?>
					</ul>
				</div>
			</div>
		</div>
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
						$end = strtotime(date('Y-m-d'));
						$start = $end - 86400 * 60;							
							
						$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,gp_id,expense_type 
							from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
							where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status =2 and project_parent=0",$uid));
							
						$othercodesresults = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where expense_available=1");
							
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
						for ($t= 0;$t<=$totalrows;$t++)
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
							if($sphere == "Sphere Higher Ed"){echo '<option value="BRDU2008TTEAMB" '.($record_array[$t][1]=="BRDU2008TTEAMB" ? "selected='selected'" : "").'>Team Discretionary</option>';}
							if($sphere == "Sphere KMV"){echo '<option value="BRDU2008TTEAMW" '.($record_array[$t][1]=="BRDU2008TTEAMW" ? "selected='selected'" : "").'>Team Discretionary</option>';}
							echo '</select></div>';
							echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][expense]" onChange="mileage('.$t.');">
								<option value="">Expense</option>';
							
							$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights='project' order by expense_code_name";
							if($sphere == 'Functional'){$expensecodequery = "select * from ".$wpdb->prefix."expense_codes order by expense_code_name";}
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
								'.(!empty($record_array[$t][6]) ? 'value="'.$record_array[$t][6].'"' : '').' placeholder="Notes"/></div><br/><br/><hr><br/>';
						}
						?>
						<script type="text/javascript">
						function budget(x){
							var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0001RE","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
								"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
								"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
								"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
								"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV"];
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
							
							if(overhead.indexOf(myProject.value) != -1 || noBill.indexOf(myProject.value) != -1){
								myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;
							}
							else{
								myBillable.disabled = false;
							}
						}
						function mileage(x){
							var myForm = document.forms.new_exp;
							var myControls = myForm.elements['record[' + x + '][expense]'];
							var myAmount = myForm.elements['record[' + x + '][amount]'];
							var origValue = myAmount.value;
							
							if(myControls.value == 28){myAmount.value=".535"; myAmount.readOnly = true;}
							if(!(myControls.value == 28) && origValue==".535"){myAmount.value=""; myAmount.readOnly = false;}
							if(!(myControls.value == 28) && !(origValue==".535")){myAmount.value=origValue; myAmount.readOnly = false;}
						}
						</script>
						<li hidden><input type="text" name="y" value="<?php echo $y;?>"/></li>
						<li>&nbsp;</li>
						<li><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></li>
						<li>&nbsp;</li>
						<li><p><input type="submit" name="add_rows" class="my-buttons" value="<?php echo "Add Five (5) rows";?>" /></p></li>
						<li>&nbsp;</li>
						<li><input type="submit" name="save-info-two" class="my-buttons" value="<?php echo "Save for Later"; ?>" />&nbsp;&nbsp;
						<input type="submit" name="submit-info-two" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></li>	
						<li>&nbsp;</li>
					</ul>
</div>
</div> </div></form>
<?php }
add_shortcode('new_employee_amex','billyB_new_employee_amex')
?>