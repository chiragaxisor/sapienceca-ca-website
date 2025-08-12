<?php include 'includes/header.php'; ?>

    <main>
        <div class="container" style="max-width: 600px; margin: 120px auto 60px auto;">
            <div class="contact-container" style="background: rgba(30,30,30,0.95); border-radius: 10px; box-shadow: 0 0 40px 0 #222, 0 0 0 1px #333; padding: 40px 32px 32px 32px; color: #fff; border: 1px solid #333;">
                <h2 style="font-size:2.2rem;font-weight:700;margin-bottom:10px;">Contact Us</h2>
                <p style="color:#ccc;margin-bottom:24px;font-size:1.05rem;">Get started on Moveney faster with dedicated implementation and support services or you can contact at <strong style="color:#fff;font-weight:600;text-decoration:underline;">support@moveney.com</strong></p>
                <form class="contact-form">
                    <label>Enter your Number</label>
                    <div class="row" style="display:flex;gap:12px;margin-bottom:18px;">
                        <div class="country-select" style="flex:0 0 120px;display:flex;align-items:center;background:#222;border:1px solid #444;border-radius:7px;padding:0 10px;height:48px;">
                            <img src="https://flagcdn.com/gb.svg" alt="UK Flag" style="width:24px;height:18px;margin-right:8px;border-radius:3px;">
                            <select style="background:transparent;color:#fff;border:none;font-size:1rem;outline:none;">
                                <option value="+44">+44</option>
                                <option value="+91">+91</option>
                                <option value="+1">+1</option>
                            </select>
                        </div>
                        <input type="text" placeholder="Phone Number" style="flex:1;height:48px;background:#222;border:1px solid #444;border-radius:7px;color:#fff;font-size:1rem;padding:0 16px;">
                    </div>
                    <label>Email Address</label>
                    <input type="email" placeholder="Email address" style="width:100%;background:#222;border:1px solid #444;border-radius:7px;color:#fff;font-size:1rem;padding:14px 16px;margin-bottom:18px;">
                    <label>Your Message</label>
                    <textarea placeholder="Write here" style="width:100%;background:#222;border:1px solid #444;border-radius:7px;color:#fff;font-size:1rem;padding:14px 16px;margin-bottom:18px;min-height:120px;resize:vertical;"></textarea>
                    <button type="submit" style="width:100%;background:#000;color:#fff;border:1.5px solid #444;border-radius:30px;font-size:1.15rem;font-weight:600;padding:14px 0;margin-top:10px;cursor:pointer;box-shadow:0 0 0 4px #191919;transition:background 0.2s,color 0.2s;">Send</button>
                </form>
            </div>
        </div>
    </main>

    <section class="cta-action-section">
        <div class="container cta-action-container">
            <h2 class="cta-action-title">Are You Ready To Take Action? We Are Ready To Help.</h2>
            <a href="contact.html" class="cta-action-btn">Get Started</a>
        </div>
    </section>
    
<?php include 'includes/footer.php'; ?>