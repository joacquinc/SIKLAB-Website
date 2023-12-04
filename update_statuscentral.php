<?php
require_once('config.php');

if (isset($_POST['update_button'])) {
    if (isset($_POST['update_checkbox']) && is_array($_POST['update_checkbox']) && !empty($_POST['update_checkbox'])) {
        foreach ($_POST['update_checkbox'] as $reportId) {
            // Sansanitize the report ID to prevent SQL injection
            $reportId = mysqli_real_escape_string($mysqli, $reportId);
            
            // Update the database for the selected report ID
            $updateQuery = "UPDATE reports SET status = 'Resolved' WHERE reportID = '$reportId'";
            $updateResult = mysqli_query($mysqli, $updateQuery);

            if ($updateResult) {
                // Report with ID $reportId has been updated.
            } else {
                // Handle the error if the update fails
            }
        }
    } else {
        // Handle the case where the "Update" button is clicked, but no checkboxes are selected
        $reportId = mysqli_real_escape_string($mysqli, $_POST['report_id']);
        $alarmSeverity = mysqli_real_escape_string($mysqli, $_POST['alarm_severity']);

        $updateQuery = "UPDATE reports SET alarmSeverity = '$alarmSeverity' WHERE reportID = '$reportId'";
        $updateResult = mysqli_query($mysqli, $updateQuery);

        if ($updateResult) {
            // Report with ID $reportId has been updated.
        } else {
            // Handle the error if the update fails
        }
    }
}

// Redirect back to the previous page after updating
header("Location: centralAntipolo_reports.php");
exit;
?>
