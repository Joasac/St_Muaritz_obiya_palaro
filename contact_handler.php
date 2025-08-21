<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form inputs
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
        $mail->Username   = 'isaac.opolot2000@gmail.com'; // your Gmail
        $mail->Password   = 'jlch fbvv zpsi xcjr';        // your App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Email settings
        $mail->setFrom($email, $name); 
        $mail->addAddress('isaac.opolot2000@gmail.com'); // where the email will be sent

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
        header("Location: contact.html?popup=✅ Message sent successfully!");
        exit;
    } catch (Exception $e) {
        header("Location: contact.html?popup=❌ Message failed. {$mail->ErrorInfo}");
        exit;
    }
} else {
    header("Location: contact.html?popup=❌ Invalid request.");
    exit;
}


