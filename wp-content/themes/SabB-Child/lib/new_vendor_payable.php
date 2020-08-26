<?php
function billyB_new_vendor_payable()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
 	$employeegpidquery = $wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$employeegpidresult = $wpdb->get_results($employeegpidquery);
	$sphere = $employeegpidresult[0]->sphere;
	$today = time();
	$cutoff = strtotime(date("Y-m-d", strtotime($today)) . " -60 days");
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']) or isset($_POST['submit-info']) or isset($_POST['submit-info-two']))
	{
		if(isset($_POST['save-info']) or isset($_POST['save-info-two'])){$status = 0;}else{$status = 1;}
		$date = $_POST['date'];
		$vendor = $_POST['vendor_id'];
		$invoice = $_POST['invoice'];
		$project_id = $_POST['project_id'];
		$expense = $_POST['expense'];
		$exp_billable = $_POST['exp_billable'];
		if(empty($exp_billable)){$exp_billable = $_POST['exp_billable_a'];}
		$fee = $_POST['fee'];
		$fee_billable = $_POST['fee_billable'];
		if(empty($fee_billable)){$fee_billable = $_POST['fee_billable_a'];}
		if($fee==0){$fee_billable = 3;}
		$exp_amount = $_POST['exp_amount'];
		if($exp_amount == 0){$exp_billable=3;}
		$notes = $_POST['notes'];
		$billed_month = $_POST['billed_month'];
		$assignment = $_POST['assigned_to'];
		$now = time();
		if((empty($date) or empty($vendor) or empty($invoice) or empty($project_id) or empty($expense) or empty($exp_billable) or ($fee+$exp_amount==0) or empty($fee_billable)) and $status==1)
		{
			$status = 0;
			$empty = "yes";
		}
		
		$duplicate = $wpdb->get_results($wpdb->prepare("select vendor_payable_id from ".$wpdb->prefix."vendor_payables where vendor_id=%d and invoice_number=%s",$vendor,$invoice));
		if(empty($duplicate))
		{
		
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendor_payables (vendor_id,expense_date,invoice_number,project_id,expense_type_id,vendor_fee,
				vendor_expense,expense_billable,fee_billable,submit_date,expense_status,notes,submitted_by,assigned_to,billed_month) 
				values(%d,%d,%s,%s,%d,%f,%f,%d,%d,%d,%d,%s,%d,%d,%d)",$vendor,$date,$invoice,$project_id,$expense,$fee,$exp_amount,$exp_billable,$fee_billable,$now,$status,$notes,$uid,$assignment,$billed_month));
			$expense_id = $wpdb->insert_id;
			
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendor_payables_status (assigned_by,assigned_to,status_date,payable_id)
				values(%d,%d,%d,%d)",$uid,$assignment,$now,$expense_id));
			
			$current_dir = getcwd();
			$target_dir = $current_dir."/wp-content/expense_backup";
						
			foreach ($_FILES['fileToUpload']['name'] as $f => $name)
			{
				$file_name = time()." - ".basename($name);
				$target_file = $target_dir . "/" . $file_name;
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$f], $target_file))
				{
					$file_message = "The file: ".$file_name. " has been uploaded.";
		
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendor_backup (expense_id,expense_filename) values (%d,%s)",$expense_id,$file_name));
				}
				else
				{
					foreach($_FILES['fileToUpload']['error'] as $error)
					{
						if($error == 1){$file_message = "File was not uploaded because it was too large.<br/><br/>"; }
						if($error == 2){$file_message = "File was not uploaded because it was too large.<br/><br/>"; }
						if($error == 3){$file_message = "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
						if($error == 4){$file_message = "No attachement.<br/><br/>"; }
						if($error > 4 or $error < 1){$file_message = "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
					}
				}
			}
		
			echo '<div id="content"><div class="my_box3"><div class="padd10">';
			if($status == 0)
			{
				$message = "Your transaction has been SAVED.";
				if(empty($date)){$message .= "<br/>You will need to enter a date before submitting.";}
				if(empty($vendor)){$message .= "<br/>You will need to select a vendor before submitting.";}
				if(empty($invoice)){$message .= "<br/>You will need to enter an invoice number before submitting.";}
				if(empty($project_id)){$message .= "<br/>You will need to select a project before submitting.";}
				if(empty($expense)){$message .= "<br/>You will need to select an expense before submitting.";}
				if(empty($exp_billable)){$message .= "<br/>You will need to select billable or no-bill for the expenses before submitting.";}
				if(empty($fee_billable)){$message .= "<br/>You will need to select billable or no-bill for the fee before submitting.";}
				if($fee+$exp_amount==0){$message .= "<br/>You will need to enter a fee or expense amount before submitting.";}
				echo $message;
				echo '<br/>Files:  '.$file_message;
				echo '<br/>You can revise your entry <a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$expense_id.'">here</a>';
				echo '<br/>You can also return to your <a href="'.get_bloginfo('siteurl').'/dashboard/">dashboard</a> or enter a <a href="'.get_bloginfo('siteurl').'/vendor-payable/">new invoice</a>.';
			}
			else
			{
				echo "The invoice has been submitted for processing.  Thank you.";
				echo '<br/>Files:  '.$file_message;
				echo '<br/>You can return to your <a href="'.get_bloginfo('siteurl').'/dashboard/">dashboard</a> or enter a <a href="'.get_bloginfo('siteurl').'/vendor-payable/">new invoice</a>.';
			}
			echo '</div></div></div></div>';
		}
		else
		{
			echo '<div id="content"><div class="my_box3"><div class="padd10">';
			echo 'There is a record of this invoice for this vendor already in the system - <a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$duplicate[0]->vendor_payable_id.'">HERE</a>.';
			echo '</div></div></div></div>';
		}
		get_footer();
		$_POST = array();
		exit;
	}
?>
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
						<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" />&nbsp;&nbsp;
						<input type="submit" name="submit-info" class="my-buttons-submit" value="<?php echo "Submit"; ?>" /></p></li>
						<li>&nbsp;</li>
						<style>
						input[type=number]{width:125px;}
						</style>
						
<?php 							
						$all_users_results = $wpdb->get_results("select display_name,user_id from ".$wpdb->prefix."users
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where status=1 order by display_name");
						
						$end = strtotime(date('Y-m-d'));
						$start = $end - 86400 * 60;							
							
						$queryactive = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,gp_id,expense_type 
							from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."project_user.project_id 
							where ".$wpdb->prefix."project_user.user_id=%d and ".$wpdb->prefix."projects.status=2 and project_parent=0
							order by abbreviated_name",$uid);
						if($uid==293 or $uid==235 or $uid==94 or $uid==11)
						{
							$queryactive = $wpdb->prepare("select distinct ".$wpdb->prefix."projects.ID,abbreviated_name,".$wpdb->prefix."projects.gp_id,expense_type 
							from ".$wpdb->prefix."projects 
							where ".$wpdb->prefix."projects.status=2 and project_parent=0
							order by ".$wpdb->prefix."projects.gp_id");
						}
						$resultactive = $wpdb->get_results($queryactive);							
							
						$othercodesquery = "select * from ".$wpdb->prefix."other_project_codes 
							where expense_available=1
							order by other_project_code_name";
						$othercodesresults = $wpdb->get_results($othercodesquery);
						
						$expense_query = "select * from ".$wpdb->prefix."vendor_expense_codes order by v_exp_name";
						if($sphere != "Functional"){$expense_query = "select * from ".$wpdb->prefix."vendor_expense_codes where rights='project' order by v_exp_name";}
						$expense_results = $wpdb->get_results($expense_query);
						
						echo '<li><h3>Assigned to:</h3><p><select class="do_input_new" name="assigned_to">';
						foreach($all_users_results as $aur)
						{
							echo '<option value="'.$aur->user_id.'" '.($uid==$aur->user_id ? "selected='selected'" : "").'>'.$aur->display_name.'</option>';
						}
						echo '</select></p></li>';
						echo '<li><h3>Invoice Date</h3><p><select class="do_input_new" name="date"><option value="">Date</option>';
						for ($i = $end; $i >= $start; $i = $i - 86400)
						{
							echo '<option value="'.$i.'" >'.date( 'm-d', $i).'</option>';
						}
						echo '</select></p></li>';
						echo '<li><input type="hidden" name="vendor_id" />
							<h3>Vendor</h3><p><input type="text" name="vendor_name" class="do_input_new full_wdth_me" 
								title="You must click a vendor from the list" onkeyup="checkVendor(this.value);"/></p></li>';
						?>
						<span id="vendor_buttons"></span>
						<script type="text/javascript">
							function checkVendor(vals)
							{
								var myForm = document.forms.new_exp;
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
								var myForm = document.forms.new_exp;
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
						echo '<li><h3>Vendor Invoice Number</h3><p><input type="text" name="invoice" class="do_input_new full_wdth_me" /></p></li>';
						echo '<li><h3>Project</h3><p><select name="project_id" class="do_input_new" onchange="budget();"><option value="">Select Project</option>';
						foreach($resultactive as $ra)
						{
							if(!empty($ra->abbreviated_name)){$project = $ra->abbreviated_name;}
							else{$project = $ra->gp_id;}
							echo '<option value="'.$ra->ID.'">'.$project.'</option>';
						}
						foreach($othercodesresults as $ocr)
						{
							echo '<option value="'.$ocr->other_project_code_value.'">'.$ocr->other_project_code_name.'</option>';
						}
						echo '</select></p></li>';
						echo '<li><h3>Expense</h3><p><select name="expense" class="do_input_new">
							<option value="">Select Expense</option>';
						foreach($expense_results as $er)
						{
							echo '<option value="'.$er->vendor_exp_code_id.'" '.($er->vendor_exp_code_id==56 ? 'selected="selected"' : '').'>'.$er->v_exp_name.'</option>';
						}
						echo '</select></p></li>';
						echo '<li><h3>Fee Amount</h3><p><input type="number" step=".01" class="do_input_new" name="fee" />
							<input type="hidden" name="fee_billable_a" />
							<select name="fee_billable" class="do_input_new"><option value="1">Billable</option><option value="3">No-Bill</option></select>
							</p></li>';
						echo '<li><h3>Expense Amount</h3><p><input type="number" step=".01" class="do_input_new" name="exp_amount" />
							<input type="hidden" name="exp_billable_a" />
							<select name="exp_billable" class="do_input_new"><option value="1">Billable</option><option value="3">No-Bill</option></select>
							</p></li>';
						
						echo '<li><h3>Month Billed to Client<br/>(month services being billed<br/>not month actually billing)</h3>
								<p><select name="billed_month" class="do_input_new" >';
						
						for($i=0;$i<6;$i++)
						{
							$date = strtotime(date('Y-m-01',time()) .' - '.$i.' months');
							echo '<option value="'.$date.'">'.date('m-Y',$date).'</option>';
						}
						
						echo '</select></p></li>';
						
						echo '<li><h3>Notes</h3><p><input type="text" name="notes" class="do_input_new full_wdth_me" /></p></li>';
						?>
						<script type="text/javascript">
						function budget(){
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
							var myProject = myForm.elements['project_id'];
							var expBillable = myForm.elements['exp_billable'];
							var expBillableA = myForm.elements['exp_billable_a'];
							var feeBillable = myForm.elements['fee_billable'];
							var feeBillableA = myForm.elements['fee_billable_a'];
							
							if(overhead.indexOf(myProject.value) != -1 || noBill.indexOf(myProject.value) != -1){
								expBillable.value="3"; expBillable.disabled = true; expBillableA.value = "3";
								feeBillable.value="3"; feeBillable.disabled = true; feeBillableA.value = "3";
							}
							else{
								expBillable.disabled = false;
								feeBillable.disabled = false;
							}
						}
						</script>
						<li>&nbsp;</li>
						<li><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></li>
						<li>&nbsp;</li>
						<li><input type="submit" name="save-info-two" class="my-buttons" value="Save" />&nbsp;&nbsp;
						<input type="submit" name="submit-info-two" class="my-buttons-submit" value="Submit" /></li>
					</ul>
</div>
</div> </div></form>
<div id="right-sidebar" class="page-sidebar"><div class="padd10">
			<h3>Tips:</h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					You must select a vendor the populates from the search.  If the vendor doesn't exist in the list, contact Maresha Leizear before proceeding.
					</ul>
				</li>
			</ul>
		</div></div>
<?php }
add_shortcode('new_vendor_payable','billyB_new_vendor_payable')
?>