<?php
session_start();
include '../../includes/db.php';

// Check if the user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'nurse') {
    header('Location: ../login.php'); // Redirect to the login page
    exit();
}

// Handle form submissions for updating medical info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_id = $_POST['donor_id'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $blood_type = $_POST['blood_type'];

    // Update donor's medical information (excluding sex)
    $stmt = $mysqli->prepare("
        UPDATE users
        SET height = ?, weight = ?, blood_type = ?
        WHERE id = ?
    ");
    $stmt->bind_param('sssi', $height, $weight, $blood_type, $donor_id);
    
    if ($stmt->execute()) {
        // Redirect back to the form after successful update to clear fields
        header("Location: update_medical_info.php");
        exit();
    } else {
        echo "<p class='error'>Error updating medical information: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch donors for selection
$stmt = $mysqli->prepare("SELECT id, name FROM users WHERE role = 'donor'");
$stmt->execute();
$donors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch selected donor's information
$donor = null;
$donor_id = null;
if (isset($_GET['donor_id']) && !empty($_GET['donor_id'])) {
    $donor_id = $_GET['donor_id'];
    $stmt = $mysqli->prepare("SELECT name, height, weight, blood_type, sex, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param('i', $donor_id);
    $stmt->execute();
    $donor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Medical Information</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Same styling as before */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .profile-picture {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        .profile-picture img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .back-links {
            text-align: center;
            margin-top: 20px;
        }
        .back-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007BFF;
        }
        .back-links a:hover {
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
        <h1>Update Medical Information</h1>
        
        <form method="GET" action="">
            <label for="donor_id">Select Donor:</label>
            <select id="donor_id" name="donor_id" required onchange="this.form.submit()">
                <option value="">--Select Donor--</option>
                <?php foreach ($donors as $donor_item): ?>
                    <option value="<?php echo htmlspecialchars($donor_item['id']); ?>" <?php echo isset($donor) && $donor_id == $donor_item['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($donor_item['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($donor): ?>
            <form method="POST" action="">
                <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donor_id); ?>">

                <div class="profile-picture">
                    <?php if (!empty($donor['profile_picture'])): ?>
                        <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($donor['profile_picture']); ?>" alt="Profile Picture">
                    <?php endif; ?>
                </div>

                <p><strong>Name:</strong> <?php echo htmlspecialchars($donor['name']); ?></p>
                <p><strong>Sex:</strong> <?php echo htmlspecialchars($donor['sex']); ?></p>

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

                <button type="submit">Update Information</button>
            </form>
        <?php else: ?>
            <p>Please select a donor to view their medical information.</p>
        <?php endif; ?>

        <div class="back-links">
            <a href="index.php">Back to Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
