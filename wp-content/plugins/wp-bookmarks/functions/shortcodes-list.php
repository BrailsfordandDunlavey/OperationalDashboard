<?php

	/* dESiGNERz-CREW.iNFO for PRO users - Registers and display the shortcode */
	add_shortcode('collections', 'collections' );
	function collections( $args=array() ) {
		global $wpb;

		/* dESiGNERz-CREW.iNFO for PRO users - arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		return $wpb->bookmarks( $args );
	
	}
	add_shortcode('publiccollections', 'publiccollections' );
	function publiccollections( $args=array() ) {
		global $wpb;

		/* dESiGNERz-CREW.iNFO for PRO users - arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		return $wpb->public_bookmarks( $args );
	
	}
