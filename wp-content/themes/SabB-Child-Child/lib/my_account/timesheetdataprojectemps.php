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

//$current_user_id = get_current_user_id();
//echo $current_user_id;
//echo 'Your User ID is: ' .$current_user_id;project_id NOT LIKE '%[0-9]%'AND project_id = Vacation
//$current_user = wp_get_current_user();


echo "<pre>";
//print_r($user_id = $result_emp_sup[0]->);
//print_r($user_id = $result_emp_sup);
//require_once 'timesheetdataprojectemps.php';
$c = 0;
echo "<table style=\"table-layout: fixed; width: 130%\"><tr>";
foreach($result_emp_sup as $key => $vals){
	$user_id = $vals->user_id;
	$author_obj = get_user_by('id', $user_id);
	if($c == 3 or $c == 6 or $c == 9 or $c == 12){
		echo "</tr><tr>";		
	}
	echo "<td><hr>";
	print_r("<a href=/wp-opdash/time-analysis-hours-worked-per-project?user_id=$user_id>".$author_obj->data->display_name."</a>");
	//echo "";
	//echo "<br />";
	$display_name = $author_obj->data->display_name;
	//echo $user_id;
	$chartdivv = 'chartdivv'.$c;
	//echo "";
	myPieChart($user_id, $display_name, $chartdivv);
	echo "<br />";
	echo "</td>";
	
	$c++;
}
echo "</tr></table>";
//echo "<table>";
function myPieChart($user_id, $display_name, $chartdivv){

	$user_id." ".$display_name. " ".$chartdivv;



echo "</pre>";
$current_user_id = $user_id; 
//echo $current_user = $user_id;
//echo $current_user_id;
global $wpdb;
$table_emp_sup = 'useradd';
	$client_table_emp = 'users';
	$table_name_emp_sup = $wpdb->prefix . $table_emp_sup;
	$client_table_name_emp = $wpdb->prefix . $client_table_emp; 
	$result_emp_sup = $wpdb->get_results(
		"SELECT user_id FROM $table_name_emp_sup ud			 		    
			WHERE ud.reports_to = $current_user_id");




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
				OR project_id LIKE 'BEREAV')/**/");
		
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
$years = array('2020');
global $projectTimeTotalarray;
global $nonprojectTimeTotalarray;
$projectTimeTotalarray = array();
$nonprojectTimeTotalarray = array();
$nonprojectTimeTotalarrayPieChart  = array();
$projectTimeTotalarrayPieChart  = array();
$iter = 0;
foreach($years as $val){
	
	foreach($arr_output[$val] as $dates){
		//echo "<pre>";
			//print_r($dates);
		//	echo "</pre>";
		
		$iter;
		if($iter == 3){
			//echo "Hello World";
			$sum_total = 0;
			$i = 0;		$s = 0;
			$projectTimeTotalarray = array();
			$nonprojectTimeTotalarray = array();
			$non_project_sumtotal = "";

			foreach($dates as $hours){
				//echo "<pre>";
				    //print_r($hours);
					// $pTotalSpheres = $hours['sphere'];	
					//pTotalSpheres = $hours['timesheet_hours'];	
					//echo $pTotalSpheres = $hours['abbreviated_name'];	
					$projectTimeTotalarray[$hours['abbreviated_name']][$i] = $hours['timesheet_hours'];
									
					$sum_total = $hours['timesheet_hours'] + $sum_total;			
					
					
					//$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$i]['project_id']][$i] = $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][26]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][$i]['project_id'];
					//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_notes'];
					$i++;
				
			
					//echo "</pre>";
			} 
			
					$exp_months = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
					$months = $exp_months[1];
					
					$non_project_count = count($arr_output_nonprojects[$val][$months]);
					for($l=0; $l<$non_project_count; $l++){
						$hournonproject = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
						//echo $hournonproject;
						$exploded_hournonproject = explode("-", $hournonproject);
						//print_r($exploded_hournonproject);
						$count_exploded_hournonproject_arr = count($exploded_hournonproject);
						//echo $count_exploded_hournonproject_arr;
						//echo $i;
						if($count_exploded_hournonproject_arr == 1){
							//echo $exploded_hournonproject[0];
							
							$non_project_sumtotal = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'] + $non_project_sumtotal;
							$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$l]['project_id']][$l] = $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
							//echo $arr_output_nonprojects[$val][$months][$l]['timesheet_hours'];
							//echo $arr_output_nonprojects[$val][$months][$l]['project_id'];
							//echo "<br />";
						}
					}

			
			
		}
		$iter++;		

	}
}
 $nonprojectTimeTotalarrayPieChart = array();
 $projectTimeTotalarrayPieChart = array();

			$litres ="litres";
			$nonprojectTimeTotalarrayPieChart[$litres] = $non_project_sumtotal;
			$projectTimeTotalarrayPieChart[$litres] = $sum_total;
			$country ="country";
			$identifiernonprojectarrayPieChart  = array($country => 'Non Projects');
			$identifierprojectarrayPieChart  = array($country => 'Projects');
			$mergednonprojectTimeTotalarrayPieChart = array_merge($identifiernonprojectarrayPieChart, $nonprojectTimeTotalarrayPieChart);
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectTimeTotalarrayPieChart);

			$mergednonprojectTimeTotalarrayPieChart_encode = json_encode($mergednonprojectTimeTotalarrayPieChart);			
			$mergedprojectTimeTotalarrayPieChart_encode = json_encode($mergedprojectTimeTotalarrayPieChart);
			$charpiearr = $mergednonprojectTimeTotalarrayPieChart_encode.",".$mergedprojectTimeTotalarrayPieChart_encode;
			

			//echo $charpiearr." ";
?>
			<!-- Styles -->
			<style>
			<?php echo "#".$chartdivv?> {
			  width: 90%;
			  height: 300px;
			  margin-top: -150px;
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
			
			var chart = am4core.create(<?php echo $chartdivv?>, am4charts.PieChart3D);
			chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
			
			chart.legend = new am4charts.Legend();
			
			chart.data = [
			  
				<?php echo $charpiearr ?>
			   
			];
			
			var series = chart.series.push(new am4charts.PieSeries3D());
			series.dataFields.value = "litres";
			series.dataFields.category = "country";
			
			}); // end am4core.ready()
			</script>
			
			<!-- HTML -->
			<div id=<?php echo $chartdivv?> >


<?}?>
		