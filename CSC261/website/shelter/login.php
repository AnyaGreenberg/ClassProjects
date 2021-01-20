<?php
require_once("../db_functions.php");
session_id("shelter");
session_start();

if ( ! empty( $_POST ) ) {
    $conn = database_connect();

    $query = "SELECT Email, Password FROM USERS  WHERE User_type = 'ADMIN' AND Email = '" . $_POST['email'] . "';";
    //echo $query;
	$aid = "SELECT Admin_id FROM ADMIN_EMAIL WHERE Email = '" . $_POST['email'] . "';";
    $qry = $conn->prepare($query);
	$qaid = $conn->prepare($aid);
    $qry->execute();
	$qaid->execute();
    if ($qry->errorCode() != 0 && $qaid->errorCode() != 0) {
        print_r($qry->errorInfo());
    }
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
	$raid = $qaid->fetchAll(PDO::FETCH_ASSOC);
    //echo $result[0];

    if(sizeof($result) == 1 && sizeof($raid) == 1) {
        if(hash_function($_POST['password']) == $result[0]['Password']) {
            	$_SESSION['user_id'] = $result[0][Email];
		$_SESSION['admin_id'] = $raid[0][Admin_id];
            	header("Location: viewPets.php");
        }else{
            echo "Wrong email and password combination..";
        }
    } else{
        echo "Wrong email and password combination.";
    }

	$aid = "SELECT Admin_id FROM ADMIN_EMAIL WHERE Email='" . $_POST['email'] . "';";
	if ($conn->query($aid) === TRUE) {
		$_SESSION['aid'] = mysqli_fetch_assoc($aid);
	} else { 		
		echo "Error: " . $aid . "<br>" . $conn->error;
	}
}

?>

<html>
<body>

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
<ul>
    <li><a href="signup.php">Sign up</a></li> <br>
	<li><a href="../home.php">Return to Homepage</a></li>
</ul>

<h2> Login  </h2>

<form action="" method="post">
    <input type="text" name="email" placeholder="Enter your email" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    <input type="submit" value="Submit">
</form>

</body>
</html>
