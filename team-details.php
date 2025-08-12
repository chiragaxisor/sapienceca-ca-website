<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahul Patel | Team Details</title>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.html" class="nav-link">
                <div class="logo">
                    <div class="logo-icon">S</div>
                    <div class="logo-text">apience</div>
                </div></a>
                <nav class="nav">
                    <a href="about.html" class="nav-link">About Us</a>
                    <div class="dropdown">
                        <a href="#" class="nav-link">Services</a>
                        <div class="dropdown-content">
                            <a href="services.html">All Services</a>
                            <a href="service-bookkeeping.html">Bookkeeping</a>
                            <a href="#">Virtual CFO</a>
                            <a href="#">System Setup</a>
                        </div>
                    </div>
                    <a href="contact.html" class="nav-link">Contact Us</a>
                    <a href="#login" class="login-btn">Book an appointment</a>
                </nav>
            </div>
        </div>
    </header>
    <section class="team-detail-section">
        <div class="team-detail-simple">
            <div class="">
                <img src="https://sapienceca.com/wp-content/uploads/2023/03/Untitled-design-2023-03-01T181953.824.png" alt="Rahul Patel">
            </div>
            <div class="team-detail-simple-info">
                <div class="team-detail-simple-name">Rahul Patel</div>
                <div class="team-detail-simple-role">Founder & CEO</div>
                <div class="team-detail-simple-bio">
                    Visionary leader with 15+ years in fintech, passionate about building innovative solutions and empowering teams to achieve excellence. Rahul has led multiple successful projects and is dedicated to driving growth and delivering value to clients and partners.
                </div>
                <a class="team-detail-simple-linkedin" href="https://www.linkedin.com/in/rahulpatel" target="_blank" rel="noopener">View LinkedIn</a>
            </div>
        </div>
    </section>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-logo">
                        <div class="logo-icon">M</div>
                        <div class="logo-text">MOVENEY</div>
                        <p class="tagline">PAYMENTS MADE EASY</p>
                    </div>
                    <p class="footer-description">Moveney is an FCA approved, PCI DSS regulated, payments disruptor founded with a vision to provide a one stop platform that encompasses everything a merchant needs to achieve growth and establish themselves as a trusted brand in the retail and e-commerce market while making the payment ecosystem simpler and accessible to all.</p>
                    <p class="footer-description">We are working tirelessly to bring to our merchants, state of the art payment services and merchant focussed financial services that can significantly improve the world of shopping providing a seamless customer experience. Lets grow together!</p>
                    <p class="copyright">© 2023 Moveney App. All rights reserved</p>
                </div>
                <div class="footer-right">
                    <div class="social-links">
                        <a href="#" class="social-link">📷</a>
                        <a href="#" class="social-link">🐦</a>
                        <a href="#" class="social-link">📘</a>
                    </div>
                    <div class="footer-links">
                        <a href="#" class="footer-link">Terms & conditions</a>
                        <a href="#" class="footer-link">Privacy Policies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

<script>
// Highlight active nav link based on current page
document.querySelectorAll('.nav-link, .dropdown-content a').forEach(link => {
    if (link.href && link.href === window.location.href) {
        link.classList.add('active');
    }
});

// Dropdown open/close on click (not hover)
document.querySelectorAll('.dropdown > .nav-link').forEach(function(menuLink) {
    menuLink.addEventListener('click', function(e) {
        e.preventDefault();
        // Close other dropdowns
        document.querySelectorAll('.dropdown').forEach(function(drop) {
            if (drop !== menuLink.parentElement) {
                drop.classList.remove('open');
            }
        });
        // Toggle current dropdown
        menuLink.parentElement.classList.toggle('open');
    });
});

// Close dropdown if click outside
document.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown').forEach(function(drop) {
        if (!drop.contains(e.target)) {
            drop.classList.remove('open');
        }
    });
});
</script>
