<?php
require_once('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    
} else {
    header("Location: index.php");
    exit;
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

// Retrieve recent fire reports
$recentReports = array(); // Initialize an array to store the reports
$queryRecentReports = "SELECT * FROM reports ORDER BY timeStamp DESC";
$resultRecentReports = mysqli_query($mysqli, $queryRecentReports);
if ($resultRecentReports) {
    while ($row = mysqli_fetch_assoc($resultRecentReports)) {
        $recentReports[] = $row;
    }
}


if(isset($_POST['lat']) && isset($_POST['lng'])){
    $latitude = $_POST['lat'];
    $longitude = $_POST['lng'];
    // Use $latitude and $longitude as needed in your map.php file
} else {
    echo "Latitude and/or longitude values not provided.";
}

?>

<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8" />
        <title>MAP REPORT</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style_admin_dashboard.css" />
        <script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />
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
                <a href="admin_reports.php" class="active">
                    <span class="material-icons-sharp" >report</span>
                    <h3> Fire Reports </h3>
                </a>
                <a href="admin_settings.php">
                    <span class="material-icons-sharp">settings</span>
                    <h3> Settings </h3>
                </a>
                <a href="#" onclick="document.getElementById('logoutForm').submit();">
                    <span class="material-icons-sharp">logout</span>
                    <h3> Log Out </h3>
                </a>
            </div>
        </aside>
        <!-- END OF ASIDE, START OF MAIN -->
        
        <div class="main">
            <h1>Map View of Report</h1>
            
            <div id="map" style="width: 100%; height: 90%;"></div>
            
            <script>
                mapboxgl.accessToken = 'pk.eyJ1IjoiZXpla2llbGNhcHoiLCJhIjoiY2xnODdtcWxhMDcxdjNocWxpOTJpeXlvdCJ9.hYBJ8R_gc4RT9jx0R0nteg';

                // Check if latitude and longitude values are provided
                <?php if(isset($latitude) && isset($longitude)): ?>
                    const latitude = <?php echo $latitude; ?>;
                    const longitude = <?php echo $longitude; ?>;
                    const map = new mapboxgl.Map({
                        container: 'map', // container ID
                        style: 'mapbox://styles/ezekielcapz/cliwlgup1004t01qqg0avgzeq', // style URL
                        center: [longitude, latitude], // starting position [lng, lat]
                        zoom: 15 // starting zoom
                    });

                    // Add a marker at the center after the map has loaded
                    map.on('load', function() {
                        new mapboxgl.Marker().setLngLat([longitude, latitude]).addTo(map);
                    });
                <?php else: ?>
                    // If latitude and longitude values are not provided, show an error message or handle it as appropriate
                    console.error('Latitude and longitude values not provided.');
                <?php endif; ?>
                </script>
        </div>
    </div>

   </body>
</html>
