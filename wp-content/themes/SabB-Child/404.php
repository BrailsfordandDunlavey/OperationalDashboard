<?php
/********************************************************************
*
*	ProjectTheme for WordPress - sitemile.com
*	http://sitemile.com/p/project
*	Copyright (c) 2012 sitemile.com
*	Coder: Saioc Dragos Andrei
*	Email: andreisaioc@gmail.com
*
*********************************************************************/



	get_header();

?>
<div class="page_heading_me">
	<div class="page_heading_me_inner">
    <div class="main-pg-title">
    	<div class="mm_inn">
        <?php _e('Page not found','ProjectTheme') ?>
        
        </div>
	<?php 

		if(function_exists('bcn_display'))
		{
		    echo '<div class="my_box3_breadcrumb"><div class="padd10_a">';	
		    bcn_display();
			echo '</div></div>';
		}
		
		
		
?></div>


		<?php projectTheme_get_the_search_box() ?>            
                    
    </div>
</div>


<div id="main_wrapper">
		<div id="main" class="wrapper"><div class="padd10">


<div id="content">
    <div class="box_title"><?php _e('Page Not Found','ProjectTheme'); ?></div>
	<div class="padd10">
<?php _e('The requested page cannot be found. Maybe your project has not been approved yet.','ProjectTheme'); ?>

    </div>
    </div>


  <!-- ################### -->
    
    <div id="right-sidebar">    
    	<ul class="xoxo">
        	 <?php dynamic_sidebar( 'single-widget-area' ); ?>
        </ul>    
    </div>



</div>
</div>
</div>
<?php

	get_footer();

?>