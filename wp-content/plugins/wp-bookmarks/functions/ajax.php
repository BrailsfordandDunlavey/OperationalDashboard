<?php

	add_action('wp_head','wpb_ajax_url');
	function wpb_ajax_url() { ?>
		<script type="text/javascript">
		var wpb_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?php
	}
	

	/* dESiGNERz-CREW.iNFO for PRO users - switch Public collection */
	add_action('wp_ajax_nopriv_wpb_change_public_collection', 'wpb_change_public_collection');
	add_action('wp_ajax_wpb_change_public_collection', 'wpb_change_public_collection');
	function wpb_change_public_collection(){
		global $wpb;
		
		$output = '';
		$collection_id = $_POST['collection_id'];
		$user_id = $_POST['user_id'];
		$output['res'] = $wpb->print_public_bookmarks($collection_id,$user_id);
	
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	/* dESiGNERz-CREW.iNFO for PRO users - switch collection */
	add_action('wp_ajax_nopriv_wpb_change_collection', 'wpb_change_collection');
	add_action('wp_ajax_wpb_change_collection', 'wpb_change_collection');
	function wpb_change_collection(){
		global $wpb;
		$output = '';
		$collection_id = $_POST['collection_id'];		
		$output['res'] = $wpb->print_bookmarks($collection_id);
	
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - remove collection */
	add_action('wp_ajax_nopriv_wpb_hard_remove_collection', 'wpb_hard_remove_collection');
	add_action('wp_ajax_wpb_hard_remove_collection', 'wpb_hard_remove_collection');
	function wpb_hard_remove_collection(){
		global $wpb;
		$output = '';
		$collection_id = $_POST['collection_id'];
		
		$wpb->hard_remove_collection( $collection_id );
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - soft-remove collection */
	add_action('wp_ajax_nopriv_wpb_soft_remove_collection', 'wpb_soft_remove_collection');
	add_action('wp_ajax_wpb_soft_remove_collection', 'wpb_soft_remove_collection');
	function wpb_soft_remove_collection(){
		global $wpb;
		$output = '';
		$collection_id = $_POST['collection_id'];
		
		$wpb->soft_remove_collection( $collection_id );
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - add new collection */
	add_action('wp_ajax_nopriv_wpb_addcollection', 'wpb_addcollection');
	add_action('wp_ajax_wpb_addcollection', 'wpb_addcollection');
	function wpb_addcollection(){
		global $wpb;
		$output = '';
		$collection_name = $_POST['collection_name'];
		$privacy = $_POST['privacy'];
		$default_collection = $_POST['default_collection'];
		$post_id = $_POST['post_id'];
		$wpb->new_collection( $collection_name,$privacy );
		
		$output['options'] = '<select class="chosen-select-collections" name="wpb_bm_collection" id="wpb_bm_collection" data-placeholder="">' . $wpb->collection_options( $default_collection, $post_id ) . '</select>';
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - add new bookmark */
	add_action('wp_ajax_nopriv_wpb_newbookmark', 'wpb_newbookmark');
	add_action('wp_ajax_wpb_newbookmark', 'wpb_newbookmark');
	function wpb_newbookmark(){
		global $wpb;
		$output = '';
		
		$user_id = get_current_user_id();
		$collections = $wpb->get_collections( $user_id );
		$bookmarks = $wpb->get_bookmarks( $user_id );
		$collection_id = $_POST['collection_id'];
		$post_id = $_POST['post_id'];
		/* dESiGNERz-CREW.iNFO for PRO users - add collection (post id relation) */
		if (!isset($collections[$collection_id])){
			$collections[$collection_id] = array();
		}
		$collections[$collection_id][$post_id] = 1;
		
		/* dESiGNERz-CREW.iNFO for PRO users - add bookmark with collection id */
		if (!isset($bookmarks[$post_id])){
			$bookmarks[$post_id] = $collection_id;
		} else {
			$prev_collection_id = $bookmarks[$post_id];
			unset($collections[$prev_collection_id][$post_id]); // remove from prev collection
			$bookmarks[$post_id] = $collection_id; // update collection
		}
		
		$output['collection_id'] = $collection_id; // update active collection
				
		update_user_meta($user_id, '_wpb_collections', $collections);
		update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)+1);
		$bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' , true);
		if($bookmarked_by == false) {
			$bookmarked_by = array($user_id);
		}else{
			$bookmarked_by[] = $user_id;
		}
		update_post_meta($post_id , 'wpb_bookmarked_by' , $bookmarked_by);

		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - remove bookmark */
	add_action('wp_ajax_nopriv_wpb_removebookmark', 'wpb_removebookmark');
	add_action('wp_ajax_wpb_removebookmark', 'wpb_removebookmark');
	function wpb_removebookmark(){
		global $wpb;
		$output = '';
		
		$user_id = get_current_user_id();
		$collections = $wpb->get_collections( $user_id );
		$bookmarks = $wpb->get_bookmarks( $user_id );
		$bookmark_categories = $wpb->get_category_bookmarks( $user_id );
		$collection_id = $_POST['collection_id'];
		$post_id = $_POST['post_id'];
		$category_id = $_POST['category_id'];
		if(strrchr($category_id,","))
		{
			$categories=explode(",",$category_id);
			for($count=0;$count<count($categories)-1;$count++)
			{
				if($wpb->bookmarked_category($categories[$count])){
					if (isset($bookmark_categories[$categories[$count]])){
						$curcollection_id = $bookmark_categories[$categories[$count]];
						unset($collections[$curcollection_id][$categories[$count]]); // remove from collections
						unset($bookmark_categories[$categories[$count]]); // remove from bookmarks
					}
			
					if (isset($collections[$collection_id][$categories[$count]])){
						unset($collections[$collection_id][$$categories[$count]]);
					}
				}
			}
		}
		else
		{
			if($wpb->bookmarked_category($category_id)){
				if (isset($bookmark_categories[$category_id])){
					$curcollection_id = $bookmark_categories[$category_id];
					unset($collections[$curcollection_id][$category_id]); // remove from collections
					unset($bookmark_categories[$category_id]); // remove from bookmarks
				}
			
				if (isset($collections[$collection_id][$category_id])){
					unset($collections[$collection_id][$category_id]);
				}
			}
		}
		/******************************Code Ended****************************************************************/
		
		if (isset($bookmarks[$post_id])){
			$curcollection_id = $bookmarks[$post_id];
			unset($collections[$curcollection_id][$post_id]); // remove from collections
			unset($bookmarks[$post_id]); // remove from bookmarks
		}
		
		if (isset($collections[$collection_id][$post_id])){
			unset($collections[$collection_id][$post_id]);
		}
				
		update_user_meta($user_id, '_wpb_collections', $collections);
		update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		update_user_meta($user_id, '_wpb_bookmarks_category', $bookmark_categories);
		update_post_meta($post_id , '_wpb_post_bookmark_count' , get_post_meta($post_id , '_wpb_post_bookmark_count' , true)-1);
		
		$bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' , true);
		if($bookmarked_by == false) {
			$bookmarked_by = array();
		}else{
			if(($key = array_search($user_id, $bookmarked_by)) !== false) {
				unset($bookmarked_by[$key]);
			}
		}
		update_post_meta($post_id , 'wpb_bookmarked_by' , $bookmarked_by);

		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}


/*******************************************************Code Added By Vipin for category bookmarks*******************************************************************/
	add_action('wp_ajax_nopriv_wpb_newcategorybookmark', 'wpb_newcategorybookmark');
	add_action('wp_ajax_wpb_newcategorybookmark', 'wpb_newcategorybookmark');
	function wpb_newcategorybookmark(){
		global $wpb;
		$output = '';

		$collection_id = $_POST['collection_id'];
		$post_id = isset($_POST['post_id'])?$_POST['post_id']:null;
		$category_id = $_POST['category_id'];
		
		$user_id = get_current_user_id();
		$collections = $wpb->get_collections( $user_id );
		$bookmark_categories = $wpb->get_category_bookmarks( $user_id );
		$bookmarks=$wpb->get_bookmarks($user_id);
		/* dESiGNERz-CREW.iNFO for PRO users - add collection (post id relation) */
		if (!isset($collections[$collection_id])){
			$collections[$collection_id] = array();
		}
		$collections[$collection_id][$post_id] = 1;
		
		/* dESiGNERz-CREW.iNFO for PRO users - add category bookmark with collection id */
		if (!isset($bookmark_categories[$category_id])){
			$bookmark_categories[$category_id] = $collection_id;
		} else {
			$prev_collection_id = $bookmark_categories[$category_id];
			unset($collections[$prev_collection_id][$category_id]); // remove from prev collection
			$bookmark_categories[$category_id] = $collection_id; // update collection
		}
		$category_posts=get_posts( array( 'category' => $category_id ,'numberposts'=>-1) );
		foreach($category_posts as $category_post)
		{
			if($wpb->bookmarked($category_post->ID))
			{
			}
			else
			{
				if (!isset($bookmarks[$category_post->ID])){
					$bookmarks[$category_post->ID] = $collection_id;
				} else {
					$prev_collection_id = $bookmarks[$category_post->ID];
					unset($collections[$prev_collection_id][$category_post->ID]); // remove from prev collection
					$bookmarks[$category_post->ID] = $collection_id; // update collection
				}
				if (!isset($collections[$collection_id])){
					$collections[$collection_id] = array();
				}
				$collections[$collection_id][$category_post->ID] = 1;
				update_post_meta($category_post->ID , '_wpb_post_bookmark_count' , get_post_meta($category_post->ID , '_wpb_post_bookmark_count' , true)+1);
				$bookmarked_by = get_post_meta($category_post->ID , 'wpb_bookmarked_by' , true);
				if($bookmarked_by == false) {
					$bookmarked_by = array($user_id);
				}else{
					$bookmarked_by[] = $user_id;
				}
					update_post_meta($category_post->ID , 'wpb_bookmarked_by' , $bookmarked_by);
			}
		}
		/* dESiGNERz-CREW.iNFO for PRO users - add posts bookmark with collection id for the specified category*/

		$output['collection_id'] = $collection_id; // update active collection
		if($wpb->bookmarked($post_id)){
			$output['post']='bookmarked';
		}
		else
		{
			$output['post']='unbookmarked';

		}

		update_user_meta($user_id, '_wpb_collections', $collections);
		update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		update_user_meta($user_id, '_wpb_bookmarks_category', $bookmark_categories);

		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

	/* dESiGNERz-CREW.iNFO for PRO users - remove category bookmark */
	add_action('wp_ajax_nopriv_wpb_removecategorybookmark', 'wpb_removecategorybookmark');
	add_action('wp_ajax_wpb_removecategorybookmark', 'wpb_removecategorybookmark');
	function wpb_removecategorybookmark(){
		global $wpb;
		$output = '';
		$collection_id = $_POST['collection_id'];
		$post_id = isset($_POST['post_id'])?$_POST['post_id']:null;
		$category_id = $_POST['category_id'];
		
		$user_id = get_current_user_id();
		$collections = $wpb->get_collections( $user_id );
		$bookmark_categories = $wpb->get_category_bookmarks( $user_id );
		
		if (isset($bookmark_categories[$category_id])){
			$curcollection_id = $bookmark_categories[$category_id];
			unset($collections[$curcollection_id][$category_id]); // remove from collections
			unset($bookmark_categories[$category_id]); // remove from bookmarks
		}
		
		if (isset($collections[$collection_id][$category_id])){
			unset($collections[$collection_id][$category_id]);
		}

		$category_posts=get_posts( array( 'category' => $category_id ,'numberposts'=>-1) );
		foreach($category_posts as $category_post)
		{
			update_post_meta($category_post->ID , '_wpb_post_bookmark_count' , get_post_meta($category_post->ID , '_wpb_post_bookmark_count' , true)-1);
			$bookmarked_by = get_post_meta($category_post->ID , 'userpro_bookmarked_by' , true);
			if($bookmarked_by == false) {
				$bookmarked_by = array();
			}else{
				if(($key = array_search($user_id, $bookmarked_by)) !== false) {
					unset($bookmarked_by[$key]);
				}
			}
			update_post_meta($category_post->ID , 'wpb_bookmarked_by' , $bookmarked_by);
		}
		
		update_user_meta($user_id, '_wpb_collections', $collections);
		update_user_meta($user_id, '_wpb_bookmarks_category', $bookmark_categories);

		$output=json_encode($output);
 		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
 	}

/******************************************************************Code End*******************************************************************/
