<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;

if($uid != 11 and $uid != 94 and $uid!=235){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}

$project = $_GET['ID'];

$expense_results = $wpdb->get_results($wpdb->prepare("select display_name,expense_date,expense_quantity,".$wpdb->prefix."employee_expenses.expense_amount,
	employee_expense_notes,expense_code_name,markup,".$wpdb->prefix."projects.ID
	from ".$wpdb->prefix."employee_expenses 
	inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
	inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
	inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."employee_expenses.project_id=".$wpdb->prefix."projects.ID
	where project_id=%s and billed_status=1
	order by expense_report_id,expense_date",$project));

$vendor_expenses = $wpdb->get_results($wpdb->prepare("select vendor_name,vendor_payable_id,expense_date,v_exp_name,vendor_fee,vendor_expense,
	".$wpdb->prefix."vendor_payables.notes,markup,".$wpdb->prefix."projects.ID 
	from ".$wpdb->prefix."vendor_payables 
	inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
	inner join ".$wpdb->prefix."vendor_expense_codes on ".$wpdb->prefix."vendor_payables.expense_type_id=".$wpdb->prefix."vendor_expense_codes.vendor_exp_code_id
	inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."vendor_payables.project_id=".$wpdb->prefix."projects.ID
	where expense_billable=1 and billed_status=1 and expense_status>1 and project_id=%d",$project));

get_header();
?>   
	<form method="post"  enctype="multipart/form-data">
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<div id="content_full"><h3>Expense Summary</h3>
				<div class="my_box3">
				<div class="padd10">					
				<ul class="other-dets_m">
				<?php
				if(!empty($expense_results) or !empty($vendor_expenses))
				{
					echo '<li>
					<table width="100%">
					<tr>
					<th><b><u>Employee</u></b></th>
					<th><b><u>Date</u></b></th>
					<th><b><u>Expense Type</u></b></th>
					<th><b><u>Expense Total</u></b></th>
					<th><b><u>Notes</u></b></th>
					</tr>';
					
					$total = 0;
					if(!empty($expense_results)){$markup = $expense_results[0]->markup;}else{$markup = $vendor_expenses[0]->markup;}
					foreach($expense_results as $er)
					{
						if($er->ID===$project)
						{
							$billable = round(($er->expense_amount*$er->expense_quantity)*(1+$markup),2);
							echo '<tr>';
							echo '<td>'.$er->display_name.'</td>';
							echo '<td>'.date('m-d-Y',$er->expense_date).'</td>';
							echo '<td>'.$er->expense_code_name.'</td>';
							echo '<td>$'.number_format($billable,2).'</td>';
							echo '<td>'.$er->employee_expense_notes.'</td>';
							echo '</tr>';
							$total += $billable;
						}
					}
					foreach($vendor_expenses as $v)
					{
						if($v->ID===$project)
						{
							$billable = round(($v->vendor_fee + $v->vendor_expense)*(1+$markup),2);
							echo '<tr>
								<td>'.$v->vendor_name.'</td>
								<td>'.date('m-d-Y',$v->expense_date).'</td>
								<td>'.$v->v_exp_name.'</td>
								<td>$'.number_format($billable,2).'</td>
								<td>'.$v->notes.'</td>
								</tr>';
							$total += $billable;
						}
					}
					echo '<tr><td>&nbsp;</td></tr>';
					echo '<tr><td><strong>Total</strong></td><td>&nbsp;</td><td>&nbsp;</td><td><strong>$'.number_format($total,2).'</strong></td></tr>';
					echo '</table></li>';
				}
				else
				{
					echo 'There are currently no expenses to be billed on this project.';
				}
				?>
				</ul>
				</div>
				</div>
			</div>
			</div></div>
			</form>
<?php get_footer(); ?>