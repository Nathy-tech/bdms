<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    header('Location: ../../index.php');
    exit();
}

// Fetch inventory manager's profile information
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, phone, profile_picture FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $profile_picture);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Manager Dashboard</title>
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
        .profile-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-info img {
            border-radius: 50%;
            margin-right: 20px;
        }
        .profile-info p {
            margin: 5px 0;
            font-size: 18px;
            color: #666;
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
        <h1>Inventory Manager Dashboard</h1>
        <nav>
            <ul>
                <li><a href="discard_blood.php">Discard Blood Units</a></li>
                <li><a href="distribute_blood.php">Distribute Blood Units</a></li>
                <li><a href="view_inventory.php">View Blood Inventory</a></li>
                <li><a href="view_posted_information.php">information</a></li>
                <li><a href="update_info.php">Update Personal Information</a></li>
                <li><a href="view_report.php">View Report</a></li>
                <li><a href="give_comment.php">Give Comment</a></li>
                <li><a href="view_discarded_bloods.php">View Discarded Bloods</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        <div class="profile-info">
            <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 150px; height: 150px;">
            <div>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Blood Donation Management System</p>
    </footer>
</body>
</html>
