<?php

$contactNum = $_POST['contactNum'];

$conn = new mysqli("localhost", "id21303071_siklabmin", "Siklabproject1.23", "id21303071_siklabdatabase");

$stmt = $conn->prepare("SELECT userID FROM users WHERE contactNum = ?");
$stmt->bind_param("s", $contactNum);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        
        $row = $result->fetch_assoc();
        $userID = $row['userID'];
        $barangay = $row['barangay'];

        
        $response = (array("userID" => $userID, "barangay" => $barangay));
        echo json_encode($response);
    } else {
        
        echo json_encode(array("error" => "ContactNum not found"));
    }
} else {
    // Error executing query
    echo json_encode(array("error" => "Error executing query"));
}


?>


