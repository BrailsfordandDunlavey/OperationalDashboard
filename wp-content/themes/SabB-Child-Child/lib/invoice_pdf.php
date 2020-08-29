<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
function billyB_print_invoice()
{
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsquery = "select * from ".$wpdb->prefix."useradd where user_id = '$uid'";
	$rightsresults = $wpdb->get_results($rightsquery);
	$team = $rightsresults[0]->team;
	$employee_gp_id = $rightsresults[0]->gp_id;
	
	$expense_report = $_GET['ID'];
 
	require('fpdf.php');
	
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->Cell(40,10,'Hello Billy!');
	$pdf->Output();
}
add_shortcode('print_invoice','billyB_print_invoice')	
?>