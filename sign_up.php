<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('config.php');
session_start();

if(isset($_POST['create'])) {
    //Random firestation
    $firestations = ["Antipolo Firestation", "Taytay Firestation", "Cainta Firestation"];
    $randomIndex = array_rand($firestations);
    $logged = $firestations[$randomIndex];
    // Get user input from the form
    $adminUsername = $_POST['adminUsername'];
    $emailAddress = $_POST['emailAddress'];
    $barangay = $_POST['barangay'];
    $password = md5($_POST['password']);

    // Insert the data into the 'admins' table using prepared statements
    $sql = "INSERT INTO admins (adminUsername, emailAddress, barangay, password, logged) VALUES (?, ?, ?, ?,?)";
    $stmtinsert = $mysqli->prepare($sql);
    $result = $stmtinsert->execute([$adminUsername, $emailAddress, $barangay, $password, $logged]);

    if($result) {
        // Data inserted successfully
        //echo "Registration successful. You can now log in.";
        header("Location: index.php");
        exit;
    } else {
        // An error occurred
        echo "Registration failed. Please try again.";
    }
    
}
?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <title>SIKLAB: Sign Up</title>
      <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
      <link rel="stylesheet" href="style_signup.css" />
   </head>

   <!-- BODY -->
   <body>
      
    <div class="container">
        <div class="login-left"> 
            <div class="login-header"> 
                <h1> Sign Up Form </h1>
                <p> Populate the necessary items to sign in. </p>
            </div>
            <form class="login-form" method="post" action="sign_up.php">
                <div class="login-form-content">
                    <div class="form-item">
                        <label for="adminUsername"> Admin Username </label>
                        <input type="text" name="adminUsername" id="adminUsername" required>
                    </div>
                    <div class="form-item">
                        <label for="adminAddress"> Email Address </label>
                        <input type="email" name="emailAddress" id="emailAddress" required>
                    </div>
                    <div class="form-item">
                        <label for="barangay">Select a Barangay:</label>
                        <select name="barangay" id="barangay" required>
                            <option value="Brgy. Bagong Nayon">Brgy. Bagong Nayon</option>
                            <option value="Brgy. Beverly Hills">Brgy. Beverly Hills</option>
                            <option value="Brgy. Cupang">Brgy. Cupang</option>
                            <option value="Brgy. Dalig">Brgy. Dalig</option>
                            <option value="Brgy. Dela Paz">Brgy. Dela Paz</option>
                            <option value="Brgy. Inarawan">Brgy. Inarawan</option>
                            <option value="Brgy. Mambugan">Brgy. Mambugan</option>
                            <option value="Brgy. Mayamot">Brgy. Mayamot</option>
                            <option value="Brgy. San Isidro">Brgy. San Isidro</option>
                            <option value="Brgy. San Jose">Brgy. San Jose</option>
                            <option value="Brgy. San Luis">Brgy. San Luis</option>
                            <option value="Brgy. San Roque">Brgy. San Roque</option>
                            <option value="Brgy. Santa Cruz">Brgy. Santa Cruz</option>
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="password"> Enter Password </label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="form-item">
                        <label for="password"> Confirm Password </label>
                        <input type="password" name="confirmpassword" id="confirmpassword" required>
                    </div>
                    <button type="submit" name="create"> Sign Up </button>
                </div>
            </form>
        </div>
        <div class="login-right"> 
            <img src="https://i.imgur.com/eiNkQFu.png">
        </div>
    </div>

   </body>
</html>