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

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Prepare statement to avoid SQL injection
    $stmt = $pdo->prepare("
        SELECT * 
        FROM services 
        WHERE id = :id
        LIMIT 1
    ");

    // Bind the ID parameter
    $stmt->execute(['id' => $id]);

    // Fetch single record
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Fetch services (limit as needed)
    // $stmt = $pdo->query("SELECT title, description, image, icon_image FROM services ORDER BY created_at DESC LIMIT 8");


    // $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $service = [];
}

?>
<style>
  .bg-custom {
    background-color: #2f9d96 !important;
  }
  .service-img {
    max-width: 100%;     /* container ni width cross na kare */
    height: auto;        /* aspect ratio maintain */
    max-height: 100%;   /* responsive fixed height */
    object-fit: cover;   /* image crop thay to pan sundar lage */
    border-radius: 8px;  /* optional: thodo round corner */
  }
</style>


<div class="page-title bg-custom  text-white py-4">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h3 class="mb-2 mb-lg-0"><?php echo htmlspecialchars($service['title']); ?></h3>
    <nav class="breadcrumbs">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
        <!-- <li class="breadcrumb-item active text-white" aria-current="page">Blog Details</li> -->
      </ol>
    </nav>
  </div>
</div>

<section class="py-3 py-md-5" style="background: #f5f5f5;">
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
      <div class="col-12 col-lg-6 col-xl-5">
        <?php if (!empty($service['image'])): ?>
                <img class="service-img img-fluid" src="admin/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" />
            <?php else: ?>
                <img class="service-img img-fluid" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRkZGRkZGIi8+Cjwvc3ZnPgo=" alt="Service Image" />        
            <?php endif; ?>
      </div>
      <div class="col-12 col-lg-6 col-xl-7">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <h2 class="mb-3"><?php echo htmlspecialchars($service['title'] ?? 'Service Not Found'); ?></h2>
            
             <?php echo $service['description'] ?? 'No description available.'; ?>

            <div class="book_appointment_btn mt-5">
                    <a href="https://calendly.com/sapient-kpo" title="Book an appointment" target="_blank">Enquire Now
                        <img src="./assets/img/arrow-right.svg" alt="">
                    </a>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
