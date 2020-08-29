<?php
function billyB_new_client_contact()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
	if(isset($_POST['save-info']))
	{
		
		$now = time();
		$cp_last_name = $_POST['last_name'];
		$cp_first_name = $_POST['first_name'];
		$cp_title = $_POST['title'];
		$client_id = $_POST['client_id'];
		$client_name = $_POST['client_name'];
		if(empty($client_id) and !empty($client_name))
		{
			$name_check = $wpdb->get_results($wpdb->prepare("select client_id from ".$wpdb->prefix."clients where client_name=%s",$client_name));
			if(!empty($name_check)){$client_id=$name_check[0]->client_id;}
			else
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."clients (client_name) values(%s)",$client_name));
				$client_id = $wpdb->insert_id;
				$message = $user_name." has added a new client:  ".$client_name;
				wp_mail('bbannister@programmanagers.com',$message,"");
			}
		}
		elseif(empty($client_name)){$client_id=0;}
		$cp_phone = ($_POST['area_code']*10000000)+($_POST['three_digit']*10000)+$_POST['four_digit'];
		if(!is_numeric($cp_phone)){$cp_phone = 0;}
		$cp_email = $_POST['email'];
		if(!is_email($cp_email)){$cp_email="";$email_error="yes";}
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
		
		$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."client_person (cp_last_name,cp_first_name,cp_title,cp_market,cp_phone,cp_email,
			cp_responsible_party,last_edited,edited_by,client_id) 
			values(%s,%s,%s,%s,%d,%s,%d,%d,%d,%d)",$cp_last_name,$cp_first_name,$cp_title,$market,$cp_phone,$cp_email,$uid,$now,$uid,$client_id));
			
		$new_id = $wpdb->insert_id;
		
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		if($email_error!="yes")
		{
			echo "Thank you.  The employee record has been saved.<br/><br/>";
		}
		else
		{
			echo 'The email provided was not valid, so it was not recorded.  Please review the contact <a href="'.get_bloginfo('siteurl').'/?p_action=edit_client_contact&ID='.$new_id.'">here</a> and update the email.  The other information was saved.  Thank you.<br/><br/>';
		}
		echo '<a href="'.get_bloginfo('siteurl').'/new-client-contact/">Add a new contact</a><br/>';
		echo '<a href="'.get_bloginfo('siteurl').'/?p_action=edit_client_contact&ID='.$new_id.'">Edit this contact</a></br/><br/>';
		echo '<a href="'.get_bloginfo('siteurl').'/dashboard/">Return to your Dashboard</a>';
		
		?>
		</div></div></div>
		<?php 
		$_POST = array();
	}
	else
	{
		?>
		<form method="post" name="new_client_contact" enctype="multipart/form-data">
			<div id="content"><h3>New Client Contact</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li><h3>First Name:</h3><p><input class="do_input_new full_wdth_me" name="first_name"/></p></li>
					<li><h3>Last Name:</h3><p><input class="do_input_new full_wdth_me" name="last_name"/></p></li>
					<li><h3>Entity:</h3>
						<p><input type="hidden" name="client_id"  />
						<input class="do_input_new full_wdth_me" name="client_name" onkeyup="checkCustomer(this.value);" /></p>
					</li>
					<span id="client_buttons"></span>
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
					<li><h3>Title:</h3><p><input class="do_input_new full_wdth_me" name="title"/></p></li>
					<li><h3>Phone:</h3><p><input class="do_input_new" name="area_code" maxlength="3" type="number" style="width:80px;"
						onkeyup="areaCode();" onkeydown="javascript: return event.keyCode == 69 ? false : true"/>
						<input type="number" class="do_input_new" name="three_digit" maxlength="3" style="width:80px;"
							onkeyup="threeDigit();" onkeydown="javascript: return event.keyCode == 69 ? false : true"/>
						<input class="do_input_new" name="four_digit" maxlength="4" type="number" style="width:100px;"
							onkeyup="fourDigit();" onkeydown="javascript: return event.keyCode == 69 ? false : true"/></p></li>
					<li><h3>Email:</h3><p><input class="do_input_new full_wdth_me" name="email"/></p></li>
					<li><h3>Market:</h3>
						<p><input type="checkbox" name="higher_ed" />Higher Ed<br/>
							<input type="checkbox" name="k14" />K-14<br/>
							<input type="checkbox" name="municipal" />Municipal<br/>
							<input type="checkbox" name="venues" />Venues
						</p>
					</li>
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="Submit" /></p></li>
				</ul>
				</div>
				</div>
			</div>
		</form>
<?php } 

}
add_shortcode('new_client_contact','billyB_new_client_contact')
?>