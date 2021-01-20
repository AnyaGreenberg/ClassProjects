<?php
require_once("../db_functions.php");

session_id("shelter");
session_start();

if( !isset( $_SESSION['user_id'] ) && !isset($_SESSION['admin_id']) ) {
    header("Location: login.php");
}

function adoption_requests() {
    	$conn = database_connect();
    	if ($conn) {
        	$query = "SELECT Pet_id, Pname, Ptype, Breed, Age_year, Age_month, Origin, First_name, Last_name, Email
                	FROM ADOPTION_REQUESTS
                		NATURAL JOIN PET_INFO
                		NATURAL JOIN ADOPTER
                		NATURAL JOIN BREED
                		NATURAL JOIN ADOPTER_EMAIL
			WHERE Admin_id='$_SESSION[admin_id]'
                	ORDER BY Pet_id ASC;";
        	$qry = $conn->prepare($query);
        	$qry->execute();
        	if ($qry->errorCode() != 0) {
            		print_r($qry->errorInfo());
        	} else {
            		return $qry->fetchAll(PDO::FETCH_ASSOC);
        	}
    	} else {
        	echo "Error: "  . $conn->error;
    	}
}

function adpotion_requests_info() {
    	foreach(adoption_requests() as $adoption) {
        	print '<tr>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Pet_id']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Pname']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Ptype']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Breed']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Age_year']) . ' Years, ' . ($adoption['Age_month']) . ' Months' .  '</td>';
       	 	print '<td scope="row" style="text-align:center">' . ($adoption['Origin']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['First_name']) . ' ' . ($adoption['Last_name']) . '</td>';
        	print '<td scope="row" style="text-align:center">' . ($adoption['Email']) . '</td>';
		print '<td scope="row"> <a href="../pets/deleterequest.php?ref=' . ($adoption['Email'])  . '"> <button type="button">Remove Request</button>  </td>';
        	print '</tr>';
    	}
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
     	<h1> Shelter Center </h1>
<ul>
   	<li><a href="viewPets.php">Pets at Your Shelter</a></li> <br>
	<li><a href="../home.php">Return to Homepage</a></li> <br>
    	<li><a href="logoff.php">Log Off</a></li>
</ul>
	<h2> Adoption Requests for Pets at Your Shelter</h2>
    	<table class="table">
        <thead>
        <tr>
            	<th scope="col">Pet ID</th>
		<th scope="col">Pet Name</th>
            	<th scope="col">Pet Type</th>
            	<th scope="col">Breed</th>
            	<th scope="col">Age</th>
            	<th scope="col">Origin</th>
            	<th scope="col">Requester's Name</th>
            	<th scope="col">Requester's Email</th>
		<th scope="col"></th>
        </tr>
        </thead>
        <tbody>
            <?php
                adpotion_requests_info();
            ?>
        </tbody>
    	</table>
</body>
</html>

