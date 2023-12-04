<?php
include '../connection.php';

$contactNum = $_POST['contactNum'];

$sqlQuery = "SELECT * FROM users WHERE contactNum = '$contactNum'";

$resultOfQuery = $connectNow->query($sqlQuery);

if($resultOfQuery->num_rows > 0){
    echo json_encode(array("numberFound"=>true));
}else{
    echo json_encode(array("numberFound"=>false));
}
?>