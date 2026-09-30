<?php
// pricing.php - Pricing Page
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pricing — CollabDev</title>
  <meta name="description" content="Simple, transparent plans for developers and teams on CollabDev.">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  
  <!-- Animate On Scroll (AOS) -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <!-- App Global CSS -->
  <link rel="stylesheet" href="/login3/assets/css/app.css">

  <style>
  :root {
    --theme-primary: #8b5cf6; /* Amethyst Violet */
    --theme-secondary: #7c3aed;
    --theme-dark: #08090a; /* Pitch Black */
    --theme-light: #0f1011; /* Graphite */
    --theme-accent: #e4f222; /* Primary Neon Lime */
    --theme-accent-h: #f0fa68;
    --font-heading: 'Poppins', sans-serif;
    --font-body: 'Inter', sans-serif;
    --font-serif: 'Instrument Serif', serif;
    --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  }

  body {
    color: #d0d6e0;
    font-family: var(--font-body);
    font-size: 15px;
    line-height: 1.6;
    background-color: var(--theme-dark);
    position: relative;
    overflow-x: hidden;
  }

  /* Ambient glows */
  body::before {
    content: '';
    position: fixed;
    width: 800px; height: 800px;
    background: radial-gradient(circle, rgba(228, 242, 34, 0.02) 0%, transparent 70%);
    top: -200px; right: -200px;
    pointer-events: none;
    z-index: 0;
  }
  body::after {
    content: '';
    position: fixed;
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.025) 0%, transparent 70%);
    bottom: -150px; left: -150px;
    pointer-events: none;
    z-index: 0;
  }

  h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
    color: #f7f8f8;
    font-weight: 700;
    letter-spacing: -0.02em;
  }

  /* Navigation Bar */
  .navbar-premium {
    background: rgba(8, 9, 10, 0.75) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    transition: var(--transition);
  }
  .navbar-premium .nav-link {
    color: rgba(255, 255, 255, 0.75) !important;
    font-weight: 500;
    transition: var(--transition);
  }
  .navbar-premium .nav-link:hover {
    color: var(--theme-accent) !important;
  }
  .navbar-premium .nav-link.active {
    color: var(--theme-accent) !important;
  }

  .logo-icon {
    background: linear-gradient(135deg, var(--theme-accent), var(--theme-primary)) !important;
    box-shadow: 0 0 15px rgba(228, 242, 34, 0.25) !important;
  }

  /* ================= HERO SECTION ================= */
  .hero-section {
    padding: 160px 0 60px;
    text-align: center;
    position: relative;
    z-index: 10;
  }
  .hero-title {
    font-size: 60px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
    margin-bottom: 25px;
  }
  .hero-title span {
    font-family: var(--font-serif);
    font-weight: 400;
    font-style: italic;
    color: var(--theme-accent);
  }
  .hero-desc {
    color: rgba(255, 255, 255, 0.7);
    font-size: 19px;
    max-width: 650px;
    margin: 0 auto;
    font-weight: 300;
  }

  /* ================= PRICING SECTION ================= */
  .pricing-section {
    padding: 40px 0 80px;
    position: relative;
    z-index: 10;
  }
  
  .pricing-card {
    background: rgba(255, 255, 255, 0.01) !important;
    background-blend-mode: luminosity !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    transition: var(--transition);
    position: relative;
    height: 100%;
    border: 1px solid rgba(255, 255, 255, 0.03);
    padding: 45px 35px;
    display: flex;
    flex-direction: column;
  }

  .pricing-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.2px;
    background: linear-gradient(180deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.03) 40%, rgba(255,255,255,0.15) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }

  .pricing-card.premium-highlight {
    border-color: rgba(228, 242, 34, 0.25) !important;
    box-shadow: 0 15px 35px rgba(228, 242, 34, 0.05), 0 0 25px rgba(139, 92, 246, 0.1);
  }
  .pricing-card.premium-highlight::before {
    background: linear-gradient(180deg, rgba(228,242,34,0.4) 0%, rgba(255,255,255,0.03) 40%, rgba(139,92,246,0.3) 100%);
  }

  .pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(139, 92, 246, 0.15);
    border-color: rgba(228, 242, 34, 0.25);
  }
  .pricing-card.premium-highlight:hover {
    box-shadow: 0 20px 45px rgba(228, 242, 34, 0.12), 0 0 30px rgba(139, 92, 246, 0.15);
  }

  .pricing-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(228, 242, 34, 0.15);
    color: var(--theme-accent);
    border: 1px solid rgba(228, 242, 34, 0.3);
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .plan-name {
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 8px;
  }
  .plan-desc {
    color: #8b949e;
    font-size: 14px;
    margin-bottom: 25px;
    min-height: 42px;
  }
  
  .plan-price-box {
    margin-bottom: 30px;
    display: flex;
    align-items: baseline;
    gap: 4px;
  }
  .plan-price {
    font-family: var(--font-heading);
    font-size: 42px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
  }
  .plan-price-box.custom-price .plan-price {
    font-size: 32px;
  }
  .plan-period {
    color: #8b949e;
    font-size: 14px;
    font-weight: 500;
  }

  .plan-features-list {
    list-style: none;
    padding: 0;
    margin: 0 0 35px;
    flex: 1;
  }
  .plan-features-list li {
    color: #d0d6e0;
    font-size: 14.5px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .plan-features-list li i {
    color: var(--theme-accent);
    font-size: 16px;
  }
  .plan-features-list li.disabled {
    color: #62666d;
    text-decoration: line-through;
  }
  .plan-features-list li.disabled i {
    color: #62666d;
  }

  .btn-pricing {
    width: 100%;
    padding: 12px 25px;
    border-radius: 50px;
    font-family: var(--font-heading);
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    text-align: center;
    text-decoration: none;
    display: block;
    box-sizing: border-box;
  }
  .btn-pricing-secondary {
    background: rgba(255, 255, 255, 0.03);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.08);
  }
  .btn-pricing-secondary:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    color: #ffffff;
  }
  .btn-pricing-primary {
    background: var(--theme-primary);
    color: #ffffff;
    border: none;
    box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
  }
  .btn-pricing-primary:hover {
    background: var(--theme-secondary);
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(139, 92, 246, 0.4);
    transform: translateY(-2px);
  }

  /* ================= FAQ SECTION ================= */
  .faq-section {
    padding: 60px 0 100px;
    position: relative;
    z-index: 10;
  }
  
  .faq-header {
    text-align: center;
    margin-bottom: 50px;
  }
  .faq-header h2 {
    font-size: 36px;
    font-weight: 800;
    color: #ffffff;
  }
  .faq-header h2 span {
    font-family: var(--font-serif);
    font-weight: 400;
    font-style: italic;
    color: var(--theme-accent);
  }

  /* Bootstrap accordion override for liquid glass theme */
  .accordion-item {
    background: rgba(255, 255, 255, 0.005) !important;
    border: 1px solid rgba(255, 255, 255, 0.03) !important;
    border-radius: 16px !important;
    overflow: hidden;
    margin-bottom: 15px;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    transition: var(--transition);
  }
  .accordion-item::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.2px;
    background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.01) 40%, rgba(255,255,255,0.08) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }
  .accordion-item:hover {
    border-color: rgba(255, 255, 255, 0.08) !important;
    background: rgba(255, 255, 255, 0.01) !important;
  }
  .accordion-header {
    margin: 0;
  }
  .accordion-button {
    background: transparent !important;
    color: #f7f8f8 !important;
    font-family: var(--font-heading);
    font-weight: 600;
    font-size: 16px;
    padding: 20px 25px;
    border: none !important;
    box-shadow: none !important;
    text-align: left;
  }
  .accordion-button:not(.collapsed) {
    color: var(--theme-accent) !important;
  }
  .accordion-button::after {
    filter: invert(1);
    background-size: 1rem;
    transition: transform 0.3s;
  }
  .accordion-body {
    padding: 0 25px 25px;
    color: #8b949e;
    font-size: 14.5px;
    line-height: 1.6;
  }

  /* Detailed Premium Footer */
  .footer-premium {
    background: #050607;
    border-top: 1px solid rgba(255, 255, 255, 0.03);
    padding: 80px 0 40px;
    position: relative;
    z-index: 10;
  }
  .footer-premium::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(228,242,34,0.15) 50%, transparent 100%);
  }
  .footer-brand-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 20px;
  }
  .footer-brand-desc {
    color: #8b949e;
    font-size: 13.5px;
    line-height: 1.6;
    margin-bottom: 25px;
    max-width: 280px;
  }
  .footer-social-links {
    display: flex;
    gap: 12px;
  }
  .footer-social-btn {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    color: #8b949e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: var(--transition);
    text-decoration: none;
  }
  .footer-social-btn:hover {
    background: rgba(228, 242, 34, 0.1);
    border-color: var(--theme-accent);
    color: var(--theme-accent);
    transform: translateY(-3px);
  }
  .footer-col-title {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #ffffff;
    margin-bottom: 20px;
  }
  .footer-links-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .footer-links-list li {
    margin-bottom: 12px;
  }
  .footer-links-list a {
    color: #8b949e;
    font-size: 13.5px;
    text-decoration: none;
    transition: var(--transition);
  }
  .footer-links-list a:hover {
    color: var(--theme-accent);
    padding-left: 4px;
  }
  .footer-bottom {
    margin-top: 60px;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
  }
  .footer-copy {
    color: #62666d;
    font-size: 13px;
    margin: 0;
  }
  .footer-tech-stack {
    color: #62666d;
    font-size: 12px;
    margin: 0;
  }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section" data-aos="fade-down">
  <div class="container">
    <h1 class="hero-title">Simple, <span>Transparent Plans</span></h1>
    <p class="hero-desc">Choose the right plan to boost your workspace development speed and scale team collaboration.</p>
  </div>
</section>

<!-- PRICING SECTION -->
<section class="pricing-section">
  <div class="container">
    <div class="row g-4 justify-content-center align-items-stretch">
      
      <!-- Tier 1: Hobby -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="pricing-card">
          <h3 class="plan-name">Hobby</h3>
          <p class="plan-desc">For solo creators, builders, and developers exploring real-time coding.</p>
          <div class="plan-price-box">
            <span class="plan-price">$0</span>
            <span class="plan-period">/ forever</span>
          </div>
          
          <ul class="plan-features-list">
            <li><i class="bi bi-check2"></i> 3 Collaborative Projects</li>
            <li><i class="bi bi-check2"></i> Up to 3 workspace members</li>
            <li><i class="bi bi-check2"></i> Basic code sync & presence</li>
            <li><i class="bi bi-check2"></i> Live workspace team chat</li>
            <li class="disabled"><i class="bi bi-x-lg"></i> Git commit snapshots</li>
            <li class="disabled"><i class="bi bi-x-lg"></i> Code review approvals</li>
          </ul>
          
          <a href="register.php" class="btn-pricing btn-pricing-secondary">Get Started Free</a>
        </div>
      </div>

      <!-- Tier 2: Professional -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-card premium-highlight">
          <span class="pricing-badge">Popular</span>
          <h3 class="plan-name">Professional</h3>
          <p class="plan-desc">For teams, freelancers, and active developers building projects together.</p>
          <div class="plan-price-box">
            <span class="plan-price">$12</span>
            <span class="plan-period">/ user / mo</span>
          </div>
          
          <ul class="plan-features-list">
            <li><i class="bi bi-check2"></i> <strong>Unlimited</strong> Projects</li>
            <li><i class="bi bi-check2"></i> Unlimited collaborators</li>
            <li><i class="bi bi-check2"></i> Real-time sync & presence</li>
            <li><i class="bi bi-check2"></i> Live workspace team chat</li>
            <li><i class="bi bi-check2"></i> Git commits & branch simulation</li>
            <li><i class="bi bi-check2"></i> Inline reviews & line comments</li>
            <li><i class="bi bi-check2"></i> Priority system notifications</li>
          </ul>
          
          <a href="register.php" class="btn-pricing btn-pricing-primary">Start 14-Day Trial</a>
        </div>
      </div>

      <!-- Tier 3: Enterprise -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="pricing-card">
          <h3 class="plan-name">Enterprise</h3>
          <p class="plan-desc">For organizations requiring advanced control, security, and dedicated instances.</p>
          <div class="plan-price-box custom-price">
            <span class="plan-price">Custom</span>
            <span class="plan-period">/ organization</span>
          </div>
          
          <ul class="plan-features-list">
            <li><i class="bi bi-check2"></i> Everything in Pro plan</li>
            <li><i class="bi bi-check2"></i> Dedicated server & DB node</li>
            <li><i class="bi bi-check2"></i> Custom SSO & SAML authentication</li>
            <li><i class="bi bi-check2"></i> Granular role-based permissions</li>
            <li><i class="bi bi-check2"></i> Custom repository size limits</li>
            <li><i class="bi bi-check2"></i> Dedicated support manager</li>
            <li><i class="bi bi-check2"></i> 99.9% uptime SLA</li>
          </ul>
          
          <a href="mailto:support@collabdev.example.com" class="btn-pricing btn-pricing-secondary">Contact Sales</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- FAQ SECTION -->
<section class="faq-section">
  <div class="container">
    <div class="faq-header" data-aos="fade-up">
      <h2>Frequently <span>Asked Questions</span></h2>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="accordion" id="faqAccordion">
          
          <!-- FAQ 1 -->
          <div class="accordion-item">
            <h3 class="accordion-header" id="headingOne">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Can I invite team members who do not have a paid subscription?
              </button>
            </h3>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Yes! Under the Professional plan, you pay for your own subscription and can invite free-tier members as collaborators to your specific projects. They will have access to all Professional features within your shared workspace.
              </div>
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="accordion-item">
            <h3 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                How does the real-time code synchronization work?
              </button>
            </h3>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                CollabDev utilizes AJAX polling that keeps track of updates in a dedicated database sync queue. When you or your teammate types inside the editor, changes are merged character-by-character, and remote cursor markings update within 500ms.
              </div>
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="accordion-item">
            <h3 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Can I cancel or downgrade my plan at any time?
              </button>
            </h3>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Absolutely. You can downgrade to our Hobby plan or cancel your subscription at any point from your user profile settings page. If you cancel, your Professional benefits will continue to be active until the end of your billing cycle.
              </div>
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="accordion-item">
            <h3 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Do you support integrations with external Git platforms?
              </button>
            </h3>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                CollabDev currently operates on an internal simulated Git engine powered by database snapshots. This helps teams code together immediately without the overhead of connecting keys or configuring hooks. Integrations with GitHub and GitLab are on our roadmap for later this year.
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<footer class="footer-premium">
  <div class="container">
    <div class="row g-4 text-start">
      
      <!-- Brand Column -->
      <div class="col-lg-4 col-md-6">
        <div class="footer-brand-title d-flex align-items-center gap-2">
          <div class="logo-icon w-7 h-7 rounded d-flex align-items-center justify-content-center text-sm" style="font-size: 12px; font-weight: 700; width: 28px; height: 28px;">⚡</div>
          <span>CollabDev</span>
        </div>
        <p class="footer-brand-desc">
          A high-performance, real-time collaboration suite designed to eliminate setup overhead and bring developers together.
        </p>
        <div class="footer-social-links">
          <a href="#" class="footer-social-btn"><i class="bi bi-github"></i></a>
          <a href="#" class="footer-social-btn"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="footer-social-btn"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="footer-social-btn"><i class="bi bi-discord"></i></a>
        </div>
      </div>
      
      <!-- Features / Product Column -->
      <div class="col-lg-3 col-md-6">
        <h4 class="footer-col-title">Platform</h4>
        <ul class="footer-links-list">
          <li><a href="features.php">Live Editor Sync</a></li>
          <li><a href="features.php">Workspace Chat</a></li>
          <li><a href="features.php">Simulated Versioning</a></li>
          <li><a href="features.php">Code Review comments</a></li>
          <li><a href="pricing.php">Pricing Plans</a></li>
        </ul>
      </div>
      
      <!-- Company Column -->
      <div class="col-lg-2 col-md-6">
        <h4 class="footer-col-title">Company</h4>
        <ul class="footer-links-list">
          <li><a href="about.php">About Manifesto</a></li>
          <li><a href="about.php">Founder Story</a></li>
          <li><a href="about.php">Core Values</a></li>
          <li><a href="#">Security Audits</a></li>
          <li><a href="#">Contact Support</a></li>
        </ul>
      </div>
      
      <!-- Legal / Resources Column -->
      <div class="col-lg-3 col-md-6">
        <h4 class="footer-col-title">Resources</h4>
        <ul class="footer-links-list">
          <li><a href="#">API Documentation</a></li>
          <li><a href="#">Developer Tutorials</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

    </div>
    
    <!-- Footer Bottom Row -->
    <div class="footer-bottom">
      <p class="footer-copy">&copy; <?= date('Y') ?> CollabDev. All rights reserved.</p>
      <p class="footer-tech-stack">Built with PHP · JavaScript · MySQL · CodeMirror · Bootstrap 5</p>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS (Animate on Scroll) JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Init AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-quad',
        once: true
    });
});
</script>
</body>
</html>
