<?php
function billyB_projected_revenue_projects()
{
	if(!is_user_logged_in()) { $_SESSION['redirect_me_back'] = $_SERVER['php_self']; wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if(isset($_POST['update_user']) or isset($_POST['save-data']))
	{
		if(!empty($_POST['user_select'])){$uid = $_POST['user_select'];}
	}
	$sphere_leaders = array(11,52,58,65,88,139,180,40,39,103,147,94,215,235);
	$staff_calculator = array(11,52,58,139);
	
	$pm_results = $wpdb->get_results("select project_manager from ".$wpdb->prefix."projects");
	$pm_array = array();
	foreach($pm_results as $pm_r)
	{
		array_push($pm_array,$pm_r->project_manager);
	}
	
	$useradd_query = $wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid);
	$useradd_result = $wpdb->get_results($useradd_query);
	$sphere = $useradd_result[0]->sphere; $group=$sphere;
	//if($uid==11){$sphere = "Higher Ed";}
	if($useradd_result[0]->group_leader==1){$default_team = $useradd_result[0]->team; $group=$default_team;}
	
	if(!in_array($uid,$sphere_leaders) and empty($default_team) and $sphere != 'functional' and !in_array($uid,$pm_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit; }
	
	if(isset($_POST['save-data']))
	{
		if(!empty($_POST['user_select'])){$uid = $_POST['user_select'];}
		if($uid==11 or $uid==94 or $uid==103 or $uid==235){$sphere = $_POST['sphere_select'];}
		$now = time();
		$default_month = $_POST['select_month'];
		
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$details = explode(',,,',$record['details']);
			$projected_id = $details[0];
			$project_id = $details[1];
			$eom = $details[2];
			$orig_revenue = $details[3];
			$orig_no_bill = $details[4];
			$projected_revenue = $record['proj_revenue'];
			$no_bill = $record['no_bill'];
			
			if($projected_revenue==0 and $orig_revenue!=$projected_revenue)
			{
				$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."projected_revenue where projected_revenue_id=%d",$projected_id));
			}
			elseif($orig_revenue!=$projected_revenue and $projected_id!=0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projected_revenue set projected_revenue=%f,no_bill=%f,updated_on=%d,
					updated_by=%d where projected_revenue_id=%d",$projected_revenue,$no_bill,$now,$uid,$projected_id));
			}
			elseif($projected_id==0 and $projected_revenue!=0)
			{
				$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."projected_revenue (projected_revenue,no_bill,project_id,
					month,updated_on,updated_by) values (%f,%f,%d,%d,%d,%d)",$projected_revenue,$no_bill,$project_id,$eom,$now,$uid));
			}
			elseif($no_bill!=$orig_no_bill)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projected_revenue set no_bill=%f,updated_on=%d,updated_by=%d
					where projected_revenue_id=%d",$no_bill,$now,$uid,$projected_id));
			}
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(isset($_POST['export-info']))
	{
		//ob_end_clean();
		$filename = "WUC ".date('m-d-Y',time()).".csv";
		$default_month = $_POST['select_month'];
		if(empty($default_month)){$default_month = strtotime(date('Y-m-t',strtotime(date('Y-m-01'). " -1 month")));}
		if($uid==11 or $uid==94 or $uid==103 or $uid==235){$sphere = $_POST['sphere_select'];}
		if(empty($sphere)){$sphere = "Sphere KMV";}
		//$output = @fopen('php://output', 'w');
		$header_array = array('Project','');
		$months_actual = array('Total Actual','');
		$months_projected = array('Total Projected','');
		$months_no_bill = array('Total No-Bill','');
		$number_of_months = 13;
		$months = array();
		for($i=0;$i<$number_of_months;$i++)
		{
			array_push($months,strtotime(date('Y-m-01',$default_month). " +".$i." months"));
		}
		
		
		for($i=0;$i<count($months);$i++)
		{
			$month = date('m-Y',strtotime(date('Y-m-d',$default_month).' + '.$i.' months'));
			array_push($header_array,$month);
			//$months_projected[$i] = 0;
			//$months_actual[$i] = 0;
		}
		//fputcsv($output, $header_array);
			$main_array = array($header_array);
			//$months_projected = array();
			$months_projected_likely = array();
			$months_projected_50 = array();
			$months_projected_less = array();
			//$months_actual = array();
			
			$projects_query = $wpdb->prepare("select ID,abbreviated_name,project_name,project_group,gp_project_number,gp_id
				from ".$wpdb->prefix."projects 
				where ".$wpdb->prefix."projects.sphere=%s and status=2 and project_parent=0
				order by gp_id",$sphere);
			
			$projects_results = $wpdb->get_results($projects_query);
			$t = 0;
			
			foreach($projects_results as $p)
			{
				$project_id = $p->ID;
				$project_group = $p->project_group;
				if(empty($project_group)){$project_group = "None";}
				if(!empty($p->abbreviated_name)){$abb_name = $p->abbreviated_name;}
				elseif(!empty($p->project_name)){$abb_name = $p->project_name;}
				elseif(!empty($p->gp_project_number)){$abb_name = $p->gp_project_number;}
				else{$abb_name = "Unnamed Project";}
				
				$line_one = array($abb_name,'Actual');
				$line_two = array($project_group,'Projected');
				$line_three = array('','No-Bill');
				
				for($i=0;$i<count($months);$i++)
				{
					$eom = strtotime(date('Y-m-t',$months[$i]));
					
					$projected_revenue_query = $wpdb->prepare("select projected_revenue_id,projected_revenue,no_bill from ".$wpdb->prefix."projected_revenue
						where project_id=%d and month=%d",$project_id,$eom);
					$projected_revenue_results = $wpdb->get_results($projected_revenue_query);
					$projected_id = $projected_revenue_results[0]->projected_revenue_id;if(empty($projected_revenue_results)){$projected_id=0;}
					$projected_revenue = $projected_revenue_results[0]->projected_revenue;
					if($projected_revenue==0){$projected_revenue = 0;}
					$no_bill = $projected_revenue_results[0]->no_bill;
					if($no_bill == 0){$no_bill = 0;}
					
					$actual_revenue_query = $wpdb->prepare("select invoice_fee_amount from ".$wpdb->prefix."invoices
						where project_id=%d and invoice_period=%d",$project_id,$eom);
					$actual_revenue_results = $wpdb->get_results($actual_revenue_query);
					$actual_revenue = $actual_revenue_results[0]->invoice_fee_amount;
					if(empty($actual_revenue)){$actual_revenue = 0;}
					
					array_push($line_one,$actual_revenue);
					array_push($line_two,$projected_revenue);
					array_push($line_three,$no_bill);
					
					$months_actual[$i+2] += $actual_revenue;
					$months_projected[$i+2] += $projected_revenue;
					$months_no_bill[$i+2] += $no_bill;
					$t++;
				}
				array_push($main_array,$line_one);
				array_push($main_array,$line_two);
				array_push($main_array,$line_three);
			}
			array_push($main_array,$months_actual);
			array_push($main_array,$months_projected);
			array_push($main_array,$months_no_bill);
		
		ob_end_clean();
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		$output = @fopen('php://output', 'w');
		foreach($main_array as $ma)
		{
			fputcsv($output,$ma);
		}
		fclose($output);
		
		
		exit();
		
		//print_r($projects_results);
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$sphere_query = "select distinct sphere from ".$wpdb->prefix."useradd order by sphere";
	$sphere_results = $wpdb->get_results($sphere_query);
	
	$all_teams_query = "select distinct team from ".$wpdb->prefix."useradd where team!=''";
	$all_teams_results = $wpdb->get_results($all_teams_query);
	
	foreach($all_teams_results as $at)
	{
		$team_value = $at->team;
		$team_name = strtolower(str_replace(" ","_",$team_value));
		
		if(isset($_POST[$team_name]))
		{
			if($current_user->ID==11 and !empty($_POST['user_select'])){$uid=$_POST['user_select'];}
			$team = $team_value;
			$group = $team;
		}
	}
	
	$team_results = $wpdb->get_results($wpdb->prepare("select distinct team from ".$wpdb->prefix."useradd 
		where sphere=%s and team!='' order by team", $sphere  ));
	

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
				echo '<li>Sphere:  '.$sphere.'</li>';
				echo '<li><h3>Change User</h3><select name="user_select" class="do_input_new">';
				$user_query = "select * from ".$wpdb->prefix."users 
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
					where status=1 order by display_name";
				$user_results = $wpdb->get_results($user_query);
				foreach($user_results as $user)
				{
					echo '<option value="'.$user->user_id.'" '.($uid==$user->user_id ? 'selected="selected"' : '').'>'.$user->display_name.'</option>';
				}
				echo '</select>';
				echo '<input type="submit" class="my-buttons" value="update" name="update_user"/></li><li>&nbsp;<li>';
				
			}
			$number_of_months = 13;
			if(empty($default_month)){$default_month = strtotime(date('Y-m-t',strtotime(date('Y-m-01'). " -1 month")));}
			$months = array();
			
			echo '<li><h3>Select Start</h3>
				<select name="select_month" class="do_input_new">';
			$selectable_months_array = array();
			for($i=3;$i>0;$i--)
			{
				$beg_month = strtotime(date('Y-m-01',time()). " +".$i." months");
				array_push($selectable_months_array,strtotime(date('Y-m-t',$beg_month)));
			}
			for($i=1;$i<=12;$i++)
			{
				$beg_month = strtotime(date('Y-m-01',time()). " -".$i." months");
				array_push($selectable_months_array,strtotime(date('Y-m-t',$beg_month)));
			}
			foreach($selectable_months_array as $sm)
			{
				echo '<option value="'.$sm.'" '.($sm==$default_month ? "selected='selected'" : "").'>'.date('m-d-Y',$sm).'</option>';
			}
			echo '</select>';
			//echo '<input type="submit" class="my-buttons" name="reset_month" value="Change Start" />';
			echo '</li>';
			echo '<li>&nbsp;</li>';
			
			if($uid==11 or $uid==94 or $uid==103 or $uid==235)
			{
				echo '<li><h3>Sphere</h3><select name="sphere_select" class="do_input_new">';
				foreach($sphere_results as $s)
				{
					if($uid!=103 or ($uid==103 and $s->sphere!='Functional'))
					{
						echo '<option value="'.$s->sphere.'" '.($s->sphere==$sphere ? 'selected="selected"' : '').'>'.$s->sphere.'</option>';
					}
				}
				echo '</select></li>';
				echo '<li>&nbsp;</li>';
			}
			if($sphere == "Higher Ed"){$group_name = "Cluster";}else{$group_name = "Group";}
			echo '<li><h3>'.$group_name.':</h3><select id="select_group" value="'.$group_name.'" class="do_input_new" onchange="hideRows();">
				<option value="all">All '.$group_name.'s</option>';
			foreach($team_results as $t)
			{
				echo '<option>'.$t->team.'</option>';
			}
			echo '<option>None</option>';
			echo '</select>';
			echo '<br/><a href="'.get_bloginfo('siteurl').'/wuc-dashboard/">WUC Dashboard</a>';
			if(in_array($uid,$staff_calculator)){echo '&nbsp;&nbsp;<a href="'.get_bloginfo("siteurl").'/staffing-calculator/">Staffing Calculator</a>';}
			echo '</li>';
			echo '<li>&nbsp;</li>';
			echo '<li><input type="submit" name="save-data" value="save" class="my-buttons-submit" />
				&nbsp;<input type="submit" class="my-buttons" value="Export" name="export-info" /></li><li>&nbsp;</li>';
			
			echo '<input type="hidden" name="hidden_sphere" value="'.$sphere.'" />';
			
			//******************************
			echo '<li><div style="overflow-x:auto;">
				<table style="width:1200px;"><thead>';
			for($i=0;$i<$number_of_months;$i++)
			{
				array_push($months,strtotime(date('Y-m-01',$default_month). " +".$i." months"));
			}
			
			echo '<tr><th style="width:120px;">Project</th>';
			echo '<th style="width:80px;">&nbsp;</th>';
			foreach($months as $month)
			{
				echo '<th style="width:80px;">'.date('m-Y',$month).'</th>';
			}
			echo '</tr></thead></table>';
			//***************************
			echo '<div style="overflow-x:visible;height:auto;max-height:500px;">
				<table style="width:1200px;"><thead>';
			echo '<tr><th style="width:100px;"></th>';
			echo '<th style="width:50px;">&nbsp;</th>';
			$months_projected = array();
			$months_projected_likely = array();
			$months_projected_50 = array();
			$months_projected_less = array();
			$months_actual = array();
			for($i=0;$i<count($months);$i++)
			{
				echo '<th style="width:80px;"></th>';
				$months_projected[$i] = 0;
				$months_actual[$i] = 0;
			}
			echo '</tr></thead>';
			
			echo '<tbody style="overflow-y:auto;">';
			
			$projects_query = $wpdb->prepare("select ".$wpdb->prefix."projects.ID,project_manager,abbreviated_name,project_name,project_group,
				".$wpdb->prefix."projects.gp_project_number,".$wpdb->prefix."useradd.team,".$wpdb->prefix."projects.gp_id,reports_to
				from ".$wpdb->prefix."projects 
				left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."useradd.user_id
				where ".$wpdb->prefix."projects.sphere=%s and ".$wpdb->prefix."projects.status=2 and project_parent=0
				order by gp_id",$sphere);
			if(isset($_POST['firm_wide']))
			{
				if($current_user->ID==11 and !empty($_POST['user_select'])){$uid=$_POST['user_select'];}
				$projects_query = "select ".$wpdb->prefix."projects.ID,abbreviated_name,project_name,project_manager,project_group,".$wpdb->prefix."projects.gp_project_number,".$wpdb->prefix."useradd.team,reports_to
					from ".$wpdb->prefix."projects 
					left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."useradd.user_id
					where ".$wpdb->prefix."projects.status=2 and project_parent=0
					order by gp_id";
			}
			$projects_results = $wpdb->get_results($projects_query);
			$t = 0;
			
			foreach($projects_results as $p)
			{
				$project_id = $p->ID;
				$project_group = $p->project_group;
				if(empty($project_group)){$project_group = "None";}
				if($p->project_manager != $uid and $uid!=$p->reports_to and !in_array($uid,$sphere_leaders))
				{$editor = "no";}else{$editor = "yes";}//check to see if user is the PM or person PM reports to or sphere leader
				if($editor=="no")//check to see if user is group the designated group leader
				{
					$group_leader_array = array();
					$group_leader_results = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."useradd where team=%s and group_leader=1",$p->team));
					foreach($group_leader_results as $glr)
					{
						array_push($group_leader_array,$glr->user_id);
					}
					if(in_array($uid,$group_leader_array)){$editor = "yes";}
				}
				if(!empty($p->abbreviated_name)){$abb_name = $p->abbreviated_name;}
				elseif(!empty($p->project_name)){$abb_name = $p->project_name;}
				elseif(!empty($p->gp_project_number)){$abb_name = $p->gp_project_number;}
				else{$abb_name = "Unnamed Project";}
				echo '<tr id="g'.$project_group.'"><td style="width:120px;"><b><u><a href="/?p_action=project_card&ID='.$project_id.'" target="_blank">
					'.($editor=="yes" ? "<font color='red'>".$abb_name.".</font>" : $abb_name ).'</a></u></b>
					<br/>('.$project_group.')</td>';
				echo '<td style="width:50px;">Actual<br/>Projected<br/>No Bill</td>';
				for($i=0;$i<count($months);$i++)
				{
					$eom = strtotime(date('Y-m-t',$months[$i]));
					if($eom + 86399 < time() or $editor == "no"){$read_only = "yes";}else{$read_only = "";}
					
					$projected_revenue_query = $wpdb->prepare("select projected_revenue_id,projected_revenue,no_bill from ".$wpdb->prefix."projected_revenue
						where project_id=%d and month=%d",$project_id,$eom);
					$projected_revenue_results = $wpdb->get_results($projected_revenue_query);
					$projected_id = $projected_revenue_results[0]->projected_revenue_id;if(empty($projected_revenue_results)){$projected_id=0;}
					$projected_revenue = $projected_revenue_results[0]->projected_revenue;
					if($projected_revenue==0){$projected_revenue = 0;}
					$no_bill = $projected_revenue_results[0]->no_bill;
					if($no_bill == 0){$no_bill = 0;}
					
					$actual_revenue_query = $wpdb->prepare("select invoice_fee_amount from ".$wpdb->prefix."invoices
						where project_id=%d and invoice_period=%d",$project_id,$eom);
					$actual_revenue_results = $wpdb->get_results($actual_revenue_query);
					$actual_revenue = $actual_revenue_results[0]->invoice_fee_amount;
					if(empty($actual_revenue)){$actual_revenue = 0;}
					
					echo '<td style="width:50px;border-bottom:solid 1px black;">$'.number_format($actual_revenue,2).'
						<input type="hidden" name="record['.$t.'][details]" 
						value="'.$projected_id.',,,'.$project_id.',,,'.$eom.',,,'.$projected_revenue.',,,'.$no_bill.'" />
						<input type="text" name="record['.$t.'][proj_revenue]" 
							value="'.($projected_revenue==0 ? '' : $projected_revenue).'" size="5"
						'.($read_only=="yes" ? "readonly" : "").' />';
					echo '<br/><input type="text" name="record['.$t.'][no_bill]" 
							value="'.($no_bill==0 ? '' : $no_bill).'" size="5"
					'.($read_only=="yes" ? "readonly" : "").' /></td>';
					echo '</td>';
					$months_actual[$i] += $actual_revenue;
					$months_projected[$i] += $projected_revenue;
					$t++;
				}
				echo '</tr>';
			}
			echo '<tr><td>&nbsp;</td></tr>';//spacing
			//Add Opportunities
			$opportunities = $wpdb->get_results($wpdb->prepare("select ".$wpdb->prefix."projects.ID,project_manager,abbreviated_name,project_name,reports_to,
				".$wpdb->prefix."projects.gp_project_number,".$wpdb->prefix."useradd.team,".$wpdb->prefix."projects.status,project_group
				from ".$wpdb->prefix."projects 
				left join ".$wpdb->prefix."useradd on ".$wpdb->prefix."projects.project_manager=".$wpdb->prefix."useradd.user_id
				where ".$wpdb->prefix."projects.sphere=%s and ".$wpdb->prefix."projects.status in (0,1,4,5,6)
				order by ".$wpdb->prefix."projects.status desc",$sphere));
				
			echo '<tr><th style="width:120px;"><b>Opportunities</b></th></tr>';
			if(!empty($opportunities))
			{
				$t++;
				
				foreach($opportunities as $opp)
				{
					$project_id = $opp->ID;
					$opp_group = $opp->project_group;
					if(empty($opp_group)){$opp_group="None";}
					if($opp->project_manager != $uid and $uid!=11 and $uid!=$opp->reports_to and !in_array($uid,$sphere_leaders))
					{$editor = "no";}else{$editor = "yes";}
					if($editor=="no")//check to see if user is group the designated group leader
					{
						$group_leader_array = array();
						$group_leader_results = $wpdb->get_results($wpdb->prepare("select user_id from ".$wpdb->prefix."useradd where team=%s and group_leader=1",$opp->team));
						foreach($group_leader_results as $glr)
						{
							array_push($group_leader_array,$glr->user_id);
						}
						if(in_array($uid,$group_leader_array)){$editor = "yes";}
					}
					if(!empty($opp->abbreviated_name)){$abb_name = $opp->abbreviated_name;}
					elseif(!empty($opp->project_name)){$abb_name = $opp->project_name;}
					elseif(!empty($opp->gp_project_number)){$abb_name = $opp->gp_project_number;}
					else{$abb_name = "Unnamed Opportunity";}
					echo '<tr id="g'.$opp_group.'"><td style="width:120px;"><b><u><a href="/?p_action=edit_opportunity&ID='.$project_id.'" target="_blank">
						'.($editor=="yes" ? "<font color='red'>".$abb_name.".</font>" : $abb_name ).'</a></u></b>
						<br/>('.$opp_group.')</td>';
					echo '<td style="width:50px;">Actual<br/>Projected<br/>No Bill</td>';
					for($i=0;$i<count($months);$i++)
					{
						$eom = strtotime(date('Y-m-t',$months[$i]));
						if($eom + 86399 < time() or $editor == "no"){$read_only = "yes";}else{$read_only = "";}
						
						$projected_revenue_query = $wpdb->prepare("select projected_revenue_id,projected_revenue,no_bill from ".$wpdb->prefix."projected_revenue
							where project_id=%d and month=%d",$project_id,$eom);
						$projected_revenue_results = $wpdb->get_results($projected_revenue_query);
						$projected_id = $projected_revenue_results[0]->projected_revenue_id;if(empty($projected_revenue_results)){$projected_id=0;}
						$projected_revenue = $projected_revenue_results[0]->projected_revenue;
						if($projected_revenue==0){$projected_revenue = 0;}
						$no_bill = $projected_revenue_results[0]->no_bill;
						if($no_bill == 0){$no_bill = 0;}
						
						$actual_revenue_query = $wpdb->prepare("select invoice_fee_amount from ".$wpdb->prefix."invoices
							where project_id=%d and invoice_period=%d",$project_id,$eom);
						$actual_revenue_results = $wpdb->get_results($actual_revenue_query);
						$actual_revenue = $actual_revenue_results[0]->invoice_fee_amount;
						if(empty($actual_revenue)){$actual_revenue = 0;}
						
						echo '<td style="width:50px;border-bottom:solid 1px black;">$'.number_format($actual_revenue,2).'
							<input type="hidden" name="record['.$t.'][details]" 
							value="'.$projected_id.',,,'.$project_id.',,,'.$eom.',,,'.$projected_revenue.',,,'.$no_bill.'" />
							<input type="text" name="record['.$t.'][proj_revenue]" 
								value="'.($projected_revenue==0 ? '' : $projected_revenue).'" size="5"
							'.($read_only=="yes" ? "readonly" : "").' />';
						echo '<br/><input type="text" name="record['.$t.'][no_bill]" 
								value="'.($no_bill==0 ? '' : $no_bill).'" size="5"
						'.($read_only=="yes" ? "readonly" : "").' /></td>';
						echo '</td>';
						$months_actual[$i] += $actual_revenue;
						if($opp->status == 4){$months_projected_less[$i] += $projected_revenue;}
						elseif($opp->status == 5){$months_projected_50[$i] += $projected_revenue;}
						elseif($opp->status == 6){$months_projected_likely[$i] += $projected_revenue;}
						$t++;
					}
					echo '</tr>';
				}
				
			}
			echo '<tr><td><a href="'.get_bloginfo('siteurl').'/opportunity-checklist/">Add an Opportunity</a></td></tr>';
			echo '<tr id="gtotals"><td>&nbsp;</td></tr>';
			
			
			echo '<tr id="gtotals"><td><strong><font size="2">Total Actual</strong></font></td><td>&nbsp;</td>';
			for($i=0;$i<count($months);$i++)
			{
				echo '<td><strong>$'.number_format($months_actual[$i],2).'<strong></td>';
			}
			echo '</tr>';
			echo '<tr id="gtotals"><td><strong><font size="2">Total Projected</font></strong></td><td>&nbsp;</td>';
			for($i=0;$i<count($months);$i++)
			{
				echo '<td><strong>$'.number_format($months_projected[$i] + $months_projected_likely[$i],2).'</strong></td>';
			}
			echo '</tr>';
			echo '<tr id="gtotals"><td><strong><font size="2">Likely</font></strong></td><td>&nbsp;</td>';
			for($i=0;$i<count($months);$i++)
			{
				echo '<td><strong>$'.number_format($months_projected_likely[$i],2).'</strong></td>';
			}
			echo '</tr>';
			echo '<tr id="gtotals"><td><strong><font size="2">50/50</font></strong></td><td>&nbsp;</td>';
			for($i=0;$i<count($months);$i++)
			{
				echo '<td><strong>$'.number_format($months_projected_50[$i],2).'</strong></td>';
			}
			echo '<tr id="gtotals"><td><strong><font size="2">Farming</font></strong></td><td>&nbsp;</td>';
			for($i=0;$i<count($months);$i++)
			{
				echo '<td><strong>$'.number_format($months_projected_less[$i],2).'</strong></td>';
			}
			echo '</tr>';
			echo '</tbody></table></div></div></li>';
			echo '<li>&nbsp;</li>';
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
			echo '<div id="content-full">
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">';
			echo '<li><h2>Notes</h2></li>';
			echo '<li>
				You can only update projected revenues for projects where you are listed as the Project Manager.  
					These projects will be highlighed in <font color="red"><b>red</b></font>.<br/>
				You can only update projected revenues for months that have not ended, up until about 8:00PM on the final day of the month.<br/>
				Actual revenues booked will not appear until the month is closed to all billings, which typically will happen between the 15th and 20th of a month.<br/>
				You can use the Group/Cluster drop-down menu at the top to filter projects by group/cluster.<br/>
				If a project needs to be assigned to another group, PM, or edited in some way, please contact Maresha Mitchell.
				</li>';
			echo '</ul>
				</div>
				</div>
				</div>';
			?>
	</form>

<?php 
} 
add_shortcode('projected_revenue_projects','billyB_projected_revenue_projects')
?>