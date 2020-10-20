<?php


function ProjectTheme_all_categories_area_main_function()
{
	
	?>
    	
          	<div id="content" style="width:100%">
        	
            <div class="my_box3">
            	<div class="padd10"> 
                <div class="box_content"> 
            	
                <script>
				$(document).ready(function() {
 
 		$('.parent_taxe').click(function () {
			
			var rels = $(this).attr('rel');
			$("#" + rels).toggle();
			$("#img_" + rels).attr("src","<?php bloginfo('template_url'); ?>/images/posted1.png");
			
			return false;
		});
 
 
});
				
				</script>
                   <?php
		
		$opt = get_option('ProjectTheme_show_subcats_enbl');
		
		if($opt == 'no')
		$smk_closed = "smk_closed_disp_none";		 
		else $smk_closed = ''; 	   
				   
		//------------------------		   
				   
		$arr = array();        
        global $wpdb;
		
		$nr = 4;
		$terms = get_terms("project_cat","parent=0&hide_empty=0");
		 
		 $count = count($terms); $i = 0;
		 if ( $count > 0 ){
		     
		     foreach ( $terms as $term ) {
		       
			//   if($i%$nr == 0) echo "<li>";
			
			$stuffy = '';
			
		       	$stuffy .= "<ul id='location-stuff'><li>";
			   	$terms2 = get_terms("project_cat","parent=".$term->term_id."&hide_empty=0");
				

				$mese = '';
				
					$mese .= '<ul>';
					$mese .= "<a href='#' class='parent_taxe' rel='taxe_project_cat_".$term->term_id."' ><img rel='img_taxe_project_cat_".$term->term_id."'
					 src=\"".get_bloginfo('template_url')."/images/posted.png\" border='0' width=\"20\" height=\"20\" /></a> 
		       		<h3><a href='".get_term_link($term->slug,"project_cat")."'>" . $term->name;
					
					//."</a></h3>";
			   
			   $total_ads = ProjectTheme_get_custom_taxonomy_count2('project', $term->slug, 'project_cat'); //ProjectTheme_get_custom_taxonomy_count('ad',$term->slug);
			   
			   
			   
			   if($terms2)
				{
					$mese2 = '<ul class="'.$smk_closed.'" id="taxe_project_cat_'.$term->term_id.'">';
					foreach ( $terms2 as $term2 ) 
					{
						$tt = ProjectTheme_get_custom_taxonomy_count2('project', $term2->slug, 'project_cat'); //ProjectTheme_get_custom_taxonomy_count('project',$term2->slug);
		       			//$total_ads += $tt;
						$mese2 .= "<li><a href='".get_term_link($term2->slug,"project_cat")."'>" . $term2->name." (".$tt.")</a></li>";
						
						
						$terms3 = get_terms("project_cat","parent=".$term2->term_id."&hide_empty=0");
						
						if($terms3)
						{
							$mese2 .= '<ul class="baca_loc">';
							foreach ( $terms3 as $term3 ) 
							{
								$tt = ProjectTheme_get_custom_taxonomy_count2('project', $term3->slug, 'project_cat');; //ProjectTheme_get_custom_taxonomy_count('project',$term3->slug);
								//$total_ads += $tt;
								$mese2 .= "<li><a href='".get_term_link($term3->slug,"project_cat")."'>" . $term3->name." (".$tt.")</a></li>";
							
							}
							$mese2 .= '</ul>';
						}
						
					}
					
					$mese2 .= '</ul>';
				}
					
					$stuffy .= $mese."(".$total_ads.")</a></h3>";
					$stuffy .= $mese2;
					
					$mese2 = '';
					
					$stuffy .= '</ul></li>';
				$stuffy .= '</ul>';
				
		      // if(($i+1) % $nr == 0) echo "</li>";
			   
			   $i++;
		        $arr[] = $stuffy;
		     }
				
				//if(($i+1) % $nr != 0) echo "</li>";


		    // echo "</ul>";
		 }   
         
         //=======================================
		 
	 	$xx = count($arr);
		
		 
		 $tz = floor($xx / $nr);
		 $i = 0;
	
		 if($xx < $nr) $tz = $nr - $xx;
		
		 
		 
		 foreach($arr as $category)
		 	{
				if($i%$tz == 0) echo "<div class='stuffa'>
				";
				
				echo $category;
				
				if(($i+1) % $tz == 0) echo "</div>
				";			
				$i++;
			}
         
		 if(($i+2) % $tz != 0) echo "</div>
		 ";
		 
         ?>
                
                
                </div>
                </div>
                </div>
                </div>
                
    
    <?php
	
	
}

?>