<style>

 /* Style the buttons that are used to open and close the accordion panel */
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 200%;
  text-align: left;
  border: none;
  outline: none;
  transition: 0.4s;
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
.active, .accordion:hover {
  background-color: #ccc;
}

/* Style the accordion panel. Note: hidden by default */
.panel {
  padding: 0 18px;
  background-color: white;
  display: none;
  overflow: hidden;
}
</style>

<?php 
    $current_user_id = get_current_user_id();
    $author_obj = get_user_by('id', $current_user_id);
   // echo $current_user;
    //global $wpdb;

    $table_team = 'team';
	$table_name_team = $wpdb->prefix . $table_team;	
	$result_team = $wpdb->get_results(
		"SELECT * FROM $table_name_team		    
            WHERE Leader_cluster LIKE '%$author_obj->display_name%'");   


    foreach($result_team as $key_team => $val_team){
       $team = $val_team->Cluster;
       
       $table_staff = 'staff';
	   $table_name_staff = $wpdb->prefix . $table_staff;	
	   $result_staff = $wpdb->get_results(
		"SELECT * FROM $table_name_staff		    
            WHERE Team LIKE '%$team%'");  
    
       echo"<button class='accordion'>$team</button>
            <div class='panel'>
            ";
            foreach($result_staff as $key_staff => $val_staff){
                $staff_name = $val_staff->Name;
                //print_r(explode($staff_name));
                
                $table_name = 'users';
                $table_name_name = $wpdb->prefix . $table_name;	
                $result_name = $wpdb->get_results(
                    "SELECT * FROM $table_name_name		    
                        WHERE display_name LIKE '%$staff_name%'");

   
                 $nameID = $result_name[0]->ID;
                 echo "<p><a href=http://localhost/wp-opdash/dashboard/?current_user=$nameID>$staff_name</a></p>";
            }             
            echo "</div>";
    }
?>


<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}

</script>
