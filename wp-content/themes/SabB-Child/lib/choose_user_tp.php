<?php



if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
//-----------


	//----------

	global $wpdb,$wp_rewrite,$wp_query;

//---------------------------------

	global $current_user;
	get_currentuserinfo();
	$uid = $current_user->ID;

	if(isset($_POST['submit']))
	{
		
		update_user_meta($uid, 'user_tp', $_POST['user_tp']);		
		
		$user = new WP_User($uid);
		$user->set_role($_POST['user_tp']);
		
		wp_redirect(get_permalink(get_option('ProjectTheme_my_account_payments_id')));
		exit;	
	}
	
	
//==========================

get_header();

?>
<div class="clear10"></div>

	
			<div class="my_box3">
            	<div class="padd10">
            
            	<div class="box_title"><?php  printf(__("Choose User Type",'ProjectTheme')); ?></div>
                <div class="box_content">   
               <?php
			   
			   printf(__("Choose your user type",'ProjectTheme'));
			   
			   ?> 
                
                <div class="clear10"></div>
               
               <form method="post" > 
                
               <input type="radio" class="do_input" name="user_tp" id="user_tp" value="service_provider" checked="checked" /> <?php _e('Service Provider', 'ProjectTheme'); ?><br/>
               <input type="radio" class="do_input" name="user_tp" id="user_tp" value="business_owner" /> <?php _e('Service Contractor','ProjectTheme'); ?><br/>
                
                <br/>
                
                <input type="submit" value="Submit" name="submit" />
                
               </form>
    </div>
			</div>
			</div>
        
        
        <div class="clear100"></div>
            
            
<?php

get_footer();

?>                        