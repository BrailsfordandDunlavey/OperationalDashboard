<?php

 	if (! current_user_can('manage_options')) {
		add_filter('show_admin_bar', '__return_false');
	}


	load_theme_textdomain( 'ProjectTheme',  TEMPLATEPATH . '/languages' );
	// load the theme template for translation
	
	DEFINE("PROJECTTHEME_VERSION", "2.0.8b");
	DEFINE("PROJECTTHEME_RELEASE", "13 January 2015");
	
	//----------------------------------------------------------
	
	add_theme_support( 'post-thumbnails' );
	remove_action('wp_head', 'wp_generator');
	
	//----------------------------------------------------------
	
        
	global $current_theme_locale_name, $category_url_link, $location_url_link, $projects_url_nm;	
	$current_theme_locale_name = 'ProjectTheme';
	
	$category_url_link 	= "classification"; 
	$cc = get_option('projectTheme_category_slug_link');
	if(!empty($cc) && ProjectTheme_using_permalinks()) $category_url_link = $cc;	
	
	$location_url_link 	= "location"; 
	$cc = get_option('projectTheme_location_slug_link');
	if(!empty($cc) && ProjectTheme_using_permalinks()) $location_url_link = $cc;	
	
	$projects_url_nm 	= "projects"; 
	$cc = get_option('projectTheme_projects_slug_link');
	if(!empty($cc) && ProjectTheme_using_permalinks()) $projects_url_nm = $cc;
	
	//------------
	
	global $width_widget_categories, $height_widget_categories;
	$width_widget_categories = 190;
	$height_widget_categories = 65;
	add_image_size( 'my_category_image_thing', $width_widget_categories, $height_widget_categories, true ); //category images in the widget
	
//------------------ file includes -----------------------------
	get_template_part('password_reset');
	
	get_template_part('lib/first_run');
	get_template_part('lib/first_run_emails');
	get_template_part('lib/new_employee');
	get_template_part('lib/new_vendor');
	get_template_part('lib/admin_menu');
	get_template_part('lib/cronjob');
	get_template_part('lib/new_timesheet');
	get_template_part('lib/new_timesheet_matrix');
	get_template_part('lib/invoicing_periods');
	get_template_part('lib/expense_periods');
	get_template_part('lib/new_client_contact');
	get_template_part('lib/new_employee_expense');
	get_template_part('lib/new_employee_expense_cc');
	get_template_part('lib/new_vendor_payable');
	get_template_part('lib/import_cc');
	get_template_part('lib/import_cc_revise_date');
	get_template_part('lib/import_billings');
	get_template_part('lib/import_contacts');
	get_template_part('lib/upload');
	get_template_part('lib/projected_revenue');
	get_template_part('lib/expense_approval');
	get_template_part('lib/request_timeoff');
	get_template_part('lib/opportunity_checklist');
	get_template_part('lib/projected_time_projects');
	get_template_part('lib/confirm_gp_upload');
	get_template_part('lib/blog_page');
	get_template_part('lib/contract_checklist');
	get_template_part('lib/help');
	get_template_part('lib/employee_accruals');
	get_template_part('lib/terminate_employee');
	get_template_part('lib/test');
	get_template_part('lib/capacity_set');
	get_template_part('lib/vendor_payable_management');
	
	get_template_part('lib/exports/client_contact_export');
	get_template_part('lib/exports/confirm_hours_export');
	get_template_part('lib/exports/confirm_timeoff_export');
	get_template_part('lib/exports/expense_export');
	get_template_part('lib/exports/payroll_export');
	get_template_part('lib/exports/timeoff_export');
	
	get_template_part('lib/reports/actual_revenue');
	get_template_part('lib/reports/all_projects');
	get_template_part('lib/reports/any_expenses_details');
	get_template_part('lib/reports/any_hours_details');
	get_template_part('lib/reports/ar_report');
	get_template_part('lib/reports/cig_report');
	get_template_part('lib/reports/clients_no_gpid');
	get_template_part('lib/reports/closed_projects');
	get_template_part('lib/reports/contracts_report');
	get_template_part('lib/reports/employees_report');
	get_template_part('lib/reports/expense_management_unprocessed');
	get_template_part('lib/reports/nobill_report');
	get_template_part('lib/reports/personnel_changes');
	get_template_part('lib/reports/project_expenses_report');
	get_template_part('lib/reports/projected_revenue_projects');
	get_template_part('lib/reports/projected_time');
	get_template_part('lib/reports/projects_dashboard');
	get_template_part('lib/reports/reports_dashboard');
	get_template_part('lib/reports/staff_availability');
	get_template_part('lib/reports/staffing_calculator');
	get_template_part('lib/reports/targeted_projected_time');
	get_template_part('lib/reports/targeted_time');
	get_template_part('lib/reports/test');
	get_template_part('lib/reports/time_reports');
	get_template_part('lib/reports/timeoff_report');
	get_template_part('lib/reports/timesheet_report');
	get_template_part('lib/reports/wuc_dashboard');
	
	
	
	
	
	
	
	
	
	
	
	get_template_part('lib/login_register/custom2');
	
	get_template_part('lib/my_account/add_user_project');
	get_template_part('lib/my_account/approve_timeoff');
	get_template_part('lib/my_account/my_account');	
	get_template_part('lib/my_account/my_client_contacts');
	get_template_part('lib/my_account/my_employee_expenses');
	get_template_part('lib/my_account/invoicing_dashboard');
	get_template_part('lib/my_account/my_profile');
	get_template_part('lib/my_account/my_projects');
	get_template_part('lib/my_account/my_team_time');
	get_template_part('lib/my_account/my_team_time_all');
	get_template_part('lib/my_account/my_time');
	get_template_part('lib/my_account/my_vendor_payables');
	get_template_part('lib/my_account/pass_logo');
	get_template_part('lib/my_account/personal_information');
	get_template_part('lib/my_account/remove_user_project');
	
	get_template_part('lib/venues/venues_query');
	
//--------------------------------------------------------------
//------------ hooks and filters -------------------------------

	add_action('save_post',			'projectTheme_save_custom_fields');
	add_action('generate_rewrite_rules', 'projectTheme_rewrite_rules' );
	add_action('query_vars', 		'ProjectTheme_add_query_vars'); 
	add_action("template_redirect", 'ProjectTheme_template_redirect');
	add_action('init', 				'ProjectTheme_create_post_type' );	
	add_action('wp_head',			'ProjectTheme_add_js_coin_slider');
	add_action('the_content',		'ProjectTheme_display_my_account_page');
	add_action('the_content',		'ProjectTheme_display_my_account_pay_with_credits');
	add_action('the_content',		'ProjectTheme_display_my_account_outstanding_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_awaiting_payments_page');
	
	add_action('the_content',		'ProjectTheme_display_advanced_search_disp_page');
	add_action('the_content',		'ProjectTheme_display_my_account_pay_for_project');
	add_action('the_content',		'ProjectTheme_display_my_account_awaiting_completion_page');
	add_action('the_content',		'ProjectTheme_display_provider_search_disp_page');
	
	add_filter('the_content',		'ProjectTheme_display_blog_content_page');
	add_filter('the_content',		'ProjectTheme_display_latest_closed_projects_page');
	
	add_action('the_content',		'ProjectTheme_display_all_locations_page');
	add_action('the_content',		'ProjectTheme_display_all_categories_page');
	add_action('the_content',		'ProjectTheme_display_my_account_personal_info');
	add_action('the_content',		'ProjectTheme_display_my_account_payments');
	add_action('the_content',		'ProjectTheme_display_my_account_private_messages');
	add_action('the_content',		'ProjectTheme_display_my_account_feedbacks');
	add_action('the_content',		'ProjectTheme_display_my_account_delivered_projects');
	//BillyB add to-award
	add_action('the_content',		'ProjectTheme_display_my_account_to_award_projects');	
	
	add_action('the_content',		'ProjectTheme_display_my_account_active_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_unpublished_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_outstanding_payments');
	add_action('the_content',		'ProjectTheme_display_my_account_closed_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_completed_projects');
	
	add_action('the_content',		'ProjectTheme_display_my_account_won_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_bid_projects');
	add_action('the_content',		'ProjectTheme_display_my_account_open_proposals');
	add_action('the_content',		'ProjectTheme_display_all_projects_page');
	
	add_action('draft_to_publish', 				'ProjectTheme_run_when_post_published',10,1);
	
	add_action('the_content',							'ProjectTheme_display_post_new_pg');
	add_action('admin_menu',							'ProjectTheme_set_admin_menu');
	add_action('admin_head', 							'ProjectTheme_admin_style_sheet');
	add_action('widgets_init',	 						'ProjectTheme_framework_init_widgets' );
	add_action("manage_project_posts_custom_column", 	"ProjectTheme_my_custom_columns");
	add_filter("manage_edit-project_columns", 			"ProjectTheme_my_projects_columns");
	add_action('wp_enqueue_scripts', 					'ProjectTheme_add_theme_styles');
	add_action('wp_head',								'ProjectTheme_custom_css_thing');
	add_action('admin_notices', 						'projectTheme_admin_notices');
	add_filter('wp_head',								'ProjectTheme_add_max_nr_of_images');
	add_filter("ProjectTheme_get_regular_post_project", 'projectTheme_get_post_main_function', 0, 1);
	
	add_filter( 'manage_edit-project_sortable_columns', 				'ProjectTheme_sortable_cake_column' ); 
	add_action( 'pre_get_posts', 										'ProjectTheme_my_backend_projects_orderby' ); 
	add_filter("ProjectTheme_get_post_blog_function", 					'ProjectTheme_get_post_blog_function', 1);
	add_filter("projectTheme_get_post_outstanding_project_function", 	'projectTheme_get_post_outstanding_project_function', 1);
	add_filter("projectTheme_get_post_paid_function", 					'projectTheme_get_post_paid_function', 1);
	add_filter("projectTheme_get_post_pay_function", 					'projectTheme_get_post_pay_function', 1);
	add_filter("projectTheme_get_post_awaiting_compl_function", 		'projectTheme_get_post_awaiting_compl_function', 1);
	add_filter("projectTheme_get_post_awaiting_payment_function", 		'projectTheme_get_post_awaiting_payment_function', 1);

add_filter( 'gform_confirmation_2', 'custom_confirmation', 10, 4 );
function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
	$confirmation = array( 'redirect' => 'http://b2bconnector.com/index.php/dashboard/' );

}



 add_action( 'admin_init', 'Project_CLASS_THM_my_plugin_admin_init' );
 
 function Project_CLASS_THM_my_plugin_admin_init() {
       
	    wp_enqueue_style('thickbox'); // call to media files in wp
		wp_enqueue_script('thickbox');
		wp_enqueue_script( 'media-upload'); 
	   
    }

class Project_Walker_Nav_Menu extends Walker_Nav_Menu {
  function start_lvl(&$output, $depth) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class=\"dropdown\">\n";
  }
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

if(!function_exists('ProjectTheme_get_categories_clck'))
{
function ProjectTheme_get_categories_clck($taxo, $selected = "", $include_empty_option = "", $ccc = "" , $xx = "")
{
	$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
	$terms = get_terms( $taxo, $args );
	
	$ret = '<select name="'.$taxo.'_cat" class="'.$ccc.'" id="'.$ccc.'" '.$xx.'>';
	if(!empty($include_empty_option)) $ret .= "<option value=''>".$include_empty_option."</option>";
	
	if(empty($selected)) $selected = -1;
	
	foreach ( $terms as $term )
	{
		$id = $term->term_id;
		$ret .= '<option '.($selected == $id ? "selected='selected'" : " " ).' value="'.$id.'">'.$term->name.'</option>';

	}
	
	$ret .= '</select>';
	
	return $ret;
	
}
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_build_my_cat_arr($pid)
{
	$my_arr 	= array();
	$cat 		= wp_get_object_terms($pid, 'project_cat');
	
	if(is_array($cat))
	foreach($cat as $c)
	{
		$my_arr[] = $c->term_id;	
	}
	

	return $my_arr;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_is_selected_thing($arr, $ids)
{

	if(count($arr) == 0) return false;
	foreach($arr as $a)
	if($ids == $a) {   return true; }
	
	return false;	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_categories_multiple($taxo, $selected_arr = "")
{
	$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
	$terms = get_terms( $taxo, $args );
	
 
	foreach ( $terms as $term )
	{
		$id = $term->term_id;
		
		$ret .= '<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $id) == true ? "checked='checked'" : " " ).' value="'.$id.'" name="'.$taxo.'_cat_multi[]"> '.$term->name.'<br/>';
		
		$args = "orderby=name&order=ASC&hide_empty=0&parent=".$id;
		$sub_terms = get_terms( $taxo, $args );	
		
		if($sub_terms)
		foreach ( $sub_terms as $sub_term )
		{
			$sub_id = $sub_term->term_id; 
			$ret .= '&nbsp; &nbsp; &nbsp; 
			<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id) == true ? "checked='checked'" : " " ).' value="'.$sub_id.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term->name.'<br/>';
 
			
			$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id;
			$sub_terms2 = get_terms( $taxo, $args2 );	
			
			if($sub_terms2)
			foreach ( $sub_terms2 as $sub_term2 )
			{
				$sub_id2 = $sub_term2->term_id; 
				$ret .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
				<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id2) == true ? "checked='checked'" : " " ).' value="'.$sub_id2.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term2->name.'<br/>';
 
			 
				 
				 $args3 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id2;
				 $sub_terms3 = get_terms( $taxo, $args3 );	
				 
				 if($sub_terms3)
				 foreach ( $sub_terms3 as $sub_term3 )
				{
					$sub_id3 = $sub_term3->term_id; 
					 
					$ret .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id3) == true ? "checked='checked'" : " " ).' value="'.$sub_id2.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term3->name.'<br/>';
  
					 
				}
			}
		}
		
	}
	
	
	return $ret;
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projecttheme_get_post_new_error_thing($id)
{
	global $class_errors;
	if(is_array($class_errors))
	{
		if(isset($class_errors[$id])) return $class_errors[$id];
	}
	
	return '';
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projecttheme_get_post_new_error_thing_display($id)
{
	global $MYerror;
	if(is_array($MYerror))
	{
		if(isset($MYerror[$id])) return '<p class="display-error-inside">'.$MYerror[$id].'</p>';
	}
	
	return '';
}


//---------------------------

add_filter('wp_mail_from',		'ProjectTheme_1_mail_from');
add_filter('wp_mail_from_name',	'ProjectTheme_1_mail_from_name');

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
 
function ProjectTheme_get_user_table_row($uid)
{
	$author_info = get_userdata($uid);
	
	?>
	 <div class="user-table-row">
		<div class="user-table-user-avatar">
        		<img src="<?php echo ProjectTheme_get_avatar($uid,95, 95) ?>" alt="avatar-user" class="acc_m1" width="95" height="95" />
        </div>
        
        <div class="user-table-user-info">
        		<!-- <h3><?php echo $author_info->user_login ?></h3> -->
        		<h3><a href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $uid; ?>"><?php echo get_user_meta($uid, 'company_name', true); ?></a></h3>
        		<!-- <h3><a href="<?php bloginfo('siteurl'); ?>/members/<?php echo $author_info->user_login; ?>/profile/"><?php echo get_user_meta($uid, 'company_name', true); ?></a></h3> -->
                <p class="excerpt-thing">
                	<?php
						$user_description = get_user_meta($uid,'user_description',true);
						$user_description = strip_tags($user_description);
						
						if(empty($user_description)) _e('This user doesnt have a description.','ProjectTheme');
						else
						{
							echo substr($user_description,0,270);
						}
					
					?>		
                </p>
                <div class="user-poster-thing">
            
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $uid; ?>"><?php echo $author_info->company_name?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($uid); ?></li>
                    <li><a href="<?php echo ProjectTheme_get_priv_mess_page_url('send', '', '&uid='.$uid.'&pid='); ?>"><?php _e('Contact User','ProjectTheme'); ?></a></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($uid); ?>"><?php _e('User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
                
        </div>
					 
					
					</div>	
	<?php
}



function projecttheme_search_box_thing()
{ return '';
?>	


 <?php if(!projecttheme_is_home()): ?>
            
            <script>
			
			jQuery(function(){

				jQuery("ul.dropdown li").hover(function(){
				
					jQuery(this).addClass("hover");
					jQuery('ul:first',this).css('visibility', 'visible');
				
				}, function(){
				
					jQuery(this).removeClass("hover");
					jQuery('ul:first',this).css('visibility', 'hidden');
				
				});
				
				jQuery("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
			
			});
			
			
			</script>
            
			
			<div class="middle-header-bg">
				<div class="middle-header wrapper">
						
					<div class="near_search_links">
                    	 <ul class="dropdown">
        					<li><a href="#" class="main-browse-by-cat"><?php _e('Browse By Category','ProjectTheme') ?></a>
                        	<?php
							
								$terms_cats = get_terms('project_cat','hide_empty=false');
								if(count($terms_cats) > 0):
							?>	
                                <ul class="sub_menu">
                                		<?php
                                        
											foreach($terms_cats as $ct)
											{
												echo '<li><a href="'.get_term_link($ct->slug, 'project_cat').'">'.$ct->name.'</a></li>';	
											}
										
										?>
                                </ul>      
                                               
                            <?php endif; ?>       
                        	</li>	
                    	</ul>
                    </div>	 
                     
	       
        <div class="my_placeholder_4_suggest">
        <div id="suggest" >
                    <form method="get" action="<?php echo projectTheme_advanced_search_link(); ?>">
						<input type="text" onfocus="this.value=''" id="big-search" name="term" autocomplete="off" onkeyup="suggest(this.value);" onblur="fill();"  value="<?php if(isset($_GET['term'])) echo $_GET['term']; 
						else echo $default_search; ?>" />
					
				<?php	//echo sitemile_get_categories_slug("project_cat", $_GET["project_cat_cat"], 1, "big-search-select"); 
				?>
		
					
					<input type="submit" id="big-search-submit" name="search_me" value="<?php _e("Start Search","ProjectTheme"); ?>" />
					</form>
                    
                    <div class="suggestionsBox" id="suggestions" style="z-index:999;display: none;"> <img src="<?php echo get_bloginfo('template_url');?>/images/arrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />
        <div class="suggestionList" id="suggestionsList"> &nbsp; </div>
      </div></div>
                    

                        
                        <!-- ###### -->
				</div>
                </div>
				
			</div> <!-- middle-header-bg -->
		
			<?php endif; ?>

<?php	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_1_mail_from() 
{
	$emailaddress = 'wordpress@wordpress.org';
	$opt = get_option('ProjectTheme_email_addr_from');
	if(!empty($opt)) $emailaddress = $opt;	
	return $emailaddress;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_1_mail_from_name() 
{
 	$sendername = 'WordPress';
	$opt = get_option('ProjectTheme_email_name_from');
	if(!empty($opt)) $sendername = $opt;
	return $sendername;
}	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_run_when_post_published($post)
{
    if(is_array($post))
	{
		if($post['post_type'] == 'project'):
			
		 
			
			 ProjectTheme_send_email_subscription($post['ID']);
			 ProjectTheme_send_email_posted_project_approved($post['ID']);
							ProjectTheme_send_email_posted_project_approved_admin($post['ID']);
			 
		endif;
	}
	
	if(is_object($post))
	{
		if($post->post_type == 'project'):
			
			
			 
			 ProjectTheme_send_email_subscription($post->ID);
			 ProjectTheme_send_email_posted_project_approved($post->ID);
							ProjectTheme_send_email_posted_project_approved_admin($post->ID);
			 
		endif;
	}
}		
	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_search_into($custid, $val)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_custom_relations where custid='$custid'";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0) return 0;
	else
	foreach($r as $row) // = mysql_fetch_object($r))
	{
		if($row->catid == $val) return 1;
	}

	return 0;
}


function projectTheme_search_into_users($custid, $val)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_user_custom_relations where custid='$custid'";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0) return 0;
	else
	foreach($r as $row) // = mysql_fetch_object($r))
	{
		if($row->catid == $val) return 1;
	}

	return 0;
}


	function ProjectTheme_register_my_menus() {
		register_nav_menu( 'primary-projecttheme-header', 'ProjectTheme Top Header Menu' );
		register_nav_menu( 'primary-projecttheme-main-header', 'ProjectTheme Main Header Menu' );
	}
	
	add_action( 'init', 'ProjectTheme_register_my_menus' ); 


function cimy_update_ExtraFields_new_me() {
	global $wpdb, $wpdb_data_table, $user_ID, $max_length_value, $fields_name_prefix, $cimy_uef_file_types, $user_level, $cimy_uef_domain;
	include_once(ABSPATH.'/wp-admin/includes/user.php');
	// if updating meta-data from registration post then exit
	if (isset($_POST['cimy_post']))
		return;

	if (isset($_POST['user_id'])) {
		$get_user_id = $_POST['user_id'];

		if (!current_user_can('edit_user', $get_user_id))
			return;
	}
	else
		return;
	
	//echo "asd";
	
	if(!function_exists('get_cimyFields')) return;
	
	$get_user_id = intval($get_user_id);
	$profileuser = get_user_to_edit($get_user_id);
	$user_login = $profileuser->user_login;
	$user_displayname = $profileuser->display_name;
	$extra_fields = get_cimyFields(false, true);

	$query = "UPDATE ".$wpdb_data_table." SET VALUE=CASE FIELD_ID";
	$i = 0;

	$field_ids = "";
	$mail_changes = "";

	foreach ($extra_fields as $thisField) {
		$field_id = $thisField["ID"];
		$name = $thisField["NAME"];
		$type = $thisField["TYPE"];
		$label = $thisField["LABEL"];
		$rules = $thisField["RULES"];
		$unique_id = $fields_name_prefix.$field_id;
		$input_name = $fields_name_prefix.esc_attr($name);
		$field_id_data = $input_name."_".$field_id."_data";
		$advanced_options = cimy_uef_parse_advanced_options($rules["advanced_options"]);

		cimy_insert_ExtraFields_if_not_exist($get_user_id, $field_id);

		// if the current user LOGGED IN has not enough permissions to see the field, skip it
		// apply only for EXTRA FIELDS
		if ($rules['show_level'] == 'view_cimy_extra_fields')
		{
			if (!current_user_can($rules['show_level']))
				continue;
		}
		else if ($user_level < $rules['show_level'])
			continue;

		// if show_level == anonymous then do NOT ovverride other show_xyz rules
		if ($rules['show_level'] == -1) {
			// if flag to show the field in the profile is NOT activated, skip it
			if (!$rules['show_in_profile'])
				continue;
		}

		$prev_value = $wpdb->escape(stripslashes($_POST[$input_name."_".$field_id."_prev_value"]));
		if (cimy_uef_is_field_disabled($type, $rules['edit'], $prev_value))
			continue;

		if ((isset($_POST[$input_name])) && (!in_array($type, $cimy_uef_file_types))) {
			if ($type == "dropdown-multi")
				$field_value = stripslashes(implode(",", $_POST[$input_name]));
			else
				$field_value = stripslashes($_POST[$input_name]);

			if ($type == "picture-url")
				$field_value = str_replace('../', '', $field_value);

			if (isset($rules['max_length']))
				$field_value = substr($field_value, 0, $rules['max_length']);
			else
				$field_value = substr($field_value, 0, $max_length_value);

			$field_value = $wpdb->escape($field_value);

			if ($i > 0)
				$field_ids.= ", ";
			else
				$i = 1;

			$field_ids.= $field_id;

			$query.= " WHEN ".$field_id." THEN ";

			switch ($type) {
				case 'dropdown':
				case 'dropdown-multi':
					$ret = cimy_dropDownOptions($label, $field_value);
					$label = $ret['label'];
				case 'picture-url':
				case 'textarea':
				case 'textarea-rich':
				case 'password':
				case 'text':
					$value = "'".$field_value."'";
					$prev_value = "'".$prev_value."'";
					break;

				case 'checkbox':
					$value = $field_value == '1' ? "'YES'" : "'NO'";
					$prev_value = $prev_value == "YES" ? "'YES'" : "'NO'";
					break;

				case 'radio':
					$value = $field_value == $field_id ? "'selected'" : "''";
					$prev_value = "'".$prev_value."'";
					break;
			}

			$query.= $value;
		}
		// when a checkbox is not selected then it isn't present in $_POST at all
		// file input in html also is not present into $_POST at all so manage here
		else {
			$rules = $thisField['RULES'];

			if (in_array($type, $cimy_uef_file_types)) {
				if ($type == "avatar") {
					// since avatars are drawn max to 512px then we can save bandwith resizing, do it!
					$rules['equal_to'] = 512;
				}

				if (isset($_POST[$input_name.'_del']))
					$delete_file = true;
				else
					$delete_file = false;

				if (isset($_POST[$input_name."_".$field_id."_prev_value"]))
					$old_file = stripslashes($_POST[$input_name."_".$field_id."_prev_value"]);
				else
					$old_file = false;

				$field_value = cimy_manage_upload($input_name, $user_login, $rules, $old_file, $delete_file, $type, (!empty($advanced_options["filename"])) ? $advanced_options["filename"] : "");

				if ((!empty($field_value)) || ($delete_file)) {
					if ($i > 0)
						$field_ids.= ", ";
					else
						$i = 1;

					$field_ids.= $field_id;

					$value = "'".$field_value."'";
					$prev_value = "'".$prev_value."'";

					$query.= " WHEN ".$field_id." THEN ";
					$query.= $value;
				}
				else {
					$prev_value = $value;

					$file_on_server = cimy_uef_get_dir_or_filename($user_login, $old_file, false);
					if (($type == "picture") || ($type == "avatar"))
						cimy_uef_crop_image($file_on_server, $field_id_data);
				}
			}

			if ($type == 'checkbox') {
				// if can be editable then write NO
				// there is no way to understand if was YES or NO previously
				// without adding other hidden inputs so write always
				if ($i > 0)
					$field_ids.= ", ";
				else
					$i = 1;

				$field_ids.= $field_id;

				$field_value = "NO";
				$value = "'".$field_value."'";
				$prev_value = $prev_value == "YES" ? "'YES'" : "'NO'";

				$query.= " WHEN ".$field_id." THEN ";
				$query.= $value;
			}

			if ($type == 'dropdown-multi') {
				// if can be editable then write ''
				// there is no way to understand if was YES or NO previously
				// without adding other hidden inputs so write always
				if ($i > 0)
					$field_ids.= ", ";
				else
					$i = 1;

				$field_ids.= $field_id;

				$field_value = '';
				$value = "'".$field_value."'";
				$prev_value = "'".$prev_value."'";
				$ret = cimy_dropDownOptions($label, $field_value);
				$label = $ret['label'];
				$query.= " WHEN ".$field_id." THEN ";
				$query.= $value;
			}
		}
		if (($rules["email_admin"]) && ($value != $prev_value) && ($type != "registration-date")) {
			$mail_changes.= sprintf(__("%s previous value: %s new value: %s", $cimy_uef_domain), $label, stripslashes($prev_value), stripslashes($value));
			$mail_changes.= "\r\n";
		}
	}

	if ($i > 0) {
		$query.=" ELSE FIELD_ID END WHERE FIELD_ID IN(".$field_ids.") AND USER_ID = ".$get_user_id;

		// $query WILL BE: UPDATE <table> SET VALUE=CASE FIELD_ID WHEN <field_id1> THEN <value1> [WHEN ... THEN ...] ELSE FIELD_ID END WHERE FIELD_ID IN(<field_id1>, [<field_id2>...]) AND USER_ID=<user_id>
		$wpdb->query($query);
	}

	// mail only if set and if there is something to mail
	if (!empty($mail_changes)) {
		$admin_email = get_option('admin_email');
		$mail_subject = sprintf(__("%s (%s) has changed one or more fields", $cimy_uef_domain), $user_displayname, $user_login);
		wp_mail($admin_email, $mail_subject, $mail_changes);
	}
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_add_max_nr_of_images()
{
	?>
    
    <script type="text/javascript">
		<?php
		$ProjectTheme_enable_max_images_limit = get_option('ProjectTheme_enable_max_images_limit');
		if($ProjectTheme_enable_max_images_limit == "yes")
		{
			$projectTheme_nr_max_of_images = get_option('projectTheme_nr_max_of_images');
			if(empty($projectTheme_nr_max_of_images))	 $projectTheme_nr_max_of_images = 10;
		}
		else $ProjectTheme_enable_max_images_limit = 1000;
		
		if(empty($projectTheme_nr_max_of_images)) $projectTheme_nr_max_of_images = 100;
		
		?>
		
		
		
		var maxNrImages_PT = <?php echo $projectTheme_nr_max_of_images; ?>;
	
	</script>
    <?php
    if ( is_singular( 'project' ) ):
	
	$get_bidding_panel = 'get_bidding_panel';
	$get_bidding_panel = apply_filters('ProjectTheme_get_bidding_panel_string', $get_bidding_panel) ;
	
	?>
	
    <!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="<?php bloginfo('template_url') ?>/lib/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
    
    <!-- Add fancyBox -->
    <link rel="stylesheet" href="<?php bloginfo('template_url') ?>/lib/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php bloginfo('template_url') ?>/lib/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
    
    <!-- Optionally add helpers - button, thumbnail and/or media -->
    <link rel="stylesheet" href="<?php bloginfo('template_url') ?>/lib/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php bloginfo('template_url') ?>/lib/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="<?php bloginfo('template_url') ?>/lib/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
    
    <link rel="stylesheet" href="<?php bloginfo('template_url') ?>/lib/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
    <script type="text/javascript" src="<?php bloginfo('template_url') ?>/lib/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
    
    
    <script>
	jQuery( document ).ready(function() {
		
	 	var pid = jQuery("#submit-proposal-id").attr('rel');		
		jQuery("#submit-proposal-id").fancybox({
			 
				'scrolling'         : 'no',
				'padding'           : 0,
				'centerOnScroll'    : true,
				'href'              : '<?php bloginfo('siteurl') ?>/?<?php echo $get_bidding_panel ?>=1&pid=' + pid,
				'type'              : 'ajax'
			 
		});
		
		
		var pid = jQuery('.message_brd_cls').attr('rel');
		jQuery(".message_brd_cls").fancybox({
			 
				'scrolling'         : 'no',
				'padding'           : 0,
				'centerOnScroll'    : true,
				'href'              : '<?php bloginfo('siteurl') ?>/?get_message_board=' + pid,
				'type'              : 'ajax'
			 
		});
 
		
		jQuery('.image_gal1').fancybox();
		
		});
	
	</script>
    
    <?php
	endif;  
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_save_custom_fields($pid)
{
	$pst = get_post($pid);
	
	if($pst->post_type == "project"):
	if(isset($_POST['fromadmin']))
	{	
	
		update_post_meta($pid, 'finalised_posted', '1');
		
		$ending = get_post_meta($pid,"ending",true);
		$views = get_post_meta($pid,"views",true);
		$closed = get_post_meta($pid,"closed",true);
			
		$reverse = get_post_meta($pid, "reverse", true);	
			
		update_post_meta($pid,"ending",strtotime($_POST['ending']));	
		if(empty($views)) update_post_meta($pid,"views",0);	
		
		
		if($reverse == "yes") update_post_meta($pid, "reverse", "yes");
		else update_post_meta($pid, "reverse", "no");
			
		update_post_meta($pid, "budgets", $_POST["budgets"]);
			
		if($_POST['hide_project'] == '1') 
		update_post_meta($pid,"hide_project",'1');
		else
		update_post_meta($pid,"hide_project",'0');	
			
			
		if($_POST['featureds'] == '1') 
		update_post_meta($pid,"featured",'1');
		else
		update_post_meta($pid,"featured",'0');
		
		if($_POST['closed'] == '1') 
			{
				
				update_post_meta($pid,"closed",'1');
			}
		else
		{
			if($closed == "1") 	update_post_meta($pid,"ending",current_time('timestamp',0) + 30*24*3600);		
			update_post_meta($pid,"closed",'0');
			
		}
					
				if(isset($_POST['private_bids']))
				update_post_meta($pid, "private_bids", $_POST['private_bids']); 
				
		
		if(isset($_POST['price']))
		update_post_meta($pid,"price",$_POST['price']);
		
		if(isset($_POST['Location']))
		update_post_meta($pid,"Location",$_POST['Location']);
		
		for($i=0;$i<count($_POST['cust_ids']);$i++)
		{
			$id = $_POST['cust_ids'][$i];
			$valval = $_POST['custom_field_value_'.$id];		
			
			if(is_array($valval))
			{
				delete_post_meta($pid, 'custom_field_ID_'.$id);
				
				for($k=0;$k<count($valval);$k++)
					add_post_meta($pid, 'custom_field_ID_'.$id, $valval[$k]);
			}
			else
			update_post_meta($pid, 'custom_field_ID_'.$id, $valval);
		}
		}
		
		update_post_meta($pid,'unpaid','0');
		do_action('ProjectTheme_execute_on_submit_1', $pid);
	endif;
}


	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_custom_css_thing()
{

	$str = get_option('projectTheme_custom_CSS');
	$opt = stripslashes($str);
	if(!empty($op)):
	
?>
	<style type="text/css">
	<?php echo $opt; ?>	
	</style>


<?php	
	endif;

}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

add_action('wp_print_scripts', 'projecttheme_my_enqueue_scripts');


	function wp_tiny_mce_mine( $teeny = false, $settings = false ) {
		 
	
		static $num = 1;
	
		if ( ! class_exists('_WP_Editors' ) )
			require_once( ABSPATH . WPINC . '/class-wp-editor.php' );
	
		$editor_id = 'content' . $num++;
	
		$set = array(
			'teeny' => $teeny,
			'tinymce' => $settings ? $settings : true,
			'quicktags' => false
		);
	
		$set = _WP_Editors::parse_settings($editor_id, $set);
		_WP_Editors::editor_settings($editor_id, $set);
	}
 

function projecttheme_my_enqueue_scripts() {      
        wp_enqueue_script( 'tiny_mce' );
        wp_tiny_mce_mine();
}


function ProjectTheme_get_my_pagination_main($url, $current_page, $field_page, $total_pages, $other = '')
{
	$s = '';
	$s .= '<div class="wp-pagenavi"> <span class="pages">'.sprintf(__('Page %s of %s','ProjectTheme'), $current_page, $total_pages).'</span>';
	
	$batch 	= 5;  
	$raport = ceil($current_page/$batch) - 1; if ($raport < 0) $raport = 0;
	$start 		= $raport * $batch + 1; 
	$end		= $start + $batch - 1;
	
	if($end > $total_pages) $end = $total_pages;
	
	$previous_pg = $current_page - 1;
 
		
	$next_pg = $current_page + 1;
	if($next_pg > $total_pages) $next_pg = 1;
	
	//----------------------
	
	if($current_page > 1)
	$s .= '<a href="'.$url.'&'.$field_page.'=1'.$other.'">&laquo; '.__('First','Walleto').'</a>';
	
	if($previous_pg > 0)
	$s.= '<a href="'.$url.'&'.$field_page.'='.$previous_pg.$other.'">&laquo;</a>';		
	
	$s .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';	
	//BillyB Don't know why this for statement is necessary...
	for($i = $start; $i <= $end; $i ++) {
			if ($end == 1){
			$s.='<span class="current"></span>';}
			elseif($i == $current_page) {
				$s .= '&nbsp;&nbsp;&nbsp;<span class="current">'.$i.'</span>';
			} else {
				$s .= '&nbsp;&nbsp;&nbsp;<a class="page larger" href="'.$url.'&'.$field_page.'='.$i.$other.'">'.$i.'</a>';
			}
		}
	
	//extend
	if($end < $total_pages) $s .= '<span class="extend">...</span>';
	
	$next_pg = $current_page + 1;
	if($next_pg > $total_pages) $next_pg = 1;
	
	if($total_pages > $current_page)
	$s .= '<a href="'.$url.'&'.$field_page.'='.$next_pg.$other.'" class="page larger">&raquo;</a>';
	
	if($total_pages > $current_page)
	$s .= '<a href="'.$url.'&'.$field_page.'='.$total_pages.$other.'" class="page larger">'.__('Last','ProjectTheme').' &raquo;</a>';	
		
	return $s.'</div>';	
	
}

function ProjectTheme_add_theme_styles()  
{ 
	global $wp_query;
  	$new_Project_step = $wp_query->query_vars['post_new_step'];
    $p_action			= $wp_query->query_vars['p_action'];
	
  // Register the style like this for a theme:  
  // (First the unique name for the style (custom-style) then the src, 
  // then dependencies and ver no. and media type)
 	wp_register_style( 'mega_menu_thing', 	get_bloginfo('template_url').'/css/menu.css', array(), '20120822', 'all' );
	 

	
	wp_register_style( 'bx_styles', get_bloginfo('template_url').'/css/bx_styles.css', array(), '20120822', 'all' );
	wp_register_script( 'easing', get_bloginfo('template_url').'/js/jquery.easing.1.3.js');
	wp_register_script( 'bx_slider', get_bloginfo('template_url').'/js/jquery.bxSlider.min.js');
 
	
	
	wp_register_script( 'html5_js', get_bloginfo('template_url').'/js/html5.js');
	wp_register_script( 'jquery_ui', get_bloginfo('template_url').'/js/vendor/jquery.ui.widget.js');
	wp_register_script( 'templ_min', get_bloginfo('template_url').'/js/templ.min.js');
	wp_register_script( 'load_image', get_bloginfo('template_url').'/js/load_image.min.js');
	wp_register_script( 'canvas_to_blob', get_bloginfo('template_url').'/js/canvas_to_blob.js');
	wp_register_script( 'iframe_transport', get_bloginfo('template_url').'/js/jquery.iframe-transport.js');
	
	wp_register_script( 'fileupload_main', get_bloginfo('template_url').'/js/jquery.fileupload.js');
	wp_register_script( 'fileupload_fp', get_bloginfo('template_url').'/js/jquery.fileupload-fp.js');
	wp_register_script( 'fileupload_ui', get_bloginfo('template_url').'/js/jquery.fileupload-ui.js');
	
	wp_register_script( 'locale_thing', get_bloginfo('template_url').'/js/locale.js');
	wp_register_script( 'main_thing', get_bloginfo('template_url').'/js/main.js'); 
	
	wp_enqueue_script( 'jqueryhoverintent', get_bloginfo('template_url') . '/js/jquery.hoverIntent.minified.js', array('jquery') );
	wp_enqueue_script( 'megamenua', get_bloginfo('template_url') . '/js/responsive_menu.js', array('jquery') );
	
	global $wp_styles, $wp_scripts;
	
 
	
	 wp_enqueue_style( 'bx_styles' );
		 wp_enqueue_script( 'easing' );
		 wp_enqueue_script( 'bx_slider' );
		 wp_enqueue_script( 'jqueryhoverintent' );
		 wp_enqueue_script( 'megamenua' );
		 
	wp_enqueue_style( 'mega_menu_thing' );
 
	global $post;
	$ssl = get_option('ProjectTheme_my_account_personal_info_id');
	
	if($new_Project_step == "2" or $p_action == "edit_project" or $p_action == "repost_project" or $post->ID == $ssl ):

	  	// enqueing:
 
		
		
		 wp_enqueue_script( 'html5_js' );
		 wp_enqueue_script( 'jquery_ui' );
		 wp_enqueue_script( 'templ_min' );
		 wp_enqueue_script( 'load_image' );
		 wp_enqueue_script( 'canvas_to_blob' );
		 wp_enqueue_script( 'iframe_transport' );
		 
		 wp_enqueue_script( 'fileupload_main' );
		 wp_enqueue_script( 'fileupload_fp' );
		 wp_enqueue_script( 'fileupload_ui' );
		 wp_enqueue_script( 'locale_thing' );
		 wp_enqueue_script( 'main_thing' );
	 
		 
		$wp_styles->add_data('bootstrap_ie6', 'conditional', 'lte IE 7');
		
		
	endif;
}



/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_2_user_types()
{
	$ProjectTheme_enable_2_user_tp = get_option('ProjectTheme_enable_2_user_tp');
	if(	$ProjectTheme_enable_2_user_tp == "yes") return true;
	return false;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
/*function ProjectTheme_is_user_provider($uid)
{
	if(!ProjectTheme_2_user_types()) return true;
	
	//----------------------
	
		$can_do_both = get_user_meta($uid, 'can_do_both', true);
		if($can_do_both == "yes") return true;
	
	//----------------------
	
	$user_tp = get_user_meta($uid, 'user_tp', true);
	if($user_tp == "service_provider") return true;
	
	$user = get_userdata($uid);
	
	if($user->user_level == 10) return true;
	return false;	
}*/

function ProjectTheme_is_user_provider($uid)
{
	if(!ProjectTheme_2_user_types()) return true;
	
	//----------------------
	
		$can_do_both = get_user_meta($uid, 'can_do_both', true);
		if($can_do_both == "yes") return true;
		
	//----------------------
	$user_data = get_userdata($uid);
	
	$user_roles = $user_data->roles;
	
	
	if(is_array($user_roles))
    $user_role = array_shift($user_roles);
	if($user_role == "service_provider") return true;
	 
	
	$user_tp = get_user_meta($uid, 'user_tp', true);
	if($user_tp == "service_provider") return true;
	if($user_role == "administrator") return true;
	
	$user = get_userdata($uid);
	
	if($user->user_level == 10) return true;
	return false;	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
	
/*function ProjectTheme_is_user_business($uid)
{
	if(!ProjectTheme_2_user_types()) return true;
	
	//----------------------
	
		$can_do_both = get_user_meta($uid, 'can_do_both', true);
		if($can_do_both == "yes") return true;
	
	//----------------------
	
	$user_tp = get_user_meta($uid, 'user_tp', true);
	if($user_tp != "service_provider") return true;
	
	$user = get_userdata($uid);
	
	if($user->user_level == 10) return true;
	return false;
	
 
} */

function ProjectTheme_is_user_business($uid)
{
	if(!ProjectTheme_2_user_types()) return true;
	
	//----------------------
	
		$can_do_both = get_user_meta($uid, 'can_do_both', true);
		if($can_do_both == "yes") return true;
	
	//----------------------
	
	$user = get_userdata($uid);

	
	$user_roles = $user->roles;
	if(is_array($user_roles))
    $user_role = array_shift($user_roles);
	
	if($user_role == "business_owner") return true;
	if($user_role == "administrator") return true;
	
	if($user->user_level == 10) return true;
	return false;
	
 
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function project_isValidEmail($email){
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
	
function projectTheme_small_post()
{
			$ending = get_post_meta(get_the_ID(), 'ending', true);
			$sec 	= $ending - current_time('timestamp',0);
			$location 	= get_post_meta(get_the_ID(), 'Location', true);
			

			$price = get_post_meta(get_the_ID(), 'price', true);			
			$closed = get_post_meta(get_the_ID(), 'closed', true);
			$featured = get_post_meta(get_the_ID(), 'featured', true);
			$private_bids = get_post_meta(get_the_ID(), 'at', true);
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                
                <?php if($featured == "1"): ?>
                <div class="featured-two"></div>
                <?php endif; ?>
                
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-two"></div>
                <?php endif; ?>
                
                
                
                <div class="image_holder2">
                <a href="<?php the_permalink(); ?>"><img width="50" height="50" class="image_class" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),75,65); ?>" /></a>
                </div>
                <div  class="title_holder2" > 
                     <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
                        <?php 

                        the_title(); 

                        
                        ?></a></h2>
                        
                        <p class="mypostedon2">
                        <?php _e("Posted in",'ProjectTheme');?> <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?><br/>
                      
                      	<?php
		
			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
			if($ProjectTheme_enable_project_location == "yes"):
		
		?>
                      
                       <?php _e("Location",'ProjectTheme');?>: <?php 
					   
					   $lc = get_the_term_list( get_the_ID(), 'project_location', '', ', ', '' );
					   echo (empty($lc) ? __("not defined",'ProjectTheme') : $lc ); 
					   
					   endif;
					   
					    ?> </p>
                       
                     
                     </div></div></div> <?php	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_is_home()
{
	global $current_user, $wp_query;
	$p_action 	=  $wp_query->query_vars['p_action'];	
	
	if(!empty($p_action)) return false;
	if(is_home()) return true;
	return false;
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_replace_stuff_for_me($find, $replace, $subject)
{
	$i = 0;
	foreach($find as $item)
	{
		$replace_with = $replace[$i];
		$subject = str_replace($item, $replace_with, $subject);	
		$i++;
	}
	
	return $subject;
}	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_get_winner_bid($pid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_bids where pid='$pid' and winner='1'";
	$r = $wpdb->get_results($s);
	
	return $r[0];	
}


function projectTheme_get_bid_by_uid($pid, $uid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_bids where pid='$pid' and uid='$uid'";
	$r = $wpdb->get_results($s);
	
	return $r[0];	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


 

function ProjectTheme_my_backend_projects_orderby( $query ) {  
    if( ! is_admin() )  
        return;  
		
  	$post_type 	= $query->query_vars['post_type'];
    $orderby 	= $query->get( 'orderby');  
  	
	if($post_type == "project"):
	
  	$query->set('meta_key','ending');  
    $query->set('orderby','meta_value_num');
  
    if( 'exp' == $orderby ) {  
        $query->set('meta_key','ending');  
        $query->set('orderby','meta_value_num');  
    }
	
	if( 'feat' == $orderby ) {  
        $query->set('meta_key','featured');  
        $query->set('orderby','meta_value_num');  
    }  
	
	endif;
	
} 
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_sortable_cake_column( $columns ) {  
    $columns['exp'] 	= 'exp';  
	$columns['feat'] 	= 'feat';    
    return $columns;  
} 
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_my_projects_columns($columns) //this function display the columns headings
{
	$columns = array(
		"cb" 		=> "<input type=\"checkbox\" />",
		"title" 	=> __("Project Title","ProjectTheme"),
		"author" 	=> __("Author","ProjectTheme"),
		"posted" 	=> __("Posted On","ProjectTheme"),
		"price"		=> __("Price","ProjectTheme"),
		"exp" 		=> __("Expires in","ProjectTheme"),
		"feat" 		=> __("Featured","ProjectTheme"),
		"approveds" 		=> __("Approved","ProjectTheme"),
		"thumbnail" => __("Thumbnail","ProjectTheme"),
		"options" 	=> __("Options","ProjectTheme")
	);
	return $columns;
} 
	


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
	
function ProjectTheme_my_custom_columns($column)
{
	global $post;
	if ("ID" == $column) echo $post->ID; //displays title
	elseif ("description" == $column) echo $post->ID; //displays the content excerpt
	elseif ("posted" == $column) echo date_i18n('jS \of F, Y \<\b\r\/\>H:i:s',strtotime($post->post_date)); //displays the content excerpt
	elseif ("thumbnail" == $column) 
	{
		echo '<a href="'.get_bloginfo('siteurl').'/wp-admin/post.php?post='.$post->ID.'&action=edit"><img class="image_class" 
	src="'.ProjectTheme_get_first_post_image($post->ID,75,65).'" width="75" height="65" /></a>'; //shows up our post thumbnail that we previously created.
	}
	
	elseif ("author" == $column)
	{
		echo $post->post_author;	
	}
	
	elseif ("approveds" == $column)
	{
		$paid = get_post_meta($post->ID, 'paid', true);
		
		if($post->post_status == "draft") echo "No";
		else echo "Yes";	
	}	
	
	elseif ("feat" == $column)
	{
		$f = get_post_meta($post->ID,'featured', true);	
		if($f == "1") echo __("Yes","ProjectTheme");
		else  echo __("No","ProjectTheme");
	}
	
	elseif ("price" == $column)
	{	
		echo ProjectTheme_get_budget_name_string_fromID(get_post_meta($post->ID,'budgets',true));
	}
	
	elseif ("exp" == $column)
	{
		$ending = get_post_meta($post->ID, 'ending', true);		
		echo ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0));	
	}
	
	elseif ("options" == $column)
	{
		echo '<div style="padding-top:20px">';
		echo '<a class="awesome" href="'.get_bloginfo('siteurl').'/wp-admin/post.php?post='.$post->ID.'&action=edit">Edit</a> ';	
		echo '<a class="awesome" href="'.get_permalink($post->ID).'" target="_blank">View</a> ';
		echo '<a class="trash" href="'.get_delete_post_link($post->ID).'">Trash</a> ';
		echo '</div>';
	}
	
}	
	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
	
function ProjecTheme_get_budgets_dropdown($selected = '', $class = '' , $rui = 0)
{
		$ech = '<select name="budgets" class="'.$class.'">';
	
		global $wpdb;
		$s = "select * from ".$wpdb->prefix."project_bidding_intervals order by low_limit asc";
		$r = $wpdb->get_results($s);
		
		if($rui == 1) $ech .= '<option value="">'.__('Select','ProjectTheme').'</option>';
		
		foreach($r as $row)
		{
			//BillyB removed value of budget
			
			$nm = ProjectTheme_get_budget_name_string($row);	
			$ech .= '<option value="'.$row->id.'" '.($row->id == $selected ? 'selected="selected"' : '').'>'.$nm.'</option>';	
			
		}
	
	return $ech.'</select>';
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_set_metaboxes()
{
	
	    add_meta_box( 'project_custom_fields', 	'Project Custom Fields',	'projectTheme_custom_fields_html', 'project', 'advanced','high' );
		//BillyB hid images add_meta_box( 'project_images', 	'Project Images',	'projectTheme_theme_project_images', 	'project', 'advanced',	'high' );
		add_meta_box( 'project_files', 		'Project Files',	'projectTheme_theme_project_files', 	'project', 'advanced',	'high' );
		add_meta_box( 'project_bids', 		'Project Bids',		'projectTheme_theme_project_bids', 		'project', 'advanced',	'high' );
		add_meta_box( 'project_dets', 		'Project Details',	'projectTheme_theme_project_dts', 		'project', 'side',		'high' );


	
}


function projectTheme_custom_fields_html()
{
	global $post, $wpdb;
	$pid = $post->ID;
	?>
    <table width="100%">
    <input type="hidden" value="1" name="fromadmin" />
	<?php
		$cat 		  	= wp_get_object_terms($pid, 'project_cat');
		$catidarr 		= $cat[0]->term_id;
	
	
		$arr 	= ProjectTheme_get_project_category_fields($catidarr, $pid);
		
		for($i=0;$i<count($arr);$i++)
		{
			
			        echo '<tr> <input type="hidden" value="'.$arr[$i]['id'].'" name="cust_ids[]" />';
					echo '<td>'.$arr[$i]['field_name'].$arr[$i]['id'].':</td>';
					echo '<td>'.$arr[$i]['value'];
					do_action('ProjectTheme_step3_after_custom_field_'.$arr[$i]['id'].'_field');
					echo '</td>';

					echo '</tr>';
			
			
		}	
	
	?> 
    
    
    </table>
    <?php	
	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_theme_project_dts()
{
	global $post;
	$pid = $post->ID;
	$price = get_post_meta($pid, "price", true);
	$location = get_post_meta($pid, "Location", true);
	$f = get_post_meta($pid, "featured", true);
	$t = get_post_meta($pid, "closed", true);
	$hide_project = get_post_meta($pid, "hide_project", true);
	
	
	?>
    
    <ul id="post-new4"> 
    <input name="fromadmin" type="hidden" value="1" />
  
        <?php 
			
			do_action('ProjectTheme_project_dts_form', $pid);
			
			$filter_price = true;
			$filter_price = apply_filters("ProjectTheme_filter_price_field_admin", $filter_price);
		
			if($filter_price == true):
		
		?>
        <li>
        	<h2><?php echo __('Price','ProjectTheme'); ?>:</h2>
        <p>
        
        <?php
	  
		  $sel = get_post_meta($pid, 'budgets', true);
		  echo ProjecTheme_get_budgets_dropdown($sel, 'do_input');
	  
	  ?>
        
        </p>
        </li>
        
       <?php endif; ?>
    
    	<li>
        	<h2><?php echo __('Sealed Bids','ProjectTheme'); ?>:</h2>
        <p><select name="private_bids">
        <option value="0" <?php if(get_post_meta($pid,'private_bids',true) == "0") echo 'selected="selected"'; ?>><?php _e("No",'ProjectTheme'); ?></option>
        <option value="1" <?php if(get_post_meta($pid,'private_bids',true) == "1") echo 'selected="selected"'; ?>><?php _e("Yes",'ProjectTheme'); ?></option>
        
        </select>
        </p>
        </li>
    
     	<li>
        <h2><?php _e("Feature this project",'ProjectTheme');?>:</h2>
        <p><input type="checkbox" value="1" name="featureds" <?php if($f == '1') echo ' checked="checked" '; ?> /></p>
        </li>
        
        
        <li>
        <h2><?php _e("Hide this project",'ProjectTheme');?>:</h2>
        <p><input type="checkbox" value="1" name="hide_project" <?php if($hide_project == '1') echo ' checked="checked" '; ?> /></p>
        </li>
        
        
        <li>
        <h2><?php _e("Closed",'ProjectTheme');?>:</h2>
        <p><input type="checkbox" value="1" name="closed" <?php if($t == '1') echo ' checked="checked" '; ?> /></p>
        </li>
        
        
        <li>
        <h2><?php _e("Address",'ProjectTheme');?>:</h2>
        <p><input type="text" value="<?php echo get_post_meta($pid,'Location',true); ?>" name="Location" /></p>
        </li>
        
        
        
        
        <li>
        <h2>
        
     
 
        
        
        <link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui-thing.css" />
		<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/jquery-ui-timepicker-addon.js"></script>
          
          
          
          
       <?php _e("Project Ending On",'ProjectTheme'); ?>:</h2>
        <p><input type="text" name="ending" id="ending" value="<?php
		
		$d = get_post_meta($pid,'ending',true);
		
		if(!empty($d)) {
		$r = date_i18n('m/d/Y H:i:s', $d);
		echo $r;
		}
		 ?>" class="do_input"  /></p>
        </li>
        
 <script>

jQuery(document).ready(function() {
	 jQuery('#ending').datetimepicker({
	showSecond: true,
	timeFormat: 'hh:mm:ss'
});});
 
 </script>
        
        
	</ul>    

	
	<?php
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_get_highest_bid($pid)
{
	global $wpdb;
	$s = "select bid from ".$wpdb->prefix."project_bids where pid='$pid' order by bid desc limit 1";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0)
	{
		$start_price = get_post_meta($pid, 'start_price', true);
		if(empty($start_price)) return false;
		return $start_price;	
		
	}
	
	
	$r = $r[0];
	return $r->bid;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
function projectTheme_get_highest_bid_owner($pid)
{
	global $wpdb;
	$s = "select bid from ".$wpdb->prefix."project_bids where pid='$pid' order by bid desc limit 1";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0)
	 return false;
	
	$r = $r[0];
	return $r->uid;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_bid_values($pid)
{
	global $wpdb;
	$s = "select bid from ".$wpdb->prefix."project_bids where pid='$pid' order by bid desc";
	$r = $wpdb->get_results($s);
	
	return $r;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
function projectTheme_get_current_price($pid = '')
{
	if(empty($pid)) $pid = get_the_ID(); 
	$only_buy_now = get_post_meta($pid, 'only_buy_now' ,true);
	
	if($only_buy_now == '1') return get_post_meta($pid, 'buy_now', true);
	
	$reverse = get_post_meta($pid, "reverse", true);
	if($reverse == "yes") return get_post_meta($pid, 'price', true);
	else
	{
		$bids = projectTheme_get_bid_values($pid);
		
		if(count($bids) == 0) 
		{ 
			$start = projectTheme_get_start_price($pid); 
			return ($start == false ? 0 : $start );  
		}
		else
		{
			return projectTheme_get_highest_bid($pid);			
		}
		
	}
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_get_start_price($pid = '')
{
	if(empty($pid)) $pid = get_the_ID();
	$price = get_post_meta($pid, 'start_price', true);
	
	if(empty($price)) $price = false;
	return $price;
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

add_filter('post_type_link', 'ProjectTheme_post_type_link_filter_function', 1, 3);

 function ProjectTheme_post_type_link_filter_function( $post_link, $id = 0, $leavename = FALSE ) {
	 
	global $category_url_link;
	 
    if ( strpos('%project_cat%', $post_link) === 'FALSE' ) {
      return $post_link;
    }
    $post = get_post($id);
    if ( !is_object($post) || $post->post_type != 'project' ) {
      return str_replace("project_cat", $category_url_link ,$post_link);
    }
    $terms = wp_get_object_terms($post->ID, 'project_cat');
    if ( !$terms ) {
      return str_replace('%project_cat%', 'uncategorized', $post_link);
    }
    return str_replace('%project_cat%', $terms[0]->slug, $post_link);
  }

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
function projectTheme_theme_project_files()
{
	global $current_user;
	get_currentuserinfo();
	$cid = $current_user->ID;
	
	global $post;
	$pid = $post->ID;
 
?>


 <div class="cross_cross">



	<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/dropzone.js"></script>     
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/dropzone.css" type="text/css" />
    
 
    
    
    <script>
 
	
	jQuery(function() {

Dropzone.autoDiscover = false; 	 
var myDropzoneOptions = {
  maxFilesize: 15,
    addRemoveLinks: true,
	acceptedFiles:'.zip,.pdf,.rar,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.psd,.ai',
    clickable: true,
	url: "<?php bloginfo('siteurl') ?>/?my_upload_of_project_files_proj=1",
};
 
var myDropzone = new Dropzone('div#myDropzoneElement', myDropzoneOptions);

myDropzone.on("sending", function(file, xhr, formData) {
  formData.append("author", "<?php echo $cid; ?>"); // Will send the filesize along with the file as POST data.
  formData.append("ID", "<?php echo $pid; ?>"); // Will send the filesize along with the file as POST data.
});

   
    <?php

		$args = array(
	'order'          => 'ASC',
	'orderby'        => 'menu_order',
	'post_type'      => 'attachment',
	'meta_key' 		=> 'is_prj_file',
	'meta_value' 	=> '1',	
	'post_parent'    => $pid,
	'post_status'    => null,
	'numberposts'    => -1,
	);
	$attachments = get_posts($args);
	
	if($pid > 0)
	if ($attachments) {
	    foreach ($attachments as $attachment) {
		$url = $attachment->guid;
		$imggg = $attachment->post_mime_type; 
		
		if('image/png' != $imggg && 'image/jpeg' != $imggg)
		{
		$url = wp_get_attachment_url($attachment->ID);
 
			
			?>

					var mockFile = { name: "<?php echo $attachment->post_title ?>", size: 12345, serverId: '<?php echo $attachment->ID ?>' };
					myDropzone.options.addedfile.call(myDropzone, mockFile);
					myDropzone.options.thumbnail.call(myDropzone, mockFile, "<?php echo bloginfo('template_url') ?>/images/file_icon.png");
					 
			
			<?php
			
	  
	}
	}}


	?>
   


myDropzone.on("success", function(file, response) {
    /* Maybe display some more file information on your page */
	 file.serverId = response;
	 file.thumbnail = "<?php echo bloginfo('template_url') ?>/images/file_icon.png";
	 
	   
  });
  
  
myDropzone.on("removedfile", function(file, response) {
    /* Maybe display some more file information on your page */
	  delete_this2(file.serverId);
	 
  });  	
	
	});
	
	</script>

    <script type="text/javascript">
	
	function delete_this2(id)
	{
		 jQuery.ajax({
						method: 'get',
						url : '<?php echo get_bloginfo('siteurl');?>/index.php/?_ad_delete_pid='+id,
						dataType : 'text',
						success: function (text) {   jQuery('#image_ss'+id).remove();  }
					 });
		  //alert("a");
	
	}

	
 
	
	
	</script>

	<?php _e('Click the grey area below to add project files. Images are not accepted.','ProjectTheme') ?>
    <div class="dropzone dropzone-previews" id="myDropzoneElement" ></div>
 
    
	</div>


<?php
	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
function projectTheme_theme_project_bids()
{
	global $post;
	$pid = $post->ID;
	global $wpdb;
	
	//------------------------------------------------
	if(isset($_GET['remove_winner']))
	{
		echo 'Are you sure you want to remove the winner? | <b><a href="'.get_admin_url().'post.php?post='.$pid.'&action=edit&accepted_remove=1#project_bids">Yes, I Am Sure</a></b>'; echo '<br/>';	
		
		
		echo '<br/>';
		echo '<br/>';
	}
	
	if(isset($_GET['accepted_remove']))
	{
		echo '<div class="saved_thing">Winner for project removed.</div>';
		
		$bids = "update ".$wpdb->prefix."project_bids set winner='0' where pid='$pid' and winner='1'";
		$wpdb->query($bids);
		
		$bids = "delete from ".$wpdb->prefix."project_ratings where pid='$pid'";
		$wpdb->query($bids);
		
		delete_post_meta($pid, 'winner');
		delete_post_meta($pid, 'outstanding');
		delete_post_meta($pid, 'expected_delivery');
		delete_post_meta($pid, 'mark_seller_accepted');
		delete_post_meta($pid, 'mark_coder_delivered');
		
		delete_post_meta($pid, 'paid_user');
		delete_post_meta($pid, 'mark_seller_accepted_date');
		delete_post_meta($pid, 'mark_coder_delivered_date');
		
		echo '<br/>';
		echo '<br/>';	
	}
	
	//-----------------------------------------------
				
				$closed = get_post_meta($pid, 'closed', true);
				$post = get_post($pid);
				
				
				$bids = "select * from ".$wpdb->prefix."project_bids where pid='$pid' order by id DESC";
				$res  = $wpdb->get_results($bids);

				if(count($res) > 0)
				{
	
						echo '<table width="100%">';
						echo '<thead><tr>';
							echo '<th>'.__('Username','ProjectTheme').'</th>';
							echo '<th>'.__('Bid Amount','ProjectTheme').'</th>';
							echo '<th>'.__('Date Made','ProjectTheme').'</th>';
							
							echo '<th>'.__('Winner','ProjectTheme').'</th>';
							echo '<th>'.__('Options','ProjectTheme').'</th>';
							
						echo '</tr></thead><tbody>';

					//-------------
					
					foreach($res as $row)
					{
	
						$user = get_userdata($row->uid);
						echo '<tr>';
						echo '<th>'.$user->user_login.'</th>';
						echo '<th>'.ProjectTheme_get_show_price($row->bid).'</th>';
						echo '<th>'.date_i18n("d-M-Y H:i:s", $row->date_made).'</th>';
						
						
					if($row->winner == 1) echo '<th>'.__('Yes','ProjectTheme').'</th>'; else echo '<th>&nbsp;</th>'; 
					if($row->winner == 1) echo '<th><a href="'.get_admin_url().'post.php?post='.$pid.'&action=edit&remove_winner=1#project_bids">'.__('Remove Winner','ProjectTheme').'</a></th>'; else echo '<th>&nbsp;</th>'; 
						
						echo '</tr>';
						
					}
					
					echo '</tbody></table>';
				}
				else _e("No bids placed yet.",'ProjectTheme');
 	
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
function projectTheme_theme_project_images()
{
	global $current_user;
	get_currentuserinfo();
	$cid = $current_user->ID;
	
	global $post;
	$pid = $post->ID;
	
	//BillyB hiding images
?>
<!--
     <div class="cross_cross">



	<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/dropzone.js"></script>     
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/dropzone.css" type="text/css" />
    
 
    
    
    <script>
 
	
	jQuery(function() {

Dropzone.autoDiscover = false; 	 
var myDropzoneOptions = {
  maxFilesize: 15,
    addRemoveLinks: true,
	acceptedFiles:'image/*',
    clickable: true,
	url: "<?php bloginfo('siteurl') ?>/?my_upload_of_project_files2=1",
};
 
var myDropzone = new Dropzone('div#myDropzoneElement2', myDropzoneOptions);

myDropzone.on("sending", function(file, xhr, formData) {
  formData.append("author", "<?php echo $cid; ?>"); // Will send the filesize along with the file as POST data.
  formData.append("ID", "<?php echo $pid; ?>"); // Will send the filesize along with the file as POST data.
});

   
    <?php

		$args = array(
	'order'          => 'ASC',
	'orderby'        => 'menu_order',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
	'post_status'    => null,
	'post_mime_type' => 'image',
	'numberposts'    => -1,
	);
	$attachments = get_posts($args);
	
	if($pid > 0)
	if ($attachments) 
	{
	    foreach ($attachments as $attachment) 
		{
			$url = $attachment->guid;
			$imggg = $attachment->post_mime_type; 
			$url = wp_get_attachment_url($attachment->ID);	 
				
				?>	
						var mockFile = { name: "<?php echo $attachment->post_title ?>", size: 12345, serverId: '<?php echo $attachment->ID ?>' };
						myDropzone.options.addedfile.call(myDropzone, mockFile);
						myDropzone.options.thumbnail.call(myDropzone, mockFile, "<?php echo projectTheme_generate_thumb($attachment->ID, 100, 100) ?>");						 
				
				<?php			
	 	}
	}

	?>
 
	myDropzone.on("success", function(file, response) {
    /* Maybe display some more file information on your page */
	 file.serverId = response;
	 file.thumbnail = "<?php echo bloginfo('template_url') ?>/images/file_icon.png";
	 
	   
  });
  
  
myDropzone.on("removedfile", function(file, response) {
    /* Maybe display some more file information on your page */
	  delete_this2(file.serverId);
	 
  });  	
	
	});
	
	</script>

    

	<?php _e('Click the grey area below to add project images. Other files are not accepted. Use the form below.','ProjectTheme') ?>
    <div class="dropzone dropzone-previews" id="myDropzoneElement2" ></div>
 
    
	</div>-->

<?php	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_check_list_emails($termid, $row)
{
	if(count($row) > 0)
	foreach($row as $term)
	{
		if($term->catid == $termid) return 1;	
	}
	return 0;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


function ProjectTheme_add_query_vars($public_query_vars) 
{  
    	$public_query_vars[] = 'p_action'; 
		$public_query_vars[] = 'orderid'; 
		$public_query_vars[] = 'step';
		$public_query_vars[] = 'pg'; 
		$public_query_vars[] = 'my_second_page';
		$public_query_vars[] = 'third_page';
		$public_query_vars[] = 'username';
		$public_query_vars[] = 'pid';
		$public_query_vars[] = 'bid';
		$public_query_vars[] = 'rid';
		$public_query_vars[] = 'term_search';		//job_sort, job_category, page
		$public_query_vars[] = 'method';
		$public_query_vars[] = 'post_new_step';
		$public_query_vars[] = 'projectid';
		$public_query_vars[] = 'page';
		$public_query_vars[] = 'p_action';
		$public_query_vars[] = 'post_author';
		
    	return $public_query_vars;  
}

	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_create_post_type() {
  
	global $projects_url_nm;
  	$icn = get_bloginfo('template_url')."/images/proj_icon.png";
  	
	register_post_type( 'project',
    array(
      'labels' => array(
        'name' 			=> __( 'Projects',			'ProjectTheme' ),
        'singular_name' => __( 'Project',			'ProjectTheme' ),
		'add_new' 		=> __('Add New Project',	'ProjectTheme'),
		'new_item' 		=> __('New Project',		'ProjectTheme'),
		'edit_item'		=> __('Edit Project',		'ProjectTheme'),
		'add_new_item' 	=> __('Add New Project',	'ProjectTheme'),
		'search_items' 	=> __('Search Projects',	'ProjectTheme'),
		
		
      ),
      'public' => true,
	   'has_archive' => 'project-list',
	  'menu_position' => 5,
	  'register_meta_box_cb' => 'projectTheme_set_metaboxes',
	  'has_archive' => "project-list",
    	'rewrite' => array('slug'=> $projects_url_nm."/%project_cat%",'with_front'=>false), 
		'supports' => array('title','editor','author','thumbnail','excerpt','comments'),
	  '_builtin' => false,
	  'menu_icon' => $icn,
	  'publicly_queryable' => true,
	  'hierarchical' => false 

    )
  );

	global $category_url_link, $location_url_link;
	
	$lbs = array('add_new_item' => __('Add New Location','ProjectTheme'),
	'search_items' => __('Search Locations','ProjectTheme'));
	
	register_taxonomy( 'project_cat', 'project', array( 'rewrite' => true ,'hierarchical' => true,   'label' => __('Project Categories','ProjectTheme') ) );
	register_taxonomy(   'project_location', 'project', array('rewrite' => array('slug'=>$location_url_link,'with_front'=>false),
	 'hierarchical' => true, 'labels' => $lbs, 'label' => __('Locations','ProjectTheme') ) );
	 
	 register_taxonomy( 'project_skill', 'project', array( 'rewrite' => true ,'hierarchical' => true,   'label' => __('Skills','ProjectTheme') ) );
	 
	add_post_type_support( 'project', 'author' );
//	 add_post_type_support( 'project', 'custom-fields' );
	register_taxonomy_for_object_type('post_tag', 'project');
	
	flush_rewrite_rules();
	
	//-------------------------
	//user roles
	
	
	add_role('service_provider', __('Service Provider','ProjectTheme'), array(
    'read' => true, // True allows that capability
    'edit_posts' => false,
    'delete_posts' => false));
	
	add_role('business_owner', __('Service Contractor','ProjectTheme'), array(
    'read' => true, // True allows that capability
    'edit_posts' => false,
    'delete_posts' => false));  

	$role = get_role( 'service_provider' );
  	$role->remove_cap( 'delete_posts' );
	$role->remove_cap( 'edit_posts' );
	$role->remove_cap( 'delete_published_posts' );
	
	
	$role = get_role( 'business_owner' );
  	$role->remove_cap( 'delete_posts' );
	$role->remove_cap( 'edit_posts' );
	$role->remove_cap( 'delete_published_posts' );
 
}


function projectTheme_build_my_cat_arr2($pid)
{
	$my_arr 	= array();
	$cat 		= wp_get_object_terms($pid, 'project_skill');
	
	if(is_array($cat))
	foreach($cat as $c)
	{
		$my_arr[] = $c->term_id;	
	}
	

	return $my_arr;
}


function ProjectTheme_get_categories_multiple2($taxo, $selected_arr = "")
{
	$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
	$terms = get_terms( $taxo, $args );
	
 
	foreach ( $terms as $term )
	{
		$id = $term->term_id;
		
		$ret .= '<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $id) == true ? "checked='checked'" : " " ).' value="'.$id.'" name="'.$taxo.'_cat_multi[]"> <strong>'.$term->name.'</strong><br/>';
		
		$args = "orderby=name&order=ASC&hide_empty=0&parent=".$id;
		$sub_terms = get_terms( $taxo, $args );	
		
		if($sub_terms)
		foreach ( $sub_terms as $sub_term )
		{
			$sub_id = $sub_term->term_id; 
			$ret .= '&nbsp; &nbsp; &nbsp; 
			<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id) == true ? "checked='checked'" : " " ).' value="'.$sub_id.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term->name.'<br/>';
 
			
			$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id;
			$sub_terms2 = get_terms( $taxo, $args2 );	
			
			if($sub_terms2)
			foreach ( $sub_terms2 as $sub_term2 )
			{
				$sub_id2 = $sub_term2->term_id; 
				$ret .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
				<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id2) == true ? "checked='checked'" : " " ).' value="'.$sub_id2.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term2->name.'<br/>';
 
			 
				 
				 $args3 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id2;
				 $sub_terms3 = get_terms( $taxo, $args3 );	
				 
				 if($sub_terms3)
				 foreach ( $sub_terms3 as $sub_term3 )
				{
					$sub_id3 = $sub_term3->term_id; 
					 
					$ret .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
					<input type="checkbox" '.(ProjectTheme_is_selected_thing($selected_arr, $sub_id3) == true ? "checked='checked'" : " " ).' value="'.$sub_id2.'" name="'.$taxo.'_cat_multi[]"> '.$sub_term3->name.'<br/>';
  
					 
				}
			}
		}
		
	}
	
	
	return $ret;
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_theme_get_current_site()
{
	$get_current_site = get_current_site();
	if(empty($get_current_site->id)) return 1;
	
	return $get_current_site->id;	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
	
function ProjectTheme_get_avatar($uid, $w = 25, $h = 25)
{
	$av = get_user_meta($uid, 'avatar_' . 'project', true);
	if(empty($av)) return get_bloginfo('template_url')."/images/noav.jpg";
	else return ProjectTheme_generate_thumb($av, $w, $h);
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_post_nr_of_images($pid)
{
	
		//---------------------
		// build the exclude list
		$exclude = array();
		
		$args = array(
		'order'          => 'ASC',
		'post_type'      => 'attachment',
		'post_parent'    => get_the_ID(),
		'meta_key'		 => 'another_reserved1',
		'meta_value'	 => '1',
		'numberposts'    => -1,
		'post_status'    => null,
		);
		$attachments = get_posts($args);
		if ($attachments) {
			foreach ($attachments as $attachment) {
			$url = $attachment->ID;
			array_push($exclude, $url);
		}
		}
		
		//-----------------
	
	
		$arr = array();
		
		$args = array(
		'order'          => 'ASC',
		'orderby'        => 'post_date',
		'post_type'      => 'attachment',
		'post_parent'    => $pid,
		'exclude'    		=> $exclude,
		'post_mime_type' => 'image',
		'numberposts'    => -1,
		); $i = 0;

		
		$attachments = get_posts($args); 
		if ($attachments) {
		
			foreach ($attachments as $attachment) {
						
				$url = wp_get_attachment_url($attachment->ID);
				array_push($arr, $url);
			  
		}
			return count($arr);
		}
		return 0;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_post_images($pid, $limit = -1)
{
	
		//---------------------
		// build the exclude list
		$exclude = array();
		
		$args = array(
		'order'          => 'ASC',
		'post_type'      => 'attachment',
		'post_parent'    => $pid,
		'meta_key'		 => 'another_reserved1',
		'meta_value'	 => '1',
		'numberposts'    => -1,
		'post_status'    => null,
		);
		$attachments = get_posts($args);
		if ($attachments) {
			foreach ($attachments as $attachment) {
			$url = $attachment->ID;
			array_push($exclude, $attachment->ID);
		}
		}
		
		//-----------------
	
	
		$arr = array();
		
		$args = array(
		'order'          => 'ASC',
		'orderby'        => 'post_date',
		'post_type'      => 'attachment',
		'post_parent'    => $pid,
		'exclude'    		=> $exclude,
		'post_mime_type' => 'image',
		'numberposts'    => $limit,
		); $i = 0;
		
		$attachments = get_posts($args); 
		if ($attachments) {
		
			foreach ($attachments as $attachment) {
						
				$url =  ($attachment->ID);
				array_push($arr, $url);
			  
		}
			return $arr;
		}
		return false;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_post_new_link()
{
	return get_permalink(get_option('ProjectTheme_post_new_page_id'));	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_blog_link()
{
	return get_permalink(get_option('ProjectTheme_all_blog_posts_page_id'));
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_my_account_link()
{
	return get_permalink(get_option('ProjectTheme_my_account_page_id'));	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function

*
**************************************************************/

function projectTheme_advanced_search_link_pgs($pg)
{
	$opt = get_option('ProjectTheme_advanced_search_page_id');
	$perm = ProjectTheme_using_permalinks();
	
	$acc = 'pj='.$pg."&";
	foreach($_GET as $key=>$value)
	{
		if($key != 'pj' and $key != 'page_id')
		$acc .= $key."=".$value."&";	
	}
	
	if($perm) return get_permalink($opt). "?" . $acc;
	
	return get_permalink($opt). "&".$acc;
}


function projectTheme_advanced_search_link2()
{
	$opt = get_option('ProjectTheme_advanced_search_page_id');
	$perm = ProjectTheme_using_permalinks();
	
	if($perm) return get_permalink($opt). "?";
	
	return get_permalink($opt). "&pg=".$subpage."&";
}


function projectTheme_get_the_search_box($ff = '')
{
	?>
    <!--BillyB Hide Search
    <div class="search-ttl-form <?php echo $ff ?>">
    <form method="post" action="<?php bloginfo('siteurl') ?>/?redirect_search=1">
    <input type="hidden" value="projects" name="redirect_search" id="redirect_search" />
        	
			<div class="search-ttl-form-inner">
            	
                <div class="drpdown-wrapper">
                   <div id="dd" class="wrapper-dropdown-3" tabindex="1">
						<span><?php _e('Projects','ProjectTheme') ?></span>
						Hide ability to search for members
						<ul class="dropdown">
							
							<li><a href="#" rel="freelancers"><?php _e('Members','ProjectTheme') ?></a></li>
                            <li><a href="#" rel="projects"><?php _e('Projects','ProjectTheme') ?></a></li>
						</ul>
					</div>
                </div>
            
            	<div class="input_text_serch-wrapper">
            	<input type="text" placeholder="<?php _e('','ProjectTheme') ?>" id="my-top-search-input"	name="input_text_serch" />
                </div>
                
                
                <div class="input_submit_serch-wrapper">
            	<input type="submit" id="my-top-submit-input" value=" " name="input_submit" />
                </div>
                
            </div>
           </form> 
        </div>-->
    
    
    <?php	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_provider_search_link()
{
	$opt = get_option('ProjectTheme_provider_search_page_id');
	$perm = ProjectTheme_using_permalinks();
	
	if($perm) return get_permalink($opt). "?";
	
	return get_permalink($opt). "&pg=".$subpage."&";
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_advanced_search_link()
{
	$opt = get_option('ProjectTheme_advanced_search_page_id');	
	return get_permalink($opt);
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_add_js_coin_slider()
{
	if(ProjectTheme_is_home()):
	
	$opt = get_option('ProjectTheme_slider_in_front');
	if($opt == "yes") :
	
?>	
	<script type="text/javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/easySlider1.7.js"></script>
			<script type="text/javascript">

	var $ = jQuery;

	jQuery(document).ready(function(){	
		jQuery("#slider").easySlider({
			prevText: '<?php _e('Previous',"ProjectTheme"); ?>',
			nextText: '<?php _e('Next',"ProjectTheme"); ?>',
			firstText: '<?php _e('First','ProjectTheme'); ?>',
			lastText: '<?php _e('Last','ProjectTheme'); ?>',		
			firstShow: true,
			lastShow: true,
			vertical: true,
			
			auto: false,
			controlsBefore: "<div id='slider-controls'><div class='padd10'>",
			controlsAfter: "</div></div>"
					
		});
	});	
	
	
	</script>	
	<?php endif; endif;
	
	if(!ProjectTheme_is_home()):
	?>
	<script type="text/javascript" src="<?php bloginfo('template_url') ?>/js/modernizr.custom.79639.js"></script>
    <script type="text/javascript">
			
			function DropDown(el) {
				this.dd = el;
				this.placeholder = this.dd.children('span');
				this.opts = this.dd.find('ul.dropdown > li');
				this.val = '';
				this.index = -1;
				this.initEvents();
			}
			DropDown.prototype = {
				initEvents : function() {
					var obj = this;

					obj.dd.on('click', function(event){
						$(this).toggleClass('active');
							
						return false;
					});

					obj.opts.on('click',function(){
						var opt = jQuery(this); 
					 
						var mms = opt.children().attr("rel");
						obj.val = opt.text();
						obj.index = opt.index();
						obj.placeholder.text(obj.val);
						jQuery("#redirect_search").val(mms);
						 
					});
				},
				getValue : function() {
					return this.val;
				},
				getIndex : function() {
					return this.index;
				}
			}

			jQuery(function() {

				var dd = new DropDown( jQuery('#dd') );

				jQuery(document).click(function() {
					// all dropdowns
					jQuery('.wrapper-dropdown-3').removeClass('active');
				});

			});

		</script>
    
    <?php
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_post_blog()
{
	do_action('ProjectTheme_get_post_blog_function');	
}


function ProjectTheme_get_post_blog_function()
{
			
						 $arrImages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . get_the_ID());
						 
						 if($arrImages) 
						 {
							$arrKeys 	= array_keys($arrImages);
							$iNum 		= $arrKeys[0];
					        $sThumbUrl 	= wp_get_attachment_thumb_url($iNum);
					        $sImgString = '<a href="' . get_permalink() . '">' .
	                          '<img class="image_class" src="' . $sThumbUrl . '" width="42" height="42" />' .
                      		'</a>';
							 							 
						 }
						 else
						 {
								$sImgString = '<a href="' . get_permalink() . '">' .
	                          '<img class="image_class" src="' . get_bloginfo('template_url') . '/images/nopic.jpg" width="42" height="42" />' .
                      			'</a>'; 
							 
						 }
					
			
?>
				<div class="post vc_POST blg_pst" id="post-<?php the_ID(); ?>">
                
                <div class="image_holder" style="width:120px">
                <?php echo $sImgString; ?>
                </div>
                <div  class="title_holder" style="width:500px" > 
                     <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
                        <?php the_title(); ?></a></h2>
                        <p class="mypostedon"><?php _e('Posted on','ProjectTheme'); ?> <?php the_time('F jS, Y') ?>  <?php _e('by','ProjectTheme'); ?> 
                       <?php the_author() ?>
                  </p>
                       <p class="blog_post_preview"> <?php the_excerpt(); ?></p>
                       
                      
                        <a href="<?php the_permalink() ?>" class="post_bid_btn"><?php _e('Read More','ProjectTheme'); ?></a>
                         
                     </div> 
                     
                   
                     
                     </div>
<?php
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

add_filter('ProjectTheme_get_slider_home', 'ProjectTheme_get_home_slider_fnc');

function ProjectTheme_get_slider_home()
{
	do_action('ProjectTheme_get_slider_home');	
}

function ProjectTheme_get_home_slider_fnc()
{
	$opt = get_option('ProjectTheme_slider_in_front');
	if($opt != "no") :	
		?>
        		<div id="project-home-page-main-inner" class="wrapper"><div class="padd10">
                <div class="slider_title"><?php _e('Featured Projects of the Day','ProjectTheme'); ?></div>
                
            	<div id="slider2">
               
			<?php
					
				 global $wpdb;	
				 $querystr = "
					SELECT distinct wposts.* 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta, $wpdb->postmeta wpostmeta2
					WHERE wposts.ID = wpostmeta.post_id AND
					wpostmeta.meta_key='closed' AND wpostmeta.meta_value='0'
					AND 
					
					wposts.ID = wpostmeta2.post_id AND
					wpostmeta2.meta_key='featured' AND wpostmeta2.meta_value='1'
					AND 
					
					wposts.post_status = 'publish' 
					AND wposts.post_type = 'project' 
					ORDER BY wposts.post_date DESC LIMIT 15 ";
				
				 $pageposts = $wpdb->get_results($querystr, OBJECT);
				 $posts_per = 5;
				 ?>
					
					 <?php $i = 0; if ($pageposts): ?>
					 <?php global $post; ?>
                     <?php foreach ($pageposts as $post): ?>
                     <?php setup_postdata($post); ?>       
                     
                     <?php 
                     
					 echo '<div class="nk_slider_child">';
					      projectTheme_slider_post();			 
					 echo '</div>';					 
                                      
                     ?>                    
                     <?php endforeach; ?>                  	
                   
                     <?php endif; ?>
	
		 </div></div> 
        </div>
        <?php endif;  
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_users_links()
{
	
		global $current_user, $wpdb;
				get_currentuserinfo();
				$rd = projectTheme_get_unread_number_messages($current_user->ID);
				if($rd > 0) $ssk = "<span class='notif_a'>".$rd."</span>"; else $ssk = '';
	

				$uid = $current_user->ID;
		
		//-----------------------
		
					$query = "select id from ".$wpdb->prefix."project_ratings where fromuser='$uid' AND awarded='0'";
					$r = $wpdb->get_results($query);
					
					$ttl_fdbks = count($r);
					
					if($ttl_fdbks > 0) 
						$ttl_fdbks2 = "<span class='notif_a'>".$ttl_fdbks."</span>";
		
		$ProjectTheme_enable_2_user_tp = get_option('ProjectTheme_enable_2_user_tp');
		$user_tp = get_user_meta($uid, 'user_tp', true);
		 
		 
	?>
    
	
		<?php	
	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_username_is_valid($u)
{
	global $wpdb;
	$s = "select ID from ".$wpdb->users." where user_login='$u'";
	$r = $wpdb->get_results($s);
	
	$nr = count($r);
	
	if($nr == 0) return false;
	return true;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_my_awarded_projects2($uid)
{
	$c = "<select name='projectss'><option value=''>".__('Select','ProjectTheme')."</option>";
	global $wpdb;
	
	$querystr = "
					SELECT distinct wposts.* 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta 
					WHERE wposts.post_author='$uid' 
					AND  wposts.ID = wpostmeta.post_id 
					AND wpostmeta.meta_key = 'closed' 
					AND wpostmeta.meta_value = '1' 
					AND wposts.post_status = 'publish' 
					AND wposts.post_type = 'project'
					ORDER BY wposts.post_date DESC";
	
	//echo $querystr;
	$r = $wpdb->get_results($querystr);
	$winners_arr = array();
	
	foreach($r as $row)
	{
		$pid = $row->ID;
		$winner = get_post_meta($pid, "winner", true);
		
		
		if(!empty($winner))
		{
			
			
			if(projecttheme_check_agains_vl_vl_arr($winners_arr,$winner) == false)
			{
			
				$winners_arr[] = $winner;
				$user = get_userdata($winner);
				$c .= '<option value="'.$winner.'">'.$user->user_login.'</option>';
				$i = 1;
			}
		}
	}
	
	
	//-------------------------------
	
	if($i == 1)
	return $c.'</select>';
	
	return false;
}

function projecttheme_check_agains_vl_vl_arr($winners_arr,$winner)
{
	foreach($winners_arr as $as)
	{
		if($winner == $as) return true;	
	}
	
	return false;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_add_history_log($tp, $reason, $amount, $uid, $uid2 = '')
{
	if($amount != 0)
	{
		$tm = current_time('timestamp',0); global $wpdb;
		$s = "insert into ".$wpdb->prefix."project_payment_transactions (tp,reason,amount,uid,datemade,uid2)
		values('$tp','$reason','$amount','$uid','$tm','$uid2')";	
		$wpdb->query($s);
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

add_filter('upload_mimes', 'projectTheme_custom_upload_mimes');

function projectTheme_custom_upload_mimes ( $existing_mimes=array() ) {
 

$existing_mimes['zip'] = 'application/zip'; 
$existing_mimes['pdf'] = 'application/pdf'; 
$existing_mimes['doc'] = 'application/doc'; 
$existing_mimes['docx'] = 'application/docx'; 
$existing_mimes['xls'] = 'application/xls';
$existing_mimes['xlsx'] = 'application/xlsx'; 
$existing_mimes['ppt'] = 'application/ppt';
$existing_mimes['pptx'] = 'application/pptx'; 
$existing_mimes['csv'] = 'application/csv';
$existing_mimes['psd'] = 'application/octet-stream'; 
$existing_mimes['png'] = 'image/png'; 

return $existing_mimes;

 
}

function projectTheme_get_userid_from_username($user)
{
	//$user = get_user_by('login', $user);
	global $wpdb; $user = trim($user);
	
	$usrs = $wpdb->users;
	
	$s = "select * from ".$usrs." where user_login='$user'";
	$r = $wpdb->get_results($s);
	$row = $r[0];

	//if(empty($row->ID)) return false;
	
	return $row->ID;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_currency()
{
	$c = trim(get_option('ProjectTheme_currency_symbol'));
	if(empty($c)) return get_option('ProjectTheme_currency');
	return $c;	
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_currency()
{
	$c = trim(get_option('ProjectTheme_currency_symbol'));
	if(empty($c)) return get_option('ProjectTheme_currency');
	return $c;	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_my_awarded_projects($uid)
{
	$c = "<select name='projectss' onchange='on_proj_sel();' id='my_proj_sel'><option value='0'>".__('Select','ProjectTheme')."</option>";
	global $wpdb;
	
	$querystr = "
					SELECT distinct wposts.* 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta 
					WHERE wposts.post_author='$uid' 
					AND  wposts.ID = wpostmeta.post_id 
					AND wpostmeta.meta_key = 'closed' 
					AND wpostmeta.meta_value = '1' 
					AND wposts.post_status = 'publish' 
					AND wposts.post_type = 'project'
					ORDER BY wposts.post_date DESC";
	
	//echo $querystr;
	$r = $wpdb->get_results($querystr);
	
	foreach($r as $row)
	{
		$pid = $row->ID;
		$winner = get_post_meta($pid, "winner", true);
		
		
		if(!empty($winner))
		{
			$c .= '<option value="'.$row->ID.'" '.($row->ID == $_GET['poid'] ? 'selected="selected"' : '').'>'.$row->post_title.'</option>';
			$i = 1;
		}
	}
	
	//----------------------------
	
					 $querystr = "
					SELECT distinct wposts.* 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
					WHERE wposts.ID = wpostmeta.post_id 
					AND wpostmeta.meta_key = 'winner' 
					AND wpostmeta.meta_value = '$uid' 
					AND wposts.post_status = 'publish' 
					AND wposts.post_type = 'project'  
					ORDER BY wposts.post_date DESC ";
	
	
	
	$r = $wpdb->get_results($querystr);
	
	foreach($r as $row) // = mysql_fetch_object($r))
	{
		$pid = $row->ID;

			$c .= '<option value="'.$row->ID.'">'.$row->post_title.'</option>';
			$i = 1;
	
	}
	
	//-------------------------------
	
	if($i == 1)
	return $c.'</select>';
	
	return false;
}

function ProjectTheme_get_my_awarded_projects3($uid)
{
	$c = "<select name='projectss' id='my_proj_sel'><option value='0'>".__('Select','ProjectTheme')."</option>";
	global $wpdb;
	
	$querystr = "
					SELECT distinct wposts.* 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta  , $wpdb->postmeta wpostmeta2  
					WHERE wposts.post_author='$uid' 
					AND  wposts.ID = wpostmeta.post_id  and wposts.ID = wpostmeta2.post_id 
					AND wpostmeta.meta_key = 'closed'  AND wpostmeta2.meta_key = 'winner' 
					AND wpostmeta.meta_value = '1'  and wpostmeta2.meta_value != ' ' 
					AND wposts.post_status = 'publish' 
					AND wposts.post_type = 'project'
					ORDER BY wposts.post_date DESC";
	
	//echo $querystr;
	$r = $wpdb->get_results($querystr);
	
	foreach($r as $row)
	{
		$pid = $row->ID;
		$winner = get_post_meta($pid, "winner", true);
		
		
		if(!empty($winner))
		{
			$winner_usr = projectTheme_get_winner_bid($pid); 
			$winner_usr = get_userdata($winner_usr->uid);
			$c .= '<option value="'.$row->ID.'">'.$row->post_title.' - '.$winner_usr->user_login.'</option>';
			$i = 1;
		}
	}
	
	//----------------------------
	 
	if($i == 1)
	return $c.'</select>';
	
	return false;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_payments_page_url($subpage = '', $id = '')
{
	$opt = get_option('ProjectTheme_my_account_payments_id');
	if(empty($subpage)) $subpage = "home";
	
	$perm = ProjectTheme_using_permalinks();
	
	if($perm) return get_permalink($opt). "?pg=".$subpage.(!empty($id) ? "&id=".$id : '');
	
	return get_permalink($opt). "&pg=".$subpage.(!empty($id) ? "&id=".$id : '');
}

function ProjectTheme_get_payments_page_url2($subpage = '', $poid = '')
{
	$opt = get_option('ProjectTheme_my_account_payments_id');
	if(empty($subpage)) $subpage = "home";
	
	$perm = ProjectTheme_using_permalinks();
	
	if($perm) return get_permalink($opt). "?pg=".$subpage.(!empty($poid) ? "&poid=".$poid : '');
	
	return get_permalink($opt). "&pg=".$subpage.(!empty($poid) ? "&poid=".$poid : '');
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_payments_page_url_redir($subpage = '')
{
	$opt = get_option('ProjectTheme_my_account_payments_id');
	if(empty($subpage)) $subpage = "home";
	
	$perm = ProjectTheme_using_permalinks();
	$rdr = urlencode(PT_curPageURL());
	
	if($perm) return get_permalink($opt). "?redir1=".$rdr."&pg=".$subpage.(!empty($id) ? "&id=".$id : '');
	
	return get_permalink($opt). "&redir1=".$rdr."&pg=".$subpage.(!empty($id) ? "&id=".$id : '');
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_pay4project_page_url($pid)
{
	$opt = get_option('ProjectTheme_my_account_pay_for_project');	
	$perm = ProjectTheme_using_permalinks();
	if($perm) return get_permalink($opt). "?pid=".$pid;
	
	return get_permalink($opt). "&pid=".$pid;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_pay_with_credits_page_url($pid, $conf = '')
{
	$opt = get_option('ProjectTheme_my_account_pay_with_credits');	
	$perm = ProjectTheme_using_permalinks();
	if($perm) return get_permalink($opt). "?pid=".$pid.$conf;
	
	return get_permalink($opt). "&pid=".$pid.$conf;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_priv_mess_page_url($subpage = '', $id = '', $addon = '')
{
	$opt = get_option('ProjectTheme_my_account_private_messages_id');
	if(empty($subpage)) $subpage = "home";
	
	if($subpage == "delete-message")
	{
		if(!empty($_GET['rdr'])) $rdr = urlencode($_GET['rdr']); 
		else $rdr = urlencode(PT_curPageURL());
	}
	
	$perm = ProjectTheme_using_permalinks();
	
	if($perm) return get_permalink($opt). "?rdr=".$rdr."&pg=".$subpage.(!empty($id) ? "&id=".$id : '').$addon;
	
	return get_permalink($opt). "&rdr=".$rdr."&pg=".$subpage.(!empty($id) ? "&id=".$id : '').$addon;
}



/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_display_advanced_search_disp_page($content = '')
{
	if ( preg_match( "/\[project_theme_advanced_search\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_advanced_search_area_main_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_advanced_search\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_display_provider_search_disp_page($content = '')
{
	if ( preg_match( "/\[project_theme_provider_search\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_display_provider_search_page_disp();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_provider_search\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_latest_closed_projects_page($content = '')
{
	if ( preg_match( "/\[project_theme_recently_closed_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_display_recently_closed_page_disp();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_recently_closed_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_display_blog_content_page($content = '')
{
	if ( preg_match( "/\[project_theme_all_blog_posts\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_display_blog_page_disp();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_all_blog_posts\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function project_get_field_tp($nr)
{
		if($nr == "1") return "Text field";
		if($nr == "2") return "Select box";
		if($nr == "3") return "Radio Buttons";
		if($nr == "4") return "Check-box";
		if($nr == "5") return "Large text-area";
		if($nr == "6") return "HTML Box";	
		
		
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_all_locations_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_all_locations\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_all_locations_area_main_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_all_locations\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_all_categories_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_all_categories\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_all_categories_area_main_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_all_categories\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_all_projects_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_all_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_display_all_prjs_page_disp();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_all_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/




function ProjectTheme_display_my_account_awaiting_payments_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_awaiting_payments\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_area_awaiting_payments_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_awaiting_payments\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


function ProjectTheme_display_my_account_awaiting_completion_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_awaiting_completion\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_area_awaiting_completion_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_awaiting_completion\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


function ProjectTheme_display_my_account_page( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_home\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_area_main_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_home\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_personal_info( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_personal_info\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_personal_info_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_personal_info\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function BillyB add pass_logo
*
**************************************************************/

function ProjectTheme_display_my_account_pass_logo( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_pass_logo\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_pass_logo();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_pass_logo\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function BillyB add my_profile
*
**************************************************************/

function ProjectTheme_display_my_account_my_profile( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_my_profile\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_my_profile();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_my_profile\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function BillyB add admin_profile
*
**************************************************************/

function ProjectTheme_display_my_account_admin_profile( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_admin_profile\]/", $content ) ) 
	{
		ob_start();
		billyB_admin_profile();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_admin_profile\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_pay_for_project( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_pay_for_project\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_pay4project_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_pay_for_project\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

function ProjectTheme_display_my_account_pay_with_credits( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_pay_with_credits\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_pay_with_credits_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_pay_with_credits\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_completed_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_completed_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_completed_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_completed_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_closed_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_closed_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_closed_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_closed_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_won_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_won_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_won_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_won_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_display_my_account_outstanding_payments( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_outstanding_payments\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_outstanding_payments_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_outstanding_payments\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_outstanding_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_outstanding_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_outstanding_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_outstanding_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


function ProjectTheme_display_my_account_delivered_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_delivered_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_delivered_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_delivered_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}
//BillyB add function for "to award" requests
function ProjectTheme_display_my_account_to_award_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_to_award_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_to_award_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_to_award_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

function ProjectTheme_display_my_account_bid_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_bid_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_bid_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_bid_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}
//BillyB add open bids
function ProjectTheme_display_my_account_open_proposals( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_open_proposals\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_open_proposals_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_open_proposals\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_active_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_active_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_active_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_active_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_unpublished_projects( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_unpublish_projects\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_unpublished_projects_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_unpublish_projects\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_payments( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_payments\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_payments_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_payments\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_private_messages( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_private_messages\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_private_messages_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_private_messages\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_my_account_feedbacks( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_my_account_feedback\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_my_account_feedbacks_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_my_account_feedback\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_display_post_new_pg( $content = '' ) 
{
	if ( preg_match( "/\[project_theme_post_new\]/", $content ) ) 
	{
		ob_start();
		ProjectTheme_post_new_area_function();
		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( '$', '\$', $output );
		return preg_replace( "/(<p>)*\[project_theme_post_new\](<\/p>)*/", $output, $content );
		
	} 
	else {
		return $content;
	}
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_update_credits($uid,$am)
{

	update_user_meta($uid,'credits',$am);	

}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_listing_normal($pid = '')
{
	
	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_unread_number_messages($uid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_pm where user='$uid' and rd='0' AND show_to_destination='1'";
				$r = $wpdb->get_results($s);	
				return count($r);
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_check_if_page_existed($pid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."posts where post_type='page' AND post_status='publish' AND ID='$pid'";
	$r = $wpdb->get_results($s);
	
	if(count($r) > 0) return true;
	return false;	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_insert_pages($page_ids, $page_title, $page_tag, $parent_pg = 0 )
{
	
		$opt = get_option($page_ids);			
		if(!ProjectTheme_check_if_page_existed($opt))
		{
			
			$post = array(
			'post_title' 	=> $page_title, 
			'post_content' 	=> $page_tag, 
			'post_status' 	=> 'publish', 
			'post_type' 	=> 'page',
			'post_author' 	=> 1,
			'ping_status' 	=> 'closed', 
			'post_parent' 	=> $parent_pg);
			
			$post_id = wp_insert_post($post);
				
			update_post_meta($post_id, '_wp_page_template', 'project-special-page-template.php');
			update_option($page_ids, $post_id);
		
		}
				
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_admin_style_sheet()
{
	 
	wp_enqueue_script("jquery-ui-widget");
	wp_enqueue_script("jquery-ui-mouse");
	wp_enqueue_script("jquery-ui-tabs");
	wp_enqueue_script("jquery-ui-datepicker");
	
?>	
	
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
    
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/tipTip.css" type="text/css" /> 
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_url'); ?>/css/admin.css" type="text/css" />    
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/colorpicker.css" type="text/css" />
    <link rel="stylesheet" media="screen" type="text/css" href="<?php bloginfo('template_url'); ?>/css/layout.css" />
	<link type="text/css" href="<?php bloginfo('template_url'); ?>/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
	
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery_tip.js"></script>	
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/idtabs.js"></script>	
 
		
	<script type="text/javascript">
	
 var $ = jQuery;
 
 
     jQuery(function() {
        //jQuery( document ).tooltip();
    });
 
	
		<?php
			
			$tb = "tabs1";
			if(isset($_GET['active_tab'])) $tb = $_GET['active_tab']; 
		
		?>	
			
		jQuery(document).ready(function() {		
  			jQuery("#usual2 ul").idTabs("<?php echo $tb; ?>");
			jQuery(".tltp_cls").tipTip({maxWidth: "330"}); 
		});
		
		
		var SITE_URL = '<?php bloginfo('siteurl'); ?>';
		var SITE_CURRENCY = '<?php echo ProjectTheme_currency(); ?>';
		</script>
	
 
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/admin.js"></script>
    <?php	

}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_option_drop_down($arr, $name)
{
	
	$r = '<select name="'.$name.'">';
	foreach ($arr as $key => $value)
	{
		$r .= '<option value="'.$key.'" '.(get_option($name) == $key ? ' selected="selected" ' : "" ).'>'.$value.'</option>';		
		
	}
    return $r.'</select>';  
	
}	


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_generate_thumb($img_ID, $width, $height, $cut = true)
{
	$xx = projectTheme_wp_get_attachment_image($img_ID, array($width, $height ));
	if(empty($xx)) $xx = projectTheme_wp_get_attachment_image(($img_ID - 1), array($width, $height )); 
	return $xx;
}
 

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/



function ProjectTheme_get_first_post_image($pid, $w = 100, $h = 100)
{
	$img = ProjectTheme_get_first_post_image_fnc($pid, $w, $h);
	$img = apply_filters('ProjectTheme_get_first_post_image_filter', $img, $pid, $w, $h);
	return $img;
	
}

function ProjectTheme_get_first_post_image_fnc($pid, $w = 100, $h = 100)
{
	
	
	
	//---------------------
	// build the exclude list
	$exclude = array();
	
	$args = array(
	'order'          => 'ASC',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
	'meta_key'		 => 'another_reserved1',
	'meta_value'	 => '1',
	'numberposts'    => -1,
	'post_status'    => null,
	);
	$attachments = get_posts($args);
	if ($attachments) {
	    foreach ($attachments as $attachment) {
		$url = $attachment->ID;
		array_push($exclude, $url);
	}
	}
	
	//-----------------

	$args = array(
	'order'          => 'ASC',
	'orderby'        => 'post_date',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
	'exclude'    		=> $exclude,
	'post_mime_type' => 'image',
	'post_status'    => null,
	'numberposts'    => 1,
	);
	$attachments = get_posts($args);
	if ($attachments) {
	    foreach ($attachments as $attachment) 
	    {
			$url = wp_get_attachment_url($attachment->ID);
			return ProjectTheme_generate_thumb($attachment->ID, $w, $h);	  
		}
	}
	else	return get_bloginfo('template_url').'/images/nopic.jpg';

}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_is_owner_of_post()
{
	
	if(!is_user_logged_in())
		return false;
	
	global $current_user;
	get_currentuserinfo();
	
	$post = get_post(get_the_ID());
	if($post->post_author == $current_user->ID) return true;
	return false;	
	
}

function PT_curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_show_price($price, $cents = 2)
{	
	$ProjectTheme_currency_position = get_option('ProjectTheme_currency_position');	
	if($ProjectTheme_currency_position == "front") return ProjectTheme_get_currency()."".ProjectTheme_formats($price, $cents);	
	return ProjectTheme_formats($price,$cents)."".ProjectTheme_get_currency();	
		
}


function ProjectTheme_show_currency_in_pos($pos = 'front')
{	
	$ProjectTheme_currency_position = get_option('ProjectTheme_currency_position');	
	if($ProjectTheme_currency_position == "front" and $pos == 'front') return ProjectTheme_get_currency();	
	if($ProjectTheme_currency_position != "front" and $pos != 'front') return ProjectTheme_get_currency();	
	
		
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


function ProjectTheme_formats($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
  
  $dec_sep = get_option('ProjectTheme_decimal_sum_separator');
  if(empty($dec_sep)) $dec_sep = '.';
  
  $tho_sep = get_option('ProjectTheme_thousands_sum_separator');
  if(empty($tho_sep)) $tho_sep = ',';
  
  //dec,thou
  
  if (is_numeric($number)) { // a number
    if (!$number) { // zero
      $money = ($cents == 2 ? '0'.$dec_sep.'00' : '0'); // output zero
    } else { // value
      if (floor($number) == $number) { // whole number
        $money = number_format($number, ($cents == 2 ? 2 : 0), $dec_sep, $tho_sep ); // format
      } else { // cents
        $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2), $dec_sep, $tho_sep ); // format
      } // integer or decimal
    } // value
    return $money;
  } // numeric
} // formatMoney

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_formats_special($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
  
	$dec_sep = '.';
	$tho_sep = ',';
  
  //dec,thou
  
  if (is_numeric($number)) { // a number
    if (!$number) { // zero
      $money = ($cents == 2 ? '0'.$dec_sep.'00' : '0'); // output zero
    } else { // value
      if (floor($number) == $number) { // whole number
        $money = number_format($number, ($cents == 2 ? 2 : 0), $dec_sep, '' ); // format
      } else { // cents
        $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2), $dec_sep, '' ); // format
      } // integer or decimal
    } // value
    return $money;
  } // numeric
} // formatMoney

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_total_number_of_created_Projects($uid)
{
	
	global $wpdb;
	$s = "select distinct ID from ".$wpdb->prefix."posts where post_author='$uid' AND post_type='project' and post_status='publish'";
	$r = $wpdb->get_results($s);
	
	return count($r);	
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_total_number_of_closed_Projects($uid)
{
		global $wpdb;
		$s = "
					SELECT distinct wposts.ID 
					FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta 
					WHERE wposts.ID = wpostmeta.post_id AND
					wpostmeta.meta_key='closed' AND wpostmeta.meta_value='1'
					AND wposts.post_status = 'publish' 
					AND wposts.post_type = 'project' AND wposts.post_author = '$uid'  
					ORDER BY wposts.post_date ";
					
		$r = $wpdb->get_results($s);
		return count($r);	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_total_number_of_rated_Projects($uid)
{
	global $wpdb;
	$s = "SELECT distinct id FROM ".$wpdb->prefix."project_ratings where fromuser='$uid' AND awarded='1' ";
					
	$r = $wpdb->get_results($s);
	return count($r);	
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_number_of_bid($pid)
{
	global $wpdb;
	$s = "select bid from ".$wpdb->prefix."project_bids where pid='$pid'";
	$r = $wpdb->get_results($s);
	
	return count($r);
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_project_fields_values($pid)
{
		$cat = wp_get_object_terms($pid, 'project_cat');
	
		$catid = $cat[0]->term_id ;
	
		global $wpdb;
		$s = "select * from ".$wpdb->prefix."project_custom_fields where tp!='6' order by ordr asc  "; //where cate='all' OR cate like '%|$catid|%' order by ordr asc";	
		$r = $wpdb->get_results($s);
		
		
		$arr = array();
		$i = 0;
		
		foreach($r as $row) // = mysql_fetch_object($r))
		{
			
			$pmeta = get_post_meta($pid, "custom_field_ID_".$row->id);
			
			if(!empty($pmeta) && count($pmeta) > 0)
			{
			 	$arr[$i]['field_name']  = $row->name;
			
			if(is_array($pmeta))
			{
				$arr[$i]['field_name']  = $row->name;
				for($k=0;$k<count($pmeta);$k++)
					$arr[$i]['field_value'] .= $pmeta[$k].'<br />';
				
				$i++;
			}
			else 
			{
				if(!empty($pmeta))
				{
					$arr[$i]['field_name']  = $row->name;
					$arr[$i]['field_value'] = $pmeta;
					$i++;
				}
			}
			
		}
		}
		
		return $arr;
}

function ProjectTheme_get_user_fields_values($pid)
{
	
		global $wpdb;
		$s = "select * from ".$wpdb->prefix."project_user_custom_fields "; //where cate='all' OR cate like '%|$catid|%' order by ordr asc";	
		$r = $wpdb->get_results($s);
		
		
		$arr = array();
		$i = 0;
		
		foreach($r as $row) // = mysql_fetch_object($r))
		{
			
			$pmeta = get_user_meta($pid, "custom_field_ID_".$row->id);
			
			if(!empty($pmeta) && count($pmeta) > 0)
			{
			 	$arr[$i]['field_name']  = $row->name;
			
			if(is_array($pmeta))
			{
				$arr[$i]['field_name']  = $row->name;
				for($k=0;$k<count($pmeta);$k++)
					$arr[$i]['field_value'] .= $pmeta[$k].'<br />';
				
				$i++;
			}
			else 
			{
				if(!empty($pmeta))
				{
					$arr[$i]['field_name']  = $row->name;
					$arr[$i]['field_value'] = $pmeta;
					$i++;
				}
			}
			
		}
		}
		
		return $arr;
}
	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_project_get_star_rating($uid)
{
	
	global $wpdb;
	$s = "select grade from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='1'";
	$r = $wpdb->get_results($s);
	$i = 0; $s = 0;
		
	if(count($r) == 0)	return __('(No rating)','ProjectTheme');
	else
	foreach($r as $row) // = mysql_fetch_object($r))
	{
		$i++;
		$s = $s + $row->grade;
			
	}

	$rating = round(($s/$i)/2, 0);
	$rating2 = round(($s/$i)/2, 1);

	return ProjectTheme_get_project_stars($rating)." (".$rating2 ."/5) ". sprintf(__("on %s rating(s)","ProjectTheme"), $i);
}

function ProjectTheme_project_get_star_rating2($uid)
{
	global $wpdb;
	$s = "select grade from ".$wpdb->prefix."project_ratings where touser='$uid' AND awarded='1'";
	$r = $wpdb->get_results($s);
	$i = 0; $s = 0;
		
	if(count($r) == 0)	return __('(No rating)','ProjectTheme');
	else
	foreach($r as $row) // = mysql_fetch_object($r))
	{
		$i++;
		$s = $s + $row->grade;
			
	}
	$rating = round(($s/$i)/2, 0);
	$rating2 = round(($s/$i)/2, 1);
		
	return ProjectTheme_get_project_stars($rating)."<br/>(".$rating2 ."/5) ". sprintf(__("on %s rating(s)","ProjectTheme"), $i);
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_project_stars($rating)
{
	$full 	= get_bloginfo('template_url')."/images/full_star.gif";
	$empty 	= get_bloginfo('template_url')."/images/empty_star.gif";	
		
	$r = '';
	
	for($j=1;$j<=$rating;$j++)
	$r .= '<img src="'.$full.'" />';
	
	
	for($j=5;$j>$rating;$j--)
	$r .= '<img src="'.$empty.'" />';
	
	return $r;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_budget_name_string_fromID($id)
{
	$budget = true;
	$budget = apply_filters('ProjectTheme_budget_function_filter', $budget);
	
	if($budget == true)
	{
		global $wpdb;
		$s = "select * from ".$wpdb->prefix."project_bidding_intervals where id='$id'";
		$r = $wpdb->get_results($s);
		$row = $r[0];
		//BillyB hide budget description was $nm = $row->bidding_interval_name. " (".ProjectTheme_get_show_price($row->low_limit,0)." - ".ProjectTheme_get_show_price($row->high_limit,0).")";
		$nm = $row->bidding_interval_name;	
		return $nm;				
	}
	else
	{
		return apply_filters('ProjectTheme_other_budget_function_return');	
	}
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_budget_name_string($row)
{
	//BillyB remove price from budget display: ." (".ProjectTheme_get_show_price($row->low_limit,0)." - ".ProjectTheme_get_show_price($row->high_limit,0).")"
	$nm = $row->bidding_interval_name;	
	return $nm;				
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_average_bid($pid)
{
	$nm = $row->bidding_interval_name. " (".ProjectTheme_get_show_price($row->low_limit,0)." - ".ProjectTheme_get_show_price($row->high_limit,0).")";	
	return $nm;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_current_user_has_bid($uid, $res)
{
	foreach($res as $row)
		if($row->uid == $uid) { return true; }
	
	return false;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/



function ProjectTheme_get_user_feedback_link($uid)
{
	return get_bloginfo('siteurl'). '/?p_action=user_feedback&post_author='. $uid;	
}


function ProjectTheme_get_user_profile_link($uid)
{
	return get_bloginfo('siteurl'). '/?p_action=user_profile&post_author='. $uid;	
}

function projectTheme_get_post_active()
{

			if($arr[0] == "winner") $pay_this_me = 1;
			if($arr[0] == "winner_not") $pay_this_me2 = 1;
			if($arr[0] == "unpaid") $unpaid = 1;

			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
			$post				= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                <div class="image_holder">
                 <?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
				?>
                
                <a href="<?php the_permalink(); ?>"><img width="40" height="32" class="image_class" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),40,32); ?>" /></a>
                
                <?php endif; ?>
                
                </div>
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                        <?php 

                        the_title(); 

                        
                        ?></a></h2>
                        
                        
                  <?php if(1) { ?>     
                        
                      
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> </p>
                       
                        
                        
                        
                                <p class="task_buttons">   
                        <?php if($pay_this_me == 1): ?>
                        <a href="<?php echo ProjectTheme_get_pay4project_page_url(get_the_ID()); ?>" 
                        class="post_bid_btn"><?php echo __("Pay This", "ProjectTheme");?></a>
                        <?php endif; ?>
                        
                   <?php if(1 ) { ?>  
                 
                  <?php if( $pay_this_me != 1): ?>
                  <a href="<?php the_permalink(); ?>" class="post_bid_btn"><?php echo __("Read More", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php if( $unpaid == 1): 
				  
				  	$finalised_posted = get_post_meta(get_the_ID(),'finalised_posted',true);
					if($finalised_posted == "1") $finalised_posted = 3; else $finalised_posted = "1";
				  
				  	$finalised_posted = apply_filters('ProjectTheme_publish_prj_posted', $finalised_posted);
				  
				  ?>
                  <a href="<?php echo ProjectTheme_post_new_with_pid_stuff_thg(get_the_ID(), $finalised_posted); ?>" class="post_bid_btn"><?php echo __("Publish", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  
                
                  
				  <?php if($post->post_author == $uid) { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Edit Project", "ProjectTheme");?></a>
                  <?php }   ?>
                  
                  <?php if($post->post_author == $uid) //$closed == 1) 
				  { ?> 
                  
                   <?php if($closed == "1") //$closed == 1) 
				  { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=repost_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Repost Project", "ProjectTheme");?></a>
                  
                  <?php } /*} else { */  ?>
                <?php
					
					$winner = get_post_meta(get_the_ID(),'winner', true);
					
					if(empty($winner)):
					?>
                   <a href="<?php bloginfo('siteurl') ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Delete", "ProjectTheme");?></a>
                  
                  <?php endif; } ?>
                  
                  <?php } ?>
                  </p>
                        
                        
                     </div> 
                     
                  <div class="details_holder"> <?php } ?>
                  
                  
                  
                  <ul class="project-details1 project-details1_a">
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								  $sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
                            
                            
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php echo ProjectTheme_average_bid(get_the_ID()); ?> </p>
							</li>
					
             				
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/posted.png" width="15" height="15" /> 
								<h3><?php echo __("Bids Posted",'ProjectTheme'); ?>:</h3>
								<p><?php echo projectTheme_number_of_bid( get_the_ID()  ); ?></p>
							</li>
					
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="15" height="15" /> 
								<h3><?php echo __("Expires in",'ProjectTheme'); ?>:</h3>
								<p><?php echo ($closed=="1" ? __('Closed', 'ProjectTheme') : ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0))); ?></p>
							</li>
							
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div> <?php	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_get_post($arr = '')
{
	do_action('ProjectTheme_get_regular_post_project', $arr);	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_post_main_function ( $arr = '')
{	//this section governs what's displayed on the search page, Active Requests, Closed Requests, Unpublished Requests, Delivered Requests
	$pid = get_the_ID();
	global $post, $current_user;
	get_currentuserinfo();
	$post = get_post($pid);
	$uid = $current_user->ID;
	
	$ending 			= get_post_meta(get_the_ID(), 'ending', true);
	$sec 				= $ending - current_time('timestamp',0);
	$location 			= get_post_meta(get_the_ID(), 'Location', true);		
	$closed 			= get_post_meta(get_the_ID(), 'closed', true);
	$featured 			= get_post_meta(get_the_ID(), 'featured', true);
	$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
	$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	//BillyB add current user info
	
	
	$budget = ProjectTheme_get_budget_name_string_fromID(get_post_meta($pid,'budgets',true));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$days_left = ($closed == "0" ? ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0)) : __("Expired/Closed",'ProjectTheme'));
	$posted = get_the_time("F jS Y");
	$auth = get_userdata($post->post_author);
	$hide_project_p = get_post_meta($post->ID, 'private_bids', true);		 
	//BillyB add market
	$market = get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' );
	?>
    	
        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                     	<p><i class="budget-icon-me"></i></p>
                        <h4><?php echo $budget ?></h4>
                    </li>
                    
                    <li>
                    	 <p><i class="proposals-icon-me"></i></p>
                        <h4><?php echo $proposals ?></h4>
                    </li>

                    <li class="last">
                    	<p><i class="clock-icon-me"></i></p>
                        <h4><?php echo $days_left ?></h4>
                    </li>			
				
				<?php //BillyB hide bookmarks if user is poster.  Hiding it here removes watchlist from the search page
				if($post->post_author != $uid):?>
					<span><?php echo do_shortcode ('[bookmark widgetized="1" width="15%"]'); ?></span>
                <?php endif;?>
				</ul>
			<ul>
			<?php //BillyB remove posted date and add Market?>
					<li>
                    	<p><i class="calendar-icon-me"></i></p>
                        <h4><?php echo $market ?></h4>
					</li>	
			</ul>
				
            </div> <!-- end post-main-details -->
            <div class="excerpt-thing">
            	<?php //BillyB add if statement to hide description if owner
				if($post->post_author != $uid):
					if(has_excerpt(get_the_ID()))
					{
					$tg = strip_tags(get_the_excerpt()); echo substr($tg, 0, -10); 
					}
					else
					{
					$tg = 	strip_tags(get_the_content()); 
					echo substr($tg, 0, 250);
					}
				endif;	
				//BillyB to plug in my-deliv_2 section from projectTheme_get_post_acc
				?>
				<div class="my-deliv_2">
					 	<?php if($pay_this_me == 1): ?>
                        <a href="<?php echo ProjectTheme_get_pay4project_page_url(get_the_ID()); ?>" 
                        class="post_bid_btn"><?php echo __("Pay This", "ProjectTheme");?></a>
                        <?php endif; ?>
                        
                   <?php if(1 ) { ?>  

				  <?php if($post->post_author == $uid) { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="green_btn3"><?php echo __("Edit Request", "ProjectTheme");?></a>
                  <?php }   ?>
                  
                  <?php if($post->post_author == $uid) //$closed == 1) 
				  { ?> 
                  
                   <?php 
				   //BillyB reomve repost request
				   if($closed == "1") //$closed == 1) 
				  ?>
                	<?php
					
					$winner = get_post_meta(get_the_ID(),'winner', true);
					
					if(empty($winner)):
					?>
                   <a href="<?php bloginfo('siteurl') ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="green_btn3"><?php echo __("Delete Request", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php } ?>
                  
                  <?php } ?>
                </div>
            </div> <!-- end excerpt-thing -->
            
            <!-- All Requests -->		
			<?php //BillyB add if statement to hide poster info if user is the poster-thing
			if($post->post_author != $uid):?>
			<div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name ?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
			<?php endif;?>
        </div></div>
        
    <?php	
	
}


if(!function_exists('projectTheme_get_post_my_proposal'))
{
function projectTheme_get_post_my_proposal ( $arr = '')
{
	$pid = get_the_ID();
	global $post, $current_user;
	get_currentuserinfo();
	
	$ending 			= get_post_meta(get_the_ID(), 'ending', true);
	$sec 				= $ending - current_time('timestamp',0);
	$location 			= get_post_meta(get_the_ID(), 'Location', true);		
	$closed 			= get_post_meta(get_the_ID(), 'closed', true);
	$featured 			= get_post_meta(get_the_ID(), 'featured', true);
	$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
	$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	
	$budget = ProjectTheme_get_budget_name_string_fromID(get_post_meta($pid,'budgets',true));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$days_left = ($closed == "0" ? ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0)) : __("Expired/Closed",'ProjectTheme'));
	$posted = get_the_time("F jS Y");
	$auth = get_userdata($post->post_author);
	$hide_project_p = get_post_meta($post->ID, 'private_bids', true);		 
	
	?>
    	
        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                     	<p><i class="budget-icon-me"></i></p>
                        <h4><?php echo $budget ?></h4>
                    </li>
                    
                    <li>
                    	 <p><i class="proposals-icon-me"></i></p>
                        <h4><?php echo $proposals ?></h4>
                    </li>
                    
                    <li>
                    	<p><i class="calendar-icon-me"></i></p>
                        <h4><?php echo $posted ?></h4>
                    </li>
                    
                    <li class="last">
                    	<p><i class="clock-icon-me"></i></p>
                        <h4><?php echo $days_left ?></h4>
                    </li>
                    
                </ul>
            </div> <!-- end post-main-details -->
            <?php
				
				$my_bid = projectTheme_get_bid_by_uid($pid, $current_user->ID); 
				$my_bid = projecttheme_get_show_price($my_bid->bid);
			
			?>
            <div class="excerpt-thing">
            	<div class="my-bid-thing-me"><?php printf(__('My Bid Amount: %s','ProjectTheme'), $my_bid) ?></div>
            </div> <!-- end excerpt-thing -->
            
            
            <div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name ?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View Member Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
        
        </div></div>
        
    <?php	
	
}
}


function projectTheme_get_post_main_function2( $arr = '')
{

			if($arr[0] == "winner") 	$pay_this_me = 1;
			if($arr[0] == "winner_not") $pay_this_me2 = 1;
			if($arr[0] == "unpaid") 	$unpaid = 1;

			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
			$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
			$post				= get_post(get_the_ID());

			//echo $paid;
			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
			
			do_action('ProjectTheme_regular_proj_post_before');
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                
               
                
                <div class="image_holder">
                
                 <?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
					$width 	= 40;
					$height = 32;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_regular_proj_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_regular_proj_img_height", $height);
					$image_class 	= apply_filters("ProjectTheme_regular_proj_img_class", 	$image_class);
					
					
				?>
                
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img alt="<?php the_title(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" /></a>
               
               <?php endif; ?>
               
                </div>
  
                
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                        <?php 
						
						do_action('ProjectTheme_regular_proj_title_before');
                        the_title(); 
						do_action('ProjectTheme_regular_proj_title_after');
                        
                        ?></a></h2>
                        
                        
                  <?php if(1) { ?>     
                        
                      
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> 
                        
                        <?php
							
							$projectTheme_admin_approves_each_project = get_option('projectTheme_admin_approves_each_project');
							
							if($post->post_status == "draft" && $closed == "0" && $paid == "1" && $projectTheme_admin_approves_each_project == "yes")
							{
								echo '<br/><em>' . __('Your project is awaiting moderation.','ProjectTheme') . "</em>";	
								
							}
						
						?>
                        
                        
                        </p>
                       
                        
                        
                        
                                <p class="task_buttons">   
                        <?php if($pay_this_me == 1): ?>
                        <a href="<?php echo ProjectTheme_get_pay4project_page_url(get_the_ID()); ?>" 
                        class="post_bid_btn"><?php echo __("Pay This", "ProjectTheme");?></a>
                        <?php endif; ?>
                        
                   <?php if(1 ) { ?>  
                 
                  <?php if( $pay_this_me != 1): ?>
                  <a href="<?php the_permalink(); ?>" class="post_bid_btn"><?php echo __("Read More", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php if( $unpaid == 1): 
				  
				  	$finalised_posted = get_post_meta(get_the_ID(),'finalised_posted',true);
					if($finalised_posted == "1") $finalised_posted = 3; else $finalised_posted = "1";
				  	
					$finalised_posted = apply_filters('ProjectTheme_publish_prj_posted', $finalised_posted);
					
				  ?>
                  <a href="<?php echo ProjectTheme_post_new_with_pid_stuff_thg(get_the_ID(), $finalised_posted); ?>" class="post_bid_btn"><?php echo __("Publish", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  
                
                  
				  <?php if($post->post_author == $uid) { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Edit Project", "ProjectTheme");?></a>
                  <?php }   ?>
                  
                  <?php if($post->post_author == $uid) //$closed == 1) 
				  { ?> 
                  
                   <?php if($closed == "1") //$closed == 1) 
				  { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=repost_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Repost Project", "ProjectTheme");?></a>
                  
                  <?php } /*} else { */  ?>
                	<?php
					
					$winner = get_post_meta(get_the_ID(),'winner', true);
					
					if(empty($winner)):
					?>
                   <a href="<?php bloginfo('siteurl') ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="post_bid_btn"><?php echo __("Delete", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php } ?>
                  
                  <?php } ?>
                  </p>
                        
                        
                     </div> 
                     
                  <div class="details_holder"> <?php } ?>
                  
                  
                  
                  <ul class="project-details1">
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget:",'ProjectTheme'); ?></h3>
								<p><?php 
								
								  $sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
					
             			<?php
		
			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
			if($ProjectTheme_enable_project_location == "yes"):
		
		?>
                        
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="15" height="15" /> 
								<h3><?php echo __("Location:",'ProjectTheme'); ?></h3>
								<p><?php echo get_the_term_list( get_the_ID(), 'project_location', '', ', ', '' ); ?></p>
							</li>
                            
			<?php endif; ?>				
					
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="15" height="15" /> 
								<h3><?php echo __("Expires in:",'ProjectTheme'); ?></h3>
								<p><?php echo ($closed=="1" ? __('Closed', 'ProjectTheme') : ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0))); ?></p>
							</li>
							
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div>
<?php
	
	do_action('ProjectTheme_regular_proj_post_after');

}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projecttheme_generate_thumb2($img_ID, $width, $height, $cut = true)
{

	return projecttheme_wp_get_attachment_image($img_ID, array($width, $height ));
}

function projecttheme_get_cat_pic_attached($cat_id)
{
	$args = array(
	'order'          => 'ASC',
	'post_type'      => 'attachment',
	'meta_key'		 => 'category_image',
	'meta_value'	 => $cat_id,
	'numberposts'    => -1,
	'post_status'    => null,
	);
	$attachments = get_posts($args);
	if(count($attachments) > 0)
	{
		return 	$attachments[0]->ID;	
	}	
		
	return false;
}

function projecttheme_generate_thumb3($img_ID, $size_string)
{
	return projectTheme_wp_get_attachment_image($img_ID, $size_string);
}

function projectTheme_wp_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '') {

	$html = '';
	$image = wp_get_attachment_image_src($attachment_id, $size, $icon);
	if ( $image ) {
		list($src, $width, $height) = $image;
		$hwstring = image_hwstring($width, $height);
		if ( is_array($size) )
			$size = join('x', $size);
		$attachment =& get_post($attachment_id);
		$default_attr = array(
			'src'	=> $src,
			'class'	=> "attachment-$size",
			'alt'	=> trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
			'title'	=> trim(strip_tags( $attachment->post_title )),
		);
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['alt']) )
			$default_attr['alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		$attr = wp_parse_args($attr, $default_attr);
		$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment );
		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<img $hwstring");
		 
		$html = $attr['src'];
	}

	return $html;
}


function projectTheme_get_post_awaiting_payment()
{
	do_action('projectTheme_get_post_awaiting_payment_function');	
}

function projectTheme_get_post_awaiting_payment_function()
{
	
	$pid = get_the_ID();
	global $post, $current_user;
	get_currentuserinfo();
	
	$ending 			= get_post_meta(get_the_ID(), 'ending', true);
	$sec 				= $ending - current_time('timestamp',0);
	$location 			= get_post_meta(get_the_ID(), 'Location', true);		
	$closed 			= get_post_meta(get_the_ID(), 'closed', true);
	$featured 			= get_post_meta(get_the_ID(), 'featured', true);
	$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
	$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	
	$budget = ProjectTheme_get_budget_name_string_fromID(get_post_meta($pid,'budgets',true));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
 
	$posted = get_the_time("jS M Y");
	$auth = get_userdata($post->post_author);
	$hide_project_p = get_post_meta($post->ID, 'private_bids', true);		 
	
 
								
	$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
	$due_date = sprintf(__('Delivery was On: %s','ProjectTheme'), date_i18n('d-M-Y g:iA', $tm_d));
	
	//----------------------
 				
	$bid = projectTheme_get_winner_bid(get_the_ID());
	$my_bid = ProjectTheme_get_show_price($bid->bid);
				 
	
	?>
    	
        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/wallet_icon2.png" alt="project budget" width="16" height="16" /></p>
                        <h4><?php echo sprintf(__('My Bid: %s','ProjectTheme'), $my_bid) ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/prop_icon.png" alt="project proposals" width="16" height="16" /></p>
                        <h4><?php echo $proposals ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/cal_icon.png" alt="project calendar" width="16" height="16" /></p>
                        <h4><?php echo $posted ?></h4>
                    </li>
                    
                    <li class="last">
                    	<p><img src="<?php bloginfo('template_url') ?>/images/clock_icon.png" alt="project clock" width="16" height="16" /></p>
                        <h4><?php echo $due_date ?></h4>
                    </li>
                    
                </ul>
            </div> <!-- end post-main-details -->
          
            <div class="excerpt-thing">
            	<div class="my-deliv_1"><?php _e('Waiting for the service contractor to submit payment.','ProjectTheme') ?></div>
                <div class="my-deliv_2"><?php do_action('ProjectTheme_awaiting_payments_under_posted_in', get_the_ID()); ?> 
                </div>
            </div> <!-- end excerpt-thing -->
            
            
            <div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name ?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
        
        </div></div>
        
        <?php		
	
}


function projectTheme_get_post_awaiting_payment_function2()
{
			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			
			$mark_coder_delivered 			= get_post_meta(get_the_ID(), 'mark_coder_delivered', true);
			
			$post				= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                <div class="image_holder">
                
                <?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
					$width 	= 40;
					$height = 32;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_awaiting_payment_proj_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_awaiting_payment_proj_img_height", 	$height);
					$image_class 	= apply_filters("ProjectTheme_awaiting_payment_proj_img_class", 	$image_class);
					
					
				?>
                
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img alt="<?php the_title(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" /></a>
               
               <?php endif; ?>
                
                </div>
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                        
    
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> 
                  </p>
                   <?php do_action('ProjectTheme_awaiting_payments_under_posted_in', get_the_ID()); ?>      
                        
              <p class="task_buttons">   
                        
		 

                  </p>
      </div> 
                     
                  <div class="details_holder"> 

                  
                  <ul class="project-details1 project-details1_a">
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								  $sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
                            
                            
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" /> 
								<h3><?php echo __("Winning Bid",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$bid = projectTheme_get_winner_bid(get_the_ID());
								echo ProjectTheme_get_show_price($bid->bid);
								  
								
								 ?>
                                
                                </p>
							</li>
					
             				
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="15" height="15" /> 
								<h3><?php echo __("Winner",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$winner = get_post_meta(get_the_ID(), 'winner', true);
								$winner = get_userdata($winner);
								
								echo '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
								
								?></p>
							</li>
                        
							
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="15" height="15" /> 
								<h3><?php echo __("Delivery On",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
								echo date_i18n('d-M-Y H:i:s', $tm_d);
								
								?></p>
							</li>
							
					
                    
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div> <?php			
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projecttheme_escrow_was_made_for_project_done($pid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_escrow where pid='$pid'";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0) return false;
	return true;	
	
}

function projectTheme_get_post_awaiting_compl()
{
	do_action('projectTheme_get_post_awaiting_compl_function');	
}

function projectTheme_get_post_awaiting_compl_function()
{
			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			$hide_project_p 			= get_post_meta(get_the_ID(), 'private_bids', true);
			
			$mark_coder_delivered 			= get_post_meta(get_the_ID(), 'mark_coder_delivered', true);
			$posted 						= get_the_time("M-d-Y");
			$post							= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
											
			$bid = projectTheme_get_winner_bid(get_the_ID());
			$bid_wn = ProjectTheme_get_show_price($bid->bid);
			
			$winner = get_post_meta(get_the_ID(), 'winner', true);
			$winner = get_userdata($winner);
			//BillyB changing username to company
			$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.get_user_meta($winner->ID, company_name, true).'</a>';
			$winner = sprintf(__("Winner: %s", 'ProjectTheme'), $winner );
											
	$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);								
	$delivery_on = sprintf(__('Proposed Delivery: %s','ProjectTheme'), date_i18n('M-d-Y', $tm_d));
	$auth = get_userdata($post->post_author); 	 
			
?>

        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/wallet_icon2.png" alt="project budget" width="16" height="16" /></p>
                        <h4><?php echo sprintf(__('Winning Bid: %s','ProjectTheme'), $bid_wn) ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/prop_icon.png" alt="project proposals" width="16" height="16" /></p>
                        <h4><?php echo $winner ?></h4>
                    </li>
                    <?php //BillyB remove "posted date"?>
                    
                    <li class="last">
                    	<p><img src="<?php bloginfo('template_url') ?>/images/clock_icon.png" alt="project clock" width="16" height="16" /></p>
                        <h4><?php echo $delivery_on ?></h4>
                    </li>
                    
                </ul>
            </div> <!-- end post-main-details -->
          
            <div class="excerpt-thing">
            	
                
                	<?php if($mark_coder_delivered != "1"): ?>
       
                        <?php _e('You are waiting for the winner to mark this Request as completed.','ProjectTheme'); ?>
                        <?php
							
							$projectTheme_enable_paypal_ad = get_option('projectTheme_enable_paypal_ad');
							if($projectTheme_enable_paypal_ad == "yes")
							{
								
									$adaptive_done = get_post_meta(get_the_ID(),'adaptive_done',true);
									if($adaptive_done == '')
									{
								
								?>
                                
                                	<a href="<?php bloginfo('siteurl'); ?>/?p_action=pay_for_project_paypal&pid=<?php echo get_the_ID(); ?>" class="green_btn"><?php _e('Deposit PayPal Escrow','ProjectTheme') ?></a>
                                
                                <?php	
									}
									elseif($adaptive_done == 'started')
									{
										echo '<br/>';
										echo _e('You have deposited escrow by PayPal for this project. Once the freelancer delivers the project, and you accept it, you will release the funds to freelancer. ','ProjectTheme');	
									}
							}
							else
							{
							
							
							if(!projecttheme_escrow_was_made_for_project_done(get_the_ID())):
							$ProjectTheme_enable_credits_wallet = get_option('ProjectTheme_enable_credits_wallet');
							if($ProjectTheme_enable_credits_wallet != 'no'):						
						?> 
                        <br/>
                        <a href="<?php echo ProjectTheme_get_payments_page_url_redir('escrow') ?>" class="post_bid_btn"><?php _e('Make Escrow','ProjectTheme') ?></a>
                        
                        <?php endif; else: echo '<br/>'; _e('Escrow was made for this project.','ProjectTheme'); endif; }
                   
				     else: 
				   
				   		$dv = get_post_meta(get_the_ID(), 'mark_coder_delivered_date', true);
				   		$dv = date_i18n('M-d-Y',$dv);
				   
				   ?>
                   
                   <span class="zbk_zbk"><?php //BillyB made language edits?>
                   <?php printf(__("The winner marked this Request as delivered on: %s","ProjectTheme"), $dv); ?>
                   <?php _e('.<br/><br/>If you agree, please mark this as completed: ','ProjectTheme'); ?>
                     <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=mark_completed&pid=<?php the_ID(); ?>" 
                        class="green_btn"><?php echo __("Mark Completed", "ProjectTheme");?></a>
                   
                   </span>
                   
                   <?php endif; ?>
                   
                
            </div> <!-- end excerpt-thing -->
            
            <?php //BillyB removed user-poster-thing?>
                    
        </div></div>



		  <?php		
	
}

function projectTheme_get_post_awaiting_compl_function_old()
{
		$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			
			$mark_coder_delivered 			= get_post_meta(get_the_ID(), 'mark_coder_delivered', true);
			
			$post				= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                <div class="image_holder">
                 <?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
					$width 	= 40;
					$height = 32;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_awaiting_completion_proj_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_awaiting_completion_proj_img_height", 	$height);
					$image_class 	= apply_filters("ProjectTheme_awaiting_completion_proj_img_class", 	$image_class);
					
					
				?>
                
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img alt="<?php the_title(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" /></a>
               
               <?php endif; ?>
               
                </div>
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                        
    
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> 
                  </p>
                       
                        
              <p class="task_buttons">   
                        
		
       				<?php if($mark_coder_delivered != "1"): ?>
       
                        <?php _e('The winner must mark this as delivered.','ProjectTheme'); ?>
                        <?php
						
							if(!projecttheme_escrow_was_made_for_project_done(get_the_ID())):
							
							$ProjectTheme_enable_credits_wallet = get_option('ProjectTheme_enable_credits_wallet');
							if($ProjectTheme_enable_credits_wallet != 'no'):
													
						?> 
                        <br/>
                        <a href="<?php echo ProjectTheme_get_payments_page_url_redir('escrow') ?>" class="post_bid_btn"><?php _e('Make Escrow','ProjectTheme') ?></a>
                        
                        <?php endif; else: echo '<br/>'; _e('Escrow was made for this project.','ProjectTheme'); endif;
                   
				     else: 
				   
				   		$dv = get_post_meta(get_the_ID(), 'mark_coder_delivered_date', true);
				   		$dv = date_i18n('M-d-Y H:i:s',$dv);
				   
				   ?>
                   
                   <span class="zbk_zbk">
                   <?php printf(__("Marked as delivered on: %s","ProjectTheme"), $dv); ?><br/>
                   <?php _e('Accept this project and: ','ProjectTheme'); ?>
                     <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=mark_completed&pid=<?php the_ID(); ?>" 
                        class="green_btn"><?php echo __("Mark Completed", "ProjectTheme");?></a>
                   
                   </span>
                   
                   <?php endif; ?>
                   

                  </p>
      </div> 
                     
                  <div class="details_holder"> 

                  
                  <ul class="project-details1 project-details1_a">
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								  $sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
                            
                            
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" /> 
								<h3><?php echo __("Winning Bid",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$bid = projectTheme_get_winner_bid(get_the_ID());
								echo ProjectTheme_get_show_price($bid->bid);
								  
								
								 ?>
                                
                                </p>
							</li>
					
             				
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="15" height="15" /> 
								<h3><?php echo __("Winner",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$winner = get_post_meta(get_the_ID(), 'winner', true);
								$winner = get_userdata($winner);
								
								echo '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
								
								?></p>
							</li>
                        

							
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="15" height="15" /> 
								<h3><?php echo __("Delivery On",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
								echo date_i18n('M-d-Y H:i:s', $tm_d);
								
								?></p>
							</li>
							
					
                    
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div> <?php		
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

if(!function_exists('projectTheme_get_post_acc'))
{
function projectTheme_get_post_acc()
{//this section governs what's displayed on the dashboard and Awaiting Completion
	$pid = get_the_ID();
	global $post, $current_user;
	get_currentuserinfo();
	$post = get_post($pid);
	$uid = $current_user->ID;
	
	$ending 			= get_post_meta(get_the_ID(), 'ending', true);
	$sec 				= $ending - current_time('timestamp',0);
	$location 			= get_post_meta(get_the_ID(), 'Location', true);		
	$closed 			= get_post_meta(get_the_ID(), 'closed', true);
	$featured 			= get_post_meta(get_the_ID(), 'featured', true);
	$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
	$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	//BillyB add market
	$market = get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' );
	$budget = ProjectTheme_get_budget_name_string_fromID(get_post_meta($pid,'budgets',true));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
 
	$posted = get_the_time("F jS Y");
	$auth = get_userdata($post->post_author);
	$hide_project_p = get_post_meta($post->ID, 'private_bids', true);		 
	
 
	$days_left = ($closed == "0" ? ProjectTheme_prepare_seconds_to_words($ending - current_time('timestamp',0)) : __("Expired/Closed",'ProjectTheme'));							
	//$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
	//$due_date = sprintf(__('Due Date: %s','ProjectTheme'), date_i18n('d-M-Y g:iA', $tm_d));
	
	//----------------------
	  	
		 
			
			if($arr[0] == "winner") 	$pay_this_me = 1;
			if($arr[0] == "winner_not") $pay_this_me2 = 1;
			if($arr[0] == "unpaid") 	$unpaid = 1;

 
			$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	
	?>
    	
        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                
                	<li>
                    	<p><i class="budget-icon-me"></i></p>
						<?php //BillyB remove average bid?>
                        <h4><?php echo sprintf(__('Budget: %s','ProjectTheme'))?></h4>
                    </li>
                    
                    <li>
                    	 <p><i class="proposals-icon-me"></i></p>
                        <h4><?php echo $proposals ?></h4>
                    </li>
                    
                    <li>
                    	<p><i class="calendar-icon-me"></i></p>
                        <h4><?php echo $posted ?></h4>
                    </li>
                    
                    <li class="last">
                    	<p><i class="clock-icon-me"></i></p>
                        <h4><?php echo $days_left ?></h4>
                    </li>

            </div> 
			<?php //BillyB add market?>
			<div class="post-main-details">
				<ul>
					<li class="last">
                    	<p><i class="clock-icon-me"></i></p>
                        <h4><?php echo $market ?></h4>
                    </li></ul></div>
			
			
			<!-- end post-main-details -->
          
            <div class="excerpt-thing">
            	 
                <div class="my-deliv_2">
					 	<?php if($pay_this_me == 1): ?>
                        <a href="<?php echo ProjectTheme_get_pay4project_page_url(get_the_ID()); ?>" 
                        class="post_bid_btn"><?php echo __("Pay This", "ProjectTheme");?></a>
                        <?php endif; ?>
                        
                   <?php if(1 ) { ?>  
                 
                  <?php if( $pay_this_me != 1): ?>
                  <a href="<?php the_permalink(); ?>" class="green_btn3"><?php echo __("Request Details", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php if( $unpaid == 1): 
				  
				  	$finalised_posted = get_post_meta(get_the_ID(),'finalised_posted',true);
					if($finalised_posted == "1") $finalised_posted = 3; else $finalised_posted = "1";
				  	
					$finalised_posted = apply_filters('ProjectTheme_publish_prj_posted', $finalised_posted);
					
				  ?>
                  <a href="<?php echo ProjectTheme_post_new_with_pid_stuff_thg(get_the_ID(), $finalised_posted); ?>" class="green_btn3"><?php echo __("Publish", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  
                
                  
				  <?php if($post->post_author == $uid) { ?>
                  <a href="<?php bloginfo('siteurl') ?>/?p_action=edit_project&pid=<?php the_ID(); ?>" class="green_btn3"><?php echo __("Edit Request", "ProjectTheme");?></a>
                  <?php }   ?>
                  
                  <?php if($post->post_author == $uid) //$closed == 1) 
				  { ?> 
                  
                   <?php 
				   //BillyB reomve repost request
				   if($closed == "1") //$closed == 1) 
				  ?>
                	<?php
					
					$winner = get_post_meta(get_the_ID(),'winner', true);
					
					if(empty($winner)):
					?>
                   <a href="<?php bloginfo('siteurl') ?>/?p_action=delete_project&pid=<?php the_ID(); ?>" class="green_btn3"><?php echo __("Delete Request", "ProjectTheme");?></a>
                  <?php endif; ?>
                  
                  <?php } ?>
                  
                  <?php } ?>
                </div>
            </div> <!-- end excerpt-thing -->
            
           <?php //BillyB hide poster information if user is the poster
            
			if($post->post_author != $uid): { ?>
			
			<div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> 
			<?php } endif;?>
			<!-- end user-poster-thing -->
        
        </div></div>
        
        <?php	
	
}}


function projectTheme_get_post_outstanding_project()
{
	do_action('projectTheme_get_post_outstanding_project_function');	
}

function projectTheme_get_post_outstanding_project_function()
{
		$pid = get_the_ID();
	global $post, $current_user;
	get_currentuserinfo();
	
	$ending 			= get_post_meta(get_the_ID(), 'ending', true);
	$sec 				= $ending - current_time('timestamp',0);
	$location 			= get_post_meta(get_the_ID(), 'Location', true);		
	$closed 			= get_post_meta(get_the_ID(), 'closed', true);
	$featured 			= get_post_meta(get_the_ID(), 'featured', true);
	$private_bids 		= get_post_meta(get_the_ID(), 'private_bids', true);
	$paid		 		= get_post_meta(get_the_ID(), 'paid', true);
	
	$budget = ProjectTheme_get_budget_name_string_fromID(get_post_meta($pid,'budgets',true));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
	$proposals = sprintf(__('%s proposals','ProjectTheme'), projectTheme_number_of_bid($pid));
 
	$posted = get_the_time("F jS Y");
	$auth = get_userdata($post->post_author);
	$hide_project_p = get_post_meta($post->ID, 'private_bids', true);		 
	
 
								
	$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
	//BillyB change date format
	$due_date = sprintf(__('Due Date: %s','ProjectTheme'), date_i18n('M-d-Y g:iA', $tm_d));
	
	//----------------------
	  	
				$my_bid = projectTheme_get_bid_by_uid($pid, $current_user->ID); 
				$my_bid = projecttheme_get_show_price($my_bid->bid);
			
		 $mark_coder_delivered 			= get_post_meta(get_the_ID(), 'mark_coder_delivered', true);
	
	?>
    	
        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/wallet_icon2.png" alt="project budget" width="16" height="16" /></p>
                        <h4><?php echo sprintf(__('My Bid: %s','ProjectTheme'), $my_bid) ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/prop_icon.png" alt="project proposals" width="16" height="16" /></p>
                        <h4><?php echo $proposals ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/cal_icon.png" alt="project calendar" width="16" height="16" /></p>
                        <h4><?php echo $posted ?></h4>
                    </li>
                    
                    <li class="last">
                    	<p><img src="<?php bloginfo('template_url') ?>/images/clock_icon.png" alt="project clock" width="16" height="16" /></p>
                        <h4><?php echo $due_date ?></h4>
                    </li>
                    
                </ul>
            </div> <!-- end post-main-details -->
          
            <div class="excerpt-thing">
            	<div class="my-deliv_1"><?php 
				//BillyB change language
				_e('After completing the Request, please mark it as <em><strong>delivered</strong></em>','ProjectTheme') ?></div>
                <div class="my-deliv_2">
					 	<?php do_action('ProjectTheme_outstanding_proj_buttons'); ?>    
		
						<?php if($mark_coder_delivered != "1"): ?>
           
                            <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=mark_delivered&pid=<?php the_ID(); ?>" 
                            class="green_btn"><?php echo __("Mark Delivered", "ProjectTheme");?></a>
                            
                            
                            <?php
							
							$projectTheme_enable_paypal_ad = get_option('projectTheme_enable_paypal_ad');
							if($projectTheme_enable_paypal_ad == "yes")
							{
								
									$adaptive_done = get_post_meta(get_the_ID(),'adaptive_done',true);
								 	
									if($adaptive_done == 'started')
									{
										echo '<br/>';
										echo '<div class="ep_ep2">'.__('The project owner has put the money into escrow for you. Once you mark the project as delivered the owner will release the money. ','ProjectTheme') . '</div>';	
									}
							}
							
							?>
                            
                       
                       <?php else: 
                       
                            $dv = get_post_meta(get_the_ID(), 'mark_coder_delivered_date', true);
                            $dv = date_i18n('M-d-Y H:i:s',$dv);
                       
                       ?>
                       
                       <span class="zbk_zbk">
                       <?php 
					   //BillyB change language
					   printf(__("You marked this Request as delivered on %s.<br/>This Request is currently awaiting a response from the Requester.","ProjectTheme"), $dv); ?>
                       </span>
                       
                       <?php endif; ?>

                
                </div>
            </div> <!-- end excerpt-thing -->
            
            
            <div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name ?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
        
        </div></div>
        
        <?php	
	
}

function projectTheme_get_post_outstanding_project_function2()
{
	
			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			
			$mark_coder_delivered 			= get_post_meta(get_the_ID(), 'mark_coder_delivered', true);
			$post							= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
			do_action('ProjectTheme_outstanding_proj_post_before');
			
?>
				<div class="post" id="post-<?php the_ID(); ?>">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                <div class="image_holder">
                 <?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
					$width 	= 40;
					$height = 32;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_outstanding_proj_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_outstanding_proj_img_height", $height);
					$image_class 	= apply_filters("ProjectTheme_outstanding_proj_img_class", 	$image_class);
					
				?>
                
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" alt="<?php the_title(); ?>" /></a>
               
               <?php endif; ?>
                </div>
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php 
					 do_action('ProjectTheme_outstanding_proj_title_before');
					 the_title(); 
					 do_action('ProjectTheme_outstanding_proj_title_after');
					 ?></a></h2>
                        
    
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> 
                  </p>
                       
                        
              <p class="task_buttons">   
                    <?php do_action('ProjectTheme_outstanding_proj_buttons'); ?>    
		
       				<?php if($mark_coder_delivered != "1"): ?>
       
                        <a href="<?php echo get_bloginfo('siteurl'); ?>/?p_action=mark_delivered&pid=<?php the_ID(); ?>" 
                        class="post_bid_btn"><?php echo __("Mark Delivered", "ProjectTheme");?></a>
                   
				   <?php else: 
				   
				   		$dv = get_post_meta(get_the_ID(), 'mark_coder_delivered_date', true);
				   		//BillyB fix date format
						$dv = date_i18n('M-d-Y',$dv);
				   
				   ?>
                   
                   <span class="zbk_zbk">
                   <?php printf(__("This Request was marked as delivered on: %s.</br>It is not currently awaiting a response from the Requester.","ProjectTheme"), $dv); ?>
                   </span>
                   
                   <?php endif; ?>
                   

                  </p>
      </div> 
                     
                  <div class="details_holder"> 

                  
                  <ul class="project-details1 project-details1_a">
                  
                  			<?php do_action('ProjectTheme_outstanding_proj_details_before'); ?> 
                  		
                  
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
                            
                            
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" /> 
								<h3><?php echo __("Winning Bid",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$bid = projectTheme_get_winner_bid(get_the_ID());
								echo ProjectTheme_get_show_price($bid->bid);
								 								
								 ?>
                                
                                </p>
							</li>
					
             
                        
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/clock.png" width="15" height="15" /> 
								<h3><?php echo __("Delivery On",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);							
								//BillyB change date format
								echo date_i18n('M-d-Y', $tm_d);
								
								?></p>
							</li>
							
							<?php do_action('ProjectTheme_outstanding_proj_details_after'); ?> 
                    
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div> <?php	
					 
					 do_action('ProjectTheme_outstanding_proj_post_after');
					 
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_get_post_pay($arr = '')
{
	do_action('projectTheme_get_post_pay_function',$arr);	
}

function projecttheme_escrow_was_made_for_project_not_released($pid)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_escrow where pid='$pid' and released='0'";
	$r = $wpdb->get_results($s);
	
	if(count($r) == 0) return false;
	return true;	
	
}

function projectTheme_get_post_pay_function( $arr = '')
{

			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			$post				= get_post(get_the_ID());
			$hide_project_p 			= get_post_meta(get_the_ID(), 'private_bids', true);
			
			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
	 
								
			$bid = projectTheme_get_winner_bid(get_the_ID());
			$bid_wn = ProjectTheme_get_show_price($bid->bid);
			
			$winner = get_post_meta(get_the_ID(), 'winner', true);
			$winner = get_userdata($winner);
			$winner = '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
			$winner = sprintf(__("WInner: %s", 'ProjectTheme'), $winner );
											
	$tm_d = get_post_meta(get_the_ID(), 'expected_delivery', true);								
	$delivery_on = sprintf(__('Delivery On: %s','ProjectTheme'), date_i18n('d-M-Y g:iA', $tm_d));
	$auth = get_userdata($post->post_author); 	 
	$posted = get_the_time("jS F Y");
	
			
?>

        <div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
    		<div class="post-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a>
            
             <?php
				
				if($featured == "1")
				echo '<span class="featured_thing_project2">'.__('Featured Project','ProjectTheme').'</span>';
				
				if($hide_project_p == "1" or $hide_project_p == "yes")
				echo '<span class="private_thing_project2">'.__('Sealed Bidding','ProjectTheme').'</span>';
				
				?>
            
            </div>
    		<div class="post-main-details">
            	<ul>
                	<li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/wallet_icon2.png" alt="project budget" width="16" height="16" /></p>
                        <h4><?php echo sprintf(__('Winning Bid: %s','ProjectTheme'), $bid_wn) ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/prop_icon.png" alt="project proposals" width="16" height="16" /></p>
                        <h4><?php echo $winner ?></h4>
                    </li>
                    
                    <li>
                    	<p><img src="<?php bloginfo('template_url') ?>/images/cal_icon.png" alt="project calendar" width="16" height="16" /></p>
                        <h4><?php echo $posted ?></h4>
                    </li>
                    
                    <li class="last">
                    	<p><img src="<?php bloginfo('template_url') ?>/images/clock_icon.png" alt="project clock" width="16" height="16" /></p>
                        <h4><?php echo $delivery_on ?></h4>
                    </li>
                    
                </ul>
            </div> <!-- end post-main-details -->
          
            <div class="excerpt-thing">
            	
                <?php
					
					$projecttheme_escrow_was_made_for_project_not_released = projecttheme_escrow_was_made_for_project_not_released(get_the_ID());
					if($projecttheme_escrow_was_made_for_project_not_released == false):
				 
						
							$projectTheme_project_function_filter_adv = true;
						 $projectTheme_project_function_filter_adv = apply_filters('projectTheme_project_function_filter_adv', get_the_ID());
						
						if($projectTheme_project_function_filter_adv == true):
						
						$adaptive_done = get_post_meta(get_the_ID(), 'adaptive_done', true);
						if($adaptive_done == "started")
						{
							?>
                            
                            	 <a href="<?php echo get_bloginfo('siteurl'); ?>/?complete_paypal_escrow=<?php the_ID() ?>" class="green_btn"><?php echo __("Complete PayPal Escrow Payment", "ProjectTheme");?></a>
                            
                            <?php
						}
						else
						{
				?>
       
                        <a href="<?php echo ProjectTheme_get_pay4project_page_url(get_the_ID()); ?>" 
                        class="green_btn"><?php echo __("Pay This", "ProjectTheme");?></a>
                   
                   
				<?php } else: do_action('projectTheme_project_function_filter_1_action', get_the_ID());	endif; else: ?>
                
                	<?php 
					
					$kk = ProjectTheme_get_payments_page_url_redir('');
					echo sprintf(__('Escrow was made. <a href="%s" class="green_btn">Go and release it.</a>','ProjectTheme'), $kk); ?>	
                
                <?php endif; ?>
                
            </div> <!-- end excerpt-thing -->
            
            
            <div class="user-poster-thing">
            	<div class="user-avatar-me">
                	<img src="<?php echo ProjectTheme_get_avatar($post->post_author,25, 25) ?>" alt="avatar-user" class="acc_m1" width="25" height="25" />
                </div>
                
                <div class="user-avatar-me fun-time">
                <div class="post-main-details">
            	<ul>
                	<li><a class="avatar-posted-by-username" href="<?php bloginfo('siteurl'); ?>/?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php echo $auth->company_name ?></a></li>
                	<li><?php echo ProjectTheme_project_get_star_rating($post->post_author); ?></li>
                    <li class="last"><a href="<?php echo ProjectTheme_get_user_feedback_link($post->post_author); ?>"><?php _e('View User Feedback','ProjectTheme'); ?></a></li>
                </ul>
                
                </div>
                </div>
                
            </div> <!-- end user-poster-thing -->
        
        </div></div>


		 <?php
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projectTheme_get_post_paid($arr = '')
{
	do_action("projectTheme_get_post_paid_function", $arr);	
}

function projectTheme_get_post_paid_function( $arr = '')
{

			$ending 			= get_post_meta(get_the_ID(), 'ending', true);
			$sec 				= $ending - current_time('timestamp',0);
			$location 			= get_post_meta(get_the_ID(), 'Location', true);		
			$closed 			= get_post_meta(get_the_ID(), 'closed', true);
			$featured 			= get_post_meta(get_the_ID(), 'featured', true);
			$post				= get_post(get_the_ID());

			
			global $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
?>
				<div class="post" id="post-<?php the_ID(); ?>"><div class="padd10">
                
                <?php if($featured == "1"): ?>
                <div class="featured-one"></div>
                <?php endif; ?>
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-one"></div>
                <?php endif; ?>
                
                
                <div class="padd10_only_top">
                <div class="image_holder">
               
               	<?php
				
				$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
					
					$width 	= 40;
					$height = 32;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_paid_proj_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_paid_proj_img_height", 	$height);
					$image_class 	= apply_filters("ProjectTheme_paid_proj_img_class", 	$image_class);
					
					
				?>
                
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img alt="<?php the_title(); ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" /></a>
               
               <?php endif; ?>
               
                </div>
                <div class="title_holder" > 
                     <h2><a class="post-title-class" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                        
    
                        
                  <p class="mypostedon">
                        <?php _e("Posted in",'ProjectTheme');?>: <?php echo get_the_term_list( get_the_ID(), 'project_cat', '', ', ', '' ); ?> 
                        <?php _e("by",'ProjectTheme');?>: <a href="<?php bloginfo('siteurl'); ?>?p_action=user_profile&post_author=<?php echo $post->post_author; ?>"><?php the_author() ?></a> 
                  </p>
                       
                        
              <p class="task_buttons">   
                        
		
       	<?php
		
			$paid_user_date = get_post_meta(get_the_ID(), 'paid_user_date', true);
			if(!empty($paid_user_date)) $sk = date_i18n('d-m-Y H:i:s',$paid_user_date);
			
			printf(__('Paid on: %s','ProjectTheme'), $sk);
		
		?>
       
           
                   

                  </p>
      </div> 
                     
                  <div class="details_holder"> 

                  
                  <ul class="project-details1 project-details1_a">
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" />
								<h3><?php echo __("Budget",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								  $sel = get_post_meta(get_the_ID(), 'budgets', true);
		  						echo ProjectTheme_get_budget_name_string_fromID($sel);
								
								 ?>
                                
                                </p>
							</li>
                            
                            
                            <li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/price.png" width="15" height="15" /> 
								<h3><?php echo __("Winning Bid",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$bid = projectTheme_get_winner_bid(get_the_ID());
								echo ProjectTheme_get_show_price($bid->bid);
								  
								
								 ?>
                                
                                </p>
							</li>
					
             
                        
							<li>
								<img src="<?php echo get_bloginfo('template_url'); ?>/images/location.png" width="15" height="15" /> 
								<h3><?php echo __("Winner",'ProjectTheme'); ?>:</h3>
								<p><?php 
								
								$winner = get_post_meta(get_the_ID(), 'winner', true);
								$winner = get_userdata($winner);
								
								echo '<a href="'.ProjectTheme_get_user_profile_link($winner->ID).'">'.$winner->user_login.'</a>';
								
								?></p>
							</li>
							
					
                    
						</ul>
                      
               
                  </div>   
                     
                     </div></div></div>
<?php
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
//BillyB edit time to display only days if more than one day or hours and minutes if less than one day
function ProjectTheme_prepare_seconds_to_words($seconds)
	{
		$res = ProjectTheme_seconds_to_words_new($seconds); 
		if($res == "Expired") return __('Expired','ProjectTheme');	
		
		if($res[0] == 0) return sprintf(__("%s hours, %s min", 'ProjectTheme'), $res[1], $res[2]);
		if($res[0] == 1){
			
			$plural = $res[1] > 1 ? __('days','ProjectTheme') : __('day','ProjectTheme');
			return sprintf(__("%s %s",'ProjectTheme'), $res[1], $plural);
		}
	}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_seconds_to_words_new($seconds)
{
		if($seconds < 0 ) return 'Expired';
			
        /*** number of days ***/
        $days=(int)($seconds/86400); 
        /*** if more than one day ***/
        $plural = $days > 1 ? 'days' : 'day';
        /*** number of hours ***/
        $hours = (int)(($seconds-($days*86400))/3600);
        /*** number of mins ***/
        $mins = (int)(($seconds-$days*86400-$hours*3600)/60);
        /*** number of seconds ***/
        $secs = (int)($seconds - ($days*86400)-($hours*3600)-($mins*60));
        /*** return the string ***/
                if($days == 0 || $days < 0)
				{
					$arr[0] = 0;
					$arr[1] = $hours;
					$arr[2] = $mins;
					$arr[3] = $secs;
					return $arr;//sprintf("%d hours, %d min, %d sec", $hours, $mins, $secs);
				}
				else
				{
					$arr[0] = 1;
					$arr[1] = $days;
					$arr[2] = $hours;
					$arr[3] = $mins;
					
					return $arr; //sprintf("%d $plural, %d hours, %d min", $days, $hours, $mins);
        		}			
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_auto_draft($uid)
	{
		global $wpdb;	
		$querystr = "
			SELECT distinct wposts.* 
			FROM $wpdb->posts wposts where 
			wposts.post_author = '$uid' AND wposts.post_status = 'auto-draft' 
			AND wposts.post_type = 'project' 
			ORDER BY wposts.ID DESC LIMIT 1 ";
					
		$row = $wpdb->get_results($querystr, OBJECT);
		if(count($row) > 0)
		{
			$row = $row[0];
			return $row->ID;
		}
		
		return ProjectTheme_create_auto_draft($uid);	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_create_auto_draft($uid)
{
		$my_post = array();
		$my_post['post_title'] 		= 'Auto Draft';
		$my_post['post_type'] 		= 'project';
		$my_post['post_status'] 	= 'auto-draft';
		$my_post['post_author'] 	= $uid;
		$pid = wp_insert_post( $my_post, true );
		
		update_post_meta($pid, 'featured_paid', 		'0');
		update_post_meta($pid, 'private_bids_paid', 	'0');
		update_post_meta($pid, 'hide_project_paid', 	'0');
		update_post_meta($pid, 'base_fee_paid', 		'0');
		
		do_action('ProjectTheme_when_creating_auto_draft');
		
		return $pid;
			
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_using_permalinks()
{
	global $wp_rewrite;
	if($wp_rewrite->using_permalinks()) return true; 
	else return false; 	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function projectTheme_slider_post()
{
	
		$featured 		= get_post_meta(get_the_ID(), 'featured', true);
		$private_bids 	= get_post_meta(get_the_ID(), 'private_bids', true);
	
	?>
	
	<div class="slider-post">
    
     			<?php if($featured == "1"): ?>
                <div class="featured-three"></div>
                <?php endif; ?>
                
                
                
                <?php if($private_bids == "yes" or $private_bids == "1"): ?>
                <div class="sealed-three"></div>
                <?php endif; ?>
                
    <?php
	
		$ProjectTheme_enable_images_in_projects = get_option('ProjectTheme_enable_images_in_projects');
				if($ProjectTheme_enable_images_in_projects == "yes"):
				
				
				
					$width 	= 100;
					$height = 80;
					$image_class = "image_class";
					
					
					$width 			= apply_filters("ProjectTheme_slider_post_img_width", 	$width);
					$height 		= apply_filters("ProjectTheme_slider_post_img_height", $height);
					$image_class 	= apply_filters("ProjectTheme_slider_post_img_class", 	$image_class);
				
			?>
            
		<a href="<?php the_permalink(); ?>"><img width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="<?php echo $image_class; ?>" 
                src="<?php echo ProjectTheme_get_first_post_image(get_the_ID(),$width,$height); ?>" /></a>
                <br/>
            <?php else: ?>    
                <br/><br/><br/><br/>
                
            <?php endif; ?>
                
                 <p><b><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
                        <?php 
                      
                     
                        the_title(); 
                       
                        
                        ?></a></b><br/>
                        
                        	<?php
		
			$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
			if($ProjectTheme_enable_project_location == "yes"):
		
		?>
                        
                        <?php echo get_the_term_list( get_the_ID(), 'project_location', '', ', ', '' );   ?><br/>
                        
                        <?php endif; ?>
                        
                        <?php 
						$ids = get_post_meta(get_the_ID(),'budgets', true);
						echo ProjectTheme_get_budget_name_string_fromID($ids); ?>
                       </p>
		
	</div>
	
	<?php
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	

function ProjectTheme_post_new_with_pid_stuff_thg($pid, $step = 1, $fin = 'no')
{
	$using_perm = ProjectTheme_using_permalinks();
	if($using_perm)	return get_permalink(get_option('ProjectTheme_post_new_page_id')). "?post_new_step=".$step."&".($fin != "no" ? 'finalize=1&' : '' )."projectid=" . $pid;
	else return get_bloginfo('siteurl'). "/?page_id=". get_option('ProjectTheme_post_new_page_id'). "&".($fin != "no" ? 'finalize=1&' : '' )."post_new_step=".$step."&projectid=" . $pid;	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_login_url()
{
	return get_bloginfo('siteurl'). '/wp-login.php' ;	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function projecttheme_see_if_project_files_bid($pid, $uid)
{
	
	$args = array(
	'order'          => 'ASC',
	'post_type'      => 'attachment',
	'post_parent'    => $pid,
	'post_author'    => $uid,
	'meta_key'		 => 'is_bidding_file',
	'meta_value'	 => '1',
	'numberposts'    => -1,
	'post_status'    => null,
	);
	$attachments = get_posts($args);
	
 
	
	if ($attachments) {
	
		 foreach ($attachments as $attachment) { 
		
 
			if($attachment->post_author == $uid){	
		 
				return true;
			}
	  
	}
	
	}
 
	
	 return false; 	
	
}

function projectTheme_template_redirect()
{
	    global $wp;
	    global $wp_query, $post, $wp_rewrite;
 	
		if(isset($_GET['my_upload_of_project_files']))
		{
			get_template_part( 'lib/upload_main/uploady2');
			die();	
		}
		
		if(isset($_GET['my_upload_of_project_files_proj']))
		{
			get_template_part( 'lib/upload_main/uploady5');
			die();	
		}
		
	
		if(isset($_GET['my_upload_of_project_files2']))
		{
			get_template_part( 'lib/upload_main/uploady');
			die();	
		}
		
		if(isset($_GET['alert_ipn']))
		{
			projectTheme_alert_pay_IPN(); die();	
		}

		if(isset($_GET['my_upload_of_project_files8']))
		{
			get_template_part( 'lib/upload_main/uploady8');
			die();	
		}
		
		if(isset($_GET['complete_paypal_escrow']))
		{
			get_template_part( 'lib/gateways/complete_paypal_escrow');
			die();	
		}
		
		if(isset($_GET['get_subcats_for_me']))
		{
			$cat_id = $_POST['queryString'];
			if(empty($cat_id) ) { echo " "; }
			else
			{ 
				
				$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$cat_id;
				$sub_terms2 = get_terms( 'project_cat', $args2 );	
				
				if(count($sub_terms2) > 0)
				{
				
					$ret = '<select class="do_input_new" name="subcat" onchange="display_subsubcat(this.value)">';
					$ret .= '<option value="">'.__('Select Subcategory','ProjectTheme'). '</option>';
					
					foreach ( $sub_terms2 as $sub_term2 )
					{
						$sub_id2 = $sub_term2->term_id; 
						$ret .= '<option '.($selected == $sub_id2 ? "selected='selected'" : " " ).' value="'.$sub_id2.'">'.$sub_term2->name.'</option>';
					
					}
					$ret .= "</select>";
					echo $ret;
				
				}
			}
			
			die();	
		}
		if(isset($_GET['check_customers']))
		{
			global $wpdb;
			$name = $_POST['search_term'];
	
			if(!empty($name))
			{
				
				$clients = $wpdb->get_results($wpdb->prepare("select client_id,client_name from ".$wpdb->prefix."clients
						where client_name like %s",'%'.$name.'%'));
				$ret = "";
				if(!empty($clients))
				{
					foreach($clients as $client)
					{
						$ret .= '<li><button value="'.$client->client_id.'" type="button" 
							onclick="setCustomer('.$client->client_id.',&quot;'.$client->client_name.'&quot;);">
							'.$client->client_name.' - '.$client->client_id.'</button></li>';
					}
				}
				else{$ret .= "None";}
				echo $ret;
			}
			else{echo 'name empty';}
			die();	
		}
		if(isset($_GET['check_prime']))
		{
			global $wpdb;
			$name = $_POST['search_term'];
	
			if(!empty($name))
			{
				
				$clients = $wpdb->get_results($wpdb->prepare("select client_id,client_name from ".$wpdb->prefix."clients
						where client_name like %s",'%'.$name.'%'));
				$ret = "";
				if(!empty($clients))
				{
					foreach($clients as $client)
					{
						$ret .= '<li><button value="'.$client->client_id.'" type="button" 
							onclick="setPrime('.$client->client_id.',&quot;'.$client->client_name.'&quot;);">
							'.$client->client_name.'</button></li>';
					}
				}
				else{$ret .= "None";}
				echo $ret;
			}
			else{echo 'name empty';}
			die();	
		}
		if(isset($_GET['check_vendor']))
		{
			global $wpdb;
			$name = $_POST['search_term'];
	
			if(!empty($name))
			{
				
				$vendors = $wpdb->get_results($wpdb->prepare("select vendor_id,vendor_name from ".$wpdb->prefix."vendors
						where vendor_name like %s",'%'.$name.'%'));
				$ret = "";
				if(!empty($vendors))
				{
					foreach($vendors as $vendor)
					{
						$ret .= '<li><button value="'.$vendor->vendor_id.'" type="button" 
							onclick="setVendor('.$vendor->vendor_id.',&quot;'.$vendor->vendor_name.'&quot;);">
							'.$vendor->vendor_name.'</button></li>';
					}
				}
				else{$ret .= "None";}
				echo $ret;
			}
			else{echo 'name empty';}
			die();	
		}
		if(isset($_GET['check_projects']))
		{
			global $wpdb;
			$name = $_POST['search_term'];
	
			if(!empty($name))
			{
				
				$projects = $wpdb->get_results($wpdb->prepare("select gp_id,ID from ".$wpdb->prefix."projects
						where gp_id like %s and project_parent=0",'%'.$name.'%'));
				$ret = "";
				if(!empty($projects))
				{
					foreach($projects as $project)
					{
						$ret .= '<li><button value="'.$project->gp_id.'" type="button" 
							onclick="setProject(&quot;'.$project->gp_id.'&quot;,'.$project->ID.');">
							'.$project->gp_id.'</button></li>';
					}
				}
				else{$ret .= "None";}
				echo $ret;
			}
			else{echo 'name empty';}
			die();	
		}
		if(isset($_GET['get_locscats_for_me']))
		{
			$cat_id = $_POST['queryString'];
			if(empty($cat_id) ) { echo " "; }
			else
			{ 
				
				$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$cat_id;
				$sub_terms2 = get_terms( 'project_location', $args2 );	
				
				if(count($sub_terms2) > 0)
				{
				
					$ret = '<select class="do_input_new" name="subloc" onchange="display_subcat3(this.value)">';
					$ret .= '<option value="">'.__('Select Sublocation','ProjectTheme'). '</option>';
					
					foreach ( $sub_terms2 as $sub_term2 )
					{
						$sub_id2 = $sub_term2->term_id; 
						$ret .= '<option '.($selected == $sub_id2 ? "selected='selected'" : " " ).' value="'.$sub_id2.'">'.$sub_term2->name.'</option>';
					
					}
					$ret .= "</select>";
					echo $ret;
				
				}
			}
			
			die();	
		}
		
		if(isset($_GET['set_image_for_term']))
		{
			if(is_user_logged_in())
			{
				$term_id = $_GET['term_id'];
				$attachment_id = $_GET['attachment_id'];
				update_post_meta($attachment_id, 'category_image', $term_id);
			}
			
			die();
		}
		
		//---------------------------
		
		if(isset($_GET['get_locscats_for_me2']))
		{
			$cat_id = $_POST['queryString'];
			if(empty($cat_id) ) { echo " "; }
			else
			{ 
				
				$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$cat_id;
				$sub_terms2 = get_terms( 'project_location', $args2 );	
				
				if(count($sub_terms2) > 0)
				{
				
					$ret = '<select class="do_input_new" name="subloc2" >';
					$ret .= '<option value="">'.__('Select Sublocation','ProjectTheme'). '</option>';
					
					foreach ( $sub_terms2 as $sub_term2 )
					{
						$sub_id2 = $sub_term2->term_id; 
						$ret .= '<option '.($selected == $sub_id2 ? "selected='selected'" : " " ).' value="'.$sub_id2.'">'.$sub_term2->name.'</option>';
					
					}
					$ret .= "</select>";
					echo $ret;
				
				}
			}
			
			die();	
		}
		
		//---------------------------------------------------
		
		if(isset($_GET['redirect_search']))
		{  
if($_POST['redirect_search'] == "freelancers")
			{
				$string = "username=" . urlencode($_POST['input_text_serch']);	
				$ProjectTheme_provider_search_page_id = get_permalink(get_option('ProjectTheme_provider_search_page_id'));
				
				$perm = ProjectTheme_using_permalinks();
				if($perm == true)
				{	
					wp_redirect($ProjectTheme_provider_search_page_id . "?" . $string);				
				}
				else
				{
					wp_redirect($ProjectTheme_provider_search_page_id . "&" . $string);		
				}				
				
			}
			else
			{
				$string = "term=" . urlencode($_POST['input_text_serch']);	
				$ProjectTheme_advanced_search_page_id = get_permalink(get_option('ProjectTheme_advanced_search_page_id'));
				
				$perm = ProjectTheme_using_permalinks();
				if($perm == true)
				{	
					wp_redirect($ProjectTheme_advanced_search_page_id . "?" . $string);				
				}
				else
				{
					wp_redirect($ProjectTheme_advanced_search_page_id . "&" . $string);		
				}		
				
			}
			
			exit;	
		}
		
		if(isset($_GET['get_my_project_vl_thing']))
		{
			$pids = $_POST['queryString'];
			if($pids == 0) { echo 0; die(); }
			
			$bid = projectTheme_get_winner_bid($pids);
			
			echo $bid->bid;
			
			die();	
		}
		
		$my_pid = $post->ID; $parent = $post->post_parent;
		$paagee 	=  $wp_query->query_vars['my_custom_page_type'];
		$p_action 	=  $wp_query->query_vars['p_action'];
		
		$ProjectTheme_my_account_page_id					= get_option('ProjectTheme_my_account_page_id');
		$ProjectTheme_post_new_page_id						= get_option('ProjectTheme_post_new_page_id');
		$ProjectTheme_my_account_page_id					= get_option('ProjectTheme_my_account_page_id');

		//-------------
 
		if(isset($_GET['redir1']))
		{
			$_SESSION['redir1'] = $_GET['redir1'];
		}
		
		if(($parent == $ProjectTheme_my_account_page_id or $my_pid == get_option('ProjectTheme_my_account_milestones_id')) and !empty($my_pid) )
		{
			if(!is_user_logged_in())	{ wp_redirect(ProjectTheme_login_url()); exit; }	
		}
		
		//-------------
		
		$ProjectTheme_enable_2_user_tp = get_option('ProjectTheme_enable_2_user_tp');
		
		if($ProjectTheme_enable_2_user_tp == "yes" && $p_action != 'choose_user_tp')
		{
			if(is_user_logged_in())
			{
				global $current_user;
				get_currentuserinfo();
				
				$user_tp = get_user_meta($current_user->ID, 'user_tp' ,true);
				if(empty($user_tp) && !current_user_can('level_10'))
				{
					wp_redirect(get_bloginfo('siteurl') . "/?p_action=choose_user_tp"); exit;	
				}
				
			}			
		}
		
		if ($p_action == "payza_listing")
	    {
			 
			get_template_part('lib/gateways/payza_listing');
	        die();	
		}
		
		
		if(isset($_GET['notify_chained']))
		{
		 
			
			if($_POST['status'] == "INCOMPLETE" )
			{
				$trID 	= $_POST['tracking_id'];	
				$trID 	= explode("_",$trID);
				$pid 	= $trID[0];
				
				
				update_post_meta($pid, 'outstanding',"1");
				//update_post_meta($pid, 'paid_user',"1");
				//update_post_meta($pid, "paid_user_date", current_time('timestamp',0));
				update_post_meta($pid, "adaptive_done", "started");
				
				//$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);				
				//ProjectTheme_send_email_when_on_completed_project($pid, $projectTheme_get_winner_bid->uid, $projectTheme_get_winner_bid->bid);
				
			}
		}
		
		if(isset($_GET['return_chained']))
		{
			$ret_id = $_GET['return_chained'];	
			$pid_d = get_option('adaptive_payment_ID_thing_' . $ret_id);
			
		
			wp_redirect(get_permalink(get_option('ProjectTheme_my_account_awaiting_completion_id')));
			exit;
		}
		
		//------------
		
		if($my_pid == $ProjectTheme_post_new_page_id)
		{
			if(!is_user_logged_in())	{ wp_redirect(ProjectTheme_login_url(). '?redirect_to=' . urlencode(get_permalink($ProjectTheme_post_new_page_id))); exit; }
			global $current_user;
			get_currentuserinfo();
			
			if(!ProjectTheme_is_user_business($current_user->ID)) { wp_redirect(get_bloginfo('siteurl')); exit; }
			
			if(!isset($_GET['projectid'])) $set_ad = 1; else $set_ad = 0;
			
			
			if(!empty($_GET['projectid']))
			{
				$my_main_post = get_post($_GET['projectid']);
				if($my_main_post->post_author != $current_user->ID)
				{
					wp_redirect(get_bloginfo('siteurl')); exit;	
				}
				
			}
			
			if($set_ad == 1)
			{
				$pid 		= ProjectTheme_get_auto_draft($current_user->ID);
				wp_redirect(ProjectTheme_post_new_with_pid_stuff_thg($pid));
			}
			
			get_template_part( 'lib/post_new_post');		
		}
		
		//-------------
		
		if($my_pid == $ProjectTheme_my_account_page_id)
		{
			if(!is_user_logged_in())	{ wp_redirect(ProjectTheme_login_url()); exit; }	
		}
		
		
//----------------------------------------------------
		if($p_action == "add_another_user")
		{
			get_template_part('lib/add_another_user_project');
			die();
		}
		if($p_action == "adserv_checklist")
		{
			get_template_part('lib/adserv_new');
			die();
		}
		if($p_action == "close_project")
	    {
			get_template_part('lib/close_project');
	        die();	
		}
		if($p_action == "delete_timeoff")
		{
			get_template_part('lib/my_account/delete_timeoff');
			die();
		}
		if(isset($_GET['autosuggest']))
		{
			get_template_part ('autosuggest');
		}
		if ($p_action == "delete_backup")
		{
			get_template_part('lib/my_account/delete_emp_exp_backup');
			die();
		}
		if($p_action == "edit_scope")
		{
			get_template_part('lib/scope_new');
			die();
		}
		if ($p_action == "edit_checklist")
		{
			get_template_part('lib/edit_contract_checklist');
			die();
		}
		if ($p_action == "edit_vendor_payable")
		{
			get_template_part('lib/edit_vendor_payable');
			die();
		}
		if ($p_action == "employee_expense")
		{
			get_template_part('lib/new_employee_expense');
			die();
		}
		if($p_action=="edit_employee_expense_admin")
		{
			get_template_part('lib/edit_employee_expense_admin');
			die();
		}
		if($p_action == "project_responsibilities")
		{
			get_template_part('lib/project_responsibilities');
			die();
		}
		if($p_action == "edit_opportunity")
		{
			get_template_part('lib/edit_opportunity_checklist');
			die();
		}
		if ($p_action == "edit_backup")
		{
			get_template_part('lib/my_account/edit_emp_exp_backup');
			die();
		}
		
		if($p_action == "detailed_project_hours")
		{
			get_template_part('lib/reports/detailed_project_hours');
			die();
		}
		
		if($p_action == "detailed_employee_activity")
		{
			get_template_part('lib/reports/detailed_employee_activity');
			die();
		}
		
		if($p_action == "detailed_project_expenses")
		{
			get_template_part('lib/reports/detailed_project_expenses');
			die();
		}
		
		if($p_action == "expense_invoice_summary")
		{
			get_template_part('lib/expense_invoice_summary');
			die();
		}
		
		if ($p_action == "edit_timesheet")
		{
			get_template_part('lib/edit_timesheet');
			die();
		}
		
		if ($p_action == "employee_projected_hours")
		{
			get_template_part('lib/public_user_projected_time');
			die();
		}
		
		if ($p_action == "employee_targeted_hours")
		{
			get_template_part('lib/public_user_targeted_time');
			die();
		}

		if ($p_action == "employee_projected_revenue")
		{
			get_template_part('lib/public_user_projected_revenue');
			die();
		}		
		if($p_action == "farm_view")
		{
			get_template_part('lib/farm_view');
			die();
		}
		if($p_action == "move_employee_expenses")
		{
			get_template_part('lib/employee_expenses/move_employee_expenses');
			die();
		}
		if ($p_action == "project_projected_hours")
		{
			get_template_part('lib/public_project_projected_time');
			die();
		}
		if($p_action == "edit_client_contact")
		{
			get_template_part('lib/edit_client_contact');
			die();
		}

		if ($p_action == "employee_expense_view")
		{
			get_template_part('lib/employee_expense_view');
			die();
		}
		
		if ($p_action == "project_projected_revenue")
		{
			get_template_part('lib/public_project_projected_revenue');
			die();
		}
		
		if ($p_action == "edit_employee_expense")
		{
			get_template_part('lib/my_account/edit_employee_expense');
			die();
		}		
		
		if ($p_action == "project_invoice")
		{
			get_template_part('lib/new_project_invoice');
			die();
		}
		
		if($p_action == "edit_project_rates")
		{
			get_template_part('lib/edit_project_rates');
			die();
		}
		
		if($p_action == "add_project_docs")
		{
			get_template_part('lib/add_project_docs');
			die();
		}
		
		if($p_action == "detailed_contracts_report")
		{
			get_template_part('lib/reports/detailed_contracts_report');
			die();
		}
		
		if ($p_action == "assign_role")
		{
			get_template_part('lib/assign_role');
			die();
		}		
		
		if ($p_action == "project_card")
		{
			get_template_part('lib/project_card');
			die();
		}
		
		if ($p_action == "user_profile")
	    {
			get_template_part('lib/user-profile');
	        die();	
		}
		
		if ($p_action == "adservs")
	    {
			get_template_part('lib/adserv_view');
	        die();	
		}
		
		if (isset($_GET['get_files_panel']))
	    {
			get_template_part('lib/get_files_panel');
	        die();	
		}
		
		if (isset($_GET['get_bidding_panel']))
	    {
			get_template_part('lib/bidding-panel');
	        die();	
		}
		
		if (isset($_GET['print_invoice']))
	    {
			get_template_part('lib/invoice_pdf');
	        die();	
		}
		
		if (isset($_GET['get_message_board']))
	    {
			get_template_part('lib/message-board');
	        die();	
		}	   
	}
	

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


function projectTheme_clear_sums_of_cash($cash)
{
	$cash = str_replace(" ","",$cash);
	$cash = str_replace(",","",$cash);
	//$cash = str_replace(".","",$cash);
	
	return strip_tags($cash);
}


 
function ProjectTheme_send_email_subscription($pid)
{

	$opt = get_post_meta($pid,'subscription_email_senta',true);
	
	if(empty($opt))
	{

		$cat_qw		= '( 0=1 '; 
		$cat 		= wp_get_object_terms($pid, 'project_cat');
		
		foreach($cat as $cc)
		$cat_qw .= " or cats.catid='".$cc->term_id."' ";	
		$cat_qw .= ')';
		
		//------------------------
		
		$loc_qw		= '( 0=1 '; 
		$loc 		= wp_get_object_terms($pid, 'project_location');
		
		foreach($loc as $cc)
		$loc_qw .= " or locsa.catid='".$cc->term_id."' ";	
		$loc_qw .= ')';
		
		//------------------------
 
		
	 
		global $wpdb;
		//BillyB try to fix no email for subcategories by removing "distinct" from "select distinct..."
		$post 	= get_post($pid);
		$s 		= "select cats.uid from ".$wpdb->prefix."project_email_alerts cats, ".$wpdb->prefix."project_email_alerts_locs locsa  
					where $cat_qw AND $loc_qw AND cats.uid=locsa.uid";
		$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
		if($ProjectTheme_enable_project_location == "no")
		{
			$s = "select distinct cats.uid from ".$wpdb->prefix."project_email_alerts cats where $cat_qw ";	
			
		}
 		
 
		 
		$r 		= $wpdb->get_results($s);
 
		
		foreach($r as $row):
			
			
			$enable 	= get_option('ProjectTheme_subscription_email_enable');
			$subject 	= get_option('ProjectTheme_subscription_email_subject');
			$message 	= get_option('ProjectTheme_subscription_email_message');	
			
			if($enable != "no"):
			
				$user 			= get_userdata($row->uid);
				$site_login_url = ProjectTheme_login_url();
				$site_name 		= get_bloginfo('name');
				$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
				
		
				$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
				$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
				
				$tag		= 'ProjectTheme_subscription_email';
				$find 		= apply_filters( $tag . '_find', 	$find );
				$replace 	= apply_filters( $tag . '_replace', $replace );
				
				$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
				$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
				
				//---------------------------------------------
				
				$email = get_bloginfo('admin_email');
				ProjectTheme_send_email($user->user_email, $subject, $message);
				 
				 
				
				endif;
				 
		endforeach;
		
		update_post_meta($pid,'subscription_email_senta',"111a");
	
	}
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
//BillyB don't think this is needed
function ProjectTheme_get_credits($uid)
{
	$c = get_user_meta($uid,'credits',true);
	if(empty($c))
	{
		update_user_meta($uid,'credits',"0");	
		return 0;
	}
	
	return $c;
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_send_email($recipients, $subject = '', $message = '') {
		
	$ProjectTheme_email_addr_from 	= get_option('ProjectTheme_email_addr_from');	
	$ProjectTheme_email_name_from  	= get_option('ProjectTheme_email_name_from');
	
	$message = stripslashes($message);
	$subject = stripslashes($subject); 
	
	$hh = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=' . get_bloginfo('charset'),         
        sprintf( 'X-Mailer: PHP/%s', phpversion() ),
     );
	
	if(empty($ProjectTheme_email_name_from)) $ProjectTheme_email_name_from  = "Project Theme";
	if(empty($ProjectTheme_email_addr_from)) $ProjectTheme_email_addr_from  = "projectTheme@wordpress.org";
		
	$headers = 'From: '. $ProjectTheme_email_name_from .' <'. $ProjectTheme_email_addr_from .'>' . PHP_EOL;
	$ProjectTheme_allow_html_emails = get_option('ProjectTheme_allow_html_emails');
	if($ProjectTheme_allow_html_emails != "yes") $html = false;
	else $html = true;

	$oktosend = true;
	$oktosend = apply_filters('ProjectTheme_ok_to_send_email',$oktosend);
	 
	if($oktosend)
	{
		if ($html) {
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
			$mailtext = "<html><head><title>" . $subject . "</title></head><body>" . nl2br($message) . "</body></html>";
			return wp_mail($recipients, $subject, $mailtext, $hh);
			
		} else {
			$headers .= "MIME-Version: 1.0\n";
			$headers .= "Content-Type: text/plain; charset=\"". get_bloginfo('charset') . "\"\n";
			$message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $message);
			$message = preg_replace('|&amp;|', '&', $message);
			$mailtext = wordwrap(strip_tags($message), 80, "\n");
			return wp_mail($recipients, stripslashes($subject), stripslashes($mailtext), $hh);
		}

	}

}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_project_category_fields($catid, $pid = '', $step = '')
{ 
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_custom_fields where tp!='6' order by ordr asc";	
	$r = $wpdb->get_results($s);
	
	$sms = 0;
	$sms = apply_filters('ProjectTheme_get_get_cat_fields_thing', $sms);
	
	if($sms == 1)
	{
		$s = "select * from ".$wpdb->prefix."project_custom_fields order by ordr asc";	
		$r = $wpdb->get_results($s);
	}
	
	
	if(!empty($step))
	{
		$s = "select * from ".$wpdb->prefix."project_custom_fields where step_me='$step' order by ordr asc";	
		$r = $wpdb->get_results($s);
	}
	
	$arr1 = array(); $i = 0;
	
	foreach($r as $row)

	{
		$ims = $row->id;
		$name = $row->name;
		$tp = $row->tp;
		
		if($row->cate == 'all')
		{ 
			$arr1[$i]['id'] = $ims; 
			$arr1[$i]['name'] = $name; 
			$arr1[$i]['tp'] = $tp; $i++; 
		
		}
		else
		{
			$se = "select * from ".$wpdb->prefix."project_custom_relations where custid='$ims'";
			$re = $wpdb->get_results($se);
			
			if(count($re) > 0)
			foreach($re as $rowe) // = mysql_fetch_object($re))
			{
				if($rowe->catid == $catid)
				{
					$arr1[$i]['id'] = $ims; 
					$arr1[$i]['name'] = $name; 
					$arr1[$i]['tp'] = $tp;
					$i++;
					break;	
				}
			}
		}
	}

	$arr = array();
	$i = 0;
	
	for($j=0;$j<count($arr1);$j++)
	{
		$ids = $arr1[$j]['id'];
		$tp = $arr1[$j]['tp'];
		
		$arr[$i]['field_name']  = $arr1[$j]['name'];
		$arr[$i]['value']  = '<input type="hidden" value="'.$ids.'" name="custom_field_id[]" />';
		$arr[$i]['id']  =  $ids;
		
		if($tp == 1) 
		{
		
		$teka = !empty($pid) ? get_post_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
	
		$arr[$i]['value']  .= '<input class="full_wdth_me do_input_new" type="text" size="30" name="custom_field_value_'.$ids.'" 
		value="'.(isset($_POST['custom_field_value_'.$ids]) ? $_POST['custom_field_value_'.$ids] : $teka ).'" />';
		
		}
		
		if($tp == 5)
		{
		
			$teka 	= !empty($pid) ? get_post_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
			$value 	= isset($_POST['custom_field_value_'.$ids]) ? $_POST['custom_field_value_'.$ids] : $teka;
			
			$arr[$i]['value']  .= '<textarea rows="5" cols="40" name="custom_field_value_'.$ids.'">'.$value.'</textarea>';
		
		}
		
		if($tp == 3) //radio
		{
			$arr[$i]['value']  = '';
			
				$s2 = "select * from ".$wpdb->prefix."project_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					$teka 	= !empty($pid) ? get_post_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
					if(isset($_POST['custom_field_value_'.$ids]))
					{
						if($_POST['custom_field_value_'.$ids] == $row2->valval) $value = 'checked="checked"';
						else $value = '';
					}
					elseif(!empty($pid))
					{
						$v = get_post_meta($pid, 'custom_field_ID_'.$ids, true);
						if($v == $row2->valval) $value = 'checked="checked"';
						else $value = ''; 
					
					}				
					else $value = '';
					
					$arr[$i]['value']  .= '<input type="radio" '.$value.' value="'.$row2->valval.'" name="custom_field_value_'.$ids.'"> '.$row2->valval.'<br/>';
				}
		}
		
		if($tp == 6) //html
		{
			$arr[$i]['value']  = $row->content_box6;
			
		}
		
		
		if($tp == 4) //checkbox
		{
			$arr[$i]['value']  = '';
			
				$s2 = "select * from ".$wpdb->prefix."project_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					$teka 	= !empty($pid) ? get_post_meta($pid, 'custom_field_ID_'.$ids) : "" ;
					$ty = 0;
					if(!empty($teka))
					{	
						$ty = 1;
						foreach($teka as $te)
						{
							if($te == $row2->valval) { $ty = 2;  $tekao = "checked='checked'"; break; }
						}	
						
									
					}
					else $tekao = '';
					
					if($ty == 1) $tekao = '';
					
					$value 	= isset($_POST['custom_field_value_'.$ids]) ? "checked='checked'" : $tekao;
					
					$arr[$i]['value']  .= '<input '.$value.' type="checkbox" value="'.$row2->valval.'" name="custom_field_value_'.$ids.'[]"> '.$row2->valval.'<br/>';
				}
		}
		
		if($tp == 2) //select
		{
			$arr[$i]['value']  = '<select name="custom_field_value_'.$ids.'" />';
			
				$s2 = "select * from ".$wpdb->prefix."project_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				$teka 	= !empty($pid) ? get_post_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					
				
						
							if($teka == $row2->valval) { $tekak = "selected='selected'";  }
							else  $tekak = '';
					
				
					
					$arr[$i]['value']  .= '<option '.$tekak.' value="'.$row2->valval.'">'.$row2->valval.'</option>';
				
				}
			$arr[$i]['value']  .= '</select>';
		}
		
		$i++;
	}
	
	return $arr;
}

function ProjectTheme_get_users_category_fields($catid, $pid = '')
{ 
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_user_custom_fields order by ordr asc";	
	$r = $wpdb->get_results($s);
	
	$arr1 = array(); $i = 0;
	
	foreach($r as $row)
	{
		$ims = $row->id;
		$name = $row->name;
		$tp = $row->tp;
		
		if($row->cate == 'all')
		{ 
			$arr1[$i]['id'] = $ims; 
			$arr1[$i]['name'] = $name; 
			$arr1[$i]['tp'] = $tp; $i++; 
		
		}
		else
		{
			$se = "select * from ".$wpdb->prefix."project_user_custom_relations where custid='$ims'";
			$re = $wpdb->get_results($se);
				
			if(count($re) > 0)
			foreach($re as $rowe) // = mysql_fetch_object($re))
			{
				if(count($catid) > 0)
				foreach($catid as $id_of_cat)
				{
					
					if($rowe->catid == $id_of_cat)
					{
						$flag_me = 1;
						for($k=0;$k<count($arr1);$k++)
						{
							if(	$arr1[$k]['id'] 	== $ims	) {  $flag_me = 0; break; }						
						}
						
						if($flag_me == 1)
						{
							$arr1[$i]['id'] 	= $ims; 
							$arr1[$i]['name'] 	= $name; 
							$arr1[$i]['tp'] 	= $tp;
							$i++;
						}
					}
				}
			}
		}
	}

	$arr = array();
	$i = 0;
	
	for($j=0;$j<count($arr1);$j++)
	{
		$ids = $arr1[$j]['id'];
		$tp = $arr1[$j]['tp'];
		
		$arr[$i]['field_name']  = $arr1[$j]['name'];
		$arr[$i]['id']  = '<input type="hidden" value="'.$ids.'" name="custom_field_id[]" />';
		
		if($tp == 1) 
		{
		
		$teka = !empty($pid) ? get_user_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
	
		$arr[$i]['value']  = '<input class="do_input" type="text" size="30" name="custom_field_value_'.$ids.'" 
		value="'.(isset($_POST['custom_field_value_'.$ids]) ? $_POST['custom_field_value_'.$ids] : $teka ).'" />';
		
		}
		
		if($tp == 5)
		{
		
			$teka 	= !empty($pid) ? get_user_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
			$value 	= isset($_POST['custom_field_value_'.$ids]) ? $_POST['custom_field_value_'.$ids] : $teka;
			
			$arr[$i]['value']  = '<textarea class="do_input" rows="5" cols="40" name="custom_field_value_'.$ids.'">'.$value.'</textarea>';
		
		}
		
		if($tp == 3) //radio
		{
			$arr[$i]['value']  = '';
			
				$s2 = "select * from ".$wpdb->prefix."project_user_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					$teka 	= !empty($pid) ? get_user_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
					if(isset($_POST['custom_field_value_'.$ids]))
					{
						if($_POST['custom_field_value_'.$ids] == $row2->valval) $value = 'checked="checked"';
						else $value = '';
					}
					elseif(!empty($pid))
					{
						$v = get_user_meta($pid, 'custom_field_ID_'.$ids, true);
						if($v == $row2->valval) $value = 'checked="checked"';
						else $value = ''; 
					
					}				
					else $value = '';
					
					$arr[$i]['value']  .= '<input class="do_input" type="radio" '.$value.' value="'.$row2->valval.'" name="custom_field_value_'.$ids.'"> '.$row2->valval.'<br/>';
				}
		}
		
		
		if($tp == 4) //checkbox
		{
			$arr[$i]['value']  = '';
			
				$s2 = "select * from ".$wpdb->prefix."project_user_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					$teka 	= !empty($pid) ? get_user_meta($pid, 'custom_field_ID_'.$ids) : "" ;
					$ty = 0;
					if(!empty($teka))
					{	
						$ty = 1;
						foreach($teka as $te)
						{
							if($te == $row2->valval) { $ty = 2;  $tekao = "checked='checked'"; break; }
						}	
						
									
					}
					else $tekao = '';
					
					if($ty == 1) $tekao = '';
					
					$value 	= isset($_POST['custom_field_value_'.$ids]) ? "checked='checked'" : $tekao;
					
					$arr[$i]['value']  .= '<input class="do_input" '.$value.' type="checkbox" value="'.$row2->valval.'" name="custom_field_value_'.$ids.'[]"> '.$row2->valval.'<br/>';
				}
		}
		
		if($tp == 2) //select
		{
			$arr[$i]['value']  = '<select class="do_input" name="custom_field_value_'.$ids.'" />';
			
				$s2 = "select * from ".$wpdb->prefix."project_user_custom_options where custid='$ids' order by ordr ASC ";
				$r2 = $wpdb->get_results($s2);
				
				$teka 	= !empty($pid) ? get_user_meta($pid, 'custom_field_ID_'.$ids, true) : "" ;
				
				if(count($r2) > 0)
				foreach($r2 as $row2) // = mysql_fetch_object($r2))
				{
					
				
						
							if($teka == $row2->valval) { $tekak = "selected='selected'";  }
							else  $tekak = '';
					
				
					
					$arr[$i]['value']  .= '<option '.$tekak.' value="'.$row2->valval.'">'.$row2->valval.'</option>';
				
				}
			$arr[$i]['value']  .= '</select>';
		}
		
		$i++;
	}
	
	return $arr;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_categories_slug($taxo, $selected = "", $include_empty_option = "", $ccc = "")
{
	$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
	$terms = get_terms( $taxo, $args );
	
	$ret = '<select name="'.$taxo.'_cat" class="'.$ccc.'" id="'.$ccc.'">';
	if(!empty($include_empty_option)){
		
		if($include_empty_option == "1") $include_empty_option = "Select";
	 	$ret .= "<option value=''>".$include_empty_option."</option>";
	 }
	
	if(empty($selected)) $selected = -1;
	
	foreach ( $terms as $term )
	{
		$id = $term->slug;
		$ide = $term->term_id;
		
		$ret .= '<option '.($selected == $id ? "selected='selected'" : " " ).' value="'.$id.'">'.$term->name.'</option>';
		
		$args = "orderby=name&order=ASC&hide_empty=0&parent=".$ide;
		$sub_terms = get_terms( $taxo, $args );	
		
		foreach ( $sub_terms as $sub_term )
		{
			$sub_id = $sub_term->slug; 
			$ret .= '<option '.($selected == $sub_id ? "selected='selected'" : " " ).' value="'.$sub_id.'">&nbsp; &nbsp;|&nbsp;  '.$sub_term->name.'</option>';
			
			$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id;
			$sub_terms2 = get_terms( $taxo, $args2 );	
			
			foreach ( $sub_terms2 as $sub_term2 )
			{
				$sub_id2 = $sub_term2->term_id; 
				$ret .= '<option '.($selected == $sub_id2 ? "selected='selected'" : " " ).' value="'.$sub_id2.'">&nbsp; &nbsp; &nbsp; &nbsp;|&nbsp;  
				'.$sub_term2->name.'</option>';
			
			}
			
		}
		
	}
	
	$ret .= '</select>';
	
	return $ret;
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*

**************************************************************/
function ProjectTheme_get_categories($taxo, $selected = "", $include_empty_option = "", $ccc = "")
{
	$args = "orderby=name&order=ASC&hide_empty=0&parent=0";
	$terms = get_terms( $taxo, $args );
	
	$ret = '<select name="'.$taxo.'_cat" class="'.$ccc.'" id="'.$ccc.'">';
	if(!empty($include_empty_option)) $ret .= "<option value=''>".$include_empty_option."</option>";
	
	if(empty($selected)) $selected = -1;
	
	foreach ( $terms as $term )
	{
		$id = $term->term_id;
		
		$ret .= '<option '.($selected == $id ? "selected='selected'" : " " ).' value="'.$id.'">'.$term->name.'</option>';
		
		$args = "orderby=name&order=ASC&hide_empty=0&parent=".$id;
		$sub_terms = get_terms( $taxo, $args );	
		
		foreach ( $sub_terms as $sub_term )
		{
			$sub_id = $sub_term->term_id; 
			$ret .= '<option '.($selected == $sub_id ? "selected='selected'" : " " ).' value="'.$sub_id.'">&nbsp; &nbsp;|&nbsp;  '.$sub_term->name.'</option>';
			
			$args2 = "orderby=name&order=ASC&hide_empty=0&parent=".$sub_id;
			$sub_terms2 = get_terms( $taxo, $args2 );	
			
			foreach ( $sub_terms2 as $sub_term2 )
			{
				$sub_id2 = $sub_term2->term_id; 
				$ret .= '<option '.($selected == $sub_id2 ? "selected='selected'" : " " ).' value="'.$sub_id2.'">&nbsp; &nbsp; &nbsp; &nbsp;|&nbsp; 
				 '.$sub_term2->name.'</option>';
			
			}
		}
		
	}
	
	$ret .= '</select>';
	
	return $ret;
	
}



/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_framework_init_widgets()
{
	register_sidebar( array(
		'name' => __( 'Single Page Sidebar', 'ProjectTheme' ),
		'id' => 'single-widget-area',
		'description' => __( 'The sidebar area of the single blog post', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
		register_sidebar( array(
		'name' => __( 'Other Page Sidebar', 'ProjectTheme' ),
		'id' => 'other-page-area',
		'description' => __( 'The sidebar area of any other page than the defined ones', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	
	
	
	register_sidebar( array(
		'name' => __( 'Home Page Sidebar - Right', 'ProjectTheme' ),
		'id' => 'home-right-widget-area',
		'description' => __( 'The right sidebar area of the homepage', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	
	
	
	register_sidebar( array(
		'name' => __( 'Home Page Sidebar - Left', 'ProjectTheme' ),
		'id' => 'home-left-widget-area',
		'description' => __( 'The left sidebar area of the homepage', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	
	
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'ProjectTheme' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'ProjectTheme' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'ProjectTheme' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'ProjectTheme' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'ProjectTheme' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	

		
			register_sidebar( array(
			'name' => __( 'ProjectTheme - Project Single Sidebar', 'ProjectTheme' ),
			'id' => 'project-widget-area',
			'description' => __( 'The sidebar of the single project page', 'ProjectTheme' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		
		
			register_sidebar( array(
			'name' => __( 'ProjectTheme - HomePage Area','ProjectTheme' ),
			'id' => 'main-page-widget-area',
			'description' => __( 'The sidebar for the main page, just under the slider.', 'ProjectTheme' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		
		
			register_sidebar( array(
			'name' => __( 'ProjectTheme - Stretch Wide Sidebar','ProjectTheme' ),
			'id' => 'main-stretch-area',
			'description' => __( 'The sidebar sidewide stretched, just under the slider.', 'ProjectTheme' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_insert_pic_media_lib($author, $pid, $uri, $path, $post_title, $another_reserved1 = '')
{
	require_once(ABSPATH . '/wp-admin/includes/image.php');
		$wp_filetype = wp_check_filetype(basename($path), null );
				
			$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_author' => $author,
			'guid' => $uri,
			'post_parent' => $pid,
			'post_type' => 'attachment',
			'post_title' => $post_title
			);
			
			$id = wp_insert_attachment($attachment, $path, $pid);
			
			if(!empty($another_reserved1))
			{
				update_post_meta($id, 'another_reserved1', '1');	
			}
			
			$dt = wp_generate_attachment_metadata($id, $path);
			wp_update_attachment_metadata($id, $dt);	
			return $id;
}
	include('lib/my-upload.php');
	
	

//*********************** AJAX STUFF **************************

add_action('wp_ajax_new_package_action', 	'ProjectTheme_new_package_action');
add_action('wp_ajax_delete_package', 		'ProjectTheme_delete_package');
add_action('wp_ajax_update_package', 		'ProjectTheme_update_package');

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_update_package()
{
	if($_POST['action'] == "update_package")
	{

		$bidding_interval_name_cell 	= trim($_POST['bidding_interval_name_cell']);
		$high_limit_cell 				= trim($_POST['high_limit_cell']);
		$low_limit_cell 				= trim($_POST['low_limit_cell']);
		$id = $_POST['id'];
		
		global $wpdb;
		
		$s = "update ".$wpdb->prefix."project_bidding_intervals set bidding_interval_name='$bidding_interval_name_cell', 
		low_limit='$low_limit_cell' , high_limit='$high_limit_cell' where id='$id'";	
		$wpdb->query($s);
		
		
	}
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_delete_package()
{
	if($_POST['action'] == "delete_package")
	{

		$id 	= trim($_POST['id']);

		global $wpdb;
		
		$s = "delete from ".$wpdb->prefix."project_bidding_intervals where id='$id'";	
		$wpdb->query($s);
		
	}
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_new_package_action()
{
	if($_POST['action'] == "new_package_action")
	{

		$bidding_interval_name_new 	= trim($_POST['bidding_interval_name_new']);
		$low_limit_new 				= trim($_POST['low_limit_new']);
		$high_limit_new 			= trim($_POST['high_limit_new']);
	
		global $wpdb;
		
		$s = "insert into ".$wpdb->prefix."project_bidding_intervals (bidding_interval_name, low_limit, high_limit) 
		values('$bidding_interval_name_new', '$low_limit_new', '$high_limit_new')";	
		$wpdb->query($s);
		
		$s = "select id from ".$wpdb->prefix."project_bidding_intervals where 
		bidding_interval_name='$bidding_interval_name_new' and low_limit='$low_limit_new' and high_limit='$high_limit_new'";	
		$r = $wpdb->get_results($s);
		$row = $r[0];
		
		$arr = array();
		
		$arr['bidding_interval_name'] 	= $bidding_interval_name_new;
		$arr['low_limit'] 				= $low_limit_new;
		$arr['high_limit'] 				= $high_limit_new;
		$arr['id'] 						= $row->id;
		
		echo json_encode($arr);
	}
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_send_email_posted_project_not_approved_admin($pid)
{
	$enable 	= get_option('ProjectTheme_new_project_email_not_approve_admin_enable');
	$subject 	= get_option('ProjectTheme_new_project_email_not_approve_admin_subject');
	$message 	= get_option('ProjectTheme_new_project_email_not_approve_admin_message');	
	
	$opt = get_post_meta($pid, 'ProjectTheme_send_email_posted_project_not_approved_admin', true);
	
	if($enable != "no" and empty($opt)):
	
		update_post_meta($pid, 'ProjectTheme_send_email_posted_project_not_approved_admin', '1');
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_posted_project_not_approved_admin';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = get_bloginfo('admin_email');
		ProjectTheme_send_email($email, $subject, $message);
	
	endif;	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_send_email_on_completed_project_to_bidder($pid, $bidder_id)
{
	$enable 	= get_option('ProjectTheme_completed_project_bidder_email_enable');
	$subject 	= get_option('ProjectTheme_completed_project_bidder_email_subject');
	$message 	= get_option('ProjectTheme_completed_project_bidder_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($bidder_id);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_completed_project_to_bidder';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_completed_project_to_owner($pid) // owner = post->post_author
{
	$enable 	= get_option('ProjectTheme_completed_project_owner_email_enable');
	$subject 	= get_option('ProjectTheme_completed_project_owner_email_subject');
	$message 	= get_option('ProjectTheme_completed_project_owner_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_completed_project_to_owner';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}

//-----
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_delivered_project_to_bidder($pid, $bidder_id)
{
	$enable 	= get_option('ProjectTheme_delivered_project_bidder_email_enable');
	$subject 	= get_option('ProjectTheme_delivered_project_bidder_email_subject');
	$message 	= get_option('ProjectTheme_delivered_project_bidder_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($bidder_id);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_delivered_project_to_bidder';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = get_bloginfo('admin_email');
		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_delivered_project_to_owner($pid) // owner = post->post_author
{
	$enable 	= get_option('ProjectTheme_delivered_project_owner_email_enable');
	$subject 	= get_option('ProjectTheme_delivered_project_owner_email_subject');
	$message 	= get_option('ProjectTheme_delivered_project_owner_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_completed_project_to_owner';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_send_email_on_message_board_owner($pid, $owner_id, $sender_id)
{
	$enable 	= get_option('ProjectTheme_message_board_owner_email_enable');
	$subject 	= get_option('ProjectTheme_message_board_owner_email_subject');
	$message 	= get_option('ProjectTheme_message_board_owner_email_message');	
	
	if($enable != "no"):
	
		$owner_id 			= get_userdata($owner_id);
		$sender_id			= get_userdata($sender_id);
		
		
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$project 		= get_post($pid);

		$find 		= array('##username##', '##message_owner_username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##','##project_name##','##project_link##');
   		$replace 	= array($owner_id->user_login, $sender_id->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $project->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_message_board_owner';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($owner_id->user_email, $subject, $message);
	
	endif;
}


function ProjectTheme_send_email_on_message_board_bidder($pid, $owner_id, $sender_id)

{
	$enable 	= get_option('ProjectTheme_message_board_bidder_email_enable');
	$subject 	= get_option('ProjectTheme_message_board_bidder_email_subject');
	$message 	= get_option('ProjectTheme_message_board_bidder_email_message');	
	
	if($enable != "no"):
	
		$owner_id 			= get_userdata($owner_id);
		$sender_id			= get_userdata($sender_id);
		
		
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$project = get_post($pid);

		$find 		= array('##project_username##', '##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##','##project_name##','##project_link##');
   		$replace 	= array($owner_id->user_login, $sender_id->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $project->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_on_message_board_bidder';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($sender_id->user_email, $subject, $message);
	
	endif;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


function ProjectTheme_send_email_on_priv_mess_received($sender_uid, $receiver_uid)
{
	$enable 	= get_option('ProjectTheme_priv_mess_received_email_enable');
	$subject 	= get_option('ProjectTheme_priv_mess_received_email_subject');
	$message 	= get_option('ProjectTheme_priv_mess_received_email_message');	
	
	if($enable != "no"):
	
		$user 			= get_userdata($receiver_uid);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$sndr			= get_userdata($sender_uid);

		$find 		= array('##sender_username##', '##receiver_username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##');
   		$replace 	= array($sndr->user_login, $user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url);
		
		$tag		= 'ProjectTheme_send_email_on_priv_mess_received';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_send_email_on_rated_user($pid, $rated_user_id)
{
	$enable 	= get_option('ProjectTheme_rated_user_email_enable');
	$subject 	= get_option('ProjectTheme_rated_user_email_subject');
	$message 	= get_option('ProjectTheme_rated_user_email_message');	
	
	if($enable != "no"):
		global $wpdb;
		$post 			= get_post($pid);
		$user 			= get_userdata($rated_user_id);
		$companyid		= get_user_meta($rated_user_id,"companyid",true);
		$companyinfo	= "select * from ".$wpdb->prefix."company where id='$companyid'";
		$companyresult	= $wpdb->get_results($companyinfo);
		$companyname	= $companyresult[0]->company_name;
		
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		
		$s = "select * from ".$wpdb->prefix."project_ratings where pid='$pid' AND touser='$companyid'";
		$r = $wpdb->get_results($s);
		$row = $r[0];
		
		$rating 		= ceil($row->grade/2);
		$comment 		= $row->comment;
		$constructive	= $row->constructive;

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##','##rating##','##comment##', '##constructive##');
   		$replace 	= array($companyname, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), $rating, $comment, $constructive);
		
		$tag		= 'ProjectTheme_send_email_on_rated_user';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_win_to_loser($pid, $loser_uid)
{
	$enable 	= get_option('ProjectTheme_won_project_loser_email_enable');
	$subject 	= get_option('ProjectTheme_won_project_loser_email_subject');
	$message 	= get_option('ProjectTheme_won_project_loser_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($loser_uid);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		
		$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);
		
		$usrnm = get_userdata($projectTheme_get_winner_bid->uid);
		$winner_bid_username = $usrnm->user_login;
		$winner_bid_value = projecttheme_get_show_price($projectTheme_get_winner_bid->bid);
		
		$skk = projectTheme_get_bid_by_uid($pid, $loser_uid);
		
		$user_bid_value 		= projecttheme_get_show_price($skk->bid);
		
		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##',
		'##user_bid_value##','##winner_bid_username##','##winner_bid_value##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), 
		$user_bid_value,$winner_bid_username,$winner_bid_value);
		
		$tag		= 'ProjectTheme_send_email_on_win_to_loser';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_win_to_owner($pid, $winner_uid)
{
	$enable 	= get_option('ProjectTheme_won_project_owner_email_enable');
	$subject 	= get_option('ProjectTheme_won_project_owner_email_subject');
	$message 	= get_option('ProjectTheme_won_project_owner_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		
		$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);
		
		$usrnm = get_userdata($winner_uid);
		$winner_bid_username = $usrnm->user_login;
		$winner_bid_value = projecttheme_get_show_price($projectTheme_get_winner_bid->bid);
		
		//--------------------------------------------------------------------------
		
		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##','##winner_bid_value##','##winner_bid_username##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid),
		$winner_bid_value,$winner_bid_username );
		
		$tag		= 'ProjectTheme_send_email_on_win_to_owner';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_on_win_to_bidder($pid, $winner_uid)
{
	$enable 	= get_option('ProjectTheme_won_project_winner_email_enable');
	$subject 	= get_option('ProjectTheme_won_project_winner_email_subject');
	$message 	= get_option('ProjectTheme_won_project_winner_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($winner_uid);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		
		$projectTheme_get_winner_bid = projectTheme_get_winner_bid($pid);
		$usrnm = get_userdata($winner_uid);
		$winner_bid_username = $usrnm->user_login;
		$winner_bid_value = projecttheme_get_show_price($projectTheme_get_winner_bid->bid);
		
		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##','##winner_bid_value##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), $winner_bid_value);
		 
		$tag		= 'ProjectTheme_send_email_on_win_to_bidder';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//--------------------------------------

		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_when_bid_project_bidder($pid, $uid, $bid)
{
	$enable 	= get_option('ProjectTheme_bid_project_bidder_email_enable');
	$subject 	= get_option('ProjectTheme_bid_project_bidder_email_subject');
	$message 	= get_option('ProjectTheme_bid_project_bidder_email_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($uid);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$bid_val		= ProjectTheme_get_show_price($bid);

		$find 		= array('##username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##', '##bid_value##');
   		$replace 	= array($user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), $bid_val );
		
		
		//---------------------------------------------
		
		$tag		= 'ProjectTheme_send_email_when_bid_project_bidder';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		ProjectTheme_send_email($user->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_when_bid_project_owner($pid, $uid, $bid)
{
	$enable 	= get_option('ProjectTheme_bid_project_owner_email_enable');
	$subject 	= get_option('ProjectTheme_bid_project_owner_email_subject');
	$message 	= get_option('ProjectTheme_bid_project_owner_email_message');	
	
	
	
	if($enable != "no"):
		
		$bidder 		= get_userdata($uid);
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$bid_val		= ProjectTheme_get_show_price($bid);
		$bidder_username = $bidder->user_login;
		$author			= get_userdata($post->post_author);
		
		
		$find 		= array('##username##', '##bid_value##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##', '##bidder_username##');
   		$replace 	= array($user->user_login, $bid_val, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), $bidder_username);
		
		$tag		= 'ProjectTheme_send_email_when_bid_project_owner';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($author->user_email, $subject, $message);
	
	endif;
}


function ProjectTheme_send_email_when_on_completed_project($pid, $uid, $bid)
{
	$enable 	= get_option('ProjectTheme_payment_on_completed_project_enable');
	$subject 	= get_option('ProjectTheme_payment_on_completed_project_subject');
	$message 	= get_option('ProjectTheme_payment_on_completed_project_message');	
	
	
	
	if($enable != "no"):
		
		$bidder 		= get_userdata($uid);
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$bid_val		= ProjectTheme_get_show_price($bid);
		$bidder_username = $bidder->user_login;
		$author			= get_userdata($post->post_author);
		
		
		$find 		= array('##username##', '##bid_value##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##', '##bidder_username##');
   		$replace 	= array($user->user_login, $bid_val, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid), $bidder_username);
		
		$tag		= 'ProjectTheme_send_email_when_on_completed_project';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		ProjectTheme_send_email($author->user_email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_posted_project_approved_admin($pid)
{
	$enable 	= get_option('ProjectTheme_new_project_email_approve_admin_enable');
	$subject 	= get_option('ProjectTheme_new_project_email_approve_admin_subject');
	$message 	= get_option('ProjectTheme_new_project_email_approve_admin_message');	
	
	$opt = get_post_meta($pid,'ProjectTheme_send_email_posted_project_approved_admin', true);
	
	if($enable != "no" and empty($opt)):
		
		update_post_meta($pid,'ProjectTheme_send_email_posted_project_approved_admin', '1');
		
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		

		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_posted_project_approved_admin';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = get_bloginfo('admin_email');
		ProjectTheme_send_email($email, $subject, $message);
	
	endif;
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_posted_project_not_approved($pid)
{
	$enable 	= get_option('ProjectTheme_new_project_email_not_approved_enable');
	$subject 	= get_option('ProjectTheme_new_project_email_not_approved_subject');
	$message 	= get_option('ProjectTheme_new_project_email_not_approved_message');	
	
	$opt = get_post_meta($pid,'ProjectTheme_send_email_posted_project_not_approved', true);
	
	if($enable != "no" and empty($opt)):
	
		update_post_meta($pid,'ProjectTheme_send_email_posted_project_not_approved', '1');
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		$project_name 	= $post->post_title;

		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $project_name, get_permalink($pid));
		
		$tag		= 'ProjectTheme_send_email_posted_project_not_approved';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = $user->user_email;
		ProjectTheme_send_email($email, $subject, $message);
	
	endif;		
	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_send_email_posted_project_approved($pid)
{
	$enable 	= get_option('ProjectTheme_new_project_email_approved_enable');
	$subject 	= get_option('ProjectTheme_new_project_email_approved_subject');
	$message 	= get_option('ProjectTheme_new_project_email_approved_message');	
	
	$opt = get_post_meta($pid,'ProjectTheme_send_email_posted_project_approved', true);
	
	if($enable != "no" and empty($opt)):
		
		update_post_meta($pid,'ProjectTheme_send_email_posted_project_approved', '1');
		
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = ProjectTheme_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('ProjectTheme_my_account_page_id'));
		
		$post 		= get_post($pid);
		$project_name 	= $post->post_title;
		$project_link 	= get_permalink($pid);

		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##project_name##', '##project_link##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $project_name, $project_link);
		
		$tag		= 'ProjectTheme_send_email_posted_project_approved';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= ProjectTheme_replace_stuff_for_me($find, $replace, $message);
		$subject 	= ProjectTheme_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = $user->user_email;
		ProjectTheme_send_email($email, $subject, $message);
		
	endif;		
	
}

function Walleto_send_email_on_priv_mess_received($sender_uid, $receiver_uid)
{
	$enable 	= get_option('Walleto_priv_mess_received_email_enable');
	$subject 	= get_option('Walleto_priv_mess_received_email_subject');
	$message 	= get_option('Walleto_priv_mess_received_email_message');	
	
	if($enable != "no"):
	
		$user 			= get_userdata($receiver_uid);
		$site_login_url = Walleto_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('Walleto_my_account_page_id'));
		$sndr			= get_userdata($sender_uid);

		$find 		= array('##sender_username##', '##receiver_username##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##');
   		$replace 	= array($sndr->user_login, $user->user_login, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url);
		
		$tag		= 'Walleto_send_email_on_priv_mess_received';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= Walleto_replace_stuff_for_me($find, $replace, $message);
		$subject 	= Walleto_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------

		Walleto_send_email($user->user_email, $subject, $message);
	
	endif;
}

function Walleto_send_email_posted_item_not_approved_admin($pid)
{
	$enable 	= get_option('Walleto_new_item_email_not_approve_admin_enable');
	$subject 	= get_option('Walleto_new_item_email_not_approve_admin_subject');
	$message 	= get_option('Walleto_new_item_email_not_approve_admin_message');	
	
	if($enable != "no"):
	
		$post 			= get_post($pid);
		$user 			= get_userdata($post->post_author);
		$site_login_url = Walleto_login_url();
		$site_name 		= get_bloginfo('name');
		$account_url 	= get_permalink(get_option('Walleto_my_account_page_id'));
		

		$find 		= array('##username##', '##username_email##', '##site_login_url##', '##your_site_name##', '##your_site_url##' , '##my_account_url##', '##item_name##', '##item_link##');
   		$replace 	= array($user->user_login, $user->user_email, $site_login_url, $site_name, get_bloginfo('siteurl'), $account_url, $post->post_title, get_permalink($pid));
		
		$tag		= 'Walleto_send_email_posted_item_not_approved_admin';
		$find 		= apply_filters( $tag . '_find', 	$find );
		$replace 	= apply_filters( $tag . '_replace', $replace );
		
		$message 	= Walleto_replace_stuff_for_me($find, $replace, $message);
		$subject 	= Walleto_replace_stuff_for_me($find, $replace, $subject);
		
		//---------------------------------------------
		
		$email = get_bloginfo('admin_email');
		Walleto_send_email($email, $subject, $message);
	
	endif;	
	
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/


function projectTheme_rewrite_rules( $wp_rewrite )
{

		global $category_url_link, $location_url_link;
		$new_rules = array( 
		

		$category_url_link.'/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?project_cat='.$wp_rewrite->preg_index(1)."&feed=".$wp_rewrite->preg_index(2),
        $category_url_link.'/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?project_cat='.$wp_rewrite->preg_index(1)."&feed=".$wp_rewrite->preg_index(2),
        $category_url_link.'/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?project_cat='.$wp_rewrite->preg_index(1)."&paged=".$wp_rewrite->preg_index(2),
        $category_url_link.'/([^/]+)/?$' => 'index.php?project_cat='.$wp_rewrite->preg_index(1)
			


		);

		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

add_filter('term_link', 'ProjectTheme_post_tax_link_filter_function', 1, 3);

 function ProjectTheme_post_tax_link_filter_function( $post_link, $id = 0, $leavename = FALSE ) {
	global $category_url_link;
    return str_replace("project_cat",$category_url_link ,$post_link);
  }
	
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_get_total_nr_of_projects()
{
	$query = new WP_Query( "post_type=project&order=DESC&orderby=id&posts_per_page=1&paged=1" );	
	return $query->found_posts;
}

function ProjectTheme_get_total_nr_of_open_projects()
{
 	$args = array('paged' => '1' , 'posts_per_page' => '1', 'post_type' => 'project', 
	'meta_query' => array(array(
           'key' => 'closed',
           'value' => '0',
           'compare' => '=')));

	
	$query1 = new WP_Query($args);	
 
	return $query1->found_posts;
}

function ProjectTheme_get_total_nr_of_closed_projects()
{
	 	$args = array('paged' => '1' , 'posts_per_page' => '1', 'post_type' => 'project', 
			'meta_query' => array(array(
            'key' => 'closed',
            'value' => '1',
            'compare' => '=')));
	
	
	$query = new WP_Query( $args); 
	return $query->found_posts;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_send_email_to_project_payer($pid, $payer_user_id, $receiver_user_id, $amount, $pay_by_credits = '0')
{
	$paid_user = get_post_meta($pid, 'paid_user',true);
	
	if($paid_user == "0") : 
	
	$post 			= get_post($pid);
	$payer_user 	= get_userdata($payer_user_id);
	$datemade 		= current_time('timestamp',0);
	$perm 			= get_permalink($pid);
	$receiver_user 	= get_userdata($receiver_user_id);
	
	//-----------
	
	update_post_meta($pid, 'paid_user',"1");
	update_post_meta($pid, "paid_user_date", $datemade);
	$receiver_user_id = get_post_meta($pid, 'winner', true);
	
	//-----------
	
	$subject = sprintf(__("Your payment was completed for the project: %s",'ProjectTheme'), $post->post_title);
	$message = sprintf(__('You have paid for the project <a href="%s">%s</a> the amount of: %s %s to user: 
	<b>%s</b>',"ProjectTheme"),$perm,$post->post_title,$amount,$cure,$receiver_user->user_login) ;
	
	//sitemile_send_email($receiver_user->user_email, $subject , $message); // send email for the payment received
	
	
	$subject = sprintf(__("Details for closed Project: %s",'ProjectTheme'), $post->post_title);
	$message = sprintf(__('The project <a href="%s">%s</a> was just closed. Here is the user email for the other party: %s',"ProjectTheme"),
	$perm,$post->post_title,$payer_user->user_email) ;
	
	//sitemile_send_email($receiver_user->user_email, $subject , $message); // send email for the details
	
	//------------
	
	$subject = sprintf(__("Your have received payment for the project: %s",'ProjectTheme'), $post->post_title);
	$message = sprintf(__('You have been just paid for the project <a href="%s">%s</a> the amount of: %s %s from user: 
	<b>%s</b>',"ProjectTheme"),$perm,$post->post_title,$amount,$cure, $payer_user->user_login) ;
	
	//sitemile_send_email($payer_user->user_email, $subject , $message); // send email for the payment received
	
	$subject = sprintf(__("Details for closed Project: %s",'ProjectTheme'), $post->post_title);
	$message = sprintf(__('The project <a href="%s">%s</a> was just closed. Here is the user email for the other party: %s',"ProjectTheme"),
	$perm,$post->post_title,$receiver_user->user_email) ;
	
	//sitemile_send_email($payer_user->user_email, $subject , $message); // send email for the details
	
	//------------
	
	if($pay_by_credits == '1'):
	
		$cr = projectTheme_get_credits($payer_user_id);
		projectTheme_update_credits($payer_user_id, $cr - $amount);
		
		$uprof 	= ProjectTheme_get_user_profile_link($receiver_user->ID); //get_bloginfo('siteurl')."/user-profile/".$receiver_user->user_login;
		$reason = sprintf(__('Payment sent to <a href="%s">%s</a> for project <a href="%s">%s</a>','ProjectTheme'),$uprof, $receiver_user->user_login , $perm, 
		$post->post_title);
		projectTheme_add_history_log('0', $reason, $amount, $payer_user_id, $receiver_user_id);
	
	//=========================
	
		$projectTheme_fee_after_paid = get_option('projectTheme_fee_after_paid');
		if(!empty($projectTheme_fee_after_paid)):
		
			$deducted = $amount*($projectTheme_fee_after_paid * 0.01);
		else: 
		
			$deducted = 0;
		
		endif;
	
		$cr = projectTheme_get_credits($receiver_user_id);
		projectTheme_update_credits($receiver_user_id, $cr + $amount - $deducted);
		
		$uprof 	= ProjectTheme_get_user_profile_link($payer_user_id->ID);
		$reason = sprintf(__('Payment received from <a href="%s">%s</a> for project <a href="%s">%s</a>','ProjectTheme'),$uprof, $payer_user_id->user_login , $perm, 
		$post->post_title);
		projectTheme_add_history_log('1', $reason, $amount , $receiver_user_id, $payer_user_id);
		
		//--------
		
		$reason = sprintf(__('Payment fee for project <a href="%s">%s</a>','ProjectTheme'), $perm, $post->post_title);
		projectTheme_add_history_log('0', $reason, $deducted, $receiver_user_id);
	
	
	endif;endif;
	
	//------------
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_prepare_rating($pid, $fromuser, $touser)
{

		global $wpdb;			
		$s = "insert into ".$wpdb->prefix."project_ratings (pid, fromuser, touser) values('$pid','$fromuser','$touser')";				
		$wpdb->query($s);
		//mysql_query($s) or die(mysql_error());
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function projectTheme_get_bid_by_id($id)
{
	global $wpdb;
	$s = "select * from ".$wpdb->prefix."project_bids where id='$id'";
	$r = $wpdb->get_results($s);

	return $r[0];	
}
/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/

function ProjectTheme_get_images_cost_extra($pid)
{
	$ProjectTheme_charge_fees_for_images 	= get_option('ProjectTheme_charge_fees_for_images');
	$projectTheme_extra_image_charge		= get_option('projectTheme_extra_image_charge');
	
	//---------------------------
	
	$image_fee_paid = get_post_meta($pid, 'image_fee_paid', true);
	if(empty($image_fee_paid)) { $image_fee_paid = 0; update_post_meta($pid, 'image_fee_paid', 0);  }
		
	if($ProjectTheme_charge_fees_for_images == "yes")
	{
		$projectTheme_nr_of_free_images = get_option('projectTheme_nr_of_free_images');
		if(empty($projectTheme_nr_of_free_images)) $projectTheme_nr_of_free_images = 1;	
		
		$ProjectTheme_get_post_nr_of_images = ProjectTheme_get_post_nr_of_images($pid);
		
		$nr_imgs = $ProjectTheme_get_post_nr_of_images - $projectTheme_nr_of_free_images - $image_fee_paid;
		if($nr_imgs > 0)
		{
			return $nr_imgs*	$projectTheme_extra_image_charge;
		}
		
	}
	
	return 0;
	
}


function ProjectTheme_get_custom_taxonomy_count2($post_type, $tax_term, $taxonomy_name) 
{
	$taxonomy = 'my_taxonomy'; // this is the name of the taxonomy
    $args = array(
        'post_type' => $post_type, 'posts_per_page' => '1',
		'meta_query' => array(
				array(
					'key' => 'closed',
					'value' => '0',
					'compare' => '='
				)
			),		
        'tax_query' => array(
                    array(
                        'taxonomy' => $taxonomy_name,
                        'field' => 'slug',
                        'terms' => $tax_term)
                )
        );

     $my_query = new WP_Query( $args );
	 return $my_query->found_posts;
	
}


function ProjectTheme_get_images_extra_nr_pictures($pid)
{
	$ProjectTheme_charge_fees_for_images 	= get_option('ProjectTheme_charge_fees_for_images');
	$projectTheme_extra_image_charge		= get_option('projectTheme_extra_image_charge');
	
	$image_fee_paid = get_post_meta($pid, 'image_fee_paid', true);
	if(empty($image_fee_paid)) { $image_fee_paid = 0; update_post_meta($pid, 'image_fee_paid', 0);  }
		
	if($ProjectTheme_charge_fees_for_images == "yes")
	{
		$projectTheme_nr_of_free_images = get_option('projectTheme_nr_of_free_images');
		if(empty($projectTheme_nr_of_free_images)) $projectTheme_nr_of_free_images = 1;	
		
		$ProjectTheme_get_post_nr_of_images = ProjectTheme_get_post_nr_of_images($pid);
		
		$nr_imgs = $ProjectTheme_get_post_nr_of_images - $projectTheme_nr_of_free_images - $image_fee_paid;
		if($nr_imgs > 0)
		{
			return $nr_imgs;
		}
		
	}
	
	return 0;
	
}


function ProjectTheme_mark_images_cost_extra($pid)
{
	$ProjectTheme_charge_fees_for_images = get_option('ProjectTheme_charge_fees_for_images');
	$projectTheme_extra_image_charge	= get_option('projectTheme_extra_image_charge');
	
	$image_fee_paid = get_post_meta($pid, 'image_fee_paid', true);
	if(empty($image_fee_paid)) { $image_fee_paid = 0; update_post_meta($pid, 'image_fee_paid', 0);  }
		
	if($ProjectTheme_charge_fees_for_images == "yes")
	{
		$projectTheme_nr_of_free_images = get_option('projectTheme_nr_of_free_images');
		if(empty($projectTheme_nr_of_free_images)) $projectTheme_nr_of_free_images = 1;	
		
		$ProjectTheme_get_post_nr_of_images = ProjectTheme_get_post_nr_of_images($pid);
		
		$nr_imgs = $ProjectTheme_get_post_nr_of_images - $projectTheme_nr_of_free_images - $image_fee_paid;
		if($nr_imgs > 0)
		{
			update_post_meta($pid, 'image_fee_paid', ($nr_imgs + $image_fee_paid)); 
		}
		
	}
	
 
	
}


/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/		
function ProjectTheme_get_project_primary_cat($pid)
{
	$project_terms = wp_get_object_terms($pid, 'project_cat');	
	
	if(is_array($project_terms))
	{
		return 	$project_terms[0]->term_id;
	}
	
	return 0;
}

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/
function ProjectTheme_project_clear_table($colspan = '')
	{
		return '        <tr>
				 <td colspan="'.$colspan.'">&nbsp;</td>
			</tr>';	
	}
	
	function projectTheme_admin_notices(){
    
		if(!function_exists('wp_pagenavi')) {
		echo '<div class="updated">
		   <p>For the <strong>Project Theme</strong> you need to install the wp pagenavi plugin. 
		   Install it from <a href="http://wordpress.org/extend/plugins/wp-pagenavi"><strong>here</strong></a>.</p>
		</div>';
								}
								
	if(!function_exists('bcn_display')) {
		echo '<div class="updated">
		   <p>For the <strong>Project Theme</strong> you need to install the Breadcrumb NavXT plugin. 
		   Install it from <a href="http://wordpress.org/extend/plugins/breadcrumb-navxt/"><strong>here</strong></a>.</p>
		</div>';
								}	
	}
	
	

/*************************************************************
*
*	ProjectTheme (c) sitemile.com - function
*
**************************************************************/	
	
	include 'my-upload.php';
	
	//-=================== delete PMs ============================
		
		global $wpdb;
		
		if(isset($_GET['confirm_message_deletion']))
		{
			$return 	= $_GET['return'];
			$messid 	= $_GET['id'];	
			
			global $wpdb, $current_user;
			get_currentuserinfo();
			$uid = $current_user->ID;
			
			if(empty($messid))
			{
				foreach($_GET['message_id'] as $messid)
				{
					
					$s = "select * from ".$wpdb->prefix."project_pm where id='$messid' AND (user='$uid' OR initiator='$uid')";
					$r = $wpdb->get_results($s);	
					
					if(count($r) > 0)
					{
						$row = $r[0];
						
						if($row->initiator == $uid)
						{
							$s = "update ".$wpdb->prefix."project_pm set show_to_source='0' where id='$messid'";
							$wpdb->query($s);	
							
						}
						else
						{
							$s = "update ".$wpdb->prefix."project_pm set show_to_destination='0' where id='$messid'";
							$wpdb->query($s);						
						}
						
						$using_perm = ProjectTheme_using_permalinks();
			
						if($using_perm)	$privurl_m = get_permalink(get_option('ProjectTheme_my_account_private_messages_id')). "/?";
						else $privurl_m = get_bloginfo('siteurl'). "/?page_id=". get_option('ProjectTheme_my_account_private_messages_id'). "&";	
							 
						
						
						
					}
					else if(!empty($_GET['rdr'])) wp_redirect($_GET['rdr']);
					else wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id')));
				}
				
				if($return == "inbox") wp_redirect($privurl_m . "pg=inbox");
				else if($return == "outbox") wp_redirect($privurl_m . "pg=sent-items");
				else if($return == "home") wp_redirect($privurl_m);
				else if(!empty($_GET['rdr'])) wp_redirect($_GET['rdr']);
				else wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id')));
				
			}
			else
			{
			
				$s = "select * from ".$wpdb->prefix."project_pm where id='$messid' AND (user='$uid' OR initiator='$uid')";
				$r = $wpdb->get_results($s);	
				
				if(count($r) > 0)
				{
					$row = $r[0];
					
					if($row->initiator == $uid)
					{
						$s = "update ".$wpdb->prefix."project_pm set show_to_source='0' where id='$messid'";
						$wpdb->query($s);	
						
					}
					else
					{
						$s = "update ".$wpdb->prefix."project_pm set show_to_destination='0' where id='$messid'";
						$wpdb->query($s);						
					}
					
					$using_perm = ProjectTheme_using_permalinks();
		
					if($using_perm)	$privurl_m = get_permalink(get_option('ProjectTheme_my_account_private_messages_id')). "/?";
					else $privurl_m = get_bloginfo('siteurl'). "/?page_id=". get_option('ProjectTheme_my_account_private_messages_id'). "&";	
						 
					
					if($return == "inbox") wp_redirect($privurl_m . "pg=inbox");
					else if($return == "outbox") wp_redirect($privurl_m . "pg=sent-items");
					else if($return == "home") wp_redirect($privurl_m);
					else if(!empty($_GET['rdr'])) wp_redirect($_GET['rdr']);
					else wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id')));
					
				}
				else if(!empty($_GET['rdr'])) wp_redirect($_GET['rdr']);
				else wp_redirect(get_permalink(get_option('ProjectTheme_my_account_page_id')));
			
			}
		}
		

    //BillyB adding shortcode to add to Gravity Forms
	function billyB_func () {
	
		echo "Hello";
	}
	add_shortcode('billyB', 'billyB_func');

	//NandoC adding shortcode to add to Timesheet Analysis Post
	function timesheetDataemployee() {
	
		//require_once 'timesheetdata.php';
	}
	add_shortcode('timesheet', 'timesheetDataemployee');

	//NandoC adding shortcode to add to Timesheet Analysis Post
	function timesheetDataproject() {
	
		//require_once 'timesheetdataproject.php';
	}
	add_shortcode('timesheetproject', 'timesheetDataproject');
	//NandoC adding shortcode to add to Timesheet Analysis Post
	function timesheetDatapro() {
	
		//require_once 'timesheetdatapro.php';
	}
	add_shortcode('timesheetpro', 'timesheetDatapro');
	//NandoC adding shortcode to add to Timesheet Analysis Post
	function timesheetDatanonpro() {
	
		//require_once 'timesheetdatanonpro.php';
	}
	add_shortcode('timesheetnonpro', 'timesheetDatanonpro');

	 
	
?>