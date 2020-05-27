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

$current_user_id = get_current_user_id();
//echo $current_user_id;
//echo 'Your User ID is: ' .$current_user_id;project_id NOT LIKE '%[0-9]%'AND project_id = Vacation
$current_user = wp_get_current_user();


global $wpdb;
    $table = 'timesheets';
	$client_table = 'projects';
	$table_name = $wpdb->prefix . $table;
	$client_table_name = $wpdb->prefix . $client_table; 
	$result = $wpdb->get_results(
		"SELECT * FROM $table_name
		    INNER JOIN $client_table_name ON ID = project_id
			WHERE user_id = $current_user_id");
			
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
		
    
/**/
//	$length = count($result);
//	$length = $length - 1;

	$array = json_decode(json_encode($result), true);
		
    echo "<pre>";
	//print_r($array_nonprojects);

	echo "</pre>";
 
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
   
echo "<h1>Leader Time Analysis : </h1>";
echo $current_user->display_name;
echo "<br />";
print_r($current_user->user_email);

echo "<br />";
echo "<form action=\"/button-type\"> <button type=\"button\"><a href=/wp-opdash/time-analysis-projects/>Time Analysis - Project</a></button><label for=\"Time Analysis Project\">Time Analysis Project</label></form>";
echo "<br />";
echo "<form action=\"/button-type\"> <button type=\"button\"><a href=/wp-opdash/time-analysis-non-projects>Time Analysis - Non Project</a></button><label for=\"Time Analysis - Non Project\">Time Analysis - Non Project</label></form>";
echo "<pre>";
//print_r($arr_output_nonprojects[2020]['03']);
//print_r($arr_output[2017]);'2018','2017', '2016['timesheet_hours']'2020','
echo "</pre>";
$years = array('2020');
global $projectTimeTotalarray;
global $nonprojectTimeTotalarray;
$projectTimeTotalarray = array();
$nonprojectTimeTotalarray = array();
$nonprojectTimeTotalarrayPieChart  = array();
$projectTimeTotalarrayPieChart  = array();

 
foreach($years as $val){
	
	foreach($arr_output[$val] as $dates){
		
		$iter++;
		if($iter == 04){
		
			$sum_total = 0;
			$i = 0;		$s = 0;
			$projectTimeTotalarray = array();
			$nonprojectTimeTotalarray = array();
			$non_project_sumtotal = "";
			foreach($dates as $hours){
				echo "<pre>";
				//$pTotalTime = $hours['timesheet_hours'];		
					$projectTimeTotalarray[$hours['abbreviated_name']][$i] = $hours['timesheet_hours'];				
					$sum_total = $hours['timesheet_hours'] + $sum_total;			
					
					
					//$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$i]['project_id']][$i] = $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][26]['timesheet_hours'];
					//echo $arr_output_nonprojects[$val][$months][$i]['project_id'];
					//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_notes'];
					$i++;
				
				echo "</pre>";
			} 	
			//$month = '12';
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
					
			$s += 1;			
			echo "<pre>";
			//print_r($dates[0]['timesheet_date']);
			echo "<hr />";
			$exp_month = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
			$month = $exp_month[1];
			
			$date_nonprojects_months = $arr_output_nonprojects[$val][$month][0]['timesheet_date'];
			echo "<h1>Date </h1>";		
			
			//echo date('Y-F-d', );
			print_r(date('Y-F-d' , $dates[0]['timesheet_date']));
			echo "<br />";
			//print_r(date('Y-F-d', $date_nonprojects_months));
			echo "<br />";			
			$i="0";
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
			/*
			echo "<h1>Total Number of Hours Worked Per project: </h1><br />";		 
			foreach($projectTimeTotalarray as $keystotal => $ptotalval){
				echo "<div style=\"float: left;\">".$keystotal.": </div>";
				echo "<div style=\"padding-left: 200px;\">".ceil(array_sum($ptotalval))."</div>";
				//echo "<br />";			
			}
		
			
			echo "<h1>Total Number of Hours Worked Per Non-project: </h1><br />";
			foreach($nonprojectTimeTotalarray as $nonkeystotal => $nonptotalval){
				//print_r($nonptotalval);
				//if(empty($nonptotalval)){<div style="float: left;"></div><div style="float: left;">
				//	echo "Hello World";
				//}
				$nonptotalval = array_filter($nonptotalval);
				if(!empty($nonptotalval)){
					if($nonkeystotal == '0001'){
					$nonkeystotal = 'Admin';
					echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == '0001MK'){
						$nonkeystotal = 'Farming';
						echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == '0001HR'){
						$nonkeystotal = 'HR';
						echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == 'Sick'){
						$nonkeystotal = 'Personal';
						echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == 'BEREAV'){
						$nonkeystotal = 'BEREAV';
						echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}else{
						echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}
				
				}
					
				
				echo "<div style=\"padding-left: 200px;\">".ceil(array_sum($nonptotalval))."</div> ";
				//echo "<br />";			
			}
			*/
			echo "<h1>Total Number of Hours Worked - Projects </h1>";	 
			echo round($sum_total);
			echo "<br />";
			echo "<h1>Total Number of Hours Worked - Non Projects</h1>";				
			echo round($non_project_sumtotal);
			echo "<h1>Total Number of Hours</h1>";		
			echo  round($total = $non_project_sumtotal + $sum_total);
			//echo "</pre>";
			echo "<h1>Total Number of Hours Worked Project Percent</h1>";		
			echo  round(100 * ($sum_total/$total))."%";				
			//echo "</pre>";
			echo "<h1>Total Number of Hours Non-project Percent</h1>";		
			echo  round(100 * ($non_project_sumtotal/$total))."%";
			//echo "</pre>";/**/
			
			$litres ="litres".$diff;
			$nonprojectTimeTotalarrayPieChart[$litres] = $non_project_sumtotal;
			$projectTimeTotalarrayPieChart[$litres] = $sum_total;
			$country ="country".$diff;
			$identifiernonprojectarrayPieChart  = array($country => 'Non Projects');
			$identifierprojectarrayPieChart  = array($country => 'Projects');
			$mergednonprojectTimeTotalarrayPieChart = array_merge($identifiernonprojectarrayPieChart, $nonprojectTimeTotalarrayPieChart);
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectTimeTotalarrayPieChart);
			//print_r($mergedprojectTimeTotalarrayPieChart);
			$mergednonprojectTimeTotalarrayPieChart_encode = json_encode($mergednonprojectTimeTotalarrayPieChart);
			$mergedprojectTimeTotalarrayPieChart_encode = json_encode($mergedprojectTimeTotalarrayPieChart);
			$charpiearr = $mergednonprojectTimeTotalarrayPieChart_encode.",".$mergedprojectTimeTotalarrayPieChart_encode;
			//echo $charpiearr.", ".$litres.", ".$country;
			my_piechart($charpiearr, $litres, $country);
			$diff++;
			$charpiearr = " ";
			//echo $month;
			if($month == '12'){
				break;
			}
			// 
		}

	}
		
	echo "<pre>";
	//print_r($arr_output_nonprojects[$val][$dates]['timesheet_id']);
	echo "</pre>";
	break;
	
}	
?>
<?php
//print_r($nonprojectTimeTotalarrayPieChart)."<br />";
$arr = array( "country" => 'Projects', "litres" => 118);
$arr01 = array("country" => 'Non Projects', "litres" => 60);

//echo json_encode($nonprojectTimeTotalarrayPieChart);
 $arrenc = json_encode($arr);
 $arrenc01 = json_encode($arr01);
 global  $charpiearrr;
 $charpiearrr = $arrenc.",".$arrenc01;
 //print_r($charpiearrr);
 for($t=0; $t<=2; $t++){
	//my_piechart();
 }

?>
<!-- Styles -->
<style>
#chartdiv {
  width: 90%;
  height: 300px;
}

</style>

<?php
function my_piechart($charpiearr, $litres, $country){

?>
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

var chart = am4core.create("chartdiv", am4charts.PieChart3D);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

chart.legend = new am4charts.Legend();

chart.data = [

	<?php echo $charpiearr ?>

  
];

var series = chart.series.push(new am4charts.PieSeries3D());
var country = <?php echo "\"$country\""; ?>;
var litres = <?php echo "\"$litres\""; ?>;
series.dataFields.value = litres;
series.dataFields.category = country;

}); // end ?php echo $charpiearr ?>;am4core.ready()var country;
</script>

<!-- HTML  {
    country: "Projects",
    litres: 501.9
  },
  {
    country: "Czech Republic",
    litres: 301.9
  }, -->
<div id="chartdiv"></div>
<? } ?>