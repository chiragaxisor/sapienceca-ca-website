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

    $destination = "";
    $uploadDir = "uploads/";
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $fileName  = $_FILES['singleFile']['name'];
        $fileTmp   = $_FILES['singleFile']['tmp_name'];
        $fileSize  = $_FILES['singleFile']['size'];
        $fileError = $_FILES['singleFile']['error'];

        if(!empty($fileName)) {

        $allowed = ['pdf', 'doc', 'docx', 'txt'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if(in_array($fileExt, $allowed)) {
            $newFileName = time() . "_" . $fileName; // unique name
            $destination = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmp, $destination)){
                // echo "File uploaded successfully: " . $newFileName;
            } else {
                // echo "Failed to move uploaded file.";
            }
        }
      }

    // Sanitize input data
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $message_text = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validation
    if (empty($fullname) || empty($email) || empty($phone) || empty($message_text)) {
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
            $mail->setFrom('office@sapienceca.com', 'Office');
            $mail->addAddress('office@sapienceca.com', 'Recipient'); 

            if(!empty($fileName)) {
              $mail->addAttachment($destination, $fileName);
            }

            //Content
            $mail->isHTML(true);

            $mail->Subject = "Contact Us Form: $subject";
            $mail->Body    = "
                <h3>New Contact Us Message</h3>
                <p><strong>Name:</strong> {$fullname}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                
                <p><strong>Share your Experience:</strong><br>".nl2br($message_text)."</p>
            ";
            $mail->AltBody = "New Contact Us Message\n
                Name: {$fullname}\n
                Email: {$email}\n
                Phone: {$phone}\n
                Share your Experience: {$message_text}\n
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
<section class=" py-3 py-md-5">
  <div class="container">

   <div class="d-flex justify-content-center align-items-center m-5" >
      <div class="text-center p-4 shadow-lg rounded-4" style="background: #2f9d96  no-repeat center center fixed; background-size: cover; color: #fff;">
        <h1 class="mb-3 fw-bold">Careers at SapienceCA</h1>
        <p class="lead" >
          At SapienceCA, we are a dynamic and fast-growing company committed to delivering timely 
          business solutions and exceptional consulting services to our clients. Our success is 
          driven by the dedication, expertise, and passion of our team.
        </p>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-xl-center">
      
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
              
              <form method="POST" action="" enctype="multipart/form-data">
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
                    <label for="message" class="form-label">Share your Experience <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="3" required placeholder="Enter Message"><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                  </div>

                  <div class="mb-4">
                    <label for="singleFile" class="form-label">File Upload</label>
                    <input class="form-control" type="file" id="singleFile" name="singleFile">
                    <div id="singlePreview" class="mt-2"></div>
                  </div>

                  <div class="col-12">
                    <div class="d-grid">
                      <button id="sendBtn" class="btn bg-custom btn-primary btn-lg" type="submit">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <img class="img-fluid rounded" loading="lazy" src="./assets/img/group-confident-businesspeople-office.jpg" alt="Get in Touch" style="max-height: 90vh">
      </div>

    </div>
  </div>
</section>

<section class="py-3 py-md-5" style="background: #2f9d96;color:#fff">
</section>

<script>
      document.querySelector('form').addEventListener('submit', function() {
      document.getElementById('sendBtn').disabled = true; // Button disable
      document.getElementById('sendBtn').innerText = 'Sending...';
  });
</script>

<?php include 'includes/footer.php'; ?>