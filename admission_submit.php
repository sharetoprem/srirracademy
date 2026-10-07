<?php
/**
 * Admission Form Submission Handler
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

// Get and sanitize form data
$fullName = isset($_POST['fullName']) ? htmlspecialchars(trim($_POST['fullName'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$education = isset($_POST['education']) ? htmlspecialchars(trim($_POST['education'])) : '';
$occupation = isset($_POST['occupation']) ? htmlspecialchars(trim($_POST['occupation'])) : '';
$course = isset($_POST['course']) ? htmlspecialchars(trim($_POST['course'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validation
$errors = [];
if (empty($fullName)) $errors[] = 'Full Name is required';
if (empty($phone)) $errors[] = 'Phone Number is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required';
if (empty($education)) $errors[] = 'Education Qualification is required';
if (empty($occupation)) $errors[] = 'Occupation is required';
if (empty($course)) $errors[] = 'Interested Course is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Map values to readable labels
$educationLabels = [
    '10th' => '10th Pass',
    '12th' => '12th Pass',
    'diploma' => 'Diploma',
    'degree' => 'Degree Holder',
    'pg' => 'Post Graduate',
    'pursuing' => 'Currently Pursuing'
];

$occupationLabels = [
    'student' => 'Student',
    'parent' => 'Parent',
    'working' => 'Working Professional',
    'business' => 'Business Owner',
    'unemployed' => 'Currently Unemployed'
];

$courseLabels = [
    'tnpsc4' => 'TNPSC Group 4',
    'tnpsc2' => 'TNPSC Group 2',
    'rrb-ntpc' => 'RRB NTPC',
    'rrb-groupd' => 'RRB Group D',
    'rrb-alp' => 'RRB ALP Technician',
    'banking-po' => 'Bank PO',
    'banking-clerk' => 'Bank Clerk',
    'ssc-cgl' => 'SSC CGL',
    'ssc-chsl' => 'SSC CHSL',
    'police' => 'Police & Defence',
    'tet' => 'TET/TRB',
    'technical' => 'Technical Courses'
];

// Prepare data for email
$emailData = [
    'Full_Name' => $fullName,
    'Phone_Number' => $phone,
    'Email_Address' => $email,
    'Education' => $educationLabels[$education] ?? $education,
    'Occupation' => $occupationLabels[$occupation] ?? $occupation,
    'Interested_Course' => $courseLabels[$course] ?? $course,
    'Message' => $message ?: 'No message provided',
    'Submission_Date' => date('Y-m-d H:i:s'),
    'IP_Address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
];

// Send notification to admin
$adminResult = sendAdminNotification('Admission Form', $emailData);

if (!$adminResult['success']) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Failed to send email: ' . $adminResult['message']]);
    exit;
}

// Send auto-reply to user
sendUserAutoReply($email, $fullName, 'Admission Application');

// Log submission
$logEntry = date('Y-m-d H:i:s') . " | Admission | {$fullName} | {$email} | {$phone}" . PHP_EOL;
@file_put_contents('logs/admissions.log', $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode([
    'ok' => true, 
    'message' => 'Application submitted successfully! We will contact you within 24 hours.'
]);
?>