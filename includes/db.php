<?php
// Database connection details
$servername = "localhost";
$username = "nathyyco_admin"; // Adjust if you have a different username
$password = "w#!VX(p;VBin"; // Adjust if you have a different password
$dbname = "nathyyco_blood_donation_system";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
