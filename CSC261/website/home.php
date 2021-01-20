<?php
require_once("db_functions.php");
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
    <h1> Adoption Database Home </h1>
<ul>
    <li><a href="shelter/viewPets.php">Shelter Center</a></li> <br>
    <li><a href="adopter/viewAdopterRequests.php">Adopter Center</a></li> <br>
	<li><a href="filter.html"><button type="button">Filter Results</button></a></li>
</ul>
    <h2> Availble Pets </h2>

    <table class="table">
	<thead>
	<tr>
            <th scope="col">Pet ID</th>
            <th scope="col">Image</th>
            <th scope="col">Name</th>
            <th scope="col">Age</th>
	    <th scope="col">Type</th>
	    <th scope="col">Breed</th>
	    <th scope="col">Origin</th>
            <th scope="col">Neutered or Spade</th>
            <th scope='col'>Diet</th>
            <th scope='col'>Personality</th>
            <th scope='col'></th>
	</tr>
	
	</thead>
	<tbody>
            <?php
              	pets_adopter_info();
            ?>
        </tbody>
    </table>
</body>
</html>
