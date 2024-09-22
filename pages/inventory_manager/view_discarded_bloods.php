<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    header('Location: ../../index.php');
    exit();
}

// Fetch discarded blood units with donor names
$query = "SELECT db.id, db.blood_type, db.donor_id, d.name AS donor_name 
          FROM discarded_bloods db
          JOIN users d ON db.donor_id = d.id";

$result = $mysqli->query($query);

if (!$result) {
    echo "Error fetching discarded blood units: " . $mysqli->error;
    exit();
}

$discarded_blood_units = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Discarded Bloods</title>
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
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            transition: background-color 0.3s;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
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
        <h1>View Discarded Bloods</h1>
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
        <a href="index.php" class="button">Back to Dashboard</a>
        <h2>Discarded Blood Units</h2>

        <?php if (count($discarded_blood_units) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Blood Type</th>
                        <th>Donor ID</th>
                        <th>Donor Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($discarded_blood_units as $unit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($unit['id']); ?></td>
                            <td><?php echo htmlspecialchars($unit['blood_type']); ?></td>
                            <td><?php echo htmlspecialchars($unit['donor_id']); ?></td>
                            <td><?php echo htmlspecialchars($unit['donor_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No discarded blood units found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Blood Donation Management System</p>
    </footer>
</body>
</html>
