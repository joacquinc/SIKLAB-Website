<?php
require_once('config.php'); // Include your database configuration
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_POST['extract7days'])) {
        // Check if $_SESSION['barangay'] is set and not empty
        if (isset($_SESSION['barangay']) && !empty($_SESSION['barangay'])) {
            // Sanitize the session value to prevent SQL injection
            $barangay = mysqli_real_escape_string($mysqli, $_SESSION['barangay']);

            // Calculate the date 7 days ago
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

            // Query to extract reports for the past 7 days in the specified barangay
            $query = "SELECT * FROM reports WHERE barangay = '$barangay' AND timeStamp >= '$sevenDaysAgo'";
            $result = mysqli_query($mysqli, $query);

            if ($result) {
                // Create a new Spreadsheet
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                // Set headers for the Excel file
                $sheet->setCellValue('A1', 'Report ID');
                $sheet->setCellValue('B1', 'User ID');
                $sheet->setCellValue('C1', 'Contact Number');
                $sheet->setCellValue('D1', 'Date and Time');
                $sheet->setCellValue('E1', 'Barangay');
                $sheet->setCellValue('F1', 'Address');
                $sheet->setCellValue('G1', 'Special Assistance');
                $sheet->setCellValue('H1', 'Status');
                $sheet->setCellValue('I1', 'Alarm Severity');
                $row = 2;

                // Initialize the total number of reports
                $totalReports = 0;

                // Display the retrieved reports
                while ($report = mysqli_fetch_assoc($result)) {
                    $sheet->setCellValue('A' . $row, $report['reportID']);
                    $sheet->setCellValue('B' . $row, $report['userID']);
                    $sheet->setCellValue('C' . $row, $report['contactNum']);
                    $sheet->setCellValue('D' . $row, $report['timeStamp']);
                    $sheet->setCellValue('E' . $row, $report['barangay']);
                    $sheet->setCellValue('F' . $row, $report['addressRep']);
                    $sheet->setCellValue('G' . $row, $report['assistanceRep']);
                    $sheet->setCellValue('H' . $row, $report['status']);
                    $sheet->setCellValue('I' . $row, $report['alarmSeverity']);
                    
                    // Text Wrap
                    // (text wrap code here...)

                    // Increment the total number of reports
                    $totalReports++;

                    $row++;
                }

                // Add the "Total Reports" after the last report
                $row += 2;

                // Set the alignment to left for "Total Reports" cell
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('left');
                $sheet->setCellValue('E' . $row, 'Total Reports');

                // Set the alignment to left for the value of total reports
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('left');
                $sheet->setCellValue('F' . $row, $totalReports);

                // Auto-size columns based on content
                foreach (range('A', 'Z') as $column) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                }

                // Create a writer for XLSX format
                $writer = new Xlsx($spreadsheet);

                // Define a file name for the Excel file
                $filename = 'extracted_reports.xlsx';

                // Set response headers to trigger a download
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                // Output the Excel file
                $writer->save('php://output');
                exit;
            } else {
                echo 'Error retrieving reports.';
            }
        }
    }
}
?>
