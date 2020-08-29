<?php



function ProjectTheme_display_blog_page_disp()
{
	
		global $current_user, $wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$paged = $wp_query->query_vars['paged'];
	
	?>
    
        
   <div id="content">
			
            
            		 
            		<div class="box_content">
                    
                    <?php
					
					$args = array('post_type' => 'post', 'paged' => $paged);
					$my_query = new WP_Query( $args );

					if($my_query->have_posts()):
					while ( $my_query->have_posts() ) : $my_query->the_post();
					
						ProjectTheme_get_post_blog();
					
					endwhile;
					
						if(function_exists('wp_pagenavi')):
							wp_pagenavi( array( 'query' => $my_query ) );
						endif;
					
					else:
					_e('There are no posts.','ProjectTheme');
					
					endif;
					
					
					
					
					
					
					
					
					?>
                    
                    </div>
  </div>
    
    
      <!-- ################### -->
    
    <div id="right-sidebar">    
    	<ul class="xoxo">
        	 <?php dynamic_sidebar( 'other-page-area' ); ?>
        </ul>    
    </div>
    
    <?php
	
}

?>