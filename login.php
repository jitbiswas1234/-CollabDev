<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /login3/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — CollabDev</title>
  <meta name="description" content="Sign in to your CollabDev account and start collaborating with your team.">
  <link rel="stylesheet" href="/login3/assets/css/app.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital,wght@0,400;1,400&display=swap');

    body {
      background: #08090a;
      position: relative;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Background glows */
    body::before {
      content: '';
      position: fixed;
      width: 700px; height: 700px;
      background: radial-gradient(circle, rgba(228, 242, 34, 0.04) 0%, transparent 70%);
      top: -150px; right: -150px;
      pointer-events: none;
      z-index: 0;
    }
    body::after {
      content: '';
      position: fixed;
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(139, 92, 246, 0.04) 0%, transparent 70%);
      bottom: -100px; left: -100px;
      pointer-events: none;
      z-index: 0;
    }

    /* Floating Liquid Glass Card */
    .liquid-glass-card {
      background: rgba(255, 255, 255, 0.01) !important;
      background-blend-mode: luminosity !important;
      backdrop-filter: blur(16px) !important;
      -webkit-backdrop-filter: blur(16px) !important;
      border: none !important;
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.08), 0 20px 50px rgba(0,0,0,0.6) !important;
      position: relative;
      overflow: hidden;
      border-radius: var(--radius-xl);
      padding: 2.5rem;
      width: 100%;
      max-width: 420px;
      z-index: 10;
    }
    .liquid-glass-card::before {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: inherit;
      padding: 1.2px;
      background: linear-gradient(180deg, rgba(255,255,255,0.45) 0%, rgba(255,255,255,0.12) 20%, rgba(255,255,255,0) 40%, rgba(255,255,255,0) 60%, rgba(255,255,255,0.12) 80%, rgba(255,255,255,0.45) 100%);
      -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      pointer-events: none;
    }

    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
      position: relative;
      z-index: 1;
    }

    /* Headings */
    h2 {
      font-family: 'Instrument Serif', serif;
      font-size: 2.1rem;
      font-weight: 400;
      margin-bottom: 0.35rem;
      color: var(--text-primary);
      letter-spacing: -0.01em;
    }
    .subtitle {
      color: var(--text-secondary);
      font-size: 0.92rem;
      margin-bottom: 2rem;
    }

    /* Logo Icon Override */
    .logo-icon {
      background: linear-gradient(135deg, #e4f222, #8b5cf6) !important;
      box-shadow: 0 0 15px rgba(228, 242, 34, 0.2) !important;
    }

    /* Form Controls */
    .form-control {
      background: rgba(0, 0, 0, 0.4) !important;
      border: 1px solid var(--border-default) !important;
      color: var(--text-primary) !important;
      border-radius: var(--radius-md) !important;
      padding: 0.55rem 0.85rem !important;
      font-size: 0.9rem !important;
    }
    .form-control:focus {
      border-color: #e4f222 !important;
      box-shadow: 0 0 10px rgba(228, 242, 34, 0.1) !important;
    }

    /* Buttons */
    .btn-blue {
      background: var(--accent-blue) !important;
      color: #08090a !important;
      font-weight: 600 !important;
      border: 1px solid var(--accent-blue) !important;
      box-shadow: 0 2px 8px rgba(228, 242, 34, 0.15) !important;
      transition: all 0.2s ease !important;
      border-radius: var(--radius-full) !important;
      padding: 0.6rem 1.5rem !important;
    }
    .btn-blue:hover {
      background: var(--accent-blue-h) !important;
      border-color: var(--accent-blue-h) !important;
      color: #08090a !important;
      box-shadow: 0 4px 20px rgba(228, 242, 34, 0.3), 0 0 25px rgba(228, 242, 34, 0.1) !important;
      transform: translateY(-1px);
    }
    .btn-secondary {
      background: rgba(255, 255, 255, 0.03) !important;
      border: 1px solid var(--border-default) !important;
      color: var(--text-primary) !important;
      transition: all 0.2s ease !important;
    }
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.06) !important;
      border-color: #323334 !important;
      color: var(--text-primary) !important;
    }

    /* Demo Button dashed */
    .demo-btn {
      background: rgba(255, 255, 255, 0.01) !important;
      border: 1px dashed var(--border-default) !important;
      border-radius: var(--radius-md) !important;
      padding: 0.75rem 1rem !important;
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
      transition: all var(--transition) !important;
    }
    .demo-btn:hover {
      border-color: rgba(228, 242, 34, 0.3) !important;
      background: rgba(255, 255, 255, 0.02) !important;
      box-shadow: 0 0 15px rgba(228, 242, 34, 0.04) !important;
    }

    .divider {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 1.5rem 0;
      color: var(--text-muted);
      font-size: 0.8rem;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(255, 255, 255, 0.05);
    }
  </style>
</head>
<body>
<div class="auth-wrapper">
  <div class="liquid-glass-card animate-slide-up">
    <!-- Header Logo -->
    <a href="/login3/" class="logo" style="margin-bottom:2rem;justify-content:center">
      <div class="logo-icon">⚡</div>
      CollabDev
    </a>
    
    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to continue to your workspace</p>

    <!-- Alert area -->
    <div id="login-alert" style="display:none;margin-bottom:1.25rem"></div>

    <form id="login-form" novalidate>
      <div class="form-group">
        <label class="form-label" for="email">Email address <span class="required">*</span></label>
        <input type="email" class="form-control" id="email" name="email"
               placeholder="you@example.com" autocomplete="email" required>
        <div class="form-error" id="email-error" style="display:none"></div>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem">
        <label class="form-label" for="password">Password <span class="required">*</span></label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="••••••••" autocomplete="current-password" required>
          <button type="button" class="btn btn-secondary" id="toggle-pass" style="border-left:none;padding:0 0.75rem">👁</button>
        </div>
        <div class="form-error" id="pass-error" style="display:none"></div>
      </div>

      <button type="submit" class="btn btn-blue w-full" id="login-btn">
        <span id="login-btn-text">Sign In</span>
        <span id="login-spinner" class="spinner" style="display:none;width:16px;height:16px;border-width:2px"></span>
      </button>
    </form>

    <div class="divider">or</div>

    <!-- Demo details box -->
    <div class="demo-btn" onclick="fillDemo()">
      <div>
        <div style="font-size:0.85rem;font-weight:500;color:var(--text-primary)">🎭 Try Demo Account</div>
        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem">demo@example.com / demo123</div>
      </div>
      <span style="color:var(--accent-blue);font-weight:700">→</span>
    </div>

    <p style="text-align:center;margin-top:2rem;font-size:0.875rem;color:var(--text-secondary)">
      Don't have an account?
      <a href="/login3/register.php" style="font-weight:500">Create one free</a>
    </p>
  </div>
</div>

<div class="toast-container" id="toast-container"></div>
<script src="/login3/assets/js/app.js"></script>
<script>
function fillDemo() {
  document.getElementById('email').value = 'demo@example.com';
  document.getElementById('password').value = 'demo123';
  document.getElementById('email').classList.remove('is-invalid');
  document.getElementById('password').classList.remove('is-invalid');
}

// Toggle password visibility
document.getElementById('toggle-pass').addEventListener('click', function() {
  const inp = document.getElementById('password');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  this.textContent = inp.type === 'password' ? '👁' : '🙈';
});

// Login form submission
document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const btn      = document.getElementById('login-btn');
  const btnText  = document.getElementById('login-btn-text');
  const spinner  = document.getElementById('login-spinner');
  const alert    = document.getElementById('login-alert');

  // Clear errors
  alert.style.display = 'none';
  document.getElementById('email-error').style.display = 'none';
  document.getElementById('pass-error').style.display  = 'none';

  // Basic validation
  let valid = true;
  if (!email) {
    document.getElementById('email').classList.add('is-invalid');
    document.getElementById('email-error').textContent = 'Email is required';
    document.getElementById('email-error').style.display = 'block';
    valid = false;
  }
  if (!password) {
    document.getElementById('password').classList.add('is-invalid');
    document.getElementById('pass-error').textContent = 'Password is required';
    document.getElementById('pass-error').style.display = 'block';
    valid = false;
  }
  if (!valid) return;

  // Show spinner
  btn.disabled = true;
  btnText.textContent = 'Signing in...';
  spinner.style.display = 'inline-block';

  try {
    const res = await fetch('/login3/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'login', email, password }),
    });
    const data = await res.json();

    if (data.success) {
      btnText.textContent = '✓ Success!';
      Toast.success('Welcome back!');
      setTimeout(() => { window.location.href = data.redirect || '/login3/dashboard.php'; }, 600);
    } else {
      alert.className = 'alert alert-danger';
      alert.innerHTML = `<span>🔒</span> ${data.message || 'Invalid email or password'}`;
      alert.style.display = 'flex';
      btn.disabled = false;
      btnText.textContent = 'Sign In';
      spinner.style.display = 'none';
    }
  } catch (err) {
    alert.className = 'alert alert-danger';
    alert.innerHTML = '<span>⚠</span> Network error. Please try again.';
    alert.style.display = 'flex';
    btn.disabled = false;
    btnText.textContent = 'Sign In';
    spinner.style.display = 'none';
  }
});

// Focus email input on load
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('email')?.focus();
});
</script>
</body>
</html>
