<?php
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if($uid != 11){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	
	$project = $_GET['ID'];
	
	$month = strtotime(date('Y-m-01',time()));

	$details = $wpdb->get_results($wpdb->prepare("select projected_hours,user_id,abbreviated_name,project_name,gp_id,projected_revenue
		from ".$wpdb->prefix."projects
		inner join ".$wpdb->prefix."projected_time on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."projected_time.project_id
		left join ".$wpdb->prefix."projected_revenue on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."projected_revenue.project_id
		left join ".$wpdb->prefix."tasks on ".$wpdb->prefix."projected_time.task_id=".$wpdb->prefix."tasks.task_id
		where projected_month=%d and month=%d and ".$wpdb->prefix."projected_time.project_id=%d
		order by task_id",$month,$month,$project));
		
	?>   
		<div id="main_wrapper">
		<div id="main" class="wrapper">
		<form method="post" name="edit_contract_checklist" enctype="multipart/form-data">
			<div id="content"><h3>Project Details</h3><br/>
				<div class="my_box3">
				<div class="padd10">
				<ul class="other-dets_m">
					<?php
					echo '<li><table width="100%">';
					echo '<tr><th>Project</th>';
					echo '<th>Task</th>';
					echo '<th>Employee</th>';
					echo '<th>Projected Hours</th>';
					echo '<th>Percent Complete</th>';
					echo '<th>Projected Revenue</th></tr>';
					
					foreach($details as $d)
					{
						'hello bill';
					}
					?>
				</ul>
				</div>
				</div>
			</div>
		</form>
		</div>
		</div>