<?php
/**
 * Follow-up Center — Overdue / Today / Upcoming with KPI strip
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Follow-up Center';

// ---- Actions ----
if (isset($_GET['done']) && is_numeric($_GET['done'])) {
    $pdo->prepare("UPDATE leads SET status='Contacted', next_followup_date=NULL WHERE id=?")->execute([(int)$_GET['done']]);
    header('Location: ' . BASE_URL . 'followups.php?marked=1'); exit;
}
if (isset($_GET['snooze']) && is_numeric($_GET['snooze'])) {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $pdo->prepare("UPDATE leads SET next_followup_date=? WHERE id=?")->execute([$tomorrow, (int)$_GET['snooze']]);
    header('Location: ' . BASE_URL . 'followups.php?snoozed=1'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reschedule_id'])) {
    $rId = (int)$_POST['reschedule_id'];
    $rDate = $_POST['new_date'] ?? '';
    if ($rId > 0 && $rDate !== '') {
        $pdo->prepare("UPDATE leads SET next_followup_date=? WHERE id=?")->execute([$rDate, $rId]);
    }
    header('Location: ' . BASE_URL . 'followups.php?rescheduled=1'); exit;
}

$today = date('Y-m-d');
$next7 = date('Y-m-d', strtotime('+7 days'));

$overdueLeads  = $pdo->query("SELECT * FROM leads WHERE next_followup_date < '$today' AND status NOT IN ('Won','Lost') ORDER BY next_followup_date ASC")->fetchAll();
$todayLeads    = $pdo->query("SELECT * FROM leads WHERE next_followup_date = '$today' AND status NOT IN ('Won','Lost') ORDER BY full_name")->fetchAll();
$upcomingLeads = $pdo->query("SELECT * FROM leads WHERE next_followup_date BETWEEN DATE_ADD('$today', INTERVAL 1 DAY) AND '$next7' AND status NOT IN ('Won','Lost') ORDER BY next_followup_date")->fetchAll();

// KPI data
$totalFollowupLeads = count($overdueLeads) + count($todayLeads) + count($upcomingLeads);
$totalActive   = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status NOT IN ('Won','Lost')")->fetchColumn();
$withFollowup  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE next_followup_date IS NOT NULL AND status NOT IN ('Won','Lost')")->fetchColumn();
$coverageRate  = $totalActive > 0 ? round(($withFollowup / $totalActive) * 100) : 0;

$flashMsg = ''; $flashType = '';
if (!empty($_GET['marked']))     { $flashMsg = 'Follow-up marked as done!'; $flashType = 'success'; }
if (!empty($_GET['rescheduled'])){ $flashMsg = 'Follow-up rescheduled.'; $flashType = 'info'; }
if (!empty($_GET['snoozed']))    { $flashMsg = 'Snoozed to tomorrow.'; $flashType = 'info'; }

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar-check-fill me-2 text-orange"></i>Follow-up Center</h1>
        <p class="page-subtitle mb-0">Stop losing leads because of missed follow-ups.</p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add Lead
    </a>
</div>

<!-- KPI Strip -->
<div class="kpi-strip mb-4">
    <div class="kpi-item" style="border-top:3px solid var(--red);">
        <div class="kpi-value text-red"><?= count($overdueLeads) ?></div>
        <div class="kpi-label">Overdue</div>
    </div>
    <div class="kpi-item" style="border-top:3px solid var(--orange);">
        <div class="kpi-value text-orange"><?= count($todayLeads) ?></div>
        <div class="kpi-label">Due Today</div>
    </div>
    <div class="kpi-item" style="border-top:3px solid var(--teal);">
        <div class="kpi-value text-teal"><?= count($upcomingLeads) ?></div>
        <div class="kpi-label">This Week</div>
    </div>
    <div class="kpi-item" style="border-top:3px solid var(--blue);">
        <div class="kpi-value text-blue"><?= $coverageRate ?>%</div>
        <div class="kpi-label">Follow-up Coverage</div>
    </div>
    <div class="kpi-item" style="border-top:3px solid var(--green);">
        <div class="kpi-value text-green"><?= $withFollowup ?></div>
        <div class="kpi-label">Leads Scheduled</div>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Reschedule Follow-up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body pt-2">
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
function renderSection(array $leads, string $type, string $today, string $base): void {
    $configs = [
        'overdue'  => ['header-class'=>'overdue',  'label'=>'Overdue Follow-ups', 'icon'=>'bi-exclamation-circle-fill', 'row-class'=>'followup-row-overdue'],
        'today'    => ['header-class'=>'today',    'label'=>'Due Today',          'icon'=>'bi-clock-fill',              'row-class'=>'followup-row-today'],
        'upcoming' => ['header-class'=>'upcoming', 'label'=>'Upcoming — Next 7 Days','icon'=>'bi-calendar-range-fill',  'row-class'=>'followup-row-upcoming'],
    ];
    $c = $configs[$type];
    ?>
    <div class="card followup-section mb-4">
        <div class="followup-section-header <?= $c['header-class'] ?>">
            <i class="bi <?= $c['icon'] ?>"></i>
            <?= $c['label'] ?>
            <span class="badge ms-2 <?= $type==='overdue'?'bg-danger':($type==='today'?'bg-warning text-dark':'bg-info text-dark') ?>"><?= count($leads) ?></span>
        </div>
        <?php if (empty($leads)): ?>
            <div class="empty-state py-3">
                <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
                <p class="mb-0">Nothing here — all caught up!</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Lead</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th>Notes</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($leads as $lead): ?>
                <tr class="<?= $c['row-class'] ?>">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lead-avatar" style="width:30px;height:30px;font-size:0.75rem;flex-shrink:0;">
                                <?= strtoupper(substr($lead['full_name'],0,1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:0.845rem;"><?= htmlspecialchars($lead['full_name']) ?></div>
                                <div class="text-muted fs-12"><?= htmlspecialchars($lead['country'] ?? '') ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="copy-phone fs-13"><?= htmlspecialchars($lead['phone'] ?? '—') ?></div>
                        <?php if ($lead['whatsapp']): ?>
                            <div class="text-green fs-12"><i class="bi bi-whatsapp me-1"></i><?= htmlspecialchars($lead['whatsapp']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>"><?= $lead['status'] ?></span></td>
                    <td>
                        <?php
                        $d = $lead['next_followup_date'];
                        if ($d < $today) echo '<span class="overdue-badge"><i class="bi bi-exclamation-circle"></i>' . htmlspecialchars($d) . '</span>';
                        elseif($d === $today) echo '<span class="today-badge"><i class="bi bi-clock"></i>Today</span>';
                        else echo '<span class="text-muted fs-13">' . htmlspecialchars($d) . '</span>';
                        ?>
                    </td>
                    <td>
                        <span class="text-muted fs-12 text-truncate d-inline-block" style="max-width:150px;" title="<?= htmlspecialchars($lead['notes']) ?>">
                            <?= htmlspecialchars($lead['notes'] ?? '—') ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="<?= $base ?>followups.php?done=<?= $lead['id'] ?>"
                               class="btn btn-success btn-action btn-sm" data-bs-toggle="tooltip" data-bs-title="Mark done"
                               onclick="return confirm('Mark as done?')">
                                <i class="bi bi-check-lg"></i>
                            </a>
                            <a href="<?= $base ?>followups.php?snooze=<?= $lead['id'] ?>"
                               class="btn btn-outline-secondary btn-action btn-sm" data-bs-toggle="tooltip" data-bs-title="Snooze 1 day">
                                <i class="bi bi-alarm"></i>
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-action btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#rescheduleModal"
                                    onclick="document.getElementById('rescheduleLeadId').value=<?= $lead['id'] ?>"
                                    title="Reschedule">
                                <i class="bi bi-calendar2-week"></i>
                            </button>
                            <a href="<?= $base ?>lead_form.php?id=<?= $lead['id'] ?>"
                               class="btn btn-outline-primary btn-action btn-sm" data-bs-toggle="tooltip" data-bs-title="Open lead">
                                <i class="bi bi-arrow-up-right-square"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
?>

<?php renderSection($overdueLeads, 'overdue', $today, BASE_URL); ?>
<?php renderSection($todayLeads, 'today', $today, BASE_URL); ?>
<?php renderSection($upcomingLeads, 'upcoming', $today, BASE_URL); ?>

<!-- Value Banner -->
<div class="card text-white overflow-hidden" style="background:linear-gradient(135deg,#4361ee,#8b5cf6);">
    <div class="card-body py-4 text-center position-relative">
        <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
        <i class="bi bi-lightning-charge-fill fs-2 mb-2 d-block"></i>
        <h5 class="fw-800 mb-1">Never Miss a Follow-up Again</h5>
        <p class="mb-0 opacity-75" style="max-width:420px;margin:auto;">
            80% of sales require 5+ follow-ups. Most coaches stop at 2. The fortune is in the follow-up.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
