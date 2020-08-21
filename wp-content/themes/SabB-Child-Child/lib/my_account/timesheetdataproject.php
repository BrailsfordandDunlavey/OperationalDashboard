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


//echo $current_user_id;
//echo 'Your User ID is: ' .$current_user_id;project_id NOT LIKE '%[0-9]%'AND project_id = Vacation
//$current_user = wp_get_current_user();
//print_r($_GET['current_user']);

if($_GET['current_user']){
	$current_user_id = $_GET['current_user'];
}else{
	$current_user_id = get_current_user_id();
}

$user_info = get_user_by("id", $current_user_id);

$today = getdate();
//print_r($today);

$month_today = $today['mon'];

//$month_today;

global $wpdb;
    $table = 'timesheets';
	$client_table = 'projects';
	$table_name = $wpdb->prefix . $table;
	$client_table_name = $wpdb->prefix . $client_table; 
	$result = $wpdb->get_results(
		"SELECT * FROM $table_name
		    INNER JOIN $client_table_name ON ID = project_id
			WHERE user_id = $current_user_id AND timesheet_date BETWEEN UNIX_TIMESTAMP('2019-$month_today-01') AND UNIX_TIMESTAMP('2020-$month_today-01')");

	$array = json_decode(json_encode($result), true);

			$result_nonproject = $wpdb->get_results(
				"SELECT * FROM $table_name
					WHERE user_id = $current_user_id AND (project_id LIKE '%Vacation%'
					OR project_id LIKE '%Holiday%'						 				
					OR project_id LIKE '%HR%'
					OR project_id LIKE '0001'
					OR project_id LIKE 'Sick'
					OR project_id LIKE '0001MK'
					OR project_id LIKE 'BEREAV') AND timesheet_date BETWEEN UNIX_TIMESTAMP('2019-$month_today-01') AND UNIX_TIMESTAMP('2020-$month_today-01')");
			
	$array_nonprojects = json_decode(json_encode($result_nonproject), true);
		
	$table_emp_sup = 'useradd';
	$client_table_emp = 'users';
	$table_name_emp_sup = $wpdb->prefix . $table_emp_sup;
	$client_table_name_emp = $wpdb->prefix . $client_table_emp; 
	$result_emp_sup = $wpdb->get_results(
		"SELECT user_id, position, team FROM $table_name_emp_sup ud			 		    
			WHERE ud.user_id = $current_user_id");


$table_staff_projecthrs = 'staff';
	   $table_name_staff_projecthrs = $wpdb->prefix . $table_staff_projecthrs;	
	   $result_staff_projecthrs = $wpdb->get_results(
		"SELECT * FROM $table_name_staff_projecthrs		    
            WHERE Name LIKE '%$user_info->display_name%'");

//echo "<pre>";
//print_r($result_staff_projecthrs);
//echo "</pre>";
$total_epected_hour_percent = $result_staff_projecthrs[0]->ProjectPercent / 100;
//echo $total_epected_hour_percent;
if($total_epected_hour_percent == 0){
	$total_epected_hour_percent = 87/100;
}
//echo $total_epected_hour_percent;
//	$length = count($result);user_id, reports_to,
//	$length = $length - 1;INNER JOIN $table_emp_sup uu ON ud.user_id = uu.ID 
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
   
   //$arr_output = $arr_output[0];
//echo "This is my time analysis";
/*echo "<h1>Leader Time Analysis : </h1>";
echo $current_user->display_name;
echo "<br />";
print_r($current_user->user_email);
echo "<br />";
echo "<br />";
echo "<form action=\"/button-type\"> <button type=\"button\"><a href=/wp-opdash/time-analysis-projects>Time Analysis - Project</a></button><label for=\"Time Analysis Project\">Time Analysis Project</label></form>";
echo "<br />";
echo "<form action=\"/button-type\"> <button type=\"button\"><a href=/wp-opdash/time-analysis-non-projects>Time Analysis - Non Project</a></button><label for=\"Time Analysis - Non Project\">Time Analysis - Non Project</label></form>";
echo "<pre>";
echo "</pre>";
*/
//require "form_DatePicker.html";
//print_r($arr_output_nonprojects);
if($_POST['date']){
$month_input = explode("/", $_POST['date']);
}else{
	$month_input = explode("/", "01/01/2020");
}

$display_name = $user_info->display_name;
echo "You are currently viewing $display_name dashboard";

// best stored as array, so you can add more than one
global $holidays;
$holidays = array('2020-01-01', '2020-01-20', '2020-02-17', '2020-05-25', '2020-07-03', '2020-09-07', '2020-11-26', '2020-11-27', '2020-12-24', '2020-12-25', '2020-12-31');
$workingDaysInaMonth = array();
//print_r($month_input);
for($et=0; $et<=11; $et++){
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
		$workingDaysInaMonth[] = $days*8;
}
foreach($result_emp_sup as $key_teamsemp => $val_teamsemp){	
	$team_management_position[] = $val_teamsemp->position;
	$team_management_teams[$val_teamsemp->team][] = $val_teamsemp->user_id;

}
//print_r($result_emp_sup);
global $expected_hours_pertitle_array;
$expected_hours_pertitle_array = array( 2 => 67, 3 => 67, 4 => 67, 5 => 67, 6 => 79, 7 => 87, 8 => 87, 9 => 0, 10 => 0, 11 => 67, 12 => 0, 13 => 0, 14 => 79, 15 => 79, 16 => 87, 17 => 87, 18 => 67, 19 => 67, 20 => 67, 21 => 67, 22 => 67);
global $total_expect_hrs_project;

$first_m = '0'. $month_input[0];
$second_m = '0'. $month_input[0] + 1;
$third_m = '0'. $month_input[0] + 2;

$nonprojectTimeTotalarrayPieChartbigin = array("value1" => 0, "value2" => 0, "value3" => 0, "value4" => 0);
$mergedprojectTimeTotalarrayPieChart_encode = json_encode($nonprojectTimeTotalarrayPieChartbigin);
$years = array("2019","2020");

global $projectTimeTotalarray;
global $nonprojectTimeTotalarray;
global $charpiearr;
global $litres;
global $country;
$projectTimeTotalarray = array();
$nonprojectTimeTotalarray = array();
$nonprojectTimeTotalarrayPieChart  = array();
$projectTimeTotalarrayPieChart  = array();
$diff = rand(100,200);
$difff = rand(100,200);
///////////////////////////////////////Break this off////////////////////////////////////////"2020",
$s = 0;



foreach($years as $val){
	$expectedhour_monthone_sphere = array();
			$expectedhour_monthone_sphereone = array();
			$expectedhour_monthone_spheretwo = array();
			$added_expected = array();
			$added_expectedone = array();
			$added_expectedtwo = array();
	foreach($arr_output[$val] as $dates){
		$exp_months = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
		$months = $exp_months[1];		
		//if($months == $first_m OR $months == $second_m OR $months == $third_m){
		
			$sum_total = 0;
			$i = 0;		
			$projectTimeTotalarray = array();
			$nonprojectTimeTotalarray = array();
			$non_project_sumtotal = "";			 
			foreach($dates as $hours){
				//echo "<pre>";
					$projectTimeTotalarray[$hours['abbreviated_name']][$i] = $hours['timesheet_hours'];									
					$sum_total = $hours['timesheet_hours'] + $sum_total;
					$i++;				
				//echo "</pre>";
			} 			 					
					//echo "<br />";
					$non_project_count = count($arr_output_nonprojects[$val][$months]);
					for($l=0; $l<$non_project_count; $l++){
						$hournonproject = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
						//echo $hournonproject;
						$exploded_hournonproject = explode("-", $hournonproject);
						//print_r($exploded_hournonproject);
						$count_exploded_hournonproject_arr = count($exploded_hournonproject);
						if($count_exploded_hournonproject_arr == 1){
							//echo $exploded_hournonproject[0];
							
							$non_project_sumtotal = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'] + $non_project_sumtotal;
							$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$l]['project_id']][$l] = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
							
						}
					}											
		
			$exp_month = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
			$month = $exp_month[1];	

			$workdays = array();
			$type = CAL_GREGORIAN;
			//$month = date('n'); // Month ID, 1 through to 12.
			$year = $val; // Year in 4 digit 2009 format.
			$day_count = cal_days_in_month($type, $month, $year); // Get the amount of days
			//echo $month;
			//loop through all days
			for ($i = 1; $i <= $day_count; $i++) {
			
					$date = $year.'/'.$month.'/'.$i; //format date
					$get_name = date('l', strtotime($date)); //get week day
					$day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
			
					//if not a weekend add day to array
					if($day_name != 'Sun' && $day_name != 'Sat'){
						$workdays[] = $i;
					}
			
			}			
			// look at items in the array uncomment the next line
			$count_daysworking[] = count($workdays)*8;

			$projenArrNames = array();
			foreach($dates as $porjectnames){
				//echo $porjectnames['project_name'];		
				$projenArrNames[$porjectnames['project_name']] = $porjectnames['project_name'];
				//echo $projenArrNames;
			}	
			$filtered_arr = array_filter($projenArrNames);
			//echo "<h1>Name of The Projects </h1><br />";
			foreach($filtered_arr as $finalProjectNames){
				//echo $finalProjectNames."<br />";
			}				
		
			$projenArrABRNames = array();
			foreach($dates as $porjectABRnames){
				//echo $porjectnames['project_name'];		
				$projenArrABRNames[$porjectABRnames['abbreviated_name']] = $porjectABRnames['abbreviated_name'];
				//echo $projenArrNames;
			}	
			$filtered_arrABRName = array_filter($projenArrABRNames);
			//echo "<h1>Project Abreviated Names </h1><br />";
			foreach($filtered_arrABRName as $finalProjectABRNames){
				//echo $finalProjectABRNames."<br />";
			}
			$projenArrSpheresNames = array();
			foreach($dates as $porjectSpheresnames){
				//echo $porjectnames['project_name'];		
				$projenArrSpheresNames[$porjectSpheresnames['sphere']] = $porjectSpheresnames['sphere'];
				//echo $projenArrNames;
			}	
			$filtered_arrSpheresName = array_filter($projenArrSpheresNames);
			//echo "<h1>Project Spheres</h1> <br />";
			foreach($filtered_arrSpheresName as $finalProjectSpheresNames){
				//echo $finalProjectSpheresNames."<br />";
			}		
			$projectTimeTotalarray = array_filter($projectTimeTotalarray);		
			for($u=0;$u<=2100;$u++){
				unset($projectTimeTotalarray[$u]);
			}			 
			
			//echo "<h1>Total Number of Hours Worked - Projects </h1>";	 
			round($sum_total);
			//echo "<br />";
			//echo "<h1>Total Number of Hours Worked - Non Projects</h1>";				
			round($non_project_sumtotal);
			//echo "<h1>Total Number of Hours</h1>";		
			round($total = $non_project_sumtotal + $sum_total);
			//echo "</pre>";
			//echo "<h1>Total Number of Hours Worked Project Percent</h1>";		
			 round(100 * ($sum_total/$total))."%";				
			//echo "</pre>";
			//echo "<h1>Total Number of Hours Non-project Percent</h1>";		
			round(100 * ($non_project_sumtotal/$total))."%";
			//echo "</pre>";			
			$country ="date";				
			// Declare month number and initialize it 
			$monthNum = $month;   
			// Create date object to store the DateTime format $monthName
			$dateObj = DateTime::createFromFormat('!Ymd', $monthNum);   
			// Store the month name to variable 
			//print_r($val);*$workingDaysInaMonth[0]
			$monthName = $val.'-'.$month.'-01'; 
			$identifiernonprojectarrayPieChart  = array($country => "Average");
			//echo $total_epected_hour_percent;

			$expectedhour_monthone_sphere[$s] = $total_epected_hour_percent*$count_daysworking[$s];

			echo "<pre>";
			//print_r($total_expect_hrs_project);
			echo "</pre>";
			$nonprojectTimeTotalarrayPieChart = array($country => $monthName, "value1" => $sum_total, "value2" => $non_project_sumtotal, "value3" => $count_daysworking[$s], "value4" => $expectedhour_monthone_sphere[$s]); 
			//$nonprojectTimeTotalarrayPieChartend = array("value1" => 250, "value2" => 250, "value3" => 250, "value4" => 250);$mergedprojectTimeTotalarrayPieChart_encode 
			$mergednonprojectTimeTotalarrayPieChart =  $nonprojectTimeTotalarrayPieChart;
			$mergednonprojectTimeTotalarrayPieChart_encode .= json_encode($mergednonprojectTimeTotalarrayPieChart).",";			
			$charpiearr = $mergednonprojectTimeTotalarrayPieChart_encode;
		    $charpiearr.", ".$litres.", ".$country;
			$diff = rand(100, 200);
			$difff = rand(150, 170);
			//echo $month;
			//echo $third_m;
			if($month == $month_today - 1){
				//break;  
				//print_r($charpiearr);
				$date_nonprojects_months = $arr_output_nonprojects[$val][$month][0]['timesheet_date'];
				//echo "<h1>Date </h1>";		
				//echo "checking iffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff";
				//echo date('Y-F-d', );
				//print_r(date('Y-F-d' , $dates[0]['timesheet_date']));
				//echo "<br />";
				my_piechartt($charpiearr, $month_input[2]);
			}
			$charpiearr = " ";
			// 
			$s += 1;
		//}

	}
		

}

?>

<?php
function my_piechartt($charpiearr, $year){
//print_r($charpiearr);
?>

<!-- Styles -->
<style>
#chartdivvv {
  width: 180%;
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
var chart = am4core.create("chartdivvv", am4charts.XYChart);

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
dateAxis.title.text = "Months for Year: "+<?php echo $year; ?>;

var distanceAxis = chart.yAxes.push(new am4charts.ValueAxis());
distanceAxis.title.text = "Hours";

var latitudeAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Modify chart's colors
chart.colors.list = [
  am4core.color("#216C2A"),
  am4core.color("#D65DB1"),
  am4core.color("#FF0000"),
  am4core.color("#FF9671"),
  am4core.color("#FFC75F"),
  am4core.color("#F9F871"),
];


// Create series
var distanceSeries = chart.series.push(new am4charts.ColumnSeries());
distanceSeries.dataFields.valueY = "value1";
distanceSeries.dataFields.dateX = "date";
distanceSeries.yAxis = distanceAxis;
distanceSeries.tooltipText = "Project Hrs: {valueY} ";
distanceSeries.name = "Project Hrs";
distanceSeries.columns.template.fillOpacity = 0.7;
distanceSeries.columns.template.propertyFields.strokeDasharray = "dashLength";
distanceSeries.columns.template.propertyFields.fillOpacity = "green";
distanceSeries.showOnInit = true;
distanceSeries.stacked = true;
// Set up tooltips
distanceSeries.tooltip.label.interactionsEnabled = true;
distanceSeries.tooltip.keepTargetHover = true;
distanceSeries.columns.template.tooltipHTML = '<b>Project Hrs: {valueY}</b><br><a href="{category.urlEncode()}">More info</a>';

var distanceState = distanceSeries.columns.template.states.create("hover");
distanceState.properties.fillOpacity = 0.9;

// Create series
var distanceSeriess = chart.series.push(new am4charts.ColumnSeries());
distanceSeriess.dataFields.valueY = "value2";
distanceSeriess.dataFields.dateX = "date";
distanceSeriess.yAxis = distanceAxis;
distanceSeriess.tooltipText = "Non-project Hrs: {valueY} ";
distanceSeriess.name = "Non-project Hrs";
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
<div id="chartdivvv"></div>

<? }?>