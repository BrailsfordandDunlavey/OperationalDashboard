<?php
function billyB_new_vendor()
{
	if(!is_user_logged_in())
	{
		//$_REQUEST['redirect_to'] = "http://opdash.programmanagers.com/new-vendor/"); die();
		wp_redirect(get_bloginfo('siteurl').'/wp-login.php?redirect_to="new-vendor"'); exit;
	}

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
 	$employeegpidquery = $wpdb->prepare("select sphere,team from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$employeegpidresult = $wpdb->get_results($employeegpidquery);
	$team = $employeegpidresult[0]->team;
	if($team != 'Finance')
	{wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	$today = time();
	$cutoff = strtotime(date("Y-m-d", strtotime($today)) . " -60 days");
	
	if(isset($_POST['submit-info']))
	{
		$vendor_name = $_POST['vendor_name'];
		$vendor_gp_id = $_POST['gp_id'];
		$vendor_email = $_POST['vendor_email'];
		
		if(empty($_POST['vendor_id']))
		{
			$double_check = $wpdb->get_results($wpdb->prepare("select vendor_id from ".$wpdb->prefix."vendors where vendor_name=%s or vendor_gp_id=%s",$vendor_name,$vendor_gp_id));
			if(empty($double_check))
			{
				if(!is_email($vendor_email) and !empty($vendor_email)){$vendor_email = ""; $email_error = "yes";}
				
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."vendors (vendor_name,vendor_gp_id,vendor_email) values (%s,%s,%s)",
					$vendor_name,$vendor_gp_id,$vendor_email));
			}
			$message = "This vendor has been added to the OpDash.";
			if($email_error == "yes"){$message .= "<br/>There was an issue with the email address, so it was removed.  Please contact Bill Bannister to correct.";}
			echo '<div class="my_box3">
					<div class="padd10">				
					<ul class="other-dets_m">
						'.$message.'
					</ul>
					</div>
					</div>';
		}
		else
		{
			echo '<div class="my_box3">
					<div class="padd10">				
					<ul class="other-dets_m">
						This vendor name or GP ID already existed.  Please either enter a unique vendor and GP ID, or contact Bill Bannister if you have questions or issues.
					</ul>
					</div>
					</div>';
		}
	}
?>
			<script type="text/javascript">
				var form_being_submitted = false;
				function checkForm(){
					var myForm = document.forms.new_exp;
					var submitInfo = myForm.elements['submit-info'];
					
					if(form_being_submitted){
						alert('The form is being submitted, please wait a moment...');
						submitInfo.disabled = true;
						return false;
					}
					submitInfo.value = 'Saving form...';
					form_being_submitted = true;
					return true;
				}
			</script>			
		<form name="new_exp" method="post"  enctype="multipart/form-data" onsubmit="checkForm();">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">				
					<ul class="other-dets_m">
					<?php 							
						echo '<li><input type="hidden" name="vendor_id" />
							<h3>Vendor Name</h3><p><input type="text" name="vendor_name" class="do_input_new full_wdth_me" 
								 onkeyup="checkVendor(this.value);"/></p></li>';
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
						echo '<li><h3>GP ID</h3><p><input type="text" name="gp_id" class="do_input_new full_wdth_me" /></p></li>';
						echo '<li><h3>Vendor Email</h3><p><input type="text" class="do_input_new full_wdth_me" name="vendor_email" />';
						?>
						<li>&nbsp;</li>
						<li><input type="submit" name="submit-info" class="my-buttons-submit" value="Submit" /></li>
					</ul>
</div>
</div> </div></form>
<?php }
add_shortcode('new_vendor','billyB_new_vendor')
?>