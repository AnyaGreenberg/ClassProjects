<?php
require_once("../db_functions.php");

session_id("adopter");
session_start();

if( !isset( $_SESSION['user_id'] ) ) {
    header("Location: login.php");
}

?>

<html>
<head>
<style>
table, th, td {
	border-bottom: 1px solid #ddd;
}

ul {
  	list-style-type: none;
  	margin: 0;
  	padding: 0;
}

li {
  	display: inline;
}
</style>
</head>

<body>
	<h1> Adopter Center </h1>
<ul>
	<li><a href="../home.php">Return to Homepage</a></li> <br>
	<li><a href="logoff.php">Log Off</a></li>
</ul>
	<h3> Your Adoption Requests </h3>
If you would like to retract a request, please contact the shelter which currently has the pet. 
    	<table class="table">
        <thead>
        <tr>
		<th scope="col">Pet ID</th>
            	<th scope="col">Image</th>
		<th scope="col">Pet Name</th>
            	<th scope="col">Pet Type</th>
            	<th scope="col">Breed</th>
            	<th scope="col">Age</th>
            	<th scope="col">Origin</th>
            	<th scope="col">Neutered/Spade</th>
            	<th scope="col">Diet</th>
		<th scope="col">Personality</th>
		<th scope="col">Shelter Location</th>
        </tr>
        </thead>
        <tbody>
       	<?php
     	  	generate_pets_info_adopter($_SESSION['user_ssn']);
      	?>
        </tbody>
    	</table>
</body>
</html>

