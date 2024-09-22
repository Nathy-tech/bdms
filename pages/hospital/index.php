<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hospital') {
    header("Location: ../login.php");
    exit();
}

// Fetch hospital-specific data
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, profile_picture FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
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
    <title>Hospital Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        nav {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav li {
            margin: 0 15px;
        }
        nav a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info p {
            font-size: 18px;
            color: #555;
        }
        .profile-info img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            text-decoration: none;
        }
        .button:hover {
            background-color: #0056b3;
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
        <h1>Hospital Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="request_blood.php">Request Blood</a></li>
            <li><a href="view_requests.php">View Requests</a></li>
            <li><a href="give_comment.php">Give Feedback</a></li>
            <li><a href="view_posted_information.php">Information</a></li>
            <li><a href="view_report.php">View Report</a></li>
            <li><a href="update_info.php">update profile</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="profile-info">
            <?php if ($profile_picture): ?>
                <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php endif; ?>
            <p><strong>Hospital Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($email); ?></p>
        </div>
        <a href="../logout.php" class="button">Logout</a>
    </div>
</body>
</html>
