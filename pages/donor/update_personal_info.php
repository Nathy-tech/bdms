<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header('Location: ../../index.php');
    exit();
}

$donor_id = $_SESSION['user_id'];

// Fetch donor's current information
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $stmt = $mysqli->prepare("SELECT name, sex, email, profile_picture, phone, address FROM users WHERE id = ?");
    $stmt->bind_param('i', $donor_id);
    $stmt->execute();
    $stmt->bind_result($name, $sex, $email, $profile_picture, $phone, $address);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $phone_number = '+251' . $_POST['phone']; // Prepend country code
    $address = $_POST['address'];

    // Handle profile picture upload
    $profile_picture = $profile_picture; // Default to current picture if no new upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $profile_picture = $_FILES['profile_picture']['name'];
        $target_dir = "../../uploads/profile_pictures/";
        $target_file = $target_dir . basename($profile_picture);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            echo "<p>Profile picture uploaded successfully.</p>";
        } else {
            echo "<p>Error uploading profile picture.</p>";
        }
    }

    // Update donor information
    $sql = "UPDATE users SET name=?, sex=?, phone=?, address=?";
    if ($profile_picture) {
        $sql .= ", profile_picture=?";
    }
    $sql .= " WHERE id=?";
    
    $stmt = $mysqli->prepare($sql);
    if ($profile_picture) {
        $stmt->bind_param('sssssi', $name, $sex, $phone_number, $address, $profile_picture, $donor_id);
    } else {
        $stmt->bind_param('ssssi', $name, $sex, $phone_number, $address, $donor_id);
    }
    
    if ($stmt->execute()) {
        header('Location: index.php'); // Redirect to donor dashboard
        exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .navigation {
            display: flex;
            justify-content: center;
            background: #007bff;
            color: #fff;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .navigation a {
            color: #fff;
            text-decoration: none;
            margin: 0 20px;
            font-size: 18px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .navigation a:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group select,
        .form-group input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input[type="text"]:focus,
        .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group .phone-input {
            display: flex;
            align-items: center;
        }

        .form-group .phone-input span {
            background-color: #eee;
            padding: 10px;
            border-radius: 4px 0 0 4px;
            border: 1px solid #ddd;
            border-right: 0;
            margin-right: -1px;
        }

        .form-group .phone-input input {
            border-radius: 0 4px 4px 0;
            border-left: 0;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
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

    <div class="navigation">
        <a href="index.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
    
    <div class="container">
        <h1>Update Personal Information</h1>
        <form action="update_personal_info.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="donor_id" value="<?php echo htmlspecialchars($donor_id); ?>">

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="sex">Sex:</label>
                <select id="sex" name="sex" required>
                    <option value="Male" <?php echo $sex === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $sex === 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <div class="phone-input">
                    <span>+251</span>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars(str_replace('+251', '', $phone)); ?>" required pattern="[0-9]{9}" title="Please enter a valid phone number without the country code.">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <div class="form-group">
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</body>
 
</html>
