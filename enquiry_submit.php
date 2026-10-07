<?php
/**
 * Popup Enquiry Form Handler
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

// Get popup form data
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$education = isset($_POST['education']) ? htmlspecialchars(trim($_POST['education'])) : '';
$userType = isset($_POST['userType']) ? htmlspecialchars(trim($_POST['userType'])) : '';
$course = isset($_POST['course']) ? htmlspecialchars(trim($_POST['course'])) : '';

// Validation
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($phone)) $errors[] = 'Phone is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required';
if (empty($education)) $errors[] = 'Education is required';
if (empty($userType)) $errors[] = 'User type is required';
if (empty($course)) $errors[] = 'Course is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Map education values
$educationMap = [
    'degree' => 'Degree Holder',
    'non-degree' => 'Non-Degree',
    'pursuing' => 'Currently Pursuing'
];

$courseMap = [
    'tnpsc' => 'TNPSC Group 4',
    'rrb' => 'RRB NTPC/Group D',
    'banking' => 'IBPS/SSC',
    'police' => 'Police & Defence'
];

$emailData = [
    'Name' => $name,
    'Phone_Number' => $phone,
    'Email_Address' => $email,
    'Education' => $educationMap[$education] ?? $education,
    'User_Type' => ucfirst($userType),
    'Interested_Course' => $courseMap[$course] ?? $course,
    'Submission_Date' => date('Y-m-d H:i:s'),
    'Form_Type' => 'Popup Enquiry',
    'Page_URL' => $_SERVER['HTTP_REFERER'] ?? 'Direct'
];

// Send to admin
$adminResult = sendAdminNotification('Quick Enquiry', $emailData);

if (!$adminResult['success']) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Failed to submit enquiry']);
    exit;
}

// Auto-reply
sendUserAutoReply($email, $name, 'Quick Enquiry');

// Log
$logEntry = date('Y-m-d H:i:s') . " | Popup | {$name} | {$email} | {$phone} | {$course}" . PHP_EOL;
@file_put_contents('logs/enquiries.log', $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode([
    'ok' => true, 
    'message' => 'Enquiry submitted successfully! Our team will contact you soon.'
]);
?>