<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once('config.php');
session_start();

// Revert back if no existing session
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['barangay'])) {
        $barangay = $_SESSION['barangay'];
    }
} else {
    header("Location: index.php");
    exit;
}

// Count total fire reports
$fireReportCount = 0; // Initialize the fire report count
$queryFireReportCount = "SELECT COUNT(*) AS fire_report_count FROM reports";
$resultFireReportCount = mysqli_query($mysqli, $queryFireReportCount);
if ($resultFireReportCount) {
    $rowFireReportCount = mysqli_fetch_assoc($resultFireReportCount);
    $fireReportCount = $rowFireReportCount['fire_report_count'];
}

// Get all reports
$allReports = array();
$allReportsQuery = "SELECT * FROM reports ORDER BY timeStamp DESC";
$resultAllReports = mysqli_query($mysqli, $allReportsQuery);
while ($row = mysqli_fetch_assoc($resultAllReports)) {
    $allReports[] = $row;
}

echo '<script>var allReports = ' . json_encode($allReports) . ';</script>';

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
$userCount = 0;
//count unresolved per barangay
if (isset($_POST['barangays'])) {
    $selectedBarangay = $_POST['barangays'];

    // Query to count "Not Resolved" reports for the selected barangay
    $queryCountNotResolved = "SELECT COUNT(*) AS not_resolved_count FROM reports WHERE barangay = '$barangays' AND status = 'Not Resolved'";
    $resultCountNotResolved = mysqli_query($mysqli, $queryCountNotResolved);
    if ($resultCountNotResolved) {
        $rowCountNotResolved = mysqli_fetch_assoc($resultCountNotResolved);
        $notResolvedCount = $rowCountNotResolved['not_resolved_count'];
        //echo $notResolvedCount;
        //exit;
    }
    
    // Query to count users for the selected barangay
    $queryUserCount = "SELECT COUNT(*) AS user_count FROM users WHERE barangay = '$selectedBarangay'";
    $resultUserCount = mysqli_query($mysqli, $queryUserCount);

    if ($resultUserCount) {
        $rowCount = mysqli_num_rows($resultUserCount);

        if ($rowCount > 0) {
            $rowUserCount = mysqli_fetch_assoc($resultUserCount);
            $userCount = $rowUserCount['user_count'];
            console.log("User Count: $userCount");
        }
    } else {
        die("MySQL Error: " . mysqli_error($mysqli));
    }
}

?>

<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8" />
        <title>SIKLAB ADMIN DASHBOARD</title>
        <meta name="viewport" content="width=devide-width,initial-scale=1.0"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
        <link rel="stylesheet" href="style_centralAntipolo_barangays.css" />
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
                <a href="centralAntipolo_dashboard.php">
                    <span class="material-icons-sharp">grid_view</span>
                    <h3> Dashboard </h3>
                </a>
                <a href="centralAntipolo_reports.php" >
                <span class="material-icons-sharp" >report</span>
                    <h3> Fire Reports </h3>
                </a>
                <a href="centralAntipolo_barangay_reports.php" class="active">
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
            <h1 style="margin-bottom: 20px"> Select a Barangay </h1>
            <div class="custom_select">
                <select class="barangays-list" id="barangays" name="barangays">
                    <option value="Brgy. Bagong Nayon">Brgy. Bagong Nayon</option>
                    <option value="Brgy. Beverly Hills">Brgy. Beverly Hills</option>
                    <option value="Brgy. Cupang">Brgy. Cupang</option>
                    <option value="Brgy. Dalig">Brgy. Dalig</option>
                    <option value="Brgy. Dela Paz">Brgy. Dela Paz</option>
                    <option value="Brgy. Inarawan">Brgy. Inarawan</option>
                    <option value="Brgy. Mayamot">Brgy. Mayamot</option>
                    <option value="Brgy. San Isidro">Brgy. San Isidro</option>
                    <option value="Brgy. San Jose">Brgy. San Jose</option>
                    <option value="Brgy. San Luis">Brgy. San Luis</option>
                    <option value="Brgy. San Roque">Brgy. San Roque</option>
                    <option value="Brgy. Santa Cruz">Brgy. Santa Cruz</option>
                </select>
            </div>

            <div class="data">
                <div class="reports">
                    <span class="material-icons-sharp">analytics</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Fire Incidents Reported </h3>
                            <h1 id="reportCount"><?php echo $fireReportCount ?></h1>
                        </div>
                    </div>
                </div>
                <div class="notResolvedReports">
                    <span class="material-icons-sharp">warning</span>
                    <div class="middle">
                        <div class="left">
                            <h3> Not Resolved </h3>
                            <h1 id="notResolvedCount">0</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="recent-reports">

                <table style="background: white;">
                    <thead>
                        <th> Date and Time </th> 
                        <th> Address </th> 
                        <th> Barangay </th> 
                        <th> Contact Number </th> 
                        <th> Special Assistance </th> 
                        <th> View on Map </th> 
                        <th> Status </th> 
                        <th> Alarm Severity </th>
                    </thead>
                    <tbody>
                        <?php foreach ($allReports as $report) { ?>
                            <tr>
                                <form class="update-form" id="updateForm_<?php echo $report['reportID']; ?>" method="post" action="updateBarangay_status.php">
                                    <input type="hidden" name="report_id" value="<?php echo $report['reportID']; ?>">
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
                                    <td><?php echo $report['alarmSeverity']; ?></td>
                                </form>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <script>
                    document.getElementById('barangays').addEventListener('change', function() {
                        var selectedBarangay = this.value;
                        var filteredReports = allReports.filter(function(report) {
                            return report.barangay === selectedBarangay;
                        });
                    
                        // Update the table with filtered data
                        var tableBody = document.querySelector('tbody');
                        tableBody.innerHTML = ''; // Clear existing rows
                    
                        // Populate the table with filtered data
                        filteredReports.forEach(function(report) {
                            var row = '<tr>' +
                                '<td>' + report.timeStamp + '</td>' +
                                '<td>' + report.addressRep + '</td>' +
                                '<td>' + report.barangay + '</td>' +
                                '<td>' + report.contactNum + '</td>' +
                                '<td>' + report.assistanceRep + '</td>' +
                                '<td><button style="background: white; display: block; margin: 1rem auto; cursor: pointer;" ' +
                                'onclick="openMap(' + report.latitudeRep + ', ' + report.longitudeRep + ')">View</button></td>' +
                                '<td class="' + (report.status === 'Resolved' ? 'success' : 'danger') + '">' + report.status + '</td>' +
                                '<td>' + report.alarmSeverity + '</td>' +
                                '</tr>';
                            tableBody.innerHTML += row;
                        });
                    
                        // Count the number of reports for the selected barangay
                        var reportCount = filteredReports.length;
                        document.getElementById('reportCount').innerText = reportCount;
                    
                        // Count not resolved reports for the selected barangay
                        var notResolvedReportCount = filteredReports.reduce(function(acc, report) {
                            return report.status !== 'Resolved' ? acc + 1 : acc;
                        }, 0);
                    
                        // Update the "Not Resolved" tab with the count
                        document.getElementById('notResolvedCount').innerText = notResolvedReportCount;
                    });
                    const updateForms = document.querySelectorAll('.update-form');
                    updateForms.forEach((form) => {
                        form.addEventListener('submit', function (event) {
                            console.log("Form submitted!");
                            // Prevent the default form submission
                            event.preventDefault();
                            this.submit();
                        });
                    });
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
                window.open('/siklabprojectalpha/centralAntipolo_notifications.php')
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