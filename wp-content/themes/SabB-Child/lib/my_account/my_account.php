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


function ProjectTheme_my_account_area_main_function()
{
	
				
				global $current_user, $wp_query;
				get_currentuserinfo();
				
				$uid = $current_user->ID;
	
	
?>
    	<div id="content" class="account-main-area">
        
        <?php
			
			if(isset($_GET['prj_not_approved']))
			{
				
				$psts = get_post($_GET['prj_not_approved']);		
		?>
        
        <div class="saved_thing">
        <?php echo sprintf(__('Your payment was received for the item: <b>%s</b> but your project needs to be approved. 
		You will be notified when your project will be approved and live on our website','ProjectTheme'), $psts->post_title ); ?>
        </div>
        
        	<?php
			}
			
				if(ProjectTheme_is_user_business($uid)):
			
			
			?>
                       
            	<div class="box_title"><?php _e("My Latest Requests", "ProjectTheme"); ?></div>
                 
            	
                 <?php
							
			 
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 5;				

					
				$closed = array(
						'key' => 'closed',
						'value' => "0",
						'compare' => '='
					);	
					
				$paid = array(
						'key' => 'paid',
						'value' => "1",
						'compare' => '='
					);		
				
				$args = array('post_type' => 'project', 'author' => $uid, 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => $post_per_page,
				'paged' => 1, 'meta_query' => array($paid, $closed), 'post_status' =>array('draft','publish') );
				
				query_posts($args);
				
			//	query_posts( "meta_key=closed&meta_value=0&post_status=publish,draft&post_type=project&order=DESC&orderby=date&author=".$uid.
			//	"&posts_per_page=".$post_per_page."&paged=".$query_vars['paged'] );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_acc();
				endwhile;
				
				//if(function_exists('wp_pagenavi')):
				//wp_pagenavi(); endif;
				
				 else:
				
				echo '<div class="my_box3"> <div class="box_content"> ';
				_e("There are no projects yet.",'ProjectTheme');
				echo '</div></div>';
				
				endif;
				
				wp_reset_query();

				
				?>

          
           
           
           
          
            
            	<div class="box_title"><?php _e("My Unpublished Requests",'ProjectTheme'); ?></div>
                
			
			
				<?php

				query_posts( "post_status=draft&meta_key=paid&meta_value=0&post_type=project&order=DESC&orderby=id&author=".$uid."&posts_per_page=3" );
				
				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_acc(array('unpaid'));
				endwhile; else:
				
				echo '<div class="my_box3"><div class="box_content">';
				_e("There are no projects yet.",'ProjectTheme');
				
				echo '</div></div>';
				
				endif;
				
				wp_reset_query();
				
				?>
			
 
			<div class="clear10"></div>
			
			
			
        
            
            	<div class="box_title"><?php _e("My Closed Projects",'ProjectTheme'); ?></div>
                    
			
			
				<?php

				query_posts( "meta_key=closed&meta_value=1&post_type=project&order=DESC&orderby=id&author=".$uid."&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_acc();
				endwhile; else:
				
				echo '<div class="my_box3"><div class="box_content">';
				_e("There are no projects yet.",'ProjectTheme');
				echo '</div></div>';
				
				endif;
				wp_reset_query();
				
				?>
 
		
        <?php endif; ?>
        
        <?php if(ProjectTheme_is_user_provider($uid)): ?>	
           
           
 
        
            
            	<div class="box_title"><?php _e("My Outstanding Requests",'ProjectTheme'); ?></div>
                 
			
			
				<?php
				
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 3;				
				
		
				$outstanding = array(
						'key' => 'outstanding',
						'value' => "1",
						'compare' => '='
					);
					
				$winner = array(
						'key' => 'winner',
						'value' => $uid,
						'compare' => '='
					);		
				
				$args = array('post_type' => 'project', 'order' => 'DESC', 'orderby' => 'date', 'posts_per_page' => $post_per_page,
				'paged' => 1, 'meta_query' => array($outstanding, $winner));
				
				
				query_posts( $args  );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_outstanding_project();
				endwhile; else:
				
				echo '<div class="my_box3"><div class="box_content">';
				_e("There are no projects yet.",'ProjectTheme');
				echo '</div></div>';
				
				
				endif;
				wp_reset_query();
				
				?>

	 
 
        
            
            	<div class="box_title"><?php _e("My Latest Proposals",'ProjectTheme'); ?></div>
                 
			
			
				<?php

				query_posts( "meta_key=bid&meta_value=".$uid."&post_type=project&order=DESC&orderby=id&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post_my_proposal();
				endwhile; else:
				
				echo '<div class="my_box3"><div class="box_content">';
				_e("There are no projects yet.",'ProjectTheme');
				echo '</div></div>';
				
				endif;
				wp_reset_query();
				
				?>

			 
            
     
        
            
            	<div class="box_title"><?php _e("My Accepted proposals",'ProjectTheme'); ?></div>
            
			
			
				<?php

				query_posts( "meta_key=winner&meta_value=".$uid."&post_type=project&order=DESC&orderby=id&posts_per_page=3" );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile; else:
				
				echo '<div class="my_box3"><div class="box_content">';
				_e("There are no projects yet.",'ProjectTheme');
				echo '</div></div>';
				
				endif;
				wp_reset_query();
				
				?>
 
            
        
        <?php endif; ?>   
                
        </div> <!-- end dif content -->
        
        <?php ProjectTheme_get_users_links(); ?>
        
    
	
<?php	
} 


?>