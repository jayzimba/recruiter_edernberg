<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../vendor/autoload.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        try {
            // Server settings
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF;  // Change to DEBUG_SERVER when testing
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'uoe.enrolments@gmail.com';
            $this->mail->Password = 'tunc tzkh hito lyyf';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Try STARTTLS instead
            $this->mail->Port = 587;  // Use port 587 for STARTTLS
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Additional settings for reliability
            $this->mail->Timeout = 30;  // Increase timeout
            $this->mail->CharSet = 'UTF-8';
            $this->mail->isHTML(true);
            $this->mail->setFrom('jayzimba40@gmail.com', 'University Recruitment', false);
        } catch (Exception $e) {
            error_log("Mailer initialization error: " . $e->getMessage() . "\n" . print_r($this->mail->ErrorInfo, true));
            throw $e;
        }
    }

    public function sendApplicationConfirmation($studentEmail, $studentName, $programName)
    {
        try {
            // Clear all recipients first
            $this->mail->clearAddresses();
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();

            $this->mail->addAddress($studentEmail, $studentName);

            // Content
            $this->mail->Subject = 'Application Received - University of Edenberg Recruitment';

            // Email body
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4A90E2;'>Application Received</h2>
                    <p>Dear {$studentName},</p>
                    
                    <p>Thank you for applying to our <strong>{$programName}</strong> program. 
                    We have received your application and it is currently under review.</p>
                    
                    <p>Please note:</p>
                    <ul>
                        <li>Your application is being processed</li>
                        <li>You will receive an admission letter once the review is complete</li>
                        <li>The review process typically takes 24 Hrs.</li>
                    </ul>
                    
                        
                    <p>Important: Please do not reply to this email. If you have any questions, 
                    please contact our admissions office.</p>
                    
                    <div style='margin-top: 20px; padding: 20px; background-color: #f5f5f5;'>
                        <p style='margin: 0;'><strong>Contact Information:</strong></p>
                        <p style='margin: 5px 0;'>Phone: +260972642385</p>
                        <p style='margin: 5px 0;'>Email: admissions.edenberguniversity@ue.ac.zm</p>
                    </div>
                    
                    <p style='margin-top: 20px;'>Best regards,<br>
                    University Recruitment Team</p>
                </div>
            ";

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

            $success = $this->mail->send();
            error_log("Email sent successfully to: " . $studentEmail);
            return $success;
        } catch (Exception $e) {
            error_log("Email sending failed to {$studentEmail}: " . $e->getMessage());
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendAdmissionLetter($studentEmail, $data, $attachmentPath)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            $this->mail->addAddress($studentEmail, $data['student_name']);
            $this->mail->addCC($data['recruiter_email']);

            $this->mail->Subject = 'Admission Letter - University of Edenberg';

            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4A90E2;'>Congratulations!</h2>
                    <p>Dear {$data['student_name']},</p>
                    
                    <p>We are pleased to inform you that your application to the <strong>{$data['program_name']}</strong> 
                    program ({$data['level']}) has been accepted for {$data['study_mode']} study.</p>
                    
                    <p>Please find attached your official admission letter with important information about 
                    your enrollment.</p>
                    
                    <p>Your intake is scheduled for {$data['intake']}.</p>
                    
                    <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p><strong>Your Student ID:</strong> {$data['student_id_number']}</p>
                        <p><strong>for more information contact your recruiting officer:</strong> {$data['recruiter_email']}</p>
                        <p style='color: #dc3545;'><strong>Important:</strong> Please change your password upon first login.</p>
                    </div>
                    
                    <p>If you have any questions, please contact your recruiting officer on: {$data['recruiter_phone_number']}</p>
                    
                    
                    <div style='margin-top: 20px; padding: 20px; background-color: #f5f5f5;'>
                        <p style='margin: 0;'><strong>Contact Information:</strong></p>
                        <p style='margin: 5px 0;'>Email: admissions.edenberguniversity@ue.ac.zm</p>
                    </div>
                    
                    <p style='margin-top: 20px;'>Best regards,<br>
                    University Recruitment Team</p>
                </div>
            ";
            
            $this->mail->Body = $body;
            $this->mail->addAttachment($attachmentPath, $data['student_name'] . '_Admission_Letter.pdf');
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Failed to send admission letter to {$data['student_email']}: " . $e->getMessage());
            throw $e;
        }
    }

    public function sendWelcomeEmail($recruiterEmail, $recruiterName, $leadRecruiterName, $leadRecruiterEmail) {
        try {
            $this->mail->addAddress($recruiterEmail);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Welcome to Edernberg University Recruitment Portal';
            
            // Email body
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #333;'>Welcome to Edernberg University Recruitment Portal</h2>
                <p>Dear {$recruiterName},</p>
                
                <p>Your account has been created in the Edernberg University Recruitment Portal. Below are your login credentials:</p>
                
                <div style='background-color: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Portal Link:</strong> <a href='http://13.53.188.9/uoe'>Link to the portal</a></p>
                    <p><strong>Username:</strong> {$recruiterEmail}</p>
                    <p><strong>Temporary Password:</strong> Password@2025</p>
                </div>
                
                <p><strong>Important:</strong> For security reasons, please change your password after your first login.</p>
                
                <p>Your Lead Recruiter is {$leadRecruiterName}. If you have any questions or need assistance, you can reach them at {$leadRecruiterEmail}.</p>
                
                <p style='margin-top: 30px;'>Best regards,<br>Edernberg University Recruitment Team</p>
                
                <div style='margin-top: 30px; font-size: 12px; color: #666;'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>";
            
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Failed to send welcome email: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendLeadConfirmation($to, $data)
    {
        $subject = "Thank You for Your Interest";
        $message = "
            <h2>Thank you for your interest in {$data['program']}</h2>
            <p>Dear {$data['name']},</p>
            <p>Thank you for your interest in studying {$data['program']} under the school of {$data['school']}. 
               One of our recruiters will contact you shortly to discuss your application.</p>
            <p>Program of Interest: {$data['program']}</p>
            <p>If you have any questions in the meantime, please don't hesitate to contact us.</p>
        ";
        
        return $this->sendEmail($to, $subject, $message);
    }

    public function sendLeadNotification($to, $data)
    {
        $subject = "New Lead Notification";
        $message = "
            <h2>New Lead Alert</h2>
            <p>A new lead has been captured from Facebook:</p>
            <ul>
                <li>Name: {$data['lead_name']}</li>
                <li>Email: {$data['lead_email']}</li>
                <li>Phone: {$data['lead_phone']}</li>
                <li>Program: {$data['program']}</li>
                <li>School: {$data['school']}</li>
            </ul>
            <p>Please follow up with this lead as soon as possible.</p>
        ";
        
        return $this->sendEmail($to, $subject, $message);
    }
}
