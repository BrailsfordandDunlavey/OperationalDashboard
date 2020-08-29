<?php
function billyB_client_contact_export()
{
	if(!is_user_logged_in())
	{ 
		wp_redirect(get_bloginfo('siteurl')."/wp-login.php?redirect_to=contacts-export"); 
		exit;
	}

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$useradd = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."useradd where user_id=%d",$uid));
	if($useradd[0]->team!='Finance' and $useradd[0]->team!='Business Development'){wp_redirect(get_bloginfo('siteurl')."/dashboard");exit;}
	
 						
	if(isset($_POST['export-info']))
	{				
		ob_end_clean();
		
		$records = ($_POST['record']);
		$csv = array();
		$filename = "bd_client_contact_export - ".time().".csv";
		
		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		
		$output = @fopen('php://output', 'w');
								
		fputcsv($output, array('First Name','Last Name','Email','Phone','School'));
		
		$market = $_POST['market'];
		
		$records = $wpdb->get_results($wpdb->prepare("select cp_first_name,cp_last_name,cp_email,cp_phone,client_name
			from ".$wpdb->prefix."client_person
			left join ".$wpdb->prefix."clients on ".$wpdb->prefix."client_person.client_id=".$wpdb->prefix."clients.client_id
			where cp_market like %s",'%'.$market.'%'));
		
		foreach($records as $record)
		{				
			fputcsv($output,array($record->cp_first_name,$record->cp_last_name,$record->cp_email,$record->cp_phone,$record->client_name));
		}
		fclose($output);
		exit();
	}
	?>   
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<form method="post"  enctype="multipart/form-data">
					<div id="content">
						<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
							<li>&nbsp;</li>
							<?php
							$markets = $wpdb->get_results($wpdb->prepare("select distinct cp_market from ".$wpdb->prefix."client_person"));
							
							echo '<li><h3>Market:</h3><p><select name="market" class="do_input_new">
								<option value="">Select Market</option>';
							
							foreach($markets as $m)
							{
								echo '<option>'.$m->cp_market.'</option>';
							}
								
							echo '	</select></p></li>';
							echo '<li>&nbsp;</li>';
							echo '<li><h3>&nbsp;</h3><p><input type="submit" name="export-info" class="my-buttons" value="Export" /></p></li>';
							
							?>
						</ul>
						</div>
						</div>
					</div>
				</form>						
			</div>
		</div>
<?php } 
add_shortcode('client_contact_export','billyB_client_contact_export')
?>