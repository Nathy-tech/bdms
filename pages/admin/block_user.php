<?php
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['id'];

    // Update user status to 'blocked'
    $stmt = $mysqli->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
    $stmt->bind_param('i', $user_id);

    if ($stmt->execute()) {
        header('Location: manage_account.php'); // Redirect back to manage account page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
