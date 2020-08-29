<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	
	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_name = $current_user->display_name;
	$date = time();
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;
	
	$client = $_GET['ID'];
	
	$details = $wpdb->get_results($wpdb->prepare("select gp_id,client_name,project_name,prime_id,abbreviated_name,".$wpdb->prefix."projects.ID,prime_id,".$wpdb->prefix."projects.status,win_loss 
		from ".$wpdb->prefix."projects 
		inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
		where ".$wpdb->prefix."clients.client_id=%d
		order by ".$wpdb->prefix."projects.status",$client));
	
	function sitemile_filter_ttl($title)
	{
		global $wpdb;
		$client = $_GET['ID'];
		$query = $wpdb->get_results($wpdb->prepare("select client_name from ".$wpdb->prefix."clients where client_id=%d",$client));
		return "Farm View: ".$query[0]->client_name;
	}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
	get_header();
		
	$start_date = strtotime("12/1/2016");//1477958400;//November 1, 2016 (when timekeeping started in OpDash
	?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
			<div id="content"><h3><?php echo $details[0]->client_name;?>: Portfolio Details</h3><br/>Since December 1, 2016
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
				<table width="100%">
					<tr>
					<th><strong>Project</strong></th>
					<th><strong>Prime</strong></th>
					<th><strong>Fees Billed</strong></th>
					<th><strong>Value of Hours Worked</strong></th>
					<th><strong>Revenue Efficiency</strong></th>
					</tr>
					<?php
					$farm_value = 0;
					$farm_billed = 0;
					foreach($details as $c)
					{
						$id = $c->ID;
						$name = $c->abbreviated_name;if(empty($name)){$name = $c->project_name;}if(empty($name)){$name = $gp_id;}if(empty($name)){$name = "None";}
						
						if(!empty($c->prime_id))
						{
							$prime_results = $wpdb->get_results($wpdb->prepare("select client_name from ".$wpdb->prefix."clients where client_id=%d",$c->prime_id));
							$prime = $prime_results[0]->client_name;
						}
						else{$prime = "Brailsford & Dunlavey";}
						
						$billed_results = $wpdb->get_results($wpdb->prepare("select sum(invoice_fee_amount) as billed from ".$wpdb->prefix."invoices
							where project_id=%d and invoice_period>=%d",$id,$start_date));
						$farm_billed += $billed_results[0]->billed;
						
						$hours_results = $wpdb->get_results($wpdb->prepare("select timesheet_date,timesheet_hours,year,planning_rate,rate,".$wpdb->prefix."useradd.user_id
							from ".$wpdb->prefix."timesheets
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."useradd.user_id
							inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
							left join ".$wpdb->prefix."project_rates on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."project_rates.user_id
								and ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."project_rates.project_id
							where ".$wpdb->prefix."timesheets.project_id=%d and timesheet_date>=%d
							order by user_id,timesheet_date",$id,$start_date));
						$value = 0;
						foreach($hours_results as $hr)
						{
							if($hr->timesheet_date!=$date and $hr->timesheet_hours!=$hours)
							{
								if(date('Y',$hr->timesheet_date)==$hr->year)
								{
									$date = $hr->timesheet_date;
									$hours = $hr->timesheet_hours;
									if(!empty($hr->rate)){$rate = $hr->rate;}else{$rate = $hr->planning_rate;}
									$value += $rate * $hours;
								}
							}
						}
						if($c->status>3)
						{
							$link = 'opportunity_checklist';
							$link = 'project_card';//9-12-19 adding this since opportunity_checklist doesn't seem to be working right - blank content (header and footer are there...)
						}
						elseif($c->status<2)
						{
							$link ='edit_checklist';
						}
						else{$link = 'project_card';}
						echo '<tr>
							<td><a href="'.get_bloginfo('siteurl').'/?p_action='.$link.'&ID='.$id.'" target="_blank">'.$name.'</a></td>
							<td>'.($prime!="Brailsford & Dunlavey" ? '<a href="'.get_bloginfo('siteurl').'/?p_action=farm_view&ID='.$c->prime_id.'" target="_blank">'.$prime.'</a>'
								: $prime ).'</td>
							<td>$'.number_format($billed_results[0]->billed,2).'</td>
							<td>$'.number_format($value,2).'</td>
							<td>'.number_format($billed_results[0]->billed / $value *100,1).'%</td>
							</tr>';
						$farm_value += $value;
					}
					echo '<tr><td>&nbsp</td></tr>';
					echo '<tr>
						<td><strong>Total</strong></td>
						<td>&nbsp;</td>
						<td><strong>$'.number_format($farm_billed,2).'</strong></td>
						<td><strong>$'.number_format($farm_value,2).'</strong></td>
						<td><strong>'.number_format($farm_billed / $farm_value * 100,1).'%</td>
						</tr>';
					?>
					</table>
				</ul>
			</div></div></div>
		</form>
		</div></div>
<?php 
		get_footer();
?>