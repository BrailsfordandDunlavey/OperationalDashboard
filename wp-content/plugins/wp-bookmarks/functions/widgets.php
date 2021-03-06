<?php

add_action( 'widgets_init', 'wpb_bookmark_widget' );


function wpb_bookmark_widget() {
	register_widget( 'WPB_WIDGET' );
}

class WPB_WIDGET extends WP_Widget {

	function WPB_WIDGET() {
		$widget_ops = array( 'classname' => 'wpb_bookmark', 'description' => __('Show the bookmark widget in your sidebar', 'wpb') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wpb_bookmark' );
		
		$this->WP_Widget( 'wpb_bookmark', __('WP Bookmark Widget', 'wpb'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		global $wpb;
		$before_title = $args['before_title'];
		$after_title = $args['after_title'];
			// hard excluded by post type
			if (wpb_get_option('include_post_types')){
				if (is_array( wpb_get_option('include_post_types') ) && !in_array( get_post_type(), wpb_get_option('include_post_types')))
					return false;
			}
			
			// soft excluded by post id
			if (wpb_get_option('exclude_ids')){
				$array = explode(',', wpb_get_option('exclude_ids'));
				if (in_array($post->ID, $array))
					return false;
			}

			//Our variables
			$title = apply_filters('widget_title', $instance['title'] );

			echo $args['before_widget'];
			if ( $title )
				echo $before_title . $title . $after_title;
			echo $wpb->bookmark('width=100%&widgetized=1&no_top_margin=1&no_bottom_margin=1');
			echo $args['after_widget'];
			
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Bookmark Me', 'wpb') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'wpb'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

	<?php
	}
}

?>
