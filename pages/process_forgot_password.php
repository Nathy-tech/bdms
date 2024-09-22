<?php
include '../includes/db.php'; // Include your database connection
include '../includes/functions.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
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
            $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('iss', $user_id, $token, $expiry);
            $stmt->execute();

            // PHPMailer configuration for Gmail SMTP
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true;
                $mail->Username = 'youremail@gmail.com'; // Your Gmail address
                $mail->Password = 'yourpassword'; // Your Gmail password (or app password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('youremail@gmail.com', 'Blood Donation System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "To reset your password, please click the link below:<br><br>";
                $mail->Body .= "<a href='http://yourdomain.com/pages/reset_password.php?token=$token'>Reset Password</a>";

                $mail->send();
                echo "A password reset link has been sent to your email address.";
            } catch (Exception $e) {
                echo "Failed to send the email. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No user found with that email address.";
        }
    }
}
?>
