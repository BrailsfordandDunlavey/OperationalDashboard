<?php
function billyB_import_contacts()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$now = time();
	$periods = array(strtotime(date('Y-m-t',$now)));
	$num_of_periods = 6;
	for($i=1;$i<$num_of_periods;$i++)
	{
		array_push($periods,strtotime(date('Y-m-t',strtotime(date('Y-m-01',$now).' - '.$i.' months'))));
	}
 
	if($uid != 11 and $uid != 94){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	?>
	<form name="new_exp" method="post"  enctype="multipart/form-data" onsubmit="checkForm();">
	<?php
	if(isset($_POST['upload']))
	{
		/*
		$records = $_POST['record'];
		
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$project_id = $details[0];
			$period = $details[1];
			$expense_amount = $details[2];
			$no_bills = $details[3];
			$fee_amount = $details[4];
			$status = 0;
			
			$previous = $wpdb->get_results($wpdb->prepare("select invoice_id from ".$wpdb->prefix."invoices 
				where project_id=%d and invoice_period=%d",$project_id,$period));
			if(empty($previous))
			{
				if($fee_amount != 0 or $expense_amount!=0 or $no_bills!=0)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."invoices (project_id,invoice_period,invoice_fee_amount,invoice_expense_amount,
						invoice_no_bill,invoice_status)
						values (%s,%d,%f,%f,%f,%d)",$project_id,$period,$fee_amount,$expense_amount,$no_bills,$status));
				}				
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."invoices set invoice_fee_amount=%f,invoice_expense_amount=%f,invoice_no_bill=%f
					where invoice_id=%d",$fee_amount,$expense_amount,$no_bills,$previous[0]->invoice_id));
			}
		}
		echo '<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">';
		echo 'Thank you.  The file has been uploaded.';
		echo '</ul></div></div></div></div>';
		$_POST = array();
		get_footer(); exit;
		*/
		
	}
	if(isset($_POST['import-info']) and !empty($_FILES['import']['tmp_name']))
	{
		$period = $_POST['select_period'];
		echo '<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">';
		echo '<table width="100%">';
		echo '<tr>
			<th><b><font size="3">First Name</font></b></th>
			<th><b><font size="3">Last Name</font></b></th>
			<th><b><font size="3">Email</font></b></th>
			</tr>';
		$file = $_FILES['import']['tmp_name'];
		$row = 1;
		if(($handle = fopen($file, "r")) !==FALSE)
		{
			while (($data = fgetcsv($handle, 1000, ",")) !==FALSE)
			{
				$num = count($data);
				$row++;
				$first_name = $data[0];
				$last_name = $data[1];
				$email = $data[2];
				
				$email_query = $wpdb->get_results($wpdb->prepare("select client_person_id from ".$wpdb->prefix."client_person where cp_email=%s",$email));
				
				if(!empty($email_query))
				{
					$font_start = '<font color="red"><b>';
					$font_end = '</b></font>';
					$id = ' ('.$email_query[0]->client_person_id.')';
				}
				else
				{
					$font_start = '';
					$font_end = '';
					$id = '';
				}
				echo '<tr>';
				
				echo '<td>'.$font_start.$data[0].$id.$font_end.'</td>';
				echo '<td>'.$font_start.$data[1].$font_end.'</td>';
				echo '<td>'.$font_start.$data[2].$font_end.'</td>';
				echo '</tr>';
				echo '<input type="hidden" name="record['.$row.'][details]" value="'.$data[0].',,,'.$data[1].',,,'.$data[2].'" />';
			}
			fclose($handle);
		}
		echo '</table>';
		echo '</ul></div></div></div>';
		?>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10">
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
				<?php
				if($error != 1)
				{
					echo '<input type="submit" name="upload" value="Upload" class="my-buttons-submit"/>';
				}
				else
				{
					echo 'Please see errors in <font color="red"><strong>red</strong></font>';
				}
				?>
				</li>
			</ul>
		</div></div>
		<?php
	}
 ?>
		<div id="content">
			<div class="my_box3">
				<div class="padd10">				
					<ul class="other-dets_m">
						<li><input type="file" name="import" class="my-buttons" /></li>
						<li><input type="submit" name="import-info" value="Import" class="my-buttons" /></li>
						<li>&nbsp;</li>
					</ul>
				</div>
			</div>
		</div>
	</form>
<?php }
add_shortcode('import_contacts','billyB_import_contacts')
?>