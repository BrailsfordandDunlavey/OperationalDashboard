<?php
function billyB_password_reset()
{
	global $wpdb;
	if(isset($_POST['save-info']))
	{
		$email = $_POST['email'];
		if(is_email($email) and email_exists($email))
		{
			$generic_pass = wp_generate_password( 12, false);
			$pass = md5($generic_pass);
			
			$update_query = "update ".$wpdb->prefix."users set user_pass='$pass' where user_email='$email'";
			$wpdb->query($update_query);
			
			$message = 'Your password has been reset to:  '.$generic_pass.'.
			If you did not make this request, please contact Bill Bannister immediately.
			
			Thank you.';
			
			add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
			wp_mail($email,'Password Reset',$message);
		
			echo '<ul class="other-dets_m">
						<li>A new password has been emailed to you.  If you don\'t receive it in the next few minutes, please contact Bill Bannister.</li>
					</ul>
				</div>
			</div>
			</div></div></div>
			</div></div>';
			get_footer();
			exit;
		}
		if(!is_email($email))
		{
			echo '<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li>Sorry, the address entered is not an email.  Please re-enter and try again.</li>
					</ul>
				</div>
			</div>
			</div>';
		}
		if(!email_exists($email))
		{
			echo '<div id="content">
			<div class="my_box3">
				<div class="padd10">
					<ul class="other-dets_m">
						<li>Sorry, we don\'t show that email on our records.  Please try another email.  If you believe this to be your email, please reach out to Bill Bannister.</li>
					</ul>
				</div>
			</div>
			</div>';
		}
	}
	?>   
	<form method="post"  enctype="multipart/form-data">
		<ul class="other-dets_m">
			<li>To reset your password, enter your email address below</li>
			
			<li><input type="text" class="do_input_new full_wdth_me" name="email" /></li>
			
			<li>&nbsp;</li>
			<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Submit"; ?>" /></p></li>
		</ul>
	</form>			
<?php }
add_shortcode('password_reset','billyB_password_reset')
?>