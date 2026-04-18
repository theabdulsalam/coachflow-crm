<?php
/**
 * Dashboard — overview stats, recent leads, today's follow-ups, chart data
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';

// ---- Stats ----
$totalLeads   = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$newLeads     = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
$followToday  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE next_followup_date = CURDATE() AND status NOT IN ('Won','Lost')")->fetchColumn();
$bookedLeads  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Booked'")->fetchColumn();
$wonLeads     = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Won'")->fetchColumn();

// ---- Recent 5 leads ----
$recentLeads = $pdo->query("SELECT id, full_name, lead_source, status, created_at FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();

// ---- Today follow-ups ----
$todayFollowups = $pdo->query("SELECT id, full_name, phone, status, notes FROM leads WHERE next_followup_date = CURDATE() AND status NOT IN ('Won','Lost') ORDER BY full_name ASC")->fetchAll();

// ---- Chart: Leads by source ----
$sourceRows = $pdo->query("SELECT lead_source, COUNT(*) AS cnt FROM leads GROUP BY lead_source ORDER BY cnt DESC")->fetchAll();
$sourceLabels = array_column($sourceRows, 'lead_source');
$sourceCounts = array_column($sourceRows, 'cnt');

// ---- Chart: Leads by month (last 6 months) ----
$monthRows = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') AS mo, COUNT(*) AS cnt FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY MIN(created_at) ASC")->fetchAll();
$monthLabels = array_column($monthRows, 'mo');
$monthCounts = array_column($monthRows, 'cnt');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle mb-0">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Here's what's happening.</p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Lead
    </a>
</div>

<!-- ---- Stat Cards ---- -->
<div class="row g-3 mb-4">
    <?php
    $stats = [
        ['label'=>'Total Leads',      'value'=>$totalLeads,  'icon'=>'bi-people-fill',         'bg'=>'bg-blue-soft',   'color'=>'text-blue'],
        ['label'=>'New Leads',        'value'=>$newLeads,    'icon'=>'bi-person-plus-fill',     'bg'=>'bg-purple-soft', 'color'=>'text-purple'],
        ['label'=>'Follow-ups Today', 'value'=>$followToday, 'icon'=>'bi-calendar-check-fill',  'bg'=>'bg-orange-soft', 'color'=>'text-orange'],
        ['label'=>'Booked Calls',     'value'=>$bookedLeads, 'icon'=>'bi-telephone-fill',       'bg'=>'bg-blue-soft',   'color'=>'text-blue'],
        ['label'=>'Converted Clients','value'=>$wonLeads,    'icon'=>'bi-trophy-fill',          'bg'=>'bg-green-soft',  'color'=>'text-green'],
    ];
    foreach ($stats as $s): ?>
    <div class="col-6 col-md-4 col-xl-2-4">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon <?= $s['bg'] ?> <?= $s['color'] ?>"><?= "<i class=\"bi {$s['icon']} fs-4\"></i>" ?></div>
                <div>
                    <div class="stat-value"><?= $s['value'] ?></div>
                    <div class="stat-label"><?= $s['label'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ---- Quick Actions ---- -->
<div class="row g-2 mb-4">
    <div class="col-auto">
        <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Lead
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= BASE_URL ?>leads.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-people me-1"></i> All Leads
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= BASE_URL ?>followups.php" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-calendar-check me-1"></i> Follow-ups
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= BASE_URL ?>reports.php" class="btn btn-outline-info btn-sm">
            <i class="bi bi-bar-chart me-1"></i> Reports
        </a>
    </div>
</div>

<!-- ---- Charts + Tables ---- -->
<div class="row g-3 mb-4">
    <!-- Chart: Leads by Source -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Leads by Source</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px;">
                    <canvas id="sourceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Monthly Leads -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-2 text-success"></i>New Leads — Last 6 Months
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ---- Recent Leads + Today Follow-ups ---- -->
<div class="row g-3">
    <!-- Recent Leads -->
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Recent Leads</span>
                <a href="<?= BASE_URL ?>leads.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentLeads)): ?>
                    <div class="empty-state"><i class="bi bi-people d-block"></i>No leads yet.</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Added</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentLeads as $lead): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>lead_form.php?id=<?= $lead['id'] ?>" class="fw-semibold text-decoration-none text-dark">
                                        <?= htmlspecialchars($lead['full_name']) ?>
                                    </a>
                                </td>
                                <td><span class="text-muted"><?= htmlspecialchars($lead['lead_source'] ?? '—') ?></span></td>
                                <td>
                                    <span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>">
                                        <?= htmlspecialchars($lead['status']) ?>
                                    </span>
                                </td>
                                <td class="text-muted"><?= date('d M', strtotime($lead['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Today Follow-ups -->
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar2-check-fill me-2 text-warning"></i>Today's Follow-ups</span>
                <a href="<?= BASE_URL ?>followups.php" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($todayFollowups)): ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle d-block"></i>
                        <p>All clear! No follow-ups due today.</p>
                    </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                <?php foreach ($todayFollowups as $f): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-3">
                        <div>
                            <div class="fw-semibold"><?= htmlspecialchars($f['full_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($f['phone'] ?? '') ?></small>
                            <?php if ($f['notes']): ?>
                                <small class="d-block text-muted text-truncate" style="max-width:180px;"><?= htmlspecialchars($f['notes']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="<?= BASE_URL ?>lead_form.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary btn-action" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$extraJs = '<script>
(function(){
    // Pie — Leads by Source
    const srcLabels = ' . json_encode($sourceLabels) . ';
    const srcData   = ' . json_encode($sourceCounts) . ';
    const palette   = ["#4361ee","#2ec4b6","#f77f00","#e63946","#7209b7","#06d6a0","#118ab2"];
    new Chart(document.getElementById("sourceChart"), {
        type: "doughnut",
        data: {
            labels: srcLabels,
            datasets:[{ data: srcData, backgroundColor: palette.slice(0,srcLabels.length), borderWidth:2, borderColor:"#fff" }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ position:"bottom", labels:{ font:{ size:11 }, padding:10 } } }
        }
    });

    // Bar — Monthly Leads
    const moLabels = ' . json_encode($monthLabels) . ';
    const moData   = ' . json_encode($monthCounts) . ';
    new Chart(document.getElementById("monthlyChart"), {
        type: "bar",
        data:{
            labels: moLabels,
            datasets:[{
                label:"New Leads",
                data: moData,
                backgroundColor:"rgba(67,97,238,0.18)",
                borderColor:"#4361ee",
                borderWidth:2,
                borderRadius:6
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ display:false } },
            scales:{
                y:{ beginAtZero:true, ticks:{ stepSize:1, font:{size:11} }, grid:{ color:"#f0f0f0" } },
                x:{ ticks:{ font:{size:11} }, grid:{ display:false } }
            }
        }
    });
})();
</script>';

require_once __DIR__ . '/includes/footer.php';
?>
