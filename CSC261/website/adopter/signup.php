<?php
require_once("../db_functions.php");

//ini_set('display_errors', 'On');

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

function validate_ssn($ssn) {
    if (!is_numeric($ssn)) {
        echo "Please enter numbers only for SSN.";
        return false;
    }

    if (strlen((string) $ssn)!=9) {
        echo "SSN must be 9 numbers.";
        return false;
    }

    $conn = database_connect();
    $query = "SELECT Ssn FROM ADOPTER WHERE Ssn = '" . $ssn . "';";
    $qry = $conn->prepare($query);
    $qry->execute();
    if ($qry->errorCode() != 0) {
        print_r($qry->errorInfo());
    }
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
    if(sizeof($result) > 0) {
        echo "SSN already exists in the system.";
        return false;
    }


    return true;
}

function validate_budget($budget) {
    if (!is_numeric($budget)) {
        echo "Please enter numbers only for budget.";
        return false;
    }

    if ($budget < 0) {
        echo "Budget cannot be a negative value.";
        return false;
    }

    return true;
}

function validate_cpets($cpets) {
    if (!is_numeric($cpets)) {
        echo "Please enter numbers only for the current number of pets.";
        return false;
    }

    if ($cpets<0) {
        echo "Number of pets cannot be a negative value.";
        return false;
    }

    return true;
}

if ( ! empty( $_POST ) ) {
    if( validate_input($_POST) 
        && validate_email($_POST['email'])
        && validate_password($_POST['psw'], $_POST['rpsw'])
        && validate_ssn($_POST['ssn'])
        && validate_budget($_POST['budget'])
        && validate_cpets($_POST['cpets'])
    ) {
        $conn = database_connect();

        $query0 = "INSERT INTO USERS (Email, Password, User_type)  VALUES("
                   . "'" . $_POST['email'] . "',"
                   . "'" . hash_function($_POST['psw']) . "',"
                   . "'ADOPTER'"
                   . ");";

        $query1 = "INSERT INTO ADOPTER (Ssn, First_name, Last_name, Address, Current_pets, Budget, Landloard_permission) VALUES("
                   . "'" . $_POST['ssn'] . "',"
                   . "'" . $_POST['fname'] . "',"
                   . "'" . $_POST['lname'] . "',"
                   . "'" . $_POST['address'] . "',"
                   . "'" . $_POST['cpets'] . "',"
                   . "'" . $_POST['budget'] . "',"
                   . "'" . $_POST['lpermission'] . "'"
                   . ");";

        $query2 = "INSERT INTO ADOPTER_EMAIL (Ssn, Email)  VALUES("
                   . "'" . $_POST['ssn'] . "',"
                   . "'" . $_POST['email'] . "'"
                   . ");";

        $query3 = "INSERT INTO ADOPTER_HABITS (Ssn, Habit)  VALUES("
                   . "'" . $_POST['ssn'] . "',"
                   . "'" . $_POST['habit'] . "'"
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
     <h1> Adopter Center </h1>
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
    <label for="fname" style="display:block"><b>First Name</b></label>
    <input type="text" placeholder="First Name" name="fname" required>
<br> <br>
    <label for="lname" style="display:block"><b>Last Name</b></label>
    <input type="text" placeholder="Last Name" name="lname" required>
<br> <br>
    <label for="ssn" style="display:block"><b>SSN</b></label>
	SSN should be in the form #########. Only numbers will be accepted. <br>
    <input type="password" placeholder="SSN" name="ssn" required>
<br> <br>
    <label for="cpets" style="display:block"><b>Current number of Pets</b></label>
	Current number of pets should be represented using arabic numerals (1,2,...). <br>
    <input type="number" placeholder="Current number of Pets" name="cpets" required>
<br> <br>
    <label for="budget" style="display:block"><b>Budget</b></label>
	Budget should be represented using arabic numera;s (1,2,...). <br>
    <input type="number" placeholder="Budget" name="budget" required>
<br> <br>
    <label for="habit" style="display:block"><b>Habits</b></label>
    <input type="text" placeholder="Habits" name="habit" required>
<br> <br>
    <label for="address" style="display:block"><b>Address</b></label>
    <input type="text" placeholder="Address" name="address" required>
<br> <br>
    <label for="lpermission" style="display:block"><b>Landloard permission?</b></label>
    <input type="radio" name="lpermission" value="True" required>Yes<br>
    <input type="radio" name="lpermission" value="False">No<br>
<br> <br>
    <input type="submit" style="display:block"  value="Sign Up">
</form>

</body>
</html>
