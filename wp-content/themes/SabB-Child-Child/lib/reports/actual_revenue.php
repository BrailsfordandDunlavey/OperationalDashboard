<?php
function billyB_actual_revenue()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php?redirect_to=Actual-Revenue"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	
	if(isset($_POST['save'])){$selected_month=$_POST['month'];}
	
	?>
	<form method="post"  enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">			
			<?php
			$current_month = strtotime(date('Y-m-t',time()));
			//$beg_month = strtotime(date('Y-m-01',$current_month));
			if(empty($selected_month)){$selected_month=$current_month;}
			$selectable_months = array();
			
			$months = 24;
			
			for($i=0;$i<$months;$i++)
			{
				array_push($selectable_months,strtotime(date('Y-m-t',strtotime(date('Y-m-01',$current_month) .' - '.$i.' months'))));
			}
			
			echo '<li><select name="month" class="do_input_new"	>';
			foreach($selectable_months as $s)
			{
				echo '<option value="'.$s.'" '.($s==$selected_month ? 'selected="selected"' : '').'>'.date('m-d-Y',$s).'</option>';
			}
			echo '</select></li>';
			echo '<li><input type="submit" name="save" class="my-buttons-submit" value="Save" /></li>';
			echo '<li>&nbsp;</li>';
			
			$months_array = array($selected_month);
			$months_to_display = 12;
			for($i=1;$i<$months_to_display;$i++)
			{
				array_push($months_array,strtotime(date('Y-m-t',strtotime(date('Y-m-01',$current_month) .' - '.$i.' months'))));
			}
			
			$end_month = $months_array[$months_to_display-1];
			
			$invoices = $wpdb->get_results($wpdb->prepare("select invoice_id,invoice_period,invoice_fee_amount,invoice_expense_amount,invoice_no_bill,
				abbreviated_name,project_name,gp_project_number,client_name,project_id
				from ".$wpdb->prefix."invoices
				inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."invoices.project_id=".$wpdb->prefix."projects.ID
				inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
				where invoice_period>=%d and invoice_period<=%d
				order by sphere,client_name,gp_project_number,invoice_period desc",$end_month,$selected_month));
			
			$active_project = 0;
			print_r($months_array);
			echo '<li><table width="100%">';
			echo '<tr>
				<th><b><u>Project</u></b></th>';
			foreach($months_array as $m)
			{
				echo '<th><b><u>'.date('m-d-Y',$m).'</u></b></th>';
			}
			echo '</tr>';
			
			foreach($invoices as $inv)
			{
				if($inv->project_id==$active_project)
				{
					$value = array_search($inv->invoice_period,$months_array);
					if(empty($value))
					{
						echo '<td>Error: '.$inv->invoice_id.'</td>'; break;
						//$value = $current_value-1;
					}
					
					if($value != ($current_value-1))
					{
						for($i=$value;$i<$current_value;$i++)
						{
							echo '<td>$0.00</td>';//enter blank cells
						}
					}
					
					echo '<td>$'.number_format($inv->invoice_fee_amount,2).($inv->project_id==281 ? '<br/>'.$value.' - '.$current_value : '' ).'</td>';
					$current_value = $value;
				}
				else
				{
					if($current_value!=0)
					{
						for($i=0;$i<$current_value;$i++)
						{
							echo '<td>$0.00</td>';
						}
					}
					echo '</tr>';
					$current_value = 11;//position of the oldest month in the months_array
					//if($inv->invoice_id != $invoices[0]->invoice_id){echo '</tr>';}
					echo '<tr>
						<td>'.$inv->project_id.'</td>';
					
					if($end_month != $inv->invoice_period)
					{
						$value = array_search($inv->invoice_period,$months_array);
						if(empty($value)){$value = $current_value;}
						
						for($new_i=$value;$new_i<$current_value;$new_i++)
						{
							echo '<td>&nbsp;</td>';//enter blank cells
						}
						echo '<td>$'.number_format($inv->invoice_fee_amount,2).'</td>';
					}
					
					$current_value = $value;
					
				}
				$active_project = $inv->project_id;
				
			}
			
			echo '</tr>';
			
			echo '</table></li>';
			
			//print_r($invoices);
			?>
			</ul>
			</div>
			</div>						
		</div>
	</form>

	<?php 
} 
add_shortcode('actual_revenue','billyB_actual_revenue')
?>