<?php
include '../../includes/db.php';
include '../../includes/functions.php';
session_start();

// Check if the user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'nurse') {
    header('Location: ../../index.php'); // Redirect to the login page
    exit();
}

// Get the nurse ID
$nurse_id = $_SESSION['user_id'];

// Fetch nurse details
$stmt = $mysqli->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $nurse_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .header h1 {
            margin: 10px 0;
            font-size: 28px;
            color: #333;
        }
        .header p {
            font-size: 16px;
            color: #666;
        }
        .dashboard-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .nav a {
            display: block;
            padding: 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s, transform 0.2s;
        }
        .nav a:hover {
            background-color: #0056b3;
            transform: scale(1.02);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        .footer a:hover {
            text-decoration: underline;
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

    <div class="container">
        <div class="dashboard-title">Nurse Dashboard</div>
        <div class="header">
            <?php if ($profile_picture): ?>
                <img src="../../<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="../../uploads/profile_pictures/default.png" alt="Default Profile Picture">
            <?php endif; ?>
            <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
            <p>Your email: <?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="nav">
            <a href="view_appointments.php">View Upcoming Appointments</a>
            <a href="record_blood_collection.php">Record Blood Collection</a>
            <a href="update_medical_info.php">Update Medical Information</a>
            <a href="view_report.php">View Report</a>
            <a href="view_posted_information.php">Information</a>
            <a href="give_comment.php">Give Comment</a>
            <a href="update_profile.php">update profile</a>
        </div>

        <div class="footer">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
