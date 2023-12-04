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
            $query = "SELECT * FROM reports WHERE timeStamp >= '$sevenDaysAgo' ORDER BY barangay";
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

                // Initialize an array to store the totals per barangay
                $barangayTotals = array();
                $totalReports = 0; // Initialize the total reports count

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

                    // Count the total number of reports per barangay
                    $currentBarangay = $report['barangay'];
                    if (!isset($barangayTotals[$currentBarangay])) {
                        $barangayTotals[$currentBarangay] = 0;
                    }
                    $barangayTotals[$currentBarangay]++;
                    
                    // Increment the total reports count
                    $totalReports++;
                    $row++;
                }

                // Add the "Total" count after the last report
                $row += 2;
                $sheet->setCellValue('E' . $row, 'Total');

                // Set the alignment to left for the "Total" cell
                $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('left');

                $sheet->setCellValue('F' . $row, $totalReports);

                // Set the alignment to left for the "Total" count
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('left');

                // Add a header for the barangay totals
                $row += 2;
                $sheet->setCellValue('E' . $row, 'Total Count by Barangay');

                // Display the barangay totals
                $row++;

                foreach ($barangayTotals as $barangay => $count) {
                    $sheet->setCellValue('E' . $row, $barangay);
                    // Set alignment to left for the barangay name
                    $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal('left');
                    $sheet->setCellValue('F' . $row, $count);
                    // Set alignment to left for the count
                    $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal('left');
                    $row++;
                }

                // Auto-size columns based on content
                foreach (range('A', 'Z') as $column) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
                }

                // Create a writer for XLSX format
                $writer = new Xlsx($spreadsheet);

                // Define a file name for the Excel file
                $filename = 'central_extracted_reports.xlsx';

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
