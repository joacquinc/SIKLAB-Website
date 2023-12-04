<?php

$serverHost = "localhost";
$user = "id21303071_siklabmin";
$password = "Siklabproject1.23";
$database = "id21303071_siklabdatabase";

$connectNow = new mysqli($serverHost, $user, $password, $database);
/*
if ($connectNow->connect_error) {
    die("Connection failed: " . $connectNow->connect_error);
}else{
    echo("Connection Established");
}
*/
?>