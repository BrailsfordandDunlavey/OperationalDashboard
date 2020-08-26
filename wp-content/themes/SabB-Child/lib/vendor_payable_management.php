<?php
function billyB_vendor_payable_management()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rights_results[0]->team;

	$allowed_teams = array('Finance');
 
	if(!in_array($team,$allowed_teams)){ wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit; }
	get_header();

	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		
		$records = $_POST['payment'];
		
		foreach($records as $r)
		{
			if($r['box'] == 'on')
			{
				$paid_date = 1535587200;
				$paid_by = $uid;
			}
			else
			{
				$paid_date = "";
				$paid_by = 0;
			}
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set paid_by=%d,paid_date=%d,paid_number=%s,billed_invoice=%s 
				where vendor_payable_id=%d",$paid_by,$paid_date,$r['text'],$r['client_inv'],$r['id']));
			
			if($r['box'] == 'on')
			{
				//email PM on the project letting them know the Sub was paid
				$pm_query = $wpdb->get_results($wpdb->prepare("select vendor_name,vendor_fee,vendor_expense,invoice_number,user_email,client_name,project_name,abbreviated_name
					from ".$wpdb->prefix."vendor_payables
					inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
					inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."vendor_payables.project_id=".$wpdb->prefix."projects.ID
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
					where vendor_payable_id=%d",$r['id']));
				if(!empty($pm_query[0]->user_email))
				{
					$vendor_name = $pm_query[0]->vendor_name;
					$client_name = $pm_query[0]->client_name;
					$project = $pm_query[0]->abbreviated_name;
					if(empty($project)){$project = $pm_query[0]->project_name;}
					$message = $vendor_name.' has been paid for invoice: '.$pm_query[0]->invoice_number.' in the amount of $'.number_format($pm_query[0]->vendor_fee + $pm_query[0]->vendor_expense,2).' for their work at '.$client_name;
					if(!empty($project)){$message .= ' on project:  '.$project;}
					$message .= '.  Please reach out to Accounting if you have any questions or issues.  Thank you.';
					$to = $pm_query[0]->user_email;
					//$to = 'bbannister@programmanagers.com'; //TEST
					wp_mail($to,$vendor_name.' has been paid by B&D',$message);
				}
			}
		}
		$_POST = array();
	}
	
		?>   
		<form method="post" enctype="multipart/form-data">
		<div id="content_full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php
			
				$payables = $wpdb->get_results("select vendor_payable_id,invoice_number,expense_date,vendor_fee,vendor_name,fee_billable,expense_billable,vendor_expense,paid_number,billed_invoice
					from ".$wpdb->prefix."vendor_payables
					left join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
					where paid_by=0");
				if(empty($payables)){echo 'No Vendor Payables are pending payment.';}
				else
				{
					echo '<li><input type="submit" name="save-info" value="save" class="my-buttons"/></li>';
					echo '<li><table width="100%">
						<tr>
						<th><strong><u>Vendor</u></strong></th>
						<th><strong><u>Invoice Number</u></strong></th>
						<th><strong><u>Invoice Date</u></strong></th>
						<th><strong><u>Invoice Amount</u></strong></th>
						<th><strong><u>Invoice to Client</u></strong></th>
						<th><strong><u>Paid</u></strong></th>
						<th><strong><u>Payment Number</u></strong></th>
						</tr>';
					foreach($payables as $p)
					{
						if($p->vendor_fee != 0 and $p->vendor_expense!=0){$amount = $p->vendor_fee+$p->vendor_expense;}
						elseif($p->vendor_fee!=0){$amount = $p->vendor_fee;}
						elseif($p->vendor_expense!=0){$amount = $p->vendor_expense;}
						else{$amount = 0;}
						if($amount != 0)
						{
							echo '<tr>
								<td><b><a href="'.get_bloginfo('siteurl').'?p_action=edit_vendor_payable&ID='.$p->vendor_payable_id.'">'.$p->vendor_name.'</b></a>
									'.($p->fee_billable==1 ? '<br/>(Fee Billable)' : '').($p->expense_billable==1 ? '<br/>(Expense Billable)' : '').'</td>
								<td>'.$p->invoice_number.'</td>
								<td>'.date('m-d-Y',$p->expense_date).'</td>
								<td>$'.number_format($amount,2).'</td>
								<input type="hidden" name="payment['.$p->vendor_payable_id.'][id]" value="'.$p->vendor_payable_id.'" />
								<td><input type="text" name="payment['.$p->vendor_payable_id.'][client_inv]" class="do_input_new" value="'.$p->billed_invoice.'" /></td>
								<td><input type="checkbox" name="payment['.$p->vendor_payable_id.'][box]" class="do_input_new" /></td>
								<td><input type="text" name="payment['.$p->vendor_payable_id.'][text]" class="do_input_new" value="'.$p->paid_number.'" /></td>
								</tr>';
						}
					}
					echo '</table></li>';
					echo '<li><input type="submit" name="save-info-two" value="save" class="my-buttons"/></li>';
				}
				
			?>
			</ul>
			<script type="text/javascript">
			/*
			function noPM(){
				var box = document.getElementById('no_pm_box');
				var allRows = document.querySelectorAll("[id^='pm']");
				if(box.checked == true){
					showRows = document.querySelectorAll("[id*='pm0']");
				}
				else{
					showRows = allRows;
				}
				for(i=0;i<allRows.length;i++){
					allRows[i].style.display = 'none';
				}
				for(i=0;i<showRows.length;i++){
					showRows[i].style.display = 'table-row';
				}
			}
			*/
			</script>
			</div>
			</div>						
		</div>
		</form>
<?php }
add_shortcode('vendor_payable_management','billyB_vendor_payable_management')
?>