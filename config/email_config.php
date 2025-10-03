<?php
// Email configuration for contact form


// MAIL_MAILER=smtp
// MAIL_HOST=smtp.gmail.com
// MAIL_PORT=587
// MAIL_USERNAME=contact@hirespyspaces.com.au
// MAIL_PASSWORD=uezxgwskzqwjbaqn
// MAIL_ENCRYPTION=tls
// MAIL_FROM_ADDRESS=contact@hirespyspaces.com.au
// MAIL_FROM_NAME="${APP_NAME}"


// Email settings
define('CONTACT_EMAIL', 'lchirag85@gmail.com'); // Change this to your actual email
define('FROM_NAME', 'SapienceCA Website');
define('SMTP_HOST', 'localhost'); // Change if using external SMTP
define('SMTP_PORT', 587); // Change if using different port
define('SMTP_USERNAME', 'lchirag85@gmail.com'); // Leave empty if using local mail
define('SMTP_PASSWORD', 'bhxlgfdmggteuxlf'); // Leave empty if using local mail
define('SMTP_ENCRYPTION', 'tls'); // or 'ssl'

// Email templates
function getContactEmailSubject($subject) {
    return 'Contact Form: ' . $subject;
}

function getContactEmailBody($fullname, $email, $phone, $subject, $message_text) {
    return "New contact form submission from your website:

        Name: $fullname
        Email: $email
        Phone: $phone
        Subject: $subject

        Message:
        $message_text

        ---
        This message was sent from the contact form on your website.
        Time: " . date('Y-m-d H:i:s') . "
        IP Address: " . $_SERVER['REMOTE_ADDR'] . "
        ";
}

function getReplyEmailBody($fullname) {
    return "
Dear $fullname,

Thank you for contacting SapienceCA. We have received your message and will get back to you within 24 hours.

Best regards,
SapienceCA Team

---
This is an automated response. Please do not reply to this email.
";
}
?>
