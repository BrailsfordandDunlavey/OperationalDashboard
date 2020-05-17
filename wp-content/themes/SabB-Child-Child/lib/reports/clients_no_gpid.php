<?php
function billyB_clients_no_gp_id()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$user_rights_results = $wpdb->get_results($wpdb->prepare("select team from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	//if($user_rights_results[0]->team != 'Finance' or $user_rights_results[0]->team != 'Human Resources'){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
 
	$client_results = $wpdb->get_results("select ".$wpdb->prefix."clients.client_id,client_name from ".$wpdb->prefix."clients 
		inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."clients.client_id=".$wpdb->prefix."projects.client_id
		where client_gp_id='' and status in (1,2)
		order by client_name");
		
	if(isset($_POST['save-info']))
	{
		$records = ($_POST['record']);
		
		foreach($records as $record)
		{
			$client_id = $record['client_id'];
			$gp_id = $record['gp_id'];
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."clients set client_gp_id=%s where client_id=%d",$gp_id,$client_id));
		}
		?>
		<div id="content">
		<div class="my_box3">
		<div class="padd10">
		<?php
		echo "Thank you.  The client record has been updated.<br/><br/>";
		?>								
		<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
		</div></div></div>
		<?php 
		$client_results = $wpdb->get_results("select ".$wpdb->prefix."clients.client_id,client_name from ".$wpdb->prefix."clients 
			inner join ".$wpdb->prefix."projects on ".$wpdb->prefix."clients.client_id=".$wpdb->prefix."projects.client_id
			where client_gp_id='' and status in (1,2)
			order by client_name");
	}
	if(!empty($client_results))
	{
		?>
		<form method="post"  enctype="multipart/form-data">
		<div id="content"><h3><?php echo "Update Missing Client GP IDs";?></h3><br/>
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				
				$t=0;
				foreach($client_results as $client)
				{
					$client_id = $client->client_id;
					$project_results = $wpdb->get_results($wpdb->prepare("select ID from ".$wpdb->prefix."projects 
						where client_id=%d",$client_id));
					$project_id = $project_results[0]->ID;
					echo '<li><a href="/?p_action=project_card&ID='.$project_id,'" >'.$client->client_name.'</a>
						<input type="hidden" name="record['.$t.'][client_id]" value="'.$client->client_id.'" />
						<input type="text" name="record['.$t.'][gp_id]" class="do_input_new" placeholder="GP ID" /></li>';
					$t++;
				}
				?>
			</ul>	
			<ul class="other-dets_m">
			<li>&nbsp;</li>
			<li><p><input type="submit" name="save-info" class="my-buttons" value="<?php echo "Save"; ?>" /></p></li>
			</ul>
			</div>
			</div>
		</div>
		</form>
		<?php 
	}
}
add_shortcode('no_gp_id','billyB_clients_no_gp_id')
?>