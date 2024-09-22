<?php
session_start();
include '../../includes/db.php';

// Check if the user is logged in and is a nurse
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'nurse') {
    header('Location: ../login.php'); // Redirect to the login page
    exit();
}

// Fetch the nurse's current profile information
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT name, phone, address, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $profile_picture = $user['profile_picture']; // Existing profile picture path

    // Ensure the phone number starts with the Ethiopian country code
    if (strpos($phone, '+251') !== 0) {
        $phone = '+251' . ltrim($phone, '0'); // Remove leading zero if any
    }

    // Handle profile picture upload if a file is uploaded
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../../uploads/profile_pictures/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES["profile_picture"]["size"] > 2000000) {
                echo "Sorry, your file is too large.";
            } else {
                // Allow certain file formats
                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                        $profile_picture = "uploads/profile_pictures/" . basename($_FILES["profile_picture"]["name"]);
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
                } else {
                    echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
                }
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Update nurse's profile information
    if (!empty($password)) {
        // If password is provided, update it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("
            UPDATE users
            SET name = ?, phone = ?, address = ?, password = ?, profile_picture = ?
            WHERE id = ?
        ");
        $stmt->bind_param('sssssi', $name, $phone, $address, $hashed_password, $profile_picture, $user_id);
    } else {
        // If password is not provided, update other details only
        $stmt = $mysqli->prepare("
            UPDATE users
            SET name = ?, phone = ?, address = ?, profile_picture = ?
            WHERE id = ?
        ");
        $stmt->bind_param('ssssi', $name, $phone, $address, $profile_picture, $user_id);
    }

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
    $stmt->close();

    // Fetch updated user information
    $stmt = $mysqli->prepare("SELECT name, phone, address, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
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
        <div class="header">
            <h1>Update Profile</h1>
            <!-- Display current profile picture -->
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="../../<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture"><br><br>
            <?php endif; ?>
        </div>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <div>
                    <span>+251</span>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars(str_replace('+251', '', $user['phone'])); ?>" required pattern="[0-9]{9}" title="Please enter a valid phone number without the country code.">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password">
                <small>Leave blank if you do not want to change your password.</small>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture">
                <small>Upload a new profile picture if you want to change it. (Max size: 2MB)</small>
            </div>

            <button type="submit">Update Profile</button>
        </form>

        <div class="footer">
            <a href="index.php">Back to Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
