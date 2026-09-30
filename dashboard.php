<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — CollabDev</title>
  <meta name="description" content="Your CollabDev project dashboard. Manage and collaborate on all your coding projects.">
  <link rel="stylesheet" href="/login3/assets/css/app.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital,wght@0,400;1,400&display=swap');

    :root {
      /* Overrides for Linear dark theme */
      --bg-primary:     #08090a; /* Pitch Black */
      --bg-secondary:   #0f1011; /* Graphite */
      --bg-tertiary:    #161718; /* Deep Slate */
      --bg-elevated:    #23252a; /* Charcoal Grey */
      --bg-input:       #08090a;

      --border-default: #23252a; /* Charcoal Grey */
      --border-muted:   #161718;
      --border-subtle:  #0f1011;

      --text-primary:   #f7f8f8; /* Porcelain */
      --text-secondary: #d0d6e0; /* Light Steel */
      --text-muted:     #8a8f98; /* Storm Cloud */
      --text-disabled:  #62666d; /* Fog Grey */

      --accent-blue:    #e4f222; /* Primary Neon Lime Brand */
      --accent-blue-h:  #f0fa68; /* Hover Lime */
      --accent-green:   #3fb950;
      --accent-green-h: #56d364;
      --accent-yellow:  #02b8cc; /* Cyan Spark */
      --accent-red:     #ff7b72;
      --accent-purple:  #8b5cf6; /* Amethyst */
      --accent-orange:  #ffa657;

      --btn-primary-bg:    #e4f222;
      --btn-primary-hover: #f0fa68;

      --shadow-glow-blue:  0 0 25px rgba(228, 242, 34, 0.12), 0 0 50px rgba(228, 242, 34, 0.04);
    }

    body {
      background: var(--bg-primary);
      position: relative;
      overflow-x: hidden;
    }
    
    /* Background glows - Cinematic Neon Lime and Violet glows */
    body::before {
      content: '';
      position: fixed;
      width: 800px; height: 800px;
      background: radial-gradient(circle, rgba(228, 242, 34, 0.035) 0%, transparent 70%);
      top: -200px; right: -200px;
      pointer-events: none;
      z-index: 0;
    }
    body::after {
      content: '';
      position: fixed;
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(139, 92, 246, 0.035) 0%, transparent 70%);
      bottom: -150px; left: -150px;
      pointer-events: none;
      z-index: 0;
    }

    /* Liquid Glass panel styles */
    .liquid-glass {
      background: rgba(255, 255, 255, 0.01) !important;
      background-blend-mode: luminosity !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      border: none !important;
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.08) !important;
      position: relative;
      overflow: hidden;
    }
    .liquid-glass::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1.2px;
      background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.12) 20%, rgba(255,255,255,0) 40%, rgba(255,255,255,0) 60%, rgba(255,255,255,0.12) 80%, rgba(255,255,255,0.4) 100%);
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .liquid-glass-no-overflow {
      background: rgba(255, 255, 255, 0.01) !important;
      background-blend-mode: luminosity !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
      border: none !important;
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.08) !important;
      position: relative;
    }
    .liquid-glass-no-overflow::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1.2px;
      background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.12) 20%, rgba(255,255,255,0) 40%, rgba(255,255,255,0) 60%, rgba(255,255,255,0.12) 80%, rgba(255,255,255,0.4) 100%);
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .liquid-glass-dashed {
      background: rgba(255, 255, 255, 0.01) !important;
      background-blend-mode: luminosity !important;
      backdrop-filter: blur(8px) !important;
      -webkit-backdrop-filter: blur(8px) !important;
      border: 1px dashed var(--border-default) !important;
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.05) !important;
      position: relative;
      transition: all var(--transition);
    }
    .liquid-glass-dashed:hover {
      border-color: rgba(228, 242, 34, 0.3) !important;
      background: rgba(255, 255, 255, 0.02) !important;
      box-shadow: 0 0 15px rgba(228, 242, 34, 0.05) !important;
    }

    .dash-wrapper { padding-top: var(--topbar-height); min-height:100vh; position: relative; z-index: 10; }
    
    /* Topbar styling override */
    .topbar {
      background: rgba(8, 9, 10, 0.7) !important;
      border-bottom: none !important;
    }
    .topbar .logo-icon {
      background: linear-gradient(135deg, #e4f222, #8b5cf6) !important;
      box-shadow: 0 0 15px rgba(228, 242, 34, 0.2) !important;
    }

    /* Welcome Banner */
    .welcome-banner {
      border-radius: var(--radius-xl);
      padding: 2rem 2.5rem;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 1.75rem;
      box-shadow: 0 15px 40px rgba(0,0,0,0.5) !important;
    }
    .welcome-text h2 { 
      margin:0 0 .3rem; 
      font-size: 1.85rem; 
      font-weight: 400; 
      font-family: 'Instrument Serif', serif;
      color: var(--text-primary); 
    }
    .welcome-text p { margin:0; color:var(--text-secondary); font-size:0.95rem; }

    /* Buttons override */
    .btn-blue {
      background: var(--accent-blue) !important;
      color: #08090a !important;
      font-weight: 600 !important;
      border: 1px solid var(--accent-blue) !important;
      box-shadow: 0 2px 8px rgba(228, 242, 34, 0.15) !important;
      transition: all 0.2s ease !important;
      border-radius: var(--radius-full) !important;
      padding: 0.5rem 1.5rem !important;
    }
    .btn-blue:hover {
      background: var(--accent-blue-h) !important;
      border-color: var(--accent-blue-h) !important;
      color: #08090a !important;
      box-shadow: 0 4px 20px rgba(228, 242, 34, 0.3), var(--shadow-glow-blue) !important;
      transform: translateY(-1px);
    }
    .btn-secondary {
      background: rgba(255, 255, 255, 0.03) !important;
      border: 1px solid var(--border-default) !important;
      color: var(--text-primary) !important;
      transition: all 0.2s ease !important;
      border-radius: var(--radius-full) !important;
      padding: 0.5rem 1.5rem !important;
    }
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.06) !important;
      border-color: #323334 !important;
      color: var(--text-primary) !important;
      transform: translateY(-1px);
    }

    /* Stats Grid */
    .stat-card {
      border-radius: var(--radius-lg);
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1.25rem;
      transition: all 0.25s ease !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    }
    .stat-card:hover { 
      transform: translateY(-2px) !important;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4), var(--shadow-glow-blue) !important;
    }
    .stat-icon {
      width: 44px; height: 44px;
      border-radius: var(--radius-md) !important;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }

    /* Section Headers & Filters */
    .section-header { display:flex;align-items:center;justify-content:space-between;margin-top:2.5rem;margin-bottom:1.5rem; }
    .section-header h3 { 
      font-size:1.75rem; 
      font-weight: 400; 
      font-family: 'Instrument Serif', serif;
      letter-spacing: -0.01em;
    }
    .filter-bar { display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem; }
    
    .filter-btn { 
      padding:.4rem 1.1rem;
      border-radius:var(--radius-full);
      font-size:.82rem;
      border:1px solid var(--border-default) !important;
      background:rgba(255, 255, 255, 0.01) !important;
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      color:var(--text-secondary) !important;
      cursor:pointer;
      transition:all var(--transition); 
    }
    .filter-btn:hover {
      border-color: var(--text-muted) !important;
      background:rgba(255, 255, 255, 0.03) !important;
      color: var(--text-primary) !important;
    }
    .filter-btn.active { 
      background: rgba(228, 242, 34, 0.08) !important;
      border-color: #e4f222 !important;
      color: #e4f222 !important; 
      box-shadow: 0 0 12px rgba(228, 242, 34, 0.1) !important;
    }

    /* Projects cards */
    .project-card {
      border-radius: var(--radius-xl);
      padding: 1.5rem;
      transition: all var(--transition-slow);
    }
    .project-card:hover {
      transform: translateY(-4px) !important;
      box-shadow: 0 12px 35px rgba(0,0,0,0.5), 0 0 20px rgba(228,242,34,0.05) !important;
    }
    .project-name {
      font-family: 'Instrument Serif', serif;
      font-size: 1.45rem;
      font-weight: 400;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }

    /* Skeletons */
    .skeleton {
      background: linear-gradient(90deg, #161718 25%, #23252a 50%, #161718 75%);
      background-size: 200% 100%;
      animation: loading 1.5s infinite;
    }
    @keyframes loading {
      0% { background-position: 200% 0; }
      100% { background-position: -200% 0; }
    }

    /* Modals & Inputs */
    .modal-overlay {
      background: rgba(0,0,0,.65) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
    }
    .modal {
      border-radius: var(--radius-xl) !important;
      box-shadow: 0 25px 60px rgba(0,0,0,0.8) !important;
      max-width: 520px;
      width: 100%;
      background: rgba(15, 16, 17, 0.75) !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
    }
    .modal-header { 
      border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important; 
      padding: 1.25rem 1.75rem 1rem !important;
    }
    .modal-header h3 {
      font-family: 'Instrument Serif', serif;
      font-size: 1.7rem;
      font-weight: 400;
    }
    .modal-footer { 
      border-top: 1px solid rgba(255, 255, 255, 0.05) !important; 
      padding: 1rem 1.75rem !important;
    }
    
    .form-control {
      background: rgba(0, 0, 0, 0.4) !important;
      border: 1px solid var(--border-default) !important;
      color: var(--text-primary) !important;
      border-radius: var(--radius-md) !important;
      padding: 0.55rem 0.85rem !important;
    }
    .form-control:focus {
      border-color: #e4f222 !important;
      box-shadow: 0 0 10px rgba(228, 242, 34, 0.1) !important;
    }

    #project-search.form-control {
      background: rgba(255, 255, 255, 0.02) !important;
      border: 1px solid var(--border-default) !important;
      border-radius: var(--radius-full) !important;
      padding: 0.4rem 1.1rem !important;
      font-size: 0.85rem;
      transition: all var(--transition);
    }
    #project-search.form-control:focus {
      border-color: #e4f222 !important;
      box-shadow: 0 0 10px rgba(228, 242, 34, 0.1) !important;
      background: rgba(255, 255, 255, 0.04) !important;
    }

    /* User details and dropdowns styling */
    .dropdown-menu {
      background: rgba(15, 16, 17, 0.85) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      border: 1px solid var(--border-default) !important;
      box-shadow: 0 10px 30px rgba(0,0,0,0.6) !important;
      border-radius: var(--radius-md) !important;
    }
    .dropdown-item:hover { 
      background: rgba(255, 255, 255, 0.05) !important; 
    }
    .dropdown-divider { background: rgba(255, 255, 255, 0.05) !important; }
    
    /* Notification trigger item */
    .notif-badge {
      background: var(--accent-red) !important;
      border: 2px solid #08090a !important;
    }

    #user-menu-wrap > div:hover span:first-of-type {
      color: var(--accent-blue) !important;
    }
  </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="topbar liquid-glass-no-overflow" id="main-topbar">
  <div class="topbar-left">
    <a href="/login3/" class="logo">
      <div class="logo-icon">⚡</div>
      CollabDev
    </a>
    <div class="breadcrumb" style="margin-left:.5rem">
      <span class="breadcrumb-sep">/</span>
      <span class="current">Dashboard</span>
    </div>
  </div>

  <div class="topbar-right">
    <!-- Notifications -->
    <div class="dropdown" id="notif-dropdown-wrap">
      <button class="notif-btn" id="notif-btn-trigger" data-dropdown="notif-dropdown" title="Notifications">
        🔔
        <span class="notif-badge" id="notif-badge" style="display:none">0</span>
      </button>
      <div class="dropdown-menu" id="notif-dropdown" style="min-width:280px;max-height:380px;overflow-y:auto">
        <div style="padding:.5rem 1rem;border-bottom:1px solid var(--border-muted);display:flex;align-items:center;justify-content:space-between">
          <strong style="font-size:.85rem">Notifications</strong>
          <button class="btn btn-ghost btn-sm" onclick="Notifications.markAllRead()">Mark all read</button>
        </div>
        <div id="notif-list"><div class="dropdown-item text-muted" style="font-size:.85rem">Loading...</div></div>
      </div>
    </div>

    <!-- User menu -->
    <div class="dropdown" id="user-menu-wrap">
      <div style="cursor:pointer;display:flex;align-items:center;gap:.5rem"
           data-dropdown="user-dropdown">
        <div class="avatar avatar-sm"
             style="background:<?= sanitize($user['avatar_color'] ?? '#58a6ff') ?>">
          <?= getUserInitials($user['username']) ?>
        </div>
        <span style="font-size:.875rem;font-weight:500"><?= sanitize($user['username']) ?></span>
        <span style="font-size:.7rem;color:var(--text-muted)">▼</span>
      </div>
      <div class="dropdown-menu" id="user-dropdown">
        <div style="padding:.5rem .9rem .25rem;font-size:.75rem;color:var(--text-muted)"><?= sanitize($user['email']) ?></div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="/login3/dashboard.php">📊 Dashboard</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item danger" href="/login3/logout.php">🚪 Sign out</a>
      </div>
    </div>
  </div>
</nav>

<div class="dash-wrapper">
  <div class="dashboard-container">

    <!-- Welcome banner -->
    <div class="welcome-banner liquid-glass">
      <div class="avatar avatar-xl" style="background:<?= sanitize($user['avatar_color'] ?? '#58a6ff') ?>">
        <?= getUserInitials($user['username']) ?>
      </div>
      <div class="welcome-text">
        <h2>Welcome back, <?= sanitize($user['username']) ?>! 👋</h2>
        <p>Ready to build something great? Start a new project or jump back into an existing one.</p>
      </div>
      <button class="btn btn-blue" onclick="Modal.open('new-project-modal')" style="margin-left:auto;white-space:nowrap" id="new-project-btn">
        ＋ New Project
      </button>
    </div>

    <!-- Stats -->
    <div class="stats-grid" id="stats-grid">
      <div class="stat-card skeleton" style="height:88px"></div>
      <div class="stat-card skeleton" style="height:88px"></div>
      <div class="stat-card skeleton" style="height:88px"></div>
      <div class="stat-card skeleton" style="height:88px"></div>
    </div>

    <!-- Filters + Projects -->
    <div class="section-header">
      <h3>Your Projects</h3>
      <div style="display:flex;gap:.5rem">
        <input type="text" class="form-control" id="project-search" placeholder="🔍 Search projects..."
               style="width:200px;padding:.35rem .75rem;font-size:.85rem" oninput="filterProjects(this.value)">
      </div>
    </div>

    <div class="filter-bar" id="filter-bar">
      <button class="filter-btn active" data-filter="all" onclick="setFilter('all', this)">All</button>
      <button class="filter-btn" data-filter="owner" onclick="setFilter('owner', this)">Owner</button>
      <button class="filter-btn" data-filter="collaborator" onclick="setFilter('collaborator', this)">Collaborator</button>
      <button class="filter-btn" data-filter="public" onclick="setFilter('public', this)">Public</button>
      <button class="filter-btn" data-filter="private" onclick="setFilter('private', this)">Private</button>
    </div>

    <div id="projects-container">
      <!-- Skeleton -->
      <div class="projects-grid" id="projects-skeleton">
        <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="card liquid-glass" style="height:200px;padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
          <div class="skeleton" style="height:18px;width:60%;border-radius:4px"></div>
          <div class="skeleton" style="height:13px;width:90%;border-radius:4px"></div>
          <div class="skeleton" style="height:13px;width:70%;border-radius:4px"></div>
          <div style="margin-top:auto;display:flex;gap:.5rem">
            <div class="skeleton" style="height:12px;width:80px;border-radius:4px"></div>
            <div class="skeleton" style="height:12px;width:60px;border-radius:4px;margin-left:auto"></div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
      <div class="projects-grid" id="projects-grid" style="display:none"></div>
      <div id="empty-state" style="display:none">
        <div class="empty-state">
          <div class="empty-state-icon">📂</div>
          <h3>No projects yet</h3>
          <p>Create your first project and start collaborating with your team.</p>
          <button class="btn btn-blue" onclick="Modal.open('new-project-modal')">＋ Create your first project</button>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ============================================================
     NEW PROJECT MODAL
     ============================================================ -->
<div class="modal-overlay" id="new-project-modal">
  <div class="modal liquid-glass">
    <div class="modal-header">
      <h3>🆕 Create New Project</h3>
      <button class="btn-close" onclick="Modal.close('new-project-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div id="np-alert" style="display:none;margin-bottom:1rem"></div>
      <div class="form-group">
        <label class="form-label">Project name <span class="required">*</span></label>
        <input type="text" class="form-control" id="np-name" placeholder="e.g. My Awesome App" maxlength="100">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea class="form-control" id="np-desc" placeholder="What is this project about?" rows="3" maxlength="500"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Primary language</label>
        <select class="form-control" id="np-lang">
          <option value="php">PHP</option>
          <option value="javascript">JavaScript</option>
          <option value="python">Python</option>
          <option value="html">HTML/CSS</option>
          <option value="java">Java</option>
          <option value="cpp">C++</option>
          <option value="sql">SQL</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Visibility</label>
        <div style="display:flex;gap:1rem">
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem">
            <input type="radio" name="np-visibility" value="private" checked> 🔒 Private
          </label>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem">
            <input type="radio" name="np-visibility" value="public"> 🌍 Public
          </label>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('new-project-modal')">Cancel</button>
      <button class="btn btn-blue" id="create-project-btn" onclick="createProject()">Create Project</button>
    </div>
  </div>
</div>

<!-- Confirm modal (global) -->
<div class="modal-overlay" id="confirm-modal">
  <div class="modal liquid-glass" style="max-width:400px">
    <div class="modal-header"><h3>Confirm Action</h3></div>
    <div class="modal-body"><p id="confirm-message">Are you sure?</p></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('confirm-modal')">Cancel</button>
      <button class="btn btn-danger" id="confirm-ok-btn">Confirm</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toast-container"></div>
<script src="/login3/assets/js/app.js"></script>
<script>
let allProjects = [];
let activeFilter = 'all';
let searchQuery  = '';

// Language icons & colors
const langIcons = { php:'🐘', javascript:'📜', python:'🐍', html:'🌐', css:'🎨', sql:'🗄️', java:'☕', cpp:'⚙️', other:'📦' };
const langColors = { php:'var(--accent-purple)', javascript:'var(--accent-yellow)', python:'var(--accent-blue)', html:'var(--accent-orange)', sql:'var(--accent-green)', java:'var(--accent-red)', other:'var(--text-muted)' };

async function loadDashboard() {
  try {
    const data = await API.get('/login3/api/projects.php', { action: 'list' });
    allProjects = data.projects || [];
    renderStats(data.stats || {});
    renderProjects();
  } catch (e) {
    document.getElementById('projects-skeleton').style.display = 'none';
    document.getElementById('projects-grid').style.display = 'none';
    document.getElementById('empty-state').style.display = 'block';
    Toast.error('Failed to load projects');
  }
}

function renderStats(stats) {
  const total = allProjects.length;
  const owned = allProjects.filter(p => p.my_role === 'owner').length;
  const collab= allProjects.filter(p => p.my_role === 'collaborator').length;

  let totalFiles = 0, totalMembers = 0;
  allProjects.forEach(p => { totalFiles += parseInt(p.file_count||0); totalMembers += parseInt(p.member_count||0); });

  document.getElementById('stats-grid').innerHTML = `
    <div class="stat-card liquid-glass animate-fade">
      <div class="stat-icon" style="background:rgba(228,242,34,.05)">📁</div>
      <div class="stat-info">
        <div class="stat-value" style="color:var(--accent-blue); font-family: 'Instrument Serif', serif; font-size: 1.65rem; font-weight: 400;">${total}</div>
        <div class="stat-label">Total Projects</div>
      </div>
    </div>
    <div class="stat-card liquid-glass animate-fade" style="animation-delay:.05s">
      <div class="stat-icon" style="background:rgba(63,185,80,.05)">👑</div>
      <div class="stat-info">
        <div class="stat-value" style="color:var(--accent-green); font-family: 'Instrument Serif', serif; font-size: 1.65rem; font-weight: 400;">${owned}</div>
        <div class="stat-label">Owned by Me</div>
      </div>
    </div>
    <div class="stat-card liquid-glass animate-fade" style="animation-delay:.1s">
      <div class="stat-icon" style="background:rgba(139,92,246,.05)">📄</div>
      <div class="stat-info">
        <div class="stat-value" style="color:var(--accent-purple); font-family: 'Instrument Serif', serif; font-size: 1.65rem; font-weight: 400;">${totalFiles}</div>
        <div class="stat-label">Total Files</div>
      </div>
    </div>
    <div class="stat-card liquid-glass animate-fade" style="animation-delay:.15s">
      <div class="stat-icon" style="background:rgba(255,166,87,.05)">👥</div>
      <div class="stat-info">
        <div class="stat-value" style="color:var(--accent-orange); font-family: 'Instrument Serif', serif; font-size: 1.65rem; font-weight: 400;">${totalMembers}</div>
        <div class="stat-label">Collaborators</div>
      </div>
    </div>
  `;
}

function renderProjects() {
  const grid     = document.getElementById('projects-grid');
  const skeleton = document.getElementById('projects-skeleton');
  const empty    = document.getElementById('empty-state');

  let filtered = allProjects.filter(p => {
    const matchesFilter =
      activeFilter === 'all' ? true :
      activeFilter === 'owner' ? p.my_role === 'owner' :
      activeFilter === 'collaborator' ? p.my_role === 'collaborator' :
      activeFilter === 'public' ? p.visibility === 'public' :
      activeFilter === 'private' ? p.visibility === 'private' : true;

    const matchesSearch = !searchQuery ||
      p.name.toLowerCase().includes(searchQuery) ||
      (p.description || '').toLowerCase().includes(searchQuery);

    return matchesFilter && matchesSearch;
  });

  skeleton.style.display = 'none';

  if (filtered.length === 0) {
    grid.style.display = 'none';
    empty.style.display = 'block';
    if (allProjects.length === 0) {
      empty.querySelector('h3').textContent = 'No projects yet';
      empty.querySelector('p').textContent = 'Create your first project and start collaborating.';
    } else {
      empty.querySelector('h3').textContent = 'No matching projects';
      empty.querySelector('p').textContent = 'Try a different search or filter.';
      empty.querySelector('button').style.display = 'none';
    }
    return;
  }

  empty.style.display = 'none';
  grid.style.display = 'grid';
  grid.innerHTML = filtered.map((p, i) => `
    <a href="/login3/project.php?id=${p.id}"
       class="project-card liquid-glass animate-fade"
       style="animation-delay:${i*0.04}s"
       data-search="${p.name} ${p.description || ''}">
      <div class="project-card-header">
        <div>
          <div class="project-name">${escapeHtml(p.name)}</div>
          <div style="margin-top:.2rem">
            <span class="badge ${p.visibility==='public'?'badge-blue':'badge-gray'}">
              ${p.visibility==='public'?'🌍 Public':'🔒 Private'}
            </span>
            <span class="badge badge-purple" style="margin-left:.3rem">
              ${p.my_role}
            </span>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem">
          <div style="font-size:1.5rem" title="${p.language}">${langIcons[p.language]||'📦'}</div>
          ${p.my_role === 'owner' ? `
            <button class="btn-delete-project" onclick="confirmDeleteProject(event, ${p.id}, '${escapeJs(p.name)}')" title="Delete Project">
              🗑️
            </button>
          ` : ''}
        </div>
      </div>

      <div class="project-desc">
        ${p.description ? escapeHtml(p.description) : '<em style="color:var(--text-muted)">No description</em>'}
      </div>

      <div class="project-meta">
        <div class="project-meta-item">📄 ${p.file_count || 0} file${p.file_count!=1?'s':''}</div>
        <div class="project-meta-item">👥 ${p.member_count || 1} member${p.member_count!=1?'s':''}</div>
        <div class="project-meta-item">🌿 main</div>
      </div>

      <div class="project-footer">
        <div style="font-size:.75rem;color:var(--text-muted)">
          Updated ${p.updated_at_human || 'recently'}
        </div>
        <div class="avatar-stack">
          ${(p.members||[]).slice(0,3).map(m=>`
            <div class="avatar avatar-sm" style="background:${m.avatar_color||'#58a6ff'}" title="${m.username}">
              ${(m.username||'?').slice(0,2).toUpperCase()}
            </div>`).join('')}
        </div>
      </div>
    </a>
  `).join('') + `
    <!-- Add project card -->
    <div class="project-card liquid-glass-dashed animate-fade" style="animation-delay:${filtered.length*0.04}s;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;min-height:200px;text-align:center"
         onclick="Modal.open('new-project-modal')">
      <div style="font-size:2rem;margin-bottom:.5rem;color:var(--accent-blue)">＋</div>
      <div style="font-weight:500;color:var(--text-primary)">New Project</div>
      <div style="font-size:.8rem;color:var(--text-muted);margin-top:.25rem">Start a new collaboration</div>
    </div>
  `;
}

function setFilter(filter, el) {
  activeFilter = filter;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  renderProjects();
}

function filterProjects(q) {
  searchQuery = q.toLowerCase().trim();
  renderProjects();
}

async function createProject() {
  const name = document.getElementById('np-name').value.trim();
  const desc = document.getElementById('np-desc').value.trim();
  const lang = document.getElementById('np-lang').value;
  const vis  = document.querySelector('input[name="np-visibility"]:checked')?.value || 'private';
  const alert= document.getElementById('np-alert');
  const btn  = document.getElementById('create-project-btn');

  alert.style.display = 'none';
  if (!name) {
    alert.className = 'alert alert-danger';
    alert.innerHTML = '<span>⚠</span> Project name is required';
    alert.style.display = 'flex';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Creating...';

  try {
    const data = await API.post('/login3/api/projects.php', {
      action: 'create', name, description: desc, language: lang, visibility: vis
    });
    Toast.success(`Project "${name}" created!`);
    Modal.close('new-project-modal');
    document.getElementById('np-name').value = '';
    document.getElementById('np-desc').value = '';
    setTimeout(() => {
      window.location.href = data.redirect || `/login3/project.php?id=${data.project_id}`;
    }, 600);
  } catch (e) {
    alert.className = 'alert alert-danger';
    alert.innerHTML = `<span>⚠</span> ${e.message || 'Failed to create project'}`;
    alert.style.display = 'flex';
    btn.disabled = false;
    btn.textContent = 'Create Project';
  }
}

function confirmDeleteProject(event, projectId, projectName) {
  event.preventDefault();
  event.stopPropagation();
  confirmAction(`Are you sure you want to delete the project "${projectName}"? This action cannot be undone and will delete all files, git commits, and reviews.`, async () => {
    try {
      const res = await API.post('/login3/api/projects.php', { action: 'delete', id: projectId });
      Toast.success(res.message || 'Project deleted successfully.');
      loadDashboard();
    } catch (e) {
      Toast.error(e.message || 'Failed to delete project.');
    }
  });
}

function escapeJs(str) {
  return (str || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function escapeHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str||''));
  return d.innerHTML;
}

// Init
document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
  Notifications.init();

  // Fix notifications button
  document.getElementById('notif-btn-trigger').addEventListener('click', function(e) {
    e.stopPropagation();
    const wrap = document.getElementById('notif-dropdown-wrap');
    const isOpen = wrap.classList.contains('open');
    document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) { wrap.classList.add('open'); Notifications.loadList(); }
  });

  document.getElementById('np-name').addEventListener('keydown', e => {
    if (e.key === 'Enter') createProject();
  });
});
</script>
</body>
</html>
