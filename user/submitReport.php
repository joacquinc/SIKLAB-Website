<?php
include '../connection.php';

$userID = $_POST['userID'];
$contactNum =  $_POST['contactNum'];
$timeStamp = $_POST['timeStamp'];
$latitudeRep = $_POST['latitudeRep'];
$longitudeRep = $_POST['longitudeRep'];
$barangay = $_POST['barangay'];
$addressRep = $_POST['addressRep'];
$assistanceRep = $_POST['assistanceRep'];
$status = $_POST['status'];
$alarmSeverity = $_POST['alarmSeverity'];

$sqlQuery = "INSERT INTO reports SET userID = '$userID', contactNum = '$contactNum', timeStamp = '$timeStamp', latitudeRep = '$latitudeRep', longitudeRep = '$longitudeRep', barangay = '$barangay', addressRep = '$addressRep', assistanceRep = '$assistanceRep', status = '$status', alarmSeverity = '$alarmSeverity'";

$resultOfQuery = $connectNow->query($sqlQuery);

if($resultOfQuery){
    echo json_encode(array("Success"=>true));
}else{
    echo json_encode(array("Success"=>false));
}
?>