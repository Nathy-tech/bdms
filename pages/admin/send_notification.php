<?php
include '../../includes/db.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $distribution_id = $_POST['distribution_id'];
    $donor_id = $_POST['donor_id'];
    $blood_type = $_POST['blood_type'];
    $message = "Your donated blood have been sent to save a humans life tankyou for your donation";

    // Insert notification into notifications table
    $notificationQuery = "INSERT INTO notifications (donor_id, message, sent_at) VALUES (?, ?, NOW())";
    $notificationStmt = $mysqli->prepare($notificationQuery);
    $notificationStmt->bind_param('is', $donor_id, $message);
    $notificationStmt->execute();

    // Update the status of distributed blood to 'notified'
    $updateQuery = "UPDATE distributed_bloods SET status = 'notified' WHERE id = ?";
    $updateStmt = $mysqli->prepare($updateQuery);
    $updateStmt->bind_param('i', $distribution_id);
    $updateStmt->execute();

    // Redirect back to the notification page
    header('Location: notify_distributed_bloods.php');
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
