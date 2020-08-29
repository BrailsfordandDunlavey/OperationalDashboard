<?php

	global $wpdb,$wp_rewrite,$wp_query;
	$username = $wp_query->query_vars['post_author'];
	$uid = $username;
	$paged = $wp_query->query_vars['paged'];

	$user = get_userdata($uid);
	//BillyB username changed to company name
	$username = get_user_meta($uid, 'rpr_company_name', true);
	
	function sitemile_filter_ttl($title){return __("Member Feedback",'ProjectTheme')." - ";}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	
get_header();
?>

             
                <div class="page_heading_me">
                        <div class="page_heading_me_inner">
							<div class="mm_inn"><?php printf(__("Member Feedback - %s", 'ProjectTheme'), $username); ?>   </div>
                  	            
                                        
                        </div>
                    
                    </div> 
<!-- ########## -->

<div id="main_wrapper">
		<div id="main" class="wrapper"> 
                



	<div id="content">
    		<div class="my_box3">
            <div class="padd10">
            
            	 
            	<div class="box_content">	
               <!-- ####### -->
                
                
                <?php
					
					global $wpdb;
					$page_rows = 25;
					
					$pagenum 	= isset($_GET['pagenum']) ? $_GET['pagenum'] : 1;
					$max 		= ' limit ' .($pagenum - 1) * $page_rows .',' .$page_rows; 
					
					global $wpdb;
					$query = "select * from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='1' order by id desc $max";
					$r = $wpdb->get_results($query);
					
					$query2 = "select count(id) tots from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='1' order by id desc";
					$r2 = $wpdb->get_results($query2);
					$total 	= $r2[0]->tots;
				
					$last = ceil($total/$page_rows);
					
					if(count($r) > 0)
					{
						echo '<table width="100%">';
							echo '<tr>';
								echo '<th>&nbsp;</th>';	
								echo '<th><b>'.__('Project Title','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('From User','ProjectTheme').'</b></th>';	
								echo '<th><b>'.__('Aquired on','ProjectTheme').'</b></th>';								
								echo '<th><b>'.__('Price','ProjectTheme').'</b></th>';
								echo '<th><b>'.__('Rating','ProjectTheme').'</b></th>';
								
							
							echo '</tr>';	
						
						
						foreach($r as $row)
						{
							$post = $row->pid;
							$post = get_post($post);
							$bid = projectTheme_get_winner_bid($row->pid);
							$user = get_userdata($row->fromuser);
							
							$dts = get_post_meta($row->pid,'closed_date',true);
							if(empty($dts)) $dts = current_time('timestamp',0);
							
							echo '<tr>';
								
								echo '<th><img class="img_class" src="'.ProjectTheme_get_first_post_image($row->pid, 42, 42).'" 
                                alt="'.$post->post_title.'" width="42" /></th>';	
								echo '<th><a href="'.get_permalink($row->pid).'">'.$post->post_title.'</a></th>';
								echo '<th><a href="'.ProjectTheme_get_user_profile_link($user->ID).'">'.$user->user_login.'</a></th>';								
								echo '<th>'.date_i18n('d-M-Y H:i:s', $dts).'</th>';								
								echo '<th>'.projectTheme_get_show_price($bid->bid).'</th>';
								echo '<th>'.ProjectTheme_get_project_stars(floor($row->grade/2)).' ('.floor($row->grade/2).'/5)</th>';
								
							
							echo '</tr>';
							echo '<tr>';
							echo '<th></th>';
							echo '<th colspan="5"><b>'.__('Comment','ProjectTheme').':</b> '.$row->comment.'</th>'	;						
							echo '</tr>';
							 
							echo '<tr><th colspan="6"><hr color="#eee" /></th></tr>';
							
						}
						echo '<tr>';
						echo '<th colspan="6">'. ProjectTheme_get_my_pagination_main(get_bloginfo('siteurl') . "/?p_action=user_feedback&post_author=".$uid, $pagenum, 'pagenum', $last ) .'</th>';
						echo '</tr>';
						
						echo '</table>';
					}
					else
					{
						_e("This member has no reviews yet.","ProjectTheme");	
					}
				?>
                
                
				<!-- ####### -->
                </div>
                
            </div>
            </div>
                

  </div>

<div id="right-sidebar">
	<ul class="xoxo">
	<?php dynamic_sidebar( 'other-page-area' ); ?>
	</ul>
</div>


</div></div> 

<?php

	//sitemile_after_content(); 
	
	get_footer();
	
?>
