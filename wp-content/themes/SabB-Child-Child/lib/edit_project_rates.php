<?php

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }
get_header();
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;

$checklist = $_GET['ID'];	

$details_results = $wpdb->get_results($wpdb->prepare("select client_name,project_name,project_manager from ".$wpdb->prefix."projects
	inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
	where ".$wpdb->prefix."projects.ID=%s",$checklist));
$client_name = $details_results[0]->client_name;
$project_name = $details_results[0]->project_name;
$project_manager = $details_results[0]->project_manager;

$allowed_array = array(11,103,$project_manager);

if(!in_array($uid,$allowed_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit; }

if(isset($_POST['save-info']))
{
	$now = time();
	$records = $_POST['record'];
	
	foreach($records as $record)
	{
		$details = explode(",,,",$record['details']);
		$rate = $record['rate'];
		$user_id = $details[0];
		$rate_id = $details[1];
		$orig_value = $details[2];
		
		if($rate_id == 0)
		{
			$query = $wpdb->prepare("insert into ".$wpdb->prefix."project_rates (user_id,project_id,rate,updated_by,updated_on) 
				values(%d,%s,%f,%d,%d)",$user_id,$checklist,$rate,$uid,$now);
		}
		else
		{
			if($orig_value != $rate)
			{
					$query = $wpdb->prepare("update ".$wpdb->prefix."project_rates set rate=%f,updated_by=%d,updated_on=%d 
						where project_rate_id=%d",$rate,$uid,$now,$rate_id);
			}
		}
		$wpdb->query($query);
	}
}
?>
	<script language="javascript" type="text/javascript">
	$(document).on("keypress", ":input:not(textarea)", function(event) {
		return event.keyCode != 13;
	});
	</script>
	<div class="page_heading_me">
		<div class="page_heading_me_inner">
			<div class="main-pg-title">
				<div class="mm_inn"><?php echo $client_name." - ".$project_name;?> 
				</div>
			</div>
		</div>
	</div>
	<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="new_inv" enctype="multipart/form-data">
			<div id="content"><h2>Billing Rates</h2>
			<style>input[type=number]{width:95px;}</style>
				<div class="my_box3">
					<div class="padd10">
					<ul class="other-dets_m">
					<li>
					</li>
					<?php
					$timesheet_users = $wpdb->get_results($wpdb->prepare("select distinct user_id from ".$wpdb->prefix."timesheets where project_id=%d",$checklist));
					$project_user_results = $wpdb->get_results($wpdb->prepare("select distinct user_id from ".$wpdb->prefix."project_user where project_id=%d",$checklist));
					
					$users_string = "";
					$users_array = array();
					
					if(!empty($timesheet_users))
					{
						$users_string .= $timesheet_users[0]->user_id;
						array_push($users_array,$timesheet_users[0]->user_id);
						for($i=1;$i<count($timesheet_users);$i++)
						{
							$users_string .=",".$timesheet_users[$i]->user_id;
							array_push($users_array,$timesheet_users[$i]->user_id);
						}
						if(!empty($project_user_results))
						{
							for($i=0;$i<count($project_user_results);$i++)
							{
								if(!in_array($project_user_results[$i]->user_id,$users_array))
								{
									$users_string .=",".$project_user_results[$i]->user_id;
								}
							}
						}
					}
					elseif(!empty($project_user_results))
					{
						$users_string .= $project_user_results[0]->user_id;
						for($i=1;$i<count($project_user_results);$i++)
						{
							$users_string .=",".$project_user_results[$i]->user_id;
						}
					}
					
					
					$rate_results = $wpdb->get_results($wpdb->prepare("select project_rate_id,display_name,rate,user_id from ".$wpdb->prefix."project_rates
						inner join ".$wpdb->prefix."users on ".$wpdb->prefix."project_rates.user_id=".$wpdb->prefix."users.ID
						where ".$wpdb->prefix."project_rates.user_id in (".$users_string.") and project_id=%d",$checklist));
					
					if(empty($rate_results))
					{
						$year = date('Y');
						$rate_results = $wpdb->get_results($wpdb->prepare("select display_name,planning_rate,".$wpdb->prefix."useradd.user_id from ".$wpdb->prefix."useradd
							inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
							where year=%s and wp_users.ID in (".$users_string.")",$year));
					}
					$t = 0;
					
					foreach($rate_results as $rr)
					{
						if(empty($rr->project_rate_id)){$rate_id = 0;}else{$rate_id = $rr->project_rate_id;}
						if(empty($rr->rate)){$rate =  $rr->planning_rate;}else{$rate = $rr->rate;}
						echo '<li><h3>'.$rr->display_name.'</h3><p><input type="number" name="record['.$t.'][rate]" value="'.$rate.'" /></p></li>';
						echo '<input type="hidden" name="record['.$t.'][details]" value="'.$rr->user_id.',,,'.$rate_id.',,,'.$rate.'" />';
						$t++;
					}
					echo '<li>&nbsp</li>';
					echo '<li><input type="submit" name="save-info" value="save" class="my-buttons-submit" />&nbsp;&nbsp;
						<a href="/?p_action=project_card&ID='.$checklist.'" style="color:#ffffff;" class="my-buttons">
						Return to Project</a></li>';
					?>
					</ul>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
<?php get_footer();?>