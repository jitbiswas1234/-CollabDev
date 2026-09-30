<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser($pdo);

$project_id = intval($_GET['project_id'] ?? 0);
if (!$project_id || !hasProjectAccess($pdo, $project_id, $user['id'], 'viewer')) {
    redirect('/login3/dashboard.php');
}

$stmt = $pdo->prepare("SELECT p.*, u.username AS owner_name FROM projects p JOIN users u ON u.id=p.owner_id WHERE p.id=?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();
if (!$project) { redirect('/login3/dashboard.php'); }

$stmt = $pdo->prepare("SELECT role FROM project_members WHERE project_id=? AND user_id=?");
$stmt->execute([$project_id, $user['id']]);
$memberRow = $stmt->fetch();
$myRole = $memberRow['role'] ?? 'viewer';
$canEdit = in_array($myRole, ['owner','collaborator']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Git History — <?= sanitize($project['name']) ?> — CollabDev</title>
  <link rel="stylesheet" href="/login3/assets/css/app.css">
  <style>
    .page-wrapper { padding-top: calc(var(--topbar-height) + 2rem); max-width: 960px; margin: 0 auto; padding-bottom: 4rem; }
    .branch-selector { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
    .branch-pill {
      padding:.25rem .75rem; border-radius:var(--radius-full); font-size:.82rem;
      cursor:pointer; border:1px solid var(--border-muted); background:transparent;
      color:var(--text-secondary); transition:all var(--transition);
    }
    .branch-pill.active,.branch-pill:hover { background:rgba(63,185,80,.1); border-color:rgba(63,185,80,.4); color:var(--accent-green); }
    .commit-controls { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; }
  </style>
</head>
<body>

<!-- Topbar -->
<nav class="topbar">
  <div class="topbar-left">
    <a href="/login3/" class="logo"><div class="logo-icon">⚡</div></a>
    <div class="breadcrumb">
      <span class="breadcrumb-sep">/</span>
      <a href="/login3/dashboard.php">Projects</a>
      <span class="breadcrumb-sep">/</span>
      <a href="/login3/project.php?id=<?= $project_id ?>"><?= sanitize($project['name']) ?></a>
      <span class="breadcrumb-sep">/</span>
      <span class="current">Git History</span>
    </div>
  </div>
  <div class="topbar-right">
    <a href="/login3/project.php?id=<?= $project_id ?>" class="btn btn-secondary btn-sm">← Back to Editor</a>
    <div class="avatar avatar-sm" style="background:<?= sanitize($user['avatar_color']??'#58a6ff') ?>"><?= getUserInitials($user['username']) ?></div>
  </div>
</nav>

<div class="page-wrapper" style="padding-left:1.5rem;padding-right:1.5rem">

  <div class="page-header">
    <div>
      <h1 class="page-title">🌿 Git History</h1>
      <p class="page-subtitle"><?= sanitize($project['name']) ?> · Commit log &amp; diff viewer</p>
    </div>
    <?php if ($canEdit): ?>
    <button class="btn btn-primary" id="create-commit-btn" onclick="Modal.open('quick-commit-modal')">
      🌿 New Commit
    </button>
    <?php endif; ?>
  </div>

  <!-- Branch selector -->
  <div class="card" style="margin-bottom:1.5rem;padding:1rem 1.25rem">
    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
      <span style="font-size:.85rem;color:var(--text-muted);font-weight:500">Branch:</span>
      <div class="branch-selector" id="branch-selector">
        <button class="branch-pill active" data-branch="main" onclick="setBranch('main', this)">🌿 main</button>
        <button class="branch-pill" data-branch="dev" onclick="setBranch('dev', this)">🌱 dev</button>
        <button class="branch-pill" data-branch="feature" onclick="setBranch('feature', this)">✨ feature</button>
      </div>
      <div style="margin-left:auto;display:flex;gap:.5rem;align-items:center">
        <span style="font-size:.8rem;color:var(--text-muted)" id="commit-count">Loading...</span>
        <button class="btn btn-ghost btn-sm" onclick="loadCommits()">↻ Refresh</button>
      </div>
    </div>
  </div>

  <!-- Timeline -->
  <div id="commits-container">
    <div class="git-timeline" id="skeleton-timeline">
      <?php for ($i = 0; $i < 5; $i++): ?>
      <div class="git-commit">
        <div class="git-commit-card" style="display:flex;flex-direction:column;gap:.75rem">
          <div class="skeleton" style="height:15px;width:60%;border-radius:4px"></div>
          <div class="skeleton" style="height:12px;width:40%;border-radius:4px"></div>
          <div class="skeleton" style="height:12px;width:30%;border-radius:4px"></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
    <div class="git-timeline" id="commits-timeline" style="display:none"></div>
    <div id="empty-commits" style="display:none">
      <div class="empty-state">
        <div class="empty-state-icon">🌿</div>
        <h3>No commits yet</h3>
        <p>Make your first commit to start tracking your code history.</p>
        <?php if ($canEdit): ?>
        <button class="btn btn-primary" onclick="Modal.open('quick-commit-modal')">🌿 Make First Commit</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Diff Modal -->
<div class="modal-overlay" id="diff-modal">
  <div class="modal" style="max-width:800px;width:100%">
    <div class="modal-header">
      <h3 id="diff-modal-title">📄 Diff View</h3>
      <button class="btn-close" onclick="Modal.close('diff-modal')">✕</button>
    </div>
    <div class="modal-body" style="padding:0">
      <div id="diff-content" style="max-height:500px;overflow-y:auto;font-family:var(--font-mono);font-size:.82rem;line-height:1.7">
        <div style="padding:1.5rem;text-align:center;color:var(--text-muted)">Loading diff...</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('diff-modal')">Close</button>
      <button class="btn btn-primary" id="restore-btn" style="display:none">↩ Restore This Version</button>
    </div>
  </div>
</div>

<!-- Quick Commit Modal -->
<div class="modal-overlay" id="quick-commit-modal">
  <div class="modal">
    <div class="modal-header">
      <h3>🌿 New Commit</h3>
      <button class="btn-close" onclick="Modal.close('quick-commit-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="alert alert-info" style="margin-bottom:1rem;font-size:.82rem">
        <span>ℹ</span> Open the project editor and commit from there to include current file content.
      </div>
      <div class="form-group">
        <label class="form-label">Commit message</label>
        <input type="text" class="form-control" id="quick-commit-msg" placeholder='docs: update readme'>
      </div>
      <div class="form-group">
        <label class="form-label">Branch</label>
        <input type="text" class="form-control" id="quick-commit-branch" value="main">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('quick-commit-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="quickCommit()">Commit</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toast-container"></div>
<script src="/login3/assets/js/app.js"></script>
<script>
const PROJECT_ID = <?= (int)$project_id ?>;
const CAN_EDIT   = <?= $canEdit ? 'true' : 'false' ?>;
let currentBranch = 'main';
let commits = [];

function setBranch(branch, el) {
  currentBranch = branch;
  document.querySelectorAll('.branch-pill').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  loadCommits();
}

async function loadCommits() {
  document.getElementById('skeleton-timeline').style.display = 'block';
  document.getElementById('commits-timeline').style.display = 'none';
  document.getElementById('empty-commits').style.display = 'none';

  try {
    const data = await API.get('/login3/api/git.php', {
      action: 'log',
      project_id: PROJECT_ID,
      branch: currentBranch,
    });
    commits = data.commits || [];
    renderCommits();
  } catch (e) {
    Toast.error('Failed to load commits');
  }
}

function renderCommits() {
  const timeline = document.getElementById('commits-timeline');
  const skeleton = document.getElementById('skeleton-timeline');
  const empty    = document.getElementById('empty-commits');
  const count    = document.getElementById('commit-count');

  skeleton.style.display = 'none';
  count.textContent = `${commits.length} commit${commits.length !== 1 ? 's' : ''}`;

  if (commits.length === 0) {
    empty.style.display = 'block';
    timeline.style.display = 'none';
    return;
  }

  empty.style.display = 'none';
  timeline.style.display = 'block';
  timeline.innerHTML = commits.map((c, i) => `
    <div class="git-commit animate-fade" style="animation-delay:${i*0.04}s">
      <div class="git-commit-card">
        <div class="git-commit-header">
          <div style="display:flex;align-items:center;gap:.6rem;flex:1">
            <div class="avatar avatar-sm" style="background:${c.avatar_color||'#58a6ff'}">${c.initials||'?'}</div>
            <span class="git-commit-msg">${escapeHtml(c.message)}</span>
          </div>
          <span class="git-commit-hash">${c.short_id || c.id.toString().slice(0,7)}</span>
        </div>
        <div class="git-commit-meta">
          <span>👤 ${escapeHtml(c.username||'Unknown')}</span>
          <span>🕐 ${c.time_ago||'recently'}</span>
          ${c.file_name ? `<span>📄 ${escapeHtml(c.file_name)}</span>` : ''}
          <span class="badge badge-green">🌿 ${escapeHtml(c.branch||'main')}</span>
        </div>
        <div class="commit-controls" style="margin-top:.6rem">
          <button class="btn btn-secondary btn-sm" onclick="viewDiff(${c.id},'${escapeHtml(c.message).replace(/'/g,"\\'")}')">
            📊 View Diff
          </button>
          <button class="btn btn-secondary btn-sm" onclick="viewSnapshot(${c.id})">
            👁 Show Code
          </button>
          ${CAN_EDIT ? `<button class="btn btn-secondary btn-sm" onclick="restoreCommit(${c.id},'${escapeHtml(c.message).replace(/'/g,"\\'")}')">↩ Restore</button>` : ''}
          ${i < commits.length-1 ? `<button class="btn btn-ghost btn-sm" onclick="compareToPrev(${c.id},${commits[i+1]?.id})">🔄 Compare to prev</button>` : ''}
        </div>
      </div>
    </div>
  `).join('');
}

async function viewDiff(commitId, msg) {
  document.getElementById('diff-modal-title').textContent = `📊 Diff: ${msg}`;
  document.getElementById('diff-content').innerHTML = '<div style="padding:1.5rem;text-align:center;color:var(--text-muted)"><div class="spinner"></div></div>';
  document.getElementById('restore-btn').style.display = CAN_EDIT ? 'inline-flex' : 'none';
  document.getElementById('restore-btn').onclick = () => restoreCommit(commitId, msg);
  Modal.open('diff-modal');

  try {
    const data = await API.get('/login3/api/git.php', { action: 'diff', commit_id: commitId });
    renderDiff(data.diff || [], data.additions||0, data.deletions||0);
  } catch (e) {
    document.getElementById('diff-content').innerHTML = `<div style="padding:1.5rem;color:var(--accent-red)">Failed to load diff: ${e.message}</div>`;
  }
}

async function viewSnapshot(commitId) {
  document.getElementById('diff-modal-title').textContent = '👁 Code Snapshot';
  document.getElementById('diff-content').innerHTML = '<div style="padding:1.5rem;text-align:center"><div class="spinner"></div></div>';
  document.getElementById('restore-btn').style.display = CAN_EDIT ? 'inline-flex' : 'none';
  document.getElementById('restore-btn').onclick = () => restoreCommit(commitId, '');
  Modal.open('diff-modal');

  try {
    const data = await API.get('/login3/api/git.php', { action: 'show', commit_id: commitId });
    const content = data.commit?.content_snapshot || '';
    document.getElementById('diff-content').innerHTML = `
      <pre style="margin:0;padding:1.25rem;white-space:pre-wrap;word-break:break-all;color:var(--text-primary);line-height:1.7;font-size:.82rem">${escapeHtml(content)}</pre>
    `;
  } catch (e) {
    document.getElementById('diff-content').innerHTML = `<div style="padding:1.5rem;color:var(--accent-red)">Failed: ${e.message}</div>`;
  }
}

async function compareToPrev(commitId, prevId) {
  if (!prevId) { Toast.info('No previous commit to compare'); return; }
  document.getElementById('diff-modal-title').textContent = '🔄 Compare Commits';
  document.getElementById('diff-content').innerHTML = '<div style="padding:1.5rem;text-align:center"><div class="spinner"></div></div>';
  document.getElementById('restore-btn').style.display = 'none';
  Modal.open('diff-modal');

  try {
    const data = await API.get('/login3/api/git.php', { action: 'diff', commit_id: commitId, prev_commit_id: prevId });
    renderDiff(data.diff || [], data.additions||0, data.deletions||0);
  } catch (e) {
    document.getElementById('diff-content').innerHTML = `<div style="padding:1.5rem;color:var(--accent-red)">Failed: ${e.message}</div>`;
  }
}

function renderDiff(lines, additions, deletions) {
  const stats = `
    <div style="padding:.75rem 1.25rem;border-bottom:1px solid var(--border-muted);display:flex;gap:1rem;font-size:.82rem">
      <span style="color:var(--accent-green)">＋${additions} addition${additions!==1?'s':''}</span>
      <span style="color:var(--accent-red)">－${deletions} deletion${deletions!==1?'s':''}</span>
    </div>
  `;
  if (lines.length === 0) {
    document.getElementById('diff-content').innerHTML = stats + '<div style="padding:1.25rem;color:var(--text-muted);text-align:center">No changes to display</div>';
    return;
  }
  const rows = lines.map(l => {
    const cls = l.type === 'add' ? 'added' : l.type === 'remove' ? 'removed' : '';
    const marker = l.type === 'add' ? '+' : l.type === 'remove' ? '−' : ' ';
    const lineNum = l.type === 'remove' ? (l.line_no_old||'') : (l.line_no_new||'');
    return `<tr class="${cls}">
      <td class="line-num">${lineNum}</td>
      <td class="line-marker" style="color:${l.type==='add'?'var(--accent-green)':l.type==='remove'?'var(--accent-red)':'var(--text-muted)'}">${marker}</td>
      <td class="line-code">${escapeHtml(l.line||'')}</td>
    </tr>`;
  }).join('');
  document.getElementById('diff-content').innerHTML = stats + `
    <table class="diff-table" style="width:100%;border-collapse:collapse">
      <colgroup><col style="width:50px"><col style="width:20px"><col></colgroup>
      <tbody>${rows}</tbody>
    </table>`;
}

async function restoreCommit(commitId, msg) {
  confirmAction(`Restore to commit "${msg}"? The current file content will be overwritten.`, async () => {
    try {
      await API.post('/login3/api/git.php', { action: 'restore', commit_id: commitId });
      Toast.success('File restored to this version!');
      Modal.close('diff-modal');
    } catch (e) {
      Toast.error(e.message || 'Restore failed');
    }
  });
}

async function quickCommit() {
  const msg    = document.getElementById('quick-commit-msg').value.trim();
  const branch = document.getElementById('quick-commit-branch').value.trim() || 'main';
  if (!msg) { Toast.error('Enter a commit message'); return; }
  try {
    await API.post('/login3/api/git.php', { action: 'commit', project_id: PROJECT_ID, message: msg, branch });
    Toast.success('Committed!');
    Modal.close('quick-commit-modal');
    loadCommits();
  } catch (e) {
    Toast.error(e.message || 'Commit failed');
  }
}

function escapeHtml(str) {
  if (!str) return '';
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', loadCommits);
</script>
</body>
</html>
