<?php



function ProjectTheme_display_all_prjs_page_disp()
{

	global $current_user;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	?>

   <div id="content">
			
             
            		<div class="box_content">
                    
                    <?php
					
					global $wp_query;
					$query_vars = $wp_query->query_vars;
					
					$posts_per_page = 8;
					$posts_per_page = apply_filters('ProjectTheme_all_projects_page_per_page', $posts_per_page);
					
					$args = array('post_type' => 'project', 'paged' => $query_vars['paged'] , 'posts_per_page' => $posts_per_page);
					$my_query = new WP_Query( $args );

					if($my_query->have_posts()):
					while ( $my_query->have_posts() ) : $my_query->the_post();
					
						ProjectTheme_get_post();
					
					endwhile;
					
						if(function_exists('wp_pagenavi')):
							wp_pagenavi( array( 'query' => $my_query ) );
						endif;
					
					else:
					_e('There are no projects posted.','ProjectTheme');
					
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