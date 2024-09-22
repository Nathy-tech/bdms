<?php
require_once '../../includes/db.php';
require_once '../../includes/fpdf/fpdf.php';

// Handle report generation and export
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_type = $_POST['report_type'];
    $export_to_pdf = isset($_POST['export_pdf']);
    $report_data = [];

    switch ($report_type) {
        case 'available_blood_units':
            $query = "SELECT blood_type, COUNT(id) AS quantity 
                      FROM blood_units 
                      GROUP BY blood_type";
            $title = "Available Blood Units";
            break;

        case 'discarded_blood':
            $query = "SELECT blood_type, COUNT(id) AS discarded_count, 
                      MAX(discarded_date) AS last_discarded_date 
                      FROM discarded_bloods 
                      GROUP BY blood_type";
            $title = "Discarded Blood";
            break;

        case 'requested_blood':
            $query = "SELECT blood_type, COUNT(hospital_id) AS hospital_id, 
                      MAX(created_at) AS last_request_date 
                      FROM hospital_requests 
                      WHERE status = 'pending'
                      GROUP BY blood_type";
            $title = "Requested Blood";
            break;

        case 'distributed_blood':
            $query = "SELECT blood_type, SUM(volume) AS total_volume, 
                      MAX(distributed_at) AS last_distribution_date 
                      FROM distributed_bloods 
                      GROUP BY blood_type";
            $title = "Distributed Blood";
            break;

        default:
            $message = "Invalid report type!";
            $query = "";
            break;
    }

    if (!empty($query)) {
        $result = $mysqli->query($query);
        if ($result) {
            $report_data = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $message = "Error: " . $mysqli->error;
        }
    }

    // Export to PDF if requested
    if ($export_to_pdf && !empty($report_data)) {
        exportReportToPDF($report_data, $title);
    }
}

// Function to export report data to PDF
function exportReportToPDF($report_data, $title)
{
    $pdf = new FPDF();
    $pdf->AddPage();

    // Add Gondar Blood Bank as the header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Gondar Blood Bank', 0, 1, 'C');
    
    // Add some space below the header
    $pdf->Ln(10);

    // Add the report title
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10); // Add some space

    // Set table headers based on report type
    $pdf->SetFont('Arial', 'B', 12);
    if ($title === 'Available Blood Units') {
        $headers = ['Blood Type', 'Quantity'];
    } elseif ($title === 'Discarded Blood') {
        $headers = ['Blood Type', 'Discarded Count', 'Last Discarded Date'];
    } elseif ($title === 'Requested Blood') {
        $headers = ['Blood Type', 'hospital_id', 'Last Request Date'];
    } elseif ($title === 'Distributed Blood') {
        $headers = ['Blood Type', 'Total Volume', 'Last Distribution Date'];
    }

    // Add table headers
    foreach ($headers as $header) {
        $pdf->Cell(60, 10, $header, 1);
    }
    $pdf->Ln();

    // Add table data
    $pdf->SetFont('Arial', '', 12);
    foreach ($report_data as $row) {
        foreach ($row as $data) {
            $pdf->Cell(60, 10, $data, 1);
        }
        $pdf->Ln();
    }

    // Output the PDF
    $pdf->Output('D', $title . '.pdf');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select, input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .nav-link {
            margin-top: 20px;
            text-align: center;
        }
        .nav-link a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .nav-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Report</h1>

        <?php if (isset($message)): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="report_type">Select Report Type:</label>
            <select id="report_type" name="report_type" required>
                <option value="">--Select Report--</option>
                <option value="available_blood_units">Available Blood Units</option>
                <option value="discarded_blood">Discarded Blood</option>
                <option value="requested_blood">Requested Blood</option>
                <option value="distributed_blood">Distributed Blood</option>
            </select>

            <button type="submit">Generate Report</button>
            <button type="submit" name="export_pdf" value="1">Export to PDF</button>
        </form>

        <?php if (isset($report_data) && !empty($report_data)): ?>
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <table>
                <thead>
                    <tr>
                        <?php if ($report_type === 'available_blood_units'): ?>
                            <th>Blood Type</th>
                            <th>Quantity</th>
                        <?php elseif ($report_type === 'discarded_blood'): ?>
                            <th>Blood Type</th>
                            <th>Discarded Count</th>
                            <th>Last Discarded Date</th>
                        <?php elseif ($report_type === 'requested_blood'): ?>
                            <th>Blood Type</th>
                            <th>Request Count</th>
                            <th>Last Request Date</th>
                        <?php elseif ($report_type === 'distributed_blood'): ?>
                            <th>Blood Type</th>
                            <th>Total Volume</th>
                            <th>Last Distribution Date</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $row): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="nav-link">
            <a href="index.php">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
