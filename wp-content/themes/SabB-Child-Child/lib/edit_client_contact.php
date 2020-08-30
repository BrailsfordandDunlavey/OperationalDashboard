<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
get_header();
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;
$user_name = $current_user->display_name;

$cp_id = $_GET['ID'];	

if(isset($_POST['save-info']))
{
	$now = time();
	$cp_last_name = $_POST['last_name'];
	$cp_first_name = $_POST['first_name'];
	$cp_title = $_POST['title'];
	$client_id = $_POST['client_id'];
	$client_name = $_POST['client_name'];
	if(empty($client_id) and !empty($_POST['client_name']))
	{
		$name_check = $wpdb->get_results($wpdb->prepare("select client_id from ".$wpdb->prefix."clients where client_name=%s",$client_name));
		if(!empty($name_check)){$client_id=$name_check[0]->client_id;}
		else
		{
			$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values(%s)",$client_name));
			$client_id=$wpdb->insert_id;
			$message = $user_name." has added a new client:  ".$client_name;
			wp_mail('bbannister@programmanagers.com',$message,"");
		}
	}
	elseif(empty($client_name)){$client_id=0;}
	
	$cp_phone = ($_POST['area_code']*10000000)+($_POST['three_digit']*10000)+$_POST['four_digit']*1;
	if(!is_numeric($cp_phone)){$cp_phone = 0;}
	$cp_email = $_POST['email'];
	if(!is_email($cp_email)){$cp_email=""; $email_error="yes";}
	$cp_responsible_party = $_POST['responsible_party'];
	$higher_ed = $_POST['higher_ed'];
	$k14 = $_POST['k14'];
	$municipal = $_POST['municipal'];
	$venues = $_POST['venues'];
	
	$market_array = array();
	if($higher_ed=="on"){array_push($market_array,'Higher Ed');}
	if($k14=="on"){array_push($market_array,'K-14');}
	if($municipal=="on"){array_push($market_array,'Municipal');}
	if($venues=="on"){array_push($market_array,'Venues');}
	
	for($i=0;$i<count($market_array);$i++)
	{
		if($i==0)
		{
			$market = $market_array[$i];
		}
		else
		{
			$market .= ",".$market_array[$i];
		}
	}
	
	$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."client_person set cp_last_name=%s,cp_first_name=%s,client_id=%d,cp_title=%s,cp_phone=%d,
		cp_email=%s,cp_responsible_party=%d,last_edited=%d,edited_by=%d,cp_market=%s where client_person_id=%d",
		$cp_last_name,$cp_first_name,$client_id,$cp_title,$cp_phone,$cp_email,$cp_responsible_party,$now,$uid,$market,$cp_id));
	
}

$details = $wpdb->get_results($wpdb->prepare("select cp_last_name,cp_first_name,".$wpdb->prefix."client_person.client_id,client_name,cp_title,
	cp_phone,cp_email,cp_responsible_party,display_name,cp_market
	from ".$wpdb->prefix."client_person
	left join ".$wpdb->prefix."clients on ".$wpdb->prefix."client_person.client_id=".$wpdb->prefix."clients.client_id
	left join ".$wpdb->prefix."users on ".$wpdb->prefix."client_person.cp_responsible_party=".$wpdb->prefix."users.ID
	where ".$wpdb->prefix."client_person.client_person_id=%d",$cp_id));

$cp_last_name = $details[0]->cp_last_name;
$cp_first_name = $details[0]->cp_first_name;
$client_id = $details[0]->client_id;
$client_name = $details[0]->client_name;
$cp_title = $details[0]->cp_title;
$market_array = explode(",",$details[0]->cp_market);
$cp_phone = $details[0]->cp_phone*1;
$area_code = intval($cp_phone/10000000);
$three_digit = intval(($cp_phone - $area_code*10000000)/10000);
$four_digit = $cp_phone - ($area_code *10000000) - ($three_digit *10000);
$cp_email = $details[0]->cp_email;
$responsible_party = $details[0]->cp_responsible_party;

$editor_array = array(11,$details[0]->cp_responsible_party,132);


?>
	<div class="page_heading_me">
		<div class="page_heading_me_inner">
			<div class="main-pg-title">
				<div class="mm_inn"><?php echo "Edit Client Contact: ".$cp_first_name." ".$cp_last_name;?> 
				</div>
			</div>
		</div>
	</div>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="new_client_contact" enctype="multipart/form-data">
			<div id="content"><h2>Edit Contact Information</h2>
			<style>input[type=number]{width:95px;}</style>
				<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">
					<?php
					
					echo '<li><h3>First Name:</h3><p><input type="text" name="first_name" value="'.$cp_first_name.'" class="do_input_new full_wdth_me" required="required" /></p></li>';
					echo '<li><h3>Last Name:</h3><p><input type="text" name="last_name" value="'.$cp_last_name.'" class="do_input_new full_wdth_me" required /></p></li>';
					echo '<li><h3>Entity:</h3><p>
						<input type="hidden" name="client_id" value="'.$client_id.'" />
						<input type="text" name="client_name" class="do_input_new full_wdth_me" value="'.$client_name.'" onkeyup="checkCustomer(this.value);"/></p>
						</li>';
					echo '<span id="client_buttons"></span>';
					
					?>
					<script type="text/javascript">
					function checkCustomer(vals){
						var myForm = document.forms.new_client_contact;
						var span = document.getElementById('client_buttons');
						span.style.display = 'block';
						jQuery.post("<?php bloginfo('siteurl'); ?>/?check_customers=1", {search_term: ""+vals+""}, function(data){
							if(data.length >0) {
								jQuery('#client_buttons').html(data);
							}
						});
					}
					function setCustomer(id,name){
						var myForm = document.forms.new_client_contact;
						var clientId = myForm.elements['client_id'];
						var clientName = myForm.elements['client_name'];
						var span = document.getElementById('client_buttons');
						clientId.value = id;
						clientName.value = name;
						span.style.display = 'none';
						
					}
					function areaCode(){
						var myForm = document.forms.new_client_contact;
						var areaCode = myForm.elements['area_code'];
						var threeDigit = myForm.elements['three_digit'];
						if(areaCode.value.length==3){
							threeDigit.focus();
						}
					}
					function threeDigit(){
						var myForm = document.forms.new_client_contact;
						var fourDigit = myForm.elements['four_digit'];
						var threeDigit = myForm.elements['three_digit'];
						if(threeDigit.value.length==3){
							fourDigit.focus();
						}
					}
					function fourDigit(){
						var myForm = document.forms.new_client_contact;
						var fourDigit = myForm.elements['four_digit'];
						var email = myForm.elements['email'];
						if(fourDigit.value.length==4){
							email.focus();
						}
					}
					</script>
					<?php
					echo '<li><h3>Title:</h3><p><input type="text" name="title" value="'.$cp_title.'" class="do_input_new full_wdth_me" /></p></li>';
					?>
					<li><h3>Phone:</h3><p><input class="do_input_new" name="area_code" maxlength="3" type="number" style="width:80px;"
						onkeyup="areaCode();" onkeydown="javascript: return event.keyCode == 69 ? false : true" value="<?php echo $area_code;?>"/>
						<input type="number" class="do_input_new" name="three_digit" maxlength="3" style="width:80px;"
							onkeyup="threeDigit();" onkeydown="javascript: return event.keyCode == 69 ? false : true" value="<?php echo $three_digit;?>"/>
						<input class="do_input_new" name="four_digit" maxlength="4" type="number" style="width:100px;"
							onkeyup="fourDigit();" onkeydown="javascript: return event.keyCode == 69 ? false : true" value="<?php echo $four_digit;?>"/></p></li>
					<?php
					echo '<li><h3>Email:</h3><p><input type="text" name="email" value="'.htmlspecialchars($cp_email).'" class="do_input_new full_wdth_me" /></p></li>';
					echo '<li><h3>Market:</h3><p>
						<input type="checkbox" name="higher_ed" '.(in_array('Higher Ed',$market_array) ? 'checked="checked"' : '' ).' />Higher Ed<br/>
						<input type="checkbox" name="k14" '.(in_array('K-14',$market_array) ? 'checked="checked"' : '' ).' />K-14<br/>
						<input type="checkbox" name="municipal" '.(in_array('Municipal',$market_array) ? 'checked="checked"' : '' ).' />Municipal<br/>
						<input type="checkbox" name="venues" '.(in_array('Venues',$market_array) ? 'checked="checked"' : '').' />Venues
						</p></li>';
					echo '<li><h3>Responsible Party:</h3><p><select name="responsible_party" class="do_input_new">';
					$active_users = $wpdb->get_results($wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."users
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
						where ".$wpdb->prefix."useradd.status=1 or user_id=%d
						order by display_name",$responsible_party));
					foreach($active_users as $a)
					{
						echo '<option value="'.$a->user_id.'" '.($a->user_id==$responsible_party ? 'selected="selected"' : '').'>'.$a->display_name.'</option>';
					}
					echo '</select></p></li>';
					if(in_array($uid,$editor_array))
					{
						echo '<li>&nbsp;</li>';
						echo '<li><input type="submit" value="save" name="save-info" class="my-buttons"/></li>';
					}
					
					?>
					</ul>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
<?php get_footer();?>