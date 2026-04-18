<?php
/**
 * Reports & Analytics — KPI cards, 4 charts, funnel, CSV export
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Reports & Analytics';

// ---- Core metrics ----
$totalLeads  = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$wonLeads    = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Won'")->fetchColumn();
$lostLeads   = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Lost'")->fetchColumn();
$activeLeads = $totalLeads - $wonLeads - $lostLeads;
$convRate    = $totalLeads > 0 ? round(($wonLeads/$totalLeads)*100,1) : 0;

// Avg days to close (won leads)
$avgClose = $pdo->query("SELECT AVG(DATEDIFF(updated_at, created_at)) FROM leads WHERE status='Won'")->fetchColumn();
$avgClose = $avgClose ? round($avgClose) : 0;

// Source data
$sourceData = $pdo->query("SELECT lead_source AS label, COUNT(*) AS cnt FROM leads WHERE lead_source IS NOT NULL GROUP BY lead_source ORDER BY cnt DESC")->fetchAll();
// Status data
$statusData = $pdo->query("SELECT status AS label, COUNT(*) AS cnt FROM leads GROUP BY status ORDER BY FIELD(status,'New','Contacted','Follow-up Scheduled','Booked','Won','Lost')")->fetchAll();
// Monthly (12 months)
$monthlyData = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') AS label, COUNT(*) AS cnt FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY MIN(created_at)")->fetchAll();
// Top source
$topSource = $pdo->query("SELECT lead_source, COUNT(*) AS cnt FROM leads WHERE lead_source IS NOT NULL GROUP BY lead_source ORDER BY cnt DESC LIMIT 1")->fetch();
// Top service
$topService = $pdo->query("SELECT service_interest, COUNT(*) AS cnt FROM leads WHERE service_interest IS NOT NULL GROUP BY service_interest ORDER BY cnt DESC LIMIT 1")->fetch();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

$statusColors = ['New'=>'#4361ee','Contacted'=>'#06b6d4','Follow-up Scheduled'=>'#f59e0b','Booked'=>'#8b5cf6','Won'=>'#10b981','Lost'=>'#ef4444'];
$statColors = array_map(fn($r)=>$statusColors[$r['label']]??'#999', $statusData);

$funnelSteps = ['New','Contacted','Follow-up Scheduled','Booked','Won'];
$funnelCounts = array_map(fn($s) => $pdo->query("SELECT COUNT(*) FROM leads WHERE status='$s'")->fetchColumn(), $funnelSteps);
$funnelMax = max(array_values($funnelCounts)?:[1]);
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle mb-0">Executive pipeline overview — <?= date('d M Y') ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>reports_export.php" class="btn btn-outline-success d-flex align-items-center gap-2">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <?php
    $kpis = [
        ['Total Leads',      $totalLeads,  'bi-people-fill',     'blue',   $totalLeads . ' tracked'],
        ['Clients Won',      $wonLeads,    'bi-trophy-fill',     'green',  'Paying clients'],
        ['Conversion Rate',  $convRate.'%','bi-percent',         'purple', 'Won / Total'],
        ['Avg Days to Close',$avgClose.'d','bi-hourglass-split', 'orange', 'Won leads only'],
        ['Active Pipeline',  $activeLeads, 'bi-activity',        'teal',   'In-progress leads'],
        ['Leads Lost',       $lostLeads,   'bi-x-circle-fill',   '',       'Need re-engagement'],
    ];
    foreach ($kpis as [$lbl,$val,$ico,$color,$sub]): ?>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card <?= $color ?> p-3">
            <div class="stat-icon bg-<?= $color?>-lt text-<?= $color?> mb-2"><i class="bi <?= $ico ?>"></i></div>
            <div class="stat-value"><?= $val ?></div>
            <div class="stat-label mt-1"><?= $lbl ?></div>
            <div class="text-muted mt-1" style="font-size:0.72rem;"><?= $sub ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Insight Pills -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <?php if ($topSource): ?>
    <div class="d-flex align-items-center gap-2 bg-blue-lt text-blue px-3 py-2 rounded-3" style="font-size:0.82rem;font-weight:600;">
        <i class="bi bi-stars"></i> Best source: <strong><?= htmlspecialchars($topSource['lead_source']) ?></strong> (<?= $topSource['cnt'] ?> leads)
    </div>
    <?php endif; ?>
    <?php if ($topService): ?>
    <div class="d-flex align-items-center gap-2 bg-green-lt text-green px-3 py-2 rounded-3" style="font-size:0.82rem;font-weight:600;">
        <i class="bi bi-briefcase-fill"></i> Top service: <strong><?= htmlspecialchars($topService['service_interest']) ?></strong>
    </div>
    <?php endif; ?>
    <div class="d-flex align-items-center gap-2 bg-orange-lt text-orange px-3 py-2 rounded-3" style="font-size:0.82rem;font-weight:600;">
        <i class="bi bi-funnel-fill"></i> Conv. rate: <strong><?= $convRate ?>%</strong>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart-fill me-2 text-blue"></i>Leads by Source</div>
            <div class="card-body">
                <div class="chart-container" style="height:240px;"><canvas id="pieSource"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-2 text-info"></i>Leads by Status</div>
            <div class="card-body">
                <div class="chart-container" style="height:240px;"><canvas id="barStatus"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Line Chart -->
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-graph-up-arrow me-2 text-green"></i>Monthly New Leads — Last 12 Months</div>
    <div class="card-body">
        <div class="chart-container" style="height:260px;"><canvas id="lineMonthly"></canvas></div>
    </div>
</div>

<!-- Funnel + Tables -->
<div class="row g-3">
    <!-- Conversion Funnel -->
    <div class="col-12 col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-funnel-fill me-2 text-purple"></i>Conversion Funnel</div>
            <div class="card-body">
                <?php
                $funnelColors = ['#4361ee','#06b6d4','#f59e0b','#8b5cf6','#10b981'];
                foreach ($funnelSteps as $fi => $step):
                    $cnt = (int)$funnelCounts[$fi];
                    $pct = $funnelMax > 0 ? max(20, round(($cnt/$funnelMax)*100)) : 20;
                ?>
                <div class="funnel-bar" style="background:<?= $funnelColors[$fi] ?>;width:<?= $pct ?>%;transition:width 0.9s ease <?= $fi*0.1 ?>s;">
                    <?= $step ?> — <?= $cnt ?>
                </div>
                <?php endforeach; ?>
                <div class="text-center mt-3">
                    <span class="stat-trend up"><?= $convRate ?>% Close Rate</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Source Table -->
    <div class="col-12 col-md-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-table me-2"></i>Source Breakdown</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Source</th><th>Leads</th><th>Share</th></tr></thead>
                    <tbody>
                    <?php foreach ($sourceData as $row): ?>
                        <tr>
                            <td class="fw-500"><?= htmlspecialchars($row['label'] ?? '—') ?></td>
                            <td><strong><?= $row['cnt'] ?></strong></td>
                            <td style="min-width:140px;">
                                <?php $pct = $totalLeads > 0 ? round(($row['cnt']/$totalLeads)*100) : 0; ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-grow-1" style="height:6px;background:#f1f4f8;border-radius:99px;overflow:hidden;">
                                        <div style="height:100%;width:<?= $pct ?>%;background:var(--blue);border-radius:99px;"></div>
                                    </div>
                                    <span class="text-muted fs-12 fw-600"><?= $pct ?>%</span>
                                </div>
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
$srcLabels  = array_column($sourceData, 'label');
$srcCounts  = array_column($sourceData, 'cnt');
$statLabels = array_column($statusData, 'label');
$statCounts = array_column($statusData, 'cnt');
$moLabels   = array_column($monthlyData, 'label');
$moCounts   = array_column($monthlyData, 'cnt');

$extraJs = '<script>
(function(){
    const palette=["#4361ee","#10b981","#f59e0b","#ef4444","#8b5cf6","#06b6d4","#fb8500","#1d4ed8"];
    const font = {family:"Inter",size:11};

    new Chart(document.getElementById("pieSource"),{
        type:"doughnut",
        data:{labels:' . json_encode($srcLabels) . ',datasets:[{data:' . json_encode($srcCounts) . ',backgroundColor:palette,borderWidth:3,borderColor:"#fff",hoverOffset:8}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:"right",labels:{font,padding:10,boxWidth:10}},tooltip:{bodyFont:font}}}
    });

    new Chart(document.getElementById("barStatus"),{
        type:"bar",
        data:{labels:' . json_encode($statLabels) . ',datasets:[{label:"Leads",data:' . json_encode($statCounts) . ',backgroundColor:' . json_encode($statColors) . ',borderRadius:7,borderSkipped:false}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{bodyFont:font}},scales:{y:{beginAtZero:true,ticks:{stepSize:1,font},grid:{color:"rgba(0,0,0,0.04)"}},x:{ticks:{font},grid:{display:false}}}}
    });

    new Chart(document.getElementById("lineMonthly"),{
        type:"line",
        data:{labels:' . json_encode($moLabels) . ',datasets:[{label:"New Leads",data:' . json_encode($moCounts) . ',borderColor:"#4361ee",backgroundColor:"rgba(67,97,238,0.08)",fill:true,tension:0.45,pointBackgroundColor:"#4361ee",pointRadius:4,pointHoverRadius:6}]},
        options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{bodyFont:font}},scales:{y:{beginAtZero:true,ticks:{stepSize:1,font},grid:{color:"rgba(0,0,0,0.04)"}},x:{ticks:{font},grid:{display:false}}}}
    });
})();
</script>';

require_once __DIR__ . '/includes/footer.php';
?>
