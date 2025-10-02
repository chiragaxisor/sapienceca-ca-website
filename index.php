
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
    $stmt = $pdo->query("SELECT id,title, description, image, icon_image FROM services ORDER BY id ASC LIMIT 8");
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
                    <a href="https://calendly.com/sapienceca/30min" target="_blank"  title="Schedule a Free Consultation">Schedule a Free Consultation
                        <img src="./assets/img/arrow-right.svg" alt="">
                    </a>
                </div>
            </div>

            <div class="hero-media">
            <video
                class="hero-media__video"
                autoplay
                muted
                loop
                playsinline
                >
                <source src="./assets/videos/dubai-business.mp4" type="video/mp4">
                <!-- WebM fallback -->
                <!-- <source src="./assets/video/hero-video.webm" type="video/webm"> -->
                <!-- Your browser does not support the video tag. -->
            </video>
            </div>
            <!-- <img class="hero_img fade-up" src="./assets/img/hero-img.svg" alt=""> -->
        </div>
    </section>

      <style>
    .card-hover {
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card-hover:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .card-title {
      font-weight: 600;
    }
  </style>

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
                        <h4>Excellence</h4>
                        <p>We strive to deliver the highest quality services to our clients and learn in order to stay ahead of the curve.</p>
                    </div>
                </div>
                  <div class="services_box fade-up">
                    <div class="transperancy_icon">
                        <img src="./assets/img/transperancy-icon.svg" alt="Transparency">
                    </div>
                    <div class="service_detail">
                        <h4>Collaboration</h4>
                        <p>Working together as a team is key to achieving success, and we encourage open communication within our organization and with our clients.</p>
                    </div>
                 </div> 
                
                 <div class="services_box fade-up">
                    <div class="privacy_icon">
                        <img src="./assets/img/privacy-icon.svg" alt="Privacy">
                    </div>
                    <div class="service_detail">
                        <h4>Integrity</h4>
                        <p>We are committed to doing business with honesty, transparency, and Fairness and we always aim to act in the best interests of our clients.</p>
                    </div>
                </div> 
                
                 <!-- <div class="services_box fade-up">
                    <div class="privacy_icon">
                        <img src="./assets/img/privacy-icon.svg" alt="Privacy">
                    </div>
                    <div class="service_detail">
                        <h4>Innovation</h4>
                        <p>We believe that innovation is key to staying ahead in a constantly-evolving business landscape.</p>
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Introduction -->
    <section class="introduction_component">
        <div class="introduction_section d-flex">
            <div class="col-md-6 intro_img fade-up">
                <img src="./assets/img/intro-img-1.svg" alt="Company Intro">
            </div>
            <div class="col-md-6 intro_detail d-flex gap-3 flex-column fade-up">
                <h2>Our Commitments</h2>
                <p>At SAPIENCE, our professional advisors take the time to understand your business goals and work diligently to help you achieve them. Our extensive experience spans the full range of professional services, including:
                </p>
                <p><b>Company Formation</b> <br />
                 <img src="./assets/img/arrow-right.svg" alt=""> Assisting you in establishing a new business smoothly and efficiently. </p>
                <p><b>Organizational Development</b> <br /> <img src="./assets/img/arrow-right.svg" alt="">
                 Providing constructive guidance to enhance your operations and drive effective growth.
                </p>
                
                <p><b>Global Expansion</b> <br /> 
                <img src="./assets/img/arrow-right.svg" alt="">Supporting your geographic and global growth strategies for your conglomerate.</p>

                <!-- <p>We are committed to partnering with you at every stage of your journey, ensuring that you have the expertise and resources needed to thrive in today's dynamic marketplace.</p> -->
                <!-- <div class="book_appointment_btn butn_shadow">
                    <a href="javascript:void()" title="Schedule a Free Consultation">Schedule a Free Consultation</a>
                </div> -->
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

    <section class="solution_component py-5">
        <div class="container">
            <div class="solution_section text-center mb-5 mt-5">
                <h2>One-Stop Solutions for <br> All Your Business Needs</h2>
            </div>

            <div class="row g-4 services_section justify-content-center">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>
                        <a href="service-details.php?id=<?php echo $service['id']; ?>" >
                        <div class="col-md-12 col-sm-12">
                            <div class="card card-hover text-center">
                                <img src="admin/<?php echo htmlspecialchars($service['icon_image']); ?>" 
                                    class="card-img-top img-fluid mx-auto d-block mt-4" 
                                    alt="<?php echo htmlspecialchars($service['title']); ?>"  
                                    style="width: 100px; height: 100px;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No services found.</p>
                <?php endif; ?>
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
                        <h4>Client-Focused Approach</h4>
                        <p>We prioritize your needs, ensuring personalized solutions that align with your business goals.</p>
                    </div>
                    <div class="expertise_box fade-up">
                        <h4> Commitment to Excellence</h4>
                        <p>Our team strives for the highest standards in all our services, ensuring consistent quality and reliability.</p>
                    </div>
                    <div class="expertise_box fade-up">
                        <h4>Market and Industry Insights</h4>
                        <p>We leverage our deep understanding of market trends and industry dynamics to provide you with informed advice and strategies.</p>
                    </div>
                    <div class="expertise_box fade-up">
                        
                        <p>This commitment ensures that we not only meet but exceed your expectations at every turn.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About us -->
    <section class="about_component">
        <div class="container d-flex">
            <div class="col-md-6 about_desc d-flex flex-column gap-5">
                <h2>Industries Served</h2>
                <div class="about_content d-flex justify-content-between gap-3 position-relative fade-up">
                    <p>We provide tailored services across a wide range of industries ensuring that our solutions meet the specific requirements of each sector. Our subject matter experts specialise in their respective fields, offering deep insights and targeted support.
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
