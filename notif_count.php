<?php
    require_once('config.php');

    // Count all notif 
    $notifCount;
    $queryNotifCount = "SELECT COUNT(*) AS notif_count FROM notificationtable WHERE status = 'Pending'";
    $resultNotifCount = mysqli_query($mysqli, $queryNotifCount);
    if ($resultNotifCount){
        $row = mysqli_fetch_assoc($resultNotifCount);
        $notifCount = $row['notif_count'];

        echo $notifCount;
    }

?>
