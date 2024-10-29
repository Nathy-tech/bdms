<?php
require __DIR__ . '/../vendor/autoload.php'; // Adjust path as necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send a general email
function send_email($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'nathyy.com';                      // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'verify@blood.nathyy.com';               // SMTP username
        $mail->Password   = 'verify@nathyy.com';                        // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
        $mail->Port       = 465;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('verify@blood.nathyy.com', 'Blood Donation System');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error details for debugging
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false; // Optionally, log the error message or handle it in another way
    }
}

// Function to send email verification
function send_verification_email($email, $token) {
    $verification_link = "verification/verify.php?token=" . $token;
    $subject = "Email Verification";
    $message = "Please verify your email by clicking the following link: <a href=\"$verification_link\">$verification_link</a>";

    return send_email($email, $subject, $message);
}

// Function to send donor notification
function notify_donor($donor_id, $email, $phone, $message) {
    // Notify via Email
    $subject = "Thank You for Your Donation!";
    send_email($email, $subject, $message);

    // Notify via SMS - Placeholder for SMS logic
    send_sms($phone, $message);

    // Notify via Dashboard - Assuming a table `notifications` exists in your database
    add_dashboard_notification($donor_id, $message);
}

// Placeholder function for sending SMS
function send_sms($phone, $message) {
    // Implement SMS sending logic here using an SMS API
    // This is a placeholder function for SMS notifications
    // Example: integrate with Twilio, Nexmo, etc.
    return true;
}

// Function to add notification to donor's dashboard
function add_dashboard_notification($donor_id, $message) {
    global $mysqli;

    $stmt = $mysqli->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param('is', $donor_id, $message);

    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Database Error: " . $stmt->error);
        return false;
    }

    $stmt->close();
}
?>
