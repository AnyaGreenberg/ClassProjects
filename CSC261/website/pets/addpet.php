<!DOCTYPE html>
<html>
<head>
</head>
<body>

<h1> Shelter Center </h1>

<?php
require_once("../db_functions.php");

session_id("shelter");
session_start();

if( !isset( $_SESSION['user_id'] ) && !isset($_SESSION['admin_id']) ) {
    header("Location: ../shelter/login.php");
}

if( ! empty($_POST) ){
	$conn = database_connect();

	$query_id = "SELECT MAX(Pet_id) + 1 AS id FROM PET_INFO;";
        $qry_id = $conn->prepare($query_id);
        $qry_id->execute();
        $id = $qry_id->fetchAll(PDO::FETCH_ASSOC)[0]['id'];

	$query0 = "INSERT INTO PET_INFO(Pet_id, Ptype, Pname, Age_year, Age_month, Origin, Neutered_spade, Diet, Personality, Admin_id) VALUES("
		. "" . $id . ","	
		. "'" . $_POST['type'] . "',"
                . "'" . $_POST['name'] . "',"
                . "" . $_POST[agey] . ","
                . "" . $_POST[agem] . ","
                . "'" . $_POST['origin'] . "',"
                . "" . $_POST['ns'] . ","
                . "'" . $_POST['diet'] . "',"
		. "'" . $_POST['pers'] . "',"
		. "" . $_SESSION[admin_id] . ""
                . ");";
	
	if ( ! empty ($_POST['breed3']) ) {
		$query1 = "INSERT INTO BREED(Pet_id, Breed) VALUES("
			. "" . $id . ","
			. "'" . $_POST['breed1'] . "'"
			."),"
			. "(" . $id . ","
                        . "'" . $_POST['breed2'] . "'"
                        ."),"
			. "(" . $id . ","
                        . "'" . $_POST['breed3'] . "'"
                        .");";
	} elseif ( ! empty($_POST['breed2']) ) {
		$query1 = "INSERT INTO BREED(Pet_id, Breed) VALUES("
                        . "" . $id . ","
                        . "'" . $_POST['breed1'] . "'"
                        ."),"
                        . "(" . $id . ","
                        . "'" . $_POST['breed2'] . "'"
                        .");";
	} else {
		$query1 = "INSERT INTO BREED(Pet_id, Breed) VALUES("
                        . "" . $id . ","
                        . "'" . $_POST['breed1'] . "'"
                        .");";
	}

	$qry0 = $conn->prepare($query0);
       	$qry1 = $conn->prepare($query1);

	$qry0->execute();
       	$qry1->execute();

	if ($qry0->errorCode() != 0 || $qry1->errorCode() != 0) {
	        print_r($qry0->errorInfo());
		print_r($qry1->errorInfor());
       	}else{
           	echo("Sucessfully added pet.");
       	}
} else {
       	echo "\r\nInvalid input. Please re-enter.";
}

?>

      	<h2> Query Sent </h2>

<h3> Would you like to add an image of the pet? </h3>
	<a href="upload.html"><button type="button">Yes!</button></a> <br>
	<a href="addpet.html"><button type="button">No, but I want to add another pet.</button></a> <br>
	<a href="../shelter/viewPets.php"><button type="button">No, take me back to my shelter's pets.</button></a> <br>
	<a href="../home.php"><button type="button">No, take me back to the homepage.</button></a>
</body>
</html>
