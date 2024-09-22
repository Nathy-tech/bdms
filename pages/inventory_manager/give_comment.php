<?php
session_start();
include '../../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Initialize variables
$comment = '';
$message = '';

// Get user details
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch user name from the database
$stmt = $mysqli->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$user_name = $user['name'] ?? 'Unknown';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the comment from the form
    $comment = $_POST['comment'];
    
    // Ensure comment is not empty
    if (!empty($comment)) {
        // Insert the comment into the comments table
        $stmt = $mysqli->prepare("INSERT INTO comments (name, comment, role, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('sss', $user_name, $comment, $role);
        
        if ($stmt->execute()) {
            $message = "Comment submitted successfully!";
        } else {
            $message = "Error submitting comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please enter a comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Comment</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 10px;
            color: #fff;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 16px;
            display: inline-block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .navbar a:hover {
            background-color: #0056b3;
            color: #fff;
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
        textarea {
            width: 100%;
            height: 150px;
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
     
    <div class="navbar">
        <a href="index.php" class="active">Back to Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="container">
        <h1>Give Comment</h1>
        <?php if (!empty($message)): ?>
            <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="comment">Your Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Submit Comment</button>
        </form>
    </div>
</body>
</html>
