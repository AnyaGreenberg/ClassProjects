<?php
require_once("../db_functions.php");

session_id("shelter");
session_start();

if( !isset( $_SESSION['user_id'] ) && !isset($_SESSION['admin_id'])) {
    header("Location: ../shelter/login.php");
}

if(!empty($_POST)) {
	$conn = database_connect();

	$sql1 = "DELETE FROM BREED WHERE Pet_id=" ."'" . $_GET['ref'] . "';";
	$sql2 = "DELETE FROM PET_IMAGES WHERE Pet_id=" . "'" . $_GET['ref'] . "';";
	$sql3 = "DELETE FROM ADOPTION_REQUESTS WHERE Pet_id=" . "'" . $_GET['ref'] . "';";
	$sql4 = "DELETE FROM PET_INFO WHERE Pet_id=" . "'" . $_GET['ref'] . "';";

	$qry1 = $conn->prepare($sql1);
	$qry2 = $conn->prepare($sql2);
	$qry3 = $conn->prepare($sql3);
	$qry4 = $conn->prepare($sql4);

	$qry1->execute();
	$qry2->execute();
	$qry3->execute();
	$qry4->execute();

	if($qry1->errorCode() != 0) {
		print_r($qry1->errorInfo());
	} else {
		header("Location: ../shelter/viewPets.php");
	}

	if($qry2->errorCode() != 0) {
		print_r($qry2->errorInfo());
	} else {
		header("Location: ../shelter/viewPets.php");
	}

	if($qry3->errorCode() != 0) {
		print_r($qry3->errorInfo());
	} else {
		header("Location: ../shelter/viewPets.php");
	}

	if($qry4->errorCode() != 0) {
		print_r($qry4->errorInfo());
	} else {
		//header("Location: ../shelter/viewPets.php");
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

<h2> Are you sure you want to permanently remove this pet from the database? </h2>
Removing this pet from the database will remove all information connected to it including pet information, images, and adoption requests for this pet.

<form action="" method="post">
	<label for="confirm" style="display:block"><b>Confirm Deletion of Pet ID <?php echo $_GET['ref']; ?> From Database?</b></label>
	<input type="radio" name="confirm" value="TRUE" required>Yes<br>
	<input type="submit" style="display:block" value="Remove Pet">
</form>

</body>
</html>
 


