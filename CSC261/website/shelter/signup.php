<?php
require_once("../db_functions.php");

function validate_input($arr) {
    foreach($arr as $a) {
        if(strlen($a) >255) {
            echo "Input size must be less than 255 characters.";
            return false;
        }
    }
    return true;
}

function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        return false;
    }

    $conn = database_connect();
    $query = "SELECT Email FROM USERS WHERE Email = '" . $email . "';";
    $qry = $conn->prepare($query);
    $qry->execute();
    if ($qry->errorCode() != 0) {
        print_r($qry->errorInfo());
    }
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    if(sizeof($result) > 0) {
        echo "Email address already exists in the system.";
        return false;
    }

    return true;
}

function validate_password($psw, $rpsw) {
    if (strlen($psw)<6) {
        echo "Password should be at least 6 character.";
        return false;
    }

    if ($psw != $rpsw) {
        echo "Passwords do not match.";
        return false;
    }

    return true;
}

if ( ! empty( $_POST ) ) {
    if( validate_input($_POST) 
        && validate_email($_POST['email'])
        && validate_password($_POST['psw'], $_POST['rpsw'])
    ) {
        $conn = database_connect();
        
        $query_id = "SELECT MAX(Admin_id) + 1 AS id FROM ADMINS;";
        $qry_id = $conn->prepare($query_id);
        $qry_id->execute();
        $id = $qry_id->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
        
        $query0 = "INSERT INTO USERS (Email, Password, User_type)  VALUES("
                   . "'" . $_POST['email'] . "',"
                   . "'" . hash_function($_POST['psw']) . "',"
                   . "'ADMIN'"
                   . ");";

        $query1 = "INSERT INTO ADMINS (Admin_id, Name_of_shelter) VALUES("
                   . "'" . $id . "',"
                   . "'" . $_POST['sname'] . "'"
                   . ");";

        $query2 = "INSERT INTO ADMIN_EMAIL (Admin_id, Email)  VALUES("
                   . "'" . $id . "',"
                   . "'" . $_POST['email'] . "'"
                   . ");";

        $query3 = "INSERT INTO ADMIN_ADDRESS (Admin_id, Address)  VALUES("
                   . "'" . $id . "',"
                   . "'" . $_POST['address'] . "'"
                   . ");";
        
       $qry0 = $conn->prepare($query0);
       $qry1 = $conn->prepare($query1);
       $qry2 = $conn->prepare($query2);
       $qry3 = $conn->prepare($query3);

       $qry0->execute();
       $qry1->execute();
       $qry2->execute();
       $qry3->execute();

       if ($qry0->errorCode() != 0 || $qry1->errorCode() != 0 || $qry2->errorCode() != 0 || $qry3->errorCode() != 0) {
           echo "System error.";
       }else{
           header("Location: login.php");
           echo("Sucessfully signed up.");
       }
    } else {
        echo "\r\nInvalid input. Please re-enter.";
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
    <li><a href="login.php">Already have an account? Please login</a></li> <br>
	<li><a href="../home.php">Return to Homepage</a></li>
</ul>

<h2> Sign Up  </h2>

<form id="frm" action="" method="post">
    <label for="email" style="display:block"><b>Email</b></label>
    <input type="text"  placeholder="Enter Email" name="email" required>
<br> <br>
    <label for="psw" style="display:block"><b>Password</b></label>
	Password should be at least 6 characters long. <br>
    <input type="password" placeholder="Enter Password" name="psw" required>
<br> <br>
    <label for="psw-repeat" style="display:block"><b>Repeat Password</b></label>
    <input type="password" placeholder="Repeat Password" name="rpsw" required>
<br> <br>
    <label for="sname" style="display:block"><b>Shelter Name</b></label>
    <input type="text" placeholder="Shelter Name" name="sname" required>
<br> <br>
    <label for="address" style="display:block"><b>Address</b></label>
    	Please include city and state in the address. <br>
	<input type="text" placeholder="Address" name="address" required>
<br> <br>
    <input type="submit" style="display:block"  value="Sign Up">
</form>

</body>
</html>
