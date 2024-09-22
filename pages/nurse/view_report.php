<?php
include '../../includes/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

// Fetch the reports
$query = "SELECT id, filename, uploaded_at FROM reports";
$reports_result = $mysqli->query($query);

// Check if the query was successful
if (!$reports_result) {
    die('Error: ' . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reports</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Custom styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }

        main {
            padding: 20px;
        }

        h1, h2 {
            margin-top: 0;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .styled-table th, .styled-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .styled-table th {
            background-color: #007bff;
            color: white;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        p {
            color: #555;
        }
    </style>
</head>
<body>
    <header>
        <h1>View Reports</h1>
    </header>
    <main>
        <h2>Available Reports</h2>
        <?php if ($reports_result->num_rows > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($report = $reports_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report['filename']); ?></td>
                            <td><?php echo htmlspecialchars($report['uploaded_at']); ?></td>
                            <td>
                                <a href="../../uploads/reports/<?php echo htmlspecialchars($report['filename']); ?>" target="_blank" class="btn btn-primary">View</a>
                               
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reports available.</p>
        <?php endif; ?>
        <a href="index.php" class="btn-primary">Back to Dashboard</a>
    </main>
</body>
</html>
