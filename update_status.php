<?php
require_once('config.php');

if (isset($_POST['update_button']) && isset($_POST['update_checkbox'])) {
    foreach ($_POST['update_checkbox'] as $reportId) {
        // Sanitize the report ID to prevent SQL injection
        $reportId = mysqli_real_escape_string($mysqli, $reportId);
        $alarmSeverity = mysqli_real_escape_string($mysqli, $_POST['alarm_severity']);
        
        // Update the database for the selected report ID
        $updateQuery = "UPDATE reports SET status = 'Resolved', alarmSeverity = '$alarmSeverity'  WHERE reportID = '$reportId'";
        $updateResult = mysqli_query($mysqli, $updateQuery);

        if ($updateResult) {
            //echo "Report with ID $reportId has been updated.<br>";
        } else {
            //echo "Error updating report with ID $reportId: " . mysqli_error($mysqli) . "<br>";
        }
    }
    // Redirect back to the previous page after updating
    header("Location: admin_reports.php");
    exit;
}elseif (isset($_POST['update_button']) && !isset($_POST['update_checkbox'])) {
    // "Update" button is clicked, and no checkboxes are checked
    $reportId = mysqli_real_escape_string($mysqli, $_POST['report_id']);
    $alarmSeverity = mysqli_real_escape_string($mysqli, $_POST['alarm_severity']);

    // Update the database for the selected report ID
    $updateQuery = "UPDATE reports SET alarmSeverity = '$alarmSeverity' WHERE reportID = '$reportId'";
    $updateResult = mysqli_query($mysqli, $updateQuery);

    if ($updateResult) {
        //echo "Report with ID $reportId has been updated.<br>";
        // Redirect back to the previous page after updating
        
    } else {
        //echo "Error updating report with ID $reportId: " . mysqli_error($mysqli) . "<br>";
    }
    header("Location: admin_reports.php");
    exit;
    
}

?>
