<?php
function billyB_capacity_set()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if($uid == 11){$uid = 45;}
	
	$allowed_user_array = array(11,103,65,45,245);
	if(!in_array($uid,$allowed_user_array)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	$sphereresult = $wpdb->get_results($wpdb->prepare("select sphere from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $sphereresult[0]->sphere;
	
	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$user_id = $record['id'];
			$hours = $record['hours'];
			$billable = $record['billable'];
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set target_billable=%f,target_total=%f where user_id=%d",$billable,$hours,$user_id));
		}
	}
	?> 

	<form method="post" name="set_capacity" enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
						
	<?php
			$positions_query = $wpdb->prepare("select distinct ".$wpdb->prefix."position.ID,position_title from ".$wpdb->prefix."position
				inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."position.ID=".$wpdb->prefix."useradd.position
				where sphere=%s and status=1 and user_id!=29
				order by rank desc",$sphere);
			
			$sphere_members_query = $wpdb->prepare("select user_id,display_name,position_title,".$wpdb->prefix."position.ID,position_title,target_billable,target_total 
				from ".$wpdb->prefix."useradd 
				inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
				inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
				where ".$wpdb->prefix."useradd.sphere=%s and ".$wpdb->prefix."useradd.user_id!=29 and status=1 
				order by ".$wpdb->prefix."users.display_name",$sphere);
				
			if($uid==11)
			{
				$sphere_members_query = "select user_id,display_name,position_title,".$wpdb->prefix."position.ID,target_billable,target_total
					from ".$wpdb->prefix."useradd 
					inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
					inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID					
					where ".$wpdb->prefix."useradd.user_id!=29 
					order by ".$wpdb->prefix."users.display_name";
					
				$positions_query = "select distinct ".$wpdb->prefix."position.ID,position_title
					from ".$wpdb->prefix."position
					inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."position.ID=".$wpdb->prefix."useradd.position
					where status=1 and user_id!=29
					order by rank desc";
			}
			
			$sphere_members_results = $wpdb->get_results($sphere_members_query);
			$positions_results = $wpdb->get_results($positions_query);
			
			echo '<li><input type="submit" name="save-info" value="save" class="my-buttons" /></li>';
			echo '<li>&nbsp;</li>';
			echo '<li>To update each position, input an amount in <b>Percent Billable</b> or <b>Work Day Hours</b> portion of the <b>Positions</b> section and it will update all staff with that title</li>';
			echo '<li>&nbsp;</li>';
			echo '<li><h2>Positions</h2></li>';
			echo '<li><table>
				<tr>
				<th><strong>Position Title</strong></th>
				<th style="text-align: center;"><strong>Percent Billable</strong></th>
				<th style="text-align: center;"><strong>Work Day Hours</strong></th>
				</tr>';
			foreach($positions_results as $pr)
			{
				echo '<tr>';
				echo '<input type="hidden" name="position['.$pr->ID.'][position_id]" value="'.$pr->ID.'" />';
				echo '<td>'.$pr->position_title.'</td>';
				echo '<td style="text-align: center;"><input type="number" min="0" max="100" name="position['.$pr->ID.'][percent]" onchange="updatePosition('.$pr->ID.');" /></td>';
				echo '<td style="text-align: center;"><input type="number" min="0" max="100" name="position['.$pr->ID.'][daily]" onchange="updatePosition('.$pr->ID.');" /></td>';
				echo '</tr>';
			}
			echo '</table></li>';
			echo '<li>&nbsp;</li>';
			echo '<li><h2>Staff</h2></li>';
			echo '<li><table width="100%">
					<tr>
					<th><strong>Employee</strong></th>
					<th><strong>Position</strong></th>
					<th style="text-align: center;"><strong>Percent Billable</strong></th>
					<th style="text-align: center;"><strong>Work Day Hours</strong></th>
					</tr>';
			foreach($sphere_members_results as $smr)
			{
				echo '<tr>';
				echo '<input type="hidden" name="record['.$smr->user_id.'][id]" value="'.$smr->user_id.'" />';
				echo '<td>'.$smr->display_name.'</td>';
				echo '<input type="hidden" name="record['.$smr->user_id.'][position]" value="'.$smr->ID.'" />';
				echo '<td>'.$smr->position_title.'</td>';
				echo '<td style="text-align: center;"><input type="number" min="0" max="100" name="record['.$smr->user_id.'][billable]" readonly 
					value="'.number_format($smr->target_billable, 0).'" /></td>';
				echo '<td style="text-align: center;"><input type="number" min="0" max="184" name="record['.$smr->user_id.'][hours]" readonly 
					value="'.number_format($smr->target_total,0).'" /></td>';
				echo '</tr>';
			}
			echo '</table></li>';
			echo '<li>&nbsp;</li>';
			echo '<li><input type="submit" name="save-info" value="save" class="my-buttons" /></li>';
	?>
	<script type="text/javascript">
		function updatePosition(id){
			var myForm = document.forms.set_capacity;
			var percent = myForm.elements['position[' + id + '][percent]'].value;
			var hours = myForm.elements['position[' + id + '][daily]'].value;
			var positionFields = document.querySelectorAll("[name$='[position]']");//select all fields ending with "[position]"
			var billableFields = document.querySelectorAll("[name$='[billable]']");//select all fields ending with "[billable]"
			var hoursFields = document.querySelectorAll("[name$='[hours]']");//select all fields ending with "[hours]"
			
			for(i=0;i<positionFields.length;i++){
				if(positionFields[i].value == id)
				{
					if(percent != 0){
						billableFields[i].value = percent;
					}
					if(hours != 0){
						hoursFields[i].value = hours;
					}
				}
			}
		}
	</script>
			</ul>
			</div>
			</div>						
		</div>
	</form>
<?php } 
add_shortcode('capacity_set','billyB_capacity_set')
?>