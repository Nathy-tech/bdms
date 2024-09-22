<?php
include '../../includes/db.php';
include '../../includes/functions.php'; // Include common functions for sending email, SMS, etc.
session_start();

// Ensure the user is logged in and is an inventory manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'inventory_manager') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $requested_blood_type = $_POST['blood_type'];
    $requested_volume = $_POST['volume'];

    // Validate POST data
    if (empty($request_id) || empty($requested_blood_type) || empty($requested_volume)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
        exit();
    }

    // Define unit volume (in ml)
    $unitVolume = 1; // Adjust this value based on your actual unit volume

    // Check if there are enough available units for the requested blood type
    $countQuery = "SELECT COUNT(*) AS unit_count FROM blood_units WHERE blood_type = ?";
    $countStmt = $mysqli->prepare($countQuery);
    $countStmt->bind_param('s', $requested_blood_type);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $availableUnits = $countRow['unit_count'];

    // Calculate total available volume
    $totalAvailableVolume = $availableUnits * $unitVolume;

    if ($totalAvailableVolume >= $requested_volume) {
        // Calculate number of units to distribute
        $unitsToDistribute = ceil($requested_volume / $unitVolume);

        // Update the hospital request status to fulfilled
        $query = "UPDATE hospital_requests 
                  SET status = 'fulfilled' 
                  WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();

        // Fetch the specific blood units along with donor_id
        $fetchUnitsQuery = "SELECT id, donor_id FROM blood_units 
                            WHERE blood_type = ? 
                            LIMIT ?";
        $fetchUnitsStmt = $mysqli->prepare($fetchUnitsQuery);
        $fetchUnitsStmt->bind_param('si', $requested_blood_type, $unitsToDistribute);
        $fetchUnitsStmt->execute();
        $fetchResult = $fetchUnitsStmt->get_result();

        // Prepare the statement to distribute blood and remove the units
        $distributeQuery = "INSERT INTO distributed_bloods (request_id, blood_type, volume, donor_id) 
                            VALUES (?, ?, ?, ?)";
        $distributeStmt = $mysqli->prepare($distributeQuery);

        $removeQuery = "DELETE FROM blood_units WHERE id = ?";
        $removeStmt = $mysqli->prepare($removeQuery);

        // Distribute blood and remove units from blood_units table
        while ($row = $fetchResult->fetch_assoc()) {
            $unit_id = $row['id'];
            $donor_id = $row['donor_id'];

            // Insert into distributed_bloods table
            $distributeStmt->bind_param('isii', $request_id, $requested_blood_type, $unitVolume, $donor_id);
            $distributeStmt->execute();

            // Remove from blood_units table
            $removeStmt->bind_param('i', $unit_id);
            $removeStmt->execute();
        }

        echo json_encode(['success' => true, 'message' => 'Blood successfully distributed for request ID: ' . $request_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Insufficient blood volume available.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
