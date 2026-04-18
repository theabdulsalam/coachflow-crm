<?php
/**
 * Settings — Profile, password change, CRM preferences
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Settings';
$user      = currentUser();
$message   = '';
$msgType   = 'success';

// ---- Update Profile ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_profile') {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $message = 'Name and email are required.';
            $msgType = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $msgType = 'danger';
        } else {
            // Check email not taken by another user
            $chk = $pdo->prepare("SELECT id FROM users WHERE email=? AND id != ?");
            $chk->execute([$email, $user['id']]);
            if ($chk->fetch()) {
                $message = 'That email is already in use by another account.';
                $msgType = 'danger';
            } else {
                $upd = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?");
                $upd->execute([$name, $email, $user['id']]);
                $_SESSION['user_name']  = $name;
                $_SESSION['user_email'] = $email;
                $message = 'Profile updated successfully.';
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current  =       $_POST['current_password']  ?? '';
        $newPwd   =       $_POST['new_password']       ?? '';
        $confirm  =       $_POST['confirm_password']   ?? '';

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
        $stmt->execute([$user['id']]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($current, $hash)) {
            $message = 'Current password is incorrect.';
            $msgType = 'danger';
        } elseif (strlen($newPwd) < 6) {
            $message = 'New password must be at least 6 characters.';
            $msgType = 'danger';
        } elseif ($newPwd !== $confirm) {
            $message = 'New passwords do not match.';
            $msgType = 'danger';
        } else {
            $upd = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $upd->execute([password_hash($newPwd, PASSWORD_DEFAULT), $user['id']]);
            $message = 'Password changed successfully.';
        }
    }
}

// Reload user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user['id']]);
$userData = $stmt->fetch();

// Preferences (static config)
$leadSources = ['Website','Instagram','Facebook','LinkedIn','YouTube','Referral','Cold Outreach','Other'];
$services    = ['Business Coaching','Life Coaching','Executive Coaching','Career Coaching','Mindset Coaching','Health Coaching','Business Strategy','Other'];
$statuses    = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="mb-4">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle mb-0">Manage your profile and CRM preferences.</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $msgType ?> alert-auto-dismiss alert-dismissible d-flex align-items-center">
        <i class="bi bi-<?= $msgType === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left -->
    <div class="col-12 col-lg-4">

        <!-- Avatar Card -->
        <div class="card text-center mb-3">
            <div class="card-body py-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold"
                     style="width:80px;height:80px;font-size:2rem;">
                    <?= strtoupper(substr($userData['name'], 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($userData['name']) ?></h5>
                <p class="text-muted small mb-0"><?= htmlspecialchars($userData['email']) ?></p>
                <span class="badge bg-primary mt-2">Admin</span>
            </div>
        </div>

        <!-- CRM Stats -->
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-primary"></i>CRM Stats</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <?php
                    $statRows = [
                        ['Total Leads',   $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn()],
                        ['Won Clients',   $pdo->query("SELECT COUNT(*) FROM leads WHERE status='Won'")->fetchColumn()],
                        ['Active Leads',  $pdo->query("SELECT COUNT(*) FROM leads WHERE status NOT IN ('Won','Lost')")->fetchColumn()],
                        ['Member Since',  date('d M Y', strtotime($userData['created_at']))],
                    ];
                    foreach ($statRows as [$lbl, $val]): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted"><?= $lbl ?></span>
                        <span class="fw-semibold"><?= $val ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div><!-- /Left -->

    <!-- Right -->
    <div class="col-12 col-lg-8">

        <!-- Profile Form -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person-fill me-2 text-primary"></i>Edit Profile</div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name"
                                   value="<?= htmlspecialchars($userData['name']) ?>" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($userData['email']) ?>" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Form -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-shield-lock-fill me-2 text-warning"></i>Change Password</div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="change_password">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" minlength="6" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="bi bi-lock me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CRM Preferences (read-only view) -->
        <div class="card">
            <div class="card-header"><i class="bi bi-sliders me-2 text-info"></i>CRM Preferences</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Below are the current dropdown values used across the CRM. Edit the <code>lead_form.php</code> arrays to customise.</p>

                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Lead Sources</label>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($leadSources as $src): ?>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($src) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Services</label>
                        <div class="d-flex flex-wrap gap-1">
                            <?php foreach ($services as $sv): ?>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($sv) ?></span>
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

    </div><!-- /Right -->
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
