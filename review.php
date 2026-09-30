<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser($pdo);

$project_id = intval($_GET['project_id'] ?? 0);
$review_id  = intval($_GET['id'] ?? 0);

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
  <title>Code Reviews — <?= sanitize($project['name']) ?> — CollabDev</title>
  <link rel="stylesheet" href="/login3/assets/css/app.css">
  <style>
    .page-wrapper { padding-top: calc(var(--topbar-height) + 2rem); max-width: 1100px; margin: 0 auto; padding-bottom: 4rem; padding-left: 1.5rem; padding-right: 1.5rem; }
    .review-item {
      background: var(--bg-secondary);
      border: 1px solid var(--border-muted);
      border-radius: var(--radius-xl);
      padding: 1.25rem;
      cursor: pointer;
      transition: all var(--transition-slow);
      text-decoration: none;
      color: inherit;
      display: block;
      margin-bottom: .75rem;
    }
    .review-item:hover { border-color: var(--accent-blue); transform: translateY(-2px); color: inherit; box-shadow: var(--shadow-sm); }
    .review-item-header { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .status-icon-open            { color: var(--accent-green); }
    .status-icon-approved        { color: var(--accent-blue); }
    .status-icon-changes_requested{ color: var(--accent-yellow); }
    .status-icon-closed          { color: var(--text-muted); }

    /* Full review detail */
    .review-detail { display:none; }
    .review-detail.active { display:block; }
    .review-list { display:block; }
    .review-list.hidden { display:none; }

    /* Comment card */
    .comment-card {
      background: var(--bg-tertiary);
      border: 1px solid var(--border-muted);
      border-radius: var(--radius-lg);
      padding: 1rem;
      margin-bottom: .75rem;
      animation: slideUp .25s ease;
    }
    .comment-header { display:flex;align-items:center;gap:.5rem;margin-bottom:.4rem; }
    .comment-body { font-size:.875rem; line-height:1.6; }
    .line-ref { font-family:var(--font-mono); font-size:.75rem; background:var(--bg-elevated); padding:.1rem .5rem; border-radius:var(--radius-full); color:var(--accent-blue); }
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
      <span class="current">Code Reviews</span>
    </div>
  </div>
  <div class="topbar-right">
    <a href="/login3/project.php?id=<?= $project_id ?>" class="btn btn-secondary btn-sm">← Editor</a>
    <a href="/login3/git_log.php?project_id=<?= $project_id ?>" class="btn btn-secondary btn-sm">🌿 History</a>
    <div class="avatar avatar-sm" style="background:<?= sanitize($user['avatar_color']??'#58a6ff') ?>"><?= getUserInitials($user['username']) ?></div>
  </div>
</nav>

<div class="page-wrapper">

  <!-- List view -->
  <div id="review-list-view" class="review-list">
    <div class="page-header">
      <div>
        <h1 class="page-title">🔍 Code Reviews</h1>
        <p class="page-subtitle"><?= sanitize($project['name']) ?> · Pull requests &amp; review workflow</p>
      </div>
      <?php if ($canEdit): ?>
      <button class="btn btn-blue" onclick="Modal.open('new-review-modal')" id="new-review-btn">＋ New Review</button>
      <?php endif; ?>
    </div>

    <!-- Status filter tabs -->
    <div class="tabs" id="review-tabs" style="margin-bottom:1.5rem">
      <div class="tab-item active" data-tab-group="reviews" data-tab="all" onclick="filterReviews('all', this)">All</div>
      <div class="tab-item" data-tab-group="reviews" data-tab="open" onclick="filterReviews('open', this)">🟢 Open</div>
      <div class="tab-item" data-tab-group="reviews" data-tab="approved" onclick="filterReviews('approved', this)">✅ Approved</div>
      <div class="tab-item" data-tab-group="reviews" data-tab="changes_requested" onclick="filterReviews('changes_requested', this)">⚠ Changes Requested</div>
      <div class="tab-item" data-tab-group="reviews" data-tab="closed" onclick="filterReviews('closed', this)">✕ Closed</div>
    </div>

    <div id="reviews-container">
      <!-- Skeletons -->
      <div id="reviews-skeleton">
        <?php for ($i=0;$i<4;$i++): ?>
        <div class="card" style="padding:1.25rem;margin-bottom:.75rem;display:flex;flex-direction:column;gap:.75rem">
          <div class="skeleton" style="height:15px;width:50%;border-radius:4px"></div>
          <div class="skeleton" style="height:12px;width:30%;border-radius:4px"></div>
          <div class="skeleton" style="height:12px;width:70%;border-radius:4px"></div>
        </div>
        <?php endfor; ?>
      </div>
      <div id="reviews-list" style="display:none"></div>
      <div id="reviews-empty" style="display:none">
        <div class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <h3>No reviews yet</h3>
          <p>Create a code review to get feedback on your commits.</p>
          <?php if ($canEdit): ?>
          <button class="btn btn-blue" onclick="Modal.open('new-review-modal')">＋ New Review</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail view -->
  <div id="review-detail-view" class="review-detail">
    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem">
      <button class="btn btn-secondary btn-sm" onclick="showList()">← All Reviews</button>
      <div id="detail-status-badge"></div>
    </div>

    <div class="review-grid">
      <!-- Left: Diff + Comments -->
      <div>
        <div class="card" style="margin-bottom:1.25rem;padding:1.5rem">
          <h2 id="detail-title" style="font-size:1.2rem;margin-bottom:.4rem">Loading...</h2>
          <div id="detail-meta" style="font-size:.82rem;color:var(--text-muted);margin-bottom:.75rem"></div>
          <p id="detail-desc" style="color:var(--text-secondary);font-size:.9rem"></p>
        </div>

        <!-- Code snapshot -->
        <div class="card" style="margin-bottom:1.25rem;overflow:hidden">
          <div class="diff-file-header">
            <span id="detail-file-path">📄 Code Snapshot</span>
            <span id="detail-commit-hash" class="git-commit-hash"></span>
          </div>
          <div id="detail-code-snapshot" style="max-height:350px;overflow-y:auto;padding:1rem;font-family:var(--font-mono);font-size:.82rem;line-height:1.7;white-space:pre-wrap;word-break:break-all;color:var(--text-primary);background:var(--bg-primary)">Loading...</div>
        </div>

        <!-- Comments -->
        <div style="margin-bottom:1.25rem">
          <h3 style="font-size:1rem;margin-bottom:.75rem">💬 Review Comments</h3>
          <div id="detail-comments">
            <div style="color:var(--text-muted);font-size:.875rem;padding:.75rem 0">Loading comments...</div>
          </div>
        </div>

        <!-- Add comment -->
        <div class="card" style="padding:1.25rem">
          <h4 style="font-size:.9rem;margin-bottom:.75rem">Add Comment</h4>
          <div class="form-group">
            <label class="form-label">Line number (optional)</label>
            <input type="number" class="form-control" id="comment-line" placeholder="e.g. 42" min="1" style="width:150px">
          </div>
          <div class="form-group">
            <label class="form-label">Comment <span class="required">*</span></label>
            <textarea class="form-control" id="comment-text" rows="3" placeholder="Your feedback..."></textarea>
          </div>
          <button class="btn btn-blue" onclick="addComment()" id="add-comment-btn">Post Comment</button>
        </div>
      </div>

      <!-- Right: Review sidebar -->
      <div class="review-sidebar">
        <!-- Status card -->
        <div class="review-status-card">
          <h4 style="font-size:.9rem;margin-bottom:1rem">Review Status</h4>
          <div id="current-status" style="margin-bottom:1rem"></div>
          <div id="status-actions" style="display:flex;flex-direction:column;gap:.5rem"></div>
        </div>

        <!-- Reviewers -->
        <div class="review-status-card">
          <h4 style="font-size:.9rem;margin-bottom:1rem">👤 Participants</h4>
          <div id="detail-participants" style="display:flex;flex-direction:column;gap:.5rem"></div>
        </div>

        <!-- Commit info -->
        <div class="review-status-card">
          <h4 style="font-size:.9rem;margin-bottom:.75rem">🌿 Commit Info</h4>
          <div id="detail-commit-info" style="font-size:.82rem;color:var(--text-secondary);line-height:1.8"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Review Modal -->
<div class="modal-overlay" id="new-review-modal">
  <div class="modal">
    <div class="modal-header">
      <h3>🔍 New Code Review</h3>
      <button class="btn-close" onclick="Modal.close('new-review-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div id="nr-alert" style="display:none;margin-bottom:1rem"></div>
      <div class="form-group">
        <label class="form-label">Title <span class="required">*</span></label>
        <input type="text" class="form-control" id="nr-title" placeholder="e.g. feat: add user authentication">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea class="form-control" id="nr-desc" rows="3" placeholder="Describe what changed and why..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Commit to review</label>
        <select class="form-control" id="nr-commit">
          <option value="">Loading commits...</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Reviewer (optional)</label>
        <select class="form-control" id="nr-reviewer">
          <option value="">Select reviewer...</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('new-review-modal')">Cancel</button>
      <button class="btn btn-blue" onclick="createReview()" id="create-review-btn">Create Review</button>
    </div>
  </div>
</div>

<!-- Confirm -->
<div class="modal-overlay" id="confirm-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header"><h3>Confirm</h3></div>
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
const PROJECT_ID   = <?= (int)$project_id ?>;
const CAN_EDIT     = <?= $canEdit ? 'true' : 'false' ?>;
const CURRENT_USER = <?= json_encode(['id'=>$user['id'],'username'=>$user['username'],'avatar_color'=>$user['avatar_color']]) ?>;

let allReviews     = [];
let currentFilter  = 'all';
let currentReviewId= null;

const statusInfo = {
  open:               { label:'Open', cls:'badge-green', icon:'🟢' },
  approved:           { label:'Approved', cls:'badge-blue', icon:'✅' },
  changes_requested:  { label:'Changes Requested', cls:'badge-yellow', icon:'⚠️' },
  closed:             { label:'Closed', cls:'badge-gray', icon:'✕' },
};

// ---- LOAD REVIEWS ----
async function loadReviews() {
  try {
    const data = await API.get('/login3/api/reviews.php', { action:'list', project_id: PROJECT_ID });
    allReviews = data.reviews || [];
    renderReviews();
    document.getElementById('reviews-skeleton').style.display = 'none';
  } catch(e) {
    Toast.error('Failed to load reviews');
    document.getElementById('reviews-skeleton').style.display = 'none';
  }
}

function filterReviews(status, el) {
  currentFilter = status;
  document.querySelectorAll('#review-tabs .tab-item').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  renderReviews();
}

function renderReviews() {
  const list  = document.getElementById('reviews-list');
  const empty = document.getElementById('reviews-empty');

  let filtered = allReviews.filter(r => currentFilter === 'all' || r.status === currentFilter);

  if (filtered.length === 0) {
    list.style.display = 'none';
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';
  list.style.display = 'block';
  list.innerHTML = filtered.map(r => {
    const si = statusInfo[r.status] || statusInfo.open;
    return `
    <div class="review-item" onclick="openReview(${r.id})">
      <div class="review-item-header">
        <span class="badge ${si.cls}">${si.icon} ${si.label}</span>
        <strong style="font-size:.95rem">${escapeHtml(r.title)}</strong>
        <span class="git-commit-hash" style="margin-left:auto">#${r.id}</span>
      </div>
      <div style="margin-top:.6rem;font-size:.82rem;color:var(--text-secondary);display:flex;gap:1rem;flex-wrap:wrap">
        <span>👤 by ${escapeHtml(r.author_name||'Unknown')}</span>
        <span>💬 ${r.comment_count||0} comment${r.comment_count!=1?'s':''}</span>
        <span>🕐 ${r.time_ago||'recently'}</span>
        ${r.commit_message ? `<span>🌿 ${escapeHtml(r.commit_message)}</span>` : ''}
      </div>
      ${r.description ? `<p style="margin-top:.5rem;font-size:.82rem;color:var(--text-muted)">${escapeHtml(r.description.substring(0,120))}${r.description.length>120?'…':''}</p>` : ''}
    </div>`;
  }).join('');
}

// ---- OPEN REVIEW ----
async function openReview(id) {
  currentReviewId = id;
  document.getElementById('review-list-view').style.display = 'none';
  document.getElementById('review-detail-view').style.display = 'block';

  // Reset
  document.getElementById('detail-title').textContent = 'Loading...';
  document.getElementById('detail-comments').innerHTML = '<div class="spinner" style="margin:1rem 0"></div>';

  try {
    const data = await API.get('/login3/api/reviews.php', { action:'get', id });
    const r = data.review;
    const comments = data.comments || [];

    // Header
    const si = statusInfo[r.status] || statusInfo.open;
    document.getElementById('detail-status-badge').innerHTML = `<span class="badge ${si.cls}">${si.icon} ${si.label}</span>`;
    document.getElementById('detail-title').textContent = r.title;
    document.getElementById('detail-meta').innerHTML = `👤 ${escapeHtml(r.author_name)} · 🕐 ${r.time_ago||'recently'} · #${r.id}`;
    document.getElementById('detail-desc').textContent = r.description || '';

    // Commit info
    document.getElementById('detail-commit-hash').textContent = r.short_id || '';
    document.getElementById('detail-commit-info').innerHTML = r.commit_message
      ? `<div>📝 ${escapeHtml(r.commit_message)}</div><div>🌿 ${escapeHtml(r.branch||'main')}</div>`
      : '<div style="color:var(--text-muted)">No commit linked</div>';

    // Code snapshot
    if (r.content_snapshot) {
      document.getElementById('detail-code-snapshot').textContent = r.content_snapshot;
    } else {
      document.getElementById('detail-code-snapshot').textContent = '(No code snapshot available)';
      document.getElementById('detail-code-snapshot').style.color = 'var(--text-muted)';
    }

    // Participants
    let participants = `<div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem">
      <div class="avatar avatar-sm" style="background:${r.author_color||'#58a6ff'}">${(r.author_name||'?').slice(0,2).toUpperCase()}</div>
      <span>${escapeHtml(r.author_name||'Author')}</span> <span class="badge badge-gray">author</span>
    </div>`;
    if (r.reviewer_name) {
      participants += `<div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;margin-top:.5rem">
        <div class="avatar avatar-sm" style="background:${r.reviewer_color||'#3fb950'}">${(r.reviewer_name||'?').slice(0,2).toUpperCase()}</div>
        <span>${escapeHtml(r.reviewer_name)}</span> <span class="badge badge-blue">reviewer</span>
      </div>`;
    }
    document.getElementById('detail-participants').innerHTML = participants;

    // Status actions
    renderStatusActions(r.status, r.id, r.author_id, r.reviewer_id);

    // Comments
    renderComments(comments);

  } catch(e) {
    Toast.error('Failed to load review: ' + e.message);
  }
}

function renderComments(comments) {
  const container = document.getElementById('detail-comments');
  if (comments.length === 0) {
    container.innerHTML = '<div style="color:var(--text-muted);font-size:.875rem;padding:.5rem 0">No comments yet. Be the first to leave feedback!</div>';
    return;
  }
  container.innerHTML = comments.map(c => `
    <div class="comment-card">
      <div class="comment-header">
        <div class="avatar avatar-sm" style="background:${c.avatar_color||'#58a6ff'}">${(c.username||'?').slice(0,2).toUpperCase()}</div>
        <strong style="font-size:.85rem">${escapeHtml(c.username)}</strong>
        ${c.line_number ? `<span class="line-ref">Line ${c.line_number}</span>` : ''}
        <span style="margin-left:auto;font-size:.75rem;color:var(--text-muted)">${c.time_ago||'recently'}</span>
      </div>
      <div class="comment-body">${escapeHtml(c.content)}</div>
    </div>
  `).join('');
}

function renderStatusActions(status, reviewId, authorId, reviewerId) {
  const si = statusInfo[status] || statusInfo.open;
  document.getElementById('current-status').innerHTML = `<span class="badge ${si.cls}" style="font-size:.9rem">${si.icon} ${si.label}</span>`;

  const isAuthor   = CURRENT_USER.id == authorId;
  const isReviewer = CURRENT_USER.id == reviewerId;
  const isOwnerRole= CAN_EDIT;

  let actions = '';
  if (status === 'open' && (isReviewer || isOwnerRole)) {
    actions += `<button class="btn btn-primary btn-sm" onclick="updateStatus('approved')">✅ Approve</button>`;
    actions += `<button class="btn btn-secondary btn-sm" onclick="updateStatus('changes_requested')">⚠️ Request Changes</button>`;
  }
  if (status !== 'closed' && (isAuthor || isOwnerRole)) {
    actions += `<button class="btn btn-secondary btn-sm" onclick="updateStatus('closed')">✕ Close Review</button>`;
  }
  if (status === 'changes_requested' && (isAuthor || isOwnerRole)) {
    actions += `<button class="btn btn-blue btn-sm" onclick="updateStatus('open')">↩ Reopen</button>`;
  }

  document.getElementById('status-actions').innerHTML = actions || '<div style="font-size:.82rem;color:var(--text-muted)">No actions available</div>';
}

async function updateStatus(status) {
  try {
    await API.post('/login3/api/reviews.php', { action:'update_status', review_id: currentReviewId, status });
    Toast.success('Status updated');
    openReview(currentReviewId);
    loadReviews(); // refresh list
  } catch(e) {
    Toast.error(e.message || 'Failed to update status');
  }
}

async function addComment() {
  const line    = document.getElementById('comment-line').value.trim();
  const content = document.getElementById('comment-text').value.trim();
  const btn     = document.getElementById('add-comment-btn');

  if (!content) { Toast.error('Enter a comment'); return; }

  btn.disabled = true;
  btn.textContent = 'Posting...';

  try {
    await API.post('/login3/api/reviews.php', {
      action: 'comment',
      review_id: currentReviewId,
      line_number: line ? parseInt(line) : null,
      content,
    });
    document.getElementById('comment-text').value = '';
    document.getElementById('comment-line').value = '';
    Toast.success('Comment posted');
    openReview(currentReviewId); // refresh
  } catch(e) {
    Toast.error(e.message || 'Failed to post comment');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Post Comment';
  }
}

function showList() {
  document.getElementById('review-list-view').style.display = 'block';
  document.getElementById('review-detail-view').style.display = 'none';
  currentReviewId = null;
}

// ---- NEW REVIEW ----
async function loadNewReviewForm() {
  // Load commits
  try {
    const cd = await API.get('/login3/api/git.php', { action:'log', project_id: PROJECT_ID });
    const sel = document.getElementById('nr-commit');
    const commits = cd.commits || [];
    sel.innerHTML = commits.length
      ? commits.map(c => `<option value="${c.id}">[${c.short_id}] ${escapeHtml(c.message)} (${c.time_ago})</option>`).join('')
      : '<option value="">No commits yet</option>';
  } catch(e) {}

  // Load members
  try {
    const md = await API.get('/login3/api/members.php', { action:'list', project_id: PROJECT_ID });
    const sel = document.getElementById('nr-reviewer');
    sel.innerHTML = '<option value="">No reviewer (self-review)</option>' +
      (md.members||[]).filter(m => m.user_id != CURRENT_USER.id).map(m =>
        `<option value="${m.user_id}">${escapeHtml(m.username)}</option>`
      ).join('');
  } catch(e) {}
}

async function createReview() {
  const title    = document.getElementById('nr-title').value.trim();
  const desc     = document.getElementById('nr-desc').value.trim();
  const commitId = document.getElementById('nr-commit').value;
  const reviewer = document.getElementById('nr-reviewer').value;
  const alertEl  = document.getElementById('nr-alert');
  const btn      = document.getElementById('create-review-btn');

  alertEl.style.display = 'none';
  if (!title) {
    alertEl.className = 'alert alert-danger';
    alertEl.innerHTML = '<span>⚠</span> Title is required';
    alertEl.style.display = 'flex';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Creating...';

  try {
    const data = await API.post('/login3/api/reviews.php', {
      action: 'create',
      project_id: PROJECT_ID,
      commit_id: commitId || null,
      title, description: desc,
      reviewer_id: reviewer || null,
    });
    Toast.success('Review created!');
    Modal.close('new-review-modal');
    document.getElementById('nr-title').value = '';
    document.getElementById('nr-desc').value = '';
    await loadReviews();
    if (data.review_id) openReview(data.review_id);
  } catch(e) {
    alertEl.className = 'alert alert-danger';
    alertEl.innerHTML = `<span>⚠</span> ${e.message||'Failed to create review'}`;
    alertEl.style.display = 'flex';
  } finally {
    btn.disabled = false;
    btn.textContent = 'Create Review';
  }
}

function escapeHtml(str) {
  if (!str) return '';
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
  loadReviews();

  // Load form data when modal opens
  const observer = new MutationObserver(() => {
    const modal = document.getElementById('new-review-modal');
    if (modal && modal.classList.contains('active')) loadNewReviewForm();
  });
  observer.observe(document.getElementById('new-review-modal'), { attributes: true, attributeFilter: ['class'] });
});
</script>
</body>
</html>
