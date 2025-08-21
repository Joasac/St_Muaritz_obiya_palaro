<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

header('Content-Type: application/json'); // <-- important for fetch()

$response = ["success" => false, "message" => "Something went wrong."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name      = htmlspecialchars($_POST['name']);
    $email     = htmlspecialchars($_POST['email']);
    $phone     = htmlspecialchars($_POST['phone']);
    $ministry  = htmlspecialchars($_POST['ministry']);
    $subject   = htmlspecialchars($_POST['subject']);
    $message   = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'isaac.opolot2000@gmail.com';
        $mail->Password   = 'jlch fbvv zpsi xcjr';  // Gmail App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Email settings
        $mail->setFrom('isaac.opolot2000@gmail.com', 'Website Contact Form'); 
        $mail->addReplyTo($email, $name);
        $mail->addAddress('isaac.opolot2000@gmail.com'); 

        $mail->isHTML(true);
        $mail->Subject = "Contact Form: " . $subject;
        $mail->Body    = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Ministry:</strong> {$ministry}</p>
            <p><strong>Message:</strong><br>{$message}</p>
        ";
        $mail->AltBody = "New Contact Form Submission\n\n
            Name: {$name}\n
            Email: {$email}\n
            Phone: {$phone}\n
            Ministry: {$ministry}\n
            Message: {$message}
        ";

        $mail->send();
        $response = ["success" => true, "message" => "✅ Message sent successfully!"];
    } catch (Exception $e) {
        $response = ["success" => false, "message" => "❌ Message failed. {$mail->ErrorInfo}"];
    }
} else {
    $response = ["success" => false, "message" => "❌ Invalid request."];
}

echo json_encode($response);
exit;
?>
