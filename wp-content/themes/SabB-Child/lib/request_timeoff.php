<?php
function billyB_request_timeoff()
{
	if(!is_user_logged_in()){ wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit;}

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$name = $current_user->display_name;
	$report_to_result = $wpdb->get_results($wpdb->prepare("select user_email from ".$wpdb->prefix."users inner join ".$wpdb->prefix."useradd on 
		".$wpdb->prefix."useradd.reports_to=".$wpdb->prefix."users.ID where user_id=%d",$uid));
	$report_to = $report_to_result[0]->user_email;
	
	$previous_requests = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."request_timeoff where employee_id=%d",$uid));
 
	if(isset($_POST['update_uid'])){$uid = $_POST['uid'];}
	
	if(isset($_POST['save-info']))
	{
		?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">
				<?php	
				$records = ($_POST['record']);
							
				foreach($records as $record)
				{
					if(!empty($record['hours']))
					{
						$submitted = time();
						$report_id = $uid.$submitted;
						$date = strtotime($record['date']);
						$request_type = $record['request_type'];
						if($request_type == "Vacation"){$request_code = "V";}elseif($request_type == "Float"){$request_code = "F";}
						elseif($request_type == "MATPAT"){$request_code = "M";}elseif($request_type == "BEREAV"){$request_code = "G";}
						elseif($request_type == "Sick"){$request_code = "S";}elseif($request_type == "JURY"){$request_code = "J";}
						elseif($request_type=="Holiday"){$request_code = "H";}else{$request_code = "Error";}
						$hours = $record['hours'];
						$notes = $record['notes'];
						$status = 0;
						
						foreach($previous_requests as $pr)
						{
							if($pr->request_code == $request_code and $pr->request_date == $date and $hours>0 and $pr->request_status!=2){$duplicate = "yes";}
							else{$email = "yes";}
						}
						if($duplicate != "yes")
						{
							$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."request_timeoff (request_report_id,employee_id,request_date,request_type,request_code,request_hours,notes,request_status,
								date_requested)	values(%d,%d,%d,%s,%s,%f,%s,%d,%d)",$report_id,$uid,$date,$request_type,$request_code,$hours,$notes,$status,$submitted));
						
							$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."timesheets (user_id,timesheet_date,project_id,timesheet_hours,submitted_date,timesheet_status)
								values(%d,%d,%s,%f,%d,0)",$uid,$date,$request_type,$hours,$submitted));
						}
					}
				}
				if($email == "yes")
				{
					$subject = $name.' has submitted a time off request for your review';
					$link = 'http://opdash.programmanagers.com/approve-time-off/';
					$message = 'Please go to '.$link.' to review all time off requests pending your review';
					wp_mail($report_to,$subject,$message);
				}
				if($email == "yes" and $duplicate != "yes")
				{
					echo "The request has been submitted.<br/><br/>";
				}
				elseif($duplicate == "yes")
				{
					echo '<b><font color="red">Your entry had a duplicate request, please review your previous requests and enter again, as necessary</font></b><br/><br/>';
				}
				?>
				<a href="<?php bloginfo('siteurl');?>/request-time-off/"><?php echo "Enter a new request for time off";?></a><br/><br/>
				<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
				</div>
			</div>
		</div>
		<?php 
		$_POST = array();
	}
	else
	{
		?>   
		<form method="post" name="request_timeoff" enctype="multipart/form-data">
		<?php
		if($current_user->ID == 11 or $current_user->ID==60 or $current_user->ID==74)
		{
			echo '<div id="content">
					<div class="my_box3">
						<div class="padd10">
							<ul class="other-dets_m">';
			echo '<li><h3>&nbsp;</h3><p><select class="do_input_new" name="uid">';
			$users_query = "select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users 
				inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
				where ".$wpdb->prefix."useradd.status=1 order by display_name";
			$user_results = $wpdb->get_results($users_query);
			foreach($user_results as $user)
			{
				echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
			}
			echo '</select>';
			echo '<input type="submit" class="my-buttons" value="Change User" name="update_uid" /></p>';
			echo '</ul></div></div></div>';
		}
		?>
		<div id="content">
		<div class="my_box3">
			<div class="padd10">
				<ul class="other-dets_m">
					<li><?php echo "Please enter the number of hours for <b><u>EACH</u></b> day of your request";?></li>
					<li>&nbsp;</li>
					<style>input[type=number]{width:80px;}</style>
						<?php
							$rowstart = 4;
							$rowadd = 5;
							$x = $_POST['y'];
							if(isset($_POST['add_rows']))
							{
								$y = $x+1;
								$records = $_POST['record'];
								$record_array = array();
								for($i=0;$i<count($records);$i++)
								{
									$array = array();
									array_push($array,strtotime($records[$i]['date']));
									array_push($array,$records[$i]['request_type']);
									array_push($array,$records[$i]['hours']);
									array_push($array,$records[$i]['notes']);
									array_push($record_array,$array);
								}
							}
							$totalrows = $rowstart + ($rowadd * $y);
							?>
							<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
							<link rel="stylesheet" media="all" type="text/css" href="<?php echo get_bloginfo('template_url'); ?>/css/ui_thing.css" />
							<script type="text/javascript" language="javascript" src="<?php echo get_bloginfo('template_url'); ?>/js/timepicker.js"></script>
							<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
							<link rel="stylesheet" href="/resources/demos/style.css">
							<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
							<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
							<?php
							for ($t= 0;$t<=$totalrows;$t++)
							{
								echo '<li><div id="d3"><input type="text" id="start'.$t.'" class="do_input_new" size="11" 
									style="display:inline-block;float:left;" name="record['.$t.'][date]" 
									'.(isset($_POST['add_rows']) ? ($record_array[$t][0]!=0 ? 'value="'.date('m/d/Y',$record_array[$t][0]).'"' : '')
									 : '').' placeholder="Date" onblur="validation();"/></div>';
								?>
								<script>
								<?php $dd = 180; ?>
		
								var myDate=new Date();
								myDate.setDate(myDate.getDate()+<?php echo $dd; ?>);
			
								$(document).ready(function() {
									$('#start<?php echo $t;?>').datepicker({
										showSecond: false,
										ampm: false,
										dateFormat: 'mm/dd/yy'
								});});
								</script>
								<?php
								echo '<div id="d3"><select class="do_input_new" style="display:inline-block;float:left;" name="record['.$t.'][request_type]"
									onChange="validation();">';
								if(!empty($record_array[$t][1])){$selected_type = $record_array[$t][1];}
								$request_types = array("Vacation","Sick","Holiday","Float","BEREAV","JURY","MATPAT");
								foreach($request_types as $type)
								{echo '<option '.($type==$selected_type ? 'selected="selected"' : '').'>'.$type.'</option>';}
								echo '</select></div>
									<div id="d3"><input type="number" step=".01" style="display:inline-block;float:left;" 
									class="do_input_new" size="2" placeholder="Hours" name="record['.$t.'][hours]" onblur="validation();"
									value="'.$record_array[$t][2].'" /></div>';
								echo '<div id="d3"><input type="text" class="do_input_new" size="25" placeholder="Notes" 
									style="display:inline-block;float:left;" name="record['.$t.'][notes]"
									value="'.$record_array[$t][3].'" />
									<input type="hidden" name="record['.$t.'][disable]" /></div></li>';
							}
							?>
							<script type="text/javascript">
							function validation(){
								var dateFields = document.querySelectorAll("[name$='[date]']");//fields ending with [date]
								var hoursFields = document.querySelectorAll("[name$='[hours]']");
								var typeFields = document.querySelectorAll("[name$='[request_type]']");
								var submitField = document.forms.request_timeoff.elements['save-info'];
								var issue = "";
								var dateIssue = "";
								//alert('hello');	
								var previousDates = [<?php
									for($i=0,$b=0;$i<count($previous_requests);$i++,$b++)
									{
										if($b>0){echo ",";}
										echo '"'.date('m/d/Y',$previous_requests[$i]->request_date).'"';
									}
									?>];
								var previousTypes = [<?php
									for($i=0,$b=0;$i<count($previous_requests);$i++,$b++)
									{
										if($b>0){echo ",";}
										echo '"'.$previous_requests[$i]->request_type.'"';
									}
									?>];
								var previousHours = [<?php
									for($i=0,$b=0;$i<count($previous_requests);$i++,$b++)
									{
										if($b>0){echo ",";}
										echo '"'.$previous_requests[$i]->request_hours.'"';
									}
									?>];
								
								for(i=0;i<dateFields.length;i++){
									if(dateFields[i].value==0 && hoursFields[i].value!=0){
										issue = "true";
									}
									for(d=0;d<previousDates.length;d++){
										if(dateFields[i].value==previousDates[d].value && hoursFields[i].value==previousHours[d].value && typeFields[i].value==previousTypes[d].value){
											dateIssue = "yes";
										}
									}
								}
								if(issue == "true"){
									alert('Please select a date for each of your requests');
									submitField.disabled = true;
									submitField.style.backgroundColor = "#E0E0E0";
								}
								else{
									submitField.disabled = false;
									submitField.style.backgroundColor = "#222222";
								}
								if(dateIssue == "true"){
									alert('You have previously requested this time off');
								}
							}
							function previousRequests(x){
							}
							</script>
					<li>&nbsp;</li>
					<li hidden><input type="text" name="y" value="<?php echo $y;?>"/></li>
					<li><p><input type="submit" name="add_rows" class="my-buttons" value="<?php echo "Add Five (5) rows";?>" /></p></li>
					<li>&nbsp;</li>
					<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Submit"; ?>" /></p></li>
				</ul>
			</div>
		</div>
		</div>
		</form>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3>Tips</h3>
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
					<ul class="other-dets other-dets2">
					If you need to modify an existing request, please enter the negative hours as a new entry.  If you need to modify 8 hours down to 4, enter -4 hours.
					</ul>
				</li>
			</ul>
		</div></div>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Previously Submitted Requests";?></h3>
		<ul class="xoxo">
			<li class="widget-container widget_text" id="ad-other-details">
				<ul class="other-dets other-dets2">
				<?php
				$now = time();
				$one_year = strtotime(date('Y-m-d',$now). "- 1 year");
				$request_results = $wpdb->get_results($wpdb->prepare("select request_id,request_date,request_type,request_status,request_hours
					from ".$wpdb->prefix."request_timeoff
					where employee_id=%d and request_date>%d
					order by request_date desc",$uid,$one_year));
				if(empty($request_results)){echo 'No previously submitted requests';}
				else
				{	
					echo '<table width="100%"><tr><th><u>Date</u></th><th><u>Type</u></th><th><u>Hours</u></th><th><u>Status</u></th></tr>';
					foreach($request_results as $request)
					{
						$date = $request->request_date;
						$project = $request->request_type;
						$hours = $request->request_hours;
						$r_status = $request->request_status;
						$timesheet_results = $wpdb->get_results($wpdb->prepare("select timesheet_id from ".$wpdb->prefix."timesheets
							where timesheet_date=%d and timesheet_hours=%f and project_id=%s and user_id=%d",$date,$hours,$project,$uid));
						$t_id = $timesheet_results[0]->timesheet_id;
						
						if($r_status == 0){$status = "Unapproved";}elseif($r_status != 0 and $r_status !=2){$status = "Approved";}
							else{$status = "Rejected";}
						echo '<tr><th>'.date('m-d',$date).'</th><th>'.$project.'</th><th>'.$hours.'</th>
							<th>'.$status.'</th>
							'.(($r_status<2) ? '
							<th><a href="/?p_action=delete_timeoff&ID='.$request->request_id.'-'.$t_id.'" class="nice_link" ><font color="white">Delete</font></a></th>' : '');
					}
					echo '</table>';
				}					
				?>
				</ul>
			</li>
		</ul>
		</div></div>			
		<?php 
	} 
}
add_shortcode('request_timeoff','billyB_request_timeoff') ?>