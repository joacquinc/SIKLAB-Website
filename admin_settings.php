<?php
require_once('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['barangay'])) {
        $barangay = $_SESSION['barangay'];
    }
    if (isset($_SESSION['email'])) {
        $emailAddress = $_SESSION['email'];
    }
    if (isset($_SESSION['username'])) {
        $adminUsername = $_SESSION['username'];
    }
    if (isset($_SESSION['logged'])){
        $logged = $_SESSION['logged'];
    }
} else {
    header("Location: index.php");
    exit;
}
// Logout Function
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other page as needed
    header("Location: index.php");
    exit;
}

// Handle form submission to update 'adminUsername' and 'password' in the database
if (isset($_POST['create'])) {
    $emailAddress = $_POST['emailAddress'];
    $adminUsername = $_POST['adminUsername'];
    $password = md5($_POST['password']);

    // Check if the email address exists in the database
    $query = "SELECT adminID FROM admins WHERE emailAddress = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $emailAddress);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email address found, update adminUsername and password
        $updateQuery = "UPDATE admins SET adminUsername = ?, password = ? WHERE emailAddress = ?";
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("sss", $adminUsername, $password, $emailAddress);

        if ($updateStmt->execute()) {
            // Update successful
            echo "Admin information updated successfully.";
        } else {
            // Update failed
            echo "Failed to update admin information: " . $mysqli->error;
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
      <title>Admin Settings</title>
      <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
      <link rel="stylesheet" href="style_admin_settings.css" />
   </head>

   <!-- BODY -->
   <body>
   <!---->
   <form id="logoutForm" method="post" action="admin_settings.php" style="display: none;">
   <input type="hidden" name="logout" value="1">
   </form>
   <!---->
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo"> 
                    <img src="https://i.imgur.com/UxMopAy.png">
                    <h2> SIKLAB </h2>
                </div>
                <div class="close" id="close-btn"> 
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar"> 
                <a href="admin_dashboard.php">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3> Dashboard </h3>
                </a>
                <a href="admin_reports.php">
                <span class="material-icons-sharp">report</span>
                    <h3> Fire Reports </h3>
                </a>
                <a href="#" class="active">
                    <span class="material-icons-sharp">settings</span>
                    <h3> Settings </h3>
                </a>
                <a href="javascript:void(0);" onclick="document.getElementById('logoutForm').submit();">
                    <span class="material-icons-sharp">logout</span>
                    <h3> Log Out </h3>
                </a>
            </div>
        </aside>
        <!-- END OF ASIDE, START OF MAIN -->
        <main>
            <h1> Admin Profile and Settings </h1>

            <div class="form_div">
                <form class="login-form" method="POST">
                    <div class="login-form-content">
                        <div class="form-item">
                            <label for="admincreds"> Admin Username </label>
                            <input type="text" name="adminUsername" id="adminUsername" placeholder="<?php echo $adminUsername; ?>">
                        </div>
                        <div class="form-item">
                            <label for="emailAddress"> Email Address </label>
                            <input type="text" name="emailAddress" id="emailAddress" value="<?php echo $emailAddress; ?>" readonly style="color: gray; background-color: #f0f0f0;">
                        </div>
                        <div class="form-item">
                            <label for="barangay"> Barangay </label>
                            <input type="text" name="barangay" id="barangay" value="<?php echo $barangay; ?>" readonly style="color: gray; background-color: #f0f0f0;">
                        </div>
                        <div class="form-item">
                            <label for="firestation"> Firestation </label>
                            <input type="text" name="firestation" id="firestation" value="<?php echo $logged; ?>" readonly style="color: gray; background-color: #f0f0f0;">
                        </div>
                        <div class="form-item">
                            <label for="password"> Password </label>
                            <input type="password" name="password" id="password">
                        </div>
                        <button type="create" name="create"> Edit Information </button>
                        <button type="logout" name="logout"> Log Out </button>
                    </div>
                </form>
            </div>

        </main>
    </div>

   </body>
</html>