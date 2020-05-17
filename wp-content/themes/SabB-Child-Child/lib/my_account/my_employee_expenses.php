<?php
function billyB_my_employee_expenses()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb;
	get_currentuserinfo();
	
	if(isset($_POST['change_user_submit'])){$uid=$_POST['change_user'];}
	else{$uid = $current_user->ID;}
?>
		<form method="post" name="change_user" enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
				<div class="padd10"><h3><?php echo "Unsubmitted Expenses";?></h3>
					<ul class="other-dets_m">
<?php 				
		$expensehistoryresult = $wpdb->get_results($wpdb->prepare("select expense_quantity,expense_amount,expense_report_id,expense_submit_date,ee_mastercard 
			from ".$wpdb->prefix."employee_expenses 
			where employee_id=%d and employee_expense_status=0 group by expense_report_id order by expense_submit_date desc limit 10",$uid));
		if(empty($expensehistoryresult)){echo "You don't have any unsubmitted expenses";}
		else
		{
			echo '<table width ="100%"><tr><th><u>Report ID</u></th><th><u>Date</u></th><th><u>Total</u></th><th>&nbsp;</th></tr>';
			foreach ($expensehistoryresult as $data)
			{
				$report_id = $data->expense_report_id;
				$sumresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."employee_expenses where expense_report_id=%d",$report_id));
				if($data->expense_submit_date == 0){$date = "Unsubmitted";}
				else{$date = date('m-d',$data->expense_submit_date);}
				
				$sum = 0;
				for($i=0;$i<=count($sumresults);$i++)
				{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
				
				$link = '<a href="/?p_action=edit_employee_expense&ID='.$data->expense_report_id.'" class="nice_link">Edit</a>';
				if($data->ee_mastercard == 1){$link = '<a href="/corporate-visa-entry/" class="nice_link">Edit</a>';}
				
				echo '<tr><th>'.$data->expense_report_id.($data->ee_mastercard==1 ? " (MasterCard/Visa)" : ($data->ee_mastercard==2 ? " (AMEX)" : "")).'</th>
					<th>'.$date.'</th>
					<th>'.ProjectTheme_get_show_price($sum).'</th>
					<th>'.$link.'</th></tr>';
			}
			echo '</table>';
		}			
?>
					</ul>
				</div>
			</div>
		</div>
		<div id="content">
			<div class="my_box3">
				<div class="padd10"><h3><?php echo "Submitted Expenses";?></h3>
					<ul class="other-dets_m">
					<?php
						$expensehistoryresult = $wpdb->get_results($wpdb->prepare("select expense_quantity,expense_amount,expense_report_id,expense_submit_date,employee_expense_status,ee_mastercard 
							from ".$wpdb->prefix."employee_expenses 
							where employee_id=%d and employee_expense_status>0 
							group by expense_report_id 
							order by expense_date desc",$uid));
							
						if(empty($expensehistoryresult)){echo "You don't have any submitted expenses";}
						else
						{
							echo '<table width ="100%"><tr><th><u>Report ID</u></th><th><u>Date</u></th><th><u>Total</u></th><th><u>Status</u></th></tr>';
							foreach ($expensehistoryresult as $data)
							{
								$report_id = $data->expense_report_id;
								$sumquery = "select * from ".$wpdb->prefix."employee_expenses where expense_report_id='$report_id'";
								$sumresults = $wpdb->get_results($sumquery);
								$sum = 0;
								for($i=0;$i<=count($sumresults);$i++)
								{$sum += ($sumresults[$i]->expense_quantity * $sumresults[$i]->expense_amount);}
								
								$link = '<a href="?p_action=employee_expense_view&ID='.$data->expense_report_id.'">'.$data->expense_report_id.'</a>';
								if($data->employee_expense_status < 2 and $data->ee_mastercard==1)
								{$link = '<a href="/corporate-mastercard-entry/">'.$data->expense_report_id.'</a>';}
								if($data->ee_mastercard == 1){$link .= " (MasterCard)";}
								if($data->ee_mastercard == 2){$link .= " (AMEX)";}
								
								echo '<tr><th>'.$link.'</th>';
								echo '<th>'.date('m-d',$data->expense_submit_date).'</th>';
								echo '<th>'.ProjectTheme_get_show_price($sum).'</th>';
								if($data->employee_expense_status == 1){echo '<th>Submitted</th>';}
								elseif($data->employee_expense_status == 2){echo '<th>Approved - in process</th>';}
								elseif($data->employee_expense_status == 3){echo '<th>Approved - in process</th>';}
								elseif($data->employee_expense_status == 4){echo '<th>Queued for Payment/Paid</th>';}
								echo '</tr>';
							}
							echo '</table>';
						}
					?>
					</ul>
				</div>
			</div>
		</div>
		<?php
		$change_array = array(11,94,235, 293);
		if(in_array($current_user->ID,$change_array))
		{
			?>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10">
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<?php
						$user_results = $wpdb->get_results($wpdb->prepare("select ID,display_name from ".$wpdb->prefix."users where ID not in (1,2,29) order by display_name"));
						//echo '<li>';
						echo '<select class="do_input_new" name="change_user">';
						foreach($user_results as $u)
						{
							echo '<option value="'.$u->ID.'" '.($u->ID==$uid ? 'selected="selected"' : '').' >'.$u->display_name.'</option>';
						}
						echo '</select>';
						//echo '</li>';
						echo '<input type="submit" name="change_user_submit" value="Change User" />';
						?>
					</li>
				</ul>
			</div></div>
		<?php }
		?>
		</form>
		<?php
		
	}

add_shortcode('my_employee_expenses','billyB_my_employee_expenses')
?>