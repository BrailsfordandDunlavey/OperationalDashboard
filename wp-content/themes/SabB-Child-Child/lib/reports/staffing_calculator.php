<?php
function billyB_staffing_calculator()
{
	if(!is_user_logged_in()) { $_SESSION['redirect_me_back'] = $_SERVER['php_self']; wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$sphere_leaders = array(11,52,58,65,88,139,40,39,103,147);//for access to this page
	$allowed_array = array(11,139,58,52);//for showing the cost column
	if(!in_array($uid,$sphere_leaders)){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	
	$useradd_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	$sphere = $useradd_results[0]->sphere;
	
	$sphere_results = $wpdb->get_results("select distinct sphere from ".$wpdb->prefix."useradd order by sphere");
	foreach($sphere_results as $sr)
	{
		$sphere_value = $sr->sphere;
		$sphere_name = strtolower(str_replace(" ","_",$sphere_value));
		
		if(isset($_POST[$sphere_name]))
		{
			$sphere = $sphere_value;
		}
	}
	
	
	$team_results = $wpdb->get_results($wpdb->prepare("select distinct team from ".$wpdb->prefix."useradd where sphere=%s and team!=''",$sphere));
	
	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$user_id = $details[0];
			$allocation = $details[1];
			$new_allocation = $record['allocation'];
			
			if($allocation != $new_allocation and in_array($uid,$allowed_array))
			{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."useradd set allocation=%f where user_id=%d",$new_allocation,$user_id));
			}
		}
		$data = $_POST['data'];
		foreach($data as $d)
		{
			$details = explode(",,,",$d['details']);
			$user_id = $details[0];
			$team = $details[1];
			$percent = $details[2];
			$new_percent = $d['percent']*1;
			if(empty($new_percent)){$new_percent = 0;}
			
			if($percent != $new_percent)
			{
				if($percent == 0)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."group_user (user_id,team,percent) values(%d,%s,%d)",$user_id,$team,$new_percent));
				}
				elseif($new_percent == 0)
				{
					$wpdb->query($wpdb->prepare("delete from ".$wpdb->prefix."group_user where user_id=%d and team=%s and percent=%d",$user_id,$team,$percent));
				}
				else
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."group_user set percent=%d where user_id=%d and team=%s",$new_percent,$user_id,$team));
				}
			}
			//echo 'Details: ';print_r($details);echo '<br/>Percent: '.$new_percent.'<br/>';
		}
	}
	
	$sphere_members = $wpdb->get_results($wpdb->prepare("select user_id,display_name,team,position_title,allocation
		from ".$wpdb->prefix."useradd
		inner join ".$wpdb->prefix."users on ".$wpdb->prefix."useradd.user_id=".$wpdb->prefix."users.ID
		inner join ".$wpdb->prefix."position on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position.ID
		where sphere=%s and status=1 order by display_name",$sphere));
?>
	<form method="post" name="projected_revenue" enctype="multipart/form-data">
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<style>input[type=number]{width:68px;}</style>
			
						
<?php
			
		
			$teams_array = array();
			foreach($team_results as $t)
			{
				array_push($teams_array,$t->team);
			}
			if($sphere ==  'Sphere KMV' and !in_array('Higher Ed',$teams_array)){array_push($teams_array,'Higher Ed');}
			
			if($sphere == "Higher Ed"){$group_name = "Cluster";}else{$group_name = "Group";}
			echo '<li><h3>'.$group_name.':</h3>';
			echo '<select id="select_group" value="'.$group_name.'" class="do_input_new" onchange="hideRows();">
				<option value="all">All '.$group_name.'s</option>';
			foreach($teams_array as $t)
			{
				echo '<option>'.$t.'</option>';
			}
			echo '</select>';
			echo '<br/><a href="'.get_bloginfo("siteurl").'/operational-dashboard-revenue/">WUC Details</a>&nbsp;&nbsp;
				<a href="'.get_bloginfo('siteurl').'/wuc-dashboard/">WUC Dashboard</a>';
			echo '</li>';
			echo '<li>
				<div>
				<div style="overflow-x:auto;">
				<table style="table-layout:fixed;">
				<tr>
				<th><div style="width:80px;word-wrap:break-word;"><b><u>Name</u></b></div></th>
				<th><div style="width:80px;word-wrap:break-word;"><b><u>Title</u></b></div></th>
				<th><div style="width:80px;word-wrap:break-word;"><b><u>Primary Group</u></b></div></th>
				'.(in_array($uid,$allowed_array) ? '<th><div style="width:80px;word-wrap:break-word;"><b><u>Cost</u></b></div></th>' : '' ).'
				<th><div style="width:80px;word-wrap:break-word;"><b><u>% Committed</u></b></div></th>';
			foreach($teams_array as $tr)
			{
				echo '<th><div style="width:80px;word-wrap:break-word;"><b><u>'.$tr.'</u></b></div></th>';
			}
			echo '</tr></table></div>';
			echo '<div style="overflow-x:auto;height:400px;"><table style="table-layout:fixed;">';
			$i = 0;
			foreach($sphere_members as $sm)
			{
				$user = $sm->user_id;
				$percent_results = $wpdb->get_results($wpdb->prepare("select sum(percent) as percent from ".$wpdb->prefix."group_user where user_id=%d",$user));
				$percent_sum = $percent_results[0]->percent;
				if($percent_sum != 100){$font = 'style="color:red;"';}else{$font = '';}
				echo '
					<tr id="g'.$sm->team.'"><input type="hidden" name="record['.$user.'][details]" value="'.$user.',,,'.$sm->allocation.'" />
					<td><div style="width:80px;word-wrap:break-word;">'.$sm->display_name.'</div></td>
					<td><div style="width:80px;word-wrap:break-word;">'.$sm->position_title.'</div></td>
					<td><div style="width:80px;word-wrap:break-word;">'.$sm->team.'</div></td>
					'.(in_array($uid,$allowed_array) ? '<td><div style="width:80px;word-wrap:break-word;"><input type="number" name="record['.$user.'][allocation]" 
						value="'.($sm->allocation==0 ? '' : round($sm->allocation,0)).'" /></div></td>' : '' ).'
					<td><div style="width:80px;word-wrap:break-word;"><input type="number" id="t'.$user.'" name="record['.$user.'][committed]" readonly value="'.$percent_sum.'" '.$font.' /></div></td>';
				foreach($teams_array as $tr)
				{
					$i++;
					$team = $tr;
					$results = $wpdb->get_results($wpdb->prepare("select percent from ".$wpdb->prefix."group_user where user_id=%d and team=%s",$user,$team));
					$value = $results[0]->percent;
					if(empty($value)){$value = 0;}
					echo '<td><div style="width:80px;word-wrap:break-word;">
						<input type="hidden" name="data['.$i.'][details]" value="'.$user.',,,'.$team.',,,'.$value.'" />
						<input type="number" min="0" max="100" name="data['.$i.'][percent]" value="'.($value==0 ? '' : $value).'"
							id="u'.$user.'" onkeyup="calcCommitment('.$user.');" /></div></td>';
				}
				echo '</tr>';
			}
			echo '</table></div></div></li>';
			
			echo '<li>&nbsp;</li>';
			echo '<li><input type="submit" name="save-info" value="save" class="my-buttons-submit" /></li>';
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
				function calcCommitment(x){
					var total = 0;
					var totalField = document.getElementById('t' + x);
					var fields = document.querySelectorAll("[id^='u" + x + "']");
					for(i=0;i<fields.length;i++){
						total = total + fields[i].value*1;
					}
					totalField.value = total;
					if(total != 100){
						totalField.style.color = "red";
					}
					else{
						totalField.style.color = "black";
					}
				}
			</script>
			</ul>
			</div>
			</div>						
		</div>
		<div id="content-full">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
			<?php
			if($uid == 11)
			{
				echo '<li><h2>Spheres</h2>';
				foreach($sphere_results as $s)
				{
					$sphere_value = $s->sphere;
					$sphere_name = strtolower(str_replace(" ","_",$sphere_value));
					if($s->sphere==$sphere){$button = "my-buttons-submit";}else{$button = "my-buttons";}
					echo '<input type="submit" name="'.$sphere_name.'" class="'.$button.'" value="'.$sphere_value.'" />';
				}
				echo '</li>';
			}
			?>
			</ul>
			</div>
			</div>
		</div>
	</form>

<?php } 
add_shortcode('staffing_calculator','billyB_staffing_calculator')
?>