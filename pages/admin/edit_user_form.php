<?php
include '../../includes/db.php'; // Include your database connection

// Check if the user is an admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied!";
    exit();
}

$user = null; // Initialize $user

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the specific user
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ? AND role != 'donor'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // If user does not exist or is a donor, show a message
    if (!$user) {
        echo "No valid user found or user is a donor.";
        exit();
    }

} else {
    echo "No user ID provided.";
    exit();
}

// Handle form submission for updating the user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_picture = $_FILES['profile_picture']['name'];

    // Upload profile picture if new one is provided
    if ($profile_picture) {
        $target_dir = "../../uploads/profile_pictures/";
        $target_file = $target_dir . basename($profile_picture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
    } else {
        $profile_picture = $user['profile_picture']; // Keep existing picture if no new one provided
    }

    // Update user details excluding email
    $stmt = $mysqli->prepare("UPDATE users SET name = ?, sex = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param('sssssi', $name, $sex, $phone, $address, $profile_picture, $id);

    if ($stmt->execute()) {
        header('Location: edit_account.php?status=success'); // Redirect with success message
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
    <title>Edit User</title>
    <link rel="stylesheet" href="../../css/styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            border: none;
            padding: 0;
        }
        .form-group img {
            border-radius: 4px;
            margin-top: 10px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        .button-group a:hover {
            text-decoration: underline;
        }
        p.message {
            color: green;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-weight: bold;
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
    <h1>Edit User</h1>
    
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <p class="message">Account updated successfully!</p>
    <?php endif; ?>

    <?php if ($user): ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" required>
                    <option value="male" <?php echo ($user['sex'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($user['sex'] === 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture">
                <?php if ($user['profile_picture']): ?>
                    <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="100">
                <?php endif; ?>
            </div>

            <button type="submit">Update Account</button>
        </form>
    <?php else: ?>
        <p class="error-message">User data not available. Please check the ID and try again.</p>
    <?php endif; ?>
    
    <div class="button-group">
        <a href="edit_account.php">Back to Edit Accounts</a>
    </div>
</div>

</body>
</html>
