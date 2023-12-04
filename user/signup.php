<?php
include '../connection.php';

$username = $_POST['username'];
$barangay = $_POST['barangay'];
$contactNum = $_POST['contactNum'];
$password = md5($_POST['password']);

$sqlQuery = "INSERT INTO users SET username = '$username', barangay = '$barangay', contactNum = '$contactNum', password = '$password'";

$resultOfQuery = $connectNow->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("Success"=>true));
}else{
    echo json_encode(array("Success"=>false));
}
?>