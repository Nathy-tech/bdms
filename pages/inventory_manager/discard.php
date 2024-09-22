<?php
include '../../includes/db.php';

// Ensure the user is logged in and is an inventory manager
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    http_response_code(403); // Forbidden
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the blood unit ID from the POST request
    $id = intval($_POST['id']);

    // Start a transaction
    $mysqli->begin_transaction();

    try {
        // Fetch the blood unit details
        $query = "SELECT blood_type, donor_id FROM blood_units WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $unit = $result->fetch_assoc();

        if (!$unit) {
            throw new Exception("Blood unit not found.");
        }

        // Insert the blood unit into the discarded_bloods table
        $query = "INSERT INTO discarded_bloods (blood_type, donor_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('si', $unit['blood_type'], $unit['donor_id']);
        $stmt->execute();

        // Delete the blood unit from the blood_units table
        $query = "DELETE FROM blood_units WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Commit the transaction
        $mysqli->commit();

        echo "Success";
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $mysqli->rollback();
        http_response_code(500); // Internal Server Error
        echo "Error: " . $e->getMessage();
    }
}
?>
