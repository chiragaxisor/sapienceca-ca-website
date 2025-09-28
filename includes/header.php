<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>

<style>
.header_menu {
    display: flex;
    gap: 20px;
    position: relative;
    font-family: Arial, sans-serif;
}

.header_menu a {
    text-decoration: none;
    /* padding: 10px 15px; */
    color: #333;
    font-weight: 500;
}

.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    min-width: 220px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
    z-index: 999;
    padding: 10px;
}

.dropdown-content a {
    display: block;
    padding: 10px 12px;
    margin: 4px 0;
    color: #333;
    border-radius: 4px;
    transition: all 0.2s ease;
    background: #fafafa;
    border: 1px solid #eee;
}

.dropdown-content a:hover {
    background: #f5f5f5;
    border-color: #ccc;
}

.dropdown:hover .dropdown-content {
    display: block;
}

/* Optional: arrow indicator for Service */
.dropdown > a::after {
    /* content: " ▾";
    font-size: 12px; */
}

</style>


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

    <section class="header_component">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="logo_box" title="Sapience">
                <img src="./assets/img/logo.svg" alt="Logo">
            </a>

            <!-- Desktop Menu -->
            <div class="header_menu d-flex">
                <a href="about.php" title="About Us">About Us</a>
                <!-- <a href="javascript:void()" title="Service">Service</a> -->
                    <div class="dropdown">
                        <a href="javascript:void()" title="Service">Service ▾</a>
                        <div class="dropdown-content">
                          
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <a href="service-details.php?id=<?php echo ($service['id']); ?>">
                                    <?php echo htmlspecialchars($service['title']); ?>
                                </a>
                            <?php endforeach; ?>
                          <?php endif; ?>

                            <a href="javascript:void()">Web Development</a>
                            <a href="javascript:void()">Mobile App</a>
                            <a href="javascript:void()">SEO</a>
                        </div>
                    </div>

                <a href="contact.php" title="Contact Us">Contact Us</a>
            </div>

            <div class="book_appointment_btn d-none d-lg-block">
                <a href="https://calendly.com/sapient-kpo" target="_blank"  title="Book an appointment">Book an appointment</a>
            </div>

            <!-- Hamburger Icon (Mobile) -->
            <div class="hamburger d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Mobile Side Drawer -->
        <div class="mobile_menu">
            <div class="mobile_menu_header">
                <a href="/" class="logo_box" title="Sapience">
                    <img src="./assets/img/logo.svg" alt="Logo">
                </a>
                <div class="close_menu">&times;</div>
            </div>
            <div class="mobile_menu_links">
                <a href="about.php">About Us</a>
                <a href="javascript:void()">Service</a>
                <a href="contact.php">Contact Us</a>
                <a href="https://calendly.com/sapient-kpo" target="_blank" class="book_btn">Book an appointment</a>
            </div>
        </div>
    </section>

    <!-- header drawer -->
    <script>
        const hamburger = document.querySelector(".hamburger");
        const mobileMenu = document.querySelector(".mobile_menu");
        const closeMenu = document.querySelector(".close_menu");
        hamburger.addEventListener("click", () => { mobileMenu.classList.add("active"); });
        closeMenu.addEventListener("click", () => { mobileMenu.classList.remove("active"); });
        document.querySelectorAll(".mobile_menu_links a").forEach(link => {
            link.addEventListener("click", () => { mobileMenu.classList.remove("active"); });
        });
    </script>

</body>

</html>