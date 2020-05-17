<?php



?>

 


	<div id="footer">
	<div id="colophon" class="wrapper">	
	
		<?php
                get_sidebar( 'footer' );
        ?>
        
        
            <div id="site-info">
                <div class="padd10">
                

                        <div id="site-info-left">					
                            <h3><?php echo stripslashes(get_option('ProjectTheme_left_side_footer')); ?></h3>					
                        </div>
                        
                        <div id="site-info-right">
                            <?php echo stripslashes(get_option('ProjectTheme_right_side_footer')); ?>
                        </div>

                
                </div>
            </div>
        
        
        </div>
    </div>

</div>


<?php

	$ProjectTheme_enable_google_analytics = get_option('ProjectTheme_enable_google_analytics');
	if($ProjectTheme_enable_google_analytics == "yes"):		
		echo stripslashes(get_option('ProjectTheme_analytics_code'));	
	endif;
	
	//----------------
	
	$ProjectTheme_enable_other_tracking = get_option('ProjectTheme_enable_other_tracking');
	if($ProjectTheme_enable_other_tracking == "yes"):		
		echo stripslashes(get_option('ProjectTheme_other_tracking_code'));	
	endif;


?>


	<?php 	
            wp_footer();
    ?>
</body>
</html>