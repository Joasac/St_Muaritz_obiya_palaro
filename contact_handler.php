<?php
/**
 * Contact Form Handler for St. Moritz Catholic Church
 * Processes contact form submissions and sends emails
 */

// Set content type for JSON response
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Configuration
$config = [
    'admin_email' => 'admin@stmoritz.org',
    'from_email' => 'noreply@stmoritz.org',
    'from_name' => 'St. Moritz Website',
    'subject_prefix' => '[St. Moritz Contact] ',
    'enable_email' => true, // Set to false to disable email sending for testing
    'enable_database' => false, // Set to true if you want to save to database
];

// Database configuration (if using database)
$db_config = [
    'host' => 'localhost',
    'username' => 'your_db_username',
    'password' => 'your_db_password',
    'database' => 'your_db_name'
];

// Sanitize and validate input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Basic phone validation - adjust pattern as needed
    return preg_match('/^[\+]?[0-9\s\-\(\)]{7,15}$/', $phone);
}

try {
    // Get and sanitize form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $ministry = sanitizeInput($_POST['ministry'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = 'Please enter a valid phone number';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // Basic spam protection
    if (strlen($message) > 5000) {
        $errors[] = 'Message is too long';
    }
    
    // Check for spam keywords
    $spam_keywords = ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'urgent'];
    $message_lower = strtolower($message . ' ' . $subject);
    foreach ($spam_keywords as $keyword) {
        if (strpos($message_lower, $keyword) !== false) {
            $errors[] = 'Message contains prohibited content';
            break;
        }
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please correct the following errors: ' . implode(', ', $errors)
        ]);
        exit;
    }
    
    // Prepare data for storage/email
    $submission_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'ministry' => $ministry,
        'subject' => $subject,
        'message' => $message,
        'submitted_at' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // Save to database if enabled
    if ($config['enable_database']) {
        try {
            $pdo = new PDO(
                "mysql:host={$db_config['host']};dbname={$db_config['database']}",
                $db_config['username'],
                $db_config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $stmt = $pdo->prepare("
                INSERT INTO contact_submissions 
                (name, email, phone, ministry, subject, message, submitted_at, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $name, $email, $phone, $ministry, $subject, 
                $message, $submission_data['submitted_at'], $submission_data['ip_address']
            ]);
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            // Continue even if database fails
        }
    }
    
    // Send email if enabled
    if ($config['enable_email']) {
        $ministry_text = $ministry ? "Ministry Interest: {$ministry}\n" : '';
        $phone_text = $phone ? "Phone: {$phone}\n" : '';
        
        $email_subject = $config['subject_prefix'] . $subject;
        
        $email_body = "
New contact form submission from St. Moritz website:

Name: {$name}
Email: {$email}
{$phone_text}{$ministry_text}
Subject: {$subject}

Message:
{$message}

---
Submitted: {$submission_data['submitted_at']}
IP Address: {$submission_data['ip_address']}
        ";
        
        $headers = [
            'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
            'Reply-To: ' . $name . ' <' . $email . '>',
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];
        
        $mail_sent = mail(
            $config['admin_email'],
            $email_subject,
            $email_body,
            implode("\r\n", $headers)
        );
        
        if (!$mail_sent) {
            error_log("Failed to send contact form email");
            echo json_encode([
                'success' => false,
                'message' => 'There was an error sending your message. Please try again or contact us directly.'
            ]);
            exit;
        }
        
        // Send auto-reply to user
        $auto_reply_subject = "Thank you for contacting St. Moritz Catholic Church";
        $auto_reply_body = "
Dear {$name},

Thank you for contacting St. Moritz Catholic Church. We have received your message and will respond as soon as possible.

Your message:
Subject: {$subject}
{$message}

If you need immediate assistance, please call us at +256 XXX XXX XXX.

Blessings,
St. Moritz Catholic Church Team

---
This is an automated response. Please do not reply to this email.
        ";
        
        $auto_reply_headers = [
            'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
            'X-Mailer: PHP/' . phpversion(),
            'Content-Type: text/plain; charset=UTF-8'
        ];
        
        mail(
            $email,
            $auto_reply_subject,
            $auto_reply_body,
            implode("\r\n", $auto_reply_headers)
        );
    }
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. We will respond as soon as possible.'
    ]);

} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later or contact us directly.'
    ]);
}

// Optional: Create database table (run this once)
/*
CREATE TABLE contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    ministry VARCHAR(100),
    subject VARCHAR(500) NOT NULL,
    message TEXT NOT NULL,
    submitted_at DATETIME NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/
?>
