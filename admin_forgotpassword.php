<?php
require_once('config.php');
session_start();

if (isset($_POST['updatePassword'])) {
    $emailAddress = $_POST['emailAddress'];
    $password = md5($_POST['password']);
    $confirmPassword = md5($_POST['confirmPassword']);

    // Check if the email address exists in the database
    $query = "SELECT adminID FROM admins WHERE emailAddress = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $emailAddress);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        if ($password === $confirmPassword) {
            // Passwords match; proceed with the update
            $updateQuery = "UPDATE admins SET password = ? WHERE emailAddress = ?";
            $updateStmt = $mysqli->prepare($updateQuery);
            $updateStmt->bind_param("ss", $password, $emailAddress);

            if ($updateStmt->execute()) {
                // Password update successful
                //echo "Password updated successfully.";
                header("Location: index.php");
                exit;
            } else {
                // Password update failed
                echo "Failed to update the password: " . $mysqli->error;
            }
        } else {
            // Passwords do not match
            echo "Passwords do not match.";
        }
    } else {
        // Email address not found
        echo "Email address not found in the database.";
    }
}
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <title>SIKLAB CENTRALIZED</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <link rel="stylesheet" href="style_index.css" />
      <script>
        function validatePassword() {
            var newPassword = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmpassword").value;
            var error = document.getElementById("error");

            if (newPassword !== confirmPassword) {
                error.innerHTML = "Passwords do not match.";
                return false;
            } else {
                error.innerHTML = "";
                return true;
            }
        }
      </script>
   </head>

   <!-- BODY -->
   <body>
      
    <div class="container">
        <div class="login-left"> 
            <div class="login-header"> 
                <h1> Welcome to SIKLAB Centralized Web Application </h1>
                <p> Please enter your password </p>
            </div>
            <form class="login-form" method="POST" onsubmit="return validatePassword();">
                <div class="login-form-content">
                    <div class="form-item">
                        <label for="emailAddress"> Enter Admin Credentials (Email) </label>
                        <input type="text" id="emailAddress" name="emailAddress" required>
                    </div>
                    <div class="form-item">
                        <label for="password"> Enter New Password </label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-item">
                        <label for="confirmpassword"> Confirm New Password </label>
                        <input type="password" id="confirmpassword" name="confirmPassword" required>
                    </div>
                    <p id="error" style="color: red;"></p>
                    <button type="submit" name="updatePassword"> Update Password </button>
                </div>
            </form>
        </div>
        <div class="login-right"> 
            <img src="https://i.imgur.com/eiNkQFu.png">
        </div>
    </div>
   </body>
</html>

