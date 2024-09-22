<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch admin-specific data
$query = "SELECT name, email, profile_picture FROM users WHERE id = ? AND role = 'admin'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();

include '../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: #fff;
            padding: 15px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar nav {
            display: flex;
            gap: 30px;
        }
        .navbar nav a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .navbar nav a:hover {
            background-color: #0056b3;
        }
        .profile-container {
            margin-top: 80px; /* Adjust for fixed navbar */
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            margin: 0;
            color: #007bff;
        }
        .profile {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-right: 20px;
            border: 2px solid #007bff;
        }
        .profile-info {
            color: #333;
        }
        .profile-info h2 {
            margin: 0 0 10px;
            font-size: 24px;
            color: #007bff;
        }
        .profile-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        .logout {
            display: block;
            background-color: #dc3545;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            max-width: 200px;
            margin: auto;
        }
        .logout:hover {
            background-color: #c82333;
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
    <!-- Navigation Bar -->
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="create_account.php">Create Account</a>
            <a href="edit_account.php">Edit Account</a>
            <a href="manage_account.php">Manage Account</a>
            <a href="post_information.php">Post Information</a>
            <a href="generate_report.php">Generate Report</a>
            <a href="upload_report.php">upload Report</a>
            <a href="view_comments.php">view comments</a>
            <a href="update_info.php">update profile</a>
            <a href="notify_distributed_bloods.php">distributed bloods</a>
        </nav>
       
    </div>

    <!-- Profile Information -->
    <div class="profile-container">
        <div class="profile">
            <?php if ($profile_picture): ?>
                <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="../../uploads/profile_pictures/default.png" alt="Default Profile Picture">
            <?php endif; ?>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($name); ?></h2>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
