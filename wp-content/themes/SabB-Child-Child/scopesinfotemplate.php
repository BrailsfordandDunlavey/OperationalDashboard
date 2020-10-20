<?php /* Template Name: Scopes Information */ 
session_start(); 
get_header();
	global $post;
 
?>
<div class="page_heading_me">
	<div class="page_heading_me_inner">
    <div class="main-pg-title">
    	<div class="mm_inn"><?php
						
							echo $post->post_title
						
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
		<div id="main" class="wrapper"> 




<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<?php the_content(); ?>			
<?php endwhile; // end of the loop. ?> 

<?php print_r($summed_entries);   
  $total_fees = array_sum($_SESSION['pa_arr']);  
  echo  '<style rel="stylesheet" type="text/css">

  th {
    height: 50px;
    font-weight: bold;
    text-align: left;
    background-color: #cccccc;
  }
  
  table {
    width: 100%;
  }

  tr:hover {background-color: #f5f5f5;}
  
  th, td {
    border-bottom: 1px solid #ddd;
  }
  
  </style>';

   //print_r($_SESSION['summed_entries']); 
  //echo $_SESSION['pa_arr'][$j];
   $in = 1;
   echo "<table><tr>";
        echo "<th>Task Category</th><th>PA</th><th>APM</th><th>PM</th><th>VP</th><th>EVP</th><th>Total Fees</th></tr>";
   for($i=0; $i<5; $i++){
    echo "<tr><td>";
        echo"Scope Item " .$in;
    echo "</td>";
    echo "<td>";
        echo $_SESSION['pa_arr_two'][$i];
    echo "</td>";
    echo "<td>";
        echo $_SESSION['apm_arr_two'][$i];
    echo "</td>";
    echo "<td>";
        echo $_SESSION['pm_arr_two'][$i];
    echo "</td>";
    echo "<td>";
        echo $_SESSION['vp_arr_two'][$i];
    echo "</td>";
    echo "<td>";
        echo $_SESSION['evp_arr_two'][$i];
    echo "</td>"; 
    echo "<td>";
        echo $_SESSION['summed_entries'][$i];
    echo "</td>";  
    echo "</tr>"; 
    $in++;
   }
   echo "</table>"
?>
 
</div>
</div>
<?php get_footer(); ?>