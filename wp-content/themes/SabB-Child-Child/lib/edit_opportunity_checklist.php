<?php
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	function sitemile_filter_ttl($title){return "Edit Opportunity";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
	
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$user_sphere = $useradd[0]->sphere;
	
	$opportunity = $_GET['ID'];
	
	$all_markets = $wpdb->get_results("select ".$wpdb->prefix."terms.term_id,name,parent from ".$wpdb->prefix."terms
			inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."terms.term_id=".$wpdb->prefix."term_taxonomy.term_id
			where taxonomy='project_cat' and parent=0
			order by name");
	
	$all_subcategories = $wpdb->get_results("select ".$wpdb->prefix."terms.term_id,name,parent from ".$wpdb->prefix."terms
			inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."terms.term_id=".$wpdb->prefix."term_taxonomy.term_id
			where taxonomy='project_cat' and parent!=0
			order by name");
	$all_customers = $wpdb->get_results("select client_id,client_name,client_gp_id from ".$wpdb->prefix."clients");
	$all_teams = $wpdb->get_results("select distinct team,sphere from ".$wpdb->prefix."useradd where team!='' and sphere!='functional' order by sphere,team");
	
	if(isset($_POST['convert']))
	{
		$_SESSION['stay_on_page'] = 'remain';
		wp_redirect(get_bloginfo('siteurl')."/?p_action=edit_checklist&ID=".$opportunity); 
		exit;
	}
	
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
		$status = $_POST['opportunity_status'];if($status==0){$status = 4;}
		$submit_date = time();
		
		$querya = $wpdb->prepare("update ".$wpdb->prefix."projects set client_id=%d,project_name=%s,abbreviated_name=%s,sphere=%s,project_manager=%d,
			fee_type=%s,estimated_start=%d,project_type=%s,fee_amount=%f,market=%d,submarket=%d,confidential=%s,venues=%s,notes=%s,status=%d,submitted_date=%d,
			project_group=%s where ID=%d",
			$client_id,$project_name,$abb_name,$sphere,$project_manager,$fee_type,$estimated_start,$project_type,
			$fee_amount,$market,$submarket,$confidential,$venues,$notes,$status,$submit_date,$project_group,$opportunity);	
		$wpdb->query($querya);
		
		$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."project_user where project_id=%d",$opportunity));
		
		if(!empty($project_manager) and $project_manager != 0)
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values (%d,%d,'Team Lead')",
				$opportunity,$project_manager));
		}
		if(!empty($project_team))
		{
			foreach ($project_team as $key => $value)
			{
				if($value != $project_manager)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."project_user (project_id,user_id,user_role) values (%d,%d,'Work Team')",
						$opportunity,$value));
				}
			}
		}
		$message = "Thank you, the opportunity has been saved.";
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10"><?php echo $message."<br/><br/>"; ?>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>
		<?php 
		get_footer();
	}
	elseif(isset($_POST['close-info']))
	{
		$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set status=3, win_loss=0 where ID=%d",$opportunity));
		
		$message = "Thank you, the opportunity has been closed.";
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<div id="content">
		<div class="my_box3">
		<div class="padd10"><?php echo $message."<br/><br/>"; ?>
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>
		<?php 
		get_footer();
	}
	else
	{
		$details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d and status>3",$opportunity));
		$proposal_details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."proposal_mgmt where project_id=%d",$opportunity));
		if(empty($details))
		{
			?>
			<div id="main_wrapper">
			<div id="main" class="wrapper">
			<div id="content"><h3>Opportunity Details</h3><br/>
				<div class="my_box3">
					<div class="padd10">
					There is no project or opportunity with this ID.<br/><br/> 
					<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
					</div>
				</div>
			</div>
			</div>
			</div>
			<?php
			get_footer();
			exit;
		}
		$client_id = $details[0]->client_id;
		$project_group = $details[0]->project_group;
		$client_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."clients where client_id=%d",$client_id));
		$client_name = $client_results[0]->client_name;
		$team_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."project_user where project_id=%d",$opportunity));
		$team = array();
		foreach($team_results as $t)
		{
			array_push($team,$t->user_id);
		}
		
		?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="contract_checklist" enctype="multipart/form-data">
		<div id="content"><h3>Opportunity Details</h3><br/>
			<div class="my_box3">
				<div class="padd10">							
					<ul class="other-dets_m">
						<li><input type="submit" name="convert" value="Convert to a Project" class="my-buttons-submit" /></li>
						<li>&nbsp;</li>
						<li>
							<h3>Client Name:</h3>
							<p><input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
							<input type="text" name="named_client" class="do_input_new full_wdth_me" value="<?php echo $client_name;?>"
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
							<p><input type="text" name="project_name" class="do_input_new full_wdth_me" value="<?php echo $details[0]->project_name;?>" /></p>
						</li>
						<li>
							<h3>Opportunity Abbreviated Name: <br/>(max 25 characters)</h3>
							<p><input type="text" name="abb_name" class="do_input_new full_wdth_me" maxlength="25" value="<?php echo $details[0]->abbreviated_name;?>" /></p>
						</li>
						<li><h3>Sphere:</h3>
							<p><select class ="do_input_new" name="sphere">
							<option value="">Select Sphere</option>
							<option <?php if($details[0]->sphere=='Higher Ed'){echo 'selected="selected"';}?>>Higher Ed</option>
							<option <?php if($details[0]->sphere=='Sphere KMV'){echo 'selected="selected"';}?>>Sphere KMV</option>
							</select></p>
						</li>
						<li><h3>Group/Cluster:</h3>
							<p><select class="do_input_new" name="project_group">
							<?php
							foreach($all_teams as $at)
							{
								echo '<option value="'.$at->team.'" '.($project_group==$at->team ? "selected='selected'" : "").'>'.$at->sphere.':  '.$at->team.'</option>';
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
								echo '<option value="'.$user->ID.'" '.($user->ID==$details[0]->project_manager ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
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
								echo '<option '.(in_array($user->ID,$team) ? "selected='selected'" : "" ).' value="'.$user->ID.'">'.$user->display_name.'</option>';
							}
							echo '</select>';
							?>
							</p>
						</li>
						<li><h3>Fee Type:</h3>
							<p><select class ="do_input_new" name="fee_type">
							<option value="">Select Fee Type</option>
							<option <?php if($details[0]->fee_type=='Fixed Fee'){echo 'selected="selected"';}?>>Fixed Fee</option>
							<option <?php if($details[0]->fee_type=='T&M (as used)'){echo 'selected="selected"';}?>>T&M (as used)</option>
							<option <?php if($details[0]->fee_type=='T&M (to maximum)'){echo 'selected="selected"';}?>>T&M (to maximum)</option>
							<option <?php if($details[0]->fee_type=='Percent Complete'){echo 'selected="selected"';}?>>Percent Complete</option>
							</select>
						</li>
						<li><h3>Opportunity Status:</h3>
							<p><select class ="do_input_new" name="opportunity_status">
							<option value="">Select Status</option>
							<?php
							if($user_sphere != 'Sphere KMV'){$user_sphere = 'Higher Ed';}
							if($user_sphere=='Higher Ed'){$status_array = array('Farming','50%','95%');}
							else{$status_array = array('Projected Opportunity','Submitted Proposal');}
							for($i=4;$i<count($status_array)+4;$i++)
							{
								echo '<option value="'.$i.'" '.($details[0]->status==$i ? "selected='selected'" : "").'>'.$status_array[$i-4].'</option>';
							}
							?>							
							</select></p>
						</li>	
						<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
						<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
						<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>							
						<li><h3>Estimated Start:</h3><p><input type="text" id="start" name="estimated_start" class="do_input_new full_wdth_me"
							value="<?php echo date('m/d/Y',$details[0]->estimated_start);?>" /></p></li>
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
							<option <?php if($details[0]->project_type=='Project Definition'){echo 'selected="selected"';}?>>Project Definition</option>
							<option <?php if($details[0]->project_type=='MAS Planning'){echo 'selected="selected"';}?>>MAS Planning</option>
							<option <?php if($details[0]->project_type=='P3 Advisory'){echo 'selected="selected"';}?>>P3 Advisory</option>
							<option <?php if($details[0]->project_type=="P3 Owner's Rep"){echo 'selected="selected"';}?>>P3 Owner's Rep</option>
							<option <?php if($details[0]->project_type=='Full Service PM'){echo 'selected="selected"';}?>>Full Service PM</option>
							</select></p>
						</li>
						<li><h3>B&D Fee Amount:</h3><p>$<input type="number" step=".01" min="0" name="fee_amount" class="do_input_new"
							value="<?php echo $details[0]->fee_amount;?>"/></p></li>
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
							
							echo '<select class="do_input_new" name="project_cat_cat" onchange="display_subcategories(this.value)">';
							echo '<option value="">Select Market</option>';
							foreach($all_markets as $m)
							{
								echo '<option value="'.$m->term_id.'" '.($details[0]->market==$m->term_id ? "selected='selected'" : "" ).'>'.$m->name.'</option>';
							}
							echo '</select>';
							
							echo '<br/><span id="sub_cats">';
							if(empty($details[0]->market))
							{
								echo '<select class="do_input_new" name="subcat" style="display:none;"></select>';
							}
							else
							{
								echo '<select class="do_input_new" name="subcat">';
								echo '<option value="">Select Submarket</option>';
								foreach($all_subcategories as $s)
								{
									if($s->parent==$details[0]->market)
									{
										echo '<option value="'.$s->term_id.'" '.($s->term_id==$details[0]->submarket ? "selected='selected'" : "" ).'>'.$s->name.'</option>';
									}
								}
								echo '</select>';
							}
							echo '</span>';
							?>
							</p>
						</li>
						<li><h3>Confidential:</h3><p><input type="checkbox" name="confidential" <?php if($details[0]->confidential=="on"){echo "checked='checked'";}?>></p></li>
						<li><h3>Venues:</h3><p><input type="checkbox" name="venues" <?php if($details[0]->venues=="on"){echo "checked='checked'";}?>></p></li>
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
						placeholder="Make any notes about this project that other staff should know"  name="notes"><?php echo $details[0]->notes;?></textarea></p>
					</li>
					</ul>        
				</div>
			</div>
			<ul class="other-dets_m">
				<li>&nbsp;</li>
				<li>
					<p><input type="submit" name="save-info" class="my-buttons-submit" value="Save" />
					&nbsp;&nbsp;
					<input type="submit" name="close-info" class="my-buttons" value="Close Opportunity" />
					</p>								
				</li>
			</ul>
		</div>
		</form>
		</div>
		</div>
		<?php 
	} 
	get_footer();
?>