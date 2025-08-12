<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moveney - Soft Point of Sale | Payments Made Easy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>

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
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="nav-link">
                <div class="logo">
                    <div class="logo-icon">S</div>
                    <div class="logo-text">apience</div>
                </div></a>
                <nav class="nav">
                    <a href="about.php" class="nav-link">About Us</a>
                    <div class="dropdown">
                        <a href="#" class="nav-link">Services</a>
                        <div class="dropdown-content">
                              <?php if (!empty($services)): ?>
                                <?php foreach ($services as $service):
                                        
                                    ?>
                                    <a href="service-details.php?id=<?php echo ($service['id']); ?>"><?php echo htmlspecialchars($service['title']); ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <!-- <a href="#">All Services</a>
                            <a href="#">Virtual CFO</a>
                            <a href="#">System Setup</a> -->
                        </div>
                    </div>
                    <a href="contact.php" class="nav-link">Contact Us</a>
                    <a href="https://calendly.com/sapient-kpo" target="_blank"  class="login-btn">Book an appointment</a>
                </nav>
            </div>
        </div>
    </header>