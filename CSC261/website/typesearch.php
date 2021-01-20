<?php
require_once("../db_functions.php");

function pets() {
    $conn = database_connect();
    if ($conn) {
        $query = "SELECT Pet_id, Ptype, Pname, Age_year, Age_month, Breed, Origin, Neutered_spade, Diet, Personality, Image
                FROM PET_INFO
                NATURAL JOIN BREED
                NATURAL JOIN PET_IMAGES
		WHERE Ptype=" . "'" . $_POST['type'] . "'" 
		. "ORDER BY Pet_id ASC;";
        $qry = $conn->prepare($query);
        $qry->execute();
        if ($qry->errorCode() != 0) {
                print_r($qry->errorInfo());
        } else {
            return $qry->fetchAll(PDO::FETCH_ASSOC);
        }
	echo $query;
    } else {
	echo "Cannot connect to database.";
    }
}

function pets_info() {
    foreach(pets() as $pet) {
        print '<tr>';
        print '<td scope="row" style="text-align:center">' . ($pet['Pet_id']) . '</td>';
        print '<td scope="row"> <img src="' . 'images/'  . ($pet['Image']) . '" alt="HTML5 Icon" width="128" height="128"></td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Pname']) . '</td>';
        print '<td scope="row" style="text-align:center">' . ($pet['Age_year']). ' Years, ' . ($pet['Age_month']) . ' Months' .  '</td>';
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

table, th, td {
        border-bottom: 1px solid #ddd;
}
</style>
</head>

<body>
      	<h1> Filtered View </h1>
<ul>
        <li><a href="home.php">View All Pets</a></li> <br>
	<li><a href="filter.html"><button type="button">Filter Results</button></a></li>
</ul>
		
	If you are not seeing any pets listed, that means there are no pets with your specifications in	our database.
     	
        <table class="table">
        <thead>
        <tr>
            	<th scope="col">Pet_id</th>
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Age</th>
                <th scope="col">Type</th>
                <th scope="col">Breed</th>
                <th scope="col">Origin</th>
                <th scope="col">Neutered/Spade</th>
                <th scope='col'>Diet</th>
                <th scope='col'>Personality</th>
                <th scope='col'></th>
        </tr>
	</thead>
        <tbody>
		<?php pets_info() ?>
	</tbody>
	</table>
</body>
</html>

