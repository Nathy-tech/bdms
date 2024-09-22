<?php
session_start();
include '../../includes/db.php';

// Check if the user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'nurse') {
    header('Location: ../login.php'); // Redirect to the login page
    exit();
}

// Fetch only pending donation requests along with donor details
$stmt = $mysqli->prepare("
    SELECT dr.id, u.id AS donor_id, u.name AS donor_name, u.height, u.weight, u.blood_type, u.profile_picture, dr.appointment_date, dr.appointment_time
    FROM donation_requests dr
    JOIN users u ON dr.donor_id = u.id
    WHERE dr.status = 'pending'
");
$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle form submissions for collection and rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_request_id = $_POST['donation_request_id'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $blood_type = $_POST['blood_type'];  // Get the blood type entered by the nurse
    $collection_date = date('Y-m-d');
    $expiration_date = date('Y-m-d', strtotime($collection_date . ' +3 months'));

    // Get donor details from the donation request
    $stmt = $mysqli->prepare("SELECT donor_id FROM donation_requests WHERE id = ?");
    $stmt->bind_param('i', $donation_request_id);
    $stmt->execute();
    $donor_details = $stmt->get_result()->fetch_assoc();
    $donor_id = $donor_details['donor_id'];
    $stmt->close();

    // Update donor's medical information regardless of collection or rejection
    $stmt = $mysqli->prepare("
        UPDATE users
        SET height = ?, weight = ?, blood_type = ?
        WHERE id = ?
    ");
    $stmt->bind_param('sssi', $height, $weight, $blood_type, $donor_id);
    
    if ($stmt->execute()) {
        if (isset($_POST['record_collection'])) {
            // Mark the donation request as donated
            $stmt = $mysqli->prepare("UPDATE donation_requests SET status = 'donated' WHERE id = ?");
            $stmt->bind_param('i', $donation_request_id);
            $stmt->execute();

            // Insert the collected blood details into the blood_units table
            $stmt = $mysqli->prepare("
                INSERT INTO blood_units (donor_id, blood_type, collection_date, expiration_date) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('ssss', $donor_id, $blood_type, $collection_date, $expiration_date);
            $stmt->execute();

            // Update last donation date for the donor
            $stmt = $mysqli->prepare("UPDATE users SET last_donation_date = ? WHERE id = ?");
            $stmt->bind_param('si', $collection_date, $donor_id);
            $stmt->execute();

            echo "<p>Blood collection recorded successfully!</p>";
            $stmt->close();
        } elseif (isset($_POST['reject_request'])) {
            // Mark the donation request as rejected
            $stmt = $mysqli->prepare("UPDATE donation_requests SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param('i', $donation_request_id);
            $stmt->execute();
            
            echo "<p>Donation request rejected.</p>";
            $stmt->close();
        }
    } else {
        echo "<p>Error updating donor information: " . $stmt->error . "</p>";
    }

    // Refresh the page to reflect the changes
    header("Refresh: 2");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Blood Collection</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        header a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
        }
        header a:hover {
            text-decoration: underline;
        }
        h1 {
            color: #007BFF;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        select, input[type="text"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        img {
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px;
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
        function populateDonorDetails() {
            const requests = <?php echo json_encode($requests); ?>;
            const selectedId = document.getElementById('donation_request_id').value;
            const selectedRequest = requests.find(request => request.id == selectedId);

            if (selectedRequest) {
                document.getElementById('blood_type').value = selectedRequest.blood_type || '';
                document.getElementById('height').value = selectedRequest.height || '';
                document.getElementById('weight').value = selectedRequest.weight || '';
                
                const profilePicture = selectedRequest.profile_picture || 'default.jpg'; // Set default picture if none
                document.getElementById('donor_picture').src = `../../uploads/profile_pictures/${profilePicture}`;
            } else {
                document.getElementById('blood_type').value = '';
                document.getElementById('height').value = '';
                document.getElementById('weight').value = '';
                document.getElementById('donor_picture').src = '';
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
        <a href="index.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </header>
    
    <h1>Record Blood Collection</h1>
    
    <form method="POST" action="">
        <label for="donation_request_id">Select Donation Request:</label>
        <select id="donation_request_id" name="donation_request_id" onchange="populateDonorDetails()" required>
            <option value="">--Select Request--</option>
            <?php foreach ($requests as $request): ?>
                <option value="<?php echo htmlspecialchars($request['id']); ?>">
                    ID: <?php echo htmlspecialchars($request['id']); ?> - Donor ID: <?php echo htmlspecialchars($request['donor_id']); ?> - Donor Name: <?php echo htmlspecialchars($request['donor_name']); ?> - Date: <?php echo htmlspecialchars($request['appointment_date']); ?> - Time: <?php echo htmlspecialchars($request['appointment_time']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Donor Picture:</label><br>
        <img id="donor_picture" src="" alt="Donor Profile Picture" style="width: 100px; height: 100px;">

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
            <label for="height">Height in cm</label>
<input type="number" id="height" name="height" max="250" required>
<span id="heightError" style="color: red;"></span>

<script>
    document.getElementById('height').addEventListener('input', function() {
        var height = parseFloat(this.value);
        var heightError = document.getElementById('heightError');

        if (height > 250) {
            heightError.textContent = 'Height cannot exceed 250 cm.';
        } else {
            heightError.textContent = '';
        }
    });
</script>


        <label for="weight">Weight in kg</label>
<input type="number" id="weight" name="weight" min="45" required>
<span id="weightError" style="color: red;"></span>

<script>
    document.getElementById('weight').addEventListener('input', function() {
        var weight = parseFloat(this.value);
        var weightError = document.getElementById('weightError');

        if (weight < 45) {
            weightError.textContent = 'Weight must be at least 45 kg.';
        } else {
            weightError.textContent = '';
        }
    });
</script>


        <label for="collection_date">Collection Date:</label>
        <input type="date" id="collection_date" name="collection_date" value="<?php echo date('Y-m-d'); ?>" readonly>

        <label for="expiration_date">Expiration Date:</label>
        <input type="date" id="expiration_date" name="expiration_date" value="<?php echo date('Y-m-d', strtotime('+3 months')); ?>" readonly>

        <div class="actions">
            <button type="submit" name="record_collection">Record Collection</button>
            <button type="submit" name="reject_request">Reject Request</button>
        </div>
    </form>
</body>
</html>
