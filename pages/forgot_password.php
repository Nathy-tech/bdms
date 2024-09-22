<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        /* Custom styles for forgot password page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5rem;
            color: #4CAF50;
            margin: 0;
        }

        main {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-size: 1rem;
            color: #555;
        }

        input[type="email"] {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .btn-secondary {
            display: inline-block;
            margin-top: 15px;
            background-color: #555;
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
        }

        .btn-secondary:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <main>
        <header>
            <h1>Forgot Password</h1>
        </header>
        <h2>Request Password Reset</h2>
        <form action="process_forgot_password.php" method="POST">
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" required placeholder="e.g. john@example.com">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        <a href="login.php" class="btn btn-secondary">Back to Login</a>
    </main>
</body>
</html>
