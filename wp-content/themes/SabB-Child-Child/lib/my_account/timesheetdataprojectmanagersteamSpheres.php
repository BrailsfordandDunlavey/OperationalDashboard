<?php
/***************************************************************************
*
*	ProjectTheme - copyright (c) - sitemile.com
*	The only project theme for wordpress on the world wide web.
*
*	Coder: Andrei Dragos Saioc
*	Email: sitemile[at]sitemile.com | andreisaioc[at]gmail.com
*	More info about the theme here: http://sitemile.com/products/wordpress-project-freelancer-theme/
*	since v1.2.5.3
*
***************************************************************************/
//echo "hello world";

//$current_user_id_members = get_current_user_id();

//echo $current_user_id_members;' OR ud.sphere LIKE '%Functional%' OR ud.sphere LIKE '%KMV%'
global $wpdb;
global $first_arr;
$first_arr = array();


$table = 'timesheets';
$client_table = 'projects';
$table_name = $wpdb->prefix . $table;
$client_table_name = $wpdb->prefix . $client_table; 
$result_spheres = $wpdb->get_results("SELECT * FROM $table_name INNER JOIN $client_table_name ON ID = project_id
WHERE timesheet_date BETWEEN UNIX_TIMESTAMP('2020-01-01') AND UNIX_TIMESTAMP('2020-04-01')");

$result_nonproject_spheres = $wpdb->get_results(
			"SELECT * FROM $table_name
				WHERE timesheet_date BETWEEN UNIX_TIMESTAMP('2020-01-01') AND UNIX_TIMESTAMP('2020-04-01') AND (project_id LIKE '%Vacation%'
				OR project_id LIKE '%Holiday%'						 				
				OR project_id LIKE '%HR%'
				OR project_id LIKE '0001'
				OR project_id LIKE 'Sick'
				OR project_id LIKE '0001MK'
				OR project_id LIKE 'BEREAV')");
//print_r($result_spheres);
//$array_spheres = json_decode(json_encode($result_spheres), true);

//$array = json_decode(json_encode($result), true);		
//$array_nonprojects = json_decode(json_encode($result_nonproject),$result_nonproject true JSON_HEX_QUOT | JSON_HEX_TAG);

/*	
$arr_output = array();
//print_r($array_nonprojects);
foreach($result_spheres as $key=>$arr){
	$timesheet_date = $arr->timesheet_date;
	$date = date('Y-m-d', $timesheet_date);
	$arr_dates = explode("-", $date);
	$year = $arr_dates[0];
	$month = $arr_dates[1];	   
	$arr_output[$year][$month][] = $arr;
}

//print_r($arr_output['2020']['01']['0']);
/////Non-Project Hours/////////////
//global $arr_output_nonprojects; 
$arr_output_nonprojects = array();
foreach($result_nonproject_spheres as $key_nonprojects=>$arr_nonprojects){
	$timesheet_date_nonprojects = $arr_nonprojects->timesheet_date;
	$date_nonprojects = date('Y-m-d', $timesheet_date_nonprojects);
	$arr_dates_nonprojects = explode("-", $date_nonprojects);
	$year_nonprojects = $arr_dates_nonprojects[0];
	$month_nonprojects = $arr_dates_nonprojects[1];	   
	$arr_output_nonprojects[$year_nonprojects][$month_nonprojects][] = $arr_nonprojects;
}


$expected_hours_pertitle_array = array(1 => 67, 2 => 67, 3 => 67, 4 => 67, 5 => 67, 6 => 79, 7 => 87, 8 => 87, 9 => 0, 10 => 0, 11 => 67, 12 => 0, 13 => 0, 14 => 79, 15 => 79, 16 => 87, 17 => 87, 18 => 67, 19 => 67, 20 => 67, 21 => 67, 22 => 67);
$c = 90;
$s = 0;

echo "<table style=\"table-layout: fixed; width: 100%\"><tr>";
$spheres_arr = array("Functional", "Higher Ed", "KMV");


foreach($spheres_arr as $sphk => $sphv){
$team_management_position = array();
/	$table_emp_sup = 'useradd';
		$client_table_emp = 'users';
		$table_name_emp_sup = $wpdb->prefix . $table_emp_sup;
		$client_table_name_emp = $wpdb->prefix . $client_table_emp; 
		$result_emp_sup = $wpdb->get_results(
			"SELECT * FROM $table_name_emp_sup ud			 		    
				WHERE ud.sphere LIKE '%$sphv%'");		
	
	foreach($result_emp_sup as $keyspheres => $vals_spheres){	
		//$team_management_spheres[$vals_spheres->sphere][] = $vals_spheres->user_id;
		$team_management_position[] = $vals_spheres->position;
	}
	//echo "<pre>";
	// print_r($team_management_spheres);
	// echo "</pre>";
	foreach($team_management_spheres as $key => $vals){
		// = array();user_id, sphere, position
		
		$total_numbers_spheres = total_number_hrs_sphere($vals, $arr_output, $arr_output_nonprojects);
		$workingDaysInaMonthmembers = $total_numbers_spheres[2];
		//print_r($total_numbers_spheres);
		//echo "<br />";
		//echo "<br />";
		$user_id = $vals->user_id;
		$total_expect_count = count($vals);
			
		$workingDaysInaMonthhours = $workingDaysInaMonthmembers[0]/$total_expect_count;
		$workingDaysInaMonthhoursone = $workingDaysInaMonthmembers[1]/$total_expect_count;
		$workingDaysInaMonthhourstwo = $workingDaysInaMonthmembers[2]/$total_expect_count;
		//echo $workingDaysInaMonthhoursone;	
		$expectedhour_monthone_sphere = array();
		$expectedhour_monthone_sphereone = array();
		$expectedhour_monthone_spheretwo = array();
		$added_expected = array();
		$added_expectedone = array();
		$added_expectedtwo = array();
		foreach($team_management_position as $key_idExpect => $val_idExpect){			
			foreach($expected_hours_pertitle_array as $key_expectedHrsPercent => $val_expectedHrsPercent){
				if($val_idExpect == $key_expectedHrsPercent){					
					$expectedhour_monthone_sphere[] = ($val_expectedHrsPercent/100)*$workingDaysInaMonthhours;
					$expectedhour_monthone_sphereone[] = ($val_expectedHrsPercent/100)*$workingDaysInaMonthhoursone;
					$expectedhour_monthone_spheretwo[] = ($val_expectedHrsPercent/100)*$workingDaysInaMonthhourstwo;
				
				}
			}
		}	
					$added_expected[] = array_sum($expectedhour_monthone_sphere);
					$added_expectedone[] = array_sum($expectedhour_monthone_sphereone);
					$added_expectedtwo[] = array_sum($expectedhour_monthone_spheretwo);	
		
		$total_expect_hrs_project = array($added_expected, $added_expectedone, $added_expectedtwo);
		$s++;
		
	
			$first_month = 01;
			if(!empty($total_numbers_spheres[0])){
				$expolode_sum_total_num = explode("/", $total_numbers_spheres[0]);				
				foreach($expolode_sum_total_num as $key_explode => $val_explode){
							
					if($val_explode){						
						$first_arr[$key_explode] = $val_explode;					
					}						
				}			
				$the_sum_total_zero = $first_arr[0];
				$the_sum_total_one = $first_arr[1];
				$the_sum_total_two = $first_arr[2];
			}			
			if(!empty($total_numbers_spheres[1])){
				$expolode_sum_total_numnon = explode("/", $total_numbers_spheres[1]);		
				foreach($expolode_sum_total_numnon as $key_explodenon => $val_explodenon){						
					if($val_explodenon){					
						$first_arrnon[$key_explodenon] = $val_explodenon;					
					}								
				}			
				$the_sum_total_zeronon = $first_arrnon[0];
				$the_sum_total_onenon = $first_arrnon[1];
				$the_sum_total_twonon = $first_arrnon[2];
			}			
			$second_month = $first_month + 01;
			$third_month = $first_month + 02;
			$monthName = "2020-".$first_month."-01";
			$monthNameone = "2020-0".$second_month."-01";
			$monthNametwo = "2020-0".$third_month."-01";
			$projectTimeTotalarrayPieChart = array("date" => $monthName, "value1" => $the_sum_total_zero, "value2" => $the_sum_total_zeronon, "value3" => $workingDaysInaMonthmembers[0], "value4" => $total_expect_hrs_project[0][0]); 

			$projectTimeTotalarrayPieChartmonthone = array("date" => $monthNameone, "value1" => $the_sum_total_one, "value2" => $the_sum_total_onenon, "value3" => $workingDaysInaMonthmembers[1], "value4" => $total_expect_hrs_project[1][0]); 

			$projectTimeTotalarrayPieChartmonthtwo = array("date" => $monthNametwo, "value1" => $the_sum_total_two, "value2" => $the_sum_total_twonon, "value3" => $workingDaysInaMonthmembers[2], "value4" => $total_expect_hrs_project[2][0]); 

			$mergednonprojectTimeTotalarrayPieChart_encode = json_encode($projectTimeTotalarrayPieChart);			
			$mergednonprojectTimeTotalarrayPieChart_encodeone = json_encode($projectTimeTotalarrayPieChartmonthone);			
			$mergednonprojectTimeTotalarrayPieChart_encodetwo = json_encode($projectTimeTotalarrayPieChartmonthtwo);			

			$charpiearrsp = $mergednonprojectTimeTotalarrayPieChart_encode.",".$mergednonprojectTimeTotalarrayPieChart_encodeone.",".$mergednonprojectTimeTotalarrayPieChart_encodetwo;
			$chartdivv = 'chartdivv'.$c;
			
	    if($c == 90 OR $c == 91 OR $c == 92){
		echo "<td><hr>";
		print_r("<a href=/wp-opdash/time-analysis-hours-worked-per-project?user_id=$spherevals>".$key."</a>");
		mySpheresChart($charpiearrsp, $chartdivv);			 
		echo "</td>";
		}
		$c++;
	}		
}
echo "</tr></table>";	
///////////////////////Function to collect the number of hours/////////////////////////////////////
function total_number_hrs_sphere($user_id, $arr_output, $arr_output_nonprojects){
	 
	$s = 0;
	if($_POST['date']){
		$month_input = explode("/", $_POST['date']);
	}else{
		$month_input = explode("/", "01/01/2020");
	}
	$first_m =  $month_input[0];
	$second_m = $month_input[0] + 1;
	$third_m =  $month_input[0] + 2;
	$years = array('2020');

	///////////Create the total expected hour line/////////////////////////////////////////
	// best stored as array, so you can add more than one
	global $holidays;
	global $workingDaysInaMonthmembers;
	$holidays = array('2020-01-01', '2020-01-20', '2020-02-17', '2020-05-25', '2020-07-03', '2020-09-07', '2020-11-26', '2020-11-27', '2020-12-24', '2020-12-25', '2020-12-31');
	$workingDaysInaMonthmembers = array();
	$user_id_count_arr = count($user_id);
	//print_r($user_id_count_arr);	
	for($et=0; $et<=2; $et++){
			$months_project_totalhours = $month_input[0] + $et;
			//'0'.$months_project_totalhours;
			
			$num_of_days = cal_days_in_month(CAL_GREGORIAN, $months_project_totalhours, $month_input['2']);
			$start = new DateTime($month_input['2'].'-'.$months_project_totalhours.'-01');
			$end = new DateTime($month_input['2'].'-'.$months_project_totalhours.'-'.$num_of_days);
			// otherwise the  end date is excluded (bug?)
			$end->modify('+1 day');
			$interval = $end->diff($start);
			// total days
			$days = $interval->days;

			// create an iterateable period of date (P1D equates to 1 day)
			$period = new DatePeriod($start, new DateInterval('P1D'), $end);

			foreach($period as $dt) {
				$curr = $dt->format('D');

				// for the updated question
				if (in_array($dt->format('Y-m-d'), $holidays)) {
					$days--;
				}

				// substract if Saturday or Sunday
				if ($curr == 'Sat' || $curr == 'Sun') {
					$days--;
				}
			}	
			//echo $user_id_count_arr;	
			$workingDaysInaMonthmembers[] = ($days*8.5)*$user_id_count_arr;
			}
			$user_id_count_arr ="";
//print_r($workingDaysInaMonthmembers);
	$d=0;
	//print_r($arr_output[2020]);
	foreach($years as $val){
		//$memberteam_sum = 0;
		foreach($arr_output[$val] as $dates){
			//print_r($arr_output['2020']['02']['0']);
			//print_r($dates[0]['timesheet_hours'] );
			$sum_totalsp = 0;
			$non_project_sumtotal = 0;
			$exp_months = explode('-', date('Y-m-d' , $dates[0]->timesheet_date));
			$months = $exp_months[1];
			
			if($months == $first_m OR $months == $second_m OR $months == $third_m){			
				foreach($user_id as $keyusID => $valusID){
					foreach($dates as $hours){					
						if($d == 0 OR $d == 1 OR $d == 2){

							if($valusID == $hours->user_id){
								
								$sum_totalsp = $hours->timesheet_hours + $sum_totalsp;
								$ud++;
							}														
						}
					}						
					$non_project_count = count($arr_output_nonprojects[$val][$months]);
					for($l=0; $l<$non_project_count; $l++){
						$hournonproject = $arr_output_nonprojects[$val][$months][$l]->timesheet_hours;
								//echo $hournonproject;
						$exploded_hournonproject = explode("-", $hournonproject);
								//print_r($exploded_hournonproject);
						$count_exploded_hournonproject_arr = count($exploded_hournonproject);							
						if($count_exploded_hournonproject_arr == 1){		
							if($d == 0 OR $d == 1 OR $d == 2){
								if($valusID == $arr_output_nonprojects[$val][$months][$l]->user_id){
									$non_project_sumtotal = $arr_output_nonprojects[$val][$months][$l]->timesheet_hours + $non_project_sumtotal;
								}
							}		 
									
						}
					}
				}
				$d++;
				$memberteam_sum .= $sum_totalsp ."/";	
				$memberteam_sum_non .= $non_project_sumtotal ."/";		
			
			}
		}
	}	
	return array($memberteam_sum, $memberteam_sum_non, $workingDaysInaMonthmembers);
}
function mySpheresChart($charpiearr, $chartdivv){
	?>	
	<!-- Styles -->
	<style>
	<?php echo "#".$chartdivv; ?> {
	  width: 100%;
	  height: 500px;
	}
	
	</style>
	
	<!-- Resources -->
	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
	
	<!-- Chart code -->
	<script>
	am4core.ready(function() {
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end
	
	// Create chart instance
	var chart = am4core.create(<?php echo $chartdivv; ?>, am4charts.XYChart);
	
	chart.colors.step = 2;
	chart.maskBullets = false;
	
	// Add data
	chart.data = [
			<?php echo $charpiearr; ?>	
	];
	
	// Create axes
	var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
	dateAxis.renderer.grid.template.location = 0;
	dateAxis.renderer.minGridDistance = 50;
	dateAxis.renderer.grid.template.disabled = true;
	dateAxis.renderer.fullWidthTooltip = true;
	//dateAxis.title.text = "Months for Year: "+<?php echo $year; ?>;
	
	var distanceAxis = chart.yAxes.push(new am4charts.ValueAxis());
	distanceAxis.title.text = "Hours";
	//distanceAxis.renderer.grid.template.disabled = true;
	
	//var durationAxis = chart.yAxes.push(new am4charts.DurationAxis());
	//durationAxis.title.text = "Duration";
	//durationAxis.baseUnit = "minute";
	//durationAxis.renderer.grid.template.disabled = true;
	//durationAxis.renderer.opposite = true;
	//durationAxis.syncWithAxis = distanceAxis;
	
	//durationAxis.durationFormatter.durationFormat = "hh'h' mm'min'";
	
	//var latitudeAxis = chart.yAxes.push(new am4charts.ValueAxis());
	//latitudeAxis.renderer.grid.template.disabled = true;
	//latitudeAxis.renderer.labels.template.disabled = true;
	//latitudeAxis.syncWithAxis = distanceAxis;
	
	// Create series
	var distanceSeries = chart.series.push(new am4charts.ColumnSeries());
	distanceSeries.dataFields.valueY = "value1";
	distanceSeries.dataFields.dateX = "date";
	distanceSeries.yAxis = distanceAxis;
	distanceSeries.tooltipText = "Project Hrs: {valueY} ";
	distanceSeries.name = "Project Hrs";
	distanceSeries.columns.template.fillOpacity = 0.7;
	distanceSeries.columns.template.propertyFields.strokeDasharray = "dashLength";
	distanceSeries.columns.template.propertyFields.fillOpacity = "alpha";
	distanceSeries.showOnInit = true;
	distanceSeries.stacked = true;
	
	var distanceState = distanceSeries.columns.template.states.create("hover");
	distanceState.properties.fillOpacity = 0.9;
	
	// Create series
	var distanceSeriess = chart.series.push(new am4charts.ColumnSeries());
	distanceSeriess.dataFields.valueY = "value2";
	distanceSeriess.dataFields.dateX = "date";
	distanceSeriess.yAxis = distanceAxis;
	distanceSeriess.tooltipText = "Non Project Hrs: {valueY} ";
	distanceSeriess.name = "Non Project Hrs";
	distanceSeriess.columns.template.fillOpacity = 0.7;
	distanceSeries.columns.template.propertyFields.strokeDasharray = "dashLength";
	distanceSeriess.columns.template.propertyFields.fillOpacity = "alpha";
	distanceSeriess.showOnInit = true;
	distanceSeriess.stacked = true;
	
	var distanceState = distanceSeries.columns.template.states.create("hover");
	distanceState.properties.fillOpacity = 0.9;
	
	var durationSeries = chart.series.push(new am4charts.LineSeries());
	durationSeries.dataFields.valueY = "value4";
	durationSeries.dataFields.dateX = "date";
	//durationSeries.yAxis = distanceAxis;
	durationSeries.name = "Project Expected Hrs";
	durationSeries.strokeWidth = 2;
	durationSeries.propertyFields.strokeDasharray = "dashLength";
	durationSeries.tooltipText = "Project Expected Hrs: {valueY}";
	durationSeries.showOnInit = true;
	
	var durationBullet = durationSeries.bullets.push(new am4charts.Bullet());
	var durationRectangle = durationBullet.createChild(am4core.Rectangle);
	durationBullet.horizontalCenter = "middle";
	durationBullet.verticalCenter = "middle";
	durationBullet.width = 7;
	durationBullet.height = 7;
	durationRectangle.width = 7;
	durationRectangle.height = 7;
	
	var durationState = durationBullet.states.create("hover");
	durationState.properties.scale = 1.2;
	
	var latitudeSeries = chart.series.push(new am4charts.LineSeries());
	latitudeSeries.dataFields.valueY = "value3";
	latitudeSeries.dataFields.dateX = "date";
	//latitudeSeries.yAxis = distanceAxis;
	latitudeSeries.name = "Expected Hrs";
	latitudeSeries.strokeWidth = 2;
	latitudeSeries.propertyFields.strokeDasharray = "dashLength";
	latitudeSeries.tooltipText = "Total Expected Hrs: {valueY}";
	latitudeSeries.showOnInit = true;
	
	var latitudeBullet = latitudeSeries.bullets.push(new am4charts.CircleBullet());
	latitudeBullet.circle.fill = am4core.color("#fff");
	latitudeBullet.circle.strokeWidth = 2;
	latitudeBullet.circle.propertyFields.radius = "townSize";
	
	var latitudeState = latitudeBullet.states.create("hover");
	latitudeState.properties.scale = 1.2;
	
	var latitudeLabel = latitudeSeries.bullets.push(new am4charts.LabelBullet());
	latitudeLabel.label.text = "{townName2}";
	latitudeLabel.label.horizontalCenter = "left";
	latitudeLabel.label.dx = 14;
	
	// Add legend
	chart.legend = new am4charts.Legend();
	
	// Add cursor
	chart.cursor = new am4charts.XYCursor();
	chart.cursor.fullWidthLineX = true;
	chart.cursor.xAxis = dateAxis;
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineX.fill = am4core.color("#000");
	chart.cursor.lineX.fillOpacity = 0.1;
	
	}); // end am4core.ready()
	</script>
	
	<!-- HTML -->
	<div id=<?php echo $chartdivv; ?> ></div>
	<?php } ?>*/