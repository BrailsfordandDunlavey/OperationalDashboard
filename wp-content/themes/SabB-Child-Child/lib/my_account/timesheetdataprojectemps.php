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
 
echo "<table style=\"table-layout: fixed; width: 130%\"><tr><td>";

echo "<h1>Choose The Roll up Level</h1><br>";
echo '<button onclick="myFunction()">Teams</button>
<button onclick="myFunction_Spheres()">Spheres</button>
<button onclick="myFunction_Company()">Company</button>


<div id="Company">
<h1>Company Level</h1>
<button type="button" class="collapsible">Open Company View</button>
	<div class="content">
	 ';
	require_once "timesheetdataprojectmanagersteamcompany.php";
	echo '</div>
</div>



<div id="spheres">
<h1>Spheres Level</h1>
<button type="button" class="collapsible">Open Sphere View</button>
	<div class="content">
	 ';
	//require_once "timesheetdataprojectmanagersteamSpheres.php";
	echo '</div>
</div>


<div id="Teams">
<h1>Team Level</h1>
	<button type="button" class="collapsible">Open Team View</button>
	<div class="content">
	';
	require "timesheetdataprojectmanagersteams.php";
	echo '
	</div>

	<button type="button" class="collapsible">Open Team Member View</button>
	<div class="content">
	 '; 
	require "timesheetdataprojectmemberteam.php";
	echo '
	</div>
</div>

<style>
/* Style the button that is used to open and close the collapsible content */
.collapsible {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
.active, .collapsible:hover {
  background-color: #ccc;
}

/* Style the collapsible content. Note: hidden by default */
.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
  
  
}

#spheres{
	display:none;
}
#Company{
	display:none;
}
#Teams{
	display:none;
}
</style>

<script>

function myFunction() {
  var x = document.getElementById("Teams");
  if (x.style.display === "none") {
	x.style.display = "block";
	
  } else {
    x.style.display = "none";
  }
}
function myFunction_Spheres() {
var s = document.getElementById("spheres");
if (s.style.display === "none") {
  s.style.display = "block";';
  require_once "timesheetdataprojectmanagersteamSpheres.php";
echo '} else {
  s.style.display = "none";
}
}

function myFunction_Company() {
	var c = document.getElementById("Company");
	if (c.style.display === "none") {
	  c.style.display = "block";
	} else {
	  c.style.display = "none";
	}
	}


	var coll = document.getElementsByClassName("collapsible");
	var i;
	
	for (i = 0; i < coll.length; i++) {
	  coll[i].addEventListener("click", function() {
		this.classList.toggle("active");
		var content = this.nextElementSibling;
		if (content.style.display === "block") {
		  content.style.display = "none";
		} else {
		  content.style.display = "block";
		}
	  });
	}
</script>

</td></tr>';

echo "</table>";
 