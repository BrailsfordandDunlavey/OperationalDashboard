<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;

$selected_month = $_GET['ID'];

$number_months = 24;
$now = time();
$current_month = date('Y-m-t',$now);
$months = array($current_month);
$months_time = array(strtotime($current_month));
for($i=1;$i<$number_months;$i++)
{
	$month = date('Y-m-t',strtotime(date("Y-m-01", strtotime($current_month)) . " -".$i." months"));
	$month_time = strtotime($month);
	array_push($months,$month);
	array_push($months_time,$month_time);
}

?>
<div id="main_wrapper">
<div id="main" class="wrapper">		
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
		<div class="my_box3">
		<div class="padd10">
		<ul class="other-dets_m">
			<?php
			if(isset($_POST['set_start'])){$selected_month = ($_POST['select_start']);}
			?>
			<li><select class="do_input_new" name="select_start">
			<?php
			foreach($months as $option)
			{
				echo '<option value="'.strtotime($option).'" '.(strtotime($option) == $selected_month ? "selected='selected'" : "" ).'>'.date('M Y',strtotime($option)).'</option>';
			}
			?>
			</select>&nbsp;&nbsp;
			<input type="submit" name="set_start" class="my-buttons-submit" value="<?php echo "Set Period"; ?>" /></li>
		</ul>
		</div>
		</div>
		</div>
		<div id="content-full">
		<div class="my_box3">
		<div class="padd10">
		<ul class="other-dets_m">
		<?php						
		$beg_month = strtotime(date('Y-m-01',$selected_month));
		$contracts_query = $wpdb->prepare("select * from ".$wpdb->prefix."projects	
			inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
			where estimated_start<=%d and estimated_start>=%d and win_loss=1 
			order by sphere,estimated_start",$selected_month,$beg_month);
		$contracts_results = $wpdb->get_results($contracts_query);
		
		echo '<li><table width="100%">';
		echo '<tr>
				<th><b><u>Sphere</u></b></th>
				<th><b><u>Client Name</u></b></th>
				<th><b><u>Project Name</u></b></th>
				<th><b><u>Estimated Start</u></b></th>
				<th><b><u>Total Value</u></b></th>
				<th><b><u>Fee Value</u></b></th>
				<th><b><u>Sub Fee Value</u></b></th>
				<th><b><u>Expenses</u></b></th>
				<th><b><u>Submission Type</u></b></th>
				</tr>';
		$total_total = 0;
		$total_fee = 0;
		$total_sub = 0;
		$total_expense = 0;
		foreach($contracts_results as $contract)
		{
			$total_value = 0;
			$total_value += $contract->fee_amount;
			$total_value += $contract->sub_fee_amount;
			$total_value += $contract->expense_amount;
			$total_total += $total_value;
			$total_fee += $contract->fee_amount;
			$total_sub += $contract->sub_fee_amount;
			$total_expense += $contract->expense_amount;
			
			echo '<tr>
				<td>'.$contract->sphere.'</td>
				<td>'.$contract->client_name.'</td><td><a href="?p_action=project_card&ID='.$contract->ID.'">'.$contract->project_name.'</a></td>
				<td>'.date('m-d-Y',$contract->estimated_start).'</td>
				<td>$'.number_format($total_value,2).'</td>
				<td>$'.number_format($contract->fee_amount,2).'</td>
				<td>$'.number_format($contract->sub_fee_amount,2).'</td>
				<td>$'.number_format($contract->expense_amount,2).'</td>
				<td>'.$contract->initiation_document.'</td>
				</tr>';
		}
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td><b>Total</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
			<td><b>$'.number_format($total_total,2).'</b></td>
			<td><b>$'.number_format($total_fee,2).'</b></td>
			<td><b>$'.number_format($total_sub,2).'</b></td>
			<td><b>$'.number_format($total_expense,2).'</b></td></tr>';
		
		echo '</table></li>';
		?>
		<li>&nbsp;</li>
		</ul>
		</div>
		</div>
		</div>
	</form>
</div>
</div>
<?php get_footer(); ?>	