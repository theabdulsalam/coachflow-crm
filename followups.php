<?php
/**
 * Follow-up Center — Overdue / Today / Upcoming
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Follow-up Center';

// ---- Mark Done action ----
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $stmt = $pdo->prepare("UPDATE leads SET status='Contacted', next_followup_date=NULL WHERE id=?");
    $stmt->execute([(int)$_GET['done']]);
    header('Location: ' . BASE_URL . 'followups.php?marked=1');
    exit;
}

// ---- Reschedule action ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reschedule_id'])) {
    $rId   = (int)$_POST['reschedule_id'];
    $rDate = $_POST['new_date'] ?? '';
    if ($rId > 0 && $rDate !== '') {
        $stmt = $pdo->prepare("UPDATE leads SET next_followup_date=? WHERE id=?");
        $stmt->execute([$rDate, $rId]);
    }
    header('Location: ' . BASE_URL . 'followups.php?rescheduled=1');
    exit;
}

// ---- Queries ----
$today = date('Y-m-d');
$next7 = date('Y-m-d', strtotime('+7 days'));

$overdueLeads  = $pdo->query("SELECT * FROM leads WHERE next_followup_date < '$today' AND status NOT IN ('Won','Lost') ORDER BY next_followup_date ASC")->fetchAll();
$todayLeads    = $pdo->query("SELECT * FROM leads WHERE next_followup_date = '$today' AND status NOT IN ('Won','Lost') ORDER BY full_name ASC")->fetchAll();
$upcomingLeads = $pdo->query("SELECT * FROM leads WHERE next_followup_date BETWEEN DATE_ADD('$today', INTERVAL 1 DAY) AND '$next7' AND status NOT IN ('Won','Lost') ORDER BY next_followup_date ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar-check-fill me-2 text-warning"></i>Follow-up Center</h1>
        <p class="page-subtitle mb-0">Stop losing leads because of missed follow-ups.</p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Lead
    </a>
</div>

<?php if (!empty($_GET['marked'])): ?>
    <div class="alert alert-success alert-auto-dismiss alert-dismissible d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2"></i> Follow-up marked as done.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['rescheduled'])): ?>
    <div class="alert alert-info alert-auto-dismiss alert-dismissible d-flex align-items-center">
        <i class="bi bi-calendar-check me-2"></i> Follow-up rescheduled.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card stat-card p-3 border-start border-danger border-4">
            <div class="stat-value text-danger"><?= count($overdueLeads) ?></div>
            <div class="stat-label">Overdue</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card stat-card p-3 border-start border-warning border-4">
            <div class="stat-value text-warning"><?= count($todayLeads) ?></div>
            <div class="stat-label">Due Today</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card stat-card p-3 border-start border-info border-4">
            <div class="stat-value text-info"><?= count($upcomingLeads) ?></div>
            <div class="stat-label">Upcoming (7 days)</div>
        </div>
    </div>
</div>

<?php
// Reschedule modal (shared)
?>
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Reschedule Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="reschedule_id" id="rescheduleLeadId">
                    <label class="form-label">New Date</label>
                    <input type="date" class="form-control" name="new_date" min="<?= $today ?>" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary btn-sm">Confirm</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
function renderFollowupTable(array $leads, string $type): void
{
    if (empty($leads)) {
        echo '<div class="empty-state"><i class="bi bi-check-circle d-block"></i><p>Nothing here — you\'re all caught up!</p></div>';
        return;
    }
    ?>
    <div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Lead Name</th>
                <th>Phone / WhatsApp</th>
                <th>Status</th>
                <th>Follow-up Date</th>
                <th>Notes</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($lead['full_name']) ?></td>
                <td>
                    <div class="copy-phone small"><?= htmlspecialchars($lead['phone'] ?? '—') ?></div>
                    <?php if ($lead['whatsapp']): ?>
                        <small class="text-success"><i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($lead['whatsapp']) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>">
                        <?= htmlspecialchars($lead['status']) ?>
                    </span>
                </td>
                <td>
                    <?php
                    $date  = $lead['next_followup_date'];
                    $today = date('Y-m-d');
                    if ($date < $today) {
                        echo '<span class="overdue-badge"><i class="bi bi-exclamation-circle me-1"></i>' . htmlspecialchars($date) . '</span>';
                    } elseif ($date === $today) {
                        echo '<span class="today-badge"><i class="bi bi-clock me-1"></i>Today</span>';
                    } else {
                        echo '<span class="text-muted">' . htmlspecialchars($date) . '</span>';
                    }
                    ?>
                </td>
                <td>
                    <small class="text-muted text-truncate d-inline-block" style="max-width:160px;" title="<?= htmlspecialchars($lead['notes']) ?>">
                        <?= htmlspecialchars($lead['notes'] ?? '—') ?>
                    </small>
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <a href="<?= BASE_URL ?>followups.php?done=<?= $lead['id'] ?>"
                           class="btn btn-sm btn-success btn-action" title="Mark Done"
                           onclick="return confirm('Mark this follow-up as done?')">
                            <i class="bi bi-check-lg"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-warning btn-action"
                                title="Reschedule"
                                data-bs-toggle="modal" data-bs-target="#rescheduleModal"
                                onclick="document.getElementById('rescheduleLeadId').value=<?= $lead['id'] ?>">
                            <i class="bi bi-calendar2-week"></i>
                        </button>
                        <a href="<?= BASE_URL ?>lead_form.php?id=<?= $lead['id'] ?>"
                           class="btn btn-sm btn-outline-primary btn-action" title="Open Lead">
                            <i class="bi bi-arrow-up-right-square"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php
}
?>

<!-- Overdue -->
<div class="card mb-4">
    <div class="card-header bg-danger bg-opacity-10 d-flex align-items-center justify-content-between">
        <span class="text-danger fw-semibold">
            <i class="bi bi-exclamation-circle-fill me-2"></i>Overdue Follow-ups
            <span class="badge bg-danger ms-2"><?= count($overdueLeads) ?></span>
        </span>
    </div>
    <div class="card-body p-0">
        <?php renderFollowupTable($overdueLeads, 'overdue'); ?>
    </div>
</div>

<!-- Today -->
<div class="card mb-4">
    <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center justify-content-between">
        <span class="text-warning fw-semibold">
            <i class="bi bi-clock-fill me-2"></i>Due Today — <?= date('d M Y') ?>
            <span class="badge bg-warning text-dark ms-2"><?= count($todayLeads) ?></span>
        </span>
    </div>
    <div class="card-body p-0">
        <?php renderFollowupTable($todayLeads, 'today'); ?>
    </div>
</div>

<!-- Upcoming -->
<div class="card mb-4">
    <div class="card-header bg-info bg-opacity-10 d-flex align-items-center justify-content-between">
        <span class="text-info fw-semibold">
            <i class="bi bi-calendar-range-fill me-2"></i>Upcoming — Next 7 Days
            <span class="badge bg-info ms-2"><?= count($upcomingLeads) ?></span>
        </span>
    </div>
    <div class="card-body p-0">
        <?php renderFollowupTable($upcomingLeads, 'upcoming'); ?>
    </div>
</div>

<!-- Business Value Message -->
<div class="card bg-primary text-white">
    <div class="card-body text-center py-4">
        <i class="bi bi-lightning-charge-fill fs-2 mb-2"></i>
        <h5 class="fw-bold">Never Miss a Follow-up Again</h5>
        <p class="mb-0 opacity-75">80% of sales require 5+ follow-ups. Most coaches give up after 2. Stay in the game.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
