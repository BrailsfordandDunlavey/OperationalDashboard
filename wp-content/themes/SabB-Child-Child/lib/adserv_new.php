<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	$all_teams = $wpdb->get_results("select distinct team,sphere from ".$wpdb->prefix."useradd where team!='' and sphere!='functional' order by sphere,team");
	
	$checklist = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d",$checklist));
	
	$gp_id = $details[0]->gp_id;
	$gp_project_number = $details[0]->gp_project_number;
	$client_id = $details[0]->client_id;
	$prime_id = $details[0]->prime_id;
	$project_name = $details[0]->project_name;
	$abb_name = $details[0]->abbreviated_name;
	$sphere = $details[0]->sphere;
	$project_group = $details[0]->project_group;
	$project_manager = $details[0]->project_manager;	
	$fee_type = $details[0]->fee_type;
	$initiation_document = $details[0]->initiation_document;
	$document_number = $details[0]->document_number;
	$estimated_start = $details[0]->estimated_start;
	$project_type = $details[0]->project_type;
	$fee_amount = $details[0]->fee_amount;
	$expense_amount = $details[0]->expense_amount;
	$expense_type = $details[0]->expense_type;
	$market = $details[0]->market;
	$submarket = $details[0]->submarket;
	$confidential = $details[0]->confidential;
	$venues = $details[0]->venues;
	$contact = $details[0]->contact;
	$address = $details[0]->address;
	$city = $details[0]->city;
	$state_id = $details[0]->state;
	$zip = $details[0]->zip;
	$email = $details[0]->email;
	$phone = $details[0]->phone;
	$delivery_type = $details[0]->delivery_type;
	$notes = $details[0]->notes;
	$status = $details[0]->status;
	$accounting_notes = $details[0]->accounting_notes;
	
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
	$resultsteam = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."project_user where project_id=%s",$checklist));
	foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
	
	if($status >0 and $team != "Finance")
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
	}
	if(isset($_POST['save-info']) or isset($_POST['submit-info']) or isset($_POST['approve-info']))
	{
		$gp_id = trim($_POST['gp_id']);
		$gp_project_number = trim($_POST['gp_project_number']);
		$client_id = trim($_POST['client_id']);
		$prime_id = trim($_POST['prime_id']);
		$project_name = trim($_POST['project_name']);
		$sphere = trim($_POST['sphere']);
		$project_group = $_POST['project_group'];
		$project_manager = trim($_POST['project_manager']);
		$project_team = $_POST['project_team'];							
		$fee_type = trim($_POST['fee_type']);
		$initiation_document = trim($_POST['initiation_document']);
		$document_number = $_POST['document_number'];
		$estimated_start = strtotime($_POST['estimated_start']);
		$project_type = trim($_POST['project_type']);
		$fee_amount = trim($_POST['fee_amount']);
		$sub_fee_amount = trim($_POST['sub_fee']);
		$expense_amount = trim($_POST['expense_amount']);
		$expense_type = trim($_POST['expense_type']);
		$market = trim($_POST['project_cat_cat']);
		$submarket = trim($_POST['subcat']);
		$confidential = trim($_POST['confidential']);
		$venues = trim($_POST['venues']);
		$perdiem = $_POST['perdiem'];
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
		if(isset($_POST['submit-info'])){$status = 1;}
		elseif(isset($_POST['approve-info'])){$status = 2;}
		else{$status = 0;}
				
		$querya = $wpdb->prepare("insert into ".$wpdb->prefix."projects (project_author,gp_project_number,gp_id,client_id,prime_id,project_name,sphere,project_group,project_manager,project_parent,
			fee_type,initiation_document,current_document,document_number,estimated_start,project_type,fee_amount,sub_fee_amount,expense_amount,expense_type,market,submarket,confidential,venues,
			markup,retainer,retention,contact,address,city,state,zip,email,phone,delivery_type,notes,accounting_notes,status,perdiem)
			values(%d,%s,%s,%d,%d,%s,%s,%s,%d,%d,%s,%s,%s,%s,%d,%s,%f,%f,%f,%s,%d,%d,%s,%s,%s,%f,%f,%s,%s,%s,%d,%s,%s,%s,%s,%s,%s,%d,%s)",
			$uid,$gp_project_number,$gp_id,$client_id,$prime_id,$project_name,$sphere,$project_group,$project_manager,$checklist,
			$fee_type,$initiation_document,$initiation_document,$document_number,$estimated_start,$project_type,$fee_amount,$sub_fee_amount,
			$expense_amount,$expense_type,$market,$submarket,$confidential,$venues,$markup,$retainer,$retention,$contact,
			$address,$city,$state,$zip,$email,$phone,$delivery_type,$notes,$accounting_notes,$status,$perdiem);	
		$wpdb->query($querya);
		
		$project_id = $wpdb->insert_id;
		
		if(!empty($project_manager) and $project_manager != 0)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id) 
				values (%d,%d)",$project_id,$project_manager));
		}
				
		if(!empty($project_team))
		{
			foreach ($project_team as $user_id)
			{
				if($user_id != $project_manager)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id) 
						values (%d,%d)",$project_id,$user_id));
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
					if($error == 1){echo "File was not uploaded because it was too large.<br/><br/>"; }
					if($error == 2){echo "File was not uploaded because it was too large.<br/><br/>"; }
					if($error == 3){echo "File was only partially uploaded.  Please contact Bill Bannister.<br/><br/>"; }
					if($error == 4){echo "No attachement.<br/><br/>"; }
					if($error > 4 or $error < 1){echo "File not uploaded.  Please contact Bill Bannister.<br/><br/>";}
				}
			}
		}
		$link = get_bloginfo('siteurl').'/?p_action=edit_checklist&ID='.$project_id;
		
		if(isset($_POST['submit-info']))
		{
			$to = array('Mmitchell@programmanagers.com','bbannister@facilityplanners.com','npereira@programmanagers.com');
			
			wp_mail($to,"New Adserv Checklist",'A new Adserv checklist has been submitted for your reivew.
			  You can review the checklist here: '.$link);
			$message =  "Thank you.  The contract checklist has been submitted for processing.<br/><br/>";
		}
		elseif(isset($_POST['approve-info'])){$message = "Thank you.  The contract checklist has been submitted for processing.<br/><br/>";}
		else{$message = "The checklist has been saved for future use.<br/>You will still need to submit the checklist before it can be processed.<br/><br/>";}
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		echo $message;
		?>
		<a href="<?php bloginfo('siteurl');?>/contract-checklist/"><?php echo "Enter a new Contract Checklist";?></a><br/><br/>
								
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div></div></div>

		<?php 	
		get_footer();
	}
	else
	{ ?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
			<div id="content"><h3><?php echo "Ad Serv to:  ".$client_name." - ".(empty($abb_name) ? $project_name : $abb_name);?></h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<?php 
					if($team=="Finance")
					{
						echo '<li><h3>Project Number:</h3><p><input type="text" class="do_input_new full_wdth_me" name="gp_project_number" 
								placeholder="Enter full Project Number here" /></p></li>
							<li><h3>Project ID:</h3><p><input type="text" class="do_input_new full_wdth_me" name="gp_id" 
								placeholder="Enter only the GP ID" /></p></li>';
					}
					?>
					<input hidden="hidden" type="text" name="client_id" value="<?php echo $client_id;?>" />
					<li><h3>Client Name:</h3>
						<p><input type="text" class="do_input_new full_wdth_me" name="client_name" value="<?php echo $client_name;?>"/></p></li>
					<input hidden="hidden" type="text" name="prime_id" value="<?php echo $prime_id;?>" />
					<li><h3>Prime Name (if sub):</h3>
						<p><input type="text" class="do_input_new full_wdth_me" name="prime_name" value="<?php echo $prime_name;?>"/></p></li>
					<li><h3>Project Name:</h3><p><input type="text" name="project_name" class="do_input_new full_wdth_me" /></p></li>
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
					<li><h3>Project Manager:</h3>
						<?php
						$userquery = "select ID,display_name from ".$wpdb->prefix."users 
							where display_name not in ('admin','bbannister','test') order by display_name";
						$users = $wpdb->get_results($userquery);
						?>
						<p>
						<?php
						echo '<select name="project_manager" class="do_input_new">';
						echo '<option>Select Project Manager</option>';
						
						foreach($users as $u)
						{
							echo '<option '.($project_manager == $u->ID ? "selected='selected'" : "" ).' value="'.$u->ID.'">'.$u->display_name.'</option>';
						}
						echo '</select>';
						?>
						</p>
					</li>							
					<li><h3>Team Members:<br/>Hold ctrl to select multiple</h3>
						<p><?php
						echo '<select name="project_team[]" size="10" multiple="multiple">';
														
						foreach($users as $u)
						{
							echo '<option '.(in_array($u->ID,$project_team) ? "selected='selected'" : " " ).' value="'.$u->ID.'">'.$u->display_name.'</option>';
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
						</p>
						</select>
					</li>
					<li><h3>Initiation Document:</h3>
						<p><select class ="do_input_new" name="initiation_document">
						<option value="">Select Document</option>
						<option>Contract</option>
						<option>Purchase Order</option>
						<option>Letter of Intent</option>
						<option>Executive Override</option>								
						</select>
						</p>
					</li>
					<li><h3>&nbsp;</h3><p><input class="my-buttons" type="file" name="fileToUpload[]" multiple="multiple" id="fileToUpload" /></p></li>
					<li><h3>Document Number (if any):</h3>
						<p><input type="text" name="document_number" class="do_input_new full_wdth_me"/></p>
					</li>							
					<li><h3>Estimated Start:</h3>
						<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
						<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
						<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
						<p><input type="text" id="start" name="estimated_start" class="full_wdth_me do_input_new" /></p>
					 </li>
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
					<li>
						<h3>Project Type:</h3>
						<p><select class ="do_input_new" name="project_type">
						<option value="">Select Project Type</option>
						<option>Project Definition</option>
						<option>MAS Planning</option>
						<option>P3 Advisory</option>
						<option>P3 Owner's Rep</option>
						<option>Full Service PM</option>
						</select>
						</p>
					</li>
					<li><h3>Fee Amount:</h3><p><input type="text" name="fee_amount" class="do_input_new full_wdth_me" /></p></li>
					<li><h3>Sub Fee Amount:</h3><p><input type="text" name="sub_fee" class="do_input_new full_wdth_me" /></p></li>					
					<li><h3>Expense Amount:</h3><p><input type="text" name="expense_amount" class="do_input_new full_wdth_me" /></p></li>
					<li><h3>Expense Type:</h3>
						<p><select class ="do_input_new" name="expense_type">
						<option>Select Expense Type</option>
						<option>Reimbursable</option>
						<option>No-Bill</option>
						<option>Unlimited</option>
						</select>
						</p>
					</li>
					<li><h3>Market:</h3>
						<script>
						function display_subcat(vals){
							jQuery.post("<?php bloginfo('siteurl'); ?>/?get_subcats_for_me=1", {queryString: ""+vals+""}, function(data){
								if(data.length >0){
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

						foreach($terms as $t)
						{
							echo '<option '.($market == $t->term_id ? "selected='selected'" : "" ).' value="'.$t->term_id.'">'.$t->name.'</option>';
						}
						echo '</select>';
															
						echo '<br/><span id="sub_market">';
						if(!empty($cat[1]->term_id) or !empty($submarket))
						{
							$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$cat[0]->term_id;
							$sub_terms2 = get_terms( 'project_cat', $args2 );	
							
							echo '<select class="do_input_new" name="subcat">';
							echo '<option value="">Select Subcategory</option>';
									
							foreach($sub_terms2 as $s)
							{
								echo '<option '.($submarket == $s->term_id ? "selected='selected'" : "" ).' value="'.$s->term_id.'">'.$s->name.'</option>';
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
					<li><h3>Per Diem Notes</h3>
						<p><textarea rows="6" cols="60" class="full_wdth_me do_input_new description_edit" 
						placeholder="Reference each per diem limitation" name="perdiem"></textarea></p>
					</li>
					<li><h3>10% Markup:</h3><p><input type="checkbox" name="markup"></p></li>
					<li><h3>Retainer:</h3><p>$<input type="number" step=".01" class="do_input_new" name="retainer"></p></li>
					<li><h3>Retention:</h3><p><input type="number" step=".01" min="0" max="100" class="do_input_new" name="retention">%</p></li>
				</ul>
				</div>
				</div>
			</div>
			<div id="content"><h3>Invoicing Details</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li><h3>Contact:</h3>
						<p><input type="text" name="contact" class="do_input_new full_wdth_me" value="<?php echo $contact;?>"/></p>
					</li>
					<li><h3>Address:</h3>
						<p><input type="text" name="address" class="do_input_new full_wdth_me" value="<?php echo $address;?>"/></p>
					</li>
					<li><h3>City:</h3>
						<p><input type="text" name="city" class="do_input_new full_wdth_me" value="<?php echo $city;?>"/></p>
					</li>
					<li><h3>State:</h3>
						<p><select name="state" class="do_input_new">
						<?php
						$states_result = $wpdb->get_results("select * from ".$wpdb->prefix."states order by state_abbreviation");
						echo '<option value="">Select State</option>';
						foreach($states_result as $s)
						{
							echo '<option value="'.$s->state_id.'" '.($s->state_id == $state_id ? "selected='selected'" : "" ).'>'.$s->state_abbreviation.'</option>';
						}
						?>
						</select>
						</p>
					</li>
					<li><h3>Zip:</h3>
						<p><input type="text" name="zip" class="do_input_new full_wdth_me" value="<?php echo $zip;?>" /></p>
					</li>
					<li><h3>Email:</h3>
						<p><input type="text" name="email" class="do_input_new full_wdth_me" value="<?php echo $email;?>" /></p>
					</li>
					<li><h3>Phone:</h3>
						<p><input id="phone" type="text" name="phone" class="do_input_new full_wdth_me" value="<?php echo $phone;?>"/></p>
					</li>			
					<li><h3>Delivery Type:</h3>
						<p><select class ="do_input_new" name="delivery_type">
						<option value="" >Select Delivery</option>
						<option <?php if($delivery_type=="Email"){echo "selected=selected";}?> >Email</option>
						<option <?php if($delivery_type=="Hard Copy"){echo "selected=selected";}?> >Hard Copy</option>
						<option <?php if($delivery_type=="Both"){echo "selected=selected";}?> >Both</option>
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
							placeholder="Make any notes about this project that other staff should know"  name="notes" ></textarea></p>
					</li>
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
						<input type="submit" name="approve-info" class="my-buttons" value="Approve Project" />
						</p>
						<?php
					}
					else
					{
						?>
						<p><input type="submit" name="save-info" class="my-buttons" value="Save for Later" />
						&nbsp;
						<input type="submit" name="submit-info" class="my-buttons" value="Submit Project" />
						</p>
						<?php 
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