

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

<style>
  .about p {
    font-weight: 400;
    font-size: 20px;
    line-height: 160%;
    /* text-align: left; */
    color: var(--text);
    /* width: 80%; */
    margin: 0 auto;
  }

  .mv-card {
border: none;
border-radius: 12px;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
padding: 20px;
background: #fff;
transition: transform .2s ease-in-out;
height: 100%;
}
.mv-card:hover {
transform: translateY(-5px);
box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.mv-icon {
font-size: 2rem;
color: #0d6efd;
}
.mv-title {
font-size: 2.25rem;
font-weight: 600;
}
.mv-sub {
font-size: 1.20rem;
color: #6c757d;
}
  </style>


<section class="py-3 py-md-5">
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
      <div class="col-12 col-lg-6 col-xl-5">
        <img class="img-fluid rounded" loading="lazy" src="img/AboutUs.jpeg" alt="About 1">
      </div>
      <div class="col-12 col-lg-6 col-xl-7">
        <div class="row justify-content-xl-center" id="whoweare">
          <div class="col-12 col-xl-11 about">
            <h2 class="mb-3">About Us</h2>
            <!-- <p class="lead fs-4 text-secondary mb-3">We help people to build incredible brands and superior products. Our perspective is to furnish outstanding captivating services.</p> -->
            <p class="mb-4">Founded in India and expanded to Dubai, UAE, SAPIENCE is dedicated to providing
            high-quality services at competitive prices. We aim to be a long-term growth partner for
            our clients. As one of the growing firms in the region, we offer a diverse range of
            specialized professional services, including audit, assurance, accounting, tax, and
            advisory.
            </p>

            <p class="mb-4">Our company is committed to supporting aynamıc organizations, ensuring that all
            nember firms share a common goal: delivering best-in-class solutions to clients
            vorldwide. As approved auditors by the Ministry of Economy and Free Zones,
            with expertise in assisting clients with Federal Tax Authority (FTA) compliance,
            we strive to be a one-stop solution for all financial needs.
            </p>
            
            <p class="mb-4">Our team comprises qualified accountants, auditors, and subject matter
            experts, bringing a wealth of skills and experience to the table. We are
            dedicated to maintaining independence and adhering to the
            highest professional standards while delivering tailored solutions
            to meet our clients' financial objectives.
            </p>
      
            <!-- <h3> <b> <i class='fas fa-arrow-right'></i> Mission  </b> <h3>
             <p class="mb-4">Sapience vision to be the premier provider of
              end-to-end business solutions tor entities around
              the globe. The company aims to help its clientele
              achieve their goals by providing the requisite
              expertise and guidance they need to succeed.
            </p>  -->

            <!-- <h3> <b> Vision  </b> <h3>
             <p class="mb-4">Sapience mission is to help small and medium
              sized businesses succeed by providing
              comprehensive financial services that are tallored
              to their unique need. The company strives to
              exceed client expecations through its
              personalized approach and commitment to
              delivering high-quality results.
            </p>  -->

            
            
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<div class="container py-5">
    <div class="row g-4">
    <!-- Mission Card -->
      <div class="col-md-6">
        <div class="mv-card">
          <div class="d-flex align-items-center mb-3">
            <!-- <i class="bi bi-rocket-fill mv-icon me-3"></i> -->
          <div>
          <h5 class="mv-title">Mission</h5>
          <hr />
          <p class="mv-sub mb-0">Sapience vision to be the premier provider of end-to-end business solutions tor entities around the globe. The company aims to help its clientele achieve their goals by providing the requisite expertise and guidance they need to succeed.</p>
        </div>
      </div>
    </div>
</div>


<!-- Vision Card -->
<div class="col-md-6">
    <div class="mv-card">
        <div class="d-flex align-items-center mb-3">
        <i class="bi bi-eye-fill mv-icon me-3"></i>
          <div>
            <h5 class="mv-title">Vision</h5>
            <hr />
            <p class="mv-sub mb-0">Sapience mission is to help small and medium sized businesses succeed by providing comprehensive financial services that are tallored to their unique need. The company strives to exceed client expecations through its personalized approach and commitment to delivering high-quality results.</p>
            </div>
          </div>
          <!-- <p>Looking ahead, we aim to:</p> -->
            <!-- <ol class="small">
            <li>Enable sustainable growth for clients through thoughtfully engineered products.</li>
            <li>Champion inclusive design and accessible web experiences.</li>
            <li>Build a culture of continuous learning and ethical technology.</li>
            </ol> -->
          </div>
        </div>
    </div>
</div>

<section class="py-3 py-md-5" style="background: #2f9d96;color:#fff">
  <!-- <div class="container">
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
  </div> -->
</section>

<!-- <section class="py-3 py-md-5" style="background: #f5f5f5;">
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
</section> -->

<!-- Team 2 - Bootstrap Brain Component -->
<section class="py-3 py-md-5 py-xl-8">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6" id="MeetOurTeam">
        <h1 class=" text-center">Meet Our Team</h1>
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
                <img class="img-fluid bsb-scale bsb-hover-scale-up" loading="lazy" src="admin/<?php echo htmlspecialchars($member['avatar']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" style="height:420px">
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