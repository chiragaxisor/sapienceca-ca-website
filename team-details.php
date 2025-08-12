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
        FROM team_members 
        WHERE id = :id
        LIMIT 1
    ");

    // Bind the ID parameter
    $stmt->execute(['id' => $id]);

    // Fetch single record
    $team_member = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Fetch services (limit as needed)
    // $stmt = $pdo->query("SELECT title, description, image, icon_image FROM services ORDER BY created_at DESC LIMIT 8");


    // $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $team_member = [];
}

?>


<section class="service-detail-section">
        <div class="container service-detail-container">
            <div class="service-detail-image">

            <?php if (!empty($team_member['avatar'])): ?>
                    <img src="admin/<?php echo htmlspecialchars($team_member['avatar']); ?>" alt="<?php echo htmlspecialchars($team_member['name']); ?>" />
                <?php else: ?>
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc   3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRkZGRkZGIi8+Cjwvc3ZnPgo=" alt="Team Member Image" />
                <?php endif; ?>   



            <!-- <?php if (!empty($service['image'])): ?>
                <img src="admin/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" />
            <?php else: ?>
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBmaWxsPSIjRkZGRkZGIi8+Cjwvc3ZnPgo=" alt="Service Image" />        
            <?php endif; ?> -->
                <!-- <img src="https://sapienceca.com/wp-content/uploads/2023/02/Untitled-design-98-min.png" alt="Bookkeeping & Accounting" /> -->
            </div>
            <div class="service-detail-content">
                
                <h1> 
                     <?php echo htmlspecialchars($team_member['name']); ?> 
                     </h1>
                <p class="service-detail-desc">     
                    <?php echo htmlspecialchars($team_member['position']); ?>
                </p>

                <?php echo ($team_member['description']); ?>

                <!-- <p class="service-detail-desc">Comprehensive bookkeeping and accounting services to keep your business finances organized, compliant, and up-to-date. Our expert team ensures accurate record-keeping, timely reporting, and full compliance with regulatory standards, so you can focus on growing your business.</p>
                <ul class="service-detail-list">
                    <li>Day-to-day bookkeeping</li>
                    <li>Financial statement preparation</li>
                    <li>Bank reconciliation</li>
                    <li>Accounts payable & receivable management</li>
                    <li>Tax-ready financials</li>
                </ul> -->
                <a href="https://calendly.com/sapient-kpo" style="margin-top:20px" target="_blank" class="btn-primary">Enquire Now</a>
            </div>
        </div>
    </section>
    


    <?php include 'includes/footer.php'; ?>