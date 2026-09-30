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
  <title>Create Account — CollabDev</title>
  <meta name="description" content="Create your free CollabDev account and start collaborating with your development team.">
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
      max-width: 440px;
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

    /* Progress strength meter */
    .progress-bar {
      background: rgba(255, 255, 255, 0.05);
      border-radius: var(--radius-full);
      overflow: hidden;
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
    
    <h2>Create account</h2>
    <p class="subtitle">Join developers already collaborating in real-time</p>

    <!-- Alert area -->
    <div id="register-alert" style="display:none;margin-bottom:1.25rem"></div>

    <form id="register-form" novalidate>
      <div class="form-group">
        <label class="form-label" for="username">Username <span class="required">*</span></label>
        <input type="text" class="form-control" id="username" name="username"
               placeholder="johndoe" autocomplete="username" required
               minlength="3" maxlength="50"
               oninput="checkUsername(this.value)">
        <div class="form-error" id="username-error" style="display:none"></div>
        <div class="form-hint">At least 3 characters, letters and numbers only</div>
      </div>

      <div class="form-group">
        <label class="form-label" for="reg-email">Email address <span class="required">*</span></label>
        <input type="email" class="form-control" id="reg-email" name="email"
               placeholder="you@example.com" autocomplete="email" required>
        <div class="form-error" id="email-error" style="display:none"></div>
      </div>

      <div class="form-group">
        <label class="form-label" for="reg-password">Password <span class="required">*</span></label>
        <div class="input-group">
          <input type="password" class="form-control" id="reg-password" name="password"
                 placeholder="Min. 6 characters" autocomplete="new-password" required
                 minlength="6" oninput="checkPassword(this.value)">
          <button type="button" class="btn btn-secondary" id="toggle-pass" style="border-left:none;padding:0 0.75rem">👁</button>
        </div>
        <!-- Password strength bar -->
        <div style="margin-top:0.35rem">
          <div class="progress-bar" style="height:3px">
            <div class="progress-fill" id="pass-strength-bar" style="width:0%;height:100%;transition:width 0.3s,background 0.3s;border-radius:2px"></div>
          </div>
          <div id="pass-strength-text" style="font-size:0.72rem;color:var(--text-muted);margin-top:0.2rem"></div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm-password">Confirm password <span class="required">*</span></label>
        <input type="password" class="form-control" id="confirm-password" name="confirm_password"
               placeholder="Repeat password" autocomplete="new-password" required>
        <div class="form-error" id="confirm-error" style="display:none"></div>
      </div>

      <!-- Avatar color picker -->
      <div class="form-group" style="margin-bottom:1.5rem">
        <label class="form-label">Profile color</label>
        <div style="display:flex;gap:0.6rem;flex-wrap:wrap" id="color-picker">
          <?php
          $colors = ['#58a6ff','#3fb950','#f78166','#bc8cff','#ffa657','#e3b341','#39d353','#ff7b72'];
          foreach ($colors as $i => $c):
          ?>
          <div class="color-option" data-color="<?= $c ?>"
               style="width:28px;height:28px;border-radius:50%;background:<?= $c ?>;cursor:pointer;border:2px solid transparent;transition:all 0.2s;box-shadow:0 0 5px rgba(0,0,0,0.5);<?= $i===0?'border-color:#fff;transform:scale(1.15)':'' ?>"
               onclick="selectColor('<?= $c ?>', this)"></div>
          <?php endforeach; ?>
        </div>
        <input type="hidden" id="avatar-color" name="avatar_color" value="#58a6ff">
      </div>

      <button type="submit" class="btn btn-blue w-full" id="register-btn" style="margin-top:0.5rem">
        <span id="register-btn-text">Create Account</span>
        <span id="register-spinner" class="spinner" style="display:none;width:16px;height:16px;border-width:2px"></span>
      </button>

      <p style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:0.9rem">
        By creating an account, you agree to our Terms of Service.
      </p>
    </form>

    <p style="text-align:center;margin-top:1.5rem;font-size:0.875rem;color:var(--text-secondary)">
      Already have an account?
      <a href="/login3/login.php" style="font-weight:500">Sign in</a>
    </p>
  </div>
</div>

<div class="toast-container" id="toast-container"></div>
<script src="/login3/assets/js/app.js"></script>
<script>
function selectColor(color, el) {
  document.querySelectorAll('.color-option').forEach(o => {
    o.style.borderColor = 'transparent';
    o.style.transform = 'scale(1)';
  });
  el.style.borderColor = '#fff';
  el.style.transform = 'scale(1.15)';
  document.getElementById('avatar-color').value = color;
}

function checkUsername(val) {
  const err = document.getElementById('username-error');
  const inp = document.getElementById('username');
  if (val.length > 0 && val.length < 3) {
    err.textContent = 'Username must be at least 3 characters';
    err.style.display = 'block';
    inp.classList.add('is-invalid');
  } else if (val.length > 0 && !/^[a-zA-Z0-9_]+$/.test(val)) {
    err.textContent = 'Only letters, numbers, and underscores allowed';
    err.style.display = 'block';
    inp.classList.add('is-invalid');
  } else {
    err.style.display = 'none';
    inp.classList.remove('is-invalid');
  }
}

function checkPassword(val) {
  const bar  = document.getElementById('pass-strength-bar');
  const text = document.getElementById('pass-strength-text');
  let score = 0;
  if (val.length >= 6) score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^a-zA-Z0-9]/.test(val)) score++;

  const levels = [
    { pct:'0%', color:'', label:'' },
    { pct:'20%', color:'var(--accent-red)', label:'Very weak' },
    { pct:'40%', color:'var(--accent-orange)', label:'Weak' },
    { pct:'60%', color:'var(--accent-yellow)', label:'Fair' },
    { pct:'80%', color:'var(--accent-blue)', label:'Good' },
    { pct:'100%', color:'var(--accent-green)', label:'Strong 💪' },
  ];
  const lvl = levels[Math.min(score, 5)];
  bar.style.width = val ? lvl.pct : '0%';
  bar.style.background = lvl.color;
  text.textContent = val ? lvl.label : '';
  text.style.color = lvl.color;
}

document.getElementById('toggle-pass').addEventListener('click', function() {
  const inp = document.getElementById('reg-password');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  this.textContent = inp.type === 'password' ? '👁' : '🙈';
});

document.getElementById('register-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const username    = document.getElementById('username').value.trim();
  const email       = document.getElementById('reg-email').value.trim();
  const password    = document.getElementById('reg-password').value;
  const confirm     = document.getElementById('confirm-password').value;
  const avatar_color= document.getElementById('avatar-color').value;
  const alertBox    = document.getElementById('register-alert');
  const btn         = document.getElementById('register-btn');
  const btnText     = document.getElementById('register-btn-text');
  const spinner     = document.getElementById('register-spinner');

  alertBox.style.display = 'none';

  // Validate
  let valid = true;
  const setErr = (id, msg) => {
    document.getElementById(id).textContent = msg;
    document.getElementById(id).style.display = msg ? 'block' : 'none';
  };

  setErr('username-error', '');
  setErr('email-error', '');
  setErr('confirm-error', '');

  if (!username || username.length < 3) {
    setErr('username-error', 'Username must be at least 3 characters');
    document.getElementById('username').classList.add('is-invalid');
    valid = false;
  }
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    setErr('email-error', 'Enter a valid email address');
    document.getElementById('reg-email').classList.add('is-invalid');
    valid = false;
  }
  if (!password || password.length < 6) {
    alertBox.className = 'alert alert-danger';
    alertBox.innerHTML = '<span>⚠</span> Password must be at least 6 characters';
    alertBox.style.display = 'flex';
    valid = false;
  }
  if (password !== confirm) {
    setErr('confirm-error', 'Passwords do not match');
    document.getElementById('confirm-password').classList.add('is-invalid');
    valid = false;
  }
  if (!valid) return;

  btn.disabled = true;
  btnText.textContent = 'Creating account...';
  spinner.style.display = 'inline-block';

  try {
    const res = await fetch('/login3/api/auth.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'register', username, email, password, avatar_color }),
    });
    const data = await res.json();

    if (data.success) {
      btnText.textContent = '✓ Account created!';
      Toast.success('Account created! Redirecting...');
      setTimeout(() => { window.location.href = data.redirect || '/login3/dashboard.php'; }, 800);
    } else {
      alertBox.className = 'alert alert-danger';
      alertBox.innerHTML = `<span>⚠</span> ${data.message || 'Registration failed'}`;
      alertBox.style.display = 'flex';
      btn.disabled = false;
      btnText.textContent = 'Create Account';
      spinner.style.display = 'none';
    }
  } catch (err) {
    alertBox.className = 'alert alert-danger';
    alertBox.innerHTML = '<span>⚠</span> Network error. Please try again.';
    alertBox.style.display = 'flex';
    btn.disabled = false;
    btnText.textContent = 'Create Account';
    spinner.style.display = 'none';
  }
});
</script>
</body>
</html>
