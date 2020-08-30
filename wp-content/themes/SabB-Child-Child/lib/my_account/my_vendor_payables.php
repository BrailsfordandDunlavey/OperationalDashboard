<?php
function billyB_my_vendor_payables()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
 	$employeegpidresult = $wpdb->get_results($wpdb->prepare("select gp_id from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$employee_gp_id = $employeegpidresult[0]->gp_id;
	
	$vendor_payables = $wpdb->get_results($wpdb->prepare("select vendor_payable_id,vendor_name,expense_date,vendor_fee,vendor_expense,
		expense_status,billed_status,expense_billable,display_name,fee_billable
		from ".$wpdb->prefix."vendor_payables
		left join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
		left join ".$wpdb->prefix."users on ".$wpdb->prefix."vendor_payables.assigned_to=".$wpdb->prefix."users.ID
		where submitted_by=%d order by expense_date desc",$uid));
		
	$assigned_payables = $wpdb->get_results($wpdb->prepare("select vendor_payable_id,vendor_name,expense_date,vendor_fee,vendor_expense,
		expense_status,billed_status,expense_billable,fee_billable
		from ".$wpdb->prefix."vendor_payables
		left join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
		where assigned_to=%d and expense_status=0 order by expense_date desc",$uid));
	
	if(!empty($assigned_payables))
	{
		echo '<div id="content">
			<div class="my_box3">
				<div class="padd10"><h3>Payables Assigned to Me</h3>
					<ul class="other-dets_m">';
		
		echo '<li><table width="100%">
			<tr>
			<th><b><u>Vendor</u></b></th>
			<th><b><u>Date</u></b></th>
			<th><b><u>Fee</u></b></th>
			<th><b><u>Fee Billable</u></b></th>
			<th><b><u>Expenses</u></b></th>
			<th><b><u>Expense Billable</u></b></th>
			</tr>';
		foreach($assigned_payables as $ap)
		{
			if(empty($ap->vendor_name)){$name = "None Selected";}else{$name = $ap->vendor_name;}
			echo '<tr>
				<td><a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$ap->vendor_payable_id.'">'.$name.'</a></td>
				<td>'.date('m-d-Y',$ap->expense_date).'</td>
				<td>$'.number_format($ap->vendor_fee,2).'</td>
				<td>'.($ap->fee_billable == 1 ? "Billable" : "No-Bill" ).'</td>
				<td>$'.number_format($ap->vendor_expense,2).'</td>
				<td>'.($ap->expense_billable == 1 ? "Billable" : "No-Bill" ).'</td>
				</tr>';
		}
		echo '</table></li></ul></div></div></div>';
	}
	
	echo '<div id="content">
			<div class="my_box3">
				<div class="padd10"><h3>Unsubmitted Payables</h3>
					<ul class="other-dets_m">';
	$unsubmitted = 0;
	$submitted = 0;
	
	foreach($vendor_payables as $vp)
	{
		if($vp->expense_status == 0){$unsubmitted++;}
		if($vp->expense_status > 0){$submitted++;}
	}
	if($unsubmitted > 0)
	{
		echo '<li><table width="100%">
			<tr>
			<th><b><u>Vendor</u></b></th>
			<th><b><u>Assigned To</u></b></th>
			<th><b><u>Date</u></b></th>
			<th><b><u>Fee</u></b></th>
			<th><b><u>Fee Billable</u></b></th>
			<th><b><u>Expenses</u></b></th>
			<th><b><u>Expense Billable</u></b></th>
			</tr>';
		foreach($vendor_payables as $vp)
		{
			if($vp->expense_status == 0)
			{
				if(empty($vp->vendor_name)){$name = "None Selected";}else{$name = $vp->vendor_name;}
				echo '<tr>
					<td><a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$vp->vendor_payable_id.'">'.$name.'</a></td>
					<td>'.$vp->display_name.'</td>
					<td>'.date('m-d-Y',$vp->expense_date).'</td>
					<td>$'.number_format($vp->vendor_fee,2).'</td>
					<td>'.($vp->fee_billable == 1 ? "Billable" : "No-Bill" ).'</td>
					<td>$'.number_format($vp->vendor_expense,2).'</td>
					<td>'.($vp->expense_billable == 1 ? "Billable" : "No-Bill" ).'</td>
					</tr>';
			}
		}
		echo '</table></li>';
	}
	else
	{
		echo '<li>You don\'t currently have any unsubmitted Vendor Payables.<br/><br/>If you\'re looking for an payable for a specific project, you can check the "detailed expenses" on the Project Card.</li>';
	}
	echo '</ul>
				</div>
			</div>
		</div>';
		
	echo '<div id="content">
			<div class="my_box3">
				<div class="padd10"><h3>Submitted Payables</h3>
					<ul class="other-dets_m">';
	if($submitted > 0)
	{
		echo '<li><table width="100%">
			<tr>
			<th><b><u>Vendor</u></b></th>
			<th><b><u>Date</u></b></th>
			<th><b><u>Fee</u></b></th>
			<th><b><u>Fee Billable</u></b></th>
			<th><b><u>Expenses</u></b></th>
			<th><b><u>Expense Billable</u></b></th>
			<th><b><u>Expense Status</u></b></th>
			<th><b><u>Billed Status</u></b></th>
			</tr>';
		foreach($vendor_payables as $vp)
		{
			if($vp->expense_status > 0)
			{
				echo '<tr>
					<td><a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$vp->vendor_payable_id.'">'.$vp->vendor_name.'</a></td>
					<td>'.date('m-d-Y',$vp->expense_date).'</td>
					<td>$'.number_format($vp->vendor_fee,2).'</td>
					<td>'.($vp->fee_billable == 1 ? "Billable" : "No-Bill" ).'</td>
					<td>$'.number_format($vp->vendor_expense,2).'</td>
					<td>'.($vp->expense_billable == 1 ? "Billable" : "No-Bill" ).'</td>
					<td>'.($vp->expense_status == 1 ? "Unapproved" : ($vp->expense_status == 2 ? "Approved, awaiting Processing" : ($vp->expense_status == 3 ? "In-Process" : "Processed"))).'</td>
					<td>'.($vp->billed_status == 0 ? "Unbilled" : ($vp->billed_status == 1 ? "To be billed" : "Billed")).'</td>
					</tr>';
			}
		}
		echo '</table></li>';
	}
	else
	{
		echo '<li>You don\'t currently have any submitted Vendor Payables.<br/><br/>If you\'re looking for an payable for a specific project, you can check the "detailed expenses" on the Project Card.</li>';
	}
	echo '</ul>
				</div>
			</div>
		</div>';			
} 

add_shortcode('my_vendor_payables','billyB_my_vendor_payables')
?>