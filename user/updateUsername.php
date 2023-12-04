<?php
$servername = "localhost";
$username = "id21303071_siklabmin";
$password = "Siklabproject1.23";
$database = "id21303071_siklabdatabase";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the new username and the contact number you want to target
$contactNum = $_POST['contactNum'];
$username = $_POST['username'];

// Prepare an SQL query to update the username
$sql = "UPDATE users SET username = ? WHERE contactNum = ?";

// Prepare the statement
if ($stmt = $conn->prepare($sql)) {
    // Bind the parameters
    $stmt->bind_param("ss", $username, $contactNum);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Username updated successfully.";
    } else {
        echo "Error updating username: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error preparing the statement: " . $conn->error;
}

?>
