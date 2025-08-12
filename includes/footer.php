<footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-logo">
                        <div class="logo-icon">M</div>
                        <div class="logo-text">MOVENEY</div>
                        <p class="tagline">PAYMENTS MADE EASY</p>
                    </div>
                    <p class="footer-description">Sapience is committed to supporting dynamic organizations, ensuring that all member firms share a common goal: delivering best-in-class solutions to clients worldwide. As approved auditors by the Ministry of Economy and Free Zones, with expertise in assisting clients with Federal Tax Authority (FTA) compliance, we strive to be a one-stop solution for all financial needs.</p>
                    <!-- <p class="footer-description">We are working tirelessly to bring to our merchants, state of the art payment services and merchant focussed financial services that can significantly improve the world of shopping providing a seamless customer experience. Lets grow together!</p> -->
                    <p class="copyright">© 2023 Moveney App. All rights reserved</p>
                </div>
                <div class="footer-right">
                    
                    <div class="footer-contact">
                        <h4 class="contact-country">UNITED ARAB EMIRATES</h4>
                        <p class="contact-name">Nirav Patel</p>
                        <p class="contact-address">
                            303, HBL Building, PO Box 28860, Bank Street, Bur Dubai,<br>
                            Dubai - UAE
                        </p>
                        <p class="contact-phone"> <a href="tel:+971554099388" > 📞 +971 55 409 9388 </a></p>
                        <p class="contact-email">✉️ <a href="mailto:nirav@sapienceca.com">nirav@sapienceca.com</a></p>
                    </div>
                    <div class="footer-contact">
                        <h4 class="contact-country">INDIA</h4>
                        <p class="contact-name">Hatim Rupawala</p>
                        <p class="contact-address">
                            308-B, Shhlok Business Centre,<br>
                            Besides Apple Hospital,<br>
                            Udhna Darwaja, Ring Road,<br>
                            Surat, Gujarat, India<br>
                            Pincode: 395002
                        </p>
                        <p class="contact-phone"> <a href="tel:+919825753852" >  📞 +91 98257 53852 </a></p>
                        <p class="contact-email">✉️ <a href="mailto:hatim@sapienceca.com">hatim@sapienceca.com</a></p>
                    </div>
                    



                    <!-- <div class="social-links">
                        <a href="#" class="social-link">📷</a>
                        <a href="#" class="social-link">🐦</a>
                        <a href="#" class="social-link">📘</a>
                    </div>
                    <div class="footer-links">
                        <a href="#" class="footer-link">Terms & conditions</a>
                        <a href="#" class="footer-link">Privacy Policies</a>
                    </div> -->
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