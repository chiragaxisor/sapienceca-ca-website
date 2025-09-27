
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

        <section class="py-0 mt-5" id="home" >
        <div class="bg-holder" style="background-image:url(assets/img/illustrations/hero-bg.png);background-position:bottom;background-size:cover;">
        </div>
        <!--/.bg-holder-->

        <div class="container position-relative" style="margin-top: 100px;">
          <div class="row align-items-center py-8">
            <div class="col-md-5 col-lg-6 order-md-1 text-center text-md-end"><img class="img-fluid" src="https://c.ndtvimg.com/2025-02/l3b8jsmo_income-tax_625x300_14_February_25.jpg" width="850" style="border-radius:20px" alt="" /></div>
            <div class="col-md-7 col-lg-6 text-center text-md-start">
              <h1 class="mb-4 display-3 fw-bold lh-sm">Welcome to <br class="d-block d-lg-none d-xl-block" />Sapience</h1>
              <p class="mt-3 mb-4 fs-1">Sapience is committed to supporting dynamic organizations, ensuring that all member firms share a common goal: delivering best-in-class solutions to clients worldwide. As approved auditors by the Ministry of Economy and Free Zones, with expertise in assisting clients with Federal Tax Authority (FTA) compliance, we strive to be a one-stop solution for all financial needs!</p>
              <!-- <a class="btn btn-lg btn-primary rounded-pill hover-top" href="#" role="button">Try for free</a><a class="btn btn-link ps-md-4" href="#" role="button"> Watch demo video</a> -->
            </div>
          </div>
        </div>
      </section>


            <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-6">

        <div class="container">
          <div class="row justify-content-center mb-6">
            <div class="col-lg-6 text-center mx-auto mb-3 mb-md-2 mt-4">
              <h6 class="fw-bold fs-4 display-3 lh-sm">We Value...</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-6">
              <div class="text-center px-lg-3"><img class="img-fluid mb-3" src="assets/img/illustrations/app.png" width="90" alt="" />
                <h5 class="fw-bold">Integrity</h5>
                <p class="mb-md-0">We believe in acting with integrity and getting work done.</p>
              </div>
            </div>
            <div class="col-md-4 mb-6">
              <div class="text-center px-lg-3"><img class="img-fluid mb-3" src="assets/img/illustrations/time-award.png" width="90" alt="" />
                <h5 class="fw-bold">Transparency</h5>
                <p class="mb-md-0">Sapience is committed to building transparency into the business.</p>
              </div>
            </div>
            <div class="col-md-4 mb-6">
              <div class="text-center px-lg-3"><img class="img-fluid mb-3" src="assets/img/illustrations/cloud.png" width="90" alt="" />
                <h5 class="fw-bold">Privacy</h5>
                <p class="mb-md-0">Protecting your organization's data and assets.</p>
              </div>
            </div>
            
          </div>
        </div>
        <!-- end of .container-->

      </section>
      <!-- <section> close ============================-->
      <!-- ============================================-->


            <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-6">

        <div class="container">
          <div class="container">
            <div class="row align-items-center">
              <!-- <div class="col-md-5 order-md-1 text-center text-md-start"><img class="img-fluid mb-4" src="assets/img/illustrations/ultimate-feature.png" alt="" /></div> -->
              <div class="col-md-12 text-center text-md-start">
                
        
                <div class="row justify-content-center mb-6">
                    <div class="col-lg-6 text-center mx-auto mb-3 mb-md-2">
                    <h6 class="fw-bold fs-4 display-3 lh-sm">One-Stop Solutions for<br />All Your Business Needs</h6>
                    </div>
                </div>


                <div class="row">

                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $service): ?>


                  <div class="col-md-4">
                    <div class="mb-4">
                        <?php if (!empty($service['icon_image'])): ?>
                      <div class="py-4"><img class="img-fluid" src="admin/<?php echo htmlspecialchars($service['icon_image']); ?>" width="90" alt="" /></div>
                      <?php else: ?>
                        <div class="py-4">
                                <img class="img-fluid" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRkZGRkZGIi8+Cjwvc3ZnPgo=" alt="Product" width="90" alt="">
                        </div>
                            <?php endif; ?>
                      <h5 class="fw-bold text-undefined"><?php echo htmlspecialchars($service['title']); ?></h5>
                      <!-- <p class="mt-2 mb-0">Get your blood tests delivered at home collect a sample from the news your blood tests.</p> -->
                    </div>
                  </div>

                  <?php endforeach; ?>
                <?php else: ?>
                    <p>No services found.</p>
                <?php endif; ?>

                <!-- </div><a class="btn btn-lg btn-primary rounded-pill hover-top" href="#" role="button">See all</a> -->
              </div>
            </div>
          </div>
        </div>
        <!-- end of .container-->

      </section>
      <!-- <section> close ============================-->
      <!-- ============================================-->


      <section class="py-5" id="features">
        <div class="container-lg">
          <div class="row align-items-center">
            <div class="col-md-5 col-lg-6 order-md-0 text-center text-md-start"><img class="img-fluid" src="img/WhyChooseUs.jpeg" width="550" alt=""  style="width:100%;border-radius:24px;display:block;" /></div>
            <div class="col-md-7 col-lg-6 px-sm-5 px-md-0">
              <h6 class="fw-bold fs-4 display-3 lh-sm">Why Choose Us</h6>
              <p class="my-4"></p>
              <div class="d-flex align-items-center mb-5">
                <div><img class="img-fluid" src="assets/img/illustrations/fast-performance.png" width="90" alt="" /></div>
                <div class="px-4">
                  <h5 class="fw-bold text-danger">Expertise</h5>
                  <p>Our team of professionals has a deep understanding of accounting principles and a track record of delivering high-quality work.</p>
                </div>
              </div>
              <div class="d-flex align-items-center mb-5">
                <div><img class="img-fluid" src="assets/img/illustrations/prototype.png" width="90" alt="" /></div>
                <div class="px-4">
                  <h5 class="fw-bold text-primary">Customized solutions</h5>
                  <p>Get your blood tests delivered at <br class="d-none d-xl-block"> We work closely with our clients to understand their specific needs and goals, and tailor our services to meet those needs.</p>
                </div>
              </div>
              <div class="d-flex align-items-center mb-5">
                <div><img class="img-fluid" src="assets/img/illustrations/vector.png" width="90" alt="" /></div>
                <div class="px-4">
                  <h5 class="fw-bold text-success">Reliability</h5>
                  <p>We are dependable and always available to answer questions and provide support. We take pride in delivering accurate, timely results to our clients.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

           
      
      <section class="py-7">

        <div class="container">
          <div class="row">
            <div class="col-lg-6 text-center mx-auto mb-3 mb-md-5 mt-4">
              <h6 class="fw-bold fs-4 display-3 lh-sm">Softwares we have <br>expertise on</h6>
              <p class="mb-0"></p>
            </div>
          </div>
          <div class="row align-items-center justify-content-center justify-content-lg-around">
        
            <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/1-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/2-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/3-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/4-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/5-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/6-150x150.png" alt="JavaScript" class="expertise-logo"></div>
                    <!-- <div class="col-6 col-sm-4 col-md-4 col-lg-2 px-md-0 mb-5 mb-lg-0 text-center"><img src="img/software/7-150x150.png" alt="JavaScript" class="expertise-logo"></div> -->
          </div>
        </div>
        <!-- end of .container-->
      </section>


      <style>
        
  .marquee {
    overflow: hidden;
    white-space: nowrap;
    box-sizing: border-box;
  }

  .marquee-content {
    display: inline-block;
    padding-left: 100%;
    animation: marquee 20s linear infinite;
  }

  .marquee-content img {
    margin: 0 30px; /* gap between logos */
    height: 80px; /* adjust size */
  }

  @keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }

        </style>


    <?php include 'includes/footer.php'; ?>