<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser($pdo);

$project_id = intval($_GET['id'] ?? 0);
if (!$project_id) { redirect('/login3/dashboard.php'); }

// Check access
if (!hasProjectAccess($pdo, $project_id, $user['id'], 'viewer')) {
    redirect('/login3/dashboard.php');
}

// Load project
$stmt = $pdo->prepare("SELECT p.*, u.username AS owner_name FROM projects p JOIN users u ON u.id=p.owner_id WHERE p.id=?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();
if (!$project) { redirect('/login3/dashboard.php'); }

// User's role
$stmt = $pdo->prepare("SELECT role FROM project_members WHERE project_id=? AND user_id=?");
$stmt->execute([$project_id, $user['id']]);
$memberRow = $stmt->fetch();
$myRole = $memberRow['role'] ?? 'viewer';
$canEdit = in_array($myRole, ['owner','collaborator']);

// First file
$stmt = $pdo->prepare("SELECT id, name, language, content FROM files WHERE project_id=? ORDER BY created_at ASC LIMIT 1");
$stmt->execute([$project_id]);
$firstFile = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($project['name']) ?> — CollabDev</title>
  <meta name="description" content="Collaborating on <?= sanitize($project['name']) ?> with your team on CollabDev.">
  <link rel="stylesheet" href="/login3/assets/css/app.css">
  <!-- CodeMirror -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/show-hint.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
  <!-- CodeMirror Modes -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/python/python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/sql/sql.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/markdown/markdown.min.js"></script>
  <!-- CodeMirror Addons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/comment/comment.min.js"></script>
  <style>
    body { overflow: hidden; }
    .workspace { height: 100vh; display: grid; grid-template-rows: var(--topbar-height) 1fr; grid-template-columns: var(--filetree-width) 1fr var(--chat-width); }
    .topbar-workspace { grid-column: 1 / -1; }
    .file-tree { grid-row: 2; grid-column: 1; }
    .editor-area { grid-row: 2; grid-column: 2; }
    .chat-sidebar { grid-row: 2; grid-column: 3; }
    .CodeMirror { height: 100% !important; }
    .CodeMirror-scroll { max-height: 100% !important; }
    .editor-area { display: flex; flex-direction: column; }
    .editor-wrapper { flex: 1; overflow: hidden; }
    .editor-wrapper > .CodeMirror { height: 100% !important; }

    /* Toolbar row above file tree */
    .project-toolbar {
      display: flex; align-items: center; gap: .4rem;
      padding: .4rem .5rem;
      background: var(--bg-tertiary);
      border-bottom: 1px solid var(--border-muted);
    }
    .toolbar-btn {
      padding: .25rem .55rem; font-size: .75rem;
      background: var(--bg-elevated); border: 1px solid var(--border-muted);
      border-radius: var(--radius-sm); color: var(--text-secondary);
      cursor: pointer; transition: all var(--transition); white-space: nowrap;
    }
    .toolbar-btn:hover { background: var(--bg-secondary); color: var(--text-primary); border-color: var(--border-default); }
    .toolbar-btn.green { border-color: rgba(63,185,80,.4); color: var(--accent-green); }
    .toolbar-btn.green:hover { background: rgba(63,185,80,.1); }
    .toolbar-btn.blue { border-color: rgba(88,166,255,.4); color: var(--accent-blue); }
    .toolbar-btn.blue:hover { background: rgba(88,166,255,.1); }
    .toolbar-btn.red { border-color: rgba(248,81,73,.4); color: var(--accent-red); }

    /* Remote edit indicator */
    #remote-edit-indicator {
      font-size: .75rem; color: var(--accent-green);
      padding: 0 .75rem;
      transition: opacity .5s ease;
      opacity: 0;
    }

    /* Mobile toggle buttons */
    .mobile-toggle { display: none; }
    @media (max-width: 900px) {
      .workspace { grid-template-columns: 1fr; }
      .file-tree, .chat-sidebar { display: none; }
      .mobile-toggle { display: flex; }
    }
  </style>
</head>
<body>
<div class="workspace">

  <!-- TOP BAR -->
  <nav class="topbar topbar-workspace" style="grid-column:1/-1">
    <div class="topbar-left">
      <a href="/login3/dashboard.php" class="logo">
        <div class="logo-icon">⚡</div>
      </a>
      <div class="breadcrumb">
        <span class="breadcrumb-sep">/</span>
        <a href="/login3/dashboard.php">Projects</a>
        <span class="breadcrumb-sep">/</span>
        <span class="current" id="project-breadcrumb"><?= sanitize($project['name']) ?></span>
      </div>
    </div>

    <div class="topbar-center" style="gap:.5rem">
      <!-- Branch indicator -->
      <div class="toolbar-btn" style="display:flex;align-items:center;gap:.3rem;cursor:default">
        🌿 <span id="current-branch">main</span>
      </div>

      <!-- Online users -->
      <div class="online-users" id="online-users" style="display:flex;align-items:center;gap:.25rem"></div>
      <span id="remote-edit-indicator"></span>

      <!-- Sync indicator -->
      <div class="sync-indicator" id="sync-indicator">
        <div class="sync-dot" id="sync-dot"></div>
        <span id="sync-text">Synced</span>
      </div>
    </div>

    <div class="topbar-right" style="gap:.4rem">
      <?php if ($canEdit): ?>
      <!-- Git Commit -->
      <button class="toolbar-btn green" onclick="Modal.open('commit-modal')" id="git-commit-btn" title="Commit changes">
        🌿 Commit
      </button>
      <!-- Invite -->
      <button class="toolbar-btn blue" onclick="Modal.open('invite-modal')" id="invite-btn" title="Invite collaborator">
        ➕ Invite
      </button>
      <?php endif; ?>

      <!-- Code review -->
      <a href="/login3/review.php?project_id=<?= $project_id ?>" class="toolbar-btn" title="Code Reviews">
        🔍 Reviews
      </a>
      <!-- Git log -->
      <a href="/login3/git_log.php?project_id=<?= $project_id ?>" class="toolbar-btn" title="Git History">
        📜 History
      </a>

      <!-- User menu -->
      <div class="dropdown" id="proj-user-menu">
        <div style="cursor:pointer" data-dropdown="proj-user-dd">
          <div class="avatar avatar-sm" style="background:<?= sanitize($user['avatar_color'] ?? '#58a6ff') ?>">
            <?= getUserInitials($user['username']) ?>
          </div>
        </div>
        <div class="dropdown-menu" id="proj-user-dd">
          <div style="padding:.4rem .9rem;font-size:.8rem;color:var(--text-muted)"><?= sanitize($user['username']) ?> · <?= $myRole ?></div>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="/login3/dashboard.php">📊 Dashboard</a>
          <?php if ($myRole === 'owner'): ?>
          <a class="dropdown-item danger" href="#" onclick="confirmDeleteProjectInWorkspace(event)">🗑️ Delete Project</a>
          <?php endif; ?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item danger" href="/login3/logout.php">🚪 Sign out</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- FILE TREE -->
  <aside class="file-tree">
    <!-- Project toolbar -->
    <div class="project-toolbar">
      <?php if ($canEdit): ?>
      <button class="toolbar-btn" onclick="Modal.open('new-file-modal')" title="New file" style="flex:1">＋ File</button>
      <?php endif; ?>
      <button class="toolbar-btn" onclick="FileTree.load()" title="Refresh">↻</button>
    </div>

    <div class="file-tree-header">
      <span>EXPLORER</span>
      <span style="color:var(--accent-blue);font-size:.7rem"><?= sanitize($project['name']) ?></span>
    </div>
    <div class="file-tree-body" id="file-tree-body">
      <div style="padding:.5rem .75rem;font-size:.8rem;color:var(--text-muted);font-style:italic">Loading...</div>
    </div>
  </aside>

  <!-- EDITOR AREA -->
  <main class="editor-area">
    <!-- Editor tab bar -->
    <div class="editor-topbar">
      <div class="editor-tab active" id="editor-tab">
        <span id="editor-tab-name"><?= $firstFile ? sanitize($firstFile['name']) : 'No file open' ?></span>
        <?php if ($canEdit): ?>
        <span style="font-size:.65rem;color:var(--text-muted);cursor:pointer" id="editor-dirty-dot" style="display:none">●</span>
        <?php endif; ?>
      </div>
      <div style="margin-left:auto;display:flex;align-items:center;gap:.5rem">
        <?php if ($canEdit): ?>
        <button class="btn btn-ghost btn-sm" onclick="Editor.manualSave()" title="Save (Ctrl+S)">💾 Save</button>
        <?php endif; ?>
        <button class="btn btn-ghost btn-sm mobile-toggle" onclick="toggleFileTree()">📁</button>
        <button class="btn btn-ghost btn-sm mobile-toggle" onclick="toggleChat()">💬</button>
      </div>
    </div>

    <!-- Code editor -->
    <div class="editor-wrapper" id="editor-wrapper">
      <?php if ($firstFile): ?>
      <textarea id="code-editor"><?= htmlspecialchars($firstFile['content'] ?? '', ENT_QUOTES) ?></textarea>
      <?php else: ?>
      <div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:1rem;color:var(--text-muted)">
        <div style="font-size:3rem">📄</div>
        <p>No files in this project yet.</p>
        <?php if ($canEdit): ?>
        <button class="btn btn-blue" onclick="Modal.open('new-file-modal')">＋ Create First File</button>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Status bar -->
    <div class="editor-statusbar">
      <span class="statusbar-item">🌿 main</span>
      <span class="statusbar-item" style="margin-left:.5rem" id="editor-lang"><?= sanitize($firstFile['language'] ?? '') ?></span>
      <div class="statusbar-right">
        <span class="statusbar-item" id="editor-cursor-pos">Ln 1, Col 1</span>
        <span class="statusbar-item" id="editor-line-count">0 lines</span>
        <span class="statusbar-item">UTF-8</span>
        <?php if ($canEdit): ?>
        <span class="statusbar-item">Ctrl+S to save</span>
        <?php else: ?>
        <span class="statusbar-item" style="color:rgba(0,0,0,.5)">👁 Read Only</span>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- CHAT SIDEBAR -->
  <aside class="chat-sidebar">
    <div class="chat-header">
      <h4>💬 Team Chat</h4>
      <span class="badge badge-green" id="chat-online-badge">● online</span>
    </div>

    <div class="chat-messages" id="chat-messages">
      <div class="chat-msg-system">Loading messages...</div>
    </div>

    <div class="chat-input-area" id="chat-form">
      <textarea class="chat-input" id="chat-input"
                placeholder="Message team..." rows="1"
                <?= !$canEdit && $myRole === 'viewer' ? '' : '' ?>></textarea>
      <button class="chat-send-btn" id="chat-send-btn" title="Send (Enter)">➤</button>
    </div>
  </aside>
</div>

<!-- ============================================================
     MODALS
     ============================================================ -->

<!-- New File Modal -->
<div class="modal-overlay" id="new-file-modal">
  <div class="modal">
    <div class="modal-header">
      <h3>📄 New File</h3>
      <button class="btn-close" onclick="Modal.close('new-file-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">File name <span class="required">*</span></label>
        <input type="text" class="form-control" id="new-file-name" placeholder="e.g. index.php, styles.css">
        <div class="form-hint">Extension determines language (php, js, html, css, py, sql, md, json…)</div>
      </div>
      <div class="form-group">
        <label class="form-label">Path</label>
        <input type="text" class="form-control" id="new-file-path" placeholder="/src/filename.php">
        <div class="form-hint">Optional. Defaults to /filename</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('new-file-modal')">Cancel</button>
      <button class="btn btn-blue" onclick="FileTree.createFile()">Create File</button>
    </div>
  </div>
</div>

<!-- Commit Modal -->
<div class="modal-overlay" id="commit-modal">
  <div class="modal">
    <div class="modal-header">
      <h3>🌿 Commit Changes</h3>
      <button class="btn-close" onclick="Modal.close('commit-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Commit message <span class="required">*</span></label>
        <input type="text" class="form-control" id="commit-message" placeholder='feat: add new feature'>
        <div class="form-hint">Use conventional commits: feat, fix, docs, style, refactor, test, chore</div>
      </div>
      <div class="form-group">
        <label class="form-label">Branch</label>
        <input type="text" class="form-control" id="commit-branch" value="main" placeholder="main">
      </div>
      <div class="alert alert-info" style="font-size:.82rem">
        <span>ℹ</span> This will snapshot the current content of the active file into the Git history.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('commit-modal')">Cancel</button>
      <button class="btn btn-primary" id="commit-btn" onclick="Git.commit()">🌿 Commit</button>
    </div>
  </div>
</div>

<!-- Invite Modal -->
<div class="modal-overlay" id="invite-modal">
  <div class="modal">
    <div class="modal-header">
      <h3>➕ Invite Collaborator</h3>
      <button class="btn-close" onclick="Modal.close('invite-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Email address <span class="required">*</span></label>
        <input type="email" class="form-control" id="invite-email" placeholder="colleague@example.com">
        <div class="form-hint">They must already have a CollabDev account</div>
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select class="form-control" id="invite-role">
          <option value="collaborator">Collaborator (can edit)</option>
          <option value="viewer">Viewer (read-only)</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="Modal.close('invite-modal')">Cancel</button>
      <button class="btn btn-blue" onclick="Members.invite()">Send Invite</button>
    </div>
  </div>
</div>

<!-- Confirm modal -->
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
<script src="/login3/assets/js/editor.js"></script>
<script src="/login3/assets/js/chat.js"></script>
<script>
const PROJECT_ID   = <?= (int)$project_id ?>;
const PROJECT_NAME = <?= json_encode($project['name']) ?>;
const CURRENT_USER = <?= json_encode(['id' => $user['id'], 'username' => $user['username'], 'avatar_color' => $user['avatar_color']]) ?>;
const CAN_EDIT     = <?= $canEdit ? 'true' : 'false' ?>;

<?php if ($firstFile): ?>
const FIRST_FILE = <?= json_encode(['id' => $firstFile['id'], 'language' => $firstFile['language'], 'name' => $firstFile['name']]) ?>;
<?php else: ?>
const FIRST_FILE = null;
<?php endif; ?>

document.addEventListener('DOMContentLoaded', () => {
  // Init file tree
  FileTree.init(PROJECT_ID);
  FileTree.activeFileId = FIRST_FILE ? FIRST_FILE.id : null;

  // Init editor if a file exists
  if (FIRST_FILE && document.getElementById('code-editor')) {
    Editor.init('code-editor', {
      fileId: FIRST_FILE.id,
      projectId: PROJECT_ID,
      currentUser: CURRENT_USER,
      language: FIRST_FILE.language,
      readOnly: !CAN_EDIT,
    });
  }

  // Init chat
  Chat.init({ projectId: PROJECT_ID, currentUser: CURRENT_USER });

  // Git and Members helpers
  Git.init(PROJECT_ID);
  Members.init(PROJECT_ID);

  // New file name → auto-fill path
  document.getElementById('new-file-name')?.addEventListener('input', function() {
    const path = document.getElementById('new-file-path');
    if (path && !path.value) path.value = '/' + this.value;
    else if (path) path.value = '/' + this.value;
  });

  // Commit enter key
  document.getElementById('commit-message')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') Git.commit();
  });
});

function toggleFileTree() {
  const ft = document.querySelector('.file-tree');
  ft.classList.toggle('mobile-open');
}
function toggleChat() {
  const cs = document.querySelector('.chat-sidebar');
  cs.classList.toggle('mobile-open');
}
function confirmDeleteProjectInWorkspace(event) {
  event.preventDefault();
  confirmAction(`Are you sure you want to delete the project "${escapeJs(PROJECT_NAME)}"? This action cannot be undone and will delete all files, git commits, and reviews.`, async () => {
    try {
      const res = await API.post('/login3/api/projects.php', { action: 'delete', id: PROJECT_ID });
      Toast.success(res.message || 'Project deleted successfully.');
      setTimeout(() => {
        window.location.href = '/login3/dashboard.php';
      }, 1000);
    } catch (e) {
      Toast.error(e.message || 'Failed to delete project.');
    }
  });
}

function escapeJs(str) {
  return (str || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
}
</script>
</body>
</html>
