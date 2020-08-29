<?php
/***************************************************************************
*
*	ProjectTheme - copyright (c) - sitemile.com
*	The only project theme for wordpress on the world wide web.
*
*	Coder: Andrei Dragos Saioc
*	Email: sitemile[at]sitemile.com | andreisaioc[at]gmail.com
*	More info about the theme here: http://sitemile.com/products/wordpress-project-freelancer-theme/
*	since v1.2.5.3
*
***************************************************************************/
 		
		if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
		
		global $wpdb, $current_user;
		get_currentuserinfo();
		 
		
 		$id = $wp_query->query_vars['rid'];
		$s = "select * from ".$wpdb->prefix."project_ratings where id='$id'";
		$result = $wpdb->get_results($s);
		
		$row = $result[0];
		$pid = $row->pid;
		$user = get_userdata($row->touser);
		$post_pr = get_post($row->pid);
		
 		$my_uid = $row->touser;
 
 		//-------------------------
 
 		$fromuser = $row->fromuser;
 		if($current_user->ID != $fromuser) { wp_redirect(get_bloginfo('siteurl')); exit; }
 
 		//-------------------------
 
 		get_header();
 ?> 
 
 
                 <div class="page_heading_me">
                        <div class="page_heading_me_inner">
                            <div class="mm_inn"> <?php printf(__("Review User %s for project %s",'ProjectTheme'), $user->user_login, $post_pr->post_title ) ; ?> </div>
                  	            
                                        
                        </div>
                    
                    </div> 
<!-- ########## -->

<div id="main_wrapper">
		<div id="main" class="wrapper"><div class="padd10">
 
 <div id="content" class="account-main-area">
        	
            <div class="my_box3">
            	<div class="padd10">
            
            	 
                <div class="box_content"> 
            	
                <?php
			$nok = 1;
			
			if(isset($_POST['rateme']))
			{
			
				$rating = $_POST['rating'];
				$comment = nl2br(strip_tags($_POST['commenta']));
				
				if(empty($comment)):
					
					$nok = 1;
					
					echo '<div class="error">';
					echo __('Please leave a comment with your review.','ProjectTheme');
					echo '</div>';
					
				else:
					
					$tm = current_time('timestamp',0);
						
					$s = "update ".$wpdb->prefix."project_ratings set grade='$rating', datemade='$tm', comment='$comment', awarded='1' where id='$id'";
					$wpdb->query($s);					
						
					$link = get_permalink(get_option('ProjectTheme_my_account_page_id'));	
					printf(__("Your rating has been posted. <a href='%s'>Return to your account area</a>","ProjectTheme"),$link);
					
					$nok = 0;
					
					//---------------------------
					
					$cool_user_rating = get_user_meta($my_uid, 'cool_user_rating', true);
					if(empty($cool_user_rating)) update_user_meta($my_uid, 'cool_user_rating', 0);
					
					//---------------------------
					
					$cool_user_rating = get_user_meta($my_uid, 'cool_user_rating', true);
					
					global $wpdb;
					$s = "select grade from ".$wpdb->prefix."project_ratings where touser='$my_uid' AND awarded='1'";
					$r = $wpdb->get_results($s);
					$i = 0; $s = 0;
						
					if(count($r) > 0)
					{
						foreach($r as $row) // = mysql_fetch_object($r))
						{
							$i++;
							$s = $s + $row->grade;
								
						}
	
						$rating2 = round(($s/$i)/2, 2);
						update_user_meta($my_uid, 'cool_user_rating', $rating2);
					
					}
					
					ProjectTheme_send_email_on_rated_user($pid, $my_uid);
					
					//---------------------------
					
				endif;
			}
			
			if($nok == 1)
			{
		
		?>
        <form method="post">	
             	   <ul class="post-new3">
            <li>
        	<h2><?php echo __('Your Rating','ProjectTheme'); ?>:</h2>
        	<p><select class="do_input" name="rating"><?php for($i=5;$i>0;$i--) echo '<option value="'.($i*2).'">'.$i.'</option>'; ?></select></p>
        </li>
        
        <li>
        	<h2><?php echo __('Your Comment','ProjectTheme'); ?>:</h2>
        	<p><textarea name="commenta" class="do_input" rows="5" cols="40" ></textarea></p>
        </li>
        
        
        
           <li>
        	<h2>&nbsp;</h2>
        	<p><input type="submit" name="rateme" value="<?php _e("Submit Rating","ProjectTheme"); ?>"  /></p>
        </li>
        
        
        
        </ul>
         </form> <?php } ?>      
                
                
                                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>

</div></div></div>

<?php get_footer(); ?>