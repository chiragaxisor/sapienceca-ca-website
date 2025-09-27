<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sapienceca</title>
    <meta name="description" content="Empowering Business Growth Through Knowledge, Technology, and Outsourcing Solutions. We provide expert services to help your business thrive through innovation and efficiency.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">
</head>

<style>
    /* Base Styles */


.logo img {
    height: 50px;
}
.nav {
    display: flex;
    align-items: center;
    gap: 20px;
}
.nav-link {
    text-decoration: none;
    /* color: #fff; */
    font-weight: 500;
}
.nav-link:hover {
    /* color: #007BFF; */
}

/* Dropdown Styles */
.dropdown {
    position: relative;
}
.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    /* background: #fff; */
    min-width: 180px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    flex-direction: column;
}
.dropdown-content a {
    padding: 10px 15px;
    display: block;
    /* color: #333; */
    text-decoration: none;
}
.dropdown-content a:hover {
    /* background: #f5f5f5; */
}
.dropdown:hover .dropdown-content {
    display: block;
}

/* Mobile Styles */
.menu-toggle {
    display: none;
    font-size: 28px;
    background: none;
    border: none;
    cursor: pointer;
    color: #fff;
}

@media (max-width: 768px) {
    .nav {
        /* background: rgba(9, 9, 9, 0.95); */
        position: absolute;
        top: 60px;
        right: 0;
        background: white;
        flex-direction: column;
        /* width: 200px; */
        display: none;
        border-left: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .nav.active {
        margin-top: 30px;
        display: flex;
        background: rgba(9, 9, 9, 0.95);
    }
    .menu-toggle {
        display: block;
    }
    .dropdown-content {
        position: static;
        box-shadow: none;
    }
    /* .dropdown:hover .dropdown-content {
        display: none;
    }
     .dropdown.open .dropdown-content {
        display: flex;
    } */
}

</style>

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
                    <img src="img/logo.png" alt="Sapience Logo" class="logo-image" height="50">
                </div>
            </a>

            <button class="menu-toggle" onclick="toggleMenu()">☰</button>

            <nav class="nav">
                <a href="about.php" class="nav-link">About Us</a>
                <div class="dropdown">
                    <a href="#" class="nav-link" onclick="toggleDropdown(event)">Services</a>
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
                <a href="contact.php" class="nav-link">Contact Us</a>
                <a href="https://calendly.com/sapient-kpo" target="_blank" class="login-btn">Book an appointment</a>
            </nav>
        </div>
    </div>
</header>


<script>
function toggleMenu() {
    document.querySelector('.nav').classList.toggle('active');
}

function toggleDropdown(e) {
    e.preventDefault(); // prevent link navigation
    e.target.parentElement.classList.toggle('open');
}
</script>
