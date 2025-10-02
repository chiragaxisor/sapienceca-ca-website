<?php
// Simple email test script
// This file can be used to test if email functionality is working

include 'config/email_config.php';

// Test email sending
$test_email = 'test@example.com'; // Change this to a real email for testing
$test_subject = 'Email Test from SapienceCA';
$test_message = 'This is a test email to verify that the mail function is working properly.';

$headers = "From: " . FROM_NAME . " <noreply@sapienceca.com>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

echo "<h2>Email Test Results</h2>";

if (mail($test_email, $test_subject, $test_message, $headers)) {
    echo "<p style='color: green;'>✓ Email sent successfully!</p>";
    echo "<p>Test email sent to: $test_email</p>";
    echo "<p>Subject: $test_subject</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to send email.</p>";
    echo "<p>Please check your server's mail configuration.</p>";
}

echo "<h3>Server Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Mail function available: " . (function_exists('mail') ? 'Yes' : 'No') . "</p>";

// Check if mail configuration is loaded
echo "<h3>Email Configuration:</h3>";
echo "<p>Contact Email: " . CONTACT_EMAIL . "</p>";
echo "<p>From Name: " . FROM_NAME . "</p>";

echo "<hr>";
echo "<p><a href='contact.php'>← Back to Contact Form</a></p>";
?>
