
<?php 
include 'includes/header.php'; 
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
    $stmt = $pdo->query("SELECT id, name, avatar, position, linkedin_profile, bio FROM team_members ORDER BY id ASC");
    $team_members = $stmt->fetchAll();
} catch (PDOException $e) {
    $team_members = [];
}
?>


<section class="py-3 py-md-5">
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
      <div class="col-12 col-lg-6 col-xl-5">
        <img class="img-fluid rounded" loading="lazy" src="img/AboutUs.jpeg" alt="About 1">
      </div>
      <div class="col-12 col-lg-6 col-xl-7">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <h2 class="mb-3">About Us</h2>
            <!-- <p class="lead fs-4 text-secondary mb-3">We help people to build incredible brands and superior products. Our perspective is to furnish outstanding captivating services.</p> -->
            <p class="mb-4">Sapience was formed with the vision of becoming a global advisory company. Over time, the company has grown and developed a team of skilled professionals who utilize online technology to help small and medium-sized businesses scale up by managing their bookkeeping and accounting needs. In addition to these core services, Sapience also offers virtual CFO, system setup and migration services to help businesses streamline their financial operations and achieve their goals.</p>
            <p class="mb-4">Sapience was founded in Dubai, United Arab Emirates to provide quality services to its clients at competitive prices with and intention to become long term growth partners. And soon became one of the leading firms in UAE. We are committed to provide exceptional customer service and building long-term relationships with our clients. We strive to be responsive, transparent, and proactive in our communication, so you always know the status of your financial information and can make informed decisions for your business. We take the burden of accounting off your plate so that you can focus on what you do best – running your business.</p>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- <section class="solution_component">
    <div class="container">
        <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">

        <div class="col-12 col-lg-6 col-xl-7" style="color:#fff">
            <div class="row justify-content-xl-center">
            <div class="col-12 col-xl-11">
                <h2 class="mb-3">Mission</h2>
                <p class="mb-4">Our mission is to empower businesses with innovative, reliable, and user-friendly financial solutions that simplify operations, drive growth, and create lasting value for our clients and partners.</p>
            </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-5">
            <img class="img-fluid rounded" loading="lazy" src="img/Mission.jpg" alt="About 1">
        </div>
    </div>
  </div>
</section> -->

<section class="py-3 py-md-5" style="background: #2f9d96;color:#fff">
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
      
      <div class="col-12 col-lg-6 col-xl-7">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <h2 class="mb-3">Mission</h2>
            <p> Our mission is to empower businesses with innovative, reliable, and user-friendly financial solutions that simplify operations, drive growth, and create lasting value for our clients and partners. </p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6 col-xl-5">
                <img class="service-img img-fluid rounded" src="img/Mission.jpg" alt="" />
      </div>
    </div>
  </div>
</section>



<section class="py-3 py-md-5" style="background: #f5f5f5;">
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
      <div class="col-12 col-lg-6 col-xl-5">
                <img class="service-img img-fluid rounded" src="img/Vision.jpg" alt="" />
      </div>
      <div class="col-12 col-lg-6 col-xl-7">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <h2 class="mb-3">Vision</h2>
            <p> Our vision is to be a global leader in financial technology, recognized for our commitment to excellence, integrity, and customer success, while continuously innovating to meet the evolving needs of businesses worldwide </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- <section class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<span class="text-muted">Our Story</span>
				<h2 class="display-5 fw-bold">About Us</h2>
                <img src="img/AboutUs.jpeg" alt="Team Meeting">
			</div>
            
			<div class="col-md-6 offset-md-1">
				<p class="lead">Sapience was formed with the vision of becoming a global advisory company. Over time, the company has grown and developed a team of skilled professionals who utilize online technology to help small and medium-sized businesses scale up by managing their bookkeeping and accounting needs. In addition to these core services, Sapience also offers virtual CFO, system setup and migration services to help businesses streamline their financial operations and achieve their goals.</p>
				<p class="lead">Sapience was founded in Dubai, United Arab Emirates to provide quality services to its clients at competitive prices with and intention to become long term growth partners. And soon became one of the leading firms in UAE. We are committed to provide exceptional customer service and building long-term relationships with our clients. We strive to be responsive, transparent, and proactive in our communication, so you always know the status of your financial information and can make informed decisions for your business. We take the burden of accounting off your plate so that you can focus on what you do best – running your business.</p>
			</div>
		</div>
	</div>
</section> -->


    <!-- <section class="about-section">
        <div class="about-content">
            <div class="about-img-stack">
                <img src="img/AboutUs.jpeg" alt="Team Meeting">
            </div>
            <div class="about-text">
                <h2>About Us</h2>
                <p>Sapience was formed with the vision of becoming a global advisory company. Over time, the company has grown and developed a team of skilled professionals who utilize online technology to help small and medium-sized businesses scale up by managing their bookkeeping and accounting needs. In addition to these core services, Sapience also offers virtual CFO, system setup and migration services to help businesses streamline their financial operations and achieve their goals.</p>
                <p>Sapience was founded in Dubai, United Arab Emirates to provide quality services to its clients at competitive prices with and intention to become long term growth partners. And soon became one of the leading firms in UAE. We are committed to provide exceptional customer service and building long-term relationships with our clients. We strive to be responsive, transparent, and proactive in our communication, so you always know the status of your financial information and can make informed decisions for your business. We take the burden of accounting off your plate so that you can focus on what you do best – running your business.</p>
            </div>
        </div>
    </section> -->

    <!-- Mission Section -->
    <!-- <section class="mv-section mv-mission">
        <div class="container mv-flex mv-reverse">
            <div class="mv-img-wrap">
                <img src="img/Mission.jpg" alt="Mission Image">
            </div>

            <div class="mv-content">
                <h2>Mission</h2>
                <p>Our mission is to empower businesses with innovative, reliable, and user-friendly financial solutions that simplify operations, drive growth, and create lasting value for our clients and partners.</p>
            </div>
            
        </div>
    </section> -->

    <!-- Vision Section -->
    <!-- <section class="mv-section mv-vision">
        <div class="container mv-flex">
            <div class="mv-img-wrap">
                <img src="img/Vision.jpg" alt="Mission Image">
            </div>
            <div class="mv-content">
                <h2>Vision</h2>
                <p>Our vision is to be a global leader in financial technology, recognized for our commitment to excellence, integrity, and customer success, while continuously innovating to meet the evolving needs of businesses worldwide.</p>
            </div>
            
            
        </div>
    </section> -->
    
    
    <!-- Our Core Values Section -->

    <!-- <section class="values-section">
        <div class="container">
            <h2 class="values-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🌟</div>
                    <h3>Excellence</h3>
                    <p>We strive to deliver the highest quality services to our clients and learn in order to stay ahead of the curve.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Collaboration</h3>
                    <p>Working together as a team is key to achieving success, and we encourage open communication within our organization and with our clients.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🛡️</div>
                    <h3>Integrity</h3>
                    <p>We are committed to doing business with honesty, transparency, and fairness, and we always aim to act in the best interests of our clients.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🚀</div>
                    <h3>Innovation</h3>
                    <p>We believe that innovation is key to staying ahead in a constantly-evolving business landscape.</p>
                </div>
            </div>
        </div>
    </section> -->


    <!-- Team 2 - Bootstrap Brain Component -->
<section class="py-3 py-md-5 py-xl-8">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
        <h1 class=" text-center">Meet Our Team</h1>
        
        <!-- <p class="text-center">Get to know the passionate minds behind our success.</p> -->

        <!-- <h2 class="fs-6 text-secondary mb-2 text-uppercase text-center">Meet Our Team</h2> -->
        <!-- <p class="display-5 mb-4 mb-md-5 text-center">.</p> -->
        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
        
      </div>
    </div>
  </div>
  
  <div class="container overflow-hidden">
    <div class="row gy-4 gy-lg-0">

   <?php if (!empty($team_members)): ?>
        <?php foreach ($team_members as $member): ?> 
        
          <div class="col-12 col-lg-4 mt-5">
            <a href="team-details.php?id=<?php echo htmlspecialchars($member['id']); ?>">
            <div class="card border-1">
            <figure class="card-img-top m-0 overflow-hidden bsb-overlay-hover" style="background:#2f9d96" >
                <img class="img-fluid bsb-scale bsb-hover-scale-up" loading="lazy" src="admin/<?php echo htmlspecialchars($member['avatar']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
            </figure>
            <div class="card-body border bg-white p-4">
                <h2 class="card-title h4 fw-bold mb-3"><?php echo htmlspecialchars($member['name']); ?></h2>
                <p class="card-text text-secondary"><?php echo htmlspecialchars($member['bio']); ?></p>
            </div>
            <div class="card-footer border border-top-0 bg-white p-4">
                <ul class="nav mb-0 bsb-nav-sep">
                <li class="nav-item text-secondary">
                    <a class="nav-link link-secondary p-0 pe-3 d-inline-flex align-items-center" href="#!">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-lightbulb text-primary" viewBox="0 0 16 16">
                        <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13a.5.5 0 0 1 0 1 .5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1 0-1 .5.5 0 0 1 0-1 .5.5 0 0 1-.46-.302l-.761-1.77a1.964 1.964 0 0 0-.453-.618A5.984 5.984 0 0 1 2 6zm6-5a5 5 0 0 0-3.479 8.592c.263.254.514.564.676.941L5.83 12h4.342l.632-1.467c.162-.377.413-.687.676-.941A5 5 0 0 0 8 1z" />
                    </svg>
                    <span class="ms-2 fs-6"><?php echo htmlspecialchars($member['position']); ?></span>
                    </a>
                </li>
                </ul>
            </div>
            </div>
            </a>
        </div>
      

      <?php endforeach; ?>
      <?php else: ?>
            <p>No team members found.</p>
     <?php endif; ?>
      
    </div>
  </div>

</section>

<section class="py-3 py-md-5 py-xl-8" style="background: #2f9d96; color: #fff;">
  <div class="container">
    
  </div>
</section>




<style>
.custom-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}
.custom-card:hover {
  transform: translateY(-10px) scale(1.03);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
}
.custom-card img {
  transition: transform 0.3s ease;
}
.custom-card:hover img {
  transform: scale(1.1);
}
</style>



    <?php include 'includes/footer.php'; ?>