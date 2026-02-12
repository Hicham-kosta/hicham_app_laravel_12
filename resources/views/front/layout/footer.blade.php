<style>
/* ===== FOOTER STYLES (ISOLATED & CLEAN) ===== */

/* Footer main container – all rules prefixed with .bg-secondary */
#footer {
  background-color: #1e2a36 !important;
  color: #e4e7eb !important;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  padding-top: 3rem !important;
  margin-top: 3rem !important;
}

/* Footer logo */
#footer .text-decoration-none h1 {
  font-size: 1.8rem;
  letter-spacing: -0.5px;
  margin-bottom: 1.2rem;
  color: #ffffff;
}
#footer .text-primary {
  color: #4dabf7 !important;
}
#footer .border.border-white {
  border: 1px solid rgba(41, 206, 228, 0.15) !important;
  background: rgba(41, 206, 228, 0.15);
  padding: 4px 8px;
  border-radius: 4px;
}

/* Footer descriptive text */
#footer p {
  color: #a0aab5;
  line-height: 1.6;
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

/* Contact info icons & text */
#footer .fa-map-marker-alt,
#footer .fa-envelope,
#footer .fa-phone-alt {
  color: #4dabf7;
  width: 20px;
  text-align: center;
  margin-right: 12px;
}
#footer .mb-2,
#footer .mb-0 {
  display: flex;
  align-items: center;
  color: #cbd5e0;
  margin-bottom: 0.75rem !important;
}
#footer .mb-2:hover,
#footer .mb-0:hover {
  color: #ffffff;
}

/* Section headings (Quick Links, Collection, Newsletter) */
#footer h5.font-weight-bold {
  color: #ffffff;
  font-size: 1.1rem;
  font-weight: 600 !important;
  margin-bottom: 1.5rem !important;
  padding-bottom: 0.6rem;
  position: relative;
  display: inline-block;
  border-bottom: 2px solid #4dabf7;
}

/* Quick links & Collection links */
#footer .d-flex.flex-column a {
  color: #a0aab5 !important;
  text-decoration: none;
  padding: 0.45rem 0;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}
#footer .d-flex.flex-column a:hover {
  color: #ffffff !important;
  transform: translateX(5px);
}
#footer .d-flex.flex-column .fa-angle-right {
  font-size: 0.8rem;
  margin-right: 8px;
  color: #4dabf7;
}

/* Newsletter form */
#footer .form-control {
  background-color: rgba(41, 206, 228, 0.15) !important;
  border: 1px solid rgba(41, 206, 228, 0.15) !important;
  color: #ffffff !important;
  padding: 0.9rem 1.2rem !important;
  border-radius: 6px !important;
  font-size: 0.95rem;
  height: auto !important;
}
#footer .form-control:focus {
  background-color: rgba(41, 206, 228, 0.15) !important;
  border-color: #4dabf7 !important;
  box-shadow: 0 0 0 2px rgba(41, 206, 228, 0.15) !important;
}
#footer .form-control::placeholder {
  color: #7e8a98 !important;
}

#footer .btn-primary {
  background-color: #4dabf7 !important;
  border: none !important;
  border-radius: 6px !important;
  padding: 0.9rem !important;
  font-weight: 600;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: #ffffff;
  transition: background-color 0.2s;
}
#footer .btn-primary:hover {
  background-color: #3b9be5 !important;
}

/* Newsletter alert */
#subscriber-alert {
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  font-size: 0.9rem;
  display: none;
}
#subscriber-alert.success {
  background: rgba(40, 167, 69, 0.08);
  border: 1px solid #28a745;
  color: #28a745;
}
#subscriber-alert.error {
  background: rgba(220, 53, 69, 0.08);
  border: 1px solid #dc3545;
  color: #dc3545;
}
#subscriber-alert.loading {
  background: rgba(255, 193, 7, 0.08);
  border: 1px solid #ffc107;
  color: #ffc107;
}

/* Copyright section */
#footer .border-top.border-light {
  border-top: 1px solid rgba(41, 206, 228, 0.15) !important;
  margin-top: 1rem;
  padding-top: 2rem !important;
  padding-bottom: 2rem !important;
}
#footer .border-top a {
  color: #a0aab5;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
#footer .border-top a:hover {
  color: #4dabf7;
}

/* Payment icons */
#footer .img-fluid {
  max-height: 32px;
  opacity: 0.7;
  transition: opacity 0.2s;
}
#footer .img-fluid:hover {
  opacity: 1;
}

/* ===== RESPONSIVE ADJUSTMENTS ===== */
@media (max-width: 767px) {
  #footer .row {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
  }
  #footer .col-md-4 {
    margin-bottom: 2rem !important;
  }
  #footer .text-md-right {
    text-align: center !important;
    margin-top: 1rem;
  }
  #footer .border-top .row {
    flex-direction: column;
  }
}
</style>
<body>
<!-- Footer Start -->
    <div class="container-fluid bg-secondary text-dark mt-5 pt-2" id="footer">
        <div class="row px-xl-5 pt-5">
            <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
                <a href="" class="text-decoration-none">
                    <h1 class="mb-4 display-5 font-weight-semi-bold"><span class="text-primary font-weight-bold border border-white px-1 mr-0">S</span>ite&nbsp;<span class="text-primary font-weight-bold border border-white px-1 mr-0">M</span>akers</h1>
                </a>
                <p>Welcome to <strong>SiteMakers</strong>, a leading platform where developers come to build powerful Laravel and full-stack web applications.</p>
                <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>CP, NEW DELHI, INDIA</p>
                <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>info@sitemakers.in</p>
                <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-3"></i>+91-99999-99999</p>
            </div>
            <div class="col-lg-8 col-md-12">
                <div class="row">
                    <div class="col-md-4 mb-5">
                        <h5 class="font-weight-bold mb-4">Quick Links</h5>
                        <div class="d-flex flex-column justify-content-start">
                            <a class="text-dark mb-2" href="{{url('about-us')}}"><i class="fa fa-angle-right mr-2"></i>About Us</a>
                            <a class="text-dark mb-2" href="{{url('contact-us')}}"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                            <a class="text-dark mb-2" href="{{url('faq')}}"><i class="fa fa-angle-right mr-2"></i>FAQ</a>
                            <a class="text-dark mb-2" href="{{url('terms-conditions')}}"><i class="fa fa-angle-right mr-2"></i>Terms & Conditions</a>
                            <a class="text-dark mb-2" href="{{url('privacy-policy')}}"><i class="fa fa-angle-right mr-2"></i>Privacy Policy</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-5">
                        <h5 class="font-weight-bold mb-4">Collection</h5>
                        <div class="d-flex flex-column justify-content-start">
                            <a class="text-dark mb-2" href="listing.html"><i class="fa fa-angle-right mr-2"></i>Clothing</a>
                            <a class="text-dark mb-2" href="listing.html"><i class="fa fa-angle-right mr-2"></i>Electronics</a>
                            <a class="text-dark mb-2" href="listing.html"><i class="fa fa-angle-right mr-2"></i>Appliances</a>
                            <a class="text-dark mb-2" href="listing.html"><i class="fa fa-angle-right mr-2"></i>Accessories</a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-5">
                        <h5 class="font-weight-bold mb-4">Newsletter</h5>
                        <form id="footer-subscribe-form" 
                        action="{{route('front.subscribers.store')}}"
                        method="POST">
                            @csrf
                            <div class="form-group">
                                <input id="subscriber-email" name="email" type="email" 
                                class="form-control border-0 py-4" placeholder="Your Email" required />
                            </div>
                            <div>
                                <button id="subscriber-submit"class="btn btn-primary btn-block border-0 py-3" 
                                type="submit">Subscribe Now</button>
                            </div>
                        </form>
                        <div id="subscriber-alert" style="margin-top: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row border-top border-light mx-xl-5 py-4">
            <div class="col-md-6 px-xl-0">
                <p class="mb-md-0 text-center text-md-left" style="color: #4b95eaff">
                    &copy; <a class="font-weight-semi-bold" href="#">SiteE-Commerce</a>. All Rights Reserved. 
                    <br>Designed by
                    <a class="font-weight-semi-bold" href="https://site-e-commerce" target="_blank">Site E-commerce.com</a><br>
                    Distributed By <a href="https://youtube.com/site-e-commerce" target="_blank">E-commerce</a>
                </p>
            </div>
            <div class="col-md-6 px-xl-0 text-center text-md-right">
                <img class="img-fluid" src="img/payments.png" alt="">
            </div>
        </div>
    </div>
</body>
    <!-- Footer End -->