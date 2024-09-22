<?php
session_start();
include('../includes/db.php'); // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to find user by email
    if ($stmt = $mysqli->prepare("SELECT id, password, role, status FROM users WHERE email = ?")) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $role, $status);
        
        if ($stmt->num_rows == 1) {
            $stmt->fetch();

            // Check if the account is blocked
            if ($status === 'blocked') {
                $error = "Your account is blocked. Please contact the office.";
            } else {
                // Verify password
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = $role;
                    header("Location: ../pages/{$role}/index.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
            }
        } else {
            $error = "Email not found";
        }
        $stmt->close();
    } else {
        $error = "Failed to prepare statement";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Donation System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }

        .login-container h1 {
            text-align: center;
            color: #d32f2f;
            margin-bottom: 30px;
            font-size: 24px;
        }
         /* Google Translate Widget */
         .google-translate {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            padding: 10px;
            background-color: #d32f2f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #b71c1c;
        }

        .login-container p {
            color: red;
            text-align: center;
        }

        .login-container .links {
            text-align: center;
            margin-top: 10px;
        }

        .login-container .links a {
            text-decoration: none;
            color: #d32f2f;
        }

        .login-container .links a:hover {
            text-decoration: underline;
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

<div class="login-container">
    <h1>Login</h1>
    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>

        <!-- Display error message if exists -->
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>

    <div class="links">
        <p>Don't have an account? <a href="../pages/donor/register.php">Register Here</a></p>
        <p><a href="../index.php">Back to Home</a></p>
        <p><a href="forgot_password.php">Forgot Password</a></p>
    </div>
</div>

</body>
</html>
