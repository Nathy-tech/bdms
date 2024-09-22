<?php

include '../../includes/db.php'; // Include your database connection
include '../../includes/functions.php'; // Include functions such as send_verification_email


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $sex = $_POST['sex'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $verification_token = bin2hex(random_bytes(16));

    // Handle file upload (optional profile picture)
    $profile_picture = NULL;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $file_path = '../../uploads/profile_pictures/' . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_picture = $file_path;
            } else {
                echo "Error uploading profile picture.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Ensure the phone number starts with the Ethiopian country code
    if (strpos($phone, '+251') !== 0) {
        $phone = '+251' . ltrim($phone, '0'); // Remove leading zero if any
    }

    // Check if the email is already registered
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Error: The email address is already registered.";
    } else {
        // Insert into the 'users' table
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, sex, phone, address, role, verification_token, profile_picture) VALUES (?, ?, ?, ?, ?, ?, 'donor', ?, ?)");
        $stmt->bind_param('ssssssss', $name, $email, $password, $sex, $phone, $address, $verification_token, $profile_picture);
        
        if ($stmt->execute()) {
            send_verification_email($email, $verification_token); // Send email verification
            // Redirect to the login page after successful registration
            header('Location: ../login.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration</title>
    <link rel="stylesheet" href="../../css/styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 0;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #d32f2f;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #d32f2f;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b71c1c;
        }

        .file-input {
            margin-top: 10px;
        }

        .info-text {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group span {
            font-size: 14px;
            color: #999;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        /* Google Translate Widget */
        .google-translate {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .button-group a {
            display: inline-block;
            text-align: center;
            padding: 10px 20px;
            color: white;
            background-color: #888;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button-group a:hover {
            background-color: #555;
        }

        #password-strength-status {
            font-weight: bold;
            margin-top: 10px;
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
    <h1>Donor Registration</h1>
    <p class="info-text">Join our cause and save lives by donating blood.</p>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter your email address">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Create a strong password">
            <div id="password-strength-status"></div> <!-- Password strength status -->
        </div>

        <div class="form-group">
            <label for="sex">Gender</label>
            <select id="sex" name="sex" required>
                <option value="">--Select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <div>
                <input type="text" id="phone" name="phone" pattern="[0-9]{9}" required placeholder="Enter your phone number (without +251)">
                <span>Note: Enter phone number without country code (+251)</span>
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" required placeholder="Enter your full address">
        </div>

        <div class="form-group file-input">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            <span>Optional: Upload your profile picture</span>
        </div>

        <button type="submit">Register</button>

        <div class="button-group">
            <a href="../../index.php">Back to Landing Page</a>
            <a href="../login.php">Login</a>
        </div>
    </form>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const passwordStrengthStatus = document.getElementById('password-strength-status');

    passwordInput.addEventListener('input', function () {
        const password = passwordInput.value;
        const strength = getPasswordStrength(password);

        // Display the strength to the user
        passwordStrengthStatus.textContent = strength.text;
        passwordStrengthStatus.style.color = strength.color;
    });

    function getPasswordStrength(password) {
        let strength = { text: 'Weak', color: 'red' };

        if (password.length >= 8) {
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChars = /[!@#\$%\^&\*]/.test(password);

            if (hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars) {
                strength = { text: 'Strong', color: 'green' };
            } else if ((hasUpperCase && hasLowerCase && hasNumbers) || (hasLowerCase && hasNumbers && hasSpecialChars)) {
                strength = { text: 'Medium', color: 'orange' };
            }
        }

        return strength;
    }
</script>

</body>
</html>
