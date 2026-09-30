<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /login3/dashboard.php');
    exit;
}

$top_projects = [];
try {
    $stmt = $pdo->prepare("
        SELECT p.*, u.username AS owner_name, u.avatar_color AS owner_color,
               (SELECT COUNT(*) FROM project_members pm WHERE pm.project_id = p.id) AS member_count,
               (SELECT COUNT(*) FROM files f WHERE f.project_id = p.id) AS file_count
        FROM projects p
        INNER JOIN users u ON u.id = p.owner_id
        WHERE p.visibility = 'public'
        ORDER BY p.updated_at DESC
        LIMIT 6
    ");
    $stmt->execute();
    $top_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$tech_images = [
    'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1542831371-29b0f74f9713?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=600&q=80',
    'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?auto=format&fit=crop&w=600&q=80',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CollabDev — Collaborative Coding Platform</title>
  <meta name="description" content="CollabDev is a real-time collaborative coding platform for development teams. Edit together, review code, and ship faster.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
      margin: 0;
      padding: 0;
      background: #ffffff;
      color: #374151;
      font-family: 'Inter', sans-serif;
      font-size: 15px;
      line-height: 1.6;
    }

    /* Shared */
    a { text-decoration: none; }
    h1, h2, h3 { color: #111827; font-weight: 700; letter-spacing: -0.02em; }

    .container-md {
      max-width: 1080px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* ── HERO ─────────────────────────────── */
    .hero {
      padding: 120px 0 90px;
      border-bottom: 1px solid #e5e7eb;
    }
    .hero h1 {
      font-size: clamp(36px, 5vw, 58px);
      line-height: 1.12;
      max-width: 620px;
      margin-bottom: 20px;
    }
    .hero p {
      font-size: 17px;
      color: #6b7280;
      max-width: 500px;
      margin-bottom: 36px;
      line-height: 1.7;
    }

    .btn-main {
      display: inline-block;
      padding: 11px 24px;
      background: #111827;
      color: #fff;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 600;
      transition: background 0.2s;
    }
    .btn-main:hover { background: #1f2937; color: #fff; }

    .btn-outline {
      display: inline-block;
      padding: 11px 24px;
      border: 1px solid #d1d5db;
      color: #374151;
      border-radius: 7px;
      font-size: 14px;
      font-weight: 500;
      transition: border-color 0.2s, color 0.2s;
    }
    .btn-outline:hover { border-color: #9ca3af; color: #111827; }

    /* ── FEATURES ─────────────────────────── */
    .features {
      padding: 90px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    .features-header { margin-bottom: 56px; }
    .features-header h2 { font-size: clamp(26px, 3.5vw, 36px); margin-bottom: 12px; }
    .features-header p { font-size: 15px; color: #6b7280; max-width: 420px; }

    .feat-item {
      padding: 28px 0;
      border-top: 1px solid #e5e7eb;
      display: flex;
      align-items: flex-start;
      gap: 20px;
    }
    .feat-item:last-child { border-bottom: 1px solid #e5e7eb; }
    .feat-num {
      font-size: 12px;
      font-weight: 700;
      color: #9ca3af;
      padding-top: 3px;
      min-width: 28px;
    }
    .feat-body h3 {
      font-size: 16px;
      font-weight: 600;
      color: #111827;
      margin-bottom: 6px;
    }
    .feat-body p {
      font-size: 14px;
      color: #6b7280;
      margin: 0;
      line-height: 1.6;
    }

    /* ── PROJECTS ─────────────────────────── */
    .projects {
      padding: 90px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    .projects-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 40px;
      flex-wrap: wrap;
      gap: 12px;
    }
    .projects-header h2 { font-size: clamp(24px, 3vw, 34px); margin: 0; }

    .proj-card {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
      transition: border-color 0.2s, box-shadow 0.2s;
      background: #fff;
    }
    .proj-card:hover {
      border-color: #9ca3af;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .proj-img {
      height: 150px;
      overflow: hidden;
      background: #f3f4f6;
    }
    .proj-img img {
      width: 100%; height: 100%;
      object-fit: cover;
      filter: saturate(0.7) brightness(0.95);
      transition: transform 0.4s;
    }
    .proj-card:hover .proj-img img { transform: scale(1.04); }

    .proj-body {
      padding: 18px 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .proj-owner {
      font-size: 11.5px;
      color: #9ca3af;
      margin-bottom: 5px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 500;
    }
    .proj-title {
      font-size: 15px;
      font-weight: 600;
      color: #111827;
      display: block;
      margin-bottom: 14px;
      transition: color 0.2s;
    }
    .proj-title:hover { color: #4f46e5; }
    .proj-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
      color: #9ca3af;
      padding-top: 12px;
      border-top: 1px solid #f3f4f6;
      margin-top: auto;
    }
    .proj-lang {
      background: #f3f4f6;
      color: #374151;
      font-size: 11px;
      font-weight: 600;
      padding: 2px 9px;
      border-radius: 4px;
      font-family: monospace;
    }

    /* ── CTA ──────────────────────────────── */
    .cta-section {
      padding: 90px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    .cta-inner {
      max-width: 520px;
    }
    .cta-inner h2 {
      font-size: clamp(26px, 3.5vw, 38px);
      margin-bottom: 14px;
    }
    .cta-inner p {
      font-size: 15px;
      color: #6b7280;
      margin-bottom: 30px;
      line-height: 1.7;
    }

    /* ── FOOTER ───────────────────────────── */
    .site-footer {
      padding: 56px 0 40px;
      background: #f9fafb;
      border-top: 1px solid #e5e7eb;
    }
    .footer-brand {
      font-size: 15px;
      font-weight: 700;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 10px;
    }
    .footer-brand-icon {
      width: 26px; height: 26px;
      background: #111827;
      border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px;
      color: #fff;
    }
    .footer-tagline {
      font-size: 13.5px;
      color: #6b7280;
      margin-bottom: 0;
      max-width: 240px;
      line-height: 1.5;
    }
    .footer-col-title {
      font-size: 12px;
      font-weight: 600;
      color: #111827;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 14px;
    }
    .footer-links { list-style: none; padding: 0; margin: 0; }
    .footer-links li { margin-bottom: 9px; }
    .footer-links a { font-size: 13.5px; color: #6b7280; transition: color 0.2s; }
    .footer-links a:hover { color: #111827; }
    .footer-bottom {
      margin-top: 48px;
      padding-top: 24px;
      border-top: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      font-size: 13px;
      color: #9ca3af;
    }

    @media (max-width: 768px) {
      .hero { padding: 90px 0 60px; }
      .features, .projects, .cta-section { padding: 60px 0; }
      .site-footer { padding: 40px 0 28px; }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="container-md">
    <h1>Build software together, in real time.</h1>
    <p>CollabDev is a collaborative coding workspace where teams edit files simultaneously, review code, and ship faster — without the usual setup overhead.</p>
    <div class="d-flex gap-3 flex-wrap">
      <a href="register.php" class="btn-main">Create free account</a>
      <a href="#projects" class="btn-outline">Browse public projects</a>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features">
  <div class="container-md">
    <div class="row">
      <div class="col-lg-4 features-header">
        <h2>A complete developer workspace.</h2>
        <p>Every tool your team needs, built into one platform.</p>
      </div>
      <div class="col-lg-7 offset-lg-1">

        <div class="feat-item">
          <div class="feat-num">01</div>
          <div class="feat-body">
            <h3>Real-time code editor</h3>
            <p>Multiple developers can edit the same file at the same time. Changes sync instantly — no conflicts, no manual merging.</p>
          </div>
        </div>

        <div class="feat-item">
          <div class="feat-num">02</div>
          <div class="feat-body">
            <h3>Built-in team chat</h3>
            <p>Discuss your code in context. Each workspace has a dedicated chat panel so conversations stay tied to the project.</p>
          </div>
        </div>

        <div class="feat-item">
          <div class="feat-num">03</div>
          <div class="feat-body">
            <h3>Git-style version control</h3>
            <p>Commit snapshots, switch branches, and view visual diffs between any two versions of a file.</p>
          </div>
        </div>

        <div class="feat-item">
          <div class="feat-num">04</div>
          <div class="feat-body">
            <h3>Inline code reviews</h3>
            <p>Leave comments on specific lines. Approve changes or request revisions before anything gets merged.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- PROJECTS -->
<section class="projects" id="projects">
  <div class="container-md">
    <div class="projects-header">
      <h2>Public workspaces</h2>
      <a href="register.php" class="btn-outline" style="font-size:13px; padding:8px 18px;">See all →</a>
    </div>

    <div class="row g-4">
      <?php if (count($top_projects) > 0): ?>
        <?php foreach($top_projects as $i => $p): ?>
          <div class="col-lg-4 col-sm-6">
            <div class="proj-card">
              <div class="proj-img">
                <img src="<?= $tech_images[$p['id'] % count($tech_images)] ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              </div>
              <div class="proj-body">
                <div class="proj-owner"><?= htmlspecialchars($p['owner_name']) ?></div>
                <a href="project.php?id=<?= $p['id'] ?>" class="proj-title"><?= htmlspecialchars($p['name']) ?></a>
                <div class="proj-meta">
                  <span class="proj-lang"><?= strtoupper(htmlspecialchars($p['language'])) ?></span>
                  <span><?= $p['member_count'] ?> member<?= $p['member_count'] != 1 ? 's' : '' ?></span>
                  <span><?= timeAgo($p['updated_at']) ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <p style="color:#9ca3af; padding: 40px 0;">No public workspaces yet. <a href="register.php" style="color:#111827;">Create the first one →</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container-md">
    <div class="cta-inner">
      <h2>Start collaborating today.</h2>
      <p>Free to use. No credit card required. Your first workspace is ready in under a minute.</p>
      <div class="d-flex gap-3 flex-wrap">
        <a href="register.php" class="btn-main">Create free account</a>
        <a href="features.php" class="btn-outline">See all features</a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container-md">
    <div class="row g-5">
      <div class="col-lg-4">
        <div class="footer-brand">
          <div class="footer-brand-icon">⚡</div>
          CollabDev
        </div>
        <p class="footer-tagline">A collaborative coding platform built for modern development teams.</p>
      </div>
      <div class="col-lg-2 col-6">
        <div class="footer-col-title">Product</div>
        <ul class="footer-links">
          <li><a href="features.php">Features</a></li>
          <li><a href="pricing.php">Pricing</a></li>
          <li><a href="register.php">Sign up</a></li>
          <li><a href="login.php">Sign in</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-6">
        <div class="footer-col-title">Company</div>
        <ul class="footer-links">
          <li><a href="about.php">About</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </div>
      <div class="col-lg-2 col-6">
        <div class="footer-col-title">Legal</div>
        <ul class="footer-links">
          <li><a href="#">Privacy</a></li>
          <li><a href="#">Terms</a></li>
          <li><a href="#">Security</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> CollabDev</span>
      <span>Built with PHP &amp; MySQL</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
