<?php
function billyB_my_client_contacts()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	
	$uid = $current_user->ID;
	$change_array = array(11,132);
	
	if(in_array($current_user->ID,$change_array))
	{if(isset($_POST['set_id'])){$uid = $_POST['change_id'];}}
	else{$uid = $current_user->ID;}
	
	if(in_array($current_user->ID,$change_array))
	{
		?>
		<form method="post"  enctype="multipart/form-data">
		<div id="content">
			<div class="my_box3">
			<div class="padd10">
			<ul class="other-dets_m">
				<?php
				echo '<li><h3>Change Employee:</h3><p><select class="do_input_new" name="change_id">';
				$users_results = $wpdb->get_results("select * from ".$wpdb->prefix."users 
					where display_name!='admin' and display_name!='bbannister' and display_name!='TEST'	order by display_name");
				foreach($users_results as $user)
				{
					echo '<option value="'.$user->ID.'" '.($uid==$user->ID ? "selected='selected'" : "").'>'.$user->display_name.'</option>';
				}
				echo '</select>';
				echo '<input type="submit" name="set_id" class="my-buttons" value="Change ID" /></p></li>';
				?>
				</ul>
			</div>
			</div>						
		</div>
		</form>
		<?php
	}
	$p = $wpdb->get_results($wpdb->prepare("select client_person_id,cp_last_name,cp_first_name,".$wpdb->prefix."clients.client_id,cp_title,cp_phone,cp_email,client_name
		from ".$wpdb->prefix."client_person
		left join ".$wpdb->prefix."clients on ".$wpdb->prefix."client_person.client_id=".$wpdb->prefix."clients.client_id
		where cp_responsible_party=%d
		order by client_name,cp_last_name,cp_first_name",$uid));
	?>
	<div id="content">
		<div class="my_box3">
		<div class="padd10"><h3>My Contacts</h3>
		<ul class="other-dets_m">
			<li>&nbsp;</li>
			<?php
			if(!empty($p))
			{
				echo '<li><table width="100%">';
				echo '<tr>
						<td><b><u>Client Name</u></b></td>
						<td><b><u>Contact Name</u></b></td>
						<td><b><u>Title</u></b></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><b><u>Phone Number<u></b></td>
						<td><b><u>Email</u></b></td>
					</tr>';
				foreach($p as $pp)
				{
					if(empty($pp->cp_phone)){$phone = 'No Phone Number';}
					else{$phone = '('.substr($pp->cp_phone, 0, 3).') '.substr($pp->cp_phone, 3, 3).'-'.substr($pp->cp_phone,6);}
					if(empty($pp->cp_email)){$email = 'No email';}else{$email = '<b><a href="mailto:'.$pp->cp_email.'">'.$pp->cp_email.'</a></b>';}
					if(empty($pp->client_name)){$client_name = "None";}else{$client_name = $pp->client_name;}
					$contact = '<b><a href="'.get_bloginfo('siteurl').'/?p_action=edit_client_contact&ID='.$pp->client_person_id.'">'.$pp->cp_first_name.' '.$pp->cp_last_name.'</a></b>';
					echo '<tr id="1ln'.$pp->cp_last_name.'2fn'.$pp->cp_first_name.'3cn'.$client_name.'">
							<td>'.$client_name.'</td>
							<td>'.$contact.'</td>
							<td>'.$pp->cp_title.'</td>
						</tr>
						<tr>
							<td style="border-bottom: 1px solid #4C4646;">&nbsp;</td>
							<td style="border-bottom: 1px solid #4C4646;">'.$phone.'</td>
							<td style="border-bottom: 1px solid #4C4646;">'.$email.'</td>
						</tr>';
				}
				echo '</table></li>';
				echo '<li><a href="'.get_bloginfo('siteurl').'/new-client-contact">Enter a new contact</a></li>';
			}
			else
			{
				echo '<li>You do not have any contacts at this time</li>';
				echo '<li><a href="'.get_bloginfo('siteurl').'/new-client-contact">Enter a new contact</a></li>';
			}
			?>
			<li>&nbsp;</li>
		</ul>	
		</div>
		</div>
	</div>
	
<?php } 
add_shortcode('my_client_contacts','billyB_my_client_contacts')
?>