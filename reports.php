<?php
/**
 * Reports & Analytics
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Reports & Analytics';

// ---- Leads by Source ----
$sourceData = $pdo->query("SELECT lead_source AS label, COUNT(*) AS cnt FROM leads WHERE lead_source IS NOT NULL GROUP BY lead_source ORDER BY cnt DESC")->fetchAll();

// ---- Leads by Status ----
$statusData = $pdo->query("SELECT status AS label, COUNT(*) AS cnt FROM leads GROUP BY status ORDER BY cnt DESC")->fetchAll();

// ---- Monthly New Leads (last 12 months) ----
$monthlyData = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') AS label, COUNT(*) AS cnt FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY MIN(created_at) ASC")->fetchAll();

// ---- Conversion Summary ----
$totalLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$wonLeads   = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Won'")->fetchColumn();
$lostLeads  = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Lost'")->fetchColumn();
$activeLeads= $totalLeads - $wonLeads - $lostLeads;
$convRate   = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

// ---- Top service ----
$topService = $pdo->query("SELECT service_interest, COUNT(*) AS cnt FROM leads WHERE service_interest IS NOT NULL GROUP BY service_interest ORDER BY cnt DESC LIMIT 1")->fetch();

// ---- Top source ----
$topSource = $pdo->query("SELECT lead_source, COUNT(*) AS cnt FROM leads WHERE lead_source IS NOT NULL GROUP BY lead_source ORDER BY cnt DESC LIMIT 1")->fetch();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle mb-0">Track your lead pipeline performance at a glance.</p>
    </div>
    <span class="badge bg-light text-muted border px-3 py-2">
        <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
    </span>
</div>

<!-- Conversion Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="stat-value"><?= $totalLeads ?></div>
            <div class="stat-label">Total Leads</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="stat-value text-success"><?= $wonLeads ?></div>
            <div class="stat-label">Clients Won</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="stat-value text-danger"><?= $lostLeads ?></div>
            <div class="stat-label">Leads Lost</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="stat-value text-primary"><?= $convRate ?>%</div>
            <div class="stat-label">Conversion Rate</div>
        </div>
    </div>
</div>

<!-- Insights Row -->
<div class="row g-3 mb-4">
    <?php if ($topSource): ?>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-blue-soft text-blue">
                <i class="bi bi-stars fs-4"></i>
            </div>
            <div>
                <div class="fw-bold"><?= htmlspecialchars($topSource['lead_source']) ?></div>
                <div class="stat-label">Top Lead Source</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($topService): ?>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-green-soft text-green">
                <i class="bi bi-briefcase-fill fs-4"></i>
            </div>
            <div>
                <div class="fw-bold"><?= htmlspecialchars($topService['service_interest']) ?></div>
                <div class="stat-label">Most Requested Service</div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card p-3 d-flex flex-row align-items-center gap-3">
            <div class="stat-icon bg-orange-soft text-orange">
                <i class="bi bi-activity fs-4"></i>
            </div>
            <div>
                <div class="fw-bold"><?= $activeLeads ?> Active</div>
                <div class="stat-label">In-Pipeline Leads</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-3 mb-4">
    <!-- Pie — by Source -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Leads by Source</div>
            <div class="card-body">
                <div class="chart-container"><canvas id="pieSource"></canvas></div>
            </div>
        </div>
    </div>
    <!-- Bar — by Status -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-2 text-info"></i>Leads by Status</div>
            <div class="card-body">
                <div class="chart-container"><canvas id="barStatus"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Line Chart — Monthly -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Monthly New Leads — Last 12 Months</div>
    <div class="card-body">
        <div class="chart-container" style="height:300px;"><canvas id="lineMonthly"></canvas></div>
    </div>
</div>

<!-- Leads by Source Table -->
<div class="row g-3">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Source Breakdown</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Source</th><th>Leads</th><th>Share</th></tr></thead>
                    <tbody>
                    <?php foreach ($sourceData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['label'] ?? '—') ?></td>
                            <td><span class="fw-semibold"><?= $row['cnt'] ?></span></td>
                            <td>
                                <?php $pct = $totalLeads > 0 ? round(($row['cnt']/$totalLeads)*100) : 0; ?>
                                <div class="progress" style="height:6px;width:80px;display:inline-flex;">
                                    <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                                </div>
                                <small class="text-muted ms-1"><?= $pct ?>%</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Status Breakdown</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Status</th><th>Count</th><th>Badge</th></tr></thead>
                    <tbody>
                    <?php foreach ($statusData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['label']) ?></td>
                            <td class="fw-semibold"><?= $row['cnt'] ?></td>
                            <td>
                                <span class="status-badge status-<?= str_replace(' ','-',$row['label']) ?>">
                                    <?= htmlspecialchars($row['label']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Build JS chart data
$srcLabels   = array_column($sourceData, 'label');
$srcCounts   = array_column($sourceData, 'cnt');
$statLabels  = array_column($statusData, 'label');
$statCounts  = array_column($statusData, 'cnt');
$moLabels    = array_column($monthlyData, 'label');
$moCounts    = array_column($monthlyData, 'cnt');

// Status colours map
$statusColors = [
    'New'                   => '#4361ee',
    'Contacted'             => '#0c7592',
    'Follow-up Scheduled'   => '#f77f00',
    'Booked'                => '#6f42c1',
    'Won'                   => '#1a7431',
    'Lost'                  => '#c0392b',
];
$statColors = array_map(fn($l) => $statusColors[$l] ?? '#999', $statLabels);

$extraJs = '<script>
(function(){
    const palette = ["#4361ee","#2ec4b6","#f77f00","#e63946","#7209b7","#06d6a0","#118ab2","#fb8500"];

    // Pie — by source
    new Chart(document.getElementById("pieSource"),{
        type:"doughnut",
        data:{
            labels:' . json_encode($srcLabels) . ',
            datasets:[{data:' . json_encode($srcCounts) . ',backgroundColor:palette,borderWidth:2,borderColor:"#fff"}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:"right",labels:{font:{size:11},padding:12}}}}
    });

    // Bar — by status
    new Chart(document.getElementById("barStatus"),{
        type:"bar",
        data:{
            labels:' . json_encode($statLabels) . ',
            datasets:[{label:"Leads",data:' . json_encode($statCounts) . ',backgroundColor:' . json_encode($statColors) . ',borderRadius:6,borderSkipped:false}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11}},grid:{color:"#f0f0f0"}},x:{ticks:{font:{size:11}},grid:{display:false}}}}
    });

    // Line — monthly
    new Chart(document.getElementById("lineMonthly"),{
        type:"line",
        data:{
            labels:' . json_encode($moLabels) . ',
            datasets:[{label:"New Leads",data:' . json_encode($moCounts) . ',borderColor:"#4361ee",backgroundColor:"rgba(67,97,238,0.1)",fill:true,tension:0.4,pointBackgroundColor:"#4361ee",pointRadius:4}]
        },
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11}},grid:{color:"#f0f0f0"}},x:{ticks:{font:{size:11}},grid:{display:false}}}}
    });
})();
</script>';

require_once __DIR__ . '/includes/footer.php';
?>
