<?php
include '../includes/db.php';



if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate token and update the user's status
    $query = "UPDATE donors SET is_verified = 1 WHERE verification_token = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Your email has been verified.";
?>
