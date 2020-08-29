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
	
	/* New collection */
	function new_collection($name) {
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$collections[] = array('label' => $name);
		update_user_meta($user_id, '_wpb_collections', $collections);
	}
	
	/* Remove a collection */
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
	
	/* Soft-Remove a collection */
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
	
	/* Get bookmarks by collection */
	function get_bookmarks_by_collection($id){
		$collections = $this->get_collections( get_current_user_id() );
		return $collections[$id];
	}
	
	function get_bookmarks_count_by_collection($id){
		$collections = $this->get_collections( get_current_user_id() );
		if ($id == 0){
			if (empty($collections[$id])){
				return 0;
			} else {
				return (int)count($collections[$id]);
			}
		} else {
			return (int)count($collections[$id])-1;
		}
	}
	
	/* print bookmarks */
	function print_bookmarks($coll_id) {
		$output = '';
		
		$output .= '<div class="wpb-coll-count">';
		$output .= sprintf(__('%s Bookmarks in Collection','wpb'), $this->get_bookmarks_count_by_collection($coll_id));
		
		if ($coll_id != 0) { // default cannot be removed
		$output .= '<a href="#" class="wpb-bm-btn bookmarked wpb-remove-collection" data-undo="'.__('Undo','wpb').'" data-remove="'.__('Remove Collection','wpb').'">'.__('Remove Collection','wpb').'</a>';
		
		/* To hide a collection */
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
			if ($id != 'label') {
			$results++;
			if (get_post_status($id) == 'publish') { // active post
			
				$output .= '<div class="wpb-coll-item">';
				$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','wpb').'</a>';
				
				$output .= '<div class="uci-thumb"><a href="'.get_permalink($id).'">'.$this->post_thumb($id, 50).'</a></div>';
				
				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
				$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
			} else {
			
				$output .= '<div class="wpb-coll-item">';
				$output .= '<a href="#" class="wpb-coll-abs wpb-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','wpb').'</a>';
				
				$output .= '<div class="uci-thumb"></div>';
				
				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title">'.__('Content Removed','wpb').'</div>';
				$output .= '<div class="uci-url"></div>';
				$output .= '</div><div class="wpb-clear"></div>';
				
				$output .= '</div><div class="wpb-clear"></div>';
			
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
	
	/* Get collections for user */
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
	
	/* Find collection ID */
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
	
	/* Delete collection */
	function delete_collection($collection_id, $user_id) {
		$array = $this->get_collections($user_id);
		unset($array[$collection_id]);
		update_user_meta($user_id, '_wpb_collections', $array);
	}
	
	/* Get collections */
	function get_collections($user_id) {
		return (array)get_user_meta($user_id, '_wpb_collections', true);
	}
	
	/* Get bookmarks */
	function get_bookmarks($user_id) {
		return (array)get_user_meta($user_id, '_wpb_bookmarks', true);
	}
	
	/* Count bookmarks */
	function bookmarks_count($user_id) {
		$bookmarks = (array)get_user_meta($user_id, '_wpb_bookmarks', true);
		unset($bookmarks[0]);
		if (!empty($bookmarks) ){
			return count($bookmarks);
		} else {
			return 0;
		}
	}
	
	/* Get current page url */
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
	
		/* output */
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
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		/* options */
		if (strstr($width, 'px')) { $px = 'px'; } else { $px = '%'; }
		if ($px == '%') {
			$btn_width = 50 - $pct_gap . $px;
		} else {
			$btn_width = ($width / 2 ) - $px_gap . $px;
		}
		if ($widgetized == 1){
			$btn_width = '100%';
		}

		/* output */
		$output = '';
		
		// logged in
		if (is_user_logged_in()){
		
		if (isset($post->ID)){
			$post_id = $post->ID;
		} else {
			$post_id = null;
		}
		
		$output .= '<div class="wpb-bm wpb-bm-nobottommargin-'.$no_bottom_margin.' wpb-bm-notopmargin-'.$no_top_margin.' wpb-bm-inline-'.$inline.' wpb-bm-'.$align.' wpb-bm-widgetized-'.(int)$widgetized.'" style="width:'.$width.' !important;" data-add_new_collection="'.$add_new_collection.'" data-default_collection="'.$default_collection.'" data-new_collection_placeholder="'.$new_collection_placeholder.'" data-dialog_unbookmarked="'.$dialog_unbookmarked.'" data-dialog_bookmarked="'.$dialog_bookmarked.'" data-add_to_collection="'.$add_to_collection.'" data-remove_bookmark="'.$remove_bookmark.'" data-post_id="'.$post_id.'">';
		
		$output .= '<div class="wpb-bm-inner">';
		
		/* collections list */
		$output .= '<div class="wpb-bm-list">';
		$output .= '<select class="chosen-select-collections" name="wpb_bm_collection" id="wpb_bm_collection" data-placeholder="">';
		$output .= $this->collection_options( $default_collection, $post_id );
		$output .= '</select>';
		$output .= '</div>';
		
		/* action buttons */
		$output .= '<div class="wpb-bm-act">';
		
		if ($this->bookmarked($post_id)) {
			$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($post_id).'" />';
			$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary bookmarked" data-action="bookmark">'.$remove_bookmark.'</a></div>';
		} else {
			$output .= '<div class="wpb-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn primary unbookmarked" data-action="bookmark">'.$add_to_collection.'</a></div>';
		}
		$output .= '<div class="wpb-bm-btn-contain bm-right" style="width:'.$btn_width.' !important;"><a href="#" class="wpb-bm-btn secondary" data-action="newcollection">'.$new_collection.'</a></div>';
		
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
	
}

$wpb = new wpb_api();