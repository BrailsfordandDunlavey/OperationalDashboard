<?php
function billyB_import_billings()
{
if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	global $current_user,$wpdb,$wp_query;
	get_currentuserinfo();
	$uid = $current_user->ID;
	$now = time();
	$periods = array(strtotime(date('Y-m-t',$now)));
	$num_of_periods = 36;
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
		$records = $_POST['record'];
		$now = time();
		$new_records = 0;
		$updated_records = 0;
		
		foreach($records as $record)
		{
			$details = explode(",,,",$record['details']);
			$project_id = $details[0];
			$period = $details[1];
			$expense_amount = $details[2];
			$no_bills = $details[3];
			$fee_amount = $details[4];
			$status = 0;
			
			$previous = $wpdb->get_results($wpdb->prepare("select invoice_id,invoice_fee_amount,invoice_expense_amount,invoice_no_bill from ".$wpdb->prefix."invoices 
				where project_id=%d and invoice_period=%d",$project_id,$period));
			if(empty($previous[0]->invoice_id))
			{
				if($fee_amount != 0 or $expense_amount!=0 or $no_bills!=0)
				{
					$wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."invoices (project_id,invoice_period,invoice_fee_amount,invoice_expense_amount,
						invoice_no_bill,invoice_status)
						values (%s,%d,%f,%f,%f,%d)",$project_id,$period,$fee_amount,$expense_amount,$no_bills,$status));
					$new_records++;
				}
			}
			else
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."invoices set invoice_fee_amount=%f,invoice_expense_amount=%f,invoice_no_bill=%f
					where invoice_id=%d",$fee_amount,$expense_amount,$no_bills,$previous[0]->invoice_id));
				$updated_records++;
			}
		}
		echo '<div id="content">
				<div class="my_box3">
					<div class="padd10">
						<ul class="other-dets_m">';
		echo 'Thank you.  The file has been uploaded with '.$new_records.' new record(s) and '.$updated_records.' updated record(s).';
		echo '</ul></div></div></div></div>';
		$_POST = array();
		get_footer(); exit;
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
			<th><b><font size="3">Project</font></b></th>
			<th><b><font size="3">Date</font></b></th>
			<th><b><font size="3">Expense Amount</font></b></th>
			<th><b><font size="3">No Bills</font></b></th>
			<th><b><font size="3">Fee Amount</font></b></th>
			</tr>';
		$file = $_FILES['import']['tmp_name'];
		$row = 1;
		if(($handle = fopen($file, "r")) !==FALSE)
		{
			while (($data = fgetcsv($handle, 1000, ",")) !==FALSE)
			{
				$num = count($data);
				$row++;
				$gp_project_number = rtrim($data[0]," ");
				$project_results = $wpdb->get_results($wpdb->prepare("select ID,abbreviated_name from ".$wpdb->prefix."projects
					where gp_project_number=%s",$gp_project_number));
				if(empty($project_results))
				{
					$gp_project_number = '<font color="red"><strong>None - '.$data[0].'</strong></font>';
					$error = 1;
				}
				echo '<tr>';
				$project_id = $project_results[0]->ID;
				echo '<td>'.(!empty($project_results[0]->abbreviated_name) ? $project_results[0]->abbreviated_name : $gp_project_number).'</td>';
				echo '<td>'.date('m-d-Y',$period).'</td>';
				$font_start = "";
				$font_end = "";
				if($data[1] == 0 and $data[2] == 0 and $data[3]==0)
				{
					$error = 1;
					$font_start = '<font color="red"><strong>';
					$font_end = '</strong></font>';
				}
				echo '<td>'.$font_start."$".number_format($data[1],2).$font_end.'</td>';
				echo '<td>'.$font_start."$".number_format($data[2],2).$font_end.'</td>';
				echo '<td>'.$font_start."$".number_format($data[3],2).$font_end.'</td>';
				echo '</tr>';
				echo '<input type="hidden" name="record['.$row.'][details]" value="'.$project_id.',,,'.$period.',,,'.$data[1].',,,'.$data[2].',,,'.$data[3].'" />';
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
		<strong>Format (columns):  Project, Expense Amount, No-Bills, Fee Amount</strong><br/>
		<strong>Note:  Remove header row(s), and change amounts to number format.</strong><br/><br/>
			<div class="my_box3">
				<div class="padd10">				
					<ul class="other-dets_m">
						<li><select name="select_period" class="do_input_new">
						<?php
						foreach($periods as $p)
						{
							echo '<option value="'.$p.'" '.($period == $p ? 'selected="selected"' : '').'>'.date('F Y',$p).'</option>';
						}
						?>
						</select>
						<li><input type="file" name="import" class="my-buttons" /></li>
						<li><input type="submit" name="import-info" value="Import" class="my-buttons" /></li>
						<li>&nbsp;</li>
					</ul>
				</div>
			</div>
		</div>
	</form>
<?php }
add_shortcode('import_billings','billyB_import_billings')
?>