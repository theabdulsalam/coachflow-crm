<?php
/**
 * Leads Management — list, search, filter, paginate
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Leads';

// ---- Delete action ----
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: ' . BASE_URL . 'leads.php?deleted=1');
    exit;
}

// ---- Filters ----
$search  = trim($_GET['q']       ?? '');
$fStatus = trim($_GET['status']  ?? '');
$fSource = trim($_GET['source']  ?? '');
$fService= trim($_GET['service'] ?? '');

// ---- Pagination ----
$perPage     = 10;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

// Build WHERE
$where  = [];
$params = [];
if ($search !== '') {
    $where[]  = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $like     = "%$search%";
    $params   = array_merge($params, [$like, $like, $like]);
}
if ($fStatus  !== '') { $where[] = "status = ?";           $params[] = $fStatus; }
if ($fSource  !== '') { $where[] = "lead_source = ?";      $params[] = $fSource; }
if ($fService !== '') { $where[] = "service_interest = ?"; $params[] = $fService; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$totalRows   = (int) $pdo->prepare("SELECT COUNT(*) FROM leads $whereSql")->execute($params) ? 0 : 0;
$countStmt   = $pdo->prepare("SELECT COUNT(*) FROM leads $whereSql");
$countStmt->execute($params);
$totalRows   = (int) $countStmt->fetchColumn();
$totalPages  = max(1, (int) ceil($totalRows / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset      = ($currentPage - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM leads $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$leads = $stmt->fetchAll();

// For filter dropdowns
$statuses  = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];
$allSources  = $pdo->query("SELECT DISTINCT lead_source FROM leads WHERE lead_source IS NOT NULL ORDER BY lead_source")->fetchAll(PDO::FETCH_COLUMN);
$allServices = $pdo->query("SELECT DISTINCT service_interest FROM leads WHERE service_interest IS NOT NULL ORDER BY service_interest")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h1 class="page-title">Leads</h1>
        <p class="page-subtitle mb-0"><?= $totalRows ?> lead<?= $totalRows !== 1 ? 's' : '' ?> found</p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Lead
    </a>
</div>

<?php if (!empty($_GET['deleted'])): ?>
    <div class="alert alert-success alert-auto-dismiss alert-dismissible d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2"></i> Lead deleted successfully.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['saved'])): ?>
    <div class="alert alert-success alert-auto-dismiss alert-dismissible d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2"></i> Lead saved successfully.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Search & Filters -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" id="tableSearch"
                           placeholder="Name, email, phone…" value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= $fStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label">Source</label>
                <select class="form-select" name="source">
                    <option value="">All Sources</option>
                    <?php foreach ($allSources as $src): ?>
                        <option value="<?= htmlspecialchars($src) ?>" <?= $fSource === $src ? 'selected' : '' ?>>
                            <?= htmlspecialchars($src) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label">Service</label>
                <select class="form-select" name="service">
                    <option value="">All Services</option>
                    <?php foreach ($allServices as $sv): ?>
                        <option value="<?= htmlspecialchars($sv) ?>" <?= $fService === $sv ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sv) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="<?= BASE_URL ?>leads.php" class="btn btn-outline-secondary" title="Clear">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Leads Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($leads)): ?>
            <div class="empty-state">
                <i class="bi bi-people d-block"></i>
                <p class="mb-2">No leads found.</p>
                <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary btn-sm">Add First Lead</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email / Phone</th>
                        <th>Service</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Next Follow-up</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($leads as $i => $lead): ?>
                    <tr data-searchable="<?= htmlspecialchars(strtolower($lead['full_name'].' '.$lead['email'].' '.$lead['phone'])) ?>">
                        <td class="text-muted"><?= $offset + $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold"><?= htmlspecialchars($lead['full_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($lead['country'] ?? '') ?></small>
                        </td>
                        <td>
                            <div class="copy-phone" title="Copy email"><?= htmlspecialchars($lead['email'] ?? '—') ?></div>
                            <small class="text-muted copy-phone"><?= htmlspecialchars($lead['phone'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($lead['service_interest'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($lead['lead_source'] ?? '—') ?></span>
                        </td>
                        <td>
                            <span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>">
                                <?= htmlspecialchars($lead['status']) ?>
                            </span>
                        </td>
                        <td>
                            <span data-followup-date="<?= htmlspecialchars($lead['next_followup_date'] ?? '') ?>">
                                <?= $lead['next_followup_date'] ? htmlspecialchars($lead['next_followup_date']) : '<span class="text-muted">—</span>' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="<?= BASE_URL ?>lead_form.php?id=<?= $lead['id'] ?>"
                                   class="btn btn-outline-primary btn-action btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= BASE_URL ?>leads.php?delete=<?= $lead['id'] ?>"
                                   class="btn btn-outline-danger btn-action btn-sm btn-delete-lead"
                                   data-name="<?= htmlspecialchars($lead['full_name']) ?>" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
            <small class="text-muted">
                Showing <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?>
            </small>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
                            <a class="page-link"
                               href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($fStatus) ?>&source=<?= urlencode($fSource) ?>&service=<?= urlencode($fService) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
