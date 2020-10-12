 <?php
    session_start();
    
//include( 'PLUGIN_ROOT_DIR' . 'formidable/classes/models/FrmEntry.php');
//include('../../plugins/formidable/classes/models/FrmEntry.php');
if ( !defined('ABSPATH') ) {
    //If wordpress isn't loaded load it up.
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wpopdash/wp-load.php';
}
$plugin_dir_path = plugin_dir_path( __FILE__ );
include( $plugin_dir_path . 'plugins/formidable/classes/models/FrmEntry.php');
//echo $plugin_dir_path;
    //echo "Hello World";

    //print_r($_GET);

    $entries = FrmEntry::getAll(array('it.form_id' => 6), ' ORDER BY it.created_at DESC', 8);
    //echo "<pre>";
    //
    //echo "</pre>";
    add_action('frm_trigger_my_action_name_create_action', 'my_create_action_trigger', 10, 3);
    function my_create_action_trigger($action, $entry, $form) {
        print_r($entry);
        echo "Hello World";
         // Do some magic
    }
    my_create_action_trigger($action, $entry, $form);

    FrmProEntriesController::get_field_value_shortcode(array('field_id' => 6, 'entry' => $entry_id));

    $entries = FrmProEntriesController::get_field_value_shortcode(array('field_id' => 6, 'user_id' => 'current'));
    //print_r($entries);
    $entries = FrmProEntriesController::get_field_value_shortcode(array('field_id' => 9, 'user_id' => 'current'));

    global $wpdb;
    $table = 'frm_item_metas';	
	$table_name = $wpdb->prefix . $table;	
	$result = $wpdb->get_results(
		"SELECT * FROM $table_name");
            
    //echo "<pre>";
    //print_r($result);
    foreach($result as $ekey => $eval){
       // echo "<br />";
         //print_r($eval->meta_value);
         switch ($eval->field_id) {
          case 28:
                $pa_arr[] = $eval->meta_value * 200;
                $pa_arr_two[] = $eval->meta_value;
                break;
            case 31:
                $apm_arr[] = $eval->meta_value * 210;
                $apm_arr_two[] = $eval->meta_value;
                break;
            case 33:
                $pm_arr[] = $eval->meta_value * 240;
                $pm_arr_two[] = $eval->meta_value;
                break;
            case 35:
                $vp_arr[] = $eval->meta_value * 360;
                $vp_arr_two[] = $eval->meta_value;
                break;                
            case 36:
                $evp_arr[] = $eval->meta_value * 455;
                $evp_arr_two[] = $eval->meta_value;
                break;            

             /* 
                case 14:
                    $pa_arr[] = $eval->meta_value * 200;
                    $pa_arr_two[] = $eval->meta_value;
                    break;
                case 15:
                    $apm_arr[] = $eval->meta_value * 210;
                    $apm_arr_two[] = $eval->meta_value;
                    break;
                case 21:
                    $pm_arr[] = $eval->meta_value * 240;
                    $pm_arr_two[] = $eval->meta_value;
                    break;
                case 22:
                    $vp_arr[] = $eval->meta_value * 360;
                    $vp_arr_two[] = $eval->meta_value;
                    break;                
                case 24:
                    $evp_arr[] = $eval->meta_value * 455;
                    $evp_arr_two[] = $eval->meta_value;
                    break;*/
                
        }
    }
  //  echo "</pre>";

    //print_r($pa_arr_two);
   // 
    
    for($i = 0; $i < sizeof($pa_arr); $i++){
        $summed_entries[] = $pa_arr[$i] + $apm_arr[$i] + $vp_arr[$i] + $evp_arr[$i] + $epa_arr[$i] + $pm_arr[$i];       
    }
    
    //$total_fees = array_sum($summed_entries);
   // print_r($summed_entries);

    $_SESSION['summed_entries'] = $summed_entries;    
    $_SESSION['pa_arr_two'] = $pa_arr_two;
    $_SESSION['apm_arr_two'] = $apm_arr_two;
    $_SESSION['pm_arr_two'] = $pm_arr_two;
    $_SESSION['vp_arr_two'] = $vp_arr_two;
    $_SESSION['evp_arr_two'] = $evp_arr_two;

    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo $actual_link;
    echo "Hello World";
   // header("Location: /scopes-information/");
    
?>