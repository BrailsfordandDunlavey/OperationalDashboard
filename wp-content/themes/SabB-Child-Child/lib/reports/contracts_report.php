<?php
function billyb_contracts_report()
{ 

	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	//wp_mail('mmitchell@programmanagers.com','What','What What');
 	$now = time();
	$current_month = date('Y-m-t',$now);
	$month2 = date('Y-m-t',strtotime(date("Y-m-01", strtotime($current_month)) . " -1 month"));
	$month3 = date('Y-m-t',strtotime(date("Y-m-01", strtotime($month2)) . " -1 month"));
	$month4 = date('Y-m-t',strtotime(date("Y-m-01", strtotime($month3)) . " -1 month"));
	$month5 = date('Y-m-t',strtotime(date("Y-m-01", strtotime($month4)) . " -1 month"));
	$month6 = date('Y-m-t',strtotime(date("Y-m-01", strtotime($month5)) . " -1 month"));
	$months = array($current_month,$month2,$month3,$month4,$month5,$month6);

	$current_month_time = strtotime($current_month);
	$month2_time = strtotime($month2);
	$month3_time = strtotime($month3);
	$month4_time = strtotime($month4);
	$month5_time = strtotime($month5);
	$month6_time = strtotime($month6);
	$months_time = array($current_month_time,$month2_time,$month3_time,$month4_time,$month5_time,$month6_time);
	
	$selected_month = $month2_time;
	
	if(isset($_POST['update-info']))
	{
		$selected_month = $_POST['select_period'];
	}
	?>   
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post"  enctype="multipart/form-data">
				<div id="content">
					<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">
					<li><select name="select_period" class="do_input_new">
					<?php
					echo '<option value="'.$current_month_time.'" '.($current_month_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$current_month_time).'</option>';
					echo '<option value="'.$month2_time.'" '.($month2_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month2_time).'</option>';
					echo '<option value="'.$month3_time.'" '.($month3_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month3_time).'</option>';
					echo '<option value="'.$month4_time.'" '.($month4_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month4_time).'</option>';
					echo '<option value="'.$month5_time.'" '.($month5_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month5_time).'</option>';
					echo '<option value="'.$month6_time.'" '.($month6_time==$selected_month ? "selected='selected'" : "").'>'.date('m-d-Y',$month6_time).'</option>';
					?>
					</select>&nbsp;
					<input type="submit" name="update-info" value="Update Period" class="my-buttons" /></li>
					</ul>
					</div>
					</div>
				</div>
				<div id="content">
					<div class="my_box3">
					<div class="padd10">					
					<ul class="other-dets_m">
						<li>&nbsp;</li>
						<li>
						<table width="100%"><h2><?php echo "Contracts for ".date('F Y',$selected_month);?></h2>
						<tr>
						<th><b><u><?php echo "Number of Contracts";?></u></b></th>
						<th><b><u><?php echo "Total value of Contracts";?></u></b></th>
						<th><b><u><?php echo "Fee value of Contracts";?></u></b></th>
						</tr>
						<?php
						$beg_month = strtotime(date('Y-m-01',$selected_month));
						$contracts_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects	
							where estimated_start<=%d and win_loss=1 and estimated_start>=%d",$selected_month,$beg_month));
						
						$total_value = 0;
						$fee_value = 0;
						foreach($contracts_results as $contract)
						{
							$total_value += $contract->fee_amount;
							$total_value += $contract->sub_fee_amount;
							$total_value += $contract->expense_amount;
							$fee_value += $contract->fee_amount;
						}
						echo '<tr><th>'.count($contracts_results).'</th><th>$'.number_format($total_value,2).'</th>
							<th>$'.number_format($fee_value,2).'</th></tr>';
						?>
						</table>
						</li>
						<li><a href="/?p_action=detailed_contracts_report&ID=<?php echo $selected_month;?>" class="nice_link">Get Details</a></li>
						<li>&nbsp;</li>
						<li>
						<table width="100%"><h2><?php echo "Contracts for ".date('Y',$selected_month)." YTD";?></h2>
						<tr>
						<th><b><u><?php echo "Number of Contracts";?></u></b></th>
						<th><b><u><?php echo "Total value of Contracts";?></u></b></th>
						<th><b><u><?php echo "Fee value of Contracts";?></u></b></th>
						</tr>
						<?php
						$beg_year = strtotime(date('Y-01-01',$selected_month));
						$ytd_contracts_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects 
							where estimated_start<=%d and win_loss=1 and estimated_start>=%d",$selected_month,$beg_year));
						
						$ytd_total_value = 0;
						$ytd_fee_value = 0;
						foreach($ytd_contracts_results as $ytd)
						{
							$ytd_total_value += $ytd->fee_amount;
							$ytd_total_value += $ytd->sub_fee_amount;
							$ytd_total_value += $ytd->expense_amount;
							$ytd_fee_value += $ytd->fee_amount;
						}
						echo '<tr><th>'.count($ytd_contracts_results).'</th><th>$'.number_format($ytd_total_value,2).'</th>
							<th>$'.number_format($ytd_fee_value,2).'</th></tr>';
						?>
						</table>
						</li>
						<li>&nbsp;</li>
						<li><table width="100%"><h2>Contracts by Market:Submarket for <?php echo date('F Y',$selected_month);?></h2>
						<tr><th><b><u>Market</u></b></th><th><b><u>Number of Contracts</u></b></th><th><b><u>Total Value</u></b></th><th><b><u>Fee Value</u></b></th></tr>
						<?php
						$submarket_query = $wpdb->prepare("select distinct submarket,name,parent from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."terms on ".$wpdb->prefix."projects.submarket=".$wpdb->prefix."terms.term_id
							inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."projects.submarket=".$wpdb->prefix."term_taxonomy.term_id
							where estimated_start<=%d and estimated_start>=%d and win_loss=1 order by market",$selected_month,$beg_month);
						$submarket_results = $wpdb->get_results($submarket_query);
						foreach($submarket_results as $submarket)
						{
							$submarket_id = $submarket->submarket;
							$submarket_name = $submarket->name;
							$market_id = $submarket->parent;
							
							$market_name_query = $wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$market_id);
							$market_name_result = $wpdb->get_results($market_name_query);
							$market_name = $market_name_result[0]->name;
							
							$sub_fee_total = 0;
							$sub_total_total = 0;
							
							$details_query = $wpdb->prepare("select * from ".$wpdb->prefix."projects where estimated_start<=%d 
								and estimated_start>=%d	and win_loss=1 and submarket=%d",$selected_month,$beg_month,$submarket_id);
							$details_results = $wpdb->get_results($details_query);
							foreach($details_results as $detail)
							{
								$sub_fee_total += $detail->fee_amount;
								$sub_total_total += $detail->sub_fee_amount;
								$sub_total_total += $detail->expense_amount;
								$sub_total_total += $detail->fee_amount;
							}
							$count = count($details_results);
							echo '<tr><th>'.$market_name.':  '.$submarket_name.'</th>
								<th>'.$count.'</th>
								<th>$'.number_format($sub_total_total,2).'</th>
								<th>$'.number_format($sub_fee_total,2).'</th></tr>';
						}
						?>
						</table></li>
						<li>&nbsp;</li>
						<li><table width="100%"><h2>Contracts by Market:Submarket for <?php echo date('Y',$selected_month).' YTD';?></h2>
						<tr><th><b><u>Market</u></b></th><th><b><u>Number of Contracts</u></b></th><th><b><u>Total Value</u></b></th><th><b><u>Fee Value</u></b></th></tr>
						<?php
						$submarket_query = $wpdb->prepare("select distinct submarket,name,parent from ".$wpdb->prefix."projects 
							inner join ".$wpdb->prefix."terms on ".$wpdb->prefix."projects.submarket=".$wpdb->prefix."terms.term_id
							inner join ".$wpdb->prefix."term_taxonomy on ".$wpdb->prefix."projects.submarket=".$wpdb->prefix."term_taxonomy.term_id
							where estimated_start<=%d and estimated_start>=%d and win_loss=1 order by market",$selected_month,$beg_year);
						$submarket_results = $wpdb->get_results($submarket_query);
						foreach($submarket_results as $submarket)
						{
							$submarket_id = $submarket->submarket;
							$submarket_name = $submarket->name;
							$market_id = $submarket->parent;
							
							$market_name_query = $wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$market_id);
							$market_name_result = $wpdb->get_results($market_name_query);
							$market_name = $market_name_result[0]->name;
								
							$sub_fee_total = 0;
							$sub_total_total = 0;
							
							$details_query = $wpdb->prepare("select * from ".$wpdb->prefix."projects where estimated_start<=%d 
								and estimated_start>=%d	and win_loss=1 and submarket=%d",$selected_month,$beg_year,$submarket_id);
							$details_results = $wpdb->get_results($details_query);
							foreach($details_results as $detail)
							{
								$sub_fee_total += $detail->fee_amount;
								$sub_total_total += $detail->sub_fee_amount;
								$sub_total_total += $detail->expense_amount;
								$sub_total_total += $detail->fee_amount;
							}
							$count = count($details_results);
							echo '<tr><th>'.$market_name.':  '.$submarket_name.'</th>
								<th>'.$count.'</th>
								<th>$'.number_format($sub_total_total,2).'</th>
								<th>$'.number_format($sub_fee_total,2).'</th></tr>';
						}
						?>
						</table></li>
					</ul>
					</div>	
					</div>
				</div>
			</form>
		</div>
	</div>
<?php }

add_shortcode('contracts_report','billyB_contracts_report') ?>