<?php
function billyB_wuc_dashboard()
{
	if(!is_user_logged_in()) { $_SESSION['redirect_me_back'] = $_SERVER['php_self']; wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if(isset($_POST['update_user']))
	{
		if(!empty($_POST['user_select'])){$uid = $_POST['user_select'];}
	}
	
	$sphere_leaders = array(52,58,65,88,139,40,39,103,147,94,215,180,245);
	$pm_results = $wpdb->get_results("select project_manager from ".$wpdb->prefix."projects");
	$pm_array = array();
	foreach($pm_results as $pm_r)
	{
		array_push($pm_array,$pm_r->project_manager);
	}
	
	$useradd_query = $wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$useradd_result = $wpdb->get_results($useradd_query);
	$sphere = $useradd_result[0]->sphere; 
	$group = $sphere;
	if($useradd_result[0]->group_leader==1){$default_team = $useradd_result[0]->team; $group=$default_team;}
	
	if(!in_array($uid,$sphere_leaders) and empty($default_team) and $sphere != 'Functional' and !in_array($uid,$pm_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit; }
	
	$sphere_query = "select distinct sphere from ".$wpdb->prefix."useradd order by sphere";
	$sphere_results = $wpdb->get_results($sphere_query);
	
	foreach($sphere_results as $sr)
	{
		$sphere_value = $sr->sphere;
		$sphere_name = strtolower(str_replace(" ","_",$sphere_value));
		
		if(isset($_POST[$sphere_name]))
		{
			if(($current_user->ID==11 or $current_user->ID==94 or $current_user->ID==235)and !empty($_POST['user_select'])){$uid=$_POST['user_select'];}
			$sphere = $sphere_value;
			$group = $sphere;
			if(!empty($_POST['select_month'])){$default_month=$_POST['select_month'];}
		}
	}
	$all_teams_query = "select distinct team from ".$wpdb->prefix."useradd where team!=''";
	$all_teams_results = $wpdb->get_results($all_teams_query);
	
	foreach($all_teams_results as $at)
	{
		$team_value = $at->team;
		$team_name = strtolower(str_replace(" ","_",$team_value));
		
		if(isset($_POST[$team_name]))
		{
			if(($current_user->ID==11 or $current_user->ID==94 or $current_user->ID==235) and !empty($_POST['user_select'])){$uid=$_POST['user_select'];}
			$team = $team_value;
			$sphere = $_POST['hidden_sphere'];
			$group = $team;
		}
	}
	
	$team_results = $wpdb->get_results($wpdb->prepare("select distinct team from ".$wpdb->prefix."useradd 
		where sphere=%s and team!='' order by team",$sphere));
	
	foreach($team_results as $t)
	{
		$team_value = $t->team;
		$team_name = strtolower(str_replace(" ","_",$team_value));
		
		if(isset($_POST[$team_name]))
		{
			if(($current_user->ID==11 or $current_user->ID==94 or $current_user->ID==235) and !empty($_POST['user_select'])){$uid=$_POST['user_select'];}
			$team = $team_value;
			$sphere = $_POST['hidden_sphere'];
			$group = $team;
		}
	}
	?>
	<form method="post" name="projected_revenue" enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<style>input[type=number]{width:80px;}</style>	
			<?php
			if($current_user->ID==11)
			{
				echo '<div><input type="submit" class="my-buttons" value="update" name="update_user"/></div>';
				echo '<div><select name="user_select" class="do_input_new">';
				$user_query = "select * from ".$wpdb->prefix."users 
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where status=1 order by display_name";
				$user_results = $wpdb->get_results($user_query);
				foreach($user_results as $user)
				{
					echo '<option value="'.$user->user_id.'" '.($uid==$user->user_id ? 'selected="selected"' : '').'>'.$user->display_name.'</option>';
				}
				echo '</select></div>';
			}
		
			
			echo '<input type="hidden" name="hidden_sphere" value="'.$sphere.'" />';
			$number_of_months = 12;
			if(empty($default_month)){$default_month = strtotime(date('Y-m-t',strtotime(date('Y-m-01'). " -1 month")));}
			if(isset($_POST['reset_month'])){$default_month = $_POST['select_month'];$sphere=$_POST['hidden_sphere'];}
			$months = array();
			//******************************
			echo '<li>&nbsp;</li>';
			echo date('m-d-Y',$default_month);
			echo '<li><h3>Select Start</h3>
				<select name="select_month" class="do_input_new">';
			$selectable_months_array = array();
			for($i=3;$i>=0;$i--)
			{
				$beg_month = strtotime(date('Y-m-01',time()). " +".$i." months");
				array_push($selectable_months_array,strtotime(date('Y-m-t',$beg_month)));
			}
			for($i=1;$i<=24;$i++)
			{
				$beg_month = strtotime(date('Y-m-01',time()). " -".$i." months");
				array_push($selectable_months_array,strtotime(date('Y-m-t',$beg_month)));
			}
			foreach($selectable_months_array as $sm)
			{
				echo '<option value="'.$sm.'" '.($sm==$default_month ? "selected='selected'" : "").'>'.date('m-d-Y',$sm).'</option>';
			}
			echo '</select>';
			echo '<input type="submit" class="my-buttons" name="reset_month" value="Change Start" />';
			echo '</li>';
			echo '<li>&nbsp;</li>';
			
			echo '<li><a href="'.get_bloginfo('siteurl').'/operational-dashboard-revenue/">WUC Details</a>';
			$staff_calculator = array(11,37,38,52,58,139);
			if(in_array($uid,$staff_calculator))
			{
				echo '&nbsp;&nbsp;
				<a href="'.get_bloginfo('siteurl').'/staffing-calculator/">Staffing Calculator</a>';
			}
			echo '</li>';
			echo '<li>&nbsp;</li>';
			echo '<li><div style="overflow-x:auto;">
				<table style="width:1200px;"><thead>';
			for($i=0;$i<$number_of_months;$i++)
			{
				array_push($months,strtotime(date('Y-m-01',$default_month). " +".$i." months"));
			}
			
			echo '<tr><th style="width:100px;border-bottom:1pt solid black;">Month</th>';
			echo '<th style="width:100px;border-bottom:1pt solid black;">&nbsp;</th>';
			foreach($months as $month)
			{
				if(strtotime(date('Y-m-t',$month)) + 86399 < time()){$type = "Projected";}else{$type = "Projected";}//edited to show "Projected" even in historical months
				echo '<th style="width:80px;border-bottom:1pt solid black;">'.date('m-Y',$month).'<br/>('.$type.')</th>';
			}
			echo '</tr></thead></table>';
			//***************************
			echo '<div style="overflow-x:visible;height:auto;max-height:500px;">
				<table style="width:1200px;"><thead>';
			echo '<tr><th style="width:100px;"></th>';
			$months_projected = array();
			$months_actual = array();
			for($i=0;$i<count($months);$i++)
			{
				echo '<th style="width:80px;"></th>';
				$months_projected[$i] = 0;
				$months_actual[$i] = 0;
			}
			echo '</tr></thead>';
			
			echo '<tbody style="overflow-y:auto;">';
			
			$main_array = array();
			$now = time();
			
			for($i=0;$i<count($months);$i++)
			{
				
				$eom = strtotime(date('Y-m-t',$months[$i]));
				$month_array = array();
				/*
				remove showing actuals for now
				
				
				if($eom + 86399 < $now)//actual query
				{
					
					$revenue_query = $wpdb->prepare("select gp_project_number,".$wpdb->prefix."useradd.team,project_group,invoice_fee_amount,invoice_period
						from ".$wpdb->prefix."projects
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."useradd.user_id
						left join ".$wpdb->prefix."invoices on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."invoices.project_id 
							and invoice_period=%d
						where ".$wpdb->prefix."projects.sphere=%s
						order by project_group,".$wpdb->prefix."projects.ID",$eom,$sphere);
					$revenue_results = $wpdb->get_results($revenue_query);
					
					foreach($revenue_results as $r)
					{
						if($r->invoice_fee_amount == 0){$fee = 0;}else{$fee = $r->invoice_fee_amount;}
						if(empty($r->team)){$team = "none";}else{$team = $r->team;}
						if(empty($r->project_group)){$group = "none";}else{$group = $r->project_group;}
						$record_array = array($r->gp_project_number,$team,$group,$fee);
						array_push($month_array,$record_array);
					}
					array_push($main_array,$month_array);
					
				}
				else
				{
					*/
					$projected_query = $wpdb->prepare("select gp_project_number,".$wpdb->prefix."useradd.team,project_group,projected_revenue,no_bill,
						".$wpdb->prefix."projected_revenue.month,".$wpdb->prefix."projects.status
						from ".$wpdb->prefix."projects
						left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."useradd.user_id
						left join ".$wpdb->prefix."projected_revenue on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."projected_revenue.project_id 
							and ".$wpdb->prefix."projected_revenue.month=%d
						where ".$wpdb->prefix."projects.sphere=%s and ".$wpdb->prefix."projects.status in (2,4,5) and project_parent=0
						order by project_group,".$wpdb->prefix."projects.status,".$wpdb->prefix."projects.ID",$eom,$sphere);
					$projected_results = $wpdb->get_results($projected_query);
					
					foreach($projected_results as $p)
					{
						if($p->projected_revenue == 0 and $p->no_bill==0){$fee = 0;}else{$fee = $p->projected_revenue - $p->no_bill;}
						if(empty($p->team)){$team = "none";}else{$team = $p->team;}
						if(empty($p->project_group)){$group = "none";}else{$group = $p->project_group;}
						if($team == 'none' and $group != 'none'){$team = $group;}
						$status = $p->status;
						$record_array = array($p->gp_project_number,$team,$group,$fee,$status);
						array_push($month_array,$record_array);
					}
					array_push($main_array,$month_array);
					
				//}
				
			}
			$max_value_array = array();
			foreach($main_array as $arr)
			{
				array_push($max_value_array,count($arr));
			}
			$max = max($max_value_array);
			//main array = month,record  record = gp_project_number,team,group,fee,status
			//sphere wide total
			echo '<tr><td style="border-bottom:solid 1px black;"><b>Sphere Roll Up</b></td>';
			echo '<td style="border-bottom:solid 1px black;">Contracted<br/>Cost<br/>Net<br/>Possible</td>';
			$total_cost_query = $wpdb->get_results($wpdb->prepare("select allocation,percent,".$wpdb->prefix."group_user.team from ".$wpdb->prefix."group_user
				inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."group_user.user_id=".$wpdb->prefix."useradd.user_id
				where ".$wpdb->prefix."useradd.sphere=%s",$sphere));
			$total_monthly_cost = 0;
			foreach($total_cost_query as $tcq)
			{
				if($sphere=='Sphere KMV' and $tcq->team!='Higher Ed')
				{
					$total_monthly_cost += ($tcq->allocation*$tcq->percent/100)/12;
					if($tcq->allocation*$tcq->percent/100 > 0){$staff_count++;}
				}
			}
			$under_contract_total = 0;
			$possible_total = 0;
			$project_count = 0;
			for($m=0;$m<count($months);$m++)
			{
				$under_contract = 0;
				$possible = 0;
				for($i=0;$i<$max;$i++)
				{
					if($main_array[$m][$i][4] == 2){$under_contract += $main_array[$m][$i][3];}
					else{$possible += $main_array[$m][$i][3];}
					if($main_array[$m][$i][3] > 0){$project_count++;}
				}
				$total_monthly_net = $under_contract - $total_monthly_cost;
				echo '<td style="border-bottom:solid 1px black;"><b>$'.number_format($under_contract,0).'</b><br/>
					<b>($'.number_format($total_monthly_cost,0).')</b><br/>
					'.($total_monthly_net < 0 ? '<font color="red"><b>$'.number_format($total_monthly_net,0).'</b></font>' : 
					'<b>$'.number_format($total_monthly_net,0).'</b>').'<br/>
					<b>$'.number_format($possible,0).'</b></td>';
				$under_contract_total += $under_contract;
				$possible_total += $possible;
			}
			echo '</tr>';
			echo '<tr><td>&nbsp;</td></tr>';
			
			$team_array = array();
			
			for($i=0;$i<$max;$i++)
			{
				if(!in_array($main_array[0][$i][2],$team_array))
				{
					array_push($team_array,$main_array[0][$i][2]);
				}
			}
			foreach($team_array as $ta)
			{
				echo '<tr><td style="border-bottom:solid 1px black;">'.$ta.'</td>';
				echo '<td style="border-bottom:solid 1px black;">Contracted<br/>Cost<br/>Net<br/>Possible</td>';
				
				for($i=0;$i<count($months);$i++)
				{
					$monthly_under_contract = 0;
					$monthly_possible = 0;
					for($m=0;$m<$max;$m++)
					{
						if($main_array[$i][$m][2] == $ta)
						{
							if($main_array[$i][$m][4] == 2){$monthly_under_contract += $main_array[$i][$m][3];}
							else{$monthly_possible += $main_array[$i][$m][3];}
						}
					}
					
					//query the costs
					$cost = 0;
					$cost_results = $wpdb->get_results("select percent,allocation from ".$wpdb->prefix."group_user
						inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."group_user.user_id=".$wpdb->prefix."useradd.user_id
						where ".$wpdb->prefix."group_user.team='".$ta."'");
					foreach($cost_results as $r)
					{
						$cost += ($r->percent * ($r->allocation/100))/12;
					}
					$net = $monthly_under_contract - $cost;
					echo '<td style="border-bottom:solid 1px black;">$'.number_format($monthly_under_contract,0).'<br/>
						($'.number_format($cost,0).')<br/>
						<b>'.($net < 0 ? '<font color="red">$'.number_format($net,0).'</font>' : '$'.number_format($net,0)).'</b><br/>
						$'.number_format($monthly_possible,0).'
						</td>';
				}
				
				echo '</tr>';
			}
			//end group totals
			echo '</tbody></table></div></div></li>';
			
			echo '<li>&nbsp;</li>';
			echo '<li><h2>Metrics</h2></li>';
			echo '<li><h3>Monthly Revenue per Project:</h3><p><b>$'.number_format((($under_contract_total)/$project_count),0).'</b></p></li>';
			echo '<li><h3>Revenue per Person:</h3><p><b>$'.number_format((($under_contract_total)/$staff_count),0).'</b></p></li>';
?>
			<script language="javascript" type="text/javascript">
			function hideRows(){
				var x = document.getElementById('select_group').value;
				var allRows = document.querySelectorAll("[id^='g']");
				if(x != "all"){
					var showRows = document.querySelectorAll("[id*='g" + x + "']");
				}else{
					var showRows = allRows;
				}
				for(i=0;i<allRows.length;i++){
					allRows[i].style.display = 'none';
				}
				for(i=0;i<showRows.length;i++){
					showRows[i].style.display = 'table-row';
				}
			}
			</script>
			</ul>
			</div>
			</div>						
		</div>
			<?php
			$sphere_change_array = array(11,94,103,38,37,235,245);
			if(in_array($uid,$sphere_change_array))
			{
				echo '<div id="content-full">
					<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">';
				echo '<li><h2>Spheres</h2>';
				foreach($sphere_results as $s)
				{
					$sphere_value = $s->sphere;
					$sphere_name = strtolower(str_replace(" ","_",$sphere_value));
					if($s->sphere==$sphere){$button = "my-buttons-submit";}else{$button = "my-buttons";}
					if((($uid==103 or $uid==245) and $sphere_value!='Functional')or ($uid!=103 and $uid!=245) )
					{
						echo '<input type="submit" name="'.$sphere_name.'" class="'.$button.'" value="'.$sphere_value.'" />';
					}
				}
				echo '</li>';
				echo '</ul>
					</div>
					</div>
					</div>';
			}
			?>
	</form>

<?php } 
add_shortcode('wuc_dashboard','billyB_wuc_dashboard')
?>