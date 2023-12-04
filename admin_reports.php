<?php
require_once('config.php');

session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['barangay'])) {
        $barangay = $_SESSION['barangay'];
        // Now $userName contains the user's name
    }
} else {
    header("Location: index.php");
    exit;
}

// Count total users
$userCount = 0; // Initialize the user count

// Check if $_SESSION['barangay'] is set and not empty
if (isset($_SESSION['barangay']) && !empty($_SESSION['barangay'])) {
    // Sanitize the session value to prevent SQL injection
    $barangay = mysqli_real_escape_string($mysqli, $_SESSION['barangay']);

    $queryUserCount = "SELECT COUNT(*) AS user_count FROM users WHERE barangay = '$barangay'";
    $resultUserCount = mysqli_query($mysqli, $queryUserCount);

    if ($resultUserCount) {
        $rowUserCount = mysqli_fetch_assoc($resultUserCount);
        $userCount = $rowUserCount['user_count'];
    }
}

// Count total fire reports
$fireReportCount = 0; // Initialize the fire report count

// Check if $_SESSION['barangay'] is set and not empty
if (isset($_SESSION['barangay']) && !empty($_SESSION['barangay'])) {
    // Sanitize the session value to prevent SQL injection
    $barangay = mysqli_real_escape_string($mysqli, $_SESSION['barangay']);

    $queryFireReportCount = "SELECT COUNT(*) AS fire_report_count FROM reports WHERE barangay = '$barangay'";
    $resultFireReportCount = mysqli_query($mysqli, $queryFireReportCount);

    if ($resultFireReportCount) {
        $rowFireReportCount = mysqli_fetch_assoc($resultFireReportCount);
        $fireReportCount = $rowFireReportCount['fire_report_count'];
    }
}


// Retrieve recent fire reports
$recentReports = array(); // Initialize an array to store the reports

// Check if $_SESSION['barangay'] is set and not empty
if (isset($_SESSION['barangay']) && !empty($_SESSION['barangay'])) {
    // Sanitize the session value to prevent SQL injection
    $barangay = mysqli_real_escape_string($mysqli, $_SESSION['barangay']);

    $queryRecentReports = "SELECT * FROM reports WHERE barangay = '$barangay' ORDER BY timeStamp DESC";
    $resultRecentReports = mysqli_query($mysqli, $queryRecentReports);

    if ($resultRecentReports) {
        while ($row = mysqli_fetch_assoc($resultRecentReports)) {
            $recentReports[] = $row;
        }
    }
}

// Retrieve latest barangay 
$recentBarangay;
$queryRecentBarangay = "SELECT barangay FROM reports ORDER BY timeStamp DESC LIMIT 1";
$resultRecentBarangay = mysqli_query($mysqli, $queryRecentBarangay);
if ($resultRecentBarangay){
    $row = mysqli_fetch_assoc($resultRecentBarangay);
    $recentBarangay = $row['barangay'];
}

// Logout button sidebar
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other page as needed
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <title>FIRE REPORTS HISTORY</title>
      <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
      <link rel="stylesheet" href="style_admin_reports_list.css" />
        <script>
            function openMap(latitudeRep, longitudeRep) {
                if (!isNaN(latitudeRep) && !isNaN(longitudeRep)) {
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'map.php';
                    form.target = '_blank';

                    var inputLat = document.createElement('input');
                    inputLat.type = 'hidden';
                    inputLat.name = 'lat';
                    inputLat.value = latitudeRep;

                    var inputLng = document.createElement('input');
                    inputLng.type = 'hidden';
                    inputLng.name = 'lng';
                    inputLng.value = longitudeRep;

                    form.appendChild(inputLat);
                    form.appendChild(inputLng);

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                } else {
                    alert('Location coordinates are not available for this report.');
                }
            }
        </script>
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
                <a href="#" class="active">
                <span class="material-icons-sharp" >report</span>
                    <h3> Fire Reports </h3>
                </a>
                <a href="admin_settings.php">
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
            <h1> History of Fire Reports in <?php echo "$barangay, "; ?> Antipolo City </h1>

            <div class="data">
                <div class="reports">
                    <span class="material-icons-sharp">analytics</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Fire Incidents Reported </h3>
                            <h1><?php echo $fireReportCount ?></h1>
                        </div>
                    </div>
                </div>
                <div class="users">
                    <span class="material-icons-sharp">person</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Total Users </h3>
                            <h1><?php echo $userCount; ?></h1>
                        </div>
                    </div>
                </div>
                <div class="widget">
                    <span class="material-icons-sharp">event</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Extract Reports for the Past 7 Days </h3>
                            <form method="post" action="extract7days.php">
                                <input type="submit" name="extract7days" value="Extract Reports" style="background: none; border: none; padding: 0; font: inherit; cursor: pointer; color: #333; font-size: 24px; font-weight: bold; text-decoration: none;">
                            </form>
                        </div>
                    </div>
                </div>
            </div>    

            <div class="recent-reports">
                <table style="background: white;">
                    <thead>
                        <th> Checkbox </th>
                        <th> Date and Time </th> 
                        <th> Address </th> 
                        <th> Barangay </th> 
                        <th> Contact Number </th> 
                        <th> Special Assistance </th> 
                        <th> View on Map </th> 
                        <th> Status </th> 
                        <th> Alarm Severity </th> <!-- Add this column -->
                        <th> Update Status </th> 
                    </thead>
                    <tbody>
                        <?php foreach ($recentReports as $report) { ?>
                            <tr>
                                <form method="post" action="update_status.php">
                                    <input type="hidden" name="report_id" value="<?php echo $report['reportID']; ?>">
                                    <td>
                                        <label class="checkbox_container" <?php echo ($report['status'] == 'Resolved') ? 'style="pointer-events: none;"' : ''; ?> <?php echo ($report['status'] == 'Resolved') ? 'checked' : ''; ?>>
                                            <input type="checkbox" name="update_checkbox[]" value="<?php echo $report['reportID']; ?>" <?php echo ($report['status'] == 'Resolved') ? 'disabled' : ''; ?> <?php echo ($report['status'] == 'Resolved') ? 'checked' : ''; ?>>
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                    <td><?php echo $report['timeStamp']; ?></td>
                                    <td><?php echo $report['addressRep']; ?></td>
                                    <td><?php echo $report['barangay']; ?></td>
                                    <td><?php echo $report['contactNum']; ?></td>
                                    <td><?php echo $report['assistanceRep']; ?></td>
                                    <td>
                                        <button style="background: white; display: block; margin: 1rem auto; cursor: pointer;" onclick="openMap(<?php echo $report['latitudeRep']; ?>, <?php echo $report['longitudeRep']; ?>)">
                                            View
                                        </button>
                                    </td>
                                    <td class="<?php echo ($report['status'] == 'Resolved') ? 'success' : 'danger'; ?>">
                                        <?php echo $report['status']; ?>
                                    </td>
                                    <td>
                                        <select name="alarm_severity">
                                            <option value="Alarm 1" <?php echo ($report['alarmSeverity'] === 'Alarm 1') ? 'selected' : ''; ?>>Alarm 1</option>
                                            <option value="Alarm 2" <?php echo ($report['alarmSeverity'] === 'Alarm 2') ? 'selected' : ''; ?>>Alarm 2</option>
                                            <option value="Alarm 3" <?php echo ($report['alarmSeverity'] === 'Alarm 3') ? 'selected' : ''; ?>>Alarm 3</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button style="background: white; display: block; margin: 1rem auto; cursor: pointer;" type="submit" name="update_button">
                                            Update
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
   </body>
</html>