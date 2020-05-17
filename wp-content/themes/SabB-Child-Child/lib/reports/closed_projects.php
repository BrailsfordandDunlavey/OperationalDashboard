<?php
function promise_closedprojects()
{   
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	if($uid != 11 and $uid != 92 and $uid !=2){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}

	$closedpjtresults = $wpdb->get_results("select * from ".$wpdb->prefix."projects 
		where status ='3' order by close_date desc");
					
	foreach ($closedpjtresults as $cpr)
	{
		echo $cpr->abbreviated_name." ".date('m-d-Y',$cpr->close_date)."<br/>";
	}
}
add_shortcode('closed_projects','promise_closedprojects')
?>
