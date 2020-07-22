<style>

details {
    border: 1px solid #aaa;
    border-radius: 4px;
    padding: .75em .75em 0;
    width: 200%;
}

summary {
    font-weight: bold;
    margin: -.75em -.75em 0;
    padding: .75em;
    background-color: steelblue;
    color: white;
}

details[open] {
    padding: .75em;
}

details[open] summary {
    border-bottom: 1px solid #aaa;
    margin-bottom: .75em;
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
    echo "<details><summary>$team</summary>";
      // echo"<button class='accordion'>$team</button>
            //<div class='panel'>
           // ";
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
            //echo "</div>";
            echo "</details>";
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
