<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
 	$period = $_GET['ID'];
	$employee = $_GET['employee'];
	
	$current_user_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	
	$allowable_teams = array('Finance','Human Resources','Executive');
	
	if(isset($_POST['submit_user'])){$employee = $_POST['change_user'];}
	
	$employee_details_query = $wpdb->prepare("select display_name,sphere,user_comp_type from ".$wpdb->prefix."useradd 
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
		where ".$wpdb->prefix."useradd.user_id=%d",$employee);
	$employee_details_results = $wpdb->get_results($employee_details_query);
	
	if($employee_details_results[0]->sphere == $current_user_results[0]->sphere or ($employee_details_results[0]->sphere=='Higher Ed' and $uid==103) or in_array($current_user_results[0]->team,$allowable_teams)
		or $employee==$uid){$ok =1;}
	if($ok !=1){wp_redirect("/dashboard"); exit;}
	
	if($employee_details_results[0]->user_comp_type='hourly'){$hourly=1;}else{$hourly=0;}
	
	if($period == 0){$period = strtotime(date('Y-m-01'));}
	
	if(isset($_POST['change_period'])){$period = $_POST['period'];}
	$period_end = strtotime(date('Y-m-t',$period));
	
	function sitemile_filter_ttl($title){return __("Employee Activity Report",'ProjectTheme');}
	add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );
	get_header();
	
	$last_month_beg = strtotime(date('Y-m-01',$period).' - 1 month');
	$last_month_end = strtotime(date('Y-m-t',$last_month_beg));
	$last_month_hours = 0; $last_month_admin = 0; $last_month_billable = 0; $last_month_timeoff = 0; $last_month_available = 0;
	
	$prev_month_beg = strtotime(date('Y-m-01',$last_month_beg).' - 1 month');
	$prev_month_end = strtotime(date('Y-m-t',$prev_month_beg));
	$prev_month_hours = 0; $prev_month_admin = 0; $prev_month_billable = 0; $prev_month_timeoff = 0; $prev_month_available = 0;
	
	$two_months_beg = strtotime(date('Y-m-01',$prev_month_beg).' - 1 month');
	$two_months_end = strtotime(date('Y-m-t',$two_months_beg));
	$two_months_hours = 0; $two_months_admin = 0; $two_months_billable = 0; $two_months_timeoff = 0; $two_months_available = 0;
	
	if($hourly==0){$max_hours = 10;}else{$max_hours=8;}
	if($hourly==0){$min_hours = 8;}else{$min_hours=0;}
	?>
	<div class="page_heading_me">
		<div class="page_heading_me_inner">
            <div class="mm_inn"><?php echo $employee_details_results[0]->display_name; ?></div>
        </div>
    </div>
	<form name="detailed_emp_hours" method="post"  enctype="multipart/form-data">
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<div id="content"><h2><?php echo "Time Overview";?></h2><p><?php echo 'From '.date('m-d-Y',$two_months_beg).' to '.date('m-d-Y',$last_month_end);?></p>
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">
						<li>&nbsp;</li>
						<?php 
						//BillyB build snap shot for last three months
						/*
						Add avg hours worked per week (contrast to 40 hrs/week
						time off details
						% billable
						value of services worked on each project
						total value worked on each project vs billings on each project
						Experience in markets
						marketing effort
						%admin
						Filter by role
						*/
						
						$hours_by_project = $wpdb->prepare("select timesheet_date,timesheet_hours,timesheet_notes,project_id,client_name,project_name,abbreviated_name,
							".$wpdb->prefix."projects.market,submarket,name,project_type 
							from ".$wpdb->prefix."timesheets 
							left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
							left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
							left join ".$wpdb->prefix."terms on ".$wpdb->prefix."projects.market=".$wpdb->prefix."terms.term_id
							where user_id=%d and timesheet_date<=%d and timesheet_date>=%d
							order by -".$wpdb->prefix."projects.market desc,submarket,project_id",$employee,$period_end,$two_months_beg);
						$hbp = $wpdb->get_results($hours_by_project);
						
						$hours_by_date = $wpdb->prepare("select timesheet_date,timesheet_hours,timesheet_notes,project_id,client_name,project_name,abbreviated_name,
							".$wpdb->prefix."projects.market,submarket
							from ".$wpdb->prefix."timesheets 
							left join ".$wpdb->prefix."projects on ".$wpdb->prefix."timesheets.project_id=".$wpdb->prefix."projects.ID
							left join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
							left join ".$wpdb->prefix."terms on ".$wpdb->prefix."projects.market=".$wpdb->prefix."terms.term_id
							where user_id=%d and timesheet_date<=%d and timesheet_date>=%d
							order by timesheet_date",$employee,$period_end,$two_months_beg);
						$hbd = $wpdb->get_results($employee_hours_query);
						
						$workable_hours = 0;
						for($i=$two_months_beg;$i<=$last_month_end;$i=$i+83600)
						{
							//if(date("D",$i) != "Sun" and date("D",$i) != "Sat"){$workable_hours += 8;}
						}
						$all_array_projects = array();
						$worked_hours = 0;
						if(!empty($hbp))
						{
							$projects_array = array();
							$total_hours_worked = 0;
							
							//average hours worked per day
							//total hours worked in last three months, then total by project
							//total hours by project type
							//total project efficiency (fees billed over value of hours worked - make sure to only get for the periods invoiced if latest invoices not uploaded yet)
							
							for($i=0;$i<count($hbp);$i++)
							{
								//NAME THE PROJECT
								if(!empty($hbp[$i]->abbreviated_name))
								{
									$project = $hbp[$i]->abbreviated_name;
								}
								elseif(!empty($hbp[$i]->project_name))
								{
									$project = $hbp[$i]->project_name;
								}
								else{$project = $hbp[$i]->project_id;}
								//LOOP PROJECT HOURS - POPULATE PROJECT HOURS ARRAY
								if(!in_array($hbp[$i]->project_id,$projects_array))
								{
									$q = $wpdb->get_results($wpdb->prepare("select name from ".$wpdb->prefix."terms where term_id=%d",$hbp[$i]->submarket));
									$submarket = $q[0]->name;
									if($i>0)
									{
										array_push($record_array,$project_hours);
										array_push($all_array_projects,$record_array);
									}
									$record_array = array();
									array_push($projects_array,$hbp[$i]->project_id);
									array_push($record_array,$hbp[$i]->project_id,$project,$hbp[$i]->name,$submarket,$hbp[$i]->project_type);
									$project_hours = $hbp[$i]->timesheet_hours;
								}
								else
								{
									$project_hours += $hbp[$i]->timesheet_hours;
								}
								$worked_hours += $hbp[$i]->timesheet_hours;
								
							}
							array_push($record_array,$project_hours);
							array_push($all_array_projects,$record_array);
							
							//HOURS BY MARKET
							
							foreach($all_array_projects as $aap)
							{
								if($aap[2]!=0)
								{
									
								}
							}
							
							
							//HOURS BY PROJECT DETAIL
							echo '<h3>Hours by Project</h3>';
							echo '<li><table width=-"100%">';
							echo '<tr>
								<th><b><u>Project</b></u></th>
								<th style="text-align:center;"><b><u>Market</b></u></th>
								<th style="text-align:center;"><b><u>Submarket</b></u></th>
								<th style="text-align:center;"><b><u>Project Type</b></u></th>
								<th style="text-align:center;"><b><u>Hours</b></u></th>
								<th style="text-align:center;"><b><u>Percent</b></u></th>
								</tr>';
							foreach($all_array_projects as $aap)
							{
								if($aap[5]!=0)
								{
									echo '<tr>
										<td><strong>'.$aap[1].'</strong></td>
										<td style="text-align:center;">'.$aap[2].'</td>
										<td style="text-align:center;">'.$aap[3].'</td>
										<td style="text-align:center;">'.$aap[4].'</td>
										<td style="text-align:center;">'.number_format($aap[5],2).' hours</td>
										<td style="text-align:center;">'.round(($aap[5]/$worked_hours)*100).'%</td>
										</tr>';
								}
							}
							echo '<tr><td>&nbsp;</tr>';
							echo '<tr>
								<td><strong>Total</strong></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><strong>'.$worked_hours.' hours</strong></td>
								</tr>';
							echo '</table></li>';
						}
						else{echo '<li>No time records for last three months</li>';}
						?>
						<li>&nbsp;</li>
						</ul>
					</div>
				</div>
			</div>
			<div id="right-sidebar" class="page-sidebar"><div class="padd10">
				<ul class="xoxo">
					<li class="widget-container widget_text" id="ad-other-details">
						<ul class="other-dets other-dets2">
						<?php
						echo '<select name="change_user" class="do_input_new">';
						$active_users_query = "select user_id,display_name from ".$wpdb->prefix."users
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where status = 1";
						$active_users_results = $wpdb->get_results($active_users_query);
						foreach($active_users_results as $aur)
						{
							echo '<option value="'.$aur->user_id.'" '.($uid == $aur->user_id ? 'selected="selected"' : '').'>'.$aur->display_name.'</option>';
						}
						echo '</select>';
						echo '<input type="submit" name="submit_user" value="Change User" class="my-buttons-submit" />';
						?>
						</ul>
					</li>
				</ul>
			</div></div>
		</div>
	</div>
	</form>
<?php get_footer(); ?>