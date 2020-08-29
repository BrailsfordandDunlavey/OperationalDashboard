<?php


function ProjectTheme_my_account_closed_projects_area_function()
{
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo();
		$uid = $current_user->ID;
		
?>
    	<div id="content" class="account-main-area">
        
        
                
                
				
                
                <?php
				global $wp_query;
				$query_vars = $wp_query->query_vars;
				$post_per_page = 5;				
				
				query_posts( "meta_key=closed&meta_value=1&post_type=project&order=DESC&orderby=id&author=".$uid.
				"&posts_per_page=".$post_per_page."&paged=".$query_vars['paged'] );

				if(have_posts()) :
				while ( have_posts() ) : the_post();
					projectTheme_get_post();
				endwhile;
				
				if(function_exists('wp_pagenavi')):
				wp_pagenavi(); endif;
				
				 else:
				echo '<div class="my_box3 border_bottom_0">
				<div class="box_content"> ';
				
				_e("There are no closed projects yet.",'ProjectTheme');
				
				echo '</div></div>';
				
				endif;
				
				wp_reset_query();
				
				?>
                
 
        
        
  		</div>      
<?php
		ProjectTheme_get_users_links();

}
	
?>