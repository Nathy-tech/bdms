<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is a hospital
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'hospital') {
    header('Location: ../../index.php');
    exit();
}

$hospital_id = $_SESSION['user_id'];
$profile_picture = ''; // Initialize profile_picture

// Fetch hospital's current information
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $mysqli->prepare("SELECT name, email, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param('i', $hospital_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $profile_picture);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password']; // Password is optional

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $profile_picture = $_FILES['profile_picture']['name'];
        $target_dir = "../../uploads/profile_pictures/";
        $target_file = $target_dir . basename($profile_picture);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            // Success message
        } else {
            // Error message
        }
    }

    // Prepare the update query
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($profile_picture) {
            $sql = "UPDATE users SET name=?, profile_picture=?, password=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('sssi', $name, $profile_picture, $hashed_password, $hospital_id);
        } else {
            $sql = "UPDATE users SET name=?, password=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssi', $name, $hashed_password, $hospital_id);
        }
    } else {
        if ($profile_picture) {
            $sql = "UPDATE users SET name=?, profile_picture=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssi', $name, $profile_picture, $hospital_id);
        } else {
            $sql = "UPDATE users SET name=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('si', $name, $hospital_id);
        }
    }

    // Execute the update query
    if ($stmt->execute()) {
        header('Location: index.php'); // Redirect to hospital dashboard
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Personal Information</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
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
        <h1>Update Personal Information</h1>
        <form action="update_info.php" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="profile_picture">Profile Picture:</label>
            <?php if ($profile_picture): ?>
                <div>
                    <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%;">
                </div>
            <?php endif; ?>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank if not changing">

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
