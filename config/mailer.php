<?php
/**
 * PHPMailer Configuration for Hostinger Email
 * Email: support@srirracademy.com
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Send email using Hostinger SMTP
 */
function sendEmail($to, $subject, $body, $altBody = '', $attachments = []) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'support@srirracademy.com';
        $mail->Password = 'RR@group4';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('support@srirracademy.com', 'Sri RR Academy');
        $mail->addAddress($to);
        $mail->addReplyTo('support@srirracademy.com', 'Sri RR Academy');
        $mail->addBCC('support@srirracademy.com');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
        
        // Attachments
        foreach ($attachments as $attachment) {
            if (file_exists($attachment)) {
                $mail->addAttachment($attachment);
            }
        }
        
        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => $mail->ErrorInfo];
    }
}

/**
 * Send email to admin with enquiry details
 */
function sendAdminNotification($formType, $data, $attachments = []) {
    $subject = "New {$formType} - Sri RR Academy";
    $body = buildEmailTemplate($formType, $data);
    return sendEmail('support@srirracademy.com', $subject, $body, '', $attachments);
}

/**
 * Build HTML email template
 */
function buildEmailTemplate($formType, $data) {
    $date = date('F j, Y g:i A');
    
    $rows = '';
    foreach ($data as $key => $value) {
        $label = ucwords(str_replace('_', ' ', $key));
        $rows .= "
        <tr>
            <td style='padding: 10px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1e40af; width: 40%;'>{$label}</td>
            <td style='padding: 10px; border-bottom: 1px solid #e5e7eb; color: #374151;'>{$value}</td>
        </tr>";
    }

    return "<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #f9fafb; }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: white; }
        .footer { background: #1f2937; color: white; padding: 20px; text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .logo { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .tagline { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='logo'>Sri RR Academy</div>
            <div class='tagline'>Read and Rule</div>
            <h2 style='margin: 15px 0 0 0;'>New {$formType} Submission</h2>
        </div>
        <div class='content'>
            <p style='color: #6b7280; font-size: 14px; margin-bottom: 20px;'>
                <strong>Received on:</strong> {$date}
            </p>
            <table>
                {$rows}
            </table>
        </div>
        <div class='footer'>
            <p>This email was sent from Sri RR Academy website</p>
            <p>© 2026 Sri RR Academy. All rights reserved.</p>
            <p>Contact: +91 97516 99922 | support@srirracademy.com</p>
        </div>
    </div>
</body>
</html>";
}

/**
 * Send auto-reply to user
 */
function sendUserAutoReply($userEmail, $userName, $formType) {
    $subject = "Thank you for contacting Sri RR Academy";
    
    $body = "<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 40px 30px; text-align: center; }
        .content { padding: 30px; background: #f9fafb; }
        .highlight { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1e40af; }
        .cta { background: #1e40af; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
        .footer { background: #1f2937; color: white; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Thank You, {$userName}!</h1>
            <p>We've received your {$formType}</p>
        </div>
        <div class='content'>
            <p>Dear {$userName},</p>
            <p>Thank you for reaching out to <strong>Sri RR Academy</strong>. We have received your {$formType} and our team will contact you within <strong>24 hours</strong>.</p>
            
            <div class='highlight'>
                <h3 style='margin-top: 0; color: #1e40af;'>What happens next?</h3>
                <ul style='padding-left: 20px;'>
                    <li>Our counselors will review your requirements</li>
                    <li>We'll contact you via phone/WhatsApp</li>
                    <li>Free career guidance session</li>
                    <li>Course recommendations tailored for you</li>
                </ul>
            </div>
            
            <p style='text-align: center;'>
                <a href='tel:+919751699922' class='cta'>Call Us: +91 97516 99922</a>
            </p>
            
            <p style='text-align: center; margin-top: 30px;'>
                <a href='https://wa.me/919003244002' style='color: #25d366; text-decoration: none; font-weight: 600;'>Chat on WhatsApp</a>
            </p>
        </div>
        <div class='footer'>
            <p>Sri RR Academy - Read and Rule</p>
            <p>16 & 17, AR Plaza, North Veli Street, Simmakkal, Madurai 625 001</p>
            <p>© 2026 Sri RR Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>";

    return sendEmail($userEmail, $subject, $body);
}
?>