<?php 
ob_start();
session_start();
include 'includes/header.php'; 
include 'config/email_config.php'; // Include email configuration

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

// Mail sending functionality
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SESSION['form_submitted']) )  {

    // Sanitize input data
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message_text = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validation
    if (empty($fullname) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {

            // Send email to admin
            $admin_email = CONTACT_EMAIL;
            $email_subject = getContactEmailSubject($subject);
            $email_body = getContactEmailBody($fullname, $email, $phone, $subject, $message_text);

            // Email headers
            $headers = "From: " . FROM_NAME . " <noreply@sapienceca.com>\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            $mail->SMTPDebug  = 0; // Debug માટે 2 કરો
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lchirag85@gmail.com';        // તમારું Gmail
            $mail->Password   = 'bhxlgfdmggteuxlf';      // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('nirav@sapienceca.com', 'Your Name');
            $mail->addAddress('nirav@sapienceca.com', 'Recipient'); 

            //Content
            $mail->isHTML(true);

            $mail->Subject = "Contact Us Form: $subject";
            $mail->Body    = "
                <h3>New Contact Us Message</h3>
                <p><strong>Name:</strong> {$fullname}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>".nl2br($message_text)."</p>
            ";
            $mail->AltBody = "New Contact Us Message\n
                Name: {$fullname}\n
                Email: {$email}\n
                Phone: {$phone}\n
                Subject: {$subject}\n
                Message: {$message_text}
            ";
            // Send
            $mail->send();

          $_SESSION['form_submitted'] = true;

          header("Location: ".$_SERVER['PHP_SELF']."?success=1");
          ob_end_flush();
          exit();

        } catch (Exception $e) {
            $error = 'Sorry, there was an error processing your request. Please try again later.';
        }
    }
}
?>

<style>
.bg-custom {
    background-color: #2f9d96 !important;
  }
  
.alert {
    padding: 12px 16px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>



<!-- Contact 4 - Bootstrap Brain Component -->
<section class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
        <h3 class="fs-6 text-secondary mb-2 text-uppercase text-center">Get in Touch</h3>
        <h2 class="display-5 mb-4 mb-md-5 text-center">We're always on the lookout to work with new clients.</h2>
        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-xl-center">
      <div class="col-12 col-lg-6">
        <img class="img-fluid rounded" loading="lazy" src="./assets/img/5114855.jpg" alt="Get in Touch">
      </div>
      <div class="col-12 col-lg-6">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <div class="bg-white border rounded shadow-sm overflow-hidden">

            <?php
              if (isset($_GET['success'])) {
                    unset($_SESSION['form_submitted']);
                    echo '<div class="alert alert-success m-2"> Thank you! Your message has been sent successfully. We will get back to you soon.</div>';
                }
              ?>
              <?php if ($message): ?>
                <div class="alert alert-success">
                  <?php echo $message; ?>
                </div>
              <?php endif; ?>
              
              <?php if ($error): ?>
                <div class="alert alert-danger">
                  <?php echo $error; ?>
                </div>
              <?php endif; ?>
              
              <form method="POST" action="">
                <div class="row gy-4 p-xl-5 m-1">
                  <div class="col-12">
                    <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname ?? ''); ?>" required placeholder="Enter Full Name">
                  </div>
                  <div class="col-12">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                          <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                        </svg>
                      </span>
                      <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required placeholder="Enter Email">
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                          <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg>
                      </span>
                      <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" placeholder="Enter Phone Number">
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required placeholder="Enter Subject">
                  </div>
                  <div class="col-12">
                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="3" required placeholder="Enter Message"><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button id="sendBtn" class="btn bg-custom btn-primary btn-lg" type="submit">Send Message</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


<div class="my-4">
  <div class="row g-3">
    <!-- First Map -->
    <div class="col-12 col-md-6">
      <div class="ratio ratio-16x9">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d69794.15096856387!2d55.22734403607849!3d25.201796243033538!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f433adeaf8135%3A0x164c2eb559d909d4!2sHBL%20Habib%20Bank%20Limited!5e0!3m2!1sen!2sin!4v1759073305904!5m2!1sen!2sin" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>

    <!-- Second Map -->
    <div class="col-12 col-md-6">
      <div class="ratio ratio-16x9">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4495.279240187402!2d72.82915302526088!3d21.18297678050525!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e43dafafdc1%3A0x70fb5b4f91f86eb5!2sShhlok%20Business%20Centre!5e0!3m2!1sen!2sin!4v1759073080047!5m2!1sen!2sin" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>
  </div>
</div>

</section>

<script>
      document.querySelector('form').addEventListener('submit', function() {
      document.getElementById('sendBtn').disabled = true; // Button disable
      document.getElementById('sendBtn').innerText = 'Sending...';
  });
</script>

    
<?php include 'includes/footer.php'; ?>