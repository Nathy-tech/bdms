<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    header('Location: ../../index.php');
    exit();
}

// Fetch blood units where expiration_date is exactly one day after collection_date
$query = "SELECT id, blood_type, collection_date, expiration_date 
          FROM blood_units 
          WHERE DATEDIFF(expiration_date, collection_date) = 0";

$result = $mysqli->query($query);

if (!$result) {
    echo "Error fetching blood units: " . $mysqli->error;
    exit();
}

$expiring_blood_units = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiring Blood Units</title>
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
    <script>
        function handleDeleteClick(event, id) {
            event.preventDefault(); // Prevent the form from submitting

            if (confirm('Are you sure you want to delete this blood unit?')) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'discard.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            alert('Blood unit discarded successfully.');
                            location.reload(); // Refresh the page to update the table
                        } else {
                            alert('An error occurred: ' + xhr.responseText);
                        }
                    }
                };

                xhr.send('id=' + encodeURIComponent(id));
            }
        }
    </script>
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
        <h1>Inventory Manager Dashboard</h1>
        <nav>
            <ul>
                <li><a href="discard_blood.php">Discard Blood Units</a></li>
                <li><a href="distribute_blood.php">Distribute Blood Units</a></li>
                <li><a href="view_inventory.php">View Blood Inventory</a></li>
                <li><a href="update_info.php">Update Personal Information</a></li>
                <li><a href="give_comment.php">Give Comment</a></li>
                <li><a href="view_discarded_bloods.php">View Discarded Bloods</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Blood Units Expiring Today</h2>

        <?php if (count($expiring_blood_units) > 0): ?>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Blood Type</th>
                        <th>Collection Date</th>
                        <th>Expiration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expiring_blood_units as $unit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($unit['id']); ?></td>
                            <td><?php echo htmlspecialchars($unit['blood_type']); ?></td>
                            <td><?php echo htmlspecialchars($unit['collection_date']); ?></td>
                            <td><?php echo htmlspecialchars($unit['expiration_date']); ?></td>
                            <td>
                                <form onsubmit="handleDeleteClick(event, <?php echo htmlspecialchars($unit['id']); ?>);">
                                    <button type="submit" class="btn btn-danger">Discard</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No blood units expired</p>
        <?php endif; ?>

        <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Blood Donation Management System</p>
    </footer>
</body>
</html>
