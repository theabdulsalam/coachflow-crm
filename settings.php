<?php
/**
 * Settings — Profile, security, preferences, branding
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Settings';
$user = currentUser();
$flashMsg = ''; $flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($name === '' || $email === '') {
            $flashMsg = 'Name and email are required.'; $flashType = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $flashMsg = 'Invalid email format.'; $flashType = 'danger';
        } else {
            $chk = $pdo->prepare("SELECT id FROM users WHERE email=? AND id!=?");
            $chk->execute([$email, $user['id']]);
            if ($chk->fetch()) {
                $flashMsg = 'Email already taken by another account.'; $flashType = 'danger';
            } else {
                $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?")->execute([$name, $email, $user['id']]);
                $_SESSION['user_name'] = $name; $_SESSION['user_email'] = $email;
                $flashMsg = 'Profile updated successfully.';
            }
        }
    }

    if ($action === 'change_password') {
        $curr    = $_POST['current_password']  ?? '';
        $newPwd  = $_POST['new_password']       ?? '';
        $confirm = $_POST['confirm_password']   ?? '';
        $pwStmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
        $pwStmt->execute([$user['id']]);
        $currentHash = $pwStmt->fetchColumn();
        if (!password_verify($curr, $currentHash)) {
            $flashMsg = 'Current password is incorrect.'; $flashType = 'danger';
        } elseif (strlen($newPwd) < 6) {
            $flashMsg = 'New password must be at least 6 characters.'; $flashType = 'danger';
        } elseif ($newPwd !== $confirm) {
            $flashMsg = 'Passwords do not match.'; $flashType = 'danger';
        } else {
            $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($newPwd, PASSWORD_DEFAULT), $user['id']]);
            $flashMsg = 'Password changed successfully.';
        }
    }
}

$uStmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$uStmt->execute([$user['id']]);
$userData = $uStmt->fetch();

// Fallback to session data if row not found (e.g. session/DB mismatch after reimport)
if (!$userData) {
    $userData = [
        'id'         => $user['id'],
        'name'       => $_SESSION['user_name']  ?? 'Admin',
        'email'      => $_SESSION['user_email'] ?? '',
        'password'   => '',
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

$leadSources = ['Website','Instagram','Facebook','LinkedIn','YouTube','Referral','Cold Outreach','Other'];
$services    = ['Business Coaching','Life Coaching','Executive Coaching','Career Coaching','Mindset Coaching','Health Coaching','Business Strategy','Other'];
$statuses    = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];

// CRM stats
$stats = [
    ['Total Leads',   $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn()],
    ['Won Clients',   $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Won'")->fetchColumn()],
    ['Active Leads',  $pdo->query("SELECT COUNT(*) FROM leads WHERE status NOT IN ('Won','Lost')")->fetchColumn()],
    ['Member Since',  date('d M Y', strtotime($userData['created_at']))],
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Header -->
<div class="mb-4">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle mb-0">Manage your profile, security, and CRM preferences.</p>
</div>

<?php if ($flashMsg): ?>
<div class="alert alert-<?= $flashType ?> alert-auto-dismiss alert-dismissible d-flex align-items-center mb-4" style="border-radius:10px;">
    <i class="bi bi-<?= $flashType==='success'?'check-circle-fill':'exclamation-triangle-fill' ?> me-2"></i>
    <?= htmlspecialchars($flashMsg) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Panel -->
    <div class="col-12 col-lg-4">

        <!-- Profile Card -->
        <div class="card mb-3 text-center">
            <div class="card-body py-4">
                <div class="photo-placeholder mx-auto mb-3">
                    <?= strtoupper(substr($userData['name'],0,1)) ?>
                    <div class="photo-overlay"><i class="bi bi-camera-fill text-white"></i></div>
                </div>
                <h5 class="fw-800 mb-0"><?= htmlspecialchars($userData['name']) ?></h5>
                <p class="text-muted" style="font-size:0.82rem;"><?= htmlspecialchars($userData['email']) ?></p>
                <span class="badge" style="background:var(--blue-lt);color:var(--blue);font-weight:600;border-radius:20px;padding:.35em .9em;">
                    <i class="bi bi-shield-check me-1"></i>Admin
                </span>
            </div>
        </div>

        <!-- CRM Stats -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-bar-chart me-2 text-blue"></i>Your CRM Stats</div>
            <div class="card-body p-0">
                <?php foreach ($stats as [$lbl, $val]): ?>
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="font-size:0.845rem;">
                    <span class="text-muted"><?= $lbl ?></span>
                    <span class="fw-700"><?= $val ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Nav -->
        <div class="card">
            <div class="card-header"><i class="bi bi-lightning me-2 text-orange"></i>Quick Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>lead_form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-2"></i>Add New Lead</a>
                <a href="<?= BASE_URL ?>reports_export.php" class="btn btn-outline-success btn-sm"><i class="bi bi-download me-2"></i>Export Leads CSV</a>
                <a href="<?= BASE_URL ?>reports.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-bar-chart me-2"></i>View Reports</a>
            </div>
        </div>

    </div>

    <!-- Right Panel -->
    <div class="col-12 col-lg-8">

        <!-- Profile Settings -->
        <div class="card mb-4 settings-section">
            <div class="card-header"><i class="bi bi-person-fill me-2 text-blue"></i>Profile Information</div>
            <div class="card-body">
                <div class="settings-label-group">
                    <h6>Personal Details</h6>
                    <p>Update your name and email address.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($userData['name']) ?>" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security -->
        <div class="card mb-4 settings-section">
            <div class="card-header"><i class="bi bi-shield-lock-fill me-2 text-orange"></i>Security</div>
            <div class="card-body">
                <div class="settings-label-group">
                    <h6>Change Password</h6>
                    <p>Use a strong password with at least 6 characters.</p>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required placeholder="Enter current password">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" minlength="6" required placeholder="Min. 6 characters">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required placeholder="Repeat new password">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning text-white btn-sm"><i class="bi bi-lock me-1"></i>Change Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Branding -->
        <div class="card mb-4 settings-section">
            <div class="card-header"><i class="bi bi-palette-fill me-2 text-purple"></i>Branding</div>
            <div class="card-body">
                <div class="settings-label-group">
                    <h6>Company Details</h6>
                    <p>Customise how your CRM is identified. (Connect to database to persist.)</p>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Company / Brand Name</label>
                        <input type="text" class="form-control" placeholder="e.g. Sarah's Coaching Studio" value="CoachFlow CRM">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label">Timezone</label>
                        <select class="form-select">
                            <option selected>UTC +00:00 (London)</option>
                            <option>UTC +01:00 (Paris, Berlin)</option>
                            <option>UTC +04:00 (Dubai, UAE)</option>
                            <option>UTC +05:30 (India, IST)</option>
                            <option>UTC -05:00 (New York, EST)</option>
                            <option>UTC -08:00 (Los Angeles, PST)</option>
                            <option>UTC +10:00 (Sydney, AEST)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Logo <span class="badge bg-light text-muted border ms-1">Placeholder</span></label>
                        <input type="file" class="form-control" accept="image/*" disabled>
                        <small class="text-muted">Logo upload requires file storage configuration.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- CRM Preferences -->
        <div class="card settings-section">
            <div class="card-header"><i class="bi bi-sliders me-2 text-teal"></i>CRM Preferences</div>
            <div class="card-body">
                <div class="settings-label-group">
                    <h6>Dropdown Values</h6>
                    <p>These are the options shown in the lead form. Edit the arrays in <code>lead_form.php</code> to customise.</p>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Lead Sources</label>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($leadSources as $s): ?>
                                <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($s) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Services</label>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($services as $s): ?>
                                <span class="badge bg-light text-dark border" style="font-size:0.72rem;"><?= htmlspecialchars($s) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Status Labels</label>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($statuses as $s): ?>
                                <span class="status-badge status-<?= str_replace(' ','-',$s) ?>"><?= $s ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
