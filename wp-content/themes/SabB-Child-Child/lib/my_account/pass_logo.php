<?php
function BillyB_my_account_pass_mgmt()
{
	global $current_user,$wpdb;
	get_currentuserinfo();
	$uid = $current_user->ID;
?>
    	<div id="content" class="account-main-area">
           <?php
			if(isset($_POST['save-info']))
			{
				if(isset($_POST['password']))
				{
					$p1 = $_POST['password'];
					$p2 = $_POST['reppassword'];
					
					if(!empty($p1) && !empty($p2))
					{
						if($p1 == $p2)
						{
							$newp = md5($p1);//update algorithm from md5
							$wpdb->query($wpdb->prepare("update ".$wpdb->users." set user_pass=%s where ID=%d",$newp,$uid));
							$inc = 1;
						}
						else
						{
							echo '<div class="error">Password was not updated. Passwords do not match!</div>'; $xxp = 1;
						}
					}
					else
					{
						echo '<div class="error">Password was not updated. Passwords do not match!</div>';$xxp = 1;		
					}
				}
				if($xxp != 1)
				{
					if($inc == 1)
					{
						echo '<br/>Your password was changed. Redirecting to login page...';
						echo '<meta http-equiv="refresh" content="2; url='.get_bloginfo('url').'/wp-login.php">';
					}
					echo '</div>';
				}
			}
			$user = get_userdata($uid);
			?>
    <script type="text/javascript">
	function delete_this2(id){
		 jQuery.ajax({
			method: 'get',
			url : '<?php echo get_bloginfo('siteurl');?>/index.php/?_ad_delete_pid='+id,
			dataType : 'text',
			success: function (text){jQuery('#image_ss'+id).remove();  }
		 });
	}
	</script>     
	<form method="post"  enctype="multipart/form-data">
		<div class="my_box3">
			<div class="box_content">
				<ul class="post-new3">
				<li><h2>Username:</h2>
					<p><input type="text" value="<?php echo $user->user_login; ?>" readonly class="do_input_new full_wdth_me" /></p>
				</li>
				<li><h2>New Password:</h2>
					<p><input type="password" value="" class="do_input_new full_wdth_me" name="password" size="35" /></p>
				</li>
				<li><h2>Repeat Password:</h2>
					<p><input type="password" value="" class="do_input_new full_wdth_me" name="reppassword" size="35"  /></p>
				</li>
				<li><h2>&nbsp;</h2>
					<p><input type="submit" name="save-info" class="my-buttons" value="Save" /></p>
				</li>
				</ul> 
			</div>
		</div>        
	</form>
	</div>
<?php
}
add_shortcode('profile_mgmt','BillyB_my_account_pass_mgmt')
?>