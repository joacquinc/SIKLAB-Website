<?php
include '../connection.php';

$contactNum = $_POST['contactNum'];
$password = md5($_POST['password']);

$sqlQuery = "SELECT * FROM users WHERE contactNum = '$contactNum' AND password = '$password'";

$resultOfQuery = $connectNow->query($sqlQuery);

if($resultOfQuery->num_rows > 0){ //allow login
    $userRecord = array();
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $userRecord[] = $rowFound;
    }
    echo json_encode(
        array(
            "Success"=>true,
            "userData"=>$userRecord[0],
        )
    );
}else{ //do not allow
    echo json_encode(array("Success"=>false));
}
?>