<!DOCTYPE html>
<html lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Sapienceca </title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="">
    <link rel="icon" type="image/png" sizes="32x32" href="">
    <link rel="icon" type="image/png" sizes="16x16" href="">
    <link rel="shortcut icon" type="image/x-icon" href="">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="assets/css/theme.css" rel="stylesheet" />

  </head>


  <?php

require_once 'admin/config.php';
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    // 2. Fetch services (limit as needed)
    $stmt = $pdo->query("SELECT id, title, description, image, icon_image FROM services ORDER BY created_at DESC LIMIT 8");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}
?>

  <body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" data-navbar-on-scroll="data-navbar-on-scroll">
        <div class="container"><a class="navbar-brand d-flex align-items-center fw-bold fs-2" href="index.html">
            <!-- <div class="text-warning">Urgram</div>
            <div class="text-1000">.io</div> -->
            <img src="img/logo.png" alt="Sapience Logo" class="logo-image" height="50">
          </a>
          
          <button  class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
          <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto pt-2 pt-lg-0">
              <li class="nav-item" data-anchor="data-anchor"><a class="nav-link fw-medium active" aria-current="page" href="#home">Home</a></li>
              <li class="nav-item" data-anchor="data-anchor"><a class="nav-link fw-medium" href="#features">About Us</a></li>
              <!-- <li class="nav-item" data-anchor="data-anchor"><a class="nav-link fw-medium" href="#pricing">Pricing</a></li> -->
              <li class="nav-item" data-anchor="data-anchor"><a class="nav-link fw-medium" href="#testimonial">Services</a></li>
              <li class="nav-item" data-anchor="data-anchor"><a class="nav-link fw-medium" href="#faq">Contact Us</a></li>
            </ul>
            
            <form class="ps-lg-5">
              <a class="btn btn-lg btn-primary rounded-pill order-0" href="#contact" type="submit" style='background:#369E98;'>Book an appointment</a>
            </form>
          </div>
        </div>
      </nav>