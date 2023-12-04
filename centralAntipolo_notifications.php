<?php
require_once('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    
} else {
    header("Location: index.php");
    exit;
}

// Count total notif
$notifCount = 0; // Initialize the user count
$queryNotifCount = "SELECT COUNT(*) AS notif_count FROM notificationtable";
$resultNotifCount = mysqli_query($mysqli, $queryNotifCount);
if ($resultNotifCount) {
    $rowNotifCount = mysqli_fetch_assoc($resultNotifCount);
    $notifCount = $rowNotifCount['notif_count'];
}

// Count total pending notif
$pendingReportCount = 0; // Initialize the fire report count
$queryPendingReportCount = "SELECT COUNT(*) AS pending_reports FROM notificationtable WHERE status = 'Pending'";
$resultPendingReportCount = mysqli_query($mysqli, $queryPendingReportCount);
if ($resultPendingReportCount) {
    $rowPendingReportCount = mysqli_fetch_assoc($resultPendingReportCount);
    $pendingReportCount = $rowPendingReportCount['pending_reports'];
}

// Retrieve recent fire reports
$recentNotifications = array(); // Initialize an array to store the reports
$queryRecentNotifs = "SELECT notificationID, barangayName, status, timeStamp, alarm  FROM notificationtable ORDER BY timeStamp DESC;";
$resultRecentNotifs = mysqli_query($mysqli, $queryRecentNotifs);
if ($resultRecentNotifs) {
    while ($row = mysqli_fetch_assoc($resultRecentNotifs)) {
        $recentNotifications[] = $row;
    }
}

// Retrieve latest barangay 
$recentBarangay;
$queryRecentBarangay = "SELECT barangayName FROM notificationtable ORDER BY timeStamp DESC LIMIT 1";
$resultRecentBarangay = mysqli_query($mysqli, $queryRecentBarangay);
if ($resultRecentBarangay){
    $row = mysqli_fetch_assoc($resultRecentBarangay);
    $recentBarangay = $row['barangayName'];
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

if(isset($_POST['approved'])) {
    $notifID = $_POST['notifID'];

    // Update the database for the selected report ID
    $updateQuery = "UPDATE notificationtable SET status = 'Approved' WHERE notificationID = '$notifID'";
    $updateResult = mysqli_query($mysqli, $updateQuery);

    if($updateResult) {
        header("Location: centralAntipolo_notifications.php");
    } else {
        echo "Error updating report with ID $notifID: " . mysqli_error($mysqli) . "<br>";
    }
    
}

if(isset($_POST['declined'])) {
    $notifID = $_POST['notifID'];

    // Update the database for the selected report ID
    $updateQuery = "UPDATE notificationtable SET status = 'Declined' WHERE notificationID = '$notifID'";
    $updateResult = mysqli_query($mysqli, $updateQuery);

    if($updateResult) {
        header("Location: centralAntipolo_notifications.php");
    } else {
        echo "Error updating report with ID $notifID: " . mysqli_error($mysqli) . "<br>";
    }
    
}

?>

<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8" />
        <title>SIKLAB CENTRAL DASHBOARD</title>
        <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style_centralAntipolo_notifications.css" />
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
                <a href="centralAntipolo_dashboard.php">
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
                <a href="#" class="active">
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
            <h1> Notifications Panel </h1>

            <div class="data">
                <div class="reports">
                    <span class="material-icons-sharp">analytics</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Notifications Received </h3>
                            <h1><?php echo $notifCount ?></h1>
                        </div>
                    </div>
                </div>
                <div class="users">
                    <span class="material-icons-sharp">person</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Pending Assistance </h3>
                            <h1><?php echo $pendingReportCount; ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recent-reports">
                
                <h2 style="padding-top: 1rem; padding-left: 1rem;"> Recent Reports </h2>

                <table style="background: white;">
                    <thead>
                        <th> Date and Time </th> 
                        <th> Alarm Level </th> 
                        <th> Barangay </th> 
                        <th> Status </th> 
                        <th> &nbsp; </th> 
                        <th> &nbsp; </th> 
                    </thead>
                    <tbody>
                        <?php foreach ($recentNotifications as $notif) { ?>
                            <tr>
                                <td><?php echo $notif['timeStamp']; ?></td>
                                <td><?php echo $notif['alarm']; ?></td>
                                <td><?php echo $notif['barangayName']; ?></td>
                                <td class="<?php echo ($notif['status'] == 'Approved') ? 'success' : 'danger'; ?>">
                                    <?php echo $notif['status']; ?>
                                </td>
                                <td>
                                    <form id="changeRequest" method="post" action="centralAntipolo_notifications.php">
                                        <input type="hidden" name="notifID" value="<?php echo $notif['notificationID'] ?>">
                                        <input type="hidden" name="approved" value="approved" > 
                                        <button style="background: white; display: block; margin: 1rem auto; cursor: pointer;" type="submit" name="approve_button">
                                            Approve
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form id="changeRequest" method="post" action="centralAntipolo_notifications.php">
                                        <input type="hidden" name="notifID" value="<?php echo $notif['notificationID'] ?>">
                                        <input type="hidden" name="declined" value="declined" > 
                                        <button style="background: white; display: block; margin: 1rem auto; cursor: pointer;" type="submit" name="decline_button">
                                            Decline
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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