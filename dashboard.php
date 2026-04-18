<?php
/**
 * Dashboard — overview stats, pipeline, activity feed, charts
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';

// ---- Core Stats ----
$totalLeads  = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$newLeads    = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='New'")->fetchColumn();
$followToday = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE next_followup_date = CURDATE() AND status NOT IN ('Won','Lost')")->fetchColumn();
$bookedLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Booked'")->fetchColumn();
$wonLeads    = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Won'")->fetchColumn();

// ---- Trend: new leads this week vs last week ----
$thisWeek = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$lastWeek = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 14 DAY) AND DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
$weekTrend = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : ($thisWeek > 0 ? 100 : 0);

// ---- Conversion rate ----
$convRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

// ---- Recent 5 leads ----
$recentLeads = $pdo->query("SELECT id, full_name, lead_source, status, created_at, country FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();

// ---- Today follow-ups ----
$todayFollowups = $pdo->query("SELECT id, full_name, phone, status, notes FROM leads WHERE next_followup_date = CURDATE() AND status NOT IN ('Won','Lost') ORDER BY full_name LIMIT 6")->fetchAll();

// ---- Pipeline counts ----
$pipelineData = $pdo->query("SELECT status, COUNT(*) AS cnt FROM leads GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$statuses = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];

// ---- Chart data ----
$sourceRows  = $pdo->query("SELECT lead_source, COUNT(*) AS cnt FROM leads WHERE lead_source IS NOT NULL GROUP BY lead_source ORDER BY cnt DESC LIMIT 6")->fetchAll();
$monthRows   = $pdo->query("SELECT DATE_FORMAT(created_at,'%b %Y') AS mo, COUNT(*) AS cnt FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY MIN(created_at)")->fetchAll();

// ---- Activity Feed (recent lead changes) ----
$activityLeads = $pdo->query("SELECT id, full_name, status, lead_source, created_at, updated_at FROM leads ORDER BY updated_at DESC LIMIT 8")->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Helper: time ago
function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600)  return round($diff/60) . 'm ago';
    if ($diff < 86400) return round($diff/3600) . 'h ago';
    return round($diff/86400) . 'd ago';
}
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <div>
        <h1 class="page-title">Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> 👋</h1>
        <p class="page-subtitle mb-0">Here's what's happening with your leads today.</p>
    </div>
    <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add Lead
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Total Leads',       $totalLeads,  'bi-people-fill',        'bg-blue-lt text-blue',    'blue',
         $weekTrend > 0 ? "+{$weekTrend}% this week" : ($weekTrend < 0 ? "{$weekTrend}% this week" : "No change"),
         $weekTrend > 0 ? 'up' : ($weekTrend < 0 ? 'down' : 'flat'), '+' . $thisWeek . ' this week'],

        ['New Leads',         $newLeads,    'bi-person-plus-fill',   'bg-purple-lt text-purple', 'purple',
         'Awaiting contact', 'flat', 'In pipeline'],

        ['Follow-ups Today',  $followToday, 'bi-calendar-check-fill','bg-orange-lt text-orange', 'orange',
         $followToday > 0 ? 'Action needed' : 'All clear!', $followToday > 0 ? 'down' : 'up', 'Scheduled for today'],

        ['Booked Calls',      $bookedLeads, 'bi-telephone-fill',     'bg-teal-lt text-teal',     'teal',
         'Discovery sessions', 'flat', 'Upcoming calls'],

        ['Converted Clients', $wonLeads,    'bi-trophy-fill',        'bg-green-lt text-green',   'green',
         $convRate . '% conv. rate', 'up', 'Paying clients'],
    ];
    foreach ($cards as [$label, $value, $icon, $iconClass, $colorClass, $trend, $trendDir, $sub]): ?>
    <div class="col-6 col-xl-2-4">
        <div class="card stat-card <?= $colorClass ?>">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <div class="stat-icon <?= $iconClass ?>">
                    <i class="bi <?= $icon ?>"></i>
                </div>
                <span class="stat-trend <?= $trendDir ?>"><?= $trendDir === 'up' ? '↑' : ($trendDir === 'down' ? '↓' : '—') ?> <?= $trend ?></span>
            </div>
            <div class="stat-value" data-count><?= $value ?></div>
            <div class="stat-label mt-1"><?= $label ?></div>
            <div class="text-muted mt-1" style="font-size:0.72rem;"><?= $sub ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Row: Charts -->
<div class="row g-3 mb-4">
    <!-- Source Donut -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-blue"></i>Leads by Source
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:200px;"><canvas id="sourceChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Monthly Bar -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-2 text-green"></i>New Leads — Last 6 Months
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:200px;"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Pipeline Mini -->
    <div class="col-12 col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-kanban me-2 text-purple"></i>Pipeline
            </div>
            <div class="card-body">
                <?php
                $pipeColors = ['New'=>'#4361ee','Contacted'=>'#06b6d4','Follow-up Scheduled'=>'#f59e0b','Booked'=>'#8b5cf6','Won'=>'#10b981','Lost'=>'#ef4444'];
                $maxPipeline = max(array_values($pipelineData) ?: [1]);
                foreach ($statuses as $s):
                    $cnt = $pipelineData[$s] ?? 0;
                    $pct = $maxPipeline > 0 ? round(($cnt/$maxPipeline)*100) : 0;
                    $color = $pipeColors[$s];
                ?>
                <div class="pipeline-bar-wrap">
                    <div class="pipeline-label">
                        <span class="fw-500" style="font-size:0.78rem;"><?= $s ?></span>
                        <span class="fw-700" style="font-size:0.78rem;color:<?= $color ?>;"><?= $cnt ?></span>
                    </div>
                    <div class="pipeline-bar-track">
                        <div class="pipeline-bar-fill" data-width="<?= $pct ?>" style="background:<?= $color ?>;width:0%;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Row: Recent Leads + Activity + Follow-ups -->
<div class="row g-3">

    <!-- Recent Leads -->
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-header justify-content-between">
                <span><i class="bi bi-clock-history me-2 text-blue"></i>Recent Leads</span>
                <a href="<?= BASE_URL ?>leads.php" class="btn btn-sm btn-outline-primary ms-auto" style="font-size:0.78rem;border-radius:6px;">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentLeads)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                        <h6>No leads yet</h6>
                        <p>Add your first lead to get started.</p>
                        <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary btn-sm">Add Lead</a>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Name</th><th>Source</th><th>Status</th><th>Added</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentLeads as $lead): ?>
                            <tr>
                                <td>
                                    <a href="<?= BASE_URL ?>lead_form.php?id=<?= $lead['id'] ?>" class="fw-600 text-decoration-none text-dark" style="font-weight:600;">
                                        <?= htmlspecialchars($lead['full_name']) ?>
                                    </a>
                                    <?php if ($lead['country']): ?>
                                        <div class="text-muted" style="font-size:0.72rem;"><?= htmlspecialchars($lead['country']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size:0.72rem;font-weight:500;">
                                        <?= htmlspecialchars($lead['lead_source'] ?? '—') ?>
                                    </span>
                                </td>
                                <td><span class="status-badge status-<?= str_replace(' ','-',$lead['status']) ?>"><?= $lead['status'] ?></span></td>
                                <td class="text-muted" style="font-size:0.78rem;white-space:nowrap;"><?= date('d M', strtotime($lead['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-activity me-2 text-purple"></i>Recent Activity
            </div>
            <div class="activity-feed">
                <?php
                $actColors = ['New'=>'bg-blue-lt text-blue','Contacted'=>'bg-teal-lt text-teal','Won'=>'bg-green-lt text-green','Lost'=>'bg-red-lt text-red','Booked'=>'bg-purple-lt text-purple','Follow-up Scheduled'=>'bg-orange-lt text-orange'];
                $actIcons  = ['New'=>'bi-person-plus','Contacted'=>'bi-telephone','Won'=>'bi-trophy','Lost'=>'bi-x-circle','Booked'=>'bi-calendar-check','Follow-up Scheduled'=>'bi-clock'];
                foreach ($activityLeads as $act):
                    $cls  = $actColors[$act['status']] ?? 'bg-blue-lt text-blue';
                    $ico  = $actIcons[$act['status']]  ?? 'bi-circle';
                    $verb = $act['status'] === 'Won' ? 'converted to client' : ('status: ' . strtolower($act['status']));
                ?>
                <div class="activity-item">
                    <div class="activity-dot <?= $cls ?>"><i class="bi <?= $ico ?>"></i></div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong><?= htmlspecialchars($act['full_name']) ?></strong> — <?= $verb ?>
                        </div>
                        <div class="activity-time"><?= timeAgo($act['updated_at']) ?> · via <?= htmlspecialchars($act['lead_source'] ?? 'direct') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Today Follow-ups -->
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header justify-content-between">
                <span><i class="bi bi-calendar2-check-fill me-2 text-orange"></i>Due Today</span>
                <a href="<?= BASE_URL ?>followups.php" class="btn btn-sm btn-outline-warning ms-auto" style="font-size:0.78rem;border-radius:6px;">All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($todayFollowups)): ?>
                    <div class="empty-state py-4">
                        <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
                        <h6>All clear!</h6>
                        <p>No follow-ups due today.</p>
                    </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($todayFollowups as $f): ?>
                    <li class="list-group-item border-0 border-bottom py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div style="min-width:0;">
                                <div class="fw-600 text-truncate" style="font-size:0.82rem;font-weight:600;"><?= htmlspecialchars($f['full_name']) ?></div>
                                <div class="text-muted" style="font-size:0.72rem;"><?= htmlspecialchars($f['phone'] ?? 'No phone') ?></div>
                            </div>
                            <a href="<?= BASE_URL ?>lead_form.php?id=<?= $f['id'] ?>" class="btn btn-outline-primary btn-action flex-shrink-0" data-bs-toggle="tooltip" data-bs-title="Edit lead">
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

</div><!-- /row -->

<?php
$srcLabels = array_column($sourceRows, 'lead_source');
$srcCounts = array_column($sourceRows, 'cnt');
$moLabels  = array_column($monthRows, 'mo');
$moCounts  = array_column($monthRows, 'cnt');

$extraJs = '<script>
(function(){
    const palette = ["#4361ee","#10b981","#f59e0b","#ef4444","#8b5cf6","#06b6d4"];

    new Chart(document.getElementById("sourceChart"), {
        type:"doughnut",
        data:{
            labels:' . json_encode($srcLabels) . ',
            datasets:[{data:' . json_encode($srcCounts) . ',backgroundColor:palette,borderWidth:3,borderColor:"#fff",hoverOffset:6}]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            plugins:{legend:{position:"bottom",labels:{font:{size:11,family:"Inter"},padding:8,boxWidth:10}},tooltip:{bodyFont:{family:"Inter"}}}
        }
    });

    new Chart(document.getElementById("monthlyChart"),{
        type:"bar",
        data:{
            labels:' . json_encode($moLabels) . ',
            datasets:[{label:"Leads",data:' . json_encode($moCounts) . ',backgroundColor:"rgba(67,97,238,0.15)",borderColor:"#4361ee",borderWidth:2,borderRadius:6,borderSkipped:false}]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false}},
            scales:{
                y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11,family:"Inter"}},grid:{color:"rgba(0,0,0,0.04)"}},
                x:{ticks:{font:{size:11,family:"Inter"}},grid:{display:false}}
            }
        }
    });
})();
</script>';

require_once __DIR__ . '/includes/footer.php';
?>
