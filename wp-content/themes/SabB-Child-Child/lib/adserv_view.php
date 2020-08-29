<?php
get_header();
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	if(isset($_POST['set_id'])){$uid = $_POST['change_id'];}
 	$project_id = $_GET['ID'];
	
	if(isset($_POST['save-info']))
	{
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$adserv_id = $record['id'];
			$adserv_name = $record['name'];
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."projects set abbreviated_name=%s where ID=%d",$adserv_name,$adserv_id));
		}
	}
	?>
	<div id="main_wrapper">
			<div id="main" class="wrapper">
			<form method="post"  enctype="multipart/form-data">
				<div id="content-full">
					<div class="my_box3">
						<div class="padd10">
							<ul class="other-dets_m">
							<?php
							$adserv_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where project_parent=%s",$project_id));
							if(!empty($adserv_results))
							{								
								echo '<li><table width="100%">
									<tr><th><b><u>Adserv Name</u></b></th>
									'.(($uid==94 or $uid==11 or $uid==235) ? "<th><b><u>Adserv Link</u></b></th>" : "" ).'
									<th><b><u>Start Date</u></b></th>
									<th><b><u>Fee Amount</u></b></th>
									<th><b><u>Sub Fee Amount</u></b></th>
									<th><b><u>Expense Amount</u></b></th>
									<th><b><u>Total Contract</u></b></th></tr>';
									
								foreach($adserv_results as $adserv)
								{
									$abb_name = $adserv->abbreviated_name;
									if(empty($abb_name)){$abb_name=$adserv->project_name;}
									if(empty($abb_name)){$abb_name='Adserv'.$adserv->ID;}
									
									echo '<th><input type="text" name="record['.$adserv->ID.'][name]" maxlength="25" value="'.$abb_name.'" /></th>
										'.(($uid==94 or $uid==11 or $uid==235) ? '<th><a href="'.get_bloginfo('siteurl').'/?p_action=edit_checklist&ID='.$adserv->ID.'">'.$abb_name.'</a></th>' : '' ).'
										<th>'.date('m-d-Y',$adserv->estimated_start).'</th>
										<th>$'.number_format($adserv->fee_amount,2).'</th>
										<th>$'.number_format($adserv->sub_fee_amount,2).'</th>
										<th>$'.number_format($adserv->expense_amount,2).'</th>
										<th>$'.number_format($adserv->fee_amount + $adserv->sub_fee_amount + $adserv->expense_amount,2).'</th>
										<input type="hidden" name="record['.$adserv->ID.'][id]" value="'.$adserv->ID.'" />
										</tr>';
								}
								echo '</table></li>';
							}
							else
							{
								echo '<li>There are no adservs on this project.</li>';
							}
							?>
							<li>&nbsp;</li>
							<li><p><input type="submit" name="save-info" class="my-buttons" value="Save" /></li>
							</ul>
						</div>
					</div>
				</div>
			</form>
			</div>
		</div>
<?php get_footer(); ?>