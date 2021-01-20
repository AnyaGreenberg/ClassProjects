<?php
require_once("../db_functions.php");

session_id("adopter");
session_start();

if( !isset( $_SESSION['user_id'] ) ) {
    header("Location: signup.php");
}

//echo $_GET['ref'];

if ( ! empty( $_POST ) ) {
    $conn = database_connect();

    $query0 = "INSERT INTO ADOPTION_REQUESTS (Pet_id, Ssn)  VALUES("
                   . "'" . $_GET['ref'] . "',"
                   . "'" . $_SESSION['user_ssn'] . "'"
                   . ");";

    $qry0 = $conn->prepare($query0);
    $qry0->execute();
    if ($qry0->errorCode() != 0) {
        echo "System error.";
    }else{
        header("Location: viewAdopterRequests.php");
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
     <h1> Apply For Adoption </h1>
<ul>
    <li><a href="../home.php">Shelter Center</a></li> <br>
	<li> <a href="viewAdopterRequests.php">View Your Requests</a></li> <br>
    <li><a href="logoff.php">Log Off</a></li>
</ul>
    <h2> Adoption Requests  </h2>

    <form id="frm" action="" method="post">
    <label for="confirm" style="display:block"><b>Confirm Adoption For Pet ID 
	<?php 
		echo $_GET['ref']; 
	?>?</b></label>
    <input type="radio" name="confirm" value="True" required>Yes<br>
    <input type="submit" style="display:block"  value="Confirm">
    </form>

</body>
</html>
