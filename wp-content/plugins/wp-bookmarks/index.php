<?php
/*
Plugin Name: User Bookmarks for WordPress 
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: This plugin allows your users to bookmark (favorite) any content into private collections.
Version: 1.9
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('wpb_url',plugin_dir_url(__FILE__ ));
define('wpb_path',plugin_dir_path(__FILE__ ));

	/* dESiGNERz-CREW.iNFO for PRO users - init */
	function wpb_init() {
		load_plugin_textdomain('wpb', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	add_action('init', 'wpb_init');

	/* dESiGNERz-CREW.iNFO for PRO users - functions */
	foreach (glob(wpb_path . 'functions/*.php') as $filename) { require_once $filename; }
	
	/* dESiGNERz-CREW.iNFO for PRO users - administration */
	if (is_admin()){
		foreach (glob(wpb_path . 'admin/*.php') as $filename) { include $filename; }
	}
