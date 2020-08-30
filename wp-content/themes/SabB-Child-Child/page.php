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


	get_header();


?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="page_heading_me">
	<div class="page_heading_me_inner">
    <div class="main-pg-title">
    	<div class="mm_inn"><?php
						
						  the_title();  
						
						?>
                     </div>
                    
        
<?php 

		if(function_exists('bcn_display'))
		{
		    echo '<div class="my_box3_breadcrumb breadcrumb-wrap">';	
		    bcn_display();
			echo '</div>';
		}

?>	</div>


		<?php projectTheme_get_the_search_box() ?>            
                    
    </div>
</div>


<?php projecttheme_search_box_thing() ?>

<!-- ########## -->

<div id="main_wrapper">
		<div id="main" class="wrapper"><div class="padd10">

<?php


	$ProjectTheme_adv_code_single_page_above_content = stripslashes(get_option('ProjectTheme_adv_code_single_page_above_content'));
		if(!empty($ProjectTheme_adv_code_single_page_above_content)):
		
			echo '<div class="full_width_a_div">';
			echo $ProjectTheme_adv_code_single_page_above_content;
			echo '</div>';
		
		endif;
?>
 

 

<div id="content">	
			<div class="my_box3">
            	<div class="padd10">
 
                <div class="box_content post-content"> 


<?php the_content(); ?>			


    </div>
			</div>
			</div>
            </div>
        


<div id="right-sidebar">
    <ul class="xoxo">
        <?php dynamic_sidebar( 'other-page-area' ); ?>
    </ul>
</div>



</div></div></div>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>