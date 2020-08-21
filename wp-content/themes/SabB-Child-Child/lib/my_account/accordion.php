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
    //echo $current_user_id;
    //global $wpdb;WHERE display_name LIKE '%$executivearr[0]%'
        $executiveLaura = array();
                  $table_namepre = 'users';
                  $table_name_namepre = $wpdb->prefix . $table_namepre;	
                  $result_namepre = $wpdb->get_results(
                     "SELECT * FROM $table_name_namepre");   
                 $nameIDpre = $result_namepre[0]->ID;
                 //print_r(count($result_namepre));
                 //echo $nameIDpre;
                 
                 for($i = 0; $i < count($result_namepre); $i++){
                    // echo "<br />";
                   // echo $result_namepre[$i]->ID;
                  //  echo $result_namepre[$i]->display_name;
                  if($result_namepre[$i]->display_name == 'admin' || $result_namepre[$i]->display_name == 'bbannister'){
                    
                  }else{
                      $executiveLaura[$result_namepre[$i]->ID] = $result_namepre[$i]->display_name;
                  }
                    
                 }/**/

     $executivePaul = array(39 => "Brad Noyes", 40 => "Jeff Turner", 67 => "Joe Winters", 77 => "Carrie Rollman", 54 => "Katie Karp", 70 => "Ryan Jensen", 42 => "Kim Martin", 48 => "Matt Bohannon", 218 => "Chet Roach");
     $executiveChris = array(58 => "Will Mangrum", 52 => "Bill Mykins", 53 => "Sanath Kalidas", 180 => "Mark Newton", 112 => "Deisy Brangman", 215 => "Jeff Bonvechio");

     $executiveBradJeff =  array(67 => "Joe Winters", 77 => "Carrie Rollman", 54 => "Katie Karp", 70 => "Ryan Jensen", 42 => "Kim Martin", 48 => "Matt Bohannon", 218 => "Chet Roach");

     $executiveWill = array(52 => "Bill Mykins", 53 => "Sanath Kalidas", 180 => "Mark Newton", 112 => "Deisy Brangman", 215 => "Jeff Bonvechio");
    
     //$executiveLaura = array(52 => "Bill Mykins", 53 => "Sanath Kalidas", 180 => "Mark Newton", 112 => "Deisy Brangman", 215 => "Jeff Bonvechio", 67 => "Joe Winters", 77 => "Carrie Rollman", 54 => "Katie Karp", 70 => "Ryan Jensen", 42 => "Kim Martin", 48 => "Matt Bohannon", 218 => "Chet Roach");


    function ecxecPeople($executivearr){
        echo "<details><summary>Executive</summary>";
        //print_r($executivearr);
        foreach($executivearr as $keyPre => $valPre){
           // $valPre = trim($valPre);
           // 
                 echo "<p><a href=/wp-opdash/dashboard/?current_user=$keyPre>$valPre</a></p>";
        }        
        echo "</details>";
    }
    

    $table_team = 'team';
	$table_name_team = $wpdb->prefix . $table_team;	
	$result_team = $wpdb->get_results(
		"SELECT * FROM $table_name_team		    
            WHERE Leader_cluster LIKE '%$author_obj->display_name%'");   
   
    if($current_user_id == "37"){
        
        ecxecPeople($executivePaul);

    }elseif($current_user_id == "38"){
        ecxecPeople($executiveChris);
    }elseif($current_user_id == "39" || $current_user_id == "40"){
        ecxecPeople($executiveBradJeff);
    }elseif($current_user_id == "58"){
        ecxecPeople($executiveWill);
    }elseif($current_user_id == "103"){
        ecxecPeople($executiveLaura);
    }else{
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
                $staff_name = trim($val_staff->Name);
               // print_r($result_name[0]);
               // print_r($staff_name);
                
                $table_name = 'users';
                $table_name_name = $wpdb->prefix . $table_name;	
                $result_name = $wpdb->get_results(
                    "SELECT * FROM $table_name_name		    
                        WHERE display_name LIKE '%$staff_name%'");

   
                 $nameID = $result_name[0]->ID;
                 echo "<p><a href=/wp-opdash/dashboard/?current_user=$nameID>$staff_name</a></p>";
            }             
            //echo "</div>";
            echo "</details>";
    }
    
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
