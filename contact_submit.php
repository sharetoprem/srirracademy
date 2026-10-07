<?php
/**
 * Contact Form Submission Handler
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Set timezone to IST (Indian Standard Time)
date_default_timezone_set('Asia/Kolkata');

require_once 'config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Get form data
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$course = isset($_POST['course']) ? htmlspecialchars(trim($_POST['course'])) : '';
$userType = isset($_POST['userType']) ? htmlspecialchars(trim($_POST['userType'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validation
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($phone)) $errors[] = 'Phone is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => implode(', ', $errors)]);
    exit;
}

$emailData = [
    'Name' => $name,
    'Phone_Number' => $phone,
    'Email_Address' => $email,
    'Interested_Course' => $course ?: 'Not specified',
    'User_Type' => $userType ?: 'Not specified',
    'Message' => $message ?: 'No message provided',
    'Submission_Date' => date('Y-m-d H:i:s'),
    'Form_Type' => 'Contact Form'
];

// Send to admin
$adminResult = sendAdminNotification('Contact Enquiry', $emailData);

if (!$adminResult['success']) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Failed to send email']);
    exit;
}

// Auto-reply to user
sendUserAutoReply($email, $name, 'Contact Enquiry');

// Log entry
$logEntry = date('Y-m-d H:i:s') . " | Contact | {$name} | {$email} | {$phone}" . PHP_EOL;
@file_put_contents('logs/contact.log', $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode([
    'ok' => true, 
    'message' => 'Thank you for contacting us! We will get back to you within 24 hours.'
]);
?>