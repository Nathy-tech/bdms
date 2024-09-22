<?php
include '../../includes/db.php'; // Include your database connection
include '../../includes/functions.php'; // Include any necessary functions

// Check if the user is an admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); // Redirect if not an admin
    exit();
}

// Handle actions: block, unblock, delete
if (isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);
    
    if ($action === 'block') {
        $stmt = $mysqli->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $message = 'Account has been blocked.';
    } elseif ($action === 'unblock') {
        $stmt = $mysqli->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $message = 'Account has been unblocked.';
    } elseif ($action === 'delete') {
        // Prevent deletion for donors and hospitals
        $stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            if ($user['role'] !== 'donor' && $user['role'] !== 'hospital') {
                $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                $message = 'Account has been deleted.';
            } else {
                $message = 'Cannot delete donor or hospital accounts.';
            }
        } else {
            $message = 'User not found.';
        }
    }
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <link rel="stylesheet" href="../../css/styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Your existing styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b71c1c;
        }

        .status-active {
            color: green;
        }

        .status-blocked {
            color: red;
        }

        .message {
            color: green;
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
    <h1>Manage Accounts</h1>

    <?php if (isset($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr id="user-<?php echo $user['id']; ?>">
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="<?php echo ($user['status'] === 'active') ? 'status-active' : 'status-blocked'; ?>" id="status-<?php echo $user['id']; ?>">
                    <?php echo htmlspecialchars(ucfirst($user['status'])); ?>
                </td>
                <td>
                    <form action="manage_account.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <?php if ($user['status'] === 'active'): ?>
                            <button type="submit" name="action" value="block">Block</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="unblock">Unblock</button>
                        <?php endif; ?>
                    </form>
                    <?php if ($user['role'] !== 'donor' && $user['role'] !== 'hospital'): ?>
                        <form action="manage_account.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this account?');">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <div class="button-group">
        <a href="index.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
