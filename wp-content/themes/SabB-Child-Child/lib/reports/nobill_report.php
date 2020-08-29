<?php
function billyb_nobill_report()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$allowed_array = array('Finance','Executive');
	$allowed_users = array(103);
	if(!in_array($rights_results[0]->team,$allowed_array) and !in_array($uid,$allowed_users)){wp_redirect(get_bloginfo('siteurl')."/dashboard");}

	$beg_period = strtotime(date('01-01-Y',time()));
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		
	}
	
	$results = $wpdb->get_results($wpdb->prepare("select display_name,expense_code_name,project_id,expense_date,expense_quantity,expense_amount,employee_expense_id,employee_expense_notes
		from ".$wpdb->prefix."employee_expenses
		left join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
		left join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
		where expense_billable=3 and project_id in ('BRDU2008TTEAMW','BRDU2008TTEAM2','BRDU2008TTEAMB') and expense_date>=".$beg_period."
		order by project_id,display_name,expense_date"));
	
	?>   
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post" name="employee_report" enctype="multipart/form-data">
				<div id="content">
					<div class="my_box3">
					<div class="padd10">					
					<ul class="other-dets_m">
					<?php
					
					if(!empty($results))
					{
						echo '<li><table width=100%>
							<tr><th>Project</th>
							<th>Employee</th>
							<th>Expense Date</th>
							<th>Expense Type</th>
							<th>Expense Amount</th>
							<th>Notes</th>
							</tr>';
							
						$project_id = $results[0]->project_id;
						
						foreach($results as $r)
						{
							echo '<tr>';
							if($r->employee_expense_id==$results[0]->employee_expense_id)
							{
								echo '<td>'.$r->project_id.'</td>';
							}
							elseif($project_id != $r->project_id)
							{
								if($r->project_id != $results[0]->project_id)
								{
									echo '<td>&nbsp;</td></tr>';
									echo '<tr><td></td><td>Total</td><td></td><td></td><td>$'.number_format($total,2).'</td></tr>';
									echo '<tr><td>&nbsp;</td><tr>';
									echo '<tr>';
									$total = 0;
								}
								echo '<td>'.$r->project_id.'</td>';
								$project_id = $r->project_id;
							}
							else
							{
								echo '<td>&nbsp;</td>';
							}
							echo '<td>'.$r->display_name.'</td>
								<td>'.date('m-d-Y',$r->expense_date).'</td>
								<td>'.$r->expense_code_name.'</td>
								<td>$'.number_format($r->expense_quantity*$r->expense_amount,2).'</td>
								<td>'.$r->employee_expense_notes.'</td>
								</tr>';
							$total += round($r->expense_amount * $r->expense_quantity,2);
						}
						echo '<tr><td>&nbsp;</td></tr>';
						echo '<tr><td>&nbsp;</td><td>Total</td><td></td><td></td><td>$'.number_format($total,2).'</td></tr>';
						echo '</table></li>';
					}
					else
					{
						echo '<li>There are no expenses for the selected filters</li>';
					}
					
					?>
					<li>&nbsp;</li>
					</ul>
					</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php
}

add_shortcode('nobill_report','billyb_nobill_report') ?>