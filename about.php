<?php
// about.php - About Page
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
  <title>About Us — CollabDev</title>
  <meta name="description" content="Learn more about our mission to simplify developer collaboration on CollabDev.">
  
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

  /* ================= MANIFESTO SECTION ================= */
  .manifesto-section {
    padding: 40px 0 80px;
    position: relative;
    z-index: 10;
  }

  .manifesto-card {
    background: rgba(255, 255, 255, 0.01) !important;
    background-blend-mode: luminosity !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border-radius: 30px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.03);
    padding: 60px 50px;
    position: relative;
  }

  .manifesto-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.2px;
    background: linear-gradient(135deg, rgba(228, 242, 34, 0.25) 0%, rgba(255,255,255,0.01) 40%, rgba(139, 92, 246, 0.25) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }

  .manifesto-text {
    font-family: var(--font-serif);
    font-size: 32px;
    line-height: 1.4;
    color: #ffffff;
    font-style: italic;
    margin-bottom: 30px;
    position: relative;
  }
  .manifesto-text::before {
    content: '“';
    font-size: 80px;
    color: rgba(228, 242, 34, 0.15);
    position: absolute;
    top: -45px;
    left: -20px;
    font-family: Georgia, serif;
  }
  .manifesto-author {
    font-family: var(--font-heading);
    font-size: 15px;
    font-weight: 600;
    color: var(--theme-accent);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
  }

  /* ================= VALUES SECTION ================= */
  .values-section {
    padding: 60px 0 100px;
    position: relative;
    z-index: 10;
  }

  .value-card {
    background: rgba(255, 255, 255, 0.005) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.02);
    padding: 35px 25px;
    transition: var(--transition);
    height: 100%;
    position: relative;
  }
  .value-card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1px;
    background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, transparent 60%, rgba(255,255,255,0.08) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }
  .value-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.01) !important;
  }

  .value-icon {
    font-size: 26px;
    color: var(--theme-primary);
    margin-bottom: 20px;
  }
  .value-card:hover .value-icon {
    color: var(--theme-accent);
  }

  .value-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 12px;
  }
  .value-desc {
    color: #8b949e;
    font-size: 13.5px;
    line-height: 1.6;
    margin: 0;
  }

  /* ================= CTA BANNER ================= */
  .cta-banner {
    background: linear-gradient(to right, #08090a, #0f1011);
    padding: 80px 0;
    border-radius: 30px;
    margin: 0 20px 80px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.03);
    text-align: center;
  }

  .cta-banner::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.2px;
    background: linear-gradient(135deg, rgba(228, 242, 34, 0.2) 0%, transparent 60%, rgba(139, 92, 246, 0.2) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
  }

  .cta-content {
    position: relative;
    z-index: 2;
    color: #fff;
  }
  .cta-content h2 {
    color: #fff;
    font-size: 40px;
    margin-bottom: 20px;
  }
  .cta-content h2 span {
    font-family: var(--font-serif);
    font-weight: 400;
    font-style: italic;
    color: var(--theme-accent);
  }
  .cta-content p {
    color: #8b949e;
    font-size: 17px;
    margin-bottom: 35px;
    max-width: 600px;
    margin-inline: auto;
  }

  /* Primary Button Design */
  .btn-premium {
    background: var(--theme-primary);
    color: #fff;
    padding: 14px 36px;
    border-radius: 50px;
    font-family: var(--font-heading);
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.5px;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: none;
    box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
    text-decoration: none;
  }

  .btn-premium:hover {
    background: var(--theme-secondary);
    color: #fff;
    transform: translateY(-3px);
    box-shadow: 0 15px 25px rgba(139, 92, 246, 0.4);
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

  .founder-section {
    padding: 60px 0 100px;
    position: relative;
    z-index: 10;
  }
  .founder-img-wrap {
    position: relative;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.03);
    max-width: 440px;
    margin: 0 auto;
  }
  .founder-img-wrap::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1.2px;
    background: linear-gradient(180deg, rgba(255,255,255,0.2) 0%, transparent 50%, rgba(139,92,246,0.3) 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
    z-index: 2;
  }
  .founder-img-wrap img {
    width: 100%;
    height: auto;
    object-fit: cover;
    display: block;
    transition: var(--transition);
  }
  .founder-img-wrap:hover img {
    transform: scale(1.03);
  }
  .founder-title-badge {
    display: inline-block;
    background: rgba(228, 242, 34, 0.1);
    color: var(--theme-accent);
    border: 1px solid rgba(228, 242, 34, 0.25);
    padding: 5px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
  }
  .founder-name {
    font-size: 40px;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 20px;
    line-height: 1.1;
  }
  .founder-name span {
    font-family: var(--font-serif);
    font-weight: 400;
    font-style: italic;
    color: var(--theme-accent);
  }
  .founder-paragraph {
    color: #8b949e;
    font-size: 15px;
    line-height: 1.8;
    margin-bottom: 25px;
  }
  .founder-quote {
    font-family: var(--font-serif);
    font-size: 20px;
    font-style: italic;
    color: #ffffff;
    border-left: 2px solid var(--theme-primary);
    padding-left: 20px;
    margin: 25px 0;
  }

  @media (max-width: 768px) {
    .hero-title { font-size: 40px; }
    .manifesto-card { padding: 40px 30px; }
    .manifesto-text { font-size: 24px; }
    .cta-banner { margin: 0 10px 50px; padding: 60px 20px; }
    .cta-content h2 { font-size: 30px; }
    .founder-name { font-size: 32px; }
  }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section" data-aos="fade-down">
  <div class="container">
    <h1 class="hero-title">About <span>CollabDev</span></h1>
    <p class="hero-desc">We are on a mission to build the world's most intuitive, setup-free, real-time collaboration environment for developers.</p>
  </div>
</section>

<!-- MANIFESTO SECTION -->
<section class="manifesto-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
        <div class="manifesto-card text-center">
          <p class="manifesto-text">
            We believe that coding is inherently social, yet modern development setups have built higher walls instead of wider tables. Configuring complex virtual machines, setting up Docker containers, and managing SSH keys should not stand between developers and building great software together. We built CollabDev to let teams jump right into the editor and share ideas instantly.
          </p>
          <p class="manifesto-author">— The CollabDev Manifesto</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOUNDER SECTION -->
<section class="founder-section">
  <div class="container">
    <div class="row align-items-center g-5">
      
      <!-- Founder Image -->
      <div class="col-lg-5" data-aos="fade-right">
        <div class="founder-img-wrap">
          <img src="assets/img/founder.jpg" alt="Jit - Founder & Director of CollabDev">
        </div>
      </div>
      
      <!-- Founder Biography Content -->
      <div class="col-lg-7" data-aos="fade-left">
        <span class="founder-title-badge">Leadership</span>
        <h2 class="founder-name">Meet Our Founder, <span>Jit</span></h2>
        
        <p class="founder-paragraph">
          CollabDev was founded in 2026 by Jit, a software engineer who experienced first-hand the friction of configuring development environments across team members. He recognized that while communications tools and deployment pipelines had advanced rapidly, the core coding workspace remained isolated. Developers were regularly spending hours alignment-checking dependencies, resolving Docker network glitches, managing keys, and copying files back and forth—stifling productivity and natural creative workflow.
        </p>
        
        <p class="founder-paragraph">
          With CollabDev, Jit set out to build a platform that strips away local VM setup entirely. By designing a lightweight, character-accurate database synchronization model and pairing it with a high-fidelity inline text editor, teams can now spin up collaborative, secure workgroups within 500 milliseconds. Jit's commitment to building elegant developer toolsets underpins the platform's vision: enabling developer teams to sit at a single global table and code in absolute harmony.
        </p>
        
        <p class="founder-quote">
          "Software engineering is inherently collaborative, yet our tools continue to treat code like a solitary endeavor. We built CollabDev to erase boundaries, letting developers code together, chat, and review commits instantly."
        </p>
        
        <p class="founder-author" style="font-size: 13px;">— Jit, Founder & Director of CollabDev</p>
      </div>

    </div>
  </div>
</section>

<!-- VALUES SECTION -->
<section class="values-section">
  <div class="container">
    <div class="row g-4">
      
      <!-- Value 1 -->
      <div class="col-lg-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-vector-pen"></i></div>
          <h4 class="value-title">Extreme Simplicity</h4>
          <p class="value-desc">No complicated containers or virtual environments. Create a workspace, invite members by email, and start editing in seconds.</p>
        </div>
      </div>

      <!-- Value 2 -->
      <div class="col-lg-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-lightning-charge-fill"></i></div>
          <h4 class="value-title">High Speed Sync</h4>
          <p class="value-desc">Lightweight sync queues ensure code merges instantly under 500ms, maintaining smooth flow without workspace clashes.</p>
        </div>
      </div>

      <!-- Value 3 -->
      <div class="col-lg-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-people-fill"></i></div>
          <h4 class="value-title">Integrated Context</h4>
          <p class="value-desc">Built-in live chat, visual branch simulation commits, and inline code reviews placed side-by-side inside your active editor view.</p>
        </div>
      </div>

      <!-- Value 4 -->
      <div class="col-lg-3 col-sm-6" data-aos="fade-up" data-aos-delay="400">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-shield-check"></i></div>
          <h4 class="value-title">Secure & Autonomous</h4>
          <p class="value-desc">Robust role permissions (Owner, Collaborator, Viewer) and commit restore features protect your codebase integrity at all times.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="container" data-aos="zoom-in" data-aos-duration="800">
  <div class="cta-banner">
    <div class="cta-content">
      <h2>Build <span>Something Great</span> Together</h2>
      <p>Sign up now to invite your team, spin up collaborative workspaces, and start coding together in real-time.</p>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php" class="btn-premium">
          Go To Dashboard <i class="bi bi-arrow-right"></i>
        </a>
      <?php else: ?>
        <a href="register.php" class="btn-premium">
          Create Account Free <i class="bi bi-arrow-right"></i>
        </a>
      <?php endif; ?>
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
