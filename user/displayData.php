<?php
$conn = new mysqli("localhost", "id21303071_siklabmin", "Siklabproject1.23", "id21303071_siklabdatabase");

$query = "SELECT reportID, addressRep, timeStamp, longitudeRep, latitudeRep FROM reports ORDER BY timestamp DESC LIMIT 10";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);

?>
