
<?php 

include 'includes/header.php'; 

// 1. Include DB config and connect
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
    $stmt = $pdo->query("SELECT id,title, description, image, icon_image FROM services ORDER BY created_at DESC LIMIT 8");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}

?>  

<!-- Hero Section -->
    <section class="hero_component">
        <!-- Full-width background image -->
        <img src="./assets/img/hero-bg.svg" alt="Hero Background" class="hero-bg">

        <div class="hero_section d-flex flex-column align-items-center">
            <div class="hero_title d-flex flex-column gap-3 align-items-center">
                <h1>Welcome to Sapience</h1>
                <p>Sapience is committed to supporting dynamic organizations, ensuring that all member firms share a
                    common
                    goal: delivering best-in-class solutions to clients worldwide.</p>

                <div class="book_appointment_btn">
                    <a href="https://calendly.com/sapient-kpo" target="_blank"  title="Book an appointment">Book an appointment
                        <img src="./assets/img/arrow-right.svg" alt="">
                    </a>
                </div>
            </div>

            <img class="hero_img fade-up" src="./assets/img/hero-img.svg" alt="">
        </div>
    </section>

    <!-- services -->
    <section class="services_component">
        <div class="container">
            <div class="services_section">
                <!-- Integrity -->
                <div class="services_box fade-up">
                    <div class="integrity_icon">
                        <img src="./assets/img/integrity-icon.svg" alt="Integrity">
                    </div>
                    <div class="service_detail">
                        <h4>Integrity</h4>
                        <p>We believe in acting with integrity and getting work done.</p>
                    </div>
                </div>
                <!-- Transparency -->
                <div class="services_box fade-up">
                    <div class="transperancy_icon">
                        <img src="./assets/img/transperancy-icon.svg" alt="Transparency">
                    </div>
                    <div class="service_detail">
                        <h4>Transparency</h4>
                        <p>Sapience is committed to building transparency into the business.</p>
                    </div>
                </div>
                <!-- Privacy -->
                <div class="services_box fade-up">
                    <div class="privacy_icon">
                        <img src="./assets/img/privacy-icon.svg" alt="Privacy">
                    </div>
                    <div class="service_detail">
                        <h4>Privacy</h4>
                        <p>Protecting your organization's data and assets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction -->
    <section class="introduction_component">
        <div class="introduction_section d-flex">
            <div class="col-md-6 intro_img fade-up">
                <img src="./assets/img/intro-img-1.svg" alt="Company Intro">
            </div>
            <div class="col-md-6 intro_detail d-flex gap-5 flex-column fade-up">
                <h2>Sapience</h2>
                <p>Sapience is committed to supporting dynamic organizations, ensuring that all member firms share a
                    common goal: delivering best-in-class solutions to clients worldwide. As approved auditors by the
                    Ministry of Economy and Free Zones, with expertise in assisting clients with Federal Tax Authority
                    (FTA) compliance, we strive to be a one-stop solution for all financial needs.
                </p>
                <div class="book_appointment_btn butn_shadow">
                    <a href="javascript:void()" title="Book an appointment">Book an appointment</a>
                </div>
            </div>
        </div>
        <div class="introduction_images d-flex">
            <div class="col-md-6 d-flex intro_img_box fade-up">
                <div class="col-md-10 intro_img_box">
                    <img src="./assets/img/intro-img-2.svg" alt="">
                </div>
                <div class="col-md-2">
                    <img src="./assets/img/intro-img-3.svg" alt="">
                </div>
            </div>
            <div class="col-md-6 d-flex">
                <div class="col-md-10 intro_img_box fade-up">
                    <img src="./assets/img/intro-img-4.svg" alt="">
                </div>
                <div class="col-md-2">
                    <img src="./assets/img/intro-img-5.svg" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- solution -->
    <section class="solution_component">
        <div class="solution_section">
            <h2>One-Stop Solutions for <br> All Your Business Needs</h2>
            <div class="container">
                <div class="services_section">
                    <!-- International Liaison Services -->
                    <div class="services_box fade-up">
                        <div class="integrity_icon">
                            <img src="./assets/img/integrity-icon.svg" alt="Integrity">
                        </div>
                        <div class="solution_detail">
                            <h4>International Liaison Services</h4>
                        </div>
                    </div>
                    <!-- Staffing Solutions -->
                    <div class="services_box fade-up">
                        <div class="transperancy_icon">
                            <img src="./assets/img/transperancy-icon.svg" alt="Transparency">
                        </div>
                        <div class="solution_detail">
                            <h4>Staffing Solutions</h4>
                        </div>
                    </div>
                    <!-- Business Support Services -->
                    <div class="services_box fade-up">
                        <div class="privacy_icon">
                            <img src="./assets/img/privacy-icon.svg" alt="Privacy">
                        </div>
                        <div class="solution_detail">
                            <h4>Business Support Services</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="services_section">
                    <!-- Financial Statement Preparation -->
                    <div class="services_box fade-up">
                        <div class="integrity_icon">
                            <img src="./assets/img/integrity-icon.svg" alt="Integrity">
                        </div>
                        <div class="solution_detail">
                            <h4>Financial Statement Preparation</h4>
                        </div>
                    </div>
                    <!-- Virtual CFO Services -->
                    <div class="services_box fade-up">
                        <div class="transperancy_icon">
                            <img src="./assets/img/transperancy-icon.svg" alt="Transparency">
                        </div>
                        <div class="solution_detail">
                            <h4>Virtual CFO Services</h4>
                        </div>
                    </div>
                    <!-- Controller Services -->
                    <div class="services_box fade-up">
                        <div class="privacy_icon">
                            <img src="./assets/img/privacy-icon.svg" alt="Privacy">
                        </div>
                        <div class="solution_detail">
                            <h4>Controller Services</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why choose us -->
    <section class="why_choose_component">
        <div class="container">
            <h2>Why Choose Us</h2>
            <div class="why_choose_box align-items-center d-flex">
                <div class="col-md-6 why_choose_sec fade-up">
                    <img src="./assets/img/why-choose-us.png" alt="Choose us">
                </div>
                <div class="col-md-6 d-flex flex-column gap-4 why_choose_data">
                    <div class="expertise_box fade-up">
                        <h4>Expertise</h4>
                        <p>Our team of professionals has a deep understanding of accounting principles and a track
                            record of delivering high-quality work.</p>
                    </div>
                    <div class="expertise_box fade-up">
                        <h4>Customized solutions</h4>
                        <p>We work closely with our clients to understand their specific needs and goals, and tailor our
                            services to meet those needs.</p>
                    </div>
                    <div class="expertise_box fade-up">
                        <h4>Reliability</h4>
                        <p>We are dependable and always available to answer questions and provide support. We take pride
                            in delivering accurate, timely results to our clients</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About us -->
    <section class="about_component">
        <div class="container d-flex">
            <div class="col-md-6 about_desc d-flex flex-column gap-5">
                <h2>About Us</h2>
                <div class="about_content d-flex justify-content-between gap-3 position-relative fade-up">
                    <p>Sapience was formed with the vision of becoming a global advisory company. Over time, the company
                        has grown and
                        developed a team of skilled professionals who utilize online technology to help small and
                        medium-sized businesses scale
                        up by managing their bookkeeping and accounting needs. In addition to these core services,
                        Sapience also offers virtual
                        CFO, system setup and migration services to help businesses streamline their financial
                        operations and achieve their
                        goals.
                    </p>
                    <img src="./assets/img/about-1.svg" alt="">
                </div>
                <div class="about_imgs d-flex justify-content-between align-items-end fade-up">
                    <img src="./assets/img/about-2.svg" alt="">
                    <img src="./assets/img/about-3.svg" alt="">
                </div>
            </div>
            <div class="col-md-6 about_main_img fade-up">
                <img src="./assets/img/about-4.svg" alt="">
            </div>
        </div>
    </section>

    <!-- softwares -->
    <section class="software_component">
        <div class="software_title">
            <h2>Softwares we have expertise on</h2>
        </div>
        <div class="software_slider">
            <div class="card">
                <div class="logos-slider">
                    <div class="logos-slider-container">
                        <img src="./assets/img/software-1.svg" />
                        <img src="./assets/img/software-2.svg" />
                        <img src="./assets/img/software-3.svg" />
                        <img src="./assets/img/software-4.svg" />
                        <img src="./assets/img/software-5.svg" />
                        <img src="./assets/img/software-6.svg" />
                        <img src="./assets/img/software-7.svg" />
                    </div>

                    <div class="logos-slider-container">
                        <img src="./assets/img/software-1.svg" />
                        <img src="./assets/img/software-2.svg" />
                        <img src="./assets/img/software-3.svg" />
                        <img src="./assets/img/software-4.svg" />
                        <img src="./assets/img/software-5.svg" />
                        <img src="./assets/img/software-6.svg" />
                        <img src="./assets/img/software-7.svg" />
                    </div>
                </div>
            </div>

        </div>
    </section>

  
    <?php include 'includes/footer.php'; ?>
        <script>
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        });
        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>
