<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch admin-specific data
$query = "SELECT name, email, profile_picture FROM users WHERE id = ? AND role = 'admin'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_picture);
$stmt->fetch();
$stmt->close();

include '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard</title>
    <style>
        /* Basic Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: #fff;
            padding: 15px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar nav {
            display: flex;
            gap: 20px;
        }
        .navbar nav a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .navbar nav a:hover {
            background-color: #0056b3;
        }

        /* Hamburger Menu */
        .menu-toggle {
            display: none; /* Hidden by default */
            font-size: 30px;
            cursor: pointer;
            color: #fff;
        }

        /* Mobile Menu */
        @media screen and (max-width: 768px) {
            .menu-toggle {
                display: block; /* Show the hamburger icon on mobile */
            }
            .navbar nav {
                display: none; /* Hide menu by default */
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #007bff;
                padding: 10px 0;
                flex-direction: column;
            }
            .navbar nav.show {
                display: flex; /* Show menu when 'show' class is added */
            }
            .navbar nav a {
                text-align: center;
                display: block;
                padding: 10px;
                width: 100%;
            }
        }

        /* Rest of Profile and Logout Styles */
        .profile-container {
            margin-top: 80px;
            padding: 20px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.querySelector(".menu-toggle");
            const nav = document.querySelector(".navbar nav");

            menuToggle.addEventListener("click", function () {
                nav.classList.toggle("show"); // Toggle the 'show' class on the nav
            });
        });
    </script>
</head>
<body>

    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <!-- Hamburger menu icon -->
        <div class="menu-toggle">&#9776;</div>
        <nav>
            <a href="create_account.php">Create Account</a>
            <a href="edit_account.php">Edit Account</a>
            <a href="manage_account.php">Manage Account</a>
            <a href="post_information.php">Post Information</a>
            <a href="generate_report.php">Generate Report</a>
            <a href="upload_report.php">Upload Report</a>
            <a href="view_comments.php">View Comments</a>
            <a href="update_info.php">Update Profile</a>
            <a href="notify_distributed_bloods.php">Distributed Bloods</a>
        </nav>
    </div>

    <div class="profile-container">
        <div class="profile">
            <img src="profile_picture.jpg" alt="Profile Picture">
            <div class="profile-info">
                <h2>User Name</h2>
                <p>Email: user@example.com</p>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>

</body>
</html>
