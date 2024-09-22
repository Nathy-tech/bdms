<?php
include 'db.php';

function checkEligibility($donor_id) {
    global $conn;
    
    // Fetch the last donation date
    $sql = "SELECT last_donation_date FROM donors WHERE id='$donor_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_donation_date = new DateTime($row['last_donation_date']);
        $current_date = new DateTime();
        $interval = $last_donation_date->diff($current_date);
        
        // Check if 3 months have passed
        if ($interval->m >= 3) {
            return true; // Eligible to donate
        } else {
            return false; // Not eligible yet
        }
    } else {
        return false; // No donation record found
    }
}
?>
