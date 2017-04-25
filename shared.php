
<!--
 shared file among all php files. This is where we will put shared functions
 and connection credentials for the database.
-->

<?php
$servername = 'localhost:3306'; // change to your correct localhost port number
$dbname = 'gisterm'; // change to your database name
$username = 'root'; // change to your username
$password = 'perfectmint299'; // change to your db password


//connection code

// $db = new mysqli($servername, $username, $password, $dbname);
//
// if($db->connect_errno > 0){
//     die('Unable to connect to database [' . $db->connect_error . ']');
// }

?>
