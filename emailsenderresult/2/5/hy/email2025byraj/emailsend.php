<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';

function sendRegistrationEmail($to, $full_name, $application_id, $roll_number) {
    $mail = new PHPMailer(true);
    
    try {
        // Gmail SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '@gmail.com';
        $mail->Password   = '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Email content
        $mail->setFrom('2027mikejohnson@gmail.com', 'Online Exam System');
        $mail->addAddress($to, $full_name);
        $mail->isHTML(true);
        $mail->Subject = "Registration Successful - Online Exam System";
        $mail->Body = "
        <html>
        <body>
            <h2>Welcome to Online Exam System, $full_name!</h2>
            <p>Your registration has been completed successfully.</p>
            <p><strong>Application ID:</strong> $application_id</p>
            <p><strong>Roll Number:</strong> $roll_number</p>
            <p>Please login using your Roll Number as username.</p>
            <p>Login URL: https://examsystem.liveblog365.com/student/login.php</p>
            <br>
            <p>Best regards,<br>Online Exam System Team</p>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>