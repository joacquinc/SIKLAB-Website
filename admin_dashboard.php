<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['barangay'])) {
        $barangay = $_SESSION['barangay'];
    }
} else {
    header("Location: index.php");
    exit;
}

$userCount = 0;
$fireReportCount = 0;
$recentReports = array();
$recentReportLat;
$recentReportLong;
$recentBarangay;

if (isset($_SESSION['barangay']) && !empty($_SESSION['barangay'])) {
    $barangay = mysqli_real_escape_string($mysqli, $_SESSION['barangay']);

    // Count total users
    $queryUserCount = "SELECT COUNT(*) AS user_count FROM users WHERE barangay = '$barangay'";
    $resultUserCount = mysqli_query($mysqli, $queryUserCount);

    if ($resultUserCount) {
        $rowUserCount = mysqli_fetch_assoc($resultUserCount);
        $userCount = $rowUserCount['user_count'];
    }

    // Count total fire reports
    $queryFireReportCount = "SELECT COUNT(*) AS fire_report_count FROM reports WHERE barangay = '$barangay'";
    $resultFireReportCount = mysqli_query($mysqli, $queryFireReportCount);

    if ($resultFireReportCount) {
        $rowFireReportCount = mysqli_fetch_assoc($resultFireReportCount);
        $fireReportCount = $rowFireReportCount['fire_report_count'];
    }

    // Retrieve recent fire reports
    $queryRecentReports = "SELECT * FROM reports WHERE barangay = '$barangay' ORDER BY timeStamp DESC LIMIT 3";
    $resultRecentReports = mysqli_query($mysqli, $queryRecentReports);

    if ($resultRecentReports) {
        while ($row = mysqli_fetch_assoc($resultRecentReports)) {
            $recentReports[] = $row;
        }
    }

    // Retrieve latest report
    $queryRecentReport = "SELECT latitudeRep, longitudeRep FROM reports ORDER BY timeStamp DESC LIMIT 1";
    $resultRecentReport = mysqli_query($mysqli, $queryRecentReport);

    if ($resultRecentReport) {
        $row = mysqli_fetch_assoc($resultRecentReport);
        $recentReportLat = $row['latitudeRep'];
        $recentReportLong = $row['longitudeRep'];
    }

    // Retrieve latest barangay
    $queryRecentBarangay = "SELECT barangay FROM reports ORDER BY timeStamp DESC LIMIT 1";
    $resultRecentBarangay = mysqli_query($mysqli, $queryRecentBarangay);

    if ($resultRecentBarangay) {
        $row = mysqli_fetch_assoc($resultRecentBarangay);
        $recentBarangay = $row['barangay'];
    }

    // Send Notif
    if (isset($_POST['submit'])) {
    // Make sure the session variable is set
        if (isset($_SESSION['barangay'])) {
            $barangay = $_SESSION['barangay'];
    
            $status = $_POST['status'];
            $date = date("Y-m-d H:i:s"); 
            $alarm = $_POST['alarm'];
        
            // Use prepared statements for better security and to handle possible data types
            $insertNotifQuery = "INSERT INTO notificationtable (barangayName, status, timeStamp, alarm) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($mysqli, $insertNotifQuery);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $barangay, $status, $date, $alarm);
    
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    echo "Error: " . mysqli_error($mysqli);
                }
            } else {
                echo "Error in preparing the statement: " . mysqli_error($mysqli);
            }
        } else {
            echo "Session variable 'barangay' is not set.";
        }
    }


}

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
        <title>SIKLAB ADMIN DASHBOARD</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style_admin_dashboard.css" />
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />
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
                <a href="#" class="active">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3> Dashboard </h3>
                </a>
                <a href="admin_reports.php">
                <span class="material-icons-sharp">report</span>
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
            <h1><?php echo $barangay; ?> Dashboard </h1>
            <br>
            
            <div class="a_div">
                <form id="requestAssistance" method="post" action="admin_dashboard.php">
                    <input type="hidden" name="status" value="Pending">
                    <div class="custom_select" style="display: inline-block; background-color: white; font-size: 14px; font-family: poppins, sans-serif; width: 250px; border: 1px solid black;">
                        <select class="alarm-list" id="alarm" name="alarm" style="border: 1px solid-black; font-family: poppins, sans-serif; padding: 0 2rem; width: 100%; font-size: 18px;">
                            <option value="Alarm 1">Alarm 1</option>
                            <option value="Alarm 2">Alarm 2</option>
                            <option value="Alarm 3">Alarm 3</option>
                        </select>
                    </div>
                    <br> <br>
                    <button type="submit" name="submit" style="cursor: pointer;">
                        <h1> <span class="material-icons-sharp">live_help</span> Request Assistance </h1>
                    </button>
                </form>
            </div>

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
                <div class="latest_barangay">
                <span class="material-icons-sharp">location_city</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Latest Barangay Report </h3>
                            <h1><?php echo $recentBarangay; ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recent-reports">
                
                <h2 style="padding-top: 1rem; padding-left: 1rem;"> Recent Reports </h2>

                <table style="background: white;">
                    <thead>
                        <th> Date and Time </th> 
                        <th> Address </th> 
                        <th> Barangay </th> 
                        <th> Contact Number </th> 
                        <th> Special Assistance </th> 
                        <th> View on Map </th> 
                        <th> Status </th> 
                    </thead>
                    <tbody>
                        <?php foreach ($recentReports as $report) { ?>
                            <tr>
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
                            </tr>
                        <?php } ?>
                    </tbody>
                        <a href="admin_reports.php"> Show All Reports </a>
                </table>
            </div>        
            <div class="map-screen">
                <h2>Recent Fire Report</h2>
                <!-- Mapbox container for the static marker -->
                <div id="map" style="width: 100%; height: 500px;"></div>
                <script>
                    mapboxgl.accessToken = 'pk.eyJ1IjoiZXpla2llbGNhcHoiLCJhIjoiY2xnODdtcWxhMDcxdjNocWxpOTJpeXlvdCJ9.hYBJ8R_gc4RT9jx0R0nteg'; // Replace with your Mapbox access token
                    const latitude = <?php echo isset($recentReportLat) ? $recentReportLat : 0; ?>;
                    const longitude = <?php echo isset($recentReportLong) ? $recentReportLong : 0; ?>;

                    const fireStations = [
                    {
                        fireStation: "Antipolo City Fire Station",
                        geo_location: [121.1939298439968, 14.575617380192305], // lng lat 
                    },
                    {
                        fireStation: "Cainta Rizal Fire Station",
                        geo_location: [121.11502998626732, 14.579978425147186], // lng lat
                    },
                    {
                        fireStation: "Taytay Rizal Fire Station",
                        geo_location: [121.13056534047448, 14.553395478537187], // lng lat 
                    }
                    ];
                    
                    // Check if latitude and longitude are valid
                    if (!isNaN(latitude) && !isNaN(longitude)) {
                        const map = new mapboxgl.Map({
                            container: 'map', // container ID
                            style: 'mapbox://styles/ezekielcapz/cliwlgup1004t01qqg0avgzeq', // style URL
                            center: [longitude, latitude], // starting position [lng, lat]
                            zoom: 15 // starting zoom
                        });

                        // Create a marker with a custom popup
                        const marker = new mapboxgl.Marker()
                            .setLngLat([longitude, latitude])
                            .addTo(map)
                            .setPopup(new mapboxgl.Popup({ offset: 25 }) // Offset positions the popup to the side
                            .setHTML(`
                                <h3>Location Name</h3>
                                <button onclick="sendAssistanceRequest()">Request Assistance</button>
                            `));

                        const el = document.createElement('div');
                            el.classList.add('marker');
                            new mapboxgl.Marker(el)
                                .setLngLat([121.1939298439968, 14.575617380192305])
                                .setPopup(
                                    new mapboxgl.Popup({ offset: 25 }) // add popups
                                        .setHTML(
                                            `<h3>Central Antipolo Fire Station</h3>` + 
                                            `<button style="background: #de0909; color: white; display: block; margin: 1rem 0; padding: 1rem auto; width: 150px; height: 50px; cursor: pointer;" onClick="sendNotificationToCentralAntipolo()"> Request Assistance </button>`
                                        )
                                )
                                .addTo(map);

                        // Function to send assistance request
                        function sendAssistanceRequest() {
                            // Send a request to centralAntipolo_dashboard.php
                            var request = new XMLHttpRequest();
                            request.open('POST', 'centralAntipolo_dashboard.php', true);
                            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                            request.send('action=requestAssistance');

                            // Handle the response from centralAntipolo_dashboard.php
                            request.onreadystatechange = function () {
                                if (request.readyState === 4 && request.status === 200) {
                                    // Display the response in a pop-up dialog
                                    alert('Response from centralAntipolo_dashboard.php: ' + request.responseText);
                                }
                            };
                        }
                    } else {
                        // Handle the case where latitude or longitude is not available
                        document.getElementById('map').textContent = 'Location coordinates are not available for the most recent report.';
                    }
                </script>
            </div>
        </main>
    </div>
    <iframe id="centralAntipoloFrame" src="centralAntipolo.php" style="display: none;"></iframe>
</body>
</html>
