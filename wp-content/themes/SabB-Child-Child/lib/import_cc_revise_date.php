<?php
function billyB_import_cc_revise_date()
{
	if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	
	if($uid != 11 and $uid != 79){wp_redirect(get_bloginfo('siteurl')."/dashboard"); exit;}
	?>
	<form name="new_exp" method="post" enctype="multipart/form-data" onsubmit="checkForm();">
	<?php
	if(isset($_POST['upload']))
	{
		$records = $_POST['record'];
		$now = time();
		
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$employee_id = $details[0];
			$report_id = $employee_id.$now;
			$employee_gp_id = $details[1];
			$date = $details[2];
			$id = $details[3];
			$notes = $details[3];
			$amount = $details[4];
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_date=%d where employee_expense_id=%d",$date,$id));
			
			//$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."employee_expenses (employee_id,employee_gp_id,expense_quantity,expense_amount,expense_date,
			//	ee_mastercard,employee_expense_notes,expense_report_id)
			//	values (%d,%s,1,%f,%d,1,%s,%d)",$employee_id,$employee_gp_id,$amount,$date,$notes,$report_id));
			//$wpdb->query($insert_query);
		}
		echo '<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">';
		echo 'Thank you.  The file has been uploaded.';
		echo '</ul></div></div></div></div>';				
		get_footer(); exit;
	}
	/* BillyB one-time use to update the notes field when the original upload didn't populate correctly
	if(isset($_POST['revise_import']) and !empty($_FILES['import']['tmp_name']))
	{
		$file = $_FILES['import']['tmp_name'];
		$row = 1;
		$total = 0;
		if(($handle = fopen($file, "r")) !==FALSE)
		{
			while (($data = fgetcsv($handle, 1000, ",")) !==FALSE)
			{
				$num = count($data);
				$row++;
				
				$exp_id = $data[0];
				$notes = $data[1];
				
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set employee_expense_notes=%s 
					where employee_expense_id=%d",$notes,$exp_id));
			}
			fclose($handle);
		}
	}
	*/
	if(isset($_POST['import-info']) and !empty($_FILES['import']['tmp_name']))
	{
		echo '<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">';
		echo '<table width="100%">';
		echo '<tr><th><b><font size="3">User</font></b></th>
			<th><b><font size="3">Date</font></b></th>
			<th><b><font size="3">ID</font></b></th>
			<th><b><font size="3">Description</font></b></th>
			<th><b><font size="3">Amount</font></b></th>
			</tr>';
		$file = $_FILES['import']['tmp_name'];
		$row = 1;
		$total = 0;
		if(($handle = fopen($file, "r")) !==FALSE)
		{
			while (($data = fgetcsv($handle, 1000, ",")) !==FALSE)
			{
				$num = count($data);
				$row++;
				
				if($data[0] != $user_gp_id)
				{
					$user_gp_id = $data[0];
					$name = $data[1]." ".$data[2];
					if($data[0] == "")
					{
						$user_results = $wpdb->get_results($wpdb->prepare("select ID,display_name from ".$wpdb->prefix."users
							where display_name=%s",$name));
						$user_results = $wpdb->get_results($user_query);
					}
					else
					{
						$user_results = $wpdb->get_results($wpdb->prepare("select ".$wpdb->prefix."users.ID,display_name from ".$wpdb->prefix."users
							inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
							where gp_id=%s",$user_gp_id));
					}
				}
				$string = $data[5]." ".$data[7]." ".$data[8]." ".$data[10]." ".$data[11]." ".$data[13]." ".$data[14]." ".$data[15];//Testing
				$amount = $data[6];
				$user_id = $user_results[0]->ID;
				if($user_id == 0){$error = 1;}
				if($amount != 0)
				{
					echo '<tr>';
					echo '<td>'.($user_id==0 ? '<font color="red"><strong>None</strong></font>' : $user_results[0]->display_name).'</td>';
					echo '<td>'.date('m-d-Y',strtotime($data[3])).'</td>';
					echo '<td>'.$data[4].'</td>';
					echo '<td>'.$string.'</td>';
					echo '<td>$'.number_format($amount,2).'</td>';
					echo '</tr>';
					echo '<input type="hidden" name="record['.$row.'][details]" value="'.$user_id.',,,'.$data[0].',,,'.strtotime($data[3]).',,,'.$data[4].',,,'.$string.',,,'.$amount.'" />';
				}
				$total += $amount;
			}
			fclose($handle);
		}
		echo '<tr><td>&nbsp;</td></tr>
			<tr>
			<td><strong>Total</strong></td><td>&nbsp;</td><td>&nbsp;</td><td><strong>$'.number_format($total,2).'</strong></td>
			</tr>';
		echo '</table>';
		echo '</ul></div></div></div>';
		?>
		<div id="right-sidebar" class="page-sidebar"><div class="padd10">
			<ul class="xoxo">
				<li class="widget-container widget_text" id="ad-other-details">
				<?php
				//if($error != 1)
				//{
					echo '<input type="submit" name="upload" value="Upload" class="my-buttons-submit"/>';
				//}
				//else
				//{
				//	echo 'The Upload has issues, please see items highlighted in <font color="red">red</font>';
				//}
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
					<li><h3>Hints:</h3><p>Amount needs to be in Number formant (no commas), file needs to be in CSV format, and first row needs to be removed</p></li>
					<li>&nbsp;</li>
					<li><input type="file" name="import" class="my-buttons" /></li>
					<li>&nbsp;</li>
					<li><input type="submit" name="import-info" value="Import" class="my-buttons" /></li>
				</ul>
			</div>
		</div>
	</div>
	</form>	
<?php }
add_shortcode('import_cc_revise_date','billyB_import_cc_revise_date')
?>