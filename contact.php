<?php include 'includes/header.php'; ?>

<style>
.bg-custom {
    background-color: #2f9d96 !important;
  }
  
</style>



<!-- Contact 4 - Bootstrap Brain Component -->
<section class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
        <h3 class="fs-6 text-secondary mb-2 text-uppercase text-center">Get in Touch</h3>
        <h2 class="display-5 mb-4 mb-md-5 text-center">We're always on the lookout to work with new clients.</h2>
        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-xl-center">
      <div class="col-12 col-lg-6">
        <img class="img-fluid rounded" loading="lazy" src="./assets/img/contact-img-1.jpg" alt="Get in Touch">
      </div>
      <div class="col-12 col-lg-6">
        <div class="row justify-content-xl-center">
          <div class="col-12 col-xl-11">
            <div class="bg-white border rounded shadow-sm overflow-hidden">
              <form action="#!">
                <div class="row gy-4 gy-xl-5 p-4 p-xl-5">
                  <div class="col-12">
                    <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="" required>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                          <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                        </svg>
                      </span>
                      <input type="email" class="form-control" id="email" name="email" value="" required>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                          <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg>
                      </span>
                      <input type="tel" class="form-control" id="phone" name="phone" value="">
                    </div>
                  </div>
                  <div class="col-12">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" value="" required>
                  </div>
                  <div class="col-12">
                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn bg-custom btn-primary btn-lg" type="submit">Send Message</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- <div class=" my-4">
  <div class="row">
    <div class="col-6">
      
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d69794.15096856387!2d55.22734403607849!3d25.201796243033538!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f433adeaf8135%3A0x164c2eb559d909d4!2sHBL%20Habib%20Bank%20Limited!5e1!3m2!1sen!2sin!4v1759073305904!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      
    </div>
    <div class="col-6">
      
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4495.279240187402!2d72.82915302526088!3d21.18297678050525!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e43dafafdc1%3A0x70fb5b4f91f86eb5!2sShhlok%20Business%20Centre!5e1!3m2!1sen!2sin!4v1759073080047!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      
    </div>
  </div>
</div> -->


<div class="my-4">
  <div class="row g-3">
    <!-- First Map -->
    <div class="col-12 col-md-6">
      <div class="ratio ratio-16x9">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d69794.15096856387!2d55.22734403607849!3d25.201796243033538!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f433adeaf8135%3A0x164c2eb559d909d4!2sHBL%20Habib%20Bank%20Limited!5e1!3m2!1sen!2sin!4v1759073305904!5m2!1sen!2sin" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>

    <!-- Second Map -->
    <div class="col-12 col-md-6">
      <div class="ratio ratio-16x9">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4495.279240187402!2d72.82915302526088!3d21.18297678050525!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e43dafafdc1%3A0x70fb5b4f91f86eb5!2sShhlok%20Business%20Centre!5e1!3m2!1sen!2sin!4v1759073080047!5m2!1sen!2sin" 
          style="border:0;" 
          allowfullscreen 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>
  </div>
</div>



</section>


    <!-- <main>
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
    </main> -->

    <!-- <section class="cta-action-section">
        <div class="container cta-action-container">
            <h2 class="cta-action-title">Are You Ready To Take Action? We Are Ready To Help.</h2>
            <a href="https://calendly.com/sapient-kpo" target="_blank" class="cta-action-btn">Get Started</a>
        </div>
    </section> -->
    
<?php include 'includes/footer.php'; ?>