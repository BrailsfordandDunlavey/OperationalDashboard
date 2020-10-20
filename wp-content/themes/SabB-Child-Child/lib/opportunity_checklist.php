<?php
function billyB_opportunity_checklist()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$user_sphere = $useradd[0]->sphere;
	
	$all_subcategories = $wpdb->get_results("select ".$wpdb->prefix."terms.term_id,name,parent from ".$wpdb->prefix."terms
			inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."terms.term_id=".$wpdb->prefix."term_taxonomy.term_id
			where taxonomy='project_cat' and parent!=0");
	$all_customers = $wpdb->get_results("select client_id,client_name,client_gp_id from ".$wpdb->prefix."clients");
	$all_teams = $wpdb->get_results("select distinct team,sphere from ".$wpdb->prefix."useradd where team!='' and sphere!='functional' order by sphere,team");
	
	if(isset($_POST['save-info']))
	{
		$client_id = trim($_POST['client_id']);
		$named_client = trim($_POST['named_client']);
		if(empty($client_id) and !empty($named_client))
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values (%s)",$named_client));
			$client_id = $wpdb->insert_id;		
		}
		$project_name = trim($_POST['project_name']);
		$abb_name = trim($_POST['abb_name']);
		$sphere = trim($_POST['sphere']);
		$project_group = $_POST['project_group'];
		$project_manager = trim($_POST['project_manager']);
		$project_team = $_POST['project_team'];							
		$fee_type = trim($_POST['fee_type']);
		$estimated_start = strtotime($_POST['estimated_start']);
		$project_type = trim($_POST['project_type']);
		$fee_amount = trim($_POST['fee_amount']);
		$market = trim($_POST['project_cat_cat']);
		$submarket = trim($_POST['subcat']);
		$confidential = trim($_POST['confidential']);
		$venues = trim($_POST['venues']);
		$notes = trim($_POST['notes']);
		$status = $_POST['opportunity_status'];if($status==0){$status=4;}
		$submit_date = time();
		
		$querya = $wpdb->prepare("insert into ".$wpdb->prefix."projects (project_author,client_id,project_name,abbreviated_name,sphere,project_group,project_manager,fee_type,
			estimated_start,project_type,fee_amount,market,submarket,confidential,venues,notes,status,submitted_date)
			values(%d,%d,%s,%s,%s,%s,%d,%s,%d,%s,%f,%d,%d,%s,%s,%s,%d,%d)",
			$uid,$client_id,$project_name,$abb_name,$sphere,$project_group,$project_manager,$fee_type,$estimated_start,$project_type,
			$fee_amount,$market,$submarket,$confidential,$venues,$notes,$status,$submit_date);	
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
		else
		{
			$message = "Thank you, the opportunity has been saved.";
		}
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10"><?php echo $message; ?>
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
		<div id="content"><h3>Opportunity Details</h3><br/>
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
							<h3>Opportunity Name:</h3>
							<p><input type="text" name="project_name" class="do_input_new full_wdth_me"/></p>
						</li>
						<li>
							<h3>Opportunity Abbreviated Name: <br/>(max 25 characters)</h3>
							<p><input type="text" name="abb_name" class="do_input_new full_wdth_me" maxlength="25"/></p>
						</li>
						<li><h3>Sphere:</h3>
							<p><select class ="do_input_new" name="sphere">
							<option value="">Select Sphere</option>
							<option <?php if($user_sphere=='Higher Ed'){echo 'selected="selected"';}?>>Higher Ed</option>
							<option <?php if($user_sphere=='Sphere KMV'){echo 'selected="selected"';}?>>Sphere KMV</option>
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
							
							foreach($users as $user)
							{
								echo '<option value="'.$user->ID.'" '.($user->ID==$uid ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
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
						<li><h3>Opportunity Status:</h3>
							<p><select class ="do_input_new" name="opportunity_status">
							<option value="">Select Status</option>
							<?php
							if($user_sphere != 'Sphere KMV'){$user_sphere = 'Higher Ed';}
							if($user_sphere=='Higher Ed'){$status_array = array('Farming','50%','95%');}
							else{$status_array = array('Farming','50/50','Likely');}
							for($i=4;$i<count($status_array)+4;$i++)
							{
								echo '<option value="'.$i.'">'.$status_array[$i-4].'</option>';
							}
							?>							
							</select></p>
						</li>	
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
						<li><h3>B&D Fee Amount:</h3><p>$<input type="number" step=".01" min="0" name="fee_amount" class="do_input_new"/></p></li>
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
					<p><input type="submit" name="save-info" class="my-buttons" value="Save" />
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
add_shortcode('opportunity_checklist','billyB_opportunity_checklist')
?>