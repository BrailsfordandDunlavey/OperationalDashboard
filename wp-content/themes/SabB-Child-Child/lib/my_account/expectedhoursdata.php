<?php
/***************************************************************************
*
*	ProjectTheme - copyright (c) - sitemile.com
*	The only project theme for wordpress on the world wide web.
*
*	Coder: Andrei Dragos Saioc
*	Email: sitemile[at]sitemile.com | andreisaioc[at]gmail.com
*	More info about the theme here: http://sitemile.com/products/wordpress-project-freelancer-theme/
*	since v1.2.5.3
*
***************************************************************************/


global $wpdb;
$contactus_table = $wpdb->prefix."expected_hours";
$wpdb->insert( $contactus_table, array('column_name_1'=>'hello', 'other'=> 123), array( '%s', '%d' ) );