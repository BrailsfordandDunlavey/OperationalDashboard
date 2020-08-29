<?php
global $current_user,$wpdb,$wp_query;
get_currentuserinfo();
$uid = $current_user->ID;

if(!is_user_logged_in()) { wp_redirect(get_bloginfo('siteurl')."/wp-login.php"); exit; }

	if(isset($_POST['print-info']) or isset($_POST['save-info']))
	{
		$checklist = $_GET['ID'];
		$detail_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."projects where ID=%d",$checklist));
		$gp_id = $detail_results[0]->gp_id;
		$invoice_date = time();
		$invoice_period = $_POST['invoice_period'];
		$fee_invoice_number = $gp_id.date('my',$invoice_period)."F";
		$exp_invoice_number = $gp_id.date('my',$invoice_period)."E";
		$fee_amount = $_POST['fee_amount'];
		$invoice_paid = 0;
		$invoice_comment = trim($_POST['invoice_comment']);
		$invoice_status = 0;
		
		$expenses = $_POST['expense'];
		$vendors = $_POST['vendor'];
		
		foreach($expenses as $expense)//record the update of status in the database
		{
			$details = explode(',,,',$expense['details']);//total,expense_id,report_id,billed_status
			$expense_id = $details[1];
			$report_id = $details[2];
			$amt = $details[0];
			$billed_status = $details[3];
			
			if($expense['bill'] == 'bill')
			{
				$expense_total += $expense['amt'];
				
				if($billed_status != 1)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=1,billed_by=%d,billed_date=%d 
						where employee_expense_id=%d",$current_user->ID,time(),$expense_id));
				}
			}
			elseif($expense['bill']=="no-bill")
			{
				if($billed_status > 2)
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=-1,billed_by=%d,billed_date=%d 
						where employee_expense_id=%d",$current_user->ID,$invoice_date,$expense_id));
				}
				else
				{
					$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set expense_billable=3,billed_by=%d,billed_date=%d,billed_status=0 
						where employee_expense_id=%d",$current_user->ID,$invoice_date,$expense_id));
				}
			}
			elseif($expense['bill'] == "nothing" and $billed_status!=0)
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=0,billed_by=%d,billed_date=%d
					where employee_expense_id=%d",$current_user->ID,$invoice_date,$expense_id));
			}
			elseif($expense['bill'] == "on_invoice" and $billed_status!=2)
			{				
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status= 2,billed_by=%d,billed_date=%d 
					where employee_expense_id=%d",$current_user->ID,$invoice_date,$expense_id));
			}
			elseif($expense['bill'] == "billed" and $billed_status!=3)
			{				
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."employee_expenses set billed_status=3,billed_by=%d,billed_date=%d,billed_invoice='project invoice' 
					where employee_expense_id=%d",$current_user->ID,$invoice_date,$expense_id));
			}
		}
		foreach($vendors as $vendor)
		{
			$details = explode(',,,',$vendor['details']);
			$vp_id = $details[1];
			$billable = 1;
			if($vendor['bill'] == "bill"){$status = 1;}
			elseif($vendor['bill'] == "on_invoice"){$status = 2;}
			elseif($vendor['bill'] == "billed"){$status = 3;}
			elseif($vendor['bill'] == "nothing"){$status = 0;}
			elseif($vendor['bill'] == "no-bill"){$billable = 3; $status = 0;}
			else{$status = 0;}
			
			$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set billed_status=%d 
				where vendor_payable_id=%d",$status,$vp_id));
			
			if($vendor['bill'] == "no-bill")
			{
				$wpdb->query($wpdb->prepare("update ".$wpdb->prefix."vendor_payables set expense_billable=3,fee_billable=3
					where vendor_payable_id=%d",$vp_id));
			}
		}
		if(isset($_POST['print-info']))
		{
			$current_dir = getcwd();
			$fpdf_dir = $current_dir."/wp-content/themes/SabB-Child-Child/lib/fpdf181/";
			$fpdi_dir = $current_dir."/wp-content/themes/SabB-Child-Child/lib/fpdi/";
			chdir($fpdf_dir);
			
			require ('fpdf.php');
			
			chdir($fpdi_dir);
			
			require ('fpdi.php');
			
			chdir($current_dir);
			$backup_dir = $current_dir."/wp-content/expense_backup/";
			$pdf = new FPDI();
			$pdf->SetFont('Arial','B',16);
			//$pdf->Cell(40,10,'Hello Billy!');
			/*
			class PDF extends FPDF
			{
				function Header()
				{
					$this->SetFont('Arial','B',15);
					$this->Cell(80);
					$this->Cell(30,10,'Hello Billy',1,0,'C');
					$this->Ln(20);
				}
			}
			*/
		
			$expense_total = 0;
			$t = 0;
			$imagesPerPage = 1;
			$imagesPerRow = 0;
			$expense_array = array();
			foreach($expenses as $expense)
			{
				$details = explode(',,,',$expense['details']);//total,expense_id,report_id,billed_status
				$expense_id = $details[1];
				$report_id = $details[2];
				$amt = $details[0];
				$billed_status = $details[3];
				
				if($expense['bill'] == 'bill')
				{
					$backup_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."expense_backup where expense_report_id=%d",$report_id));
					
					if(empty($backup_results))
					{
						$backup_results = $wpdb->get_results($wpdb->prepare("select * from ".$wpdb->prefix."expense_backup where expense_id=%d",$expense_id));
					}
					if(count($backup_results) > 0){chdir($backup_dir);}
					
					for($i=0;$i<count($backup_results);$i++)
					{
						$image = $backup_results[$i]->expense_filename;
						if(!in_array($image,$expense_array))
						{
							if($imagesPerPage > 2){$pdf->AddPage();$imagesPerPage = 1;}
							array_push($expense_array,$image);
							$image_array = array('image/png','image/gif','image/jpeg','image/bmp','image/tiff');
							if(in_array(mime_content_type($image),$image_array))
							{
								if($t==0){$pdf->AddPage();}
								$imagesPerRow ++;
								list($width,$height) = getimagesize($image);
								$ratio = $width/$height;
								$max_width = 190;
								$max_height = 100;//was 125
								if($max_width/$max_height < $ratio){$max_height = "";}else{$max_width = "";}
								if($imagesPerRow === 1)
								{
									$pdf->Cell(0,10,'Report ID:  '.$report_id,0,1);
									$pdf->Image($image, 10,20, $max_width,$max_height);
								}
								if($imagesPerRow != 1)
								{
									$pdf->Cell(0,240,'Report ID:  '.$report_id);
									$pdf->Image($image, 10,145, $max_width,$max_height);$imagesPerRow = 0;	
								}
								$imagesPerPage ++;
								$t++;
							}
							if(mime_content_type($image) == 'application/pdf')
							{
								$pageCount = $pdf->setSourceFile($image);
								for($pageNo = 1; $pageNo <= $pageCount; $pageNo++)
								{
									$tplIdx = $pdf->importPage($pageNo);
									
									$pdf->addPage();
									$pdf->useTemplate($tplIdx, null, null, 0, 0, true);
								}
							}
						}
					}
				}
			}
			
			$pdf->Output();
			
			exit();
		}
	}
function sitemile_filter_ttl($title){return "Project Invoice";}
add_filter( 'wp_title', 'sitemile_filter_ttl', 10, 3 );	
get_header();

//FPDF Functions****************************************************************************************************************************

	function addClientAddress( $address )
	{
		$r1     = $this->w - 80;
		$r2     = $r1 + 68;
		$y1     = 40;
		$this->SetXY( $r1, $y1);
		$this->MultiCell( 60, 4, $address);
	}
	function addPageNumber( $page )
	{
		$r1  = $this->w - 80;
		$r2  = $r1 + 19;
		$y1  = 17;
		$y2  = $y1;
		$mid = $y1 + ($y2 / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+3 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,5, "PAGE", 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 9 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$page, 0,0, "C");
	}
	function addClient( $ref )
	{
		$r1  = $this->w - 31;
		$r2  = $r1 + 19;
		$y1  = 17;
		$y2  = $y1;
		$mid = $y1 + ($y2 / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+3 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,5, "CLIENT", 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 + 9 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$ref, 0,0, "C");
	}
	function addDate( $date )
	{
		$r1  = $this->w - 61;
		$r2  = $r1 + 30;
		$y1  = 17;
		$y2  = $y1 ;
		$mid = $y1 + ($y2 / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+3 );
		$this->SetFont( "Arial", "B", 10);
		$this->Cell(10,5, "DATE", 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+9 );
		$this->SetFont( "Arial", "", 10);
		$this->Cell(10,5,$date, 0,0, "C");
	}
	
//end FPDF Functions****************************************************************************************************************************
	//PRINT BACKUP*******************************************************************************************************
	
//Manage Periods - Create new and close old**********************************************
$today = time();

$active_period_array = array();

$active_period_query = "select invoice_period from ".$wpdb->prefix."invoice_periods where invoice_active='on' or projection_active='on'";
$active_period_results = $wpdb->get_results($active_period_query);
foreach($active_period_results as $active_period){array_push($active_period_array,$active_period->invoice_period);}

$period_one = strtotime(date('Y-m-t',$today));
$period_two = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_one) . " +1 month")));
$period_three = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_two) . " +1 month")));
$period_four = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_three) . " +1 month")));
$period_five = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_four) . " +1 month")));
$period_six = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_five) . " +1 month")));
$period_seven = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_six) . " +1 month")));
$period_eight = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_seven) . " +1 month")));
$period_nine = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_eight) . " +1 month")));
$period_ten = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_nine) . " +1 month")));
$period_eleven = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_ten) . " +1 month")));
$period_twelve = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_eleven) . " +1 month")));
$last_month = strtotime(date('Y-m-t',strtotime(date("Y-m-01", $period_one) . " -1 month")));

$invoice_period_array = array($last_month,$period_one,$period_two);
$projection_period_array = array($period_three,$period_four,$period_five,$period_six,$period_seven,$period_eight,$period_nine,$period_ten,$period_eleven,
	$period_twelve);
//BillyB create new period if doesn't exist
foreach($invoice_period_array as $invoice_period)
{
	if(!in_array($invoice_period,$active_period_array))
	{
		$invoice_query = "insert into ".$wpdb->prefix."invoice_periods (invoice_period,invoice_active,projection_active) values ('$invoice_period','on','on')";
		$wpdb->query($invoice_query);
	}
	$activate_query = "update ".$wpdb->prefix."invoice_periods set invoice_active='on' where invoice_period='$invoice_period'";
	$wpdb->query($activate_query);
}
foreach($projection_period_array as $projection_period)
{
	if(!in_array($projection_period,$active_period_array))
	{
		$projection_query = "insert into ".$wpdb->prefix."invoice_periods (invoice_period,projection_active) values ('$projection_period','on')";
		$wpdb->query($projection_query);
	}
}
//BillyB close periods
$update = "no";
foreach($active_period_results as $active_period)
{if($active_period->invoice_period < $today){$update = "yes";}}
if($update !="no")
{
	$close_projection_query = "update ".$wpdb->prefix."invoice_periods set projection_active='' where invoice_period < '$today'";
	$wpdb->query($close_projection_query);
	
	$close_invoicing_query = "update ".$wpdb->prefix."invoice_periods set invoice_active='' where invoice_period < '$last_month'";
	$wpdb->query($close_invoicing_query);
}
//End Period Management******************************************************************************************

$rightsquery = "select * from ".$wpdb->prefix."useradd where user_id = '$uid'";
$rightsresults = $wpdb->get_results($rightsquery);
$team = $rightsresults[0]->team;

$checklist = $_GET['ID'];

$queryedit = $wpdb->prepare("select * from ".$wpdb->prefix."projects 
	inner join ".$wpdb->prefix."clients on ".$wpdb->prefix."projects.client_id=".$wpdb->prefix."clients.client_id
	where ID=%d",$checklist);
$details = $wpdb->get_results($queryedit);

$gp_id = $details[0]->gp_id;
$client_name = $details[0]->client_name;
$prime_name = $details[0]->prime_name;
$project_name = $details[0]->project_name;
$sphere = $details[0]->sphere;
$project_manager = $details[0]->project_manager;	
$fee_type = $details[0]->fee_type;
$initiation_document = $details[0]->initiation_document;
$document_number = $details[0]->document_number;
$estimated_start = $details[0]->estimated_start;
$project_type = $details[0]->project_type;
$fee_amount = $details[0]->fee_amount;
$expense_amount = $details[0]->expense_amount;
$expense_type = $details[0]->expense_type;
$market = $details[0]->market;
$submarket = $details[0]->submarket;
$confidential = $details[0]->confidential;
$venues = $details[0]->venues;
$contact = $details[0]->contact;
$address = $details[0]->address;
$city = $details[0]->city;
$state = $details[0]->state;
$zip = $details[0]->zip;
$email = $details[0]->email;
$phone = $details[0]->phone;
$delivery_type = $details[0]->delivery_type;
$notes = $details[0]->notes;
$status = $details[0]->status;
$accounting_notes = $details[0]->accounting_notes;
$datemade = current_time('timestamp',0);
	
$project_team =array();
$queryteam = "select distinct user_id from ".$wpdb->prefix."project_user where project_id ='$checklist'";
$resultsteam = $wpdb->get_results($queryteam);
foreach ($resultsteam as $r){array_push($project_team,$r->user_id);}
	
if(isset($_POST['period-info']))
{
	$selected_period = $_POST['invoice_period'];
	$period_query = "select invoice_period from ".$wpdb->prefix."invoice_periods where invoice_period_id='$selected_period'";
	$period_results = $wpdb->get_results($period_query);
	$period_end = $period_results[0]->invoice_period;
	$period_start = strtotime(date('Y-m-01',$period_end));
}	

	
	
	
	//PROCESS INVOICE*********************************************************************************************
	if(isset($_POST['process-info']))
	{
	}			
	//SAVE INVOICE**********************************************************************************************
	elseif(isset($_POST['save-info']))
	{
		$invoice_number = "blah";//BillyB write code to assign invoice number
		$invoice_date = time();
		$invoice_period = trim($_POST['invoice_period']);
		$fee_amount = $_POST['fee_amount'];
		$expense_amount = "";
		$invoice_paid = 0;
		$invoice_comment = trim($_POST['invoice_comment']);
		$invoice_status = 0;
		
		//BillyB need to check for an invoice before populating if we're going to "save"	
		//$queryb = "insert into ".$wpdb->prefix."invoices (project_id,invoice_number,invoice_date,invoice_period,invoice_fee_amount,invoice_paid,invoice_comment)
		//	values ('$checklist','$invoice_number','$invoice_date','$invoice_period','$fee_amount','$invoice_paid','$invoice_comment')";
		//$wpdb->query($queryb);
		
		
	?>
		<div class="page_heading_me">
			<div class="page_heading_me_inner">
				<div class="main-pg-title">
					<div class="mm_inn"><?php echo $client_name." - ".$project_name;?> 
					</div>
				</div>
			</div>
		</div>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<?php echo "Thank you.  Your invoice has been saved.  You will still need to process before it is sent to the client.";?>
						<a href="<?php bloginfo('siteurl'); ?>/dashboard/"><?php echo "Return to your Dashboard";?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }
	else
	{
		?>
		<script language="javascript" type="text/javascript">
		function invoicing_totals(){
			var myForm = document.forms.new_inv;
			var fields = document.querySelectorAll("[name$='[bill]']");
			var details = document.querySelectorAll("[name$='[details]']");
			var to_bill_field = document.getElementById('to_bill_total');
			var no_bill_field = document.getElementById('no_bill_total');
			var on_invoice_field = document.getElementById('on_invoice_total');
			var already_billed_field = document.getElementById('already_billed_total');
			var do_nothing_field = document.getElementById('do_nothing_total');
			
			var to_bill_input = myForm.elements['to_bill_input'];
			var no_bill_input = myForm.elements['no_bill_input'];
			var on_invoice_input = myForm.elements['on_invoice_input'];
			var already_billed_input = myForm.elements['already_billed_input'];
			var do_nothing_input = myForm.elements['do_nothing_input'];
			
			var to_bill_total = 0;
			var no_bill_total = 0;
			var on_invoice_total = 0;
			var already_billed_total = 0;
			var do_nothing_total = 0;
			
			for(i=0;i<fields.length;i++){
				if(fields[i].checked==true){
					var a = Math.floor(i/5);
					var detailsArray = details[a].value.split(",,,");
					var amount = detailsArray[0]*1;
					
					if(fields[i].value=='bill'){
						to_bill_total += amount;
					}
					else if(fields[i].value=='no-bill'){
						no_bill_total += amount;
					}
					else if(fields[i].value=='on_invoice'){
						on_invoice_total += amount;
					}
					else if(fields[i].value=='billed'){
						already_billed_total += amount;
					}
					else{
						do_nothing_total += amount;
					}
					//alert('hi');
				}
			}
			//alert('hi');
			to_bill_input.value = to_bill_total.toFixed(2);
			if(to_bill_total!=0){to_bill_field.style.display="inline";}else{to_bill_field.style.display="none";}
			no_bill_input.value = no_bill_total.toFixed(2);if(no_bill_total!=0){no_bill_field.style.display="inline";}else{no_bill_field.style.display="none";}
			on_invoice_input.value = on_invoice_total.toFixed(2);if(on_invoice_total!=0){on_invoice_field.style.display="inline";}else{on_invoice_field.style.display="none";}
			already_billed_input.value = already_billed_total.toFixed(2);if(already_billed_total!=0){already_billed_field.style.display="inline";}else{already_billed_field.style.display="none";}
			do_nothing_input.value = do_nothing_total.toFixed(2);if(do_nothing_total!=0){do_nothing_field.style.display="inline";}else{do_nothing_field.style.display="none";}
			//alert('hi');
		}
		</script>
		<div class="page_heading_me">
			<div class="page_heading_me_inner">
				<div class="main-pg-title">
					<div class="mm_inn"><?php echo $client_name." - ".$project_name;?> 
					</div>
				</div>
			</div>
		</div>
		<div id="main_wrapper">
			<div id="main" class="wrapper">
			<form method="post" name="new_inv" enctype="multipart/form-data">		
                <div id="content">
					<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<li>
						<table><tr>
						<th><b><?php echo"Period:";?></b></th>
						<th><select class ="do_input_new" name="invoice_period">
						<?php
						$now = time();
						$periodquery = "select * from ".$wpdb->prefix.'invoice_periods where invoice_active ="on" order by invoice_period asc';
						$periodresults = $wpdb->get_results($periodquery);
						
						foreach ($periodresults as $row)
						{
							if(empty($selected_period) and $now < ($row->invoice_period + (86400 * 15))){$selected_period = $row->invoice_period_id;}
							echo '<option value="'.$row->invoice_period_id.'" '.($row->invoice_period_id==$selected_period ? "selected='selected'" : "" ).'>
								'.date('m-Y',$row->invoice_period).'</option>';
						}?>
						</select>
						</th>
						
						<th><input type="submit" name="period-info" class="my-buttons-submit" value="<?php echo "Set Period"; ?>" /></th>
						</tr></table></li>
						</ul>
						</div>
					</div>
				</div>
                <div id="content">
					<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<li><h3><?php echo "Hours for the period";?></h3>
						<style>input[type=number]{width:95px;}</style>
						<?php
						if(empty($period_start))
						{$period_end = $periodresults[0]->invoice_period; $period_start = strtotime(date('Y-m-01',$period_end));}
						$year = date('Y',$period_start);
						$total_time = 0;
						$total_value = 0;
						$t = -1;
						echo '<li><table width ="100%"><tr><th><u>Employee</u></th><th><u>Hours</u></th><th><u>Rate</u></th><th><u>Value</u></th>
							'. ($fee_type == "Fixed Fee" ? "" : ($fee_type == "Percent Complete" ? "" : "<th><u>Bill</u></th>")).'</tr>';
						
						foreach($project_team as $member)
						{
							$t++;
							$employee_time_query = $wpdb->prepare("select sum(timesheet_hours) as sum,display_name,planning_rate,user_role,rate from ".$wpdb->prefix."users
								inner join ".$wpdb->prefix."project_user on ".$wpdb->prefix."users.ID=".$wpdb->prefix."project_user.user_id
								inner join ".$wpdb->prefix."useradd on ".$wpdb->prefix."users.ID=".$wpdb->prefix."useradd.user_id
								inner join ".$wpdb->prefix."position_assumptions on ".$wpdb->prefix."useradd.position=".$wpdb->prefix."position_assumptions.position_id
								left join ".$wpdb->prefix."timesheets on ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."timesheets.user_id
									and timesheet_date<='$period_end' and timesheet_date>='$period_start' 
									and ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."timesheets.project_id
								left join ".$wpdb->prefix."project_rates on ".$wpdb->prefix."project_user.project_id=".$wpdb->prefix."project_rates.project_id
									and ".$wpdb->prefix."project_user.user_id=".$wpdb->prefix."project_rates.user_id
								where ".$wpdb->prefix."project_user.project_id=%d and ".$wpdb->prefix."users.ID=%d and year=%d
									and ".$wpdb->prefix."project_user.user_id=%d",$checklist,$member,$year,$member);
							$employee_time_results = $wpdb->get_results($employee_time_query);
							
							$employee_name = $employee_time_results[0]->display_name;
							$billing_rate = $employee_time_results[0]->planning_rate;
							if(!empty($employee_time_results[0]->rate)){$billing_rate = $employee_time_results[0]->rate;}
							
							$sum = $employee_time_results[0]->sum;
							if(empty($sum)){$hours = 0;}else{$hours = $sum;}
							$total_time += $sum;
							$value = $sum * $billing_rate;
							$display_value = number_format($sum * $billing_rate,2);
							$total_value += $value;
							
							echo '<tr><th>'.$employee_name.'</th><th><input type="number" name="record['.$t.'][hours]" value="'.$hours.'" step=".01" class="do_input_new" onblur="setValue'.$t.'()" /></th>
								<th>'.projecttheme_get_show_price($billing_rate).'</th>
								<th>$<input type="text" value="'.$value.'" name="record['.$t.'][value]" size="5"
									class="do_input_new" readonly /></th>
								'. ($fee_type == "Fixed Fee" ? "" : ($fee_type == "Percent Complete" ? "" : '
								<th><input type="checkbox" name="checkbox1" value="'.$value.'" onchange="checkTotal()" /></th>')).'</tr>';
							?>
							<script language="javascript" type="text/javascript">
							function setValue<?php echo $t;?>() {
								var hours = document.new_inv.record[<?php echo $t;?>][hours].value;
								var rate = document.new_inv.record[<?php echo $t;?>][rate].value;
								var newValue = ((hours*100) * (rate*100))/(100*100);
								document.new_inv.record[<?php echo $t;?>][value].value = newValue.toFixed(2);
							}
							</script>
							<?php
						}
						if($total_time == 0){$total_time = "0";}
						echo '<tr><th>&nbsp;</th></tr><tr><th><b>Total</b></th><th><b>'.$total_time.'</b></th><th>&nbsp;</th>
							<th><b>'.ProjectTheme_get_show_price($total_value).'</b></th></tr></table></li>';
						echo '<li>&nbsp;</li>';
						echo '<li><a href="/?p_action=detailed_project_hours&ID='.$period_end.'&project='.$checklist.'" class="nice_link">Get Detailed Hours</a></li>';
						echo '<li>&nbsp;</li>';
						
						
						?>
						
						</li>
						<li>
						<h3><?php echo"Fee Amount:";?></h3><p>$<input type="number" step=".01" min="0" name="fee_amount" class="do_input_new" /></p>
						</li>
						<script language="javascript" type="text/javascript">
							function checkTotal() {
								document.new_inv.fee_amount.value = '';
								var sum = 0;
								for (i=0;i<document.new_inv.checkbox1.length;i++) {
								  if (document.new_inv.checkbox1[i].checked) {
									sum = ((sum*100) + (eval(document.new_inv.checkbox1[i].value)*100))/100;
								  }
								}
								document.new_inv.fee_amount.value = sum.toFixed(2);
							}
							$(document).on("keypress", ":input:not(textarea)", function(event) {
								return event.keyCode != 13;
							});
						</script>
						<?php 
						echo '<li><b><u>Expenses:</u></b></li>';
						$employee_expense_query = $wpdb->prepare("select project_id,employee_id,".$wpdb->prefix."employee_expenses.expense_report_id,employee_expense_id,display_name,
							expense_type_id,billed_status,expense_code_name,expense_amount,expense_quantity,expense_billable,expense_date,expense_filename
							from ".$wpdb->prefix."employee_expenses 
							inner join ".$wpdb->prefix."users on ".$wpdb->prefix."employee_expenses.employee_id=".$wpdb->prefix."users.ID
							inner join ".$wpdb->prefix."expense_codes on ".$wpdb->prefix."employee_expenses.expense_type_id=".$wpdb->prefix."expense_codes.expense_code_id
							left join ".$wpdb->prefix."expense_backup on ".$wpdb->prefix."employee_expenses.employee_expense_id=".$wpdb->prefix."expense_backup.expense_id
							where expense_billable=1 and project_id=%d and employee_expense_status>0 and billed_status<3 and billed_status>=0 
							order by ".$wpdb->prefix."employee_expenses.expense_report_id",$checklist);
						$employee_expense_results = $wpdb->get_results($employee_expense_query);
						
						$vendor_expenses = $wpdb->get_results($wpdb->prepare("select vendor_name,vendor_payable_id,expense_date,v_exp_name,vendor_fee,vendor_expense,billed_status,
							expense_billable,fee_billable
							from ".$wpdb->prefix."vendor_payables 
							inner join ".$wpdb->prefix."vendors on ".$wpdb->prefix."vendor_payables.vendor_id=".$wpdb->prefix."vendors.vendor_id
							inner join ".$wpdb->prefix."vendor_expense_codes on ".$wpdb->prefix."vendor_payables.expense_type_id=".$wpdb->prefix."vendor_expense_codes.vendor_exp_code_id
							where billed_status<3 and (expense_billable=1 or fee_billable=1) and expense_status>1 and project_id=%d",$checklist));
						
						$reports_array = array();//start array to check for unique reports
						
						$to_bill_total = 0;
						$no_bill_total = 0;
						$on_invoice_total = 0;
						$already_billed_total = 0;
						$do_nothing_total = 0;
						
						if(count($employee_expense_results) > 0)
						{
							echo '<li><table width ="100%">
								<tr>
								<th><b><u>Employee</u></b></th>
								<th><b><u>Date</u></b></th>
								<th><b><u>Expense</u></b></th>
								<th><b><u>Amount</u></b></th>
								<th><b><u>Bill</u></b></th>
								<th><b><u>Move to No-Bill</u></b></th>
								'.(($uid == 94 or $uid == 11 or $uid==235) ? '<th><b><u>On Invoice</u></b></th>' : '' ).'
								'.(($uid == 94 or $uid == 11 or $uid==235) ? '<th><b><u>Already Billed</u></b></th>' : '' ).'
								<th><b><u>Do Nothing</u></b></th>
								</tr>';
							$sum =0;
							$expense_ids = array();
							foreach($employee_expense_results as $employee_expense)
							{
								if($employee_expense->project_id===$checklist and !in_array($employee_expense->employee_expense_id,$expense_ids))
								{
									$employee_id = $employee_expense->employee_id;
									$report_id = $employee_expense->expense_report_id;
									$expense_id = $employee_expense->employee_expense_id;
									$employee_name = $employee_expense->display_name;
									$expense_code_id = $employee_expense->expense_type_id;
									$billed_status = $employee_expense->billed_status;
									$expense_name = $employee_expense->expense_code_name;
									$total = $employee_expense->expense_amount * $employee_expense->expense_quantity;
									$backup = $employee_expense->expense_filename;
									
									array_push($expense_ids,$expense_id);
									
									if($billed_status==0){
										$do_nothing_total += $total;
									}
									elseif($billed_status==1){
										$to_bill_total += $total;
									}
									elseif($billed_status==2){
										$on_invoice_total += $total;
									}
									
									if(!in_array($employee_expense->expense_report_id,$reports_array))
									{
										$report = '<strong><u><a href="'.get_bloginfo('siteurl').'/?p_action=employee_expense_view&ID='.$employee_expense->expense_report_id.'
											'.($employee_expense->expense_billable==1 ? '-'.$checklist.'-1"' : '"' ).' 
											target="_blank">'.$expense_name.'</a></u></strong>';
										array_push($reports_array,$employee_expense->expense_report_id);
									}
									else
									{
										$report = $expense_name;
									}
									
									echo '<tr><td>'.$employee_name.'</td><td>'.date('m-d',$employee_expense->expense_date).'</td>
										<td>'.$report.'</td>
										<td style="text-align:center;">'.(empty($backup) ? '$'.number_format($total,2) : '<strong><a href="/wp-content/expense_backup/'.rawurlencode($backup).'" target="_blank">$'.number_format($total,2)).'</a></strong>
										<input type="hidden" name="expense['.$employee_expense->employee_expense_id.'][details]" 
											value="'.$total.',,,'.$expense_id.',,,'.$report_id.',,,'.$billed_status.'" /></td>
										<td style="text-align:center;"><input type="radio" name="expense['.$employee_expense->employee_expense_id.'][bill]" value="bill" '.($billed_status==1 ? "checked='checked'" : "").' onclick="invoicing_totals();"/></td>
										<td style="text-align:center;"><input type="radio" name="expense['.$employee_expense->employee_expense_id.'][bill]" value="no-bill" onclick="invoicing_totals();"/></td>
										'.(($uid == 94 or $uid == 11 or $uid==235) ? '
										<td style="text-align:center;"><input type="radio" name="expense['.$employee_expense->employee_expense_id.'][bill]" value="on_invoice" '.($billed_status==2 ? "checked='checked'" : "").' onclick="invoicing_totals();"/></td>' : '' ).'
										'.(($uid == 94 or $uid == 11 or $uid==235) ? '
										<td style="text-align:center;"><input type="radio" name="expense['.$employee_expense->employee_expense_id.'][bill]" value="billed" onclick="invoicing_totals();"/></td>' : '' ).'
										<td style="text-align:center;"><input type="radio" name="expense['.$employee_expense->employee_expense_id.'][bill]" value="nothing" '.($billed_status==0 ? "checked='checked'" : "").' onclick="invoicing_totals();"/></td></tr>';
									$sum += $total;
								}
							}
							echo '<tr><td>&nbsp;</td></tr><tr><td><b>Total</b></td><td>&nbsp;</td><td>&nbsp;</td><td><b>$'.number_format($sum,2).'</b></td></tr></table></li>';
							echo '<li>&nbsp;</li>';
						}
						if(!empty($vendor_expenses))
						{
							echo '<li><table width="100%">
								<tr>
								<th><b><u>Vendor</u></b></th>
								<th><b><u>Date</u></b></th>
								<th><b><u>Expense</u></b></th>
								<th><b><u>Amount</u></b></th>
								<th style="text-align:center;"><b><u>Bill</u></b></th>
								<th style="text-align:center;"><b><u>Move to No-Bill</u></b></th>
								'.(($uid == 94 or $uid == 11 or $uid==235) ? '<th><b><u>On Invoice</u></b></th>' : '' ).'
								'.(($uid == 94 or $uid == 11 or $uid==235) ? '<th><b><u>Already Billed</u></b></th>' : '' ).'
								<th style="text-align:center;"><b><u>Do Nothing</u></b></th>
								</tr>';
							$vendor_total = 0;
							foreach($vendor_expenses as $v)
							{
								$total = 0;
								if($v->fee_billable == 1){$total += $v->vendor_fee;}
								if($v->expense_billable == 1){$total += $v->vendor_expense;}
								if($total!=0)
								{
									echo '<tr>
										<input type="hidden" name="vendor['.$v->vendor_payable_id.'][details]" value="'.$total.',,,'.$v->vendor_payable_id.'" />
										<td>'.$v->vendor_name.'</td>
										<td>'.date('m-d',$v->expense_date).'</td>
										<td><b><u><a href="'.get_bloginfo('siteurl').'/?p_action=edit_vendor_payable&ID='.$v->vendor_payable_id.'">'.$v->v_exp_name.'</a></u></b></td>
										<td>$'.number_format($total,2).'</td>
										<td style="text-align:center;"><input type="radio" name="vendor['.$v->vendor_payable_id.'][bill]" value="bill"
											'.($v->billed_status==1 ? "checked='checked'" : "").' onclick="invoicing_totals();"/></td>
										<td style="text-align:center;"><input type="radio" name="vendor['.$v->vendor_payable_id.'][bill]" value="no-bill" onclick="invoicing_totals();"/></td>
										'.(($uid == 94 or $uid == 11 or $uid==235) ? '
										<td style="text-align:center;"><input type="radio" name="vendor['.$v->vendor_payable_id.'][bill]" value="on_invoice" 
											'.($v->billed_status==2 ? "checked='checked'" : "").' onclick="invoicing_totals();"/></td>' : '' ).'
										'.(($uid == 94 or $uid == 11 or $uid==235) ? '
										<td style="text-align:center;"><input type="radio" name="vendor['.$v->vendor_payable_id.'][bill]" value="billed" onclick="invoicing_totals();"/></td>' : '' ).'
										<td style="text-align:center;"><input type="radio" name="vendor['.$v->vendor_payable_id.'][bill]" value="nothing" onclick="invoicing_totals();"/></td>
										</tr>';
									$vendor_total += ($v->vendor_fee + $v->vendor_expense);
									
									if($v->billed_status==0){
										$do_nothing_total += $total;
									}
									elseif($v->billed_status==1){
										$to_bill_total += $total;
									}
									elseif($v->billed_status==2){
										$on_invoice_total += $total;
									}
								}
							}
							echo '<tr><td>&nbsp;</td></tr>';
							echo '<tr><td><b>Total</b></td><td>&nbsp;</td><td>&nbsp;</td><td><b>$'.number_format($vendor_total,2).'</b></td></tr>';
							echo '</table></li><li>&nbsp;</li>';
							
							echo '<li id="to_bill_total" '.(empty($to_bill_total) ? 'style="display:none;"' : '').'><h3>To Bill:</h3><p><input type="number" name="to_bill_input" readonly value="'.number_format($to_bill_total,2,'.','').'" /></p></li>';
							echo '<li id="no_bill_total" '.(empty($no_bill_total) ? 'style="display:none;"' : '').'><h3>Move to No-Bill:</h3><p><input type="number" name="no_bill_input" readonly value="'.number_format($no_bill_total,2,'.','').'" /></p></li>';
							echo '<li id="on_invoice_total" '.(empty($on_invoice_total) ? 'style="display:none;"' : '').'><h3>On Invoice:</h3><p><input type="number" name="on_invoice_input" readonly value="'.number_format($on_invoice_total,2,'.','').'" /></p></li>';
							echo '<li id="already_billed_total" '.(empty($already_billed_total) ? 'style="display:none;"' : '').'><h3>Already Billed:</h3><p><input type="number" name="already_billed_input" readonly value="'.number_format($already_billed_total,2,'.','').'" /></p></li>';
							echo '<li id="do_nothing_total" '.(empty($do_nothing_total) ? 'style="display:none;"' : '').'><h3>Do Nothing:</h3><p><input type="number" name="do_nothing_input" readonly value="'.number_format($do_nothing_total,2,'.','').'" /></p></li>';
							
							if($uid==11 or $uid==94 or $uid==235)
							{
								echo '<li><a href="/?p_action=expense_invoice_summary&ID='.$checklist.'" class="nice_link" >Expense Invoice Summary</a></li>';
								echo '<li>&nbsp;</li>';
							}
						}
						elseif(!empty($employee_expense_results))
						{
							echo '<li id="to_bill_total" '.(empty($to_bill_total) ? 'style="display:none;"' : '').'><h3>To Bill:</h3><p><input type="number" name="to_bill_input" readonly value="'.number_format($to_bill_total,2,'.','').'" /></p></li>';
							echo '<li id="no_bill_total" '.(empty($no_bill_total) ? 'style="display:none;"' : '').'><h3>Move to No-Bill:</h3><p><input type="number" name="no_bill_input" readonly value="'.number_format($no_bill_total,2,'.','').'" /></p></li>';
							echo '<li id="on_invoice_total" '.(empty($on_invoice_total) ? 'style="display:none;"' : '').'><h3>On Invoice:</h3><p><input type="number" name="on_invoice_input" readonly value="'.number_format($on_invoice_total,2,'.','').'" /></p></li>';
							echo '<li id="already_billed_total" '.(empty($already_billed_total) ? 'style="display:none;"' : '').'><h3>Already Billed:</h3><p><input type="number" name="already_billed_input" readonly value="'.number_format($already_billed_total,2,'.','').'" /></p></li>';
							echo '<li id="do_nothing_total" '.(empty($do_nothing_total) ? 'style="display:none;"' : '').'><h3>Do Nothing:</h3><p><input type="number" name="do_nothing_input" readonly value="'.number_format($do_nothing_total,2,'.','').'" /></p></li>';
							
							
							if($uid==11 or $uid==94 or $uid==235)
							{
								echo '<li><a href="/?p_action=expense_invoice_summary&ID='.$checklist.'" class="nice_link" >Expense Invoice Summary</a></li>';
								echo '<li>&nbsp;</li>';
							}
						}
						echo '<li><h3>Comments to Client</h3><p><input type="text" name="invoice_comment" class="do_input_new full_wdth_me"/></p></li>';
						echo '<li><h3>Internal Notes</h3><p><input type="text" name="internal_notes" class="do_input_new full_wdth_me"/></p></li>';
						
						?>
						</ul>
						</div>
					</div>
				</div>
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Invoice Summary";?></h3>
					<ul class="xoxo">
						<li class="widget-container widget_text" id="ad-other-details">
							<ul class="other-dets other-dets2">
							<?php
								$invoicesummaryquery = "select sum(invoice_fee_amount)+sum(invoice_expense_amount) as sum_amount from ".$wpdb->prefix."invoices where project_id='$checklist'";
								$invoicesummaryresult = $wpdb->get_results($invoicesummaryquery);
								if(empty($invoicesummaryresult)){echo "<li>No Invoices submitted yet</li>";}
								else{
									$paidquery = "select sum(invoice_fee_amount)+sum(invoice_expense_amount) as sum_amount from ".$wpdb->prefix."invoices where project_id='$checklist' and invoice_paid=1";
									$paidresult = $wpdb->get_results($paidquery);
									echo '<li><h3>Amount Billed</h3><p>$'.number_format($invoicesummaryresult[0]->sum_amount,2).'</p></li>';
									echo '<li><h3>Amount Paid</h3><p>$'.number_format($paidresult[0]->sum_amount,2).'</p></li>';
									echo '<li><h3>Total Outstanding</h3><p>$'.number_format($invoicesummaryresult[0]->sum_amount-$paidresult[0]->sum_amount,2).'</p></li>';
								}
							?>
							</ul>
						</li>
					</ul>
				</div></div>					
				<div id="right-sidebar" class="page-sidebar"><div class="padd10"><h3><?php echo "Invoice History";?></h3>
					F = Fee Invoice<br/>
					E = Expense Invoice<br/>
					<ul class="xoxo">
						
						<li class="widget-container widget_text" id="ad-other-details">
							
							<ul class="other-dets other-dets2">
							
							<?php
							$invoicehistoryquery = "select * from ".$wpdb->prefix."invoices where project_id='$checklist' order by invoice_period desc,invoice_fee_amount desc";
							$invoicehistoryresult = $wpdb->get_results($invoicehistoryquery);
							if(empty($invoicehistoryresult)){echo "No Invoices submitted yet";}
							else
							{	
								echo '<table width="100%"><tr><th><b><u>Period</u></b></th><th><b><u>Amount</u></b></th></tr>';
								foreach ($invoicehistoryresult as $invoice)
								{
									$invoice_period = $invoice->invoice_period;
									$amount = $invoice->invoice_fee_amount; $suffix = '<strong> (F)</strong>';
									if($amount == 0){$amount = $invoice->invoice_expense_amount; $suffix = '<strong> (E)</strong>';}
									if($invoice->invoice_paid == 1){$paid = "Paid";}else{$paid = "Unpaid";}
									
									echo '<th>'.date('m-Y',$invoice_period).'</th><th>$'.number_format($amount,2).$suffix.'</th></tr>';
								}
								echo '</table>';
							}
							?>
							</ul>
						</li>
					</ul>
				</div></div>
				<div id="content"><h2><?php echo "Project Summary";?></h2>
					<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
							<li><h3><?php echo "Project ID:";?></h3><p><?php echo $gp_id;?></p></li>
							<li><h3><?php echo "Client Name:";?></h3><p><?php echo $client_name;?></p></li>
							<li><h3><?php echo "Prime Name (if sub):";?></h3><p><?php echo $prime_name;?></p></li>
							<li><h3><?php echo "Project Name"; ?>:</h3><p><?php echo $project_name;?></p></li>
							<li><h3><?php echo "Project Manager"; ?>:</h3>
							<p><?php 
								$pmquery = "select display_name from ".$wpdb->prefix."users where ID='$project_manager'";
								$pmresult = $wpdb->get_results($pmquery);
								echo $pmresult[0]->display_name;?>
							</p></li>						
							<li><h3><?php echo "Team Members"; ?>:</h3>
					        	<p><table width="100%">
								<?php
								echo '<tr><th>&nbsp;</th><th><b><u>Total Hours Worked</u></b></th><th><b><u>Total Value of Hours Worked</u></b></th></tr>';
								
								$totalhours =0;
								$totalvalue =0;
								foreach($project_team as $user)
								{
									$namequery = "select display_name from ".$wpdb->prefix."users where ID='$user'";
									$nameresult = $wpdb->get_results($namequery);
									$hoursquery = "select sum(timesheet_hours) as sum_amount from ".$wpdb->prefix."timesheets where user_id='$user' and project_id='$checklist'";
									$hoursresult = $wpdb->get_results($hoursquery);
									$hours = $hoursresult[0]->sum_amount;
									$ratequery = "select planning_rate from ".$wpdb->prefix."position_assumptions where ID=(select position from ".$wpdb->prefix."useradd where user_id='$user')";
									$rateresult = $wpdb->get_results($ratequery);
									$value = $hours * $rateresult[0]->planning_rate;
									
									$totalhours += $hours;
									$totalvalue += $value;
									
									echo "<tr><th>".$nameresult[0]->display_name."</th><th>".$hours."</th><th>".ProjectTheme_get_show_price($value)."</th></tr>";	
								}									
								?>
								<tr><th>&nbsp;</th><th><?php echo "_______";?></th><th><?php echo "_______";?></th></tr>
								<tr><th><?php echo "Total";?></th><th><?php echo $totalhours;?></th><th><?php echo ProjectTheme_get_show_price($totalvalue);?></th></tr>
								</table></p>
							</li>
							<li><h3><?php echo "Financial Status";?>:</h3><p>
								<table width="100%"><tr><th>&nbsp;</th><th><?php echo "Billed to-date";?></th><th><?php echo "Remaining";?></th></tr>
								
									<tr><th><?php echo "Fees";?></th><th><?php 
									$feesquery = "select sum(invoice_fee_amount) as sum from ".$wpdb->prefix."invoices where project_id='$checklist' and invoice_fee_amount>0";
									$feesresult = $wpdb->get_results($feesquery);
									echo ProjectTheme_get_show_price($feesresult[0]->sum);									
									?></th><th><?php echo ProjectTheme_get_show_price($fee_amount-$feesresult[0]->sum);?></th></tr>
									<tr><th><?php echo "Expenses";?></th><th><?php
									$expensesquery = "select sum(invoice_expense_amount) as sum from ".$wpdb->prefix."invoices where project_id='$checklist' and invoice_expense_amount>0";
									$expensesresult = $wpdb->get_results($expensesquery);
									echo ProjectTheme_get_show_price($expensesresult[0]->sum);									
									?></th><th><?php echo ProjectTheme_get_show_price($expense_amount-$expensesresult[0]->sum);?></th></tr>
								</table>	
							</li>
						</ul>
						</div>
					</div>
				</div>
				<div id="content">
					<div class="my_box3">
						<div class="padd10">
						<ul class="other-dets_m">
						<li>
							<?php 
							if($uid==11)
							{
								echo '<input type="submit" name="process-info" class="my-buttons" value="Process Invoice" />
								&nbsp;';
							}
							if($uid == 11 or $uid==94 or $uid==235)
							{
								echo '<input type="submit" name="save-info" class="my-buttons" value="Save" />
								&nbsp;';
								echo '<input type="submit" name="print-info" class="my-buttons" value="Print Backup" />
								&nbsp;';
							}
							?>
							<a href="/?p_action=project_card&ID=<?php echo $checklist;?>" class="my-buttons" style="color:#ffffff;" ><?php echo "Return to Project"; ?></a>			
						</li>
						</ul>
						</div>
					</div>
				</div>
			</form>
			</div>
		</div>
	<?php } 
	get_footer();?>