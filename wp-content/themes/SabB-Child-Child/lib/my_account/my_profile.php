<?php
function ProjectTheme_my_account_my_profile()
{
	global $current_user, $wpdb, $wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if(isset($_POST['save-info']))
	{
		$phone_characters = array(")","(",".","/","-"," ");
		$office_phone = str_replace($phone_characters,"",$_POST['office_phone']);
		$cell_phone = str_replace($phone_characters,"",$_POST['cell_phone']);
		
		$user_address = trim($_POST['user_address']);
		update_user_meta($uid, 'user_address', $user_address);

		$user_city = trim($_POST['user_city']);
		update_user_meta($uid, 'user_city', $user_city);
		
		$user_location = trim($_POST['user_location']);
		update_user_meta($uid,'user_location',$user_location);		

		$user_zip = trim($_POST['user_zip']);
		update_user_meta($uid, 'user_zip', $user_zip);
		
		$wpdb>query($wpdb->prepare("update ".$wpdb->prefix."useradd set office_phone=%d,
			cell_phone=%d where user_id=%d",$office_phone,$cell_phone,$uid));
		
	}
	$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id =%d",$uid));
	$office = $useradd_results[0]->office_phone;
	if(strlen($office) == 10){$office_phone = "(".substr($office,0,3).") ".substr($office,3,3)."-".substr($office,6,4);}
	else{$office_phone="";}
	$cell = $useradd_results[0]->cell_phone;
	if(strlen($cell) == 10){$cell_phone = "(".substr($cell,0,3).") ".substr($cell,3,3)."-".substr($cell,6,4);}
	else{$cell_phone="";}
	
	?>
	<div id="content" class="account-main-area">	
	   <?php
			
			$user = get_userdata($uid);
			?>
    <script type="text/javascript">
	function delete_this2(id)
	{
		 jQuery.ajax({
						method: 'get',
						url : '<?php echo get_bloginfo('siteurl');?>/index.php/?_ad_delete_pid='+id,
						dataType : 'text',
						success: function (text) {   jQuery('#image_ss'+id).remove();  }
					 });
		  //alert("a");
	}
</script>     
	<form method="post"  enctype="multipart/form-data">
            <div class="my_box3">
                <div class="box_content">    
         <ul class="post-new3">
        <li>
        	<h2><?php echo __('Username','ProjectTheme'); ?>:</h2>
        	<p><input type="text" value="<?php echo $user->user_login; ?>" readonly class="do_input_new full_wdth_me" /></p>
        </li>
		<li>
        	<h2><?php echo __('Address','ProjectTheme'); ?>:</h2>
        	<p><input type="text" name="user_address" value="<?php echo get_user_meta($uid, 'user_address', true); ?>" class="do_input_new full_wdth_me" /></p>
        </li>        
        <li>
        	<h2><?php echo __('City','ProjectTheme'); ?>:</h2>
        	<p><input type="text" name="user_city" value="<?php echo get_user_meta($uid, 'user_city', true); ?>" class="do_input_new full_wdth_me" /></p>
        </li>
		 <li>
        	<h2><?php echo __('Location','ProjectTheme'); ?>:</h2>
        	<p><select class="do_input_new" name="user_location"><option value=""><?php echo "Select State";?></option>
            <?php	
			$stateresults = $wpdb->get_results("select state_id, state_name from ".$wpdb->prefix."states");
			$user_state = get_user_meta($uid,"user_location",true);
			foreach ($stateresults as $state)
			{
				$state_id = $state->state_id;
				echo '<option value="'.$state_id.'"'.($state_id == $user_state ? "selected='selected'" : " ").'>'.$state->state_name.'</option>';
			}
			?>
			</select>
            </p>
        </li>
        <li>
        	<h2><?php echo __('Zip','ProjectTheme'); ?>:</h2>
        	<p><input type="text" name="user_zip" value="<?php echo get_user_meta($uid, 'user_zip', true); ?>" class="do_input_new full_wdth_me" /></p>
        </li>
		<li>
			<h2><?php echo "Office Phone:";?></h2>
			<p><input type="text" name="office_phone" value="<?php echo $office_phone;?>" class="do_input_new full_wdth_me" /></p>
		</li>
		<li>
			<h2><?php echo "Cell Phone:";?></h2>
			<p><input type="text" name="cell_phone" value="<?php echo $cell_phone;?>" class="do_input_new full_wdth_me" /></p>
		</li>
        <li>
        <h2>&nbsp;</h2>
        <p><input type="submit" name="save-info" class="my-buttons" value="<?php _e("Save" ,'ProjectTheme'); ?>" /></p>
        </li> 
	   
	   </ul> 
           </div>
           </div>                                
	</form>
        </div>
<?php	
} 
add_shortcode('my_profile','ProjectTheme_my_account_my_profile')
?>