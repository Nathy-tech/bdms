<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    header('Location: ../../index.php');
    exit();
}

$inventory_manager_id = $_SESSION['user_id'];

// Fetch all blood units from the blood_units table
$query = "SELECT id, blood_type, collection_date, expiration_date FROM blood_units";
$current_inventory = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Inventory</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #0056b3;
        }
        main {
            padding: 20px;
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        main h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }
        .styled-table thead tr {
            background-color: #f2f2f2;
        }
        .styled-table th, .styled-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .styled-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        footer {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        a {
            text-decoration: none;
        }
         /* Google Translate Widget */
         .google-translate {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
    <!-- Google Translate Widget Script -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'am,tig,en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</head>
<body>
     <!-- Google Translate Widget -->
     <div id="google_translate_element" class="google-translate"></div>

    <header>
        <h1>Blood Inventory System</h1>
    </header>
    <main>
        <h2>Current Blood Inventory</h2>
        <?php if ($current_inventory->num_rows > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Blood Type</th>
                        <th>Collected Date</th>
                        <th>Expiration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($blood = $current_inventory->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blood['id']); ?></td>
                            <td><?php echo htmlspecialchars($blood['blood_type']); ?></td>
                            <td><?php echo htmlspecialchars($blood['collection_date']); ?></td>
                            <td><?php echo htmlspecialchars($blood['expiration_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No blood units available in the inventory at the moment.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>
    <footer>
        <p>&copy; 2024 Blood Inventory System. All rights reserved.</p>
    </footer>
</body>
</html>
