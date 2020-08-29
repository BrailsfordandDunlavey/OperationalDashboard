<?php

	/* dESiGNERz-CREW.iNFO for PRO users - Enqueue Scripts */
	add_action('wp_enqueue_scripts', 'wpb_enqueue_scripts', 99);
	function wpb_enqueue_scripts(){
	
		wp_register_style('wpb', wpb_url . 'css/wpb-bookmarks.css');
		wp_enqueue_style('wpb');
		
		wp_register_style('wpb_iconfont', wpb_url . 'css/wpb-iconfont.css');
		wp_enqueue_style('wpb_iconfont');
		
		wp_register_style('wpb_list', wpb_url . 'css/wpb-collections.css');
		wp_enqueue_style('wpb_list');
		
		wp_register_style('wpb_chosen', wpb_url . 'css/wpb-chosen.css' );
		wp_enqueue_style('wpb_chosen');
		
		wp_register_script('wpb_chosen', wpb_url . 'scripts/wpb-chosen.js', array('jquery') );
		wp_enqueue_script('wpb_chosen');
		
		wp_register_script('wpb', wpb_url . 'scripts/wpb-bookmarks.js');
		wp_enqueue_script('wpb');
		
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Add the bookmark widget to content */
	add_action('the_content', 'wpb_bookmark_content', 100);
	function wpb_bookmark_content($content){
		global $post, $wpb;
		if (wpb_get_option('auto_bookmark')) {
		
			// hard excluded by post type
			if (wpb_get_option('include_post_types')){
				if (is_array( wpb_get_option('include_post_types') ) && !in_array( get_post_type(), wpb_get_option('include_post_types')))
					return $content;
			}
			
			// soft excluded by post id
			if (wpb_get_option('exclude_ids')){
				$array = explode(',', wpb_get_option('exclude_ids'));
				if (in_array($post->ID, $array))
					return $content;
			}
			
			$content .= $wpb->bookmark();
		}
		return $content;
	}

	add_action('save_post','update_bookmark_status');
	function update_bookmark_status($post_id){
		global $wpb;
		if(isset($post_id))
		{
			if ( !wp_is_post_revision( $post_id ) )
			{
				$categories=wp_get_post_categories($post_id);
				$categories=wp_get_post_categories($post_id);
				$user_id=get_current_user_id();
				foreach($categories as $category)
				{
					if($wpb->bookmarked_category($category))
					{
						$collections = $wpb->get_collections( $user_id );
						$bookmarks = $wpb->get_bookmarks( $user_id );
						$collection_id=$wpb->category_collection_id($category);
		
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
					}
				}
			}
		}
	}
	add_filter( 'the_content', 'wp_bookmark_below_post', 1001 );
	function wp_bookmark_below_post( $content ) {
		$post_id = get_the_ID();
		$post = get_post( $post_id );
		if($post->post_type=='page'){
			return $content;
		}
		if(wpb_get_option('wpb_show_users_avatar')) {
			$avt_list = '';
			$bookmarked_by = get_post_meta($post_id , 'wpb_bookmarked_by' ,true);
			if($bookmarked_by == '')
				$bookmarked_by = array();
			$bookmarked_by = array_unique($bookmarked_by);
			foreach ($bookmarked_by as $user) {
				$avt_list .= get_avatar($user , 30);
			}
			return $content.'<style>.bookmarked-avatar img{margin: 3px;}</style><div class="bookmarked-avatar"><h3>Bookmarked By</h3>'.$avt_list;
		}
	
		return $content;
	}