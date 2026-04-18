<?php
/**
 * Add / Edit Lead Form — dual mode (create & update)
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;
$pageTitle = $isEdit ? 'Edit Lead' : 'Add Lead';

$errors = [];
$lead   = [
    'full_name'          => '',
    'email'              => '',
    'phone'              => '',
    'whatsapp'           => '',
    'country'            => '',
    'service_interest'   => '',
    'lead_source'        => '',
    'status'             => 'New',
    'next_followup_date' => '',
    'notes'              => '',
];

// Load existing lead for edit
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        header('Location: ' . BASE_URL . 'leads.php');
        exit;
    }
    $lead = $row;
}

// ---- Handle form submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect & sanitize
    $lead['full_name']          = trim($_POST['full_name']          ?? '');
    $lead['email']              = trim($_POST['email']              ?? '');
    $lead['phone']              = trim($_POST['phone']              ?? '');
    $lead['whatsapp']           = trim($_POST['whatsapp']           ?? '');
    $lead['country']            = trim($_POST['country']            ?? '');
    $lead['service_interest']   = trim($_POST['service_interest']   ?? '');
    $lead['lead_source']        = trim($_POST['lead_source']        ?? '');
    $lead['status']             = trim($_POST['status']             ?? 'New');
    $lead['next_followup_date'] = trim($_POST['next_followup_date'] ?? '');
    $lead['notes']              = trim($_POST['notes']              ?? '');

    // Validate
    if ($lead['full_name'] === '') {
        $errors['full_name'] = 'Full name is required.';
    }
    if ($lead['email'] !== '' && !filter_var($lead['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (empty($errors)) {
        $followDate = $lead['next_followup_date'] !== '' ? $lead['next_followup_date'] : null;

        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE leads SET
                full_name=?, email=?, phone=?, whatsapp=?, country=?,
                service_interest=?, lead_source=?, status=?,
                next_followup_date=?, notes=?, updated_at=NOW()
                WHERE id=?");
            $stmt->execute([
                $lead['full_name'], $lead['email'], $lead['phone'], $lead['whatsapp'],
                $lead['country'], $lead['service_interest'], $lead['lead_source'],
                $lead['status'], $followDate, $lead['notes'], $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO leads
                (full_name, email, phone, whatsapp, country, service_interest, lead_source, status, next_followup_date, notes)
                VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $lead['full_name'], $lead['email'], $lead['phone'], $lead['whatsapp'],
                $lead['country'], $lead['service_interest'], $lead['lead_source'],
                $lead['status'], $followDate, $lead['notes']
            ]);
        }

        header('Location: ' . BASE_URL . 'leads.php?saved=1');
        exit;
    }
}

$statuses  = ['New','Contacted','Follow-up Scheduled','Booked','Won','Lost'];
$sources   = ['Website','Instagram','Facebook','LinkedIn','YouTube','Referral','Cold Outreach','Other'];
$services  = ['Business Coaching','Life Coaching','Executive Coaching','Career Coaching','Mindset Coaching','Health Coaching','Business Strategy','Other'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Page Heading -->
<div class="d-flex align-items-center mb-4 gap-3">
    <a href="<?= BASE_URL ?>leads.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="page-title"><?= $isEdit ? 'Edit Lead' : 'Add New Lead' ?></h1>
        <p class="page-subtitle mb-0"><?= $isEdit ? 'Update lead information' : 'Enter details for the new lead' ?></p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
        <div>
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<form method="POST" action="" novalidate>
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">

            <!-- Contact Details -->
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person-vcard me-2 text-primary"></i>Contact Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>"
                                   name="full_name" value="<?= htmlspecialchars($lead['full_name']) ?>"
                                   placeholder="e.g. Sarah Johnson" required>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   name="email" value="<?= htmlspecialchars($lead['email']) ?>"
                                   placeholder="email@example.com">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone"
                                   value="<?= htmlspecialchars($lead['phone']) ?>" placeholder="+1-555-0100">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">WhatsApp Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" class="form-control" name="whatsapp"
                                       value="<?= htmlspecialchars($lead['whatsapp']) ?>" placeholder="+1-555-0100">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country"
                                   value="<?= htmlspecialchars($lead['country']) ?>" placeholder="e.g. United States">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lead Details -->
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-funnel-fill me-2 text-info"></i>Lead Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Service Interested In</label>
                            <select class="form-select" name="service_interest">
                                <option value="">— Select Service —</option>
                                <?php foreach ($services as $sv): ?>
                                    <option value="<?= $sv ?>" <?= $lead['service_interest'] === $sv ? 'selected' : '' ?>><?= $sv ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Lead Source</label>
                            <select class="form-select" name="lead_source">
                                <option value="">— Select Source —</option>
                                <?php foreach ($sources as $src): ?>
                                    <option value="<?= $src ?>" <?= $lead['lead_source'] === $src ? 'selected' : '' ?>><?= $src ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="4"
                                      placeholder="Add any notes, context, or lead details here…"><?= htmlspecialchars($lead['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /Left Column -->

        <!-- Right Column -->
        <div class="col-12 col-lg-4">

            <!-- Pipeline -->
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-kanban me-2 text-warning"></i>Pipeline</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Next Follow-up Date</label>
                        <input type="date" class="form-control" name="next_followup_date"
                               value="<?= htmlspecialchars($lead['next_followup_date']) ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Update Lead' : 'Save Lead' ?>
                        </button>
                        <a href="<?= BASE_URL ?>leads.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-2"></i>Cancel
                        </a>
                        <?php if ($isEdit): ?>
                            <a href="<?= BASE_URL ?>leads.php?delete=<?= $id ?>"
                               class="btn btn-outline-danger btn-delete-lead"
                               data-name="<?= htmlspecialchars($lead['full_name']) ?>">
                                <i class="bi bi-trash me-2"></i>Delete Lead
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <!-- Meta -->
            <div class="card mt-3">
                <div class="card-body py-3">
                    <p class="mb-1 small text-muted"><i class="bi bi-calendar-plus me-1"></i>
                        Added: <?= date('d M Y H:i', strtotime($lead['created_at'])) ?>
                    </p>
                    <p class="mb-0 small text-muted"><i class="bi bi-calendar-check me-1"></i>
                        Updated: <?= date('d M Y H:i', strtotime($lead['updated_at'])) ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- /Right Column -->
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
