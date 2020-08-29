<?php

 
 	session_start();
	global $current_user, $wp_query;
	$pid 	=  $wp_query->query_vars['pid'];
	
	function ProjectTheme_filter_ttl($title){return __("Delete Project",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'ProjectTheme_filter_ttl', 10, 3 );	
	
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }   
	   
	
	get_currentuserinfo;   

	$post = get_post($pid);

	$uid 	= $current_user->ID;
	$title 	= $post->post_title;
	$cid 	= $current_user->ID;
	
	$winner = get_post_meta($pid, 'winner', true);
	
	if(!empty($winner)) { echo 'Project has a winner, cant be deleted. Sorry!'; exit; }
	if($uid != $post->post_author) { echo 'Not your post. Sorry!'; exit; }

//-------------------------------------

	get_header();
?>
             
                <div class="page_heading_me">
                        <div class="page_heading_me_inner">
                            <div class="mm_inn"><?php printf(__("Delete Project - %s", "ProjectTheme"), $post->post_title); ?>  </div>
                  	            
                                        
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
				
				if(isset($_POST['are_you_sure']))
				{
					wp_delete_post($pid);
					echo sprintf(__("The project has been deleted. <a href='%s'>Return to your account</a>.",'ProjectTheme'), get_permalink(get_option('ProjectTheme_my_account_page_id')));
				
				}
				else
				{
				?>
                
                    <form method="post" enctype="application/x-www-form-urlencoded">
                    <?php _e("Are you sure you want to delete this project?",'ProjectTheme'); ?><br/><br/>
                    <input class="submit_bottom2" type="submit" name="are_you_sure" value="<?php _e("Confirm Deletion",'ProjectTheme'); ?>"  />
                    </form>
                  
                 <?php } ?>              
                
                
                </div>
                </div>
                </div>
                </div>
                
	<?php ProjectTheme_get_users_links(); ?>


</div></div></div>

<?php get_footer(); ?>