<?php
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
if(isset($_POST['search_term']))
{
	$search_term = $_POST['search_term'];
	
	if(!empty($search_term))
	{
		$wpdb->query(
			$wpdb->prepare(
			"select client_id,client_name from ".$wpdb->prefix."clients where client_name like '%%s%'",$search_term
			)
		);
		echo 'hello';
	}
}
?>