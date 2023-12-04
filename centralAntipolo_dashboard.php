<?php
require_once('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    
} else {
    header("Location: index.php");
    exit;
}

// Count total users
$userCount = 0; // Initialize the user count
$queryUserCount = "SELECT COUNT(*) AS user_count FROM users";
$resultUserCount = mysqli_query($mysqli, $queryUserCount);
if ($resultUserCount) {
    $rowUserCount = mysqli_fetch_assoc($resultUserCount);
    $userCount = $rowUserCount['user_count'];
}

// Count total fire reports
$fireReportCount = 0; // Initialize the fire report count
$queryFireReportCount = "SELECT COUNT(*) AS fire_report_count FROM reports";
$resultFireReportCount = mysqli_query($mysqli, $queryFireReportCount);
if ($resultFireReportCount) {
    $rowFireReportCount = mysqli_fetch_assoc($resultFireReportCount);
    $fireReportCount = $rowFireReportCount['fire_report_count'];
}

// Retrieve recent fire reports
$recentReports = array(); // Initialize an array to store the reports
$queryRecentReports = "SELECT * FROM reports ORDER BY timeStamp DESC LIMIT 3";
$resultRecentReports = mysqli_query($mysqli, $queryRecentReports);
if ($resultRecentReports) {
    while ($row = mysqli_fetch_assoc($resultRecentReports)) {
        $recentReports[] = $row;
    }
}

// Retrieve latest report
$recentReportLat;
$recentReportLong;
$queryRecentReport = "SELECT latitudeRep, longitudeRep FROM reports ORDER BY timeStamp DESC LIMIT 1";
$resultRecentReport = mysqli_query($mysqli, $queryRecentReport);
if ($resultRecentReport){
    $row = mysqli_fetch_assoc($resultRecentReport);
    $recentReportLat = $row['latitudeRep'];
    $recentReportLong = $row['longitudeRep'];
}

// Retrieve latest barangay 
$recentBarangay;
$queryRecentBarangay = "SELECT barangay FROM reports ORDER BY timeStamp DESC LIMIT 1";
$resultRecentBarangay = mysqli_query($mysqli, $queryRecentBarangay);
if ($resultRecentBarangay){
    $row = mysqli_fetch_assoc($resultRecentBarangay);
    $recentBarangay = $row['barangay'];
}

// Count all notif 
$notifCount;
$queryNotifCount = "SELECT COUNT(*) AS notif_count FROM notificationtable";
$resultNotifCount = mysqli_query($mysqli, $queryNotifCount);
if ($resultNotifCount){
    $row = mysqli_fetch_assoc($resultNotifCount);
    $notifCount = $row['notif_count'];
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

// Listener for Pop Up Dialog
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'requestAssistance') {
    // Handle incoming assistance request
    // Display a pop-up dialog to accept or decline the request
    echo 'Central Antipolo has received your message.'; // You can customize the message here.

    // You may want to implement a mechanism to send the response back to admin_dashboard.php in real-time.
}

?>


<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8" />
        <title>SIKLAB CENTRAL DASHBOARD</title>
        <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
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
        <script>
        // Function to handle the assistance request message
        function handleAssistanceRequest(event) {
            var data = event.data;

            if (data && data.latitude && data.longitude) {
                // Display the request assistance dialog here
                // You can use a modal or other UI components to create the dialog
                // Example:
                var latitude = data.latitude;
                var longitude = data.longitude;

                // Show a modal dialog or any custom UI element for assistance request
                // For example:
                var modal = document.getElementById('requestAssistanceModal');
                modal.style.display = 'block';
                // Set up the UI for assistance request, including a response mechanism
            }
        }

    // Listen for the message from admin_dashboard.php
    window.addEventListener('message', handleAssistanceRequest);
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
                <a href="centralAntipolo_reports.php">
                <span class="material-icons-sharp">report</span>
                    <h3> Fire Reports </h3>
                </a>
                <a href="centralAntipolo_barangay_reports.php">
                    <span class="material-icons-sharp">location_city</span>
                    <h3> Barangay List </h3>
                </a>
                <a href="centralAntipolo_notifications.php">
                    <span class="material-icons-sharp">notifications</span>
                    <h3> Notifications </h3>
                    <span id="notif_number" style="border-radius: 0.4rem; padding: 2px 10px; font-size: 11px; color: white; background: #db2121;">1</span>
                </a>
                <a href="centralAntipolo_settings.php">
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
            <h1> Dashboard </h1>

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
                <h2> Latest Report Location on Map </h2>
                
                <div id="map" style="width: 100%; height: 500px;"></div>

                <script>
                    mapboxgl.accessToken = 'pk.eyJ1IjoiZXpla2llbGNhcHoiLCJhIjoiY2xnODdtcWxhMDcxdjNocWxpOTJpeXlvdCJ9.hYBJ8R_gc4RT9jx0R0nteg';

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

                        // Add a marker at the center after the map has loaded
                        map.on('load', function() {
                            new mapboxgl.Marker().setLngLat([longitude, latitude]).addTo(map);

                            // add markers to map
                            fireStations.forEach((firestation) =>
                            {
                                const el = document.createElement('div');
                                el.classList.add('marker');
                                //el.style.backgroundImage = "url('marker.png')";
                                new mapboxgl.Marker(el)
                                    .setLngLat(firestation.geo_location)
                                    .setPopup(
                                        new mapboxgl.Popup({ offset: 25 }) // add popups
                                            .setHTML(
                                                `<h3>${firestation.fireStation}</h3>` + 
                                                `<button style="background: #de0909; color: white; display: block; margin: 1rem 0; padding: 1rem auto; width: 150px; height: 50px; cursor: pointer;" onClick="#"> Request Assistance </button>`
                                            )
                                    )
                                    .addTo(map);
                                console.log(firestation);
                            })
                        });
                    } else {
                        // Handle the case where latitude or longitude is not available
                        document.getElementById('map').textContent = 'Location coordinates are not available for the most recent report.';
                    }
                </script>
            </div>
        </main>
    </div>

   </body>

    <script type="text/javascript">

        if ("Notification" in window){
            if(Notification.permission === 'granted'){
                console.log("Notification granted.")
            } else {
                Notification.requestPermission().then((res) => {
                    if(res === 'granted'){
                        console.log("Notification granted.")
                    } else if (res === 'denied'){
                        console.log("Notification access denied");
                    } else if (res === 'default'){
                        console.log("Notification permission not given");
                    }
                })
            }
        } else {
            console.error("Notification not supported");
        }

        function notify(){
            const notification = new Notification("SIKLAB CENTRAL FIRE STATION", {
                body: `A barangay might be needing assistance.`,
                vibrate: [200, 100, 200],
            });

            notification.addEventListener('click', () => {
                window.open('centralAntipolo_notifications.php')
                notification.close();
            });
        }

        function loadDoc(){
            setInterval(function() {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200){
                        document.getElementById("notif_number").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "notif_count.php", true);
                xhttp.send();

            }, 1000);
            notify();
        }

        loadDoc();
    </script>

</html>