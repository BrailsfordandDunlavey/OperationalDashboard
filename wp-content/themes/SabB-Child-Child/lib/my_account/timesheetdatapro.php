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
//print_r($arr_output['2020']);
echo "<table cellpadding=\"15\" style=\"table-layout: fixed; width: 180%\"><tr>";
foreach($years as $val){	
	
	foreach($arr_output[$val] as $dates){
			$sum_total = 0;
			$i = 0;		$s = 0;
			$projectTimeTotalarray = array();
			$nonprojectTimeTotalarray = array();
			$non_project_sumtotal = "";
			foreach($dates as $hours){
			
			
			$pTotalTime = $hours['timesheet_hours'];		
			$projectTimeTotalarray[$hours['abbreviated_name']][$i] = $hours['timesheet_hours'];	

			$projectTimeTotalarraySPHERES[$hours['sphere']][$i] = $hours['timesheet_hours'];				
			$projectTimeTEAMTotalarray[$hours['project_group']][$i] = $hours['timesheet_hours'];				
				$sum_total = $hours['timesheet_hours'] + $sum_total;			
				//um_total_spheres = $hours['timesheet_hours'] + $sum_total;			
				
				//$nonprojectTimeTotalarray[$arr_output_nonprojects[$val][$months][$i]['project_id']][$i] = $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
				//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_hours'];
				//echo $arr_output_nonprojects[$val][$months][26]['timesheet_hours'];
				//echo $arr_output_nonprojects[$val][$months][$i]['project_id'];
				//echo $arr_output_nonprojects[$val][$months][$i]['timesheet_notes'];
				$i++;
			//echo "<pre>";
		//print_r($hours);
		//echo "</pre>";
			
		}
		

		$exp_months = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
		$months = $exp_months[1];
		
		$non_project_count = count($arr_output_nonprojects[$val][$months]);

		//echo $non_project_count;
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

		$iter++;

		foreach($projectTimeTotalarray as $keystotal => $ptotalval){
			//echo "<div style=\"float: left;\">".$keystotal.": </div>";
			//echo "<div style=\"padding-left: 200px;\">".$propertotal = round(array_sum($ptotalval));
			//echo "</div>";
			$consol_projectTimeTotalarray[$keystotal] = $ptotalval; 
			
			/*$propertotal = round(array_sum($ptotalval));
			$projectperTotalarrayPieChart[$litres] = $propertotal;
			$identifierprojectarrayPieChart  = array($country => $keystotal);
			//echo "<br />";
			//print_r($projectperTotalarrayPieChart['litres']);	
			//print_r($identifierprojectarrayPieChart);	
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectperTotalarrayPieChart);
			//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodepro .= json_encode($mergedprojectTimeTotalarrayPieChart).",";
			*/
			
			//print_r($mergedprojectTimeTotalarrayPieChart_encodepro);
		}

		foreach($nonprojectTimeTotalarray as $nonkeystotal => $nonptotalval){
			//echo "<div style=\"float: left;\">".$keystotal.": </div>";
			//echo "<div style=\"padding-left: 200px;\">".$propertotal = round(array_sum($ptotalval));
			//echo "</div>";
			$consol_nonprojectTimeTotalarray[$nonkeystotal] = $nonptotalval; 
			
			/*$propertotal = round(array_sum($ptotalval));
			$projectperTotalarrayPieChart[$litres] = $propertotal;
			$identifierprojectarrayPieChart  = array($country => $keystotal);
			//echo "<br />";
			//print_r($projectperTotalarrayPieChart['litres']);	
			//print_r($identifierprojectarrayPieChart);	
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectperTotalarrayPieChart);
			//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodepro .= json_encode($mergedprojectTimeTotalarrayPieChart).",";
			*/
			
			//print_r($mergedprojectTimeTotalarrayPieChart_encodepro);
		}

		foreach($projectTimeTotalarraySPHERES as $SPHERESkeystotal => $SPHERESnonptotalval){
			//echo "<div style=\"float: left;\">".$keystotal.": </div>";
			//echo "<div style=\"padding-left: 200px;\">".$propertotal = round(array_sum($ptotalval));
			//echo "</div>";
			$consol_SPHERESprojectTimeTotalarray[$SPHERESkeystotal] = $SPHERESnonptotalval; 
			
			/*$propertotal = round(array_sum($ptotalval));
			$projectperTotalarrayPieChart[$litres] = $propertotal;
			$identifierprojectarrayPieChart  = array($country => $keystotal);
			//echo "<br />";
			//print_r($projectperTotalarrayPieChart['litres']);	
			//print_r($identifierprojectarrayPieChart);	
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectperTotalarrayPieChart);
			//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodepro .= json_encode($mergedprojectTimeTotalarrayPieChart).",";
			*/
			
			//print_r($mergedprojectTimeTotalarrayPieChart_encodepro);
		}

		foreach($projectTimeTEAMTotalarray as $TEAMkeystotal => $TEAMnonptotalval){
			//echo "<div style=\"float: left;\">".$keystotal.": </div>";
			//echo "<div style=\"padding-left: 200px;\">".$propertotal = round(array_sum($ptotalval));
			//echo "</div>";
			switch ($TEAMkeystotal) {
				case "West":
					$TEAMkeystotal = "Costa Mesa";
					break;
				case "Southeast":
					$TEAMkeystotal = "Atlanta";
					break;
				case "HEIG":
					$TEAMkeystotal = "CIG";
					break;
				case "Central":
					$TEAMkeystotal = "Austin and Chicago";
					break;
				case "Large K-14":
					$TEAMkeystotal = "Nor Cal, Baltimore, U St.";
					break;
				case "East":
					$TEAMkeystotal = "Boston and DC/Northeast";
					break;
				case "Mun & Ven Planning":
					$TEAMkeystotal = "Municipal and Venues";
					break;
				case "Charter School":
					$TEAMkeystotal = "Charter School";
					break;				
			}
			$consol_TEAMprojectTimeTotalarray[$TEAMkeystotal] = $TEAMnonptotalval; 
			
			/*$propertotal = round(array_sum($ptotalval));
			$projectperTotalarrayPieChart[$litres] = $propertotal;
			$identifierprojectarrayPieChart  = array($country => $keystotal);
			//echo "<br />";
			//print_r($projectperTotalarrayPieChart['litres']);	
			//print_r($identifierprojectarrayPieChart);	
			$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectperTotalarrayPieChart);
			//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodepro .= json_encode($mergedprojectTimeTotalarrayPieChart).",";
			*/
			
			//print_r($mergedprojectTimeTotalarrayPieChart_encodepro);
		}

		
		
		//echo "<pre>";
		//print_r($consol_TEAMprojectTimeTotalarray);
		//echo "</pre>";
		
		


		//echo "hello Owlrl";
		//echo $iter;
		if($iter == 5){
		
		
			///////////HERE/////////////////////
			//$month = '12';
					
					
			$s += 1;			
			echo "<pre>";
			//print_r($dates[0]['timesheet_date']);
			
			$exp_month = explode('-', date('Y-m-d' , $dates[0]['timesheet_date']));
			$month = $exp_month[1];
			
			$date_nonprojects_months = $arr_output_nonprojects[$val][$month][0]['timesheet_date'];
			//echo "<h1>Date </h1>";		
			
			//echo date('Y-F-d', );
			//print_r(date('Y-F-d' , $dates[0]['timesheet_date']));
			//echo "<br />";
			//print_r(date('Y-F-d', $date_nonprojects_months));
			//echo "<br />";			
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
			
			
			$litres ="litres";
			$country ="country";
			
			foreach($consol_nonprojectTimeTotalarray as $nonkeystotal => $nonptotalval){
				//print_r($nonptotalval);
				//if(empty($nonptotalval)){<div style="float: left;"></div><div style="float: left;">
				//	echo "Hello World";
				//}
				
				$nonptotalval = array_filter($nonptotalval);
				if(!empty($nonptotalval)){
					if($nonkeystotal == '0001'){
					$nonkeystotal = 'Admin';
					// "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == '0001MK'){
						$nonkeystotal = 'Farming';
						//echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == '0001HR'){
						$nonkeystotal = 'HR';
						//echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == 'Sick'){
						$nonkeystotal = 'Personal';
						//echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}elseif($nonkeystotal == 'BEREAV'){
						$nonkeystotal = 'BEREAV';
						//echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}else{
						//echo "<div style=\"float: left;\">".$nonkeystotal.": </div>";
					}
				
				}
					
				//echo "<div style=\"padding-left: 200px;\">".."</div> ";

				$nonpropertotal = ceil(array_sum($nonptotalval));

				$projectpernonTotalarrayPieChart[$litres] = $nonpropertotal;
				$identifiernonprojectarrayPieChart  = array($country => $nonkeystotal);
				$mergednonprojectTimeTotalarrayPieChartnon = array_merge($identifiernonprojectarrayPieChart, $projectpernonTotalarrayPieChart);
				$mergednonprojectTimeTotalarrayPieChart_encoden .= json_encode($mergednonprojectTimeTotalarrayPieChartnon).",";

				//echo "<br />";			
			}
			
			$chartdivv = "chartdivv_non";
			
			$charpiearr_no = $mergednonprojectTimeTotalarrayPieChart_encoden;
			echo "<td><hr>"; 
			echo "<h1>Non-project Time: </h1><br />";
			my_piechart($charpiearr_no, $litres, $country, $chartdivv);
			echo "</td>";
			/*echo "<br />";
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
			//echo "</pre>";/*.$diff.$diff*/
			
			//$nonprojectTimeTotalarrayPieChart[$litres] = $non_project_sumtotal;
			
			
			//$identifiernonprojectarrayPieChart  = array($country => 'Non Projects');
			
			//$mergednonprojectTimeTotalarrayPieChart = array_merge($identifiernonprojectarrayPieChart, $nonprojectTimeTotalarrayPieChart);
			
			//print_r($mergedprojectTimeTotalarrayPieChart);
			//$mergednonprojectTimeTotalarrayPieChart_encode = json_encode($mergednonprojectTimeTotalarrayPieChart);
			
			//$consol_projectTimeTotalarray[$keystotal] = $ptotalval; 
			foreach($consol_projectTimeTotalarray as $ckeystotal => $cptotalval){
				$propertotal = round(array_sum($cptotalval));
				$projectperTotalarrayPieChart[$litres] = $propertotal;
				$identifierprojectarrayPieChart  = array($country => $ckeystotal);
				//echo "<br />";
				//print_r($projectperTotalarrayPieChart['litres']);	
				//print_r($identifierprojectarrayPieChart);	
				$mergedprojectTimeTotalarrayPieChart = array_merge($identifierprojectarrayPieChart, $projectperTotalarrayPieChart);
				//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodepro .= json_encode($mergedprojectTimeTotalarrayPieChart).",";
			}
			$charpiearr = $mergedprojectTimeTotalarrayPieChart_encodepro;		
			
			//echo $charpiearr.", ".$litres.", ".$country;
			$arr = array( "country" => 'Projects', "litres" => 118);
			$arr01 = array("country" => 'Non Projects', "litres" => 60);

			//echo json_encode($nonprojectTimeTotalarrayPieChart);
			$arrenc = json_encode($arr);
			$arrenc01 = json_encode($arr01);
			global  $charpiearrr;
			$charpiearrr = $arrenc.",".$arrenc01;
			$chartdivv = "chartdivv_pro";
			echo "<td><hr>"; 
			echo "<h1>Project Time: </h1><br />";	
			my_piechart($charpiearr, $litres, $country, $chartdivv);
			echo "</td>";
			$diff++;
			$charpiearr = " ";
			//echo $month;
			if($month == '12'){
				//break;
			}

////////////////////////////////////SPHERE TIME ANALYSIS/////////////////////////////////////////////////////////
			foreach($consol_SPHERESprojectTimeTotalarray as $cSPHERESkeystotal => $cSPHERESptotalval){
				$properSPHEREStotal = round(array_sum($cSPHERESptotalval));
				$projectperSPHERESTotalarrayPieChart[$litres] = $properSPHEREStotal;
				$identifierprojectarrayPieChartSPHERES  = array($country => $cSPHERESkeystotal);
				//echo "<br />";
				//print_r($projectperTotalarrayPieChart['litres']);	
				//print_r($identifierprojectarrayPieChart);	
				$mergedprojectTimeTotalarrayPieChartSPHERES = array_merge($identifierprojectarrayPieChartSPHERES, $projectperSPHERESTotalarrayPieChart);
				//echo $mergedprojectTimeTotalarrayPieChart['country'];
			
				$mergedprojectTimeTotalarrayPieChart_encodeproSPHERES .= json_encode($mergedprojectTimeTotalarrayPieChartSPHERES).",";
			}
			$charpiearrSPHERES = $mergedprojectTimeTotalarrayPieChart_encodeproSPHERES;


			$chartdivv = "chartdivv_spheres";
			echo "</tr><tr>";
			echo "<td><hr>"; 
			echo "<h1>Spheres Time Analysis </h1>";
			//echo $pTotalSpheres;
			my_piechart($charpiearrSPHERES, $litres, $country, $chartdivv);
			echo " ";	 
			//echo round($sum_total);
			echo "</td>";
////////////////////////////////////////////////////SPHERE TIME ANALYSIS END//////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////TEAM TIME ANALYSIS////////////////////////////////////////////////////////////////////////////////
				foreach($consol_TEAMprojectTimeTotalarray as $cTEAMkeystotal => $cTEAMptotalval){
					$properTEAMtotal = round(array_sum($cTEAMptotalval));
					$projectperTEAMTotalarrayPieChart[$litres] = $properTEAMtotal;
					$identifierprojectarrayPieChartTEAM  = array($country => $cTEAMkeystotal);
					//echo "<br />";
					//print_r($projectperTotalarrayPieChart['litres']);	
					//print_r($identifierprojectarrayPieChart);	
					$mergedprojectTimeTotalarrayPieChartTEAM = array_merge($identifierprojectarrayPieChartTEAM, $projectperTEAMTotalarrayPieChart);
					//echo $mergedprojectTimeTotalarrayPieChart['country'];

					$mergedprojectTimeTotalarrayPieChart_encodeproTEAM .= json_encode($mergedprojectTimeTotalarrayPieChartTEAM).",";
				}
				$charpiearrTEAM = $mergedprojectTimeTotalarrayPieChart_encodeproTEAM;
				//echo $charpiearr.", ".$litres.", ".$country;
				$arrr = array( "country" => 'Projects', "litres" => 118);
				$arrr01 = array("country" => 'Non Projects', "litres" => 60);
	
				//echo json_encode($nonprojectTimeTotalarrayPieChart);
				$arrrenc = json_encode($arrr);
				$arrrenc01 = json_encode($arrr01);
				global  $charpiearrr;
				$charpiearrrr = $arrrenc.",".$arrrenc01;

			$chartdivv = "chartdivv_team";
			echo "<td><hr>"; 
			echo "<h1>Team Time Analysis </h1>";
			//echo $pTotalSpheres;
			my_piechart($charpiearrTEAM, $litres, $country, $chartdivv);
			echo " ";	 
			//echo round($sum_total);
			echo "</td>";
			// 
		}
	}		
	echo "<pre>";
	//print_r($arr_output_nonprojects[$val][$dates]['timesheet_id']);
	echo "</pre>";
	break;
	
}	
echo "</tr></table>";
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
<?php
function my_piechart($charpiearr, $litres, $country, $chartdivv){

?>

<!-- Styles -->
<style>
<?php echo "#".$chartdivv; ?>{
  width: 125%;
  height: 700px;
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

var chart = am4core.create(<?php echo $chartdivv; ?>, am4charts.PieChart3D);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

/*chart.legend = new am4charts.Legend();
chart.legend.position = "right";
chart.legend.scrollable = true;
chart.legend.width = 120;
chart.legend.labels.template.truncate = true;
chart.legend.labels.template.wrap = true;*/
chart.data = [

	<?php echo $charpiearr ?>

];

var series = chart.series.push(new am4charts.PieSeries3D());
var country = <?php echo "\"$country\""; ?>;
var litres = <?php echo "\"$litres\""; ?>;
series.dataFields.value = 'litres';
//series.dataFields.category = 'country';

series.ticks.template.disabled = true;
series.alignLabels = false;
//series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
series.labels.template.radius = am4core.percent(-40);
series.labels.template.fill = am4core.color("white");

// Set up tooltips
series.tooltip.label.interactionsEnabled = true;
series.tooltip.keepTargetHover = true;
series.slices.template.tooltipHTML = '<b>{country}</b><br><a href="{category.urlEncode()}">More info</a>';

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
<div id=<?php echo $chartdivv; ?>></div>
<? } ?>