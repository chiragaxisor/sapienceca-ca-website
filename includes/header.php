<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
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
    $stmt = $pdo->query("SELECT id, title, description, image, icon_image FROM services ORDER BY id ASC LIMIT 8");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}
?>

<style>
    .mobile_dropdown {
        display: flex;
        flex-direction: column;
    }

    .mobile_dropdown_content {
        display: none;
        flex-direction: column;
        margin-left: 15px;
        margin-top: 5px;
    }
    .mobile_dropdown.open .mobile_dropdown_content {
        display: flex;
    }
</style>
<body>

    <section class="header_component">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="logo_box" title="Sapience">
                <img src="./assets/img/logo.svg" alt="Logo" >
            </a>

            <!-- Desktop Menu -->
            <div class="header_menu d-flex">
                <a href="index.php" title="Home">Home</a>
                <div class="dropdown">
                <a href="about.php" title="About Us">About Us ▾</a>
                <div class="dropdown-content">
                    <a href="about.php#whoweare">Who we are</a>
                    <a href="about.php#MeetOurTeam">Meet Choose Us</a>
                    <a href="career.php">Careers</a>
                </div>
                </div>

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
                        </div>
                    </div>

                <a href="career.php" title="Career">Careers</a>
                <a href="contact.php" title="Contact Us">Contact Us</a>
            </div>

            <div class="book_appointment_btn d-none d-lg-block">
                <a href="https://calendly.com/sapienceca/30min" target="_blank"  title="Schedule a Free Consultation">Schedule a Free Consultation</a>
            </div>

            <!-- Hamburger Icon (Mobile) -->
            <div class="hamburger d-lg-none">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Mobile Side Drawer -->
        <!-- <div class="mobile_menu">
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
                <a href="https://calendly.com/sapienceca/30min" target="_blank" class="book_btn">Schedule a Free Consultation</a>
            </div>
        </div> -->

        <div class="mobile_menu">
                <div class="mobile_menu_header">
                    <a href="/" class="logo_box" title="Sapience">
                        <img src="./assets/img/logo.svg" alt="Logo">
                    </a>
                    <div class="close_menu">&times;</div>
                </div>
                <div class="mobile_menu_links">
                    <a href="index.php">Home</a>
                    <a href="about.php">About Us</a>
                    
                    <!-- Service dropdown inside mobile -->
                    <div class="mobile_dropdown">
                        <a href="javascript:void(0)">Services ▾</a>
                        <div class="mobile_dropdown_content">
                            <?php if (!empty($services)): ?>
                                <?php foreach ($services as $service): ?>
                                    <a href="service-details.php?id=<?php echo ($service['id']); ?>">
                                        <?php echo htmlspecialchars($service['title']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="career.php">Careers</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="https://calendly.com/sapienceca/30min" target="_blank" class="book_btn">
                        Schedule a Free Consultation
                    </a>
                </div>
            </div>


    </section>

    <!-- header drawer -->
    <script>
        const hamburger = document.querySelector(".hamburger");
        const mobileMenu = document.querySelector(".mobile_menu");
        const closeMenu = document.querySelector(".close_menu");

        hamburger.addEventListener("click", () => {
            mobileMenu.classList.add("active");
        });
        closeMenu.addEventListener("click", () => {
            mobileMenu.classList.remove("active");
        });

        // Close menu on link click
        document.querySelectorAll(".mobile_menu_links a").forEach(link => {
            link.addEventListener("click", () => {
                mobileMenu.classList.remove("active");
            });
        });

        // Dropdown inside mobile
        document.querySelectorAll(".mobile_dropdown > a").forEach(drop => {
            drop.addEventListener("click", (e) => {
                e.preventDefault();
                drop.parentElement.classList.toggle("open");
            });
        });
    </script>

</body>

</html>