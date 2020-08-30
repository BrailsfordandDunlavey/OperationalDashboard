<?php
function billyB_venues_query()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 
	$markets_results = $wpdb->get_results("select * from ".$wpdb->prefix."venues_markets order by vm_name");
	
	if(isset($_POST['save-info']))
	{
		$market_id = $_POST['market'];
		$all_markets = array($market_id);
		
		$comp_markets = array();
		$comparables = $_POST['comp_markets'];
		
		foreach($comparables as $key => $value)
		{
			array_push($comp_markets,$value);
			array_push($all_markets,$value);
		}
	}
 ?>
	<form name="new_exp" method="post"  enctype="multipart/form-data" onsubmit="checkForm();">
		<div id="content">
			<div class="my_box3">
				<div class="padd10">				
					<ul class="other-dets_m">
					<?php
					echo '<li><h2>Primary Market</h2><p><select name="market" class="do_input_new">';
					echo '<option value="">Select Market</option>';
					foreach($markets_results as $mr)
					{
						echo '<option value="'.$mr->vm_id.'" '.($market_id==$mr->vm_id ? "selected='selected'" : "").'>'.$mr->vm_name.'</option>';
					}
					echo '</select></p></li>';
					
					echo '<li><h2>Comparable Markets</h2><p><select name="comp_markets[]" multiple size="12">';
					foreach($markets_results as $mr)
					{
						if($mr->vm_id != $market_id)
						{
							echo '<option value="'.$mr->vm_id.'" '.(in_array($mr->vm_id,$comp_markets) ? "selected='selected'" : "" ).'>'.$mr->vm_name.'</option>';
						}
					}
					echo '</select></p></li>';
					
					echo '<li>&nbsp;</li>';
					echo '<li><input type="submit" name="save-info" class="my-buttons" value="Submit" /></li>';
					
					if($market_id != 0)
					{
						$sport = 'NBA';
						echo '<li><h2>Market Size</h2>';
						echo '<p><table width="100%">';
						echo '<tr>
							<th><strong>Market</strong></th>
							<th><strong>Total Population</strong></th>
							<th><strong>Total Households</strong></th>
							<th><strong>Household Size</strong></th>
							</tr>';
						
						$select_info_query = "select vm_id,vm_name,population,total_households,corp_fortune,corp_1,corp_5,corp_10,corp_20,
							corp_50,corp_100,corp_250,corp_500,corp_1000,vf_luxury_number,vf_loge_number,vf_club_seats,hh_inc_150,hh_inc_200
							from ".$wpdb->prefix."vm_act_demographics 
							inner join ".$wpdb->prefix."venues_markets on ".$wpdb->prefix."vm_act_demographics.market_id=".$wpdb->prefix."venues_markets.vm_id
							left join ".$wpdb->prefix."venues_facilities on ".$wpdb->prefix."vm_act_demographics.market_id=".$wpdb->prefix."venues_facilities.market_id and vf_sports like '%$sport%'
							left join ".$wpdb->prefix."vm_corp on ".$wpdb->prefix."vm_act_demographics.market_id=".$wpdb->prefix."vm_corp.market_id
							where ".$wpdb->prefix."vm_act_demographics.market_id in (";
							$filter = "";
							for($i=0;$i<count($all_markets);$i++)
							{
								if($i<(count($all_markets)-1))
								{
									$filter .= $all_markets[$i].",";
								}
								else
								{
									$filter .= $all_markets[$i];
								}
							}
							$select_info_query .= $filter;
							$select_info_query .= ") order by population desc";
						$info_results = $wpdb->get_results($select_info_query);
						
						$count = 0;
						$total_pop = 0;
						$total_households = 0;
						$total_bus_100 = 0;
						$total_large_businesses = 0;
						$total_hh_150 = 0;
						$market_array = array();
						
						foreach($info_results as $ir)
						{
							$count++;
							if($ir->vm_id==$market_id)
							{
								$rank = $count;
								$selected_market = $ir->vm_name;
								$selected_population = $ir->population;
								$selected_households = $ir->total_households;
								$selected_hh_size = ($selected_population/$selected_households);
								$selected_large_businesses = ($ir->corp_500 + $ir->corp_1000);
								$selected_suites_per = $ir->vf_luxury_number/$selected_large_businesses;
								$selected_suites = $ir->vf_luxury_number;
								$selected_loge_boxes = $ir->vf_loge_number;
								$selected_club_seats = $ir->vf_club_seats;
								$selected_total_preimium = $ir->vf_luxury_number + $ir->vf_loge_number + $ir->vf_club_seats;
								$selected_bus_100 = $ir->corp_100 + $ir->corp_250 + $ir->corp_500 + $ir->corp_1000;
								$selcted_hh_150 = $ir->hh_inc_150 + $ir->hh_inc_200;
							}
							$total_pop += $ir->population;
							$total_households += $ir->total_households;
							$total_bus_100 += ($ir->corp_100 + $ir->corp_250 + $ir->corp_500 + $ir->corp_1000);
							$total_large_businesses += ($ir->corp_500 + $ir->corp_1000);
							$total_hh_150 += ($ir->hh_inc_150 + $ir->hh_inc_200);
							
							if(!in_array($ir->vm_id,$market_array))
							{
								echo '<tr>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$ir->vm_name.'</strong>' : $ir->vm_name ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->population).'</strong>' : number_format($ir->population) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->total_households).'</strong>' : number_format($ir->total_households) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.round($ir->population / $ir->total_households,2).'</strong>' : round($ir->population / $ir->total_households,2) ).'</td>
									</tr>';
							}
							array_push($market_array,$ir->vm_id);
						}
						$selected_household_rank = 1;
						$selected_hh_size_rank = 1;
						$businesses_rank = 1;
						$suites_per_rank = 1;
						$suites_rank = 1;
						$loge_boxes_rank = 1;
						$club_seats_rank = 1;
						$total_premium_rank = 1;
						foreach($info_results as $ir_ranking)
						{
							if($ir_ranking->vm_id != $market_id)
							{
								if($ir_ranking->total_households > $selected_households){$selected_household_rank ++;}
								if(($ir_ranking->population / $ir_ranking->total_households) > $selected_hh_size){$selected_hh_size_rank ++;}
								if(($ir_ranking->corp_500 + $ir_ranking->corp_1000) > $selected_large_businesses){$businesses_rank ++;}
								if($ir_ranking->vf_luxury_number/($ir_ranking->corp_500 + $ir_ranking->corp_1000) > $selected_suites_per){$suites_per_rank ++;}
								if($ir_ranking->vf_luxury_number > $selected_suites){$suites_rank ++;}
								if($ir_ranking->vf_loge_number > $selected_loge_boxes){$loge_boxes_rank ++;}
								if($ir_ranking->vf_club_seats > $selected_club_seats){$club_seats_rank ++;}
								if(($ir_ranking->vf_luxury_number + $ir_ranking->vf_loge_number + $ir_ranking->vf_club_seats) > $selected_total_preimium){$total_premium_rank ++;}
							}
						}
						echo '<tr><td>&nbsp;</td></tr>';
						echo '<tr>
							<td><strong>Average</strong></td>
							<td><strong>'.number_format($total_pop / $count).'</strong></td>
							<td><strong>'.number_format($total_households / $count).'</strong></td>
							<td><strong>'.round($selected_hh_size, 2).'</strong></td>
							</tr>';
						echo '<tr><td>&nbsp;</td></tr>';
						
						echo '<tr>
							<td><strong><font color="gray">'.$selected_market.'</font></strong></td>
							<td><strong><font color="gray">'.number_format($selected_population).'</font></strong></td>
							<td><strong><font color="gray">'.number_format($selected_households).'</font></strong></td>
							<td><strong><font color="gray">'.round(($selected_population / $selected_households),2).'</font></strong></td>
							</tr>';
						
						echo '<tr>
							<td><strong><font color="orange">Comparable Rank</font></strong></td>
							<td><strong><font color="orange">'.$rank.' of '.$count.'</font></strong></td>
							<td><strong><font color="orange">'.$selected_household_rank.' of '.$count.'</font></strong></td>
							<td><strong><font color="orange">'.$selected_hh_size_rank.' of '.$count.'</font></strong></td>
							</tr>';
						
						echo '</table></p></li>';
						
						echo '<li>&nbsp;</li>';
						echo '<li>Note:  Markets sorted by total population<br/>';
						echo 'Source:  SitesUSA</li>';
						/********************************************************************************************************/
						echo '<li>&nbsp;</li>';
						echo '<li><h2>Market Premium Seating</h2>';
						echo '<p><table width="100%">';
						echo '<tr>
							<th><strong>Market</strong></th>
							<th><strong>MSA Population</strong></th>
							<th><strong>Business Establishments</strong></th>
							<th><strong>Suites Per Business (500+)</strong></th>
							<th><strong>Suites</strong></th>
							<th><strong>Loge Boxes</strong></th>
							<th><strong>Club Seats</strong></th>
							<th><strong>Total Premium</strong></th>
							</tr>';
						$market_array = array();
						
						foreach($info_results as $ir)
						{
							$total_businesses = $ir->corp_500 + $ir->corp_1000;
							$suites_per = round($ir->vf_luxury_number/$total_businesses, 2);
							$total_premium = number_format($ir->vf_luxury_number + $ir->vf_loge_number + $ir->vf_club_seats);
							
							if(!in_array($ir->vm_id))
							{
								echo '<tr>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$ir->vm_name.'</strong>' : $ir->vm_name ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->population).'</strong>' : number_format($ir->population) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($total_businesses).'</strong>' : number_format($total_businesses) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$suites_per.'</strong>' : $suites_per ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->vf_luxury_number).'</strong>' : number_format($ir->vf_luxury_number) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->vf_loge_number).'</strong>' : number_format($ir->vf_loge_number) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->vf_club_seats).'</strong>' : number_format($ir->vf_club_seats) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$total_premium.'</strong>' : $total_premium ).'</td>
									</tr>';
							}
							array_push($market_array,$ir->vm_id);
						}
						echo '<tr><td>&nbsp;</td></tr>';
						echo '<tr>
							<td><strong><font color="red">'.$selected_market.'</font></strong></td>
							<td><strong><font color="red">'.number_format($selected_population).'</font></strong></td>
							<td><strong><font color="red">'.number_format($selected_large_businesses).'</font></strong></td>
							<td><strong><font color="red">'.round($selected_suites_per, 2).'</font></strong></td>
							<td><strong><font color="red">'.$selected_suites.'</font></strong></td>
							<td><strong><font color="red">'.$selected_loge_boxes.'</font></strong></td>
							<td><strong><font color="red">'.number_format($selected_club_seats).'</font></strong></td>
							<td><strong><font color="red">'.number_format($selected_total_preimium).'</font></strong></td>
							</tr>';
						echo '<tr>
							<td><strong><font color="red">Rank</font></strong></td>
							<td><strong><font color="red">'.$rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$businesses_rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$suites_per_rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$suites_rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$loge_boxes_rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$club_seats_rank.' / '.$count.'</font></strong></td>
							<td><strong><font color="red">'.$total_premium_rank.' / '.$count.'</font></strong></td>
							</tr>';
						echo '</table></p></li>';
						
						echo '<li>&nbsp;</li>';
						echo '<li>Source:  SitesUSA</li>';
						/*****************************************************************************************************/
						echo '<li>&nbsp;</li>';
						echo '<li><h2>Share Analysis</h2>';
						echo '<p><table width="100%">';
						echo '<tr>
							<th>&nbsp;</th>
							<th colspan="3" style="text-align:center;"><strong>Total Premium Seats</strong></th>
							<th colspan="3" style="text-align:center;"><strong>Luxury Suites</strong></th>
							<th colspan="3" style="text-align:center;"><strong>Club Seats</strong></th>
							</tr>';
						echo '<tr>
							<th><strong>Market</strong></th>
							<th><strong>Quantity</strong></th>
							<th><strong>Business w/ 100+ Employees</strong></th>
							<th><strong>Share Ratio</strong></th>
							<th><strong>Quantity</strong></th>
							<th><strong>Businesses w/ 500+ Employees</strong></th>
							<th><strong>Share Ratio</strong></th>
							<th><strong>Quantity</strong></th>
							<th><strong>HH Income of $150,000+ / Year</strong></th>
							<th><strong>Share Ratio</strong></th>
							</tr>';
						$market_array = array();
						
						foreach($info_results as $ir)
						{
							$large_businesses = $ir->corp_500 + $ir->corp_1000;
							$med_businesses = $large_businesses + $ir->corp_100 + $ir->corp_250;
							$total_premium = number_format($ir->vf_luxury_number + $ir->vf_club_seats);
							$hh_150 = ($ir->hh_inc_150 + $ir->hh_inc_200)/100 * $ir->total_households;
							
							if(!in_array($ir->vm_id,$market_array))
							{
								echo '<tr>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$ir->vm_name.'</strong>' : $ir->vm_name ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($total_premium).'</strong>' : number_format($total_premium) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($med_businesses).'</strong>' : number_format($med_businesses) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.round($total_premiums / $med_businesses, 2).'</strong>' : round($total_premium / $med_businesses,2) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($ir->vf_luxury_number).'</strong>' : number_format($ir->vf_luxury_number) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($large_businesses).'</strong>' : number_format($large_businesses) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.round($ir->vf_luxury_number / $large_businesses, 2).'</strong>' : round($ir->vf_luxury_number / $large_businesses, 2) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.$ir->vf_club_seats.'</strong>' : $ir->vf_club_seats ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.number_format($hh_150).'</strong>' : number_format($hh_150) ).'</td>
									<td>'.($ir->vm_id==$market_id ? '<strong>'.round($hh_150 / $ir->vf_club_seats, 2).'</strong>' : round($hh_150 / $ir->vf_club_seats, 2) ).'</td>
									</tr>';
							}
						}
						echo '<tr><td>&nbsp;</td></tr>';
						echo '</table></p></li>';
						
						echo '<li>&nbsp;</li>';
						echo '<li>Note:  Markets sorted by total population<br/>';
						echo 'Source:  SitesUSA</li>';
						
						//BillyB try new array with sorts
						if($current_user->ID==11)
						{
							echo '<li>Hello Bill</li>';
							$new_array = array();
							
							foreach($info_results as $ir)
							{
								$record_array = array();
								array_push($record_array,$ir->vm_id,$ir->vm_name,$ir->population);
								array_push($new_array,$record_array);
							}
							
							
							
							foreach($new_array as $key => $row)
							{
								$population[$key] = $row['2'];
							}
							array_multisort($population,SORT_DESC,$new_array);
							
							foreach($new_array as $na)
							{
								echo '<li>'.$na[1].' - '.number_format($na[2]).'</li>';
							}
							
							//print_r($new_array);
							/* not working...
							function cmp($a,$b){
								if($a->population == $b->population){
									return 0;
								}
								return ($a->poplulation > $b->population) ? -1 : 1;
							}
							uasort($new_array,'cmp');
							
							if($current_user->ID == 11)
							{
								foreach($new_array as $na)
								{
									echo '<li>'.$na->vm_id.' - '.$na->vm_name.' - '.$na->population.'</li>';
								}
								print_r($new_array);
							*/
							//end BillyB experiment
							}
					}
					?>
					</ul>
</div>
	
</div> </div></form>
				
<?php }
add_shortcode('venues_query','billyB_venues_query')
?>