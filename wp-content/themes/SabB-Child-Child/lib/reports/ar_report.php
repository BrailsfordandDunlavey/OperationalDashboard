<?php
function billyB_ar_report()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;

	$spherequery = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	?>

	<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php
			$periods_array = array("Total","Current","31 - 60","61 - 90","90 +");
							
			echo '<li><table width="100%"><tr><th>Project Number</th>';
			foreach ($periods_array as $period){echo '<th>'.$period.'</th>';}
			echo '</tr><tr><th>&nbsp;</th><th><u>Amount</u></th><th><u>Amount</u></th><th><u>Amount</u></th><th><u>Amount</u></th>
				<th><u>Amount</u></th></tr>';
											
			if(isset($_POST['sphere_brad'])){$sphere = "Sphere Brad";}
			if(isset($_POST['sphere_jeff'])){$sphere = "Sphere Jeff";}
			if(isset($_POST['sphere_will'])){$sphere = "Sphere Will";}
			if(isset($_POST['food_team'])){$sphere = "Food";}
			if(isset($_POST['ann_team'])){$sphere = "Ann";}
			if(isset($_POST['functional_sphere'])){$sphere = "Functional";}			
			
			$sphere_members_query = $wpdb->prepare("select user_id,display_name from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."useradd.user_id!=29
				order by ".$wpdb->prefix."users.display_name",$sphere);			

			if(isset($_POST['firm_wide'])){$sphere_members_query = "select user_id,display_name from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID 
				where ".$wpdb->prefix."useradd.user_id!=29 order by ".$wpdb->prefix."users.display_name";}
				
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
			
			$projects_array = array();
			
			foreach($sphere_members_results as $sphere_member)
			{
				$user_id = $sphere_member->user_id;
				
				$projects_results = $wpdb->get_results($wpdb->prepare("select project_id from ".$wpdb->prefix."project_user 
					where user_id=%d",$user_id));
				
				foreach($projects_results as $project)
				{
					$project_id = $project->project_id;
					if(!in_array($project_id,$projects_array)){array_push($projects_array,$project_id);}
				}
			}
			$total_total = 0;
			$current_total = 0;
			$thirty_total = 0;
			$sixty_total = 0;
			$ninety_total = 0;
			
			foreach($projects_array as $projects)
			{
				$project_number_results = $wpdb->get_results($wpdb->prepare("select gp_id from ".$wpdb->prefix."projects 
					where ID=%d",$projects));
				$project_number = $project_number_results[0]->gp_id;
				
				$today = time();
				$thirty_one_days = $today - (86400*31);
				$sixty_one_days = $today - (86400*61);
				$ninety_one_days = $today - (86400*91);
				
				$total_query = $wpdb->prepare("select (sum(invoice_fee_amount)+sum(invoice_expense_amount)) as total from ".$wpdb->prefix."invoices 
					where invoice_paid=0 and project_id=%d",$projects);
				$total_results = $wpdb->get_results($total_query);
				$total = ProjectTheme_get_show_price($total_results[0]->total);
				$total_total += $total_results[0]->total;
				
				$current_query = $wpdb->prepare("select (sum(invoice_fee_amount)+sum(invoice_expense_amount)) as current from ".$wpdb->prefix."invoices where invoice_paid=0 and 
					invoice_date>%d and project_id=%d",$thirty_one_days,$projects);
				$current_results = $wpdb->get_results($current_query);
				$current = ProjectTheme_get_show_price($current_results[0]->current);
				$current_total += $current_results[0]->current;

				$thirty_one_query = $wpdb->prepare("select (sum(invoice_fee_amount)+sum(invoice_expense_amount)) as thirty from ".$wpdb->prefix."invoices where invoice_paid=0 and 
					invoice_date>%d and invoice_date<=%d and project_id=%d",$sixty_one_days,$thirty_one_days,$projects);
				$thirty_one_results = $wpdb->get_results($thirty_one_query);
				$thirty = ProjectTheme_get_show_price($thirty_one_results[0]->thirty);
				$thirty_total += $thirty_one_results[0]->thirty;
				
				$sixty_one_query = $wpdb->prepare("select (sum(invoice_fee_amount)+sum(invoice_expense_amount)) as sixty from ".$wpdb->prefix."invoices where invoice_paid=0 and 
					invoice_date>%d and invoice_date<=%d and project_id=%d",$ninety_one_days,$sixty_one_days,$projects);
				$sixty_one_results = $wpdb->get_results($sixty_one_query);
				$sixty = ProjectTheme_get_show_price($sixty_one_results[0]->sixty);
				$sixty_total += $sixty_one_results[0]->sixty;

				$ninety_one_query = $wpdb->prepare("select (sum(invoice_fee_amount)+sum(invoice_expense_amount)) as ninety from ".$wpdb->prefix."invoices where invoice_paid=0 and 
					invoice_date<=%d and project_id=%d",$ninety_one_days,$projects);
				$ninety_one_results = $wpdb->get_results($ninety_one_query);
				$ninety = ProjectTheme_get_show_price($ninety_one_results[0]->ninety);
				$ninety_total += $ninety_one_results[0]->ninety;
				if($total != 0)
				{
					if(!empty($project_number)){echo '<tr><th><a href="/?p_action=project_card&ID='.$projects.'" >'
						.$project_number.'</a></th>';}
					else{echo '<tr><th><a href="/?p_action=edit_checklist&ID='.$projects.'" >Inactive Project</a></th>';}
					
					echo '<th>'.$total.'</th><th>'.$current.'</th><th>'.$thirty.'</th><th>'.$sixty.'</th><th>'.$ninety.'</th></tr>';
				}
			}
			echo '<tr><th>&nbsp;</th></tr>';
			if($total_total != 0)
			{
				echo '<tr><th>Total</th><th><b>'.ProjectTheme_get_show_price($total_total).'</b></th><th><b>'.ProjectTheme_get_show_price($current_total).'</b></th>
					<th><b>'.ProjectTheme_get_show_price($thirty_total).'</b></th><th><b>'.ProjectTheme_get_show_price($sixty_total).'</b></th>
					<th><b>'.ProjectTheme_get_show_price($ninety_total).'</b></th></tr>';
			}
			echo '</table></li>';
			if($total_total == 0){echo '<li>There are currently no outstanding invoices for this group.';}
	?>

			</ul>
			</div>
			</div>						
		</div>
		<?php
			$team_query = $wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid);
			$team_results = $wpdb->get_results($team_query);			
			
			$firm_wide_teams = array("Executive","Finance");
			if(in_array($team_results[0]->team,$firm_wide_teams))
			{$firm_wide = 1;}
			
			$sphere_jeff_array = array(40,54,48,86);
			if(in_array($uid,$sphere_jeff_array)){$sphere_jeff = 1;}
			
			$sphere_brad_array = array(39,103,55);
			if(in_array($uid,$sphere_brad_array)){$sphere_brad =1;}
			
			$sphere_will_array = array(58,116);
			if(in_array($uid,$sphere_will_array)){$sphere_will =1;}
			
			$food_team_array = array(80,51);
			if(in_array($uid,$food_team_array)){$food_team =1;}
			
			$ann_team_array = array(45);
			if(in_array($uid,$ann_team_array)){$ann_team =1;}
			
			//$functional_array = ();
			//if(in_array($uid,$functional_array)){$functional =1;}
				?>
		
		
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "More Groups";?></h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					<?php
					if($firm_wide == 1 or $sphere_brad == 1){echo '<li><input type="submit" name="sphere_brad" class="my-buttons-sidebar" value="Sphere Brad" /></li>';}
					if($firm_wide == 1 or $sphere_jeff == 1){echo '<li><input type="submit" name="sphere_jeff" class="my-buttons-sidebar" value="Sphere Jeff" /></li>';}
					if($firm_wide == 1 or $sphere_will == 1){echo '<li><input type="submit" name="sphere_will" class="my-buttons-sidebar" value="Sphere Will" /></li>';}
					if($firm_wide == 1 or $food_team == 1){echo '<li><input type="submit" name="food_team" class="my-buttons-sidebar" value="Food Team"" /></li>';}
					if($firm_wide == 1 or $ann_team == 1){echo '<li><input type="submit" name="ann_team" class="my-buttons-sidebar" value="Ann Drummie" /></li>';}
					if($firm_wide == 1 or $functional == 1){echo '<li><input type="submit" name="funtional_sphere" class="my-buttons-sidebar" value="Functional" /></li>';}
					if($firm_wide == 1){echo '<li><input type="submit" name="firm_wide" class="my-buttons-sidebar" value="Firm Wide" /></li>';}
					?>
					</ul>
				</li>
			</ul>
		</div></div>
			<?php  
			?>		
	</form>

<?php 
} 
add_shortcode('ar_report','billyB_ar_report')
?>