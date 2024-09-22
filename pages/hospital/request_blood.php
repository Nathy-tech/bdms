<?php
include '../../includes/db.php';
include '../../includes/functions.php'; // Include common functions for sending email, SMS, etc.
session_start();

// Ensure the user is logged in and is a hospital
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hospital') {
    header('Location: ../../index.php');
    exit();
}

$hospital_id = $_SESSION['user_id']; // Directly use user_id as hospital_id

// Handle blood request
if (isset($_POST['request_blood'])) {
    $blood_type = $_POST['blood_type'];
    $volume = $_POST['volume'];

    // Validate hospital_id (ensure it exists in the users table)
    $query = "SELECT id FROM users WHERE id = ? AND role = 'hospital'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hospital ID is valid
        $query = "INSERT INTO hospital_requests (hospital_id, blood_type, volume, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('isi', $hospital_id, $blood_type, $volume);

        if ($stmt->execute()) {
            $message = "Blood request has been successfully submitted.";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
    } else {
        $message = "Invalid hospital ID.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Blood</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
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
        <h1>Request Blood</h1>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="blood_type">Select Blood Type:</label>
            <select name="blood_type" id="blood_type" required>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>

            <label for="volume">Volume (in ml):</label>
            <input type="number" name="volume" id="volume" required>

            <button type="submit" name="request_blood">Request Blood</button>
        </form>

        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
