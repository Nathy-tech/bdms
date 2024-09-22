<?php
include '../../includes/db.php'; // Include your database connection

// Check if the user is an admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    echo "Access denied!";
    exit();
}

// Fetch all users except donors
$stmt = $mysqli->prepare("SELECT id, name, email, role, status FROM users WHERE role != 'donor'");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accounts</title>
    <link rel="stylesheet" href="../../css/styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #d32f2f;
            margin-bottom: 20px;
        }

        p {
            color: #4caf50;
            text-align: center;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            color: #555;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            text-decoration: none;
            color: #d32f2f;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .nav-links {
            margin-bottom: 20px;
            text-align: center;
        }

        .nav-links a {
            margin: 0 10px;
            color: #d32f2f;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links a:hover {
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
    <div class="nav-links">
        <a href="index.php">Back to Dashboard</a> <!-- Link to the dashboard -->
    </div>

    <h1>Edit Accounts</h1>
    
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <p>Account updated successfully!</p>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                    <td><a href="edit_user_form.php?id=<?php echo $user['id']; ?>">Edit</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
?>
