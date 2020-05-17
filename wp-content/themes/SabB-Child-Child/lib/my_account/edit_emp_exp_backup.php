<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$expense_backup = $_GET['ID'];
 
	$details = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."expense_backup where expense_backup_id=%d",$expense_backup));
	$expense_report_id = $details[0]->expense_report_id;
	
	$rights_results = $wpdb->get_results($wpdb->prepare("select ID from ".$wpdb->prefix."users 
		inner join ".$wpdb->prefix."employee_expenses on ".$wpdb->prefix."users.ID=".$wpdb->prefix."employee_expenses.employee_id 
		where ".$wpdb->prefix."employee_expenses.expense_report_id=%d",$expense_report_id));
	if($uid != $rights_results[0]->ID)
	{
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<?php echo "Sorry, this isn't your expense report.";?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }
	else{
	
		$current_dir = getcwd();
		$target_dir = $current_dir."/wp-content/expense_backup";
		chdir($target_dir);
		
		foreach ($details as $backup)
		{
			$filename = $backup->expense_filename;
			if (file_exists($filename)) 
			{
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($filename).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($filename));
				readfile($filename);
				exit;
			}
			readfile($filename);
		}
		chdir($current_dir);
		
	?>


<?php 
	}
?>