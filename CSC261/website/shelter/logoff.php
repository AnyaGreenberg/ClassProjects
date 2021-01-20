<?php
session_id("shelter");
session_start();
session_destroy();
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
<ul>
    <li><a href="login.php">Login</a></li> <br>
	<li><a href="../home.php">Return to Homepage</a></li>
</ul>

<h3>Successfully logged off.</h3>

</html>

