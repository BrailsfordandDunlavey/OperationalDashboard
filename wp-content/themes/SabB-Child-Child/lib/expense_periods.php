<?php
function billyb_expense_periods()
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
				$employee_expense = $period['employee_expense'];
				$project_expense = $period['project_expense'];
			
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."invoice_periods set employee_expense_active=%s,project_expense_active=%s 
					where invoice_period_id=%d",$employee_expense,$project_expense,$period_id));
			}
		}
		?>
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post"  enctype="multipart/form-data">
			<div id="content"><h3><?php echo "Periods";?></h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<li>
					<p><input type="submit" name="save-info" class="my-buttons" value="Update" /></p>
					</li>
					<li><p>
						<?php 
						$details = $wpdb->get_results("select * from ".$wpdb->prefix."invoice_periods");
						echo '<table width ="100%"><tr><th>Period</th><th>Open for Employee Expenses</th><th>Open for Project Expenses</th></tr>';
						for($i=0;$i<count($details);$i++)
						{
							echo '<tr><th>'.date('m-Y',strtotime($details[$i]->invoice_period)).'</th>';
							echo '<th hidden><input type="text" name="period['.$i.'][id]" value="'.$details[$i]->invoice_period_id.'"/></th>';
							echo '<th><input type="checkbox" name="period['.$i.'][employee_expense]"';
							if($details[$i]->employee_expense_active=="on"){echo "checked=checked";}
							echo '></th>';
							echo '<th><input type="checkbox" name="period['.$i.'][project_expense]"';
							if($details[$i]->project_expense_active=="on"){echo "checked=checked";}
							echo '></th></tr>';
						}
						echo '</table>';
						?>
					</li>
					<li>
					<p><input type="submit" name="save-info" class="my-buttons" value="Update" /></p>
					</li>
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
		<a href="<?php bloginfo('siteurl');?>/dashboard/">Return to your Dashboard</a>
		</div></div></div></div></div>
		<?php 
	}
}
add_shortcode('expense_periods','billyB_expense_periods')
?>