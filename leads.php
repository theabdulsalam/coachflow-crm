<?php
/**
 * Leads Management — list, search, filter, paginate, modal view
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Leads';

// ---- Delete ----
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM leads WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: ' . BASE_URL . 'leads.php?deleted=1');
    exit;
}

// ---- Filters ----
$search   = trim($_GET['q']       ?? '');
$fStatus  = trim($_GET['status']  ?? '');
$fSource  = trim($_GET['source']  ?? '');
$fService = trim($_GET['service'] ?? '');

// ---- Pagination ----
$perPage     = 12;
$currentPage = max(1, (int)($_GET['page'] ?? 1));

$where  = [];
$params = [];
if ($search !== '') {
    $where[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $like    = "%$search%";
    $params  = [$like, $like, $like];
}
if ($fStatus  !== '') { $where[] = "status = ?";           $params[] = $fStatus; }
if ($fSource  !== '') { $where[] = "lead_source = ?";      $params[] = $fSource; }
if ($fService !== '') { $where[] = "service_interest = ?"; $params[] = $fService; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$cntStmt  = $pdo->prepare("SELECT COUNT(*) FROM leads $whereSql");
$cntStmt->execute($params);
$totalRows  = (int) $cntStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM leads $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$leads = $stmt->fetchAll();

$statuses    = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];
$allSources  = $pdo->query("SELECT DISTINCT lead_source FROM leads WHERE lead_source IS NOT NULL ORDER BY lead_source")->fetchAll(PDO::FETCH_COLUMN);
$allServices = $pdo->query("SELECT DISTINCT service_interest FROM leads WHERE service_interest IS NOT NULL ORDER BY service_interest")->fetchAll(PDO::FETCH_COLUMN);

// Flash messages
$flashMsg = ''; $flashType = '';
if (!empty($_GET['deleted'])) { $flashMsg = 'Lead deleted successfully.'; $flashType = 'success'; }
if (!empty($_GET['saved']))   { $flashMsg = 'Lead saved successfully.'; $flashType = 'success'; }

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="page-title">Leads</h1>
        <p class="page-subtitle mb-0">
            <?= $totalRows ?> lead<?= $totalRows !== 1 ? 's' : '' ?>
            <?= ($search || $fStatus || $fSource || $fService) ? ' matching your filters' : ' total' ?>
        </p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add Lead
    </a>
</div>

<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control" name="q" id="tableSearch"
                               placeholder="Name, email or phone…" value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                        <?php if ($search): ?>
                        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('tableSearch').value='';document.getElementById('filterForm').submit();">
                            <i class="bi bi-x"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= $s ?>" <?= $fStatus===$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label">Source</label>
                    <select class="form-select" name="source" onchange="this.form.submit()">
                        <option value="">All Sources</option>
                        <?php foreach ($allSources as $src): ?>
                            <option value="<?= htmlspecialchars($src) ?>" <?= $fSource===$src?'selected':'' ?>><?= htmlspecialchars($src) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="form-label">Service</label>
                    <select class="form-select" name="service" onchange="this.form.submit()">
                        <option value="">All Services</option>
                        <?php foreach ($allServices as $sv): ?>
                            <option value="<?= htmlspecialchars($sv) ?>" <?= $fService===$sv?'selected':'' ?>><?= htmlspecialchars($sv) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <?php if ($search || $fStatus || $fSource || $fService): ?>
                    <a href="<?= BASE_URL ?>leads.php" class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="Clear filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Leads Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                <h6>No leads found</h6>
                <p><?= ($search||$fStatus||$fSource||$fService) ? 'Try adjusting your filters.' : 'Add your first lead to get started.' ?></p>
                <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary btn-sm">Add Lead</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Service</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Next Follow-up</th>
                        <th class="text-center" style="width:90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($leads as $i => $lead): ?>
                    <?php $leadJson = json_encode([
                            'id'                => $lead['id'],
                            'full_name'         => $lead['full_name'],
                            'email'             => $lead['email'],
                            'phone'             => $lead['phone'],
                            'whatsapp'          => $lead['whatsapp'],
                            'country'           => $lead['country'],
                            'service_interest'  => $lead['service_interest'],
                            'lead_source'       => $lead['lead_source'],
                            'status'            => $lead['status'],
                            'next_followup_date'=> $lead['next_followup_date'],
                            'notes'             => $lead['notes'],
                        ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS); ?>
                    <tr class="clickable-row"
                        data-lead="<?= htmlspecialchars($leadJson, ENT_COMPAT, 'UTF-8') ?>"
                        style="cursor:pointer;"
                        title="Click to view lead details">
                        <td class="text-muted fs-12"><?= $offset + $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="lead-avatar" style="width:32px;height:32px;font-size:0.8rem;flex-shrink:0;">
                                    <?= strtoupper(substr($lead['full_name'],0,1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:0.845rem;"><?= htmlspecialchars($lead['full_name']) ?></div>
                                    <div class="text-muted fs-12"><?= htmlspecialchars($lead['country'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="copy-phone" style="font-size:0.82rem;"><?= htmlspecialchars($lead['email'] ?? '—') ?></div>
                            <div class="copy-phone text-muted fs-12"><?= htmlspecialchars($lead['phone'] ?? '') ?></div>
                        </td>
                        <td style="font-size:0.82rem;"><?= htmlspecialchars($lead['service_interest'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-light text-dark border" style="font-size:0.72rem;font-weight:500;">
                                <?= htmlspecialchars($lead['lead_source'] ?? '—') ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>">
                                <?= $lead['status'] ?>
                            </span>
                        </td>
                        <td>
                            <span data-followup-date="<?= htmlspecialchars($lead['next_followup_date'] ?? '') ?>">
                                <?= $lead['next_followup_date'] ? htmlspecialchars($lead['next_followup_date']) : '<span class="text-muted fs-12">—</span>' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="<?= BASE_URL ?>lead_form.php?id=<?= $lead['id'] ?>"
                                   class="btn btn-outline-primary btn-action btn-sm"
                                   data-bs-toggle="tooltip" data-bs-title="Edit lead">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>leads.php?delete=<?= $lead['id'] ?>"
                                   class="btn btn-outline-danger btn-action btn-sm btn-delete-lead"
                                   data-name="<?= htmlspecialchars($lead['full_name']) ?>"
                                   data-bs-toggle="tooltip" data-bs-title="Delete lead">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination + info -->
        <div class="d-flex flex-wrap justify-content-between align-items-center px-3 py-3 border-top gap-2">
            <small class="text-muted">
                Showing <strong><?= $offset + 1 ?>–<?= min($offset + $perPage, $totalRows) ?></strong> of <strong><?= $totalRows ?></strong> leads
            </small>
            <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <?php for ($p = max(1, (int)$currentPage - 2); $p <= min((int)$totalPages, (int)$currentPage + 2); $p++): ?>
                        <li class="page-item <?= $p===$currentPage?'active':'' ?>">
                            <a class="page-link" style="border-radius:6px;"
                               href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($fStatus) ?>&source=<?= urlencode($fSource) ?>&service=<?= urlencode($fService) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
