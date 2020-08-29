<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	function sitemile_filter_ttl($title){return ("Edit Employee Expense");}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	$employee_gp_id = $rightsresults[0]->gp_id;
	$sphere = $rightsresults[0]->sphere;
	$personal_project = $rightsresults[0]->personal_project;
	$amex = $rightsresults[0]->amex;
	
	$expense_report = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare(
		"select employee_expense_status,ee_mastercard,employee_expense_id,expense_date,project_id,expense_type_id,expense_quantity,expense_amount,
			expense_billable,employee_expense_notes,expense_filename
		from ".$wpdb->prefix."employee_expenses 
		left join ".$wpdb->prefix."expense_backup on ".$wpdb->prefix."employee_expenses.employee_expense_id=".$wpdb->prefix."expense_backup.expense_id
		where ".$wpdb->prefix."employee_expenses.expense_report_id='%d' and (ee_mastercard=0 or ee_mastercard=2)",
		$expense_report));
	
	if($details[0]->employee_expense_status > 2)
	{
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<?php
						echo "Sorry, this expense report has been processed and cannot be edited.<br/><br/>";
						?>
						<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
						<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View your previously saved and submitted expenses";?></a><br/><br/>
						<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 	
		get_footer();
		exit;
	}
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$records = ($_POST['record']);
		$amex_post = $_POST['amex'];
		if($amex_post == "on"){$ee_mastercard = 2;}else{$ee_mastercard = 0;}
		$submit_date = time();
		$status = 0;
		$increment=0;
		$file_name_array = array();
		
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		
		foreach($records as $record)
		{
			$exp_id = $record['id'];
			if(!empty($record['amount']) and !empty($exp_id))
			{
				$report = $expense_report;
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				$amount = $record['amount'];
				if($expense == 28){$amount = 0.575;}//ensure mileage rate in case user disables JavaScript
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$notes = $record['notes'];

				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_date=%d,project_id=%s,expense_type_id=%d,
					expense_quantity=%f,expense_amount=%f,expense_billable=%d,expense_submit_date=%d,employee_expense_notes=%s,
					employee_expense_status=%d,ee_mastercard=%d where employee_expense_id=%d",$date,$project,$expense,$quantity,$amount,$billable,
					$submit_date,$notes,$status,$ee_mastercard,$exp_id));
			}
			if(!empty($record['amount']) and empty($exp_id))
			{
				$report = $expense_report;
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				$amount = $record['amount'];
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$submit_date = time();
				$approved_date = 0;
				$approved_by = 0;
				$status = 0;
				$notes = $record['notes'];
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,
					expense_quantity,expense_amount,expense_billable,expense_submit_date,employee_expense_status,employee_expense_notes,ee_mastercard) 
					values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,%d,%s,%d)",$report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,
					$status,$notes,$ee_mastercard));
				$exp_id = $wpdb->insert_id;
			}
			if((empty($record['amount']) or empty($record['quantity'])) and !empty($record['orig']))
			{
				$exp_id = $record['id'];
				$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where employee_expense_id=%d",$exp_id));
			}
			
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
		
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_filename) values (%d,%s)",$exp_id,$file_name));
					$none++;
				}
				else
				{
					foreach($_FILES['file'.$increment]['error'] as $error)
					{
						if($error == 1){echo "File ".basename($name)." was not uploaded because it was too large.<br/><br/>"; }
						if($error == 2){echo "File ".basename($name)."was not uploaded because it was too large.<br/><br/>"; }
						if($error == 3){echo "File ".basename($name)."was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					}
				}
			}
			$increment++;
		}
				
		
		echo 'The <a href="'.get_bloginfo('siteurl').'/?p_action=edit_employee_expense&ID='.$expense_report.'">expense report</a> has been saved for future use.<br/>You will still need to submit the report before it can be processed.<br/><br/>';
		?>
		<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View your previously saved and submitted expenses";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div>
		</div>
		</div>
		</div>
		</div>

		<?php 
		$_POST = array();
		get_footer();
	}
	elseif(isset($_POST['delete-info']))
	{
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$expense_report));
		
		$backup_results = $wpdb->get_results($wpdb->prepare("select expense_filename from ".$wpdb->prefix."expense_backup where expense_report_id=%d",$expense_report));
		$current_dir = getcwd();
		$target_dir = $current_dir."/wp-content/expense_backup";
		chdir($target_dir);
		
		foreach($backup_results as $backup)
		{
			$filename = $backup->expense_filename;
			unlink($filename);
		}
		
		chdir($current_dir);
		
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."expense_backup where expense_report_id=%d",$expense_report));
			
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
		<?php 
		
		echo "This expense has been deleted<br/><br/>";
		echo '<a href="/new-employee-expense/">Enter a new Expense Report</a><br/><br/>';
		echo '<a href="/my-employee-expenses/">View all your saved and submitted expenses.</a><br/><br/>';
		echo '<a href="/dashboard/">Return to your Dashboard</a>';
		?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
	}
	elseif(isset($_POST['submit-info']) or isset($_POST['submit-info-two']))
	{
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
		<?php
		$records = ($_POST['record']);
		$amex_post = $_POST['amex'];
		if($amex_post == "on"){$ee_mastercard = 2;}else{$ee_mastercard = 0;}
		$status = 0;
		$submit_date = time();
		$increment = 0;
		$file_name_array = array();
						
		foreach($records as $record)
		{
			$exp_id = $record['id'];
			if(!empty($record['amount']) and !empty($exp_id))
			{
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				if(empty($quantity)){$quantity=1;}
				$amount = $record['amount'];
				if($expense == 28){$amount = 0.575;}//ensure mileage rate in case user disables JavaScript
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$notes = $record['notes'];
				
				if($date==0){$date_error = 1;}if(empty($project)){$project_error=1;}if(empty($expense)){$expense_error =1;}
				
				if($date_error + $project_error + $expense_error == 0){$status =1;}
					
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_date=%d,project_id=%s,expense_type_id=%d,
					expense_quantity=%f,expense_amount=%f,expense_billable=%d,expense_submit_date=%d,employee_expense_notes=%s,
					employee_expense_status=%d,ee_mastercard=%d where employee_expense_id=%d",$date,$project,$expense,$quantity,$amount,$billable,$submit_date,
					$notes,$status,$ee_mastercard,$exp_id));
			}
			if(!empty($record['amount']) and empty($exp_id))
			{
				$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				if(empty($quantity)){$quantity=1;}
				$amount = $record['amount'];
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$notes = $record['notes'];
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (expense_report_id,employee_id,employee_gp_id,expense_date,project_id,expense_type_id,
					expense_quantity,expense_amount,expense_billable,expense_submit_date,employee_expense_status,employee_expense_notes,ee_mastercard)
					values(%d,%d,%s,%d,%s,%d,%f,%f,%d,%d,%d,%s,%d)",$expense_report,$uid,$employee_gp_id,$date,$project,$expense,$quantity,$amount,$billable,$submit_date,
					$status,$notes,$ee_mastercard));
			}
			if(empty($record['amount']) and !empty($record['orig']))
			{
				$exp_id = $record['id'];
				$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where employee_expense_id=%d",$exp_id));
			}
			
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
		
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."expense_backup (expense_id,expense_filename) values (%d,%s)",$exp_id,$file_name));
					$none++;
				}
				else
				{
					foreach($_FILES['file'.$increment]['error'] as $error)
					{
						if($error == 1){echo "File ".basename($name)." was not uploaded because it was too large.<br/><br/>"; }
						if($error == 2){echo "File ".basename($name)."was not uploaded because it was too large.<br/><br/>"; }
						if($error == 3){echo "File ".basename($name)."was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					}
				}
			}
			$increment++;
		}
		
		if($date_error + $project_error + $expense_error == 0){echo "Thank you.  The expense report has been submitted for processing.<br/><br/>";}
		else
		{
			echo '<font size="3" color="red"><b><u>Sorry, your report has the following errors:</u></b></font><br/><br/>
					'.($date_error !=0 ? "Missing Date Selection<br/>" : "").'
					'.($project_error != 0 ? "Missing Project Selection<br/>" : "").'
					'.($expense_error != 0 ? "Missing Expense Code Selection<br/>" : "").'
					<br/>
					Your expense report was saved, but not submitted.<br/>  
					To make changes and submit, please see your unsubmitted expenses <a href="/my-employee-expenses/"><b>here</b></a>.
					</div></div></div></div></div>';
			get_footer();
			exit;
		}
		?>
		<a href="<?php bloginfo('siteurl');?>/new-employee-expense/"><?php echo "Enter a new Expense Report";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl');?>/my-employee-expenses/"><?php echo "View all your saved and submitted expenses";?></a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		get_footer();
		$_POST = array();
	}
	else
	{
		foreach($details as $detail)
		{
			$status = $detail->employee_expense_status;
			$mastercard = $detail->ee_mastercard;
		
			if($status >1 and $mastercard>0){$delete = "no";}
		}
		?> 	
		<form name="edit_emp_exp" id="edit_emp_exp" method="post"  enctype="multipart/form-data">
			<div id="main_wrapper">
				<div id="main" class="wrapper">
			<div id="content">
				<div class="my_box3">
				<div class="padd10">
								
				<ul class="other-dets_m">
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save for Later"; ?>" />&nbsp;&nbsp;
					<input type="submit" name="submit-info" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></p></li>
					<li>&nbsp;</li>
					<style>input[type=number]{width:90px;}</style>
					<?php 							
					if($amex != 0)
					{
						echo '<li><h3>AMEX</h3><p><input type="checkbox" name="amex" '.($mastercard==2 ? 'checked="checked"' : '' ).'/> Check if this is an AMEX entry<p></li>
							<li>&nbsp;</li>';
					}
					$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,".$wpdb->prefix."projects.gp_id,expense_type 
						from ".$wpdb->prefix."projects 
						inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
						where ".$wpdb->prefix."project_user.user_id =%d and ".$wpdb->prefix."projects.status =2 and project_parent=0",$uid));
					
					$othercodesresults = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where other_project_code_id!=1 and expense_available=1
						order by other_project_code_name");
					
					$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights='project'";
					if($sphere == 'Functional'){$expensecodequery = "select * from ".$wpdb->prefix."expense_codes";}
					$expensecoderesults = $wpdb->get_results($expensecodequery);
					
					$end = strtotime(date('Y-m-d'));
					$start = $end - 86400 * 60;
					
					$overhead_array = array("0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
							"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
							"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
							"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008-INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
							"0008FINTV","0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAMB","BRDU2008TTEAMW","BRDU2008TTEAM2");
					for($i=0;$i<count($resultactive);$i++)
					{
						if($resultactive[$i]->expense_type == "No-Bill")
						{
							array_push($overhead_array,$resultactive[$i]->project_id);
						}
					}
					

				$t = -1;
				$expense_ids = array();
				for($i=0;$i<count($details);$i++)
				{
					$t++;
					$expense_id = $details[$i]->employee_expense_id;
					$date = $details[$i]->expense_date;
					$project_id = $details[$i]->project_id;
					$expense_type_id = $details[$i]->expense_type_id;
					$expense_quantity = $details[$i]->expense_quantity;
					$expense_amount = $details[$i]->expense_amount;
					$expense_billable = $details[$i]->expense_billable;
					$expense_notes = $details[$i]->employee_expense_notes;
					$status = $details[$i]->employee_expense_status;
					$mastercard = $details[$i]->ee_mastercard;
					$backup = $details[$i]->expense_filename;
					
					if(!in_array($expense_id,$expense_ids))
					{
						array_push($expense_ids,$expense_id);
						
						if($i>0)
						{
							$a++;
							
							echo '<li><hr></li>';
						}
						else{$a=$i;}
						
						echo '<li><input type="hidden" value="'.$expense_id.'" name="record['.$i.'][id]" />
							<div id="'.$i.'" style="display:inline-block;"><select name="record['.$i.'][date]" class="do_input_new" '.($mastercard > 0 ? "readonly" : "").' >';
						for ($d=$end;$d>=$start;$d=$d - 86400)
							{echo '<option value="'.$d.'"';if($d==$date){echo 'selected="selected"';}echo '>'.date( 'm-d', $d).'</option>';}
						echo '</select></div>';
						echo '<div id="'.$i.'" style="display:inline-block;"><select class="do_input_new" name="record['.$i.'][project]" onChange="budget('.$i.');" ><option value="">Project</option>';
						foreach ($resultactive as $active)
						{
							echo '<option value="'.$active->ID.'" ';
							if($active->ID == $project_id){echo 'selected="selected" ';}
							echo '>'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
						}
						foreach ($othercodesresults	as $othercode)
						{
							echo '<option value="'.$othercode->other_project_code_value.'"';if($project_id===$othercode->other_project_code_value){echo 'selected="selected"';}
							echo ' >'.$othercode->other_project_code_name.'</option>';
						}
						if($sphere == "Higher Ed"){echo '<option value="BRDU2008TTEAMB" '.($project_id=="BRDU2008TTEAMB" ? "selected='selected'" : "").'>Team Discretionary</option>';}
						if($sphere == "Sphere KMV"){echo '<option value="BRDU2008TTEAMW" '.($project_id=="BRDU2008TTEAMW" ? "selected='selected'" : "").'>Team Discretionary</option>';}
						if(!empty($personal_project)){echo '<option value="'.$personal_project.'" '.($project_id==$personal_project ? 'selected="selected"' : '').'>Personal</option>';}
						echo '</select></div>';
						echo '<div id="'.$i.'" style="display:inline-block;"><select class ="do_input_new" name="record['.$i.'][expense]" onChange="mileage('.$i.');" 
							'.($mastercard > 0 ? "" : "onChange=\"mileage('.$i.');\"" ).'><option value="">Expense</option>';
						foreach($expensecoderesults as $code)
							{echo '<option value="'.$code->expense_code_id.'"';
							if($code->expense_code_id == $expense_type_id){echo 'selected="selected" ';}
							echo '>'.$code->expense_code_name.'</option>';}
						echo '</select></div>';
						echo '<div id="'.$i.'" style="display:inline-block;">Qty:<input type="number" class="do_input_new" step=".01" name="record['.$i.'][quantity]" 
							'.($mastercard == 1 ? "readonly" : "" ).' value="'.floatval($expense_quantity).'" /></div>';
						echo '<div id="'.$i.'" style="display:inline-block;">Amt:<input type="number" class="do_input_new" step=".01" name="record['.$i.'][amount]" 
							'.($mastercard == 1 ? "readonly" : ($expense_type_id == 28 ? "readonly" : "" )).' value="'.floatval($expense_amount).'" /></div>';
						echo '<input type="hidden" name="record['.$i.'][orig]" value="'.floatval($expense_amount).'" />';
						echo '<div id="'.$i.'" style="display:inline-block;"><select class="do_input_new" name="record['.$i.'][billable]" 
							'.(in_array($project_id,$overhead_array) ? 'disabled' : '').' >
							<option value="1"';if($expense_billable == 1){echo 'selected="selected"';}echo' >Billable</option>';
						echo '<option value = "3"';if($expense_billable == 3){echo 'selected="selected"';}echo ' >No-Bill</option></select></div>';
						echo '<input type="hidden" name="record['.$i.'][billable_a]" value="'.$expense_billable.'">';
						echo '<div id="'.$i.'" style="display:inline-block;">Notes:<input type="text" class="do_input_new" size="25" name="record['.$i.'][notes]" value="'.$expense_notes.'" /></div>';
						echo '</li>';
						if(!empty($backup))
						{
							echo '<li>&nbsp;</li>';
							echo '<li><h3>Attached Backup:</h3><a href="/wp-content/expense_backup/'.rawurlencode($backup).'" target="_blank">'.$backup.'</a></li>';
						}
						echo '<li>&nbsp;</li>';
						echo '<li><input class="my-buttons" type="file" id="file'.$i.'" name="file'.$i.'[]" multiple="multiple" 
							onChange="checkFilesize();" />&nbsp;&nbsp;<input type="button" onClick="removeFile('.$i.');" value="Clear Files" /></li>';
					}
				}
				//$a++;
				//echo '<li>&nbsp;</li>';
				//echo '<li><input class="my-buttons" type="file" id="file'.$a.'" name="file'.$a.'[]" multiple="multiple" 
				//			onChange="checkFilesize();" />&nbsp;&nbsp;<input type="button" onClick="removeFile('.$a.');" value="Clear Files" /></li>';
				echo '<li><hr></li>';
					
				$rowstart = $t;
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

				for ($t = count($details);$t<=$totalrows;$t++)
				{
					$a++;
					echo '<li><div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][date]"><option value="">Date</option>';
					for ($i = $end; $i >= $start; $i = $i - 86400)
					{
						echo '<option value="'.$i.'" '.($i == $record_array[$t][0] ? 'selected="selected"' : '').'>'.date( 'm-d', $i).'</option>';
					}
					echo '</select></div><div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][project]" onChange="budget('.$t.');" >
						<option value="">Project</option>';
					foreach ($resultactive as $active)
					{
						echo '<option value="'.$active->ID.'" '.($record_array[$t][1] == $active->ID ? 'selected="selected"' : '').'>'.(empty($active->abbreviated_name) ? $active->gp_id : $active->abbreviated_name).'</option>';
					}
					foreach ($othercodesresults as $othercode)
					{echo '<option value="'.$othercode->other_project_code_value.'" '.($record_array[$t][1] === $othercode->other_project_code_value ? 'selected="selected"' : '').'>
						'.$othercode->other_project_code_name.'</option>';}
					if(!empty($personal_project))
					{echo '<option value="'.$personal_project.'" '.($record_array[$t][1] == $personal_project ? 'selected="selected"' : '').'>Personal</option>';}
					
					echo '</select></div>';		
					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][expense]" onChange="mileage('.$t.');">
						<option value="">Expense</option>';
						
					foreach($expensecoderesults as $code)
					{echo '<option value="'.$code->expense_code_id.'" '.($code->expense_code_id == $record_array[$t][2] ? 'selected="selected"' : '').'>'.$code->expense_code_name.'</option>';}
						
					echo '</select></div>';
					echo '<div id="d3" style="display:inline-block;">Qty:<input type="text" class="do_input_new" size="1" step=".01" name="record['.$t.'][quantity]" 
						value="'.$record_array[$t][3].'" placeholder="Qty"/></div>';
					echo '<div id="d3" style="display:inline-block;">Amt:<input type="text" class="do_input_new" size="1" step=".01" name="record['.$t.'][amount]" 
						value="'.$record_array[$t][4].'" placeholder="Amt"/></div>';
					echo '<div id="d3" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][billable]" 
						'.($record_array[$t][5] == 3 ? 'disabled="disabled"' : '').'>
						<option value="1" '.($record_array[$t][5] == 1 ? 'selected="selected"' : '').'>Billable</option>
						<option value="3" '.($record_array[$t][5] == 3 ? 'selected="selected"' : '').'>No-Bill</option></select></div>';							
					echo '<input type="hidden" name="record['.$t.'][billable_a]" value="'.$record_array[$t][7].'">';
					echo '<div id="d3" style="display:inline-block;">Notes:<input type="text" class="do_input_new" size="25" name="record['.$t.'][notes]" 
						value="'.$record_array[$t][6].'"/></div></li>';
					echo '<li>&nbsp;</li>';
					echo '<li><input class="my-buttons" type="file" id="file'.$a.'" name="file'.$a.'[]" multiple="multiple" 
							onChange="checkFilesize();" />&nbsp;&nbsp;<input type="button" onClick="removeFile('.$a.');" value="Clear Files" /></li>';
					echo '<li><hr></li>';
				}
				?>
				<script language="javascript" type="text/javascript">
					function removeFile(x){
						var fileField = document.getElementById('file' + x);
						fileField.value = "";
						checkFilesize();
					}
					function checkFilesize(){
						var myForm = document.forms.edit_emp_exp;
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
					function budget(x)
					{
						var myForm = document.forms.edit_emp_exp;
						var myProject = myForm.elements['record[' + x + '[project]'];
						var myBillable = myForm.elements['record[' + x + '][billable]'];
						
						var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
							"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
							"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
							"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008-INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
							"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAM2","BRDU2008TTEAMB","BRDU2008TTEAMW"
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
						var myForm = document.forms.edit_emp_exp;
						var myProject = myForm.elements['record[' + x + '][project]'];
						var myBillable = myForm.elements['record[' + x + '][billable]'];
						var myBillableA = myForm.elements['record[' + x + '][billable_a]'];
						if(overhead.indexOf(myProject.value) == -1 && noBill.indexOf(myProject.value) == -1){
							myBillable.value="1"; myBillable.disabled = false; myBillableA.value = 1;
							if(myProject.value == "<?php echo $personal_project;?>"){
								myBillable.disabled = true;
							}
						}
						else{myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;}
					}
					function mileage(x)
					{
						var myForm = document.forms.edit_emp_exp;
						var myControls = myForm.elements['record[' + x + '][expense]'];
						var myAmount = myForm.elements['record[' + x + '][amount]'];
						var origValue = myAmount.value;
						
						if(myControls.value == 28){myAmount.value=".575"; myAmount.readOnly = true;}
						if((myControls.value != 28) && origValue==".575"){myAmount.value=""; myAmount.readOnly = false;}
						if((myControls.value != 28) && !(origValue==".575")){myAmount.value=origValue; myAmount.readOnly = false;}
						
					}
				
				</script>

				<input type="hidden" name="y" value="<?php echo $y;?>"/>
				<li><input type="submit" name="add_rows" class="my-buttons" value="<?php echo "Add Five (5) rows";?>" /><?php echo "  Save before adding rows";?></li>
				<li>&nbsp;</li>
				<li><input type="submit" name="save-info-two" class="my-buttons" value="<?php echo "Save for Later"; ?>" />&nbsp;&nbsp;
				<?php
				if($delete != "no")
				{
				?>
				<input type="submit" name="delete-info" class="my-buttons" value="<?php echo "Delete"; ?>" />&nbsp;&nbsp;
				<?php
				}
				?>
				<input type="submit" name="submit-info-two" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></li>
			</ul>
				
</div>
	
</div> </div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Tips";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						echo '<li>To delete a line item, zero out the "amount" field.  You will need to change the expense type if the desired line was "Mileage"';
						?>
						</ul>
					</li>
				</ul>
			</div></div>
		</div></div>
		</form>
<?php 
	}  
	get_footer();
?>