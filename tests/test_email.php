<?php
require_once __DIR__ . '/../utils/Mailer.php';

try {
    $mailer = new Mailer();
    $sent = $mailer->sendApplicationConfirmation(
        'test@example.com',  // Replace with your test email
        'Test User',
        'Test Program'
    );

    echo $sent ? "Email sent successfully!" : "Failed to send email";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
