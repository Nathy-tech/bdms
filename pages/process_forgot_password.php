<?php
include '../includes/db.php'; // Include your database connection
include '../includes/functions.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate the email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check if the email exists in the database
    $query = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Save the token to the database with an expiry time
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
            $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token=?, expires_at=?";
            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param('issss', $user_id, $token, $expiry, $token, $expiry);
                $stmt->execute();
            } else {
                echo "Database error while preparing the statement.";
                exit;
            }

            // PHPMailer configuration
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'blood.nathyy.com'; // Your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'blood@blood.nathyy.com'; // Your SMTP username
                $mail->Password = 'j$IUWy}6$NG$'; // Your SMTP password (use app passwords if available)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // Use 587 for TLS

                // Recipients
                $mail->setFrom('blood@blood.nathyy.com', 'Blood Donation System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "To reset your password, please click the link below:<br><br>";
                $mail->Body .= "<a href='http://blood.nathyy.com/pages/reset_password.php?token=$token'>Reset Password</a>";

                $mail->send();
                echo "A password reset link has been sent to your email address.";
            } catch (Exception $e) {
                echo "Failed to send the email. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No user found with that email address.";
        }
    } else {
        echo "Database error while preparing the statement.";
    }
}
?>
