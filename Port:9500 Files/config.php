<?php
//Specifies database info and connects to it

DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', 'root');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'SYSAD');

//connect to the database or throw the error

$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
OR die("Could not connect to mySQL, " . mysqli_connect_error());

?>
