<?php

DEFINE('DB_USER', 'host');

function database_connect() {
    $host = "localhost";
    $username = "root";
    $password = "---";
    $db_name = "adoption";
    
    try{
        // create a mySQL  connection
        $conn = new PDO("mysql:host=$host;dbname=$db_name;user=$username;password=$password", $username, $password);
        // display a message if connected to the PostgreSQL successfully
        if($conn){
            return $conn;
        } else {
            echo "ERROR: cannot connect to <strong>$host</strong> !";
        }
    }catch (PDOException $e){
        // report error message
        echo $e->getMessage();
        return null;
    }
}

function hash_function($plaintext) {
    return sha1($plaintext);
}

function adopter_pets() {
    $conn = database_connect();
    if ($conn) {
        $query = "SELECT Pet_id, Ptype, Pname, Age_year, Age_month, Breed, Origin, Neutered_spade, Diet, Personality, Image
		FROM PET_INFO 
		NATURAL JOIN PET_IMAGES
		NATURAL JOIN BREED;";
        $qry = $conn->prepare($query);
        $qry->execute();
        if ($qry->errorCode() != 0) {
            print_r($qry->errorInfo());
        } else {
            return $qry->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        echo "COULD NOT CONNECT TO DATABASE TO GET PETS INFO";
    }
}

function pets_adopter_info() {
    foreach(adopter_pets() as $pet) {
        print '<tr>';
	print '<td scope="row" style="text-align:center">' . ($pet['Pet_id']) . '</td>';
	print '<td scope="row"> <img src="./images/' . ($pet['Image']) . '" alt="HTML5 Icon" width="150" height="128"></td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Pname']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Age_year']). ' years, ' . ($pet['Age_month']) . ' months' .  '</td>';
	print '<td scope="row" style="text-align:center">' . ($pet['Ptype']) . '</td>';        
	print '<td scope="row" style="text-align:center">' . ($pet['Breed']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Origin']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Neutered_spade']) . '</td>';
	print '<td scope="row">' . ($pet['Diet']) . '</td>';
	print '<td scope="row" style="text-align:center">' . ($pet['Personality']) . '</td>';
        print '<td scope="row"> <a href="adopter/adopt.php?ref=' . ($pet['Pet_id'])  . '"> <button type="button">Apply for Adoption Now!</button>  </td>';
        print '</tr>';
    }
}

function query_adoption_requests() {
    $conn = database_connect();
    if ($conn) {
        $query = "SELECT Pet_id, PName, PType, Breed, Age_year, Age_month, Origin, First_name, Last_name, Email  
		FROM ADOPTION_REQUESTS 
		NATURAL JOIN PET_INFO 
		NATURAL JOIN ADOPTER 
		NATURAL JOIN BREED
		NATURAL JOIN ADOPTER_EMAIL
		ORDER BY Pet_id ASC;";
        $qry = $conn->prepare($query);
        $qry->execute();
        if ($qry->errorCode() != 0) {
            print_r($qry->errorInfo());
        } else {
            return $qry->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        echo "COULD NOT CONNECT TO DATABASE TO GET ADOPTION INFO";
    }
}

function generate_adpotion_requests_info() {
    foreach(query_adoption_requests() as $adoption) {
        print '<tr>';
        print '<td scope="row">' . ($adoption['Pet_id']) . '</td>';
	print '<td scope="row">' . ($adoption['Pname']) . '</td>';
	print '<td scope="row">' . ($adoption['Ptype']) . '</td>';
	print '<td scope="row">' . ($adoption['Breed']) . '</td>';
        print '<td scope="row">' . ($adoption['Age_year']) . ' Years, ' . ($adoption['Age_month']) . ' Months' .  '</td>';
        print '<td scope="row">' . ($adoption['Origin']) . '</td>';
        print '<td scope="row">' . ($adoption['First_name']) . ' ' . ($adoption['Last_name']) . '</td>';
	print '<td scope="row">' . ($adoption['Email']) . '</td>';
        print '</tr>';
    }
}

function generate_pets_info_shelter() {
    foreach(query_pets() as $pet) {
        print '<tr>';
        print '<td scope="row">' . ($pet['Pet_id']) . '</td>';
        print '<td scope="row">' . ($pet['Age_year']) . '</td>';
        print '<td scope="row">' . ($pet['Breed']) . '</td>';
        print '<td scope="row">' . ($pet['Origin']) . '</td>';
        print '<td scope="row">' . ($pet['Neutered_spade']) . '</td>';
	print '<td scope="row">' . ($pet['Diet']) . '</td>';
    	print '<td scope="row">' . ($pet['Personality']) . '</td>';
	print '<td scope="row"> <img src="' . ($pet['Image']) . '"></td>';
        print '</tr>';
    }
}

function query_adoption_requests_by_ssn($adopter_ssn) {
    $conn = database_connect();
    if ($conn) {
        $query = "SELECT Pet_id
                FROM ADOPTION_REQUESTS
                WHERE Ssn = '" . $adopter_ssn  .  "';";
        $qry = $conn->prepare($query);
        $qry->execute();
        //echo $query;
        if ($qry->errorCode() != 0) {
            print_r($qry->errorInfo());
        } else {
            return $qry->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        echo "COULD NOT CONNECT TO DATABASE TO GET PETS INFO";
    }
}

function generate_pets_info_adopter($adopter_ssn) {
    $requests = array();
    foreach(query_adoption_requests_by_ssn($adopter_ssn) as $request) {
        array_push($requests, $request['Pet_id']);
        //echo $request['\r\nPet_id'];
    }
     
    foreach(adopter_pets($adopter_ssn) as $pet) {
        print '<tr>';
	print '<td scope="row" style="text-align:center">' . ($pet['Pet_id']) . '</td>';
        print '<td scope="row"> <img src="' . '../images/'  . ($pet['Image']) . '" alt="HTML5 Icon" width="150" height="128"></td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Pname']) . '</td>';
	print '<td scope="row" style="text-align:center">' . ($pet['Ptype']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Breed']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Age_year']). ' years, ' . ($pet['Age_month']) . ' months' .  '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Origin']) . '</td>'; 
        print '<td scope="row" style="text-align:center">' . ($pet['Neutered_spade']) . '</td>';
        print '<td scope="row">' . ($pet['Diet']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Personality']) . '</td>';
        //echo $pet['Ssn'];
        if(in_array($pet['Pet_id'], $requests)) {
            print '<td scope="row"> Applied!  </td>';
        } else {
            print '<td scope="row"> <a href="adopt.php?ref=' . ($pet['Pet_id'])  . '"><button type="button">Apply for Adoption Now!</button></a>  </td>';
        }
        print '</tr>';
    }
}

?>

