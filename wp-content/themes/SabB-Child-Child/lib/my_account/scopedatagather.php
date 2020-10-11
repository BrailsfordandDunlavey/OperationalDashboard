 <?php

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

    print_r($_GET);

    $entries = FrmEntry::getAll(array('it.form_id' => 6), ' ORDER BY it.created_at DESC', 8);
    echo "<pre>";
    //
    echo "</pre>";
    add_action('frm_trigger_my_action_name_create_action', 'my_create_action_trigger', 10, 3);
    function my_create_action_trigger($action, $entry, $form) {
        print_r($entry);
        echo "Hello World";
         // Do some magic
    }
    my_create_action_trigger($action, $entry, $form);

    FrmProEntriesController::get_field_value_shortcode(array('field_id' => 6, 'entry' => $entry_id));

    $entries = FrmProEntriesController::get_field_value_shortcode(array('field_id' => 6, 'user_id' => 'current'));
    print_r($entries);
    $entries = FrmProEntriesController::get_field_value_shortcode(array('field_id' => 9, 'user_id' => 'current'));
?>