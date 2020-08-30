<?php
function billyb_invoicing_periods()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	$rightsresults = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$team = $rightsresults[0]->team;

	if($team == "Finance")
	{
		if(isset($_POST['save-info']))
		{	
			$periods = ($_POST['period']);
			foreach ($periods as $period)
			{
				$period_id = $period['id'];
				$invoice = $period['invoice'];
				$projection = $period['projection'];
					
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."invoice_periods set invoice_active=%s,projection_active=%s 
					where invoice_period_id=%d",$invoice,$projection,$period_id));
			}
		}	
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post" enctype="multipart/form-data">
                	<div id="content"><h3>Periods</h3><br/>
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
							<li>
							<?php  
							$details = $wpdb->get_results("select * from ".$wpdb->prefix."invoice_periods order by invoice_period asc");
							echo '<table width ="100%"><tr><th>Period</th><th>Open for Invoicing</th><th>Open for Projections</th></tr>';
							for($i=0;$i<count($details);$i++)
							{
								echo '<tr><th>'.date('m-d-Y',$details[$i]->invoice_period).'</th>';
								echo '<input type="hidden" name="period['.$i.'][id]" value="'.$details[$i]->invoice_period_id.'"/>';
								echo '<th><input type="checkbox" name="period['.$i.'][invoice]"';
								if($details[$i]->invoice_active=="on"){echo "checked=checked";}
								echo '></th>';
								echo '<th><input type="checkbox" name="period['.$i.'][projection]"';
								if($details[$i]->projection_active=="on"){echo "checked=checked";}
								echo '></th></tr>';
							}
							echo '</table>';
							?>
							</li>
							<li><input type="submit" name="save-info" class="my-buttons" value="Update" /></li>
						</ul>
						</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php 
	}
	else
	{
		?>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<?php
						echo "Sorry, but you don't have access to this page.<br/><br/>";
						?>										
						<a href="<?php bloginfo('siteurl'); ?>/dashboard/">Return to your Dashboard</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
	}
}
add_shortcode('invoicing_periods','billyB_invoicing_periods')
?>