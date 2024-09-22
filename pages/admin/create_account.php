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
    $role = $_POST['role']; // New field for role
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
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, sex, phone, address, role, verification_token, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss', $name, $email, $password, $sex, $phone, $address, $role, $verification_token, $profile_picture);
        
        if ($stmt->execute()) {
            send_verification_email($email, $verification_token); // Send email verification
            // Redirect to the admin dashboard after successful registration
            header('Location: index.php');
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
    <title>Create Account</title>
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
    </style>
</head>
<body>

<div class="container">
    <h1>Create Account</h1>
    <p class="info-text">Create accounts for new actors in the system.</p>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required placeholder="Enter full name">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Enter email address">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Create a strong password">
        </div>

        <div class="form-group">
            <label for="sex">Sex</label>
            <select id="sex" name="sex" required>
                <option value="">--Select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <div>
                <input type="text" id="phone" name="phone" pattern="[0-9]{9}" required placeholder="Enter phone number (without +251)">
                <span>Note: Enter phone number without country code (+251)</span>
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" required placeholder="Enter full address">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">--Select Role--</option>
                <option value="nurse">Nurse</option>
                <option value="inventory_manager">Inventory Manager</option>
                <option value="hospital">Hospital</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="form-group file-input">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            <span>Optional: Upload a profile picture</span>
        </div>

        <button type="submit">Create Account</button>

        <div class="button-group">
            <a href="index.php">Back to Landing Page</a>
            <a href="../login.php">Login</a>
        </div>
    </form>
</div>

</body>
</html>
