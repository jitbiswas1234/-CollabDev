<?php
// features.php - Features Page
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
  <title>Features — CollabDev</title>
  <meta name="description" content="Explore CollabDev's real-time collaborative coding, chat, version control, and code review features.">
  
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
    padding: 160px 0 80px;
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
    margin: 0 auto 40px;
    font-weight: 300;
  }

  /* ================= FEATURES GRID ================= */
  .features-grid-section {
    padding: 60px 0 100px;
    position: relative;
    z-index: 10;
  }
  
  .premium-card {
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
    padding: 40px 30px;
  }

  .premium-card::before {
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

  .premium-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(139, 92, 246, 0.12);
    border-color: rgba(228, 242, 34, 0.15);
  }

  .card-icon-wrapper {
    width: 65px;
    height: 65px;
    border-radius: 20px;
    background: rgba(139, 92, 246, 0.1);
    color: var(--theme-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 25px;
    transition: var(--transition);
  }

  .premium-card:hover .card-icon-wrapper {
    background: var(--theme-primary);
    color: #fff;
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.4);
  }

  .card-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #ffffff;
  }

  .card-desc {
    color: #8b949e;
    font-size: 14px;
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

  @media (max-width: 768px) {
    .hero-title { font-size: 40px; }
    .cta-banner { margin: 0 10px 50px; padding: 60px 20px; }
    .cta-content h2 { font-size: 30px; }
  }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section" data-aos="fade-down">
  <div class="container">
    <h1 class="hero-title">Features Built <span>for Developers</span></h1>
    <p class="hero-desc">An integrated suite of real-time tools designed to maximize productivity and eliminate collaboration friction.</p>
  </div>
</section>

<!-- FEATURES GRID SECTION -->
<section class="features-grid-section">
  <div class="container">
    <div class="row g-4">
      
      <!-- Feature 1 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-code-slash"></i>
          </div>
          <h3 class="card-title">Real-Time Editor</h3>
          <p class="card-desc">Simultaneous multi-user file editing using CodeMirror integration. Features active remote cursor markers and character-accurate workspace synchronization.</p>
        </div>
      </div>

      <!-- Feature 2 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-chat-left-text"></i>
          </div>
          <h3 class="card-title">Workspace Team Chat</h3>
          <p class="card-desc">Communicate instantly with team members directly inside the workspace view. Discuss ideas, share files, and iterate rapidly without leaving your editor context.</p>
        </div>
      </div>

      <!-- Feature 3 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-git"></i>
          </div>
          <h3 class="card-title">Git Simulation Control</h3>
          <p class="card-desc">Snapshot your files into simulation branches at key milestones. Easily review project commits and perform quick restores directly from the commits log.</p>
        </div>
      </div>

      <!-- Feature 4 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-file-diff"></i>
          </div>
          <h3 class="card-title">Visual Code Diffs</h3>
          <p class="card-desc">Compare current file states side-by-side with previous versions. Instantly identify additions, modifications, and deletions with lines-accurate highlighting.</p>
        </div>
      </div>

      <!-- Feature 5 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-shield-check"></i>
          </div>
          <h3 class="card-title">Inline Code Reviews</h3>
          <p class="card-desc">Review your peers' commits line-by-line. Submit detailed feedback, request revisions, approve changes, and post inline comments to resolve issues quickly.</p>
        </div>
      </div>

      <!-- Feature 6 -->
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
        <div class="premium-card">
          <div class="card-icon-wrapper">
            <i class="bi bi-bell"></i>
          </div>
          <h3 class="card-title">Smart Notifications</h3>
          <p class="card-desc">Never miss workspace updates. Receive real-time system alerts and toast notifications for new invites, commit approvals, or review comments.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA BANNER -->
<section class="container" data-aos="zoom-in" data-aos-duration="800">
  <div class="cta-banner">
    <div class="cta-content">
      <h2>Experience <span>Next-Gen</span> Coding</h2>
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
