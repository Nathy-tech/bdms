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

// Check for existing donation requests
$stmt = $mysqli->prepare("
    SELECT id, status 
    FROM donation_requests 
    WHERE donor_id = ? 
    AND status = 'pending'
    ORDER BY date_requested DESC 
    LIMIT 1
");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$existing_request = $stmt->get_result()->fetch_assoc();
$stmt->close();

$can_request = !$existing_request; // Check if the donor can make a new request

// Handle form submission if no existing request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $can_request) {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Prepare and execute SQL statement
    $stmt = $mysqli->prepare("INSERT INTO donation_requests (donor_id, appointment_date, appointment_time) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $donor_id, $appointment_date, $appointment_time);

    if ($stmt->execute()) {
        $message = "Donation request submitted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Donation</title>
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
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-container label {
            font-weight: bold;
            color: #555;
        }
        .form-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-container button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            font-size: 18px;
            color: #d9534f;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .links a {
            text-decoration: none;
            color: #007bff;
            font-size: 16px;
        }
        .links a:hover {
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set today's date as the minimum date for appointment
            const appointmentDate = document.getElementById('appointment_date');
            const today = new Date().toISOString().split('T')[0];
            appointmentDate.setAttribute('min', today);
        });
    </script>
</head>
<body>
    <!-- Google Translate Widget -->
    <div id="google_translate_element" class="google-translate"></div>

    <div class="container">
        <h1>Request a Donation</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="form-container">
            <?php if ($can_request): ?>
                <form method="POST" action="">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="date" id="appointment_date" name="appointment_date" required>

                    <label for="appointment_time">Appointment Time:</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>

                    <button type="submit">Submit Request</button>
                </form>
            <?php else: ?>
                <p class="message">You have an existing pending request. Please wait until it is processed before making a new request.</p>
            <?php endif; ?>
        </div>

        <div class="links">
            <a href="index.php">Back to Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
