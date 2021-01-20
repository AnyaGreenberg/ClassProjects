<?php
require_once("../db_functions.php");

session_id("shelter");
session_start();

if( !isset( $_SESSION['user_id'] )) {
    header("Location: ../shelter/login.php");
}

if(!empty($_POST)) {
	$conn = database_connect();

	$query_ssn = "SELECT Ssn AS ssn FROM ADOPTER_EMAIL WHERE Email=" . "'" . $_GET['ref'] . "';";
	$qry_ssn = $conn->prepare($query_ssn);
	$qry_ssn->execute();
	$ssn = $qry_ssn->fetchAll(PDO::FETCH_ASSOC)[0]['ssn'];

	$sql1 = "DELETE FROM ADOPTION_REQUESTS WHERE Ssn=" . $ssn . " AND Pet_id='" . $_POST['petid'] . "';";
	$qry1 = $conn->prepare($sql1);
	$qry1->execute();
	if($qry1->errorCode() != 0) {
		print_r($qry1->errorInfo());
	} else {
		header("Location: ../shelter/viewShelterRequests.php");
	}
}
?>

<html>
<head>
<style>
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

<a href="../shelter/viewPets.php">Return to Shelter Center</a> <br>
<a href="../shelter/viewShelterRequests.php">Return to Adoption	Requests</a> <br>
<a href="deleterequest.html">Remove Another Request</a>

<h2> Are you sure you want to permanently remove this request from the database? </h2>
Removing this request from the database will only remove the request and will not affect pet information or adopter information beyond the request.

<form action="" method="post">
	<label for="confirm" style="display:block"><b>Confirm Deletion of Request From Database?</b></label>
	If so, enter the pet ID associated with <?php echo $_GET['ref']; ?> for the request you would like to delete. <br>
	<input type=number placeholder="Pet ID" name="petid" min="1" required> 
	<input type="submit" style="display:block" value="Remove Pet">
</form>

</body>
</html>
 


