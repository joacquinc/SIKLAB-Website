<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('config.php');
session_start();

if (isset($_POST['login'])) {
    // Get user input from the form
    $emailAddress = $_POST['emailAddress'];
    $password = md5($_POST['password']); // Note: It's recommended to use a more secure hashing method, like bcrypt.

    // Check if the credentials exist in the database
    $query = "SELECT adminID, adminUsername, emailAddress, barangay, password, logged FROM admins WHERE emailAddress = '$emailAddress' AND password = '$password'";
    $stmt = $mysqli->prepare($query);
    $stmt->execute();
    $stmt->bind_result($adminID, $adminUsername, $dbEmail, $dbBarangay, $dbPassword, $dblogged);

    if ($stmt->fetch()) {
        // Credentials found 
        // echo "Login Successful";
        $_SESSION['user_id'] = $adminID;
        $_SESSION['username'] = $adminUsername;
        $_SESSION['email'] = $dbEmail;
        $_SESSION['barangay'] = $dbBarangay;
        $_SESSION['password'] = $dbPassword;
        $_SESSION['logged'] = $dblogged;

        if ($dbEmail == 'siklabcentralized@gmail.com') {
            header('Location: centralAntipolo_dashboard.php');
        } else {
            header('Location: admin_dashboard.php');
        }
    } else {
        // Credentials not found
        //echo "Login failed. Please check your email and password.";
        header('Location: index.php');
        exit();
    }
    /////////////// header('Location: admin_dashboard.php');exit();///////////////////////////////////////////////////////
    


}
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <title>SIKLAB CENTRALIZED</title>
      <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
      <link rel="stylesheet" href="style_index.css" />
   </head>

   <!-- BODY -->
   <body>
      
    <div class="container">
        <div class="login-left"> 
            <div class="login-header"> 
                <h1> Welcome to SIKLAB Centralized Web Application </h1>
                <p> Please login to use the platform </p>
            </div>
            <form class="login-form" method="post" action="index.php">
                <div class="login-form-content">
                    <div class="form-item">
                        <label for="emailAddress"> Enter Admin Credentials (Email) </label>
                        <input type="text" id="emailAddress" name="emailAddress" required>
                    </div>
                    <div class="form-item">
                        <label for="password"> Enter Password </label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="login" name="login"> Login </button>
                    <button style="color: white; text-decoration: none;"><a href="sign_up.php" style="color: white; text-decoration: none;"> Sign Up </a></button>
                    <button style="color: white; text-decoration: none;"><a href="admin_forgotpassword.php" style="color: white; text-decoration: none;"> Forgot Password? </a></button>
                </div>
            </form>
        </div>
        <div class="login-right"> 
            <img src="https://i.imgur.com/eiNkQFu.png">
        </div>
    </div>

   </body>
</html>