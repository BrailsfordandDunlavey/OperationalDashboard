<?php
function billyb_cig_report()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$rights_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$allowed_array = array('Finance','Executive');
	$allowed_users = array(103);
	if(!in_array($rights_results[0]->team,$allowed_array) and !in_array($uid,$allowed_users)){wp_redirect(get_bloginfo('siteurl')."/dashboard");}

	$today = time();
	$beg_period = strtotime(date('01-01-Y',$today));
	$count_of_months = (date('Y',$today)-date('Y',$beg_period))*12 + (date('m',$today)-date('m',$beg_period));
	
	if(isset($_POST['save-info']) or isset($_POST['save-info-two']))
	{
		
	}
	//Dale, Mike Quadrino, Marco, Marcus, Kevin Mara, Elle, Kendra, Steve, Jenny, Rebecca, Greg
	
	$team_array = array('Dale Randels','Rebecca Geraghty','kevin mara','michael quadrino','marcus huff','noelle carne','kendra chatburn','steven green','jenny derry','greg smith');
	$team_string = "";
	for($i=0;$i<count(team_array);$i++)
	{
		if($i!=0){$team_string .= ",";}
		$team_string .= $t;
	}
	
	$results = $wpdb->get_results($wpdb->prepare("select display_name,timesheet_hours,project_id,timesheet_date,project_name,abbreviated_name
		from ".$wpdb->prefix."timesheets
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."timesheets.user_id=".$wpdb->prefix."users.ID
		left join ".$wpdb->prefix."projects on ".$wpdb->prefix."projects.ID=".$wpdb->prefix."timesheets.project_id
		where display_name in (".$team_string.") and timesheet_date>=".$beg_period."
		order by display_name,project_id,timesheet_date"));
	
	//get query results
	//loop results to create array of arrays - person, project, month, hours
	
	$results_array = array();
	$person_array = array($results[0]->display_name);
	$project_array = array($results[0]->project_id);
	$month_array = array(strtotime(date('m-01-Y',$results[0]->timesheet_date)));
	$project = $results[0]->project_id;
	$name = $results[0]->display_name;
	$month = strtotime(date('m-01-Y',$results[0]->timesheet_date));
	
	foreach($results as $r)
	{
		if($r->display_name != $name)
		{
			array_push($month_array,$hours);
			array_push($project_array,$month_array);
			array_push($person_array,$project_array);
			array_push($results_array,$person_array);
			$person_array = array($r->display_name);
			$project_array = array($r->project_id);
			$month_array = array(strtotime(date('m-01-Y',$r->timesheet_date)));
			$hours = $r->timesheet_hours;
		}
		elseif($r->project_id != $project)
		{
			array_push($month_array,$hours);
			array_push($project_array,$month_array);
			array_push($person_array,$project_array);
			$project_array = array($r->project_id);
			$month_array = array(strtotime(date('m-01-Y',$r->timesheet_date)));
			$hours = $r->timesheet_hours;
		}
		elseif($month != strtotime(date('m-01-Y',$r->timesheet_date)))
		{
			array_push($month_array,$hours);
			array_push($project_array,$month_array);
			$month_array = array(strtotime(date('m-01-Y',$r->timesheet_date)));
			$hours = $r->timesheet_hours;
		}
		else
		{
			$hours =+ $r->timesheet_hours;
		}
	}
	array_push($month_array,$hours);
	array_push($project_array,$month_array);
	array_push($person_array,$project_array);
	array_push($results_array,$person_array);
	
	//use code to resort array based on person, project
	
	?>   
	<div id="main_wrapper">
		<div id="main" class="wrapper">
			<form method="post" name="employee_report" enctype="multipart/form-data">
				<div id="content">
					<div class="my_box3">
					<div class="padd10">					
					<ul class="other-dets_m">
					<?php
					
					if(!empty($results_array))
					{
						echo '<li><table width="100%">
								<tr>
								<th>Employee</th>
								<th>Project</th>';
						//iterate months
						
						for($i=0;$i<=$count_of_months;$i++)
						{
							echo '<th>'.date('m-Y',strtotime(date('m-01-Y',$beg_period)."+ ".$i." months")).'</th>';
						}
						
						echo '</tr>';
						//loop data
						echo '</table></li>';
					}
					else
					{
						echo '<li>There are no hours for the selected filters</li>';
					}
					
					?>
					<li>&nbsp;</li>
					</ul>
					</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php
}

add_shortcode('cig_report','billyb_cig_report') ?>