<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; // Update with your SMTP host
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'jayzmbaia40@gmail.com'; // Update with your email
        $this->mail->Password = '0777603060joe.'; // Update with your app password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
    }

    public function sendApplicationConfirmation($studentEmail, $studentName, $programName)
    {
        try {
            // Email settings
            $this->mail->setFrom('jayzmbaia40@gmail.com', 'University Recruitment');
            $this->mail->addAddress($studentEmail, $studentName);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Application Received - University Recruitment';

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
                        <p style='margin: 5px 0;'>Phone: +123456789</p>
                        <p style='margin: 5px 0;'>Email: admissions@edernberguniversity.com</p>
                    </div>
                    
                    <p style='margin-top: 20px;'>Best regards,<br>
                    University Recruitment Team</p>
                </div>
            ";

            $this->mail->Body = $body;
            $this->mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
