<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report'])) {
    $file = $_FILES['report'];
    $target_dir = "../../uploads/reports/";
    $target_file = $target_dir . basename($file["name"]);
    
    // Check if the file is a PDF
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($fileType != 'pdf') {
        echo "Only PDF files are allowed.";
        exit();
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Insert info about the file into the database
        $insert_query = "INSERT INTO reports (filename, uploaded_at) VALUES (?, NOW())";
        $insert_stmt = $mysqli->prepare($insert_query);
        $filename = basename($file["name"]);
        $insert_stmt->bind_param('s', $filename);
        $insert_stmt->execute();
        
        echo "The file has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_id'])) {
    $report_id = $_POST['report_id'];
    
    // Fetch the report filename from the database
    $select_query = "SELECT filename FROM reports WHERE id = ?";
    $select_stmt = $mysqli->prepare($select_query);
    $select_stmt->bind_param('i', $report_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $report = $result->fetch_assoc();
    
    if ($report) {
        $filename = $report['filename'];
        $file_path = "../../uploads/reports/" . $filename;
        
        // Delete the file from the server
        if (file_exists($file_path) && unlink($file_path)) {
            // Delete the record from the database
            $delete_query = "DELETE FROM reports WHERE id = ?";
            $delete_stmt = $mysqli->prepare($delete_query);
            $delete_stmt->bind_param('i', $report_id);
            $delete_stmt->execute();
            
            echo "The file has been deleted.";
        } else {
            echo "Error deleting the file.";
        }
    } else {
        echo "File not found.";
    }
}

// Fetch uploaded reports
$fetch_reports_query = "SELECT id, filename, uploaded_at FROM reports";
$reports = $mysqli->query($fetch_reports_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload and Manage Reports</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Your custom styles here */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
        }
        .styled-table th, .styled-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .styled-table th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <header>
        <h1>Upload Report</h1>
    </header>
    <main>
        <h2>Upload New Report</h2>
        <form action="upload_report.php" method="POST" enctype="multipart/form-data">
            <label for="report">Choose PDF file:</label>
            <input type="file" id="report" name="report" accept=".pdf" required><br><br>
            <button type="submit" class="btn btn-primary">Upload Report</button>
        </form>
        
        <h2>Uploaded Reports</h2>
        <?php if ($reports->num_rows > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Filename</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($report = $reports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['id']); ?></td>
                            <td><?php echo htmlspecialchars($report['filename']); ?></td>
                            <td><?php echo htmlspecialchars($report['uploaded_at']); ?></td>
                            <td>
                                <a href="../../uploads/reports/<?php echo htmlspecialchars($report['filename']); ?>" target="_blank" class="btn btn-secondary">View</a>
                                <form action="upload_report.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reports available.</p>
        <?php endif; ?>
        
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>
</body>
</html>
