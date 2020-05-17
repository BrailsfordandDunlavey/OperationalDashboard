<?php
function billyB_contract_checklist()
{
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	
	$useradd_details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$user_sphere = $useradd_details[0]->sphere;
	
	$all_subcategories = $wpdb->get_results("select ".$wpdb->prefix."terms.term_id,name,parent from ".$wpdb->prefix."terms
			inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."terms.term_id=".$wpdb->prefix."term_taxonomy.term_id
			where taxonomy='project_cat' and parent!=0");
	$all_customers = $wpdb->get_results("select client_id,client_name,client_gp_id from ".$wpdb->prefix."clients");
	$all_teams = $wpdb->get_results("select distinct team,sphere from ".$wpdb->prefix."useradd where team!='' and sphere!='functional' order by sphere,team");
	
	
	if(isset($_POST['save-info']) or isset($_POST['submit-info']))
	{
		$client_id = trim($_POST['client_id']);
		$named_client = trim($_POST['named_client']);
		if(empty($client_id) and !empty($named_client))
		{
			$name_check = $wpdb->get_results($wpdb->prepare("select client_id from ".$wpdb->prefix."clients where client_name=%s",$named_client));
			if(empty($name_check))
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values (%s)",$named_client));
				$client_id = $wpdb->insert_id;
			}
			else{$client_id = $name_check[0]->client_id;}
		}
		
		$prime_id = trim($_POST['prime_id']);
		$named_prime = trim($_POST['named_prime']);
		if(empty($prime_id) and !empty($named_prime))
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values (%s)",$named_prime));
			$prime_id = $wpdb->insert_id;
		}
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
		$sub_fee = trim($_POST['sub_fee_amount']);
		$expense_amount = trim($_POST['expense_amount']);
		$expense_type = trim($_POST['expense_type']);
		$market = trim($_POST['project_cat_cat']);
		$submarket = trim($_POST['subcat']);
		$confidential = trim($_POST['confidential']);
		$venues = trim($_POST['venues']);
		$perdiem = $_POST['perdiem'];
		$receipts = $_POST['receipts'];
		$background = 0;if($_POST['background']=="on"){$background=1;}
		$return = 0; if($_POST['return']=="on"){$return=1;}
		$does = 0; if($_POST['does']=="on"){$does=1;}
		$markup = $_POST['markup'];
		$retainer = $_POST['retainer'];
		$retention = $_POST['retention'];
		$client_portal = $_POST['client_portal'];
		$contact = trim($_POST['contact']);
		$address = trim($_POST['address']);
		$city = trim($_POST['city']);
		$state = trim($_POST['state']);
		$zip = $_POST['zip'];
		$email = trim($_POST['email']);
		$phone = trim($_POST['phone']);
		$delivery_type = trim($_POST['delivery_type']);
		$notes = trim($_POST['notes']);
		$win_loss = 0;
		$status = 0;
		$submit_date = 0;
		if(isset($_POST['submit-info']))
		{
			$status = 1;
			$submit_date = time();
			$win_loss = 1;
		}
		if($receipts == "on"){$receipts_required = 1;}else{$receipts_required = 0;}
		$querya = $wpdb->prepare("insert into ".$wpdb->prefix."projects (project_author,client_id,prime_id,project_name,abbreviated_name,sphere,project_group,project_manager,fee_type,initiation_document,current_document,
			document_number,estimated_start,project_type,fee_amount,sub_fee_amount,expense_amount,expense_type,receipt,market,submarket,confidential,venues,markup,retainer,retention,client_portal,perdiem,
			contact,address,city,state,zip,email,phone,delivery_type,notes,status,submitted_date,win_loss,return_destroy,background)
			values(%d,%d,%d,%s,%s,%s,%s,%d,%s,%s,%s,%s,%d,%s,%f,%f,%f,%s,%d,%d,%d,%s,%s,%s,%f,%f,%s,%s,%s,%s,%s,%d,%s,%s,%s,%s,%s,%d,%d,%d,%d,%d)",
			$uid,$client_id,$prime_id,$project_name,$abb_name,$sphere,$project_group,$project_manager,$fee_type,$initiation_document,$initiation_document,$document_number,$estimated_start,$project_type,
			$fee_amount,$sub_fee,$expense_amount,$expense_type,$receipts_required,$market,$submarket,$confidential,$venues,$markup,$retainer,$retention,$client_portal,$perdiem,$contact,$address,$city,$state,
			$zip,$email,$phone,$delivery_type,$notes,$status,$submit_date,$win_loss,$return,$background);	
		$wpdb->query($querya);
		
		$project_id = $wpdb->insert_id;							
		
		if(!empty($project_manager) and $project_manager != 0)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values (%d,%d,'Team Lead')",
				$project_id,$project_manager));
		}
			
		if(!empty($project_team))
		{
			foreach ($project_team as $key => $value)
			{
				if($value != $project_manager)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values (%d,%d,'Work Team')",
						$project_id,$value));
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
					values (%d,%s,'Contract')",$project_id,$file_name));
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
		$proposal = time()." - ".basename($_FILES["proposalUpload"]["name"]);
		$proposal_file = $target_dir . "/" . $proposal;
		if (move_uploaded_file($_FILES["proposalUpload"]["tmp_name"], $proposal_file))
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_docs (project_id,project_doc_name,project_doc_type) 
				values (%s,%s,'Proposal')",$project_id,$proposal));
			
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
		//************************************************************************************************
		if(isset($_POST['submit-info']))
		{					
			$link = get_bloginfo('siteurl').'/?p_action=edit_checklist&ID='.$project_id;
			//BillyB update $to when projects are all setup
			$to = array('bbannister@programmanagers.com','mmitchell@programmanagers.com','npereira@programmanagers.com');
			wp_mail($to,'New Contract Checklist',$user_name.' has submitted a new contract checklist at: '.$link);
			$message = "Thank you.  The contract checklist has been submitted for processing.<br/><br/>";
		}
		else
		{
			$message = "The checklist has been saved for future use.<br/>You will still need to submit the checklist before it can be processed.<br/><br/>";
		}
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10"><?php echo $message; ?>
		<a href="<?php bloginfo('siteurl');?>/?edit_checklist&ID=<?php echo $project_id;?>">Edit this Checklist</a><br/><br/>
		<a href="<?php bloginfo('siteurl');?>/contract-checklist/">Enter a new Contract Checklist</a><br/><br/>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>
		<?php 
	}
	else
	{
		?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="contract_checklist" enctype="multipart/form-data">
		<div id="content"><h3>Project Details</h3><br/>
			<div class="my_box3">
				<div class="padd10">							
					<ul class="other-dets_m">
						<li>
							<h3>Client Name:</h3>
							<p><input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
							<input type="text" name="named_client" class="do_input_new full_wdth_me" 
							 onkeyup="checkCustomer(this.value);" />
							</p>
						</li>
						<span id="client_buttons"></span>
						<script type="text/javascript">
						function checkCustomer(vals){
							var myForm = document.forms.contract_checklist;
							var span = document.getElementById('client_buttons');
							span.style.display = 'block';
							jQuery.post("<?php bloginfo('siteurl'); ?>/?check_customers=1", {search_term: ""+vals+""}, function(data){
								if(data.length >0) {
									jQuery('#client_buttons').html(data);
								}
							});
						}
						function setCustomer(id,name){
							var myForm = document.forms.contract_checklist;
							var clientId = myForm.elements['client_id'];
							var clientName = myForm.elements['named_client'];
							var span = document.getElementById('client_buttons');
							clientId.value = id;
							//name = str.replace("'","");
							clientName.value = name;
							span.style.display = 'none';
						}
						</script>
						<li>
							<h3>Prime Name:<br/>(enter if contract is not with the client)</h3>
							<p><input type="hidden" name="prime_id" value="<?php echo $prime_id;?>" />
							<input type="text" name="named_prime" class="do_input_new full_wdth_me" value="<?php echo $prime_name;?>" 
								onkeyup="checkPrime(this.value);" />
							</p>
						</li>
						<span id="prime_buttons"></span>
						<script type="text/javascript">
						function checkPrime(vals){
							var myForm = document.forms.contract_checklist;
							var span = document.getElementById('prime_buttons');
							span.style.display = 'block';
							jQuery.post("<?php bloginfo('siteurl'); ?>/?check_prime=1", {search_term: ""+vals+""}, function(data){
								if(data.length >0) {
									jQuery('#prime_buttons').html(data);
								}
							});
						}
						function setPrime(id,name){
							var myForm = document.forms.contract_checklist;
							var clientId = myForm.elements['prime_id'];
							var clientName = myForm.elements['named_prime'];
							var span = document.getElementById('prime_buttons');
							clientId.value = id;
							clientName.value = name;
							span.style.display = 'none';
						}
						</script>
						<li>
							<h3>Project Name:</h3>
							<p><input type="text" name="project_name" class="do_input_new full_wdth_me"/></p>
						</li>
						<li>
							<h3>Project Abbreviated Name: <br/>(max 25 characters)</h3>
							<p><input type="text" name="abb_name" class="do_input_new full_wdth_me" maxlength="25"/></p>
						</li>
						<li><h3>Sphere:</h3>
							<p><select class ="do_input_new" name="sphere">
							<option value="">Select Sphere</option>
							<option <?php if($user_sphere=="Higher Ed"){echo 'selected="selected"';}?>>Higher Ed</option>
							<option <?php if($user_sphere=="Sphere KMV"){echo 'selected="selected"';}?>>Sphere KMV</option>
							</select></p>
						</li>
						<li><h3>Group/Cluster:</h3>
							<p><select class="do_input_new" name="project_group">
							<?php
							foreach($all_teams as $at)
							{
								echo '<option value="'.$at->team.'">'.$at->sphere.':  '.$at->team.'</option>';
							}
							?>
							</select></p>
						</li>
						<li>
							<h3>Project Manager:</h3>
							<?php
							$userquery = "select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users 
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
								where display_name not in('admin','bbannister','test') and status=1 order by display_name asc";
							$users = $wpdb->get_results($userquery);
							?>
							<p>
							<?php
							echo '<select name="project_manager" class="do_input_new">';
							echo '<option value="">Select Project Manager</option>';
							
							foreach($users as $user)
							{
								echo '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
							}
							echo '</select>';
							?>
							</p>
						</li>							
						<li><h3>Team Members:<br/>Hold ctrl to select multiple</h3>
							<p><?php
							echo '<select name="project_team[]" size="10" multiple="multiple">';
							foreach($users as $user)
							{
								echo '<option '.($selected == $user->ID ? "selected='selected'" : "" ).' value="'.$user->ID.'">'.$user->display_name.'</option>';
							}
							echo '</select>';
							?>
							</p>
						</li>
						<li><h3>Fee Type:</h3>
							<p><select class ="do_input_new" name="fee_type">
							<option value="">Select Fee Type</option>
							<option>Fixed Fee</option>
							<option>T&M (as used)</option>
							<option>T&M (to maximum)</option>
							<option>Percent Complete</option>
							</select>
						</li>
						<li><h3>Initiation Document:</h3>
							<p><select class ="do_input_new" name="initiation_document">
							<option value="">Select Document</option>
							<option>Contract</option>
							<option>Purchase Order</option>
							<option>Letter of Intent</option>
							<option>Executive Override</option>								
							</select></p>
						</li>
						<li><h3>Contract:</h3><p><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></p></li>
						<li><h3>Proposal:</h3><p><input class="my-buttons" type="file" name="proposalUpload[]" multiple="multiple" id="proposalUpload" /></p></li>
						<li><h3>Document Number (P.O. Number, etc.):</h3><p><input type="text" name="document_number" class="do_input_new full_wdth_me"/></p></li>	
						<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
						<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
						<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>							
						<li><h3>Estimated Start:</h3><p><input type="text" id="start" name="estimated_start" class="do_input_new full_wdth_me"/></p></li>
							<script>
							<?php
							$start = date_i18n('m-d-Y',$estimated_start);
							$dd = 90;
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
							<option value="">Select Project Type</option>
							<option>Project Definition</option>
							<option>MAS Planning</option>
							<option>P3 Advisory</option>
							<option>P3 Owner's Rep</option>
							<option>Full Service PM</option>
							</select></p>
						</li>
						<li><h3>B&D Fee Amount:</h3><p>$<input type="number" step=".01" min="0" name="fee_amount" class="do_input_new"
							onblur="updateTotalContract();"/></p></li>
						<li><h3>Sub Fee Amount:</h3><p>$<input type="number" step=".01" min="0" name="sub_fee_amount" class="do_input_new"
							onblur="updateTotalContract();"/></p></li>
						<li><h3>Expense Amount:</h3><p>$<input type="number" step=".01" min="0" name="expense_amount" class="do_input_new"
							onblur="updateTotalContract();"/></p></li>
						<li><h3>Total Contract:</h3><p>$<input type="number" disabled class="do_input_new" name="contract_total" /><p></li>
						<script type="text/javascript">
							function updateTotalContract(){
								var myForm = document.forms.contract_checklist;
								var feeBD = myForm.elements['fee_amount'];
								var feeSub = myForm.elements['sub_fee_amount'];
								var expense = myForm.elements['expense_amount'];
								var total = myForm.elements['contract_total'];
								var amount = ((feeBD.value*100)+(feeSub.value*100)+(expense.value*100))/100;
								
								total.value = amount;
							}
						</script>
						<li><h3>Expense Type:</h3>
							<p><select class ="do_input_new" name="expense_type">
							<option value="">Select Expense Type</option>
							<option>Reimbursable</option>
							<option>No-Bill</option>
							<option>Unlimited</option>
							</select></p>
						</li>
						<li><h3>Market:</h3>
							<script>
							function display_subcategories(x){
								var myForm = document.forms.contract_checklist;
								//show / hide the span of subcategories
								var span = document.getElementById("sub_cats");
								var subSelect = myForm.elements['subcat'];
								if(x==0){
									subSelect.style.display = "none";
								}
								else{
									subSelect.style.display = "block";
								}
								//remove options if there are any
								for(i=subSelect.options.length - 1;i>=0;i--){
									subSelect.remove(i);
								}
								var array = [
								<?php
								for($i=0;$i<count($all_subcategories);$i++)
								{
									echo '['.$all_subcategories[$i]->term_id.',"'.$all_subcategories[$i]->name.'",'.$all_subcategories[$i]->parent.']';
									if($i<count($all_subcategories) - 1){echo ',';}
								}
								?>
								];
								//create options if the parent = market
								for(i=0;i<array.length;i++){
									if(array[i][2] == x){
										var option = document.createElement('option');
										option.value = array[i][0];
										option.innerHTML = array[i][1];
										subSelect.appendChild(option);
									}
								}
							}
							</script>
							<p>
							<?php
							$display = "display_subcategories";
							echo projectTheme_get_categories_clck("project_cat",  
							!isset($_POST['project_cat_cat']) ? (is_array($cat) ? $cat[0]->term_id : "") : htmlspecialchars($_POST['project_cat_cat'])
							, __('Select Category','ProjectTheme'), "do_input_new", 'onchange="'.$display.'(this.value)"' );
							
							echo '<br/><span id="sub_cats">';
							echo '<select class="do_input_new" name="subcat" style="display:none;"></select>';
							echo '</span>';
							?>
							</p>
						</li>
						<li><h3>Confidential:</h3><p><input type="checkbox" name="confidential"></p></li>
						<li><h3>Venues:</h3><p><input type="checkbox" name="venues"></p></li>
						<li>
						<h3>Per Diem Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
							placeholder="Reference each per diem limitation"  name="perdiem"></textarea></p>
						</li>
						<li><h3>Receipts Required:</h3><p><input type="checkbox" name="receipts" ></p></li>
						<li><h3>Return and/or Destroy Requirement:</h3><p><input type="checkbox" name="return" ></p></li>
						<li><h3>Background Checks Required:</h3><p><input type="checkbox" name="background" ></p></li>
						<li><h3>DOES:</h3><p><input type="checkbox" name="does" ></p></li>
						<li><h3>10% Markup:</h3><p><input type="checkbox" name="markup"></p></li>
						<li><h3>Retainer:</h3><p>$<input type="number" step=".01" class="do_input_new" name="retainer"></p></li>
						<li><h3>Retention:</h3><p><input type="number" step=".01" min="0" max="100" class="do_input_new" name="retention">%</p></li>
						<li><h3>Client Portal:</h3><p><input type="text" class="do_input_new full_wdth_me" name="client_portal" /></p></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="content"><h3>Invoicing Details</h3><br/>
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li><h3>Contact:</h3><p><input type="text" name="contact" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>Address:</h3><p><input type="text" name="address" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>City:</h3><p><input type="text" name="city" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>State:</h3>
							<p><select name="state" class="do_input_new" >
							<option value="">Select State</option>
							<?php
							$state_query = "select state_id,state_abbreviation from ".$wpdb->prefix."states order by state_abbreviation";
							$state_results = $wpdb->get_results($state_query);
							foreach($state_results as $state)
							{
								echo '<option value="'.$state->state_id.'" >'.$state->state_abbreviation.'</option>';
							}
							?>
							</select></p>
						</li>
						<li><h3>Zip:</h3><p><input type="text" name="zip" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>Email:</h3><p><input type="text" name="email" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>Phone:</h3><p><input type="text" name="phone" class="do_input_new full_wdth_me"/></p></li>
						<li><h3>Delivery Type:</h3>
							<p><select class ="do_input_new" name="delivery_type">
							<option value="">Select Delivery Type</option>
							<option>Email</option>
							<option>Hard Copy</option>
							<option>Both</option>
							</select>
							</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
        <div id="content"><h3>Project Notes</h3><br/>
			<div class="my_box3">
				<div class="padd10">							
					<ul class="other-dets_m">
					<li>
						<h3>Internal Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
						placeholder="Make any notes about this project that other staff should know"  name="notes"></textarea></p>
					</li>
					</ul>        
				</div>
			</div>
			<ul class="other-dets_m">
				<li>&nbsp;</li>
				<li>
					<p><input type="submit" name="save-info" class="my-buttons" value="Save for Later" />
					&nbsp;
					<input type="submit" name="submit-info" class="my-buttons" value="Submit Project" />
					</p>								
				</li>
			</ul>
		</div>
		</form>
		</div>
		</div>
		<?php 
	} 
}
add_shortcode('contract_checklist','billyB_contract_checklist')
?>