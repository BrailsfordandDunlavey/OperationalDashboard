<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
	function sitemile_filter_ttl($title){return ("Edit Employee Expense - Admin");}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	get_header();
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$admins = array(94,11,235,293);
	if(!in_array($uid,$admins)){wp_redirect(get_bloginfo('siteurl')."/dashboard/"); exit;}
	
	$expense_report = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare(
		"select * from ".$wpdb->prefix."employee_expenses 
		
		where expense_report_id='%d'",
		$expense_report));
	
	//reset uid
	$uid = $details[0]->employee_id;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	$employee_gp_id = $rightsresults[0]->gp_id;
	$sphere = $rightsresults[0]->sphere;
	$amex = $rightsresults[0]->amex;
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		$records = ($_POST['record']);
		$amex_post = $_POST['amex'];
		if($amex_post == "on"){$ee_mastercard = 2;}else{$ee_mastercard = $details[0]->ee_mastercard;}
		$submit_date = time();
		$status = $details[0]->employee_expense_status;
		
		foreach($records as $record)
		{
			$exp_id = $record['id'];
			if(!empty($record['amount']) and !empty($exp_id))
			{
				$report = $expense_report;
				//$date = $record['date'];
				$project = $record['project'];
				$expense = $record['expense'];
				$quantity = $record['quantity'];
				$amount = $record['amount'];
				if($expense == 28 and $ee_mastercard!=1){$amount = 0.575;}//ensure mileage rate in case user disables JavaScript
				if($project == '0001AC' and $expense == 32){$project = '0001HR';}
				if(($project == '0001HR' or $project == '0001AD2012' or $project == '0001IT') and ($expense == 5 or $expense == 22)){$project == '0001AC';}
				$billable = $record['billable'];
				if(empty($billable)){$billable=$record['billable_a'];}
				$notes = $record['notes'];

				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set project_id=%s,expense_type_id=%d,
					expense_quantity=%f,expense_amount=%f,expense_billable=%d,edited_date=%d,edited_by=%d,employee_expense_notes=%s,
					employee_expense_status=%d,ee_mastercard=%d where employee_expense_id=%d",$project,$expense,$quantity,$amount,$billable,
					$submit_date,$current_user->ID,$notes,$status,$ee_mastercard,$exp_id));
				if($billable == 3)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=0 where employee_expense_id=%d",$exp_id));
				}
			}
			if((empty($record['amount']) or empty($record['quantity'])) and !empty($record['orig']))
			{
				$exp_id = $record['id'];
				$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."employee_expenses where employee_expense_id=%d",$exp_id));
			}
		}
				
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<?php
						echo "The expense report has been updated.";
						?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php 
		$_POST = array();
		get_footer();
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
				<?php
				if(empty($details))
				{
					echo 'No records match your selection.';
				}
				else
				{
					?>
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" disabled="disabled" style="background-color: gray;"/>
					<li>&nbsp;</li>
					<style>input[type=number]{width:90px;}</style>
					<?php 							
					if($amex != 0)
					{
						echo '<li><h3>AMEX</h3><p><input type="checkbox" name="amex" '.($mastercard==2 ? 'checked="checked"' : '' ).'/> Check if this is an AMEX entry<p></li>
							<li>&nbsp;</li>';
					}
					$active_projects = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID 
						from ".$wpdb->prefix."projects 
						inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
						where ".$wpdb->prefix."project_user.user_id =%d and ".$wpdb->prefix."projects.status=2 and project_parent=0",$uid));
					
					$projects_array = array();
					
					foreach($active_projects as $a){if(!in_array($a->ID,$projects_array)){array_push($projects_array,$a->ID);}}
					foreach($details as $d){if(!in_array($d->project_id,$projects_array)){array_push($projects_array,$d->project_id);}}
					
					$projects_string = "(";
					
					for($i=0;$i<count($projects_array);$i++)
					{
						if($i!=0){$projects_string .=",";}
						$projects_string .= "'".$projects_array[$i]."'";
					}
					$projects_string .= ")";
					
					$resultactive = $wpdb->get_results($wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,".$wpdb->prefix."projects.gp_id,expense_type 
						from ".$wpdb->prefix."projects 
						where ID in ".$projects_string.""));
					
					$othercodesresults = $wpdb->get_results("select * from ".$wpdb->prefix."other_project_codes where other_project_code_id!=1 and expense_available=1
						order by other_project_code_name");
					$personal_query = $wpdb->get_results($wpdb->prepare("select personal_project from ".$wpdb->prefix."useradd where user_id=%d",$uid));
					$personal_project = $personal_query[0]->personal_project;
					
					$expensecodequery = "select * from ".$wpdb->prefix."expense_codes where expense_code_rights='project'";
					if($sphere == 'Functional'){$expensecodequery = "select * from ".$wpdb->prefix."expense_codes";}
					$expensecoderesults = $wpdb->get_results($expensecodequery);
					
					$end = strtotime(date('Y-m-d'));
					$start = $end - 86400 * 60;
					
					$overhead_array = array("0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
							"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
							"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
							"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
							"0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAMB","BRDU2008TTEAMW","BRDU2008TTEAM2");
					for($i=0;$i<count($resultactive);$i++)
					{
						if($resultactive[$i]->expense_type == "No-Bill")
						{
							array_push($overhead_array,$resultactive[$i]->project_id);
						}
					}
					
					$t = -1;
					foreach($details as $detail)
					{
						$t++;
						$expense_id = $detail->employee_expense_id;
						$date = $detail->expense_date;
						$project_id = $detail->project_id;
						$expense_type_id = $detail->expense_type_id;
						$expense_quantity = $detail->expense_quantity;
						$expense_amount = $detail->expense_amount;
						$expense_billable = $detail->expense_billable;
						$expense_notes = $detail->employee_expense_notes;
						$status = $detail->employee_expense_status;
						$mastercard = $detail->ee_mastercard;
						$backup_query = $wpdb->get_results($wpdb->prepare("select expense_filename from ".$wpdb->prefix."expense_backup where employee_expense_id=%d",$expense_id));
						$backup = $backup_query[0]->expense_filename;
						array_push($expense_ids,$expense_id);
						
						echo '<li><input type="hidden" value="'.$expense_id.'" name="record['.$t.'][id]" />
							<div id="'.$t.'" style="display:inline-block;"><select name="record['.$t.'][date]" class="do_input_new" '.($mastercard > 0 ? "disabled" : "").' >';
						for ($i = $end; $i >= $start; $i = $i - 86400)
							{echo '<option value="'.$i.'"';if($i==$date){echo 'selected="selected"';}echo '>'.date( 'm-d', $i).'</option>';}
						echo '</select></div>';
						echo '<div id="'.$t.'" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][project]" onChange="budget('.$t.');" ><option value="">Project</option>';
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
						echo '<div id="'.$t.'" style="display:inline-block;"><select class ="do_input_new" name="record['.$t.'][expense]" onChange="mileage('.$t.');" 
							'.($mastercard > 0 ? "" : "onChange=\"mileage('.$t.');\"" ).'><option value="">Expense</option>';
						foreach($expensecoderesults as $code)
							{echo '<option value="'.$code->expense_code_id.'"';
							if($code->expense_code_id == $expense_type_id){echo 'selected="selected" ';}
							echo '>'.$code->expense_code_name.'</option>';}
						echo '</select></div>';
						echo '<div id="'.$t.'" style="display:inline-block;">Qty:<input type="number" class="do_input_new" step=".01" name="record['.$t.'][quantity]" 
							'.($mastercard == 1 ? "readonly" : "" ).' value="'.floatval($expense_quantity).'" /></div>';
						echo '<div id="'.$t.'" style="display:inline-block;">Amt:<input type="number" class="do_input_new" step=".01" name="record['.$t.'][amount]" 
							'.($mastercard > 0 ? "readonly" : ($expense_type_id == 28 ? "readonly" : "" )).' value="'.floatval($expense_amount).'" /></div>';
						echo '<input type="hidden" name="record['.$t.'][orig]" value="'.floatval($expense_amount).'" />';
						echo '<div id="'.$t.'" style="display:inline-block;"><select class="do_input_new" name="record['.$t.'][billable]" 
							'.(in_array($project_id,$overhead_array) ? 'disabled' : '').' >
							<option value="1"';if($expense_billable == 1){echo 'selected="selected"';}echo' >Billable</option>';
						echo '<option value = "3"';if($expense_billable == 3){echo 'selected="selected"';}echo ' >No-Bill</option></select></div>';
						echo '<input type="hidden" name="record['.$t.'][billable_a]" value="'.$expense_billable.'">';
						echo '<div id="'.$t.'" style="display:inline-block;">Notes:<input type="text" class="do_input_new" size="25" name="record['.$t.'][notes]" value="'.$expense_notes.'" /></div></li><li><hr></li>';
						if(!empty($backup))
						{
							echo '<li>&nbsp;</li>';
							echo '<li><h3>Attached Backup:</h3><a href="/wp-content/expense_backup/'.rawurlencode($backup).'" target="_blank">'.$backup.'</a></li>';
						}
					}
					?>
					<script language="javascript" type="text/javascript">
						function budget(x){
							var myForm = document.forms.edit_emp_exp;
							var myProject = myForm.elements['record[' + x + '[project]'];
							var myBillable = myForm.elements['record[' + x + '][billable]'];
							
							var overhead = ["0001","0001AC","0001AD2012","0001HR","0001IT","0001MK","0002","0003","0004","0005","0005C","0005R","0006","0007","0008",
								"0008A","0008B","0008C","0008E","0008M","0008R","0008W","0009","0009O","0002CONF","0003CONF","0004CONF","0005CONF","0005CCONF","0005RCONF",
								"0006CONF","0007CONF","0008-CONF","0008ACONF","0008BCONF","0008CCONF","0008ECONF","0008MCONF","0008RCONF","0008WCONF","0009CONF","0009OCONF",
								"0002INTV","0003INTV","0004INTV","0005INTV","0005CINTV","0005RINTV","0006INTV","0007INTV","0008INTV","0008AINTV","0008BINTV","0008CINTV","0008EINTV",
								"0008FINTV","0008MINTV","0008RINTV","0008WINTV","0009INTV","0009OINTV","BRDU2008TTEAM2","BRDU2008TTEAMB","BRDU2008TTEAMW"
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
							}
							else{myBillable.value="3"; myBillable.disabled = true; myBillableA.value = 3;}
						}
						function mileage(x){
							var myForm = document.forms.edit_emp_exp;
							var myControls = myForm.elements['record[' + x + '][expense]'];
							var myAmount = myForm.elements['record[' + x + '][amount]'];
							var origValue = myAmount.value;
							
							if(myControls.value == 28){myAmount.value=".575"; myAmount.readOnly = true;}
							if((myControls.value != 28) && origValue==".575"){myAmount.value=""; myAmount.readOnly = false;}
							if((myControls.value != 28) && !(origValue==".575")){myAmount.value=origValue; myAmount.readOnly = false;}
							
						}
						function save(){//allow for save once checked that the transaction is confirmed as being in GP
							var myForm = document.forms.edit_emp_exp;
							var saveButtonOne = myForm.elements['save-info'];
							var saveButtonTwo = myForm.elements['save-info-two'];
							var checkbox = myForm.elements['checkbox'];
							
							if(checkbox.checked == true){
								saveButtonOne.disabled = false;
								saveButtonTwo.disabled = false;
								saveButtonTwo.style.backgroundColor = "red";
								saveButtonOne.style.backgroundColor = "red";
							}
							else{
								saveButtonOne.disabled = true;
								saveButtonTwo.disabled = true;
								saveButtonOne.style.backgroundColor = "gray";
								saveButtonTwo.style.backgroundColor = "gray";
							}
						}
					</script>
					<li>Check if this edit is already in GP:  <input type="checkbox" name="checkbox" onclick="save();"/></li>
					<li>&nbsp;</li>
					<li><input type="submit" name="save-info-two" class="my-buttons" value="<?php echo "Save"; ?>" disabled="disabled" style="background-color: gray;" />
					<?php
				}
				?>
</div>
	
</div> </div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Attached Backup";?></h3>
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
							$expense_ids = "";
							for($i=0;$i<count($details);$i++)
							{
								if($i<count($details)-1){$value = $details[$i]->employee_expense_id.",";}
								else{$value = $details[$i]->employee_expense_id;}
								$expense_ids .= $value;
							}
							if(!empty($expense_ids))
							{
								$backup_result = $wpdb->get_results($wpdb->prepare("select expense_backup_id,expense_filename from ".$wpdb->prefix."expense_backup 
									where expense_id in ( %s ) or expense_report_id=%d",$expense_ids,$expense_report));
							}
							if(empty($backup_result)){echo "There is no backup attached yet.";}
							else
							{
								echo '<table width="100%">';
								foreach ($backup_result as $backup)
								{
									echo '<tr><th><a href="/wp-content/expense_backup/'.rawurlencode($backup->expense_filename).'" target="_blank" >'
										.$backup->expense_filename.'</a></th>';
									echo '<th><a href="/?p_action=edit_backup&ID='.$backup->expense_backup_id.'" class="my-buttons" style="color:#ffffff;">Edit</a></th>';
									echo '<th><a href="/?p_action=delete_backup&ID='.$backup->expense_backup_id.'" class="my-buttons" style="color:#ffffff;" >Delete</a></th>';
									echo '</tr>';
								}
								echo '</table>';
							}?>
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