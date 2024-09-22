<?php
include '../../includes/db.php';
include '../../includes/functions.php';
session_start();

// Check if the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header('Location: ../../index.php'); // Redirect to the login page
    exit();
}

// Get the donor ID
$donor_id = $_SESSION['user_id'];

// Fetch donor details
$stmt = $mysqli->prepare("SELECT name, email, last_donation_date, height, weight, blood_type, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$stmt->bind_result($name, $email, $last_donation_date, $height, $weight, $blood_type, $profile_picture);
$stmt->fetch();
$stmt->close();

// Calculate the next eligible donation request date (3 months after last donation)
$next_donation_date = '';
if ($last_donation_date) {
    $next_donation_date = date('Y-m-d', strtotime($last_donation_date . ' +3 months'));
}

// Check if the donor is eligible to make a new request
$can_request = true;
$next_donation_date_message = '';
if ($next_donation_date && date('Y-m-d') < $next_donation_date) {
    $can_request = false;
    $next_donation_date_message = "You can request a donation after " . $next_donation_date;
}

// Fetch recent donation requests
$stmt = $mysqli->prepare("SELECT date_requested, status FROM donation_requests WHERE donor_id = ? ORDER BY date_requested DESC LIMIT 5");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch notifications for the donor
$stmt = $mysqli->prepare("SELECT id, message, sent_at AS created_at FROM notifications WHERE donor_id = ? ORDER BY sent_at DESC");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background: #007bff;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .profile-card img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-right: 20px;
        }
        .profile-card h2 {
            margin: 0 0 10px;
        }
        .profile-card p {
            margin: 5px 0;
        }
        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .card h3 {
            margin-top: 0;
        }
        .countdown {
            font-size: 1.2em;
            font-weight: bold;
            color: #d9534f;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background: #f9f9f9;
            margin: 10px 0;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        a {
            display: inline-block;
            color: #ffffff;
            background: #007bff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin: 5px;
            text-align: center;
        }
        a:hover {
            background: #0056b3;
        }
        .button-group {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .button-group a {
            margin: 5px;
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
        function calculateCountdown(targetDate) {
            const countdownElement = document.getElementById('countdown');
            const endDate = new Date(targetDate);
            const timer = setInterval(function() {
                const now = new Date();
                const timeRemaining = endDate - now;
                if (timeRemaining <= 0) {
                    clearInterval(timer);
                    countdownElement.innerHTML = "You can now request a donation!";
                    return;
                }
                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
                countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            }, 1000);
        }

        window.onload = function() {
            const nextDonationDate = "<?php echo $next_donation_date; ?>";
            if (nextDonationDate) {
                calculateCountdown(nextDonationDate);
            } else {
                document.getElementById('countdown').innerHTML = "No previous donation records found.";
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

    <div class="container">
        <div class="header">
            <h1>Donor Dashboard</h1>
        </div>

        <div class="button-group">
            <?php if ($can_request): ?>
                <a href="request_donation.php">Request a Donation</a>
            <?php endif; ?>
            <a href="give_comment.php">Give Feedback</a>
            <a href="update_personal_info.php">Update Info</a>
            <a href="view_posted_information.php">Inofrmation</a>
            <a href="view_report.php">View Reports</a>
            <a href="../logout.php">Logout</a>
        </div>

        <div class="profile-card">
            <?php if ($profile_picture): ?>
                <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="../../uploads/profile_pictures/default.png" alt="Default Profile Picture">
            <?php endif; ?>
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($name); ?></h2>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>
                <p>Height: <?php echo htmlspecialchars($height); ?></p>
                <p>Weight: <?php echo htmlspecialchars($weight); ?></p>
                <p>Blood Type: <?php echo htmlspecialchars($blood_type); ?></p>
            </div>
        </div>

        <div class="card">
            <h3>Next Donation Request</h3>
            <p id="countdown" class="countdown">
                <?php if ($can_request): ?>
                    <!-- Countdown will be displayed here -->
                <?php else: ?>
                    You can request again on: <?php echo htmlspecialchars($next_donation_date); ?>
                <?php endif; ?>
            </p>
        </div>

        <div class="card">
            <h3>Recent Donation Requests</h3>
            <ul>
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $request): ?>
                        <li>
                            <?php echo htmlspecialchars($request['date_requested']); ?> - Status: <?php echo htmlspecialchars($request['status']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No recent donation requests.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card">
            <h3>Notifications</h3>
            <ul>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li>
                            <p><?php echo htmlspecialchars($notification['message']); ?></p>
                            <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No notifications.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
