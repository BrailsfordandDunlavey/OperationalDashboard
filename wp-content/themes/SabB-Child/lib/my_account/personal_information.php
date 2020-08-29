<?php



function ProjectTheme_my_account_personal_info_function()
{
	
		global $current_user, $wpdb, $wp_query;
		get_currentuserinfo();
		$uid = $current_user->ID;
	
?>
    	<div id="content" class="account-main-area">
        	
           <?php
				
				if(isset($_POST['save-info']))
				{
					//if(file_exists('cimy_update_ExtraFields'))
					cimy_update_ExtraFields_new_me();
					
					
					if(!empty($_FILES['avatar']["tmp_name"]))
					{
					 
						
						//***********************************
						
						$pid = 0;
						$cid = $uid;				 
						
						require_once(ABSPATH . "wp-admin" . '/includes/file.php');	  
						$upload_overrides 	= array( 'test_form' => false );
						$uploaded_file 		= wp_handle_upload($_FILES['avatar'], $upload_overrides);
					
						$file_name_and_location = $uploaded_file['file'];
                    	$file_title_for_media_library = $_FILES['file']['name'];
						
						$arr_file_type 		= wp_check_filetype(basename($_FILES['avatar']['name']));
                    	$uploaded_file_type = $arr_file_type['type'];

		
						
						$attachment = array(
                                'post_mime_type' => $uploaded_file_type,
                                'post_title' =>  addslashes($file_title_for_media_library),
                                'post_content' => '',
                                'post_status' => 'inherit',
								'post_parent' =>  $pid,

								'post_author' => $cid,
                            );
						 require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						$attach_id = wp_insert_attachment( $attachment, $file_name_and_location, $pid );
                       
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file_name_and_location );
                        wp_update_attachment_metadata($attach_id,  $attach_data);

					 
						update_user_meta($uid, 'avatar_' . 'project', $attach_id);
						
						//***********************************
						
						
					}
					
					//---------------------
					
					$wpdb->query("delete from ".$wpdb->prefix."project_email_alerts where uid='$uid' ");
					
					$email_cats = $_POST['email_cats'];
					
					if(count($email_cats) > 0)
					foreach($email_cats as $em)
					{
						$wpdb->query("insert into ".$wpdb->prefix."project_email_alerts (uid,catid) values('$uid','$em') ");						
					}
					
					
					
					//-------------------
					//email_locs
					//****************************************************************************************************
					$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
					if($ProjectTheme_enable_project_location != "no"):
					
					
						$wpdb->query("delete from ".$wpdb->prefix."project_email_alerts_locs where uid='$uid' ");
						
						$email_cats = $_POST['email_locs'];
						
						if(count($email_cats) > 0)
						foreach($email_cats as $em)
						{
							$wpdb->query("insert into ".$wpdb->prefix."project_email_alerts_locs (uid,catid) values('$uid','$em') ");						
						}
					
					endif;
					
					//****************************************************************************************************
					//-------------------
					
					$user_description = trim($_POST['user_description']);
					update_user_meta($uid, 'user_description', $user_description);
					
					
					$per_hour = trim($_POST['per_hour']);
					update_user_meta($uid, 'per_hour', $per_hour);
					
					
					$user_location = trim($_POST['project_location_cat']);
					update_user_meta($uid, 'user_location', $user_location);
					
					$user_city = trim($_POST['user_city']);
					update_user_meta($uid, 'user_city', $user_city);
					
					$personal_info = trim($_POST['paypal_email']);
					update_user_meta($uid, 'paypal_email', $personal_info);
					
					$personal_info = trim($_POST['payza_email']);
					update_user_meta($uid, 'payza_email', $personal_info);
					
					$personal_info = trim($_POST['moneybookers_email']);
					update_user_meta($uid, 'moneybookers_email', $personal_info);
					
					$user_url = trim($_POST['user_url']);
					update_user_meta($uid, 'user_url', $user_url);

					$user_address = trim($_POST['user_address']);
					update_user_meta($uid, 'user_address', $user_address);

					$user_suite = trim($_POST['user_suite']);
					update_user_meta($uid, 'user_suite', $user_suite);

					$user_datefounded = trim($_POST['user_datefounded']);
					update_user_meta($uid, 'user_datefounded', $user_datefounded);

					$user_zip = trim($_POST['user_zip']);
					update_user_meta($uid, 'user_zip', $user_zip);

					$rpr_company_name = trim($_POST['rpr_company_name']);
					update_user_meta($uid, 'rpr_company_name', $rpr_company_name);

					$user_statesservicing = trim($_POST['user_statesservicing']);
					update_user_meta($uid, 'user_statesservicing', $user_statesservicing);

					$user_statesregistered = trim($_POST['user_statesregistered']);
					update_user_meta($uid, 'user_statesregistered', $user_statesregistered);

					$user_statestest = trim($_POST['user_statestest']);
					update_user_meta($uid, 'user_statestest', $user_statestest);

					$user_employeeyear1 = trim($_POST['user_employeeyear1']);
					update_user_meta($uid, 'user_employeeyear1', $user_employeeyear1);

					$user_employeesize1 = trim($_POST['user_employeesize1']);
					update_user_meta($uid, 'user_employeesize1', $user_employeesize1);

					$user_employeeyear2 = trim($_POST['user_employeeyear2']);
					update_user_meta($uid, 'user_employeeyear2', $user_employeeyear2);

					$user_employeesize2 = trim($_POST['user_employeesize2']);
					update_user_meta($uid, 'user_employeesize2', $user_employeesize2);

					$user_employeeyear3 = trim($_POST['user_employeeyear3']);
					update_user_meta($uid, 'user_employeeyear3', $user_employeeyear3);

					$user_employeesize3 = trim($_POST['user_employeesize3']);
					update_user_meta($uid, 'user_employeesize3', $user_employeesize3);

					$user_revenueyear1 = trim($_POST['user_revenueyear1']);
					update_user_meta($uid, 'user_revenueyear1', $user_revenueyear1);

					$user_revenuesize1 = trim($_POST['user_revenuesize1']);
					update_user_meta($uid, 'user_revenuesize1', $user_revenuesize1);

					$user_revenueyear2 = trim($_POST['user_revenueyear2']);
					update_user_meta($uid, 'user_revenueyear2', $user_revenueyear2);

					$user_revenuesize2 = trim($_POST['user_revenuesize2']);
					update_user_meta($uid, 'user_revenuesize2', $user_revenuesize2);

					$user_revenueyear3 = trim($_POST['user_revenueyear3']);
					update_user_meta($uid, 'user_revenueyear3', $user_revenueyear3);

					$user_revenuesize3 = trim($_POST['user_revenuesize3']);
					update_user_meta($uid, 'user_revenuesize3', $user_revenuesize3);
					
					do_action('ProjectTheme_pers_info_save_action');

					if(isset($_POST['password']) && !empty($_POST['password']))
					{
						$p1 = trim($_POST['password']);
						$p2 = trim($_POST['reppassword']);
						
						if(!empty($p1) && !empty($p2))
						{
						
							if($p1 == $p2)
							{
								global $wpdb;
								$newp = md5($p1);
								$sq = "update ".$wpdb->users." set user_pass='$newp' where ID='$uid'" ;
								$wpdb->query($sq);
								
								$inc = 1;
							}
							else {
							echo '<div class="error">'.__("Password was not updated. Passwords do not match!","ProjectTheme").'</div>'; $xxp = 1; }
						}
						else
						{ 
							
							echo '<div class="error">'.__("Password was not updated. Passwords do not match!","ProjectTheme").'</div>';	 $xxp = 1;		
						}
					}
					 
					
					
					//---------------------------------------
						
					$arr = $_POST['custom_field_id'];
					for($i=0;$i<count($arr);$i++)
					{
						$ids 	= $arr[$i];
						$value 	= $_POST['custom_field_value_'.$ids];
						
						if(is_array($value))
						{
							delete_user_meta($uid, "custom_field_ID_".$ids);
							
							for($j=0;$j<count($value);$j++) {
								add_user_meta($uid, "custom_field_ID_".$ids, $value[$j]);
								
							}
						}
						else
						update_user_meta($uid, "custom_field_ID_".$ids, $value);
						
					}


					
					//--------------------------------------------
					if($xxp != 1)
					{
						echo '<div class="saved_thing">'.__('Info saved!','ProjectTheme');
						
						if($inc == 1)
						{
						
							echo '<br/>'.__('Your password was changed. Redirecting to login page...','ProjectTheme');
							echo '<meta http-equiv="refresh" content="2; url='.get_bloginfo('url').'/wp-login.php">';
						
						}
						
						echo '</div>';
					}
				}
				$user = get_userdata($uid);
				
				$user_location = get_user_meta($uid, 'user_location',true);
				
				?>
         
       
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
            
             <form method="post"  enctype="multipart/form-data">
             
            
            
            <div class="clear10"></div>
            
            <div class="my_box3" >
           
            
            	<!-- <div class="box_title" id="other_infs_mm"><?php _e("Other Information",'ProjectTheme'); ?></div> -->
                <div class="box_content">  
                
        <ul class="post-new3">
        
        
        <?php do_action('ProjectTheme_pers_info_fields_2'); ?>
        
        <?php
		
		
		$user_tp = get_user_meta($uid,'user_tp',true);
		if(empty($user_tp)) $user_tp = 'all';
		
		if($user_tp == "all") 
			$catid = array('all','service_buyer','service_provider');
		else
			$catid = array($user_tp);
		
 		if ( current_user_can( 'manage_options' ) ) {
			$catid = array('all','service_buyer','service_provider');
		}  
		
		
		
		$k = 0;
		$arr = ProjectTheme_get_users_category_fields($catid, $uid);
		$exf = '';
		
		for($i=0;$i<count($arr);$i++)
		{
			
			        $exf .= '<li>';
					$exf .= '<h2>'.$arr[$i]['field_name'].$arr[$i]['id'].':</h2>';
					$exf .= '<p>'.$arr[$i]['value'].'</p>';
					$exf .= '</li>';
					
					$k++;
			
		}	
		
		echo $exf;
		 
		
		if(ProjectTheme_is_user_provider($uid)):
			$k++;
		?>           
                            
        <!-- <li>
        	<h2><?php echo __('Hourly Rate','ProjectTheme'); ?>:</h2>
        	<p><?php echo projectTheme_currency(); ?><input type="text" size="7" name="per_hour" value="<?php echo get_user_meta($uid, 'per_hour', true); ?>" class="do_input" /> 
             *<?php _e('your estimated hourly rate','ProjectTheme'); ?></p>
        </li> -->
        
        <?php
		endif;
		
			 global $current_user;
	 get_currentuserinfo();
	 $uid = $current_user->ID;
	$cid = $uid;
		
			if(ProjectTheme_is_user_provider($uid)):
			  
		?>           
                            
        <!-- <li>
        	<h2><?php echo __('Portfolio Pictures','ProjectTheme'); ?>:</h2>
        	<p>
			
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
	url: "<?php bloginfo('siteurl') ?>/?my_upload_of_project_files8=1",
};
 
var myDropzone = new Dropzone('div#myDropzoneElement2', myDropzoneOptions);

myDropzone.on("sending", function(file, xhr, formData) {
  formData.append("author", "<?php echo $current_user->ID; ?>"); // Will send the filesize along with the file as POST data.
  formData.append("ID", "<?php echo $pid; ?>"); // Will send the filesize along with the file as POST data.
});

   
    <?php

		$args = array(
		'order'          => 'ASC',
		'orderby'        => 'post_date',
		'post_type'      => 'attachment',
		'author'    => 		$current_user->ID,
		'meta_key' 			=> 'is_portfolio',
		'meta_value' 		=> '1',
 
		'numberposts'    	=> -1,
		);
		
	$attachments = get_posts($args);
	
	 
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

    

	<?php _e('Click the grey area below to add project images.','ProjectTheme') ?>
    <div class="dropzone dropzone-previews" id="myDropzoneElement2" ></div>
 
    
	</div>
            
            
            
     
            
            
            </p>
        </li> -->

        <!-- <li>
			        	<h2><?php echo __('Date Founded','ProjectTheme'); ?>:</h2>
			        	<p><input type="date" size="35" name="user_datefounded" value="<?php echo get_user_meta($uid, 'user_datefounded', true); ?>" class="do_input" /></p>
		</li>
 -->
		 <!-- <li>
			        	<h2><?php echo __('Fiscal End','ProjectTheme'); ?>:</h2>
			        	<p><input type="date" size="35" name="user_fiscalend" value="<?php echo get_user_meta($uid, 'user_fiscalend', true); ?>" class="do_input" /></p>
		</li> -->


		<!-- <li>
		<h2><?php echo __('States Registered to do business','ProjectTheme'); ?>:</h2>
		     <?php $selected = get_user_meta($uid, 'user_statesservicing', true);?>
			<select name="user_statesservicing" id="user_statesservicing" class="do_input"> 
				
			<option <?php if ($selected == '1' ) echo 'selected'; ?> value="District of Columbia">District of Columbia</option>
			<option <?php if ($selected == '2' ) echo 'selected'; ?> value="Maryland">Maryland</option>

			</select>

		</li> -->

        <!-- <li>
        	<h2><?php echo __('Employee Size','ProjectTheme'); ?>:</h2>
        		<p style="display: none;"><div style="overflow:auto; height:auto;">
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_employeeyear1" value="<?php echo get_user_meta($uid, 'user_employeeyear1', true); ?>" class="do_input" />
        		<label>Size: </label><input type="number" size="5" maxlength="5" name="user_employeesize1" value="<?php echo get_user_meta($uid, 'user_employeesize1', true); ?>" class="do_input" /><br />
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_employeeyear2" value="<?php echo get_user_meta($uid, 'user_employeeyear2', true); ?>" class="do_input" />
        		<label>Size: </label><input type="number" size="5" maxlength="5" name="user_employeesize2" value="<?php echo get_user_meta($uid, 'user_employeesize2', true); ?>" class="do_input" /><br />
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_employeeyear3" value="<?php echo get_user_meta($uid, 'user_employeeyear3', true); ?>" class="do_input" />
        		<label>Size: </label><input type="number" size="5" maxlength="5" name="user_employeesize3" value="<?php echo get_user_meta($uid, 'user_employeesize3', true); ?>" class="do_input" /><br /></p>
        		</div>

        </li>
 -->
       <!--  <li>
        	<h2><?php echo __('Revenue Size','ProjectTheme'); ?>:</h2>
        		<p style="display: none;"><div style="overflow:auto; height:auto;">
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_revenueyear1" value="<?php echo get_user_meta($uid, 'user_revenueyear1', true); ?>" class="do_input" />
        		<label>Revenue: $ </label><input type="number" size="8" maxlength="5" name="user_revenuesize1" value="<?php echo get_user_meta($uid, 'user_revenuesize1', true); ?>" class="do_input" /><br />
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_revenueyear2" value="<?php echo get_user_meta($uid, 'user_revenueyear2', true); ?>" class="do_input" />
        		<label>Revenue: $ </label><input type="number" size="8" maxlength="5" name="user_revenuesize2" value="<?php echo get_user_meta($uid, 'user_revenuesize2', true); ?>" class="do_input" /><br />
        		<label>Year: </label><input type="text" size="4" maxlength="4" name="user_revenueyear3" value="<?php echo get_user_meta($uid, 'user_revenueyear3', true); ?>" class="do_input" />
        		<label>Revenue: $ </label><input type="number" size="8" maxlength="5" name="user_revenuesize3" value="<?php echo get_user_meta($uid, 'user_revenuesize3', true); ?>" class="do_input" /><br /></p>
        		</div>

        </li> -->

		<li>
		   <?php
		   
		   if(function_exists('cimy_extract_ExtraFields'))
		   cimy_extract_ExtraFields();
		   
		   ?>
		   </li> 
        
        <?php
		endif;
		
		if(ProjectTheme_is_user_provider($uid)):
			$k++;
		?>

                    
                    <li>
                        <h2><?php echo __('Emails Alerts','ProjectTheme'); ?>:</h2>
                         <h3>*<?php _e('You will get an email notification when a project is posted in the selected categories','ProjectTheme'); ?></h3>
                        <p style="display: none;"><div style="border:1px solid #ccc;background:#f2f2f2; overflow:auto; width:400px; border-radius:5px; height:300px;">
                        
                        <?php
							
							global $wpdb;
							$ss = "select * from ".$wpdb->prefix."project_email_alerts where uid='$uid'";
							$rr = $wpdb->get_results($ss);
							
							$terms = get_terms( 'project_cat', 'parent=0&orderby=name&hide_empty=0' );
							
							foreach($terms as $term):
								
								$chk = (projectTheme_check_list_emails($term->term_id, $rr) == true ? "checked='checked'" : "");
								
								echo '<input type="checkbox" name="email_cats[]" '.$chk.' value="'.$term->term_id.'" /> '.$term->name."<br/>";
								
								$terms2 = get_terms( 'project_cat', 'parent='.$term->term_id.'&orderby=name&hide_empty=0' );
								foreach($terms2 as $term2):
									
								
									$chk = (projectTheme_check_list_emails($term2->term_id, $rr) == 1 ? "checked='checked'" : "");
									echo '&nbsp;&nbsp; &nbsp; <input type="checkbox" name="email_cats[]" '.$chk.' value="'.$term2->term_id.'" /> '.$term2->name."<br/>";
									
									$terms3 = get_terms( 'project_cat', 'parent='.$term2->term_id.'&orderby=name&hide_empty=0' );
									foreach($terms3 as $term3):
										
										$chk = (projectTheme_check_list_emails($term3->term_id, $rr) == 1 ? "checked='checked'" : "");
										echo '&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; <input type="checkbox" '.$chk.' name="email_cats[]" 
										value="'.$term3->term_id.'" /> '.$term3->name."<br/>";
									endforeach;
										
								endforeach;
								
							endforeach;
						
						?>
                        
                        </div>
                       </p>
                    </li>
        
        <?php
		
		$ProjectTheme_enable_project_location = get_option('ProjectTheme_enable_project_location');
		if($ProjectTheme_enable_project_location != "no"):
		
		?>
        	   <li>
                        <h2>&nbsp;</h2>
                        <h3>*<?php _e('You will get an email notification when a project is posted in the selected States','ProjectTheme'); ?></h3>
                        <p style="display: none;"><div style="border:1px solid #ccc;background:#f2f2f2; overflow:auto; width:400px; border-radius:5px; height:300px;">
                        
                        <?php
							
							global $wpdb; 
							$ss = "select * from ".$wpdb->prefix."project_email_alerts_locs where uid='$uid'";
							$rr = $wpdb->get_results($ss);
							
							$terms = get_terms( 'project_location', 'parent=0&orderby=name&hide_empty=0' );
							
							foreach($terms as $term):
								
								$chk = (projectTheme_check_list_emails($term->term_id, $rr) == true ? "checked='checked'" : "");
								
								echo '<input type="checkbox" name="email_locs[]" '.$chk.' value="'.$term->term_id.'" /> '.$term->name."<br/>";
								
								$terms2 = get_terms( 'project_location', 'parent='.$term->term_id.'&orderby=name&hide_empty=0' );
								foreach($terms2 as $term2):
									
								
									$chk = (projectTheme_check_list_emails($term2->term_id, $rr) == 1 ? "checked='checked'" : "");
									echo '&nbsp;&nbsp; &nbsp; <input type="checkbox" name="email_locs[]" '.$chk.' value="'.$term2->term_id.'" /> '.$term2->name."<br/>";
									
									$terms3 = get_terms( 'project_location', 'parent='.$term2->term_id.'&orderby=name&hide_empty=0' );
									foreach($terms3 as $term3):
										
										$chk = (projectTheme_check_list_emails($term3->term_id, $rr) == 1 ? "checked='checked'" : "");
										echo '&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; <input type="checkbox" '.$chk.' name="email_locs[]" 
										value="'.$term3->term_id.'" /> '.$term3->name."<br/>";
									endforeach;
										
								endforeach;
								
							endforeach;
						
						?>
                        
                        </div>
                        </p>
                    </li>

                    <!-- <li>
			        	<h2><?php echo __('Date Founded','ProjectTheme'); ?>:</h2>
			        	<p><input type="date" size="35" name="user_datefounded" value="<?php echo get_user_meta($uid, 'user_datefounded', true); ?>" class="do_input" /></p>
			        </li> -->
			        
        
        <?php endif;  endif; 
		 
		if($k == 0)
		{
			echo '<style>#other_infs_mm, #bk_save_not { display:none; } </style>';	
		}
		
		?>
        
        			
                    <li id="bk_save_not">
        <h2>&nbsp;</h2> <input type="hidden" value="<?php echo $uid; ?>" name="user_id" />
        <p><input type="submit" class="my-buttons" name="save-info" value="<?php _e("Save" ,'ProjectTheme'); ?>" /></p>
        </li>
                    
        </ul>
                
                
              
                </div>
                </div>
                
                
             
            
            
            
            
		</form>

                
        </div> <!-- end dif content -->
        
        <?php ProjectTheme_get_users_links(); ?>
        
    
	
<?php	
} 


?>