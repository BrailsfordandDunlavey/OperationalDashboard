<?php
function billyb_fee_invoicing_dashboard()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if($uid != 11){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	if($uid==11){$uid = 163;}

	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];//employee expenses
		$now = time();
		$vendors = $_POST['vendor'];//vendor payables
		
		foreach($records as $record)
		{
			$details = explode(',,,',$record['details']);
			$expense_id = $details[0];
			$original_status = $details[1];
			if($record['bill'] != "no_bill")
			{
				if($record['bill'] == "billed"){$status = 2;}
				elseif($record['bill'] == "to_bill"){$status = 1;}
				elseif($record['bill'] == "confirmed"){$status = 3;}
				else{$status = 0;}
				if($status != $original_status)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=%d,billed_by=%d,billed_date=%d where employee_expense_id=%d",
						$status,$uid,$now,$expense_id));
				}
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=0,expense_billable=3 where employee_expense_id=%d",$expense_id));
			}
		}
		foreach($vendors as $vendor)
		{
			$details = explode(',,,',$vendor['details']);
			$payable_id = $details[0];
			$original_status = $details[1];
			if($vendor['bill'] != "no_bill")
			{
				if($vendor['bill'] == "billed"){$status = 2;}
				elseif($vendor['bill'] == "to_bill"){$status = 1;}
				elseif($vendor['bill'] == "confirmed"){$status = 3;}
				else{$status = 0;}
				if($status != $original_status)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set billed_status=%d,billed_by=%d,billed_date=%d where vendor_payable_id=%d",
						$status,$uid,$now,$payable_id));
				}
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set expense_billable=3,billed_status=0 where vendor_payable_id=%d",$payable_id));
			}
		}
	}
	?>   
	<script type="text/javascript">
	
	</script>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post" name="billable_expenses" enctype="multipart/form-data">
				<div id="content_full"><h3>Invoicing Dashboard</h3>
					<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">
						<?php
						$selected_month = time();
						$beg_month = strtotime(date('Y-m-01',$selected_month));
						$projects = $wpdb->get_results($wpdb->prepare("select ".$wpdb->prefix."projects.ID,project_name,abbreviated_name,gp_id,display_name
							from ".$wpdb->prefix."projects
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."users.ID
							where project_manager=%d and ".$wpdb->prefix."projects.status=2",$uid,$beg_month));
							
						if(!empty($projects))
						{
							
							
							echo '<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li>';
							echo '<li>&nbsp;</li>';
							echo '<li><table width="100%">';
							echo '<tr>
								<th><b><u>Project</u></b></th>
								<th><b><u>Fee Amount</u></b></th>
								<th><b><u>Status</u></b></th>
								<th style="text-align:center;"><b><u>To Be Billed</u></b></th>
								<th style="text-align:center;"><b><u>On Invoice</u></b></th>
								<th style="text-align:center;"><b><u>Confirmed Invoiced</u></b></th>
								<th style="text-align:center;"><b><u>Move to No-Bill</u></b></th>
								<th style="text-align:center;"><b><u>Do Nothing</u></b></th>
								</tr>';
							foreach($projects as $p)
							{
								$rev_results = $wpdb->get_results($wpdb->prepare("select projected_revenue from ".$wpdb->prefix."projected_revenue
									where project_id=%d and month=%d",$p->ID,$beg_month));
								
								echo '<tr>
									<td>'.$p->gp_id.'</td>
									<td>'.number_format($rev_results[0]->projected_revenue,2).'</td>
									</tr>';
							}
							echo '</table></li>';
							echo '<li>&nbsp;</li>';
							echo '<li><input type="submit" class="my-buttons" name="save-info" value="SAVE" /></li>';
						}
						else
						{
							echo '<li>You are not currently listed as the Project Manager on any projects</li>';
						}
						?>
					</ul>
					</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php 
}

add_shortcode('invoicing_dashboard','billyb_fee_invoicing_dashboard') ?>