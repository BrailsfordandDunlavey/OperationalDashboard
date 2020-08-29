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

$current_user_id_members = get_current_user_id(); 


echo "<pre>";

global $wpdb;
$table_emp_sup = 'useradd';
	$client_table_emp = 'users';
	$table_name_emp_sup = $wpdb->prefix . $table_emp_sup;
	$client_table_name_emp = $wpdb->prefix . $client_table_emp; 
	$result_emp_sup = $wpdb->get_results(
		"SELECT * FROM $table_name_emp_sup			 		    
			WHERE reports_to = $current_user_id_members");
$c = 0;
echo "<table style=\"table-layout: fixed; width: 100%\"><tr>";
foreach($result_emp_sup as $key => $vals){
	$user_id = $vals->user_id;	
	$author_obj = get_user_by('id', $user_id);
	if($c == 3 or $c == 6 or $c == 9 or $c == 12){
		echo "</tr><tr>";		
	}

	echo "<td><hr>";
	print_r("<a href=/wp-opdash/time-analysis-hours-worked-per-project?user_id=$user_id>".$author_obj->data->display_name."</a>");

	$display_name = $author_obj->data->display_name;
	
	$chartdivv = 'chartdivv'.$c;
 
	myPieChart($chartdivv, $year, $user_id, $result_emp_sup);
	
	echo "</td>";	
	$c++;
}
echo "</tr></table>";
 
function myPieChart($chartdivv, $year, $user_id, $result_emp_sup){

echo "</pre>";
$current_user_id = $user_id; 

global $wpdb;
$table = 'timesheets';
$client_table = 'projects';
$table_name = $wpdb->prefix . $table;
$client_table_name = $wpdb->prefix . $client_table; 
$result = $wpdb->get_results(
	"SELECT * FROM $table_name
		INNER JOIN $client_table_name ON ID = project_id
		WHERE user_id = $current_user_id");
 
$array = json_decode(json_encode($result), true);

		$result_nonproject = $wpdb->get_results(
			"SELECT * FROM $table_name
				WHERE user_id = $current_user_id AND (project_id LIKE '%Vacation%'
				OR project_id LIKE '%Holiday%'						 				
				OR project_id LIKE '%HR%'
				OR project_id LIKE '0001'
				OR project_id LIKE 'Sick'
				OR project_id LIKE '0001MK'
				OR project_id LIKE 'BEREAV')");
		
$array_nonprojects = json_decode(json_encode($result_nonproject), true);
$arr_output = array();

foreach($array as $key=>$arr)
{
	$timesheet_date = $arr['timesheet_date'];
	$date = date('Y-m-d', $timesheet_date);
	$arr_dates = explode("-", $date);
	$year = $arr_dates[0];
	$month = $arr_dates[1];	   
	$arr_output[$year][$month][] = $arr;
}

/////Non-Project Hours/////////////
global $arr_output_nonprojects; 
$arr_output_nonprojects = array();
foreach($array_nonprojects as $key_nonprojects=>$arr_nonprojects)
{
	$timesheet_date_nonprojects = $arr_nonprojects['timesheet_date'];
	$date_nonprojects = date('Y-m-d', $timesheet_date_nonprojects);
	$arr_dates_nonprojects = explode("-", $date_nonprojects);
	$year_nonprojects = $arr_dates_nonprojects[0];
	$month_nonprojects = $arr_dates_nonprojects[1];	   
	$arr_output_nonprojects[$year_nonprojects][$month_nonprojects][] = $arr_nonprojects;
}
//print_r($arr_output['2020']);

	if($_POST['date']){
		$month_input = explode("/", $_POST['date']);
	}else{
		$month_input = explode("/", "01/01/2020");
	}

$first_m = '0'. $month_input[0];
$second_m = '0'. $month_input[0] + 1;
$third_m = '0'. $month_input[0] + 2;

$years = array('2020');
global $projectTimeTotalarray;
global $nonprojectTimeTotalarray;
global $month_members;
$projectTimeTotalarray = array();
$nonprojectTimeTotalarray = array();
$nonprojectTimeTotalarrayPieChart  = array();
$projectTimeTotalarrayPieChart  = array();
$iter = 0;

///////////Create the total expected hour line/////////////////////////////////////////
// best stored as array, so you can add more than one
global $holidays;
$holidays = array('2020-01-01', '2020-01-20', '2020-02-17', '2020-05-25', '2020-07-03', '2020-09-07', '2020-11-26', '2020-11-27', '2020-12-24', '2020-12-25', '2020-12-31');
$workingDaysInaMonthmembers = array();
//print_r($month_input);
for($et=0; $et<=2; $et++){
		$months_project_totalhours = $month_input[0] + $et;
		'0'.$months_project_totalhours;
		
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


		 
		$workingDaysInaMonthmembers[] = $days*8.5;

}

		
/*$expected_hours_pertitle_array = array( 2 => array("Executive Vice President" => "67"), 3 => array("Vice President" => "67"), 4 => array("Regional Vice President" => "67"), 5 => array("Senior Project Manager" => "67"), 6 => array("Project Manager" => "79"), 7 => array("Assistant Project Manager" => "87"), 8 => array("Project Analyst" => "87"), 9 => array("Project Assistant" => "0"), 10 => array("Intern" => "0"), 11 => array("Director" => "67"), 12 => array("Coordinator" => "0"), 13 => array("Administrative Assistant" => "0"), 14 => array("Associate" => "79"), 15 => array("Senior Associate" => "79"), 16 => array("Higher Ed Analyst" => "87"), 17 => array("Senior Analyst" => "87"), 18 => array("Higher Ed VP" => "67"), 19 => array("Higher Ed EVP" => "67"), 20 => array("Leader" => "67"), 21 => array("KMV VP" => "67"), 22 => array("KMV EVP" => "67"));*/


$expected_hours_pertitle_array = array( 2 => 67, 3 => 67, 4 => 67, 5 => 67, 6 => 79, 7 => 87, 8 => 87, 9 => 0, 10 => 0, 11 => 67, 12 => 0, 13 => 0, 14 => 79, 15 => 79, 16 => 87, 17 => 87, 18 => 67, 19 => 67, 20 => 67, 21 => 67, 22 => 67);

//print_r($result_emp_sup);

foreach($expected_hours_pertitle_array as $expected_hrsKeys => $expected_hrsVals){
	foreach($result_emp_sup as $emp_infoKeys => $emp_infoVals){
		if($expected_hrsKeys == $emp_infoVals->position){			 
			$expectedhour_monthone[] = ($expected_hrsVals/100)*$workingDaysInaMonthmembers[0];
			$expectedhour_monthtwo[] = ($expected_hrsVals/100)*$workingDaysInaMonthmembers[1];
			$expectedhour_monththree[] = ($expected_hrsVals/100)*$workingDaysInaMonthmembers[2];
		
		break;
		

		}
	}
	
}

$workingProjectExpectedHours = array($expectedhour_monthone[0], $expectedhour_monthtwo[0], $expectedhour_monththree[0]);

$s = 0;
foreach($years as $val){
	
	foreach($arr_output[$val] as $dates){
		$exp_months = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
		$months = $exp_months[1];
		if($months == $first_m OR $months == $second_m OR $months == $third_m){		

			$sum_total = 0;
			$i = 0;		
			$projectTimeTotalarray = array();
			$nonprojectTimeTotalarray = array();
			$non_project_sumtotal = "";

			foreach($dates as $hours){
					$projectTimeTotalarray[$hours['abbreviated_name']][$i] = $hours['timesheet_hours'];									
					$sum_total = $hours['timesheet_hours'] + $sum_total;					
					$i++;
			
			} 
			//echo $months;
			
					
					
					$non_project_count = count($arr_output_nonprojects[$val][$months]);
					for($l=0; $l<$non_project_count; $l++){
						$hournonproject = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
						//echo $hournonproject;
						$exploded_hournonproject = explode("-", $hournonproject);
						//print_r($exploded_hournonproject);
						$count_exploded_hournonproject_arr = count($exploded_hournonproject);
					
						if($count_exploded_hournonproject_arr == 1){		
							
							$non_project_sumtotal = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'] + $non_project_sumtotal;
							$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$l]['project_id']][$l] = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
							
						}
					}

			//echo $non_project_sumtotal;

 			$nonprojectTimeTotalarrayPieChart = array();
 			$projectTimeTotalarrayPieChart = array();			
			
			//print_r($month);
			$country ="date";				
			// Declare month number and initialize it 
			$monthNum = $month;   
			// Create date object to store the DateTime format 
			$dateObj = DateTime::createFromFormat('!Ymd', $monthNum);   
			// Store the month name to variable 
			$monthName = '2020-'.$months.'-01'; 			

			$nonprojectTimeTotalarrayPieChart = array($country => $monthName, "value1" => $sum_total, "value2" => $non_project_sumtotal, "value3" => $workingDaysInaMonthmembers[$s], "value4" => $workingProjectExpectedHours[$s]); 

			$mergednonprojectTimeTotalarrayPieChart =  $nonprojectTimeTotalarrayPieChart;
			$mergednonprojectTimeTotalarrayPieChart_encode .= json_encode($mergednonprojectTimeTotalarrayPieChart).",";			
			$charpiearr = $mergednonprojectTimeTotalarrayPieChart_encode;				
			
			$difff = rand(150, 170);
			$s += 1;
		}
	}
}

?>


			<!-- Styles -->
<style>
<?php echo "#".$chartdivv?> {
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
var chart = am4core.create(<?php echo $chartdivv?>, am4charts.XYChart);

chart.colors.step = 2;
chart.maskBullets = false;

// Add data
chart.data = [
	
	<?php echo $charpiearr; ?>
];

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
dateAxis.renderer.grid.template.location = 0;
dateAxis.renderer.minGridDistance = 10;
dateAxis.renderer.grid.template.disabled = true;
dateAxis.renderer.fullWidthTooltip = true;
dateAxis.title.text = "Months for Year: "+<?php echo $year; ?>;

var distanceAxis = chart.yAxes.push(new am4charts.ValueAxis());
distanceAxis.title.text = "Hours";

var latitudeAxis = chart.yAxes.push(new am4charts.ValueAxis());

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
distanceSeriess.columns.template.propertyFields.strokeDasharray = "dashLength";
distanceSeriess.columns.template.propertyFields.fillOpacity = "alpha";
distanceSeriess.showOnInit = true;
distanceSeriess.stacked = true;

var distanceStatee = distanceSeriess.columns.template.states.create("hover");
distanceStatee.properties.fillOpacity = 0.9;

var durationSeries = chart.series.push(new am4charts.LineSeries());
durationSeries.dataFields.valueY = "value4";
durationSeries.dataFields.dateX = "date";
durationSeries.yAxis = distanceAxis;
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
latitudeSeries.yAxis = distanceAxis;
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
<div id=<?php echo $chartdivv?> >


<?php } ?>