<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../index.php');
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch distributed bloods with status 'pending'
$query = "SELECT id, donor_id, blood_type FROM distributed_bloods WHERE status = 'pending'";
$pending_distributions = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notify Donors</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        main {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .styled-table th, .styled-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .styled-table th {
            background-color: #007bff;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 14px;
            color: #fff;
            background-color: #4CAF50;
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        form {
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin - Notify Donors</h1>
    </header>
    <main>
        <h2>Pending Blood Distributions</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor ID</th>
                    <th>Blood Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($distribution = $pending_distributions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($distribution['id']); ?></td>
                        <td><?php echo htmlspecialchars($distribution['donor_id']); ?></td>
                        <td><?php echo htmlspecialchars($distribution['blood_type']); ?></td>
                        <td>
                            <form action="send_notification.php" method="POST">
                                <input type="hidden" name="distribution_id" value="<?php echo $distribution['id']; ?>">
                                <input type="hidden" name="donor_id" value="<?php echo $distribution['donor_id']; ?>">
                                <input type="hidden" name="blood_type" value="<?php echo $distribution['blood_type']; ?>">
                                <button type="submit" class="btn btn-primary">Notify</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>
</body>
</html>
