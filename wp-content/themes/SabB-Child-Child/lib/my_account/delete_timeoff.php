<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;

$ids = explode('-',$_GET['ID']);	
$request_id = $ids[0];
$timesheet_id = $ids[1];

$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."request_timeoff where request_id=%d",$request_id));
$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."timesheets where timesheet_id=%d",$timesheet_id));

wp_redirect(get_bloginfo('siteurl')."/request-time-off"); exit;
	
?>