<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1> Shelter Center </h1>
<a href="../shelter/viewPets.php">Return to Shelter Center</a> <br>
<a href="../home.php">Return to Homepage</a> <br>
<h3> Query Sent </h3>

<?php
require_once("../db_functions.php");

session_id("shelter");
session_start();

if( !isset( $_SESSION['user_id'] ) ) {
    header("Location: ../shelter/signup.php");
}

$target_dir = "../images/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if(isset($_POST["submit"])) {
   	$check = getimagesize($_FILES["image"]["tmp_name"]);
    	if($check !== false) {
        	echo "File is an image - " . $check["mime"] . "." . '<br>'; 
        	$uploadOk = 1;
    	} else {
        	echo "File is not an image." . '<br>';
        $uploadOk = 0;
    	}	
}

if (file_exists($target_file)) {
   	echo "Sorry, file already exists." . '<br>';
    	$uploadOk = 0;
}

if ($_FILES["image"]["size"] > 500000) {
    	echo "Sorry, your file is too large." . '<br>';
    	$uploadOk = 0;
}

if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    	echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed." . '<br>';
    	$uploadOk = 0;
}

if ($uploadOk == 0) {
    	echo "Sorry, your file was not uploaded." . '<br>';
} else {
    	if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        	echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded." . '<br>';
    	} else {
        	echo "Sorry, there was an error uploading your file." . '<br>';
    	}
}


$conn=database_connect();

$sql = "INSERT INTO PET_IMAGES(Pet_id,Image) VALUES("
                . "" . $_POST['petid'] . ","
                . "'" . $_FILES["image"]["name"] . "');";

$qry=$conn->prepare($sql);

$qry->execute();

if($query->errorCode() != 0) {
        print_r($qry->errorInfo());
} else {
        echo "Image successfully added to database.";
}
?>

</body>
</html>
