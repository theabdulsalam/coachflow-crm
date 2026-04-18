<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<nav id="sidebar" class="sidebar d-flex flex-column flex-shrink-0">
    <!-- Brand -->
    <a href="<?= BASE_URL ?>dashboard.php" class="sidebar-brand d-flex align-items-center text-decoration-none px-3 py-4">
        <i class="bi bi-briefcase-fill me-2 fs-4 text-warning"></i>
        <span class="fw-bold fs-5 text-white">CoachFlow <span class="text-warning">CRM</span></span>
    </a>

    <hr class="sidebar-divider mx-3 my-0">

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mt-2 px-2 flex-grow-1">
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>dashboard.php"
               class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>leads.php"
               class="nav-link <?= in_array($currentPage, ['leads.php','lead_form.php']) ? 'active' : '' ?>">
                <i class="bi bi-people-fill me-2"></i> Leads
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>followups.php"
               class="nav-link <?= $currentPage === 'followups.php' ? 'active' : '' ?>">
                <i class="bi bi-calendar-check-fill me-2"></i> Follow-ups
                <?php
                // Badge for overdue follow-ups
                global $pdo;
                $stmt = $pdo->query("SELECT COUNT(*) FROM leads WHERE next_followup_date < CURDATE() AND status NOT IN ('Won','Lost')");
                $overdue = (int) $stmt->fetchColumn();
                if ($overdue > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $overdue ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>reports.php"
               class="nav-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill me-2"></i> Reports
            </a>
        </li>

        <li class="nav-item mt-auto mb-1">
            <hr class="sidebar-divider">
        </li>
        <li class="nav-item mb-1">
            <a href="<?= BASE_URL ?>settings.php"
               class="nav-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
                <i class="bi bi-gear-fill me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item mb-3">
            <a href="<?= BASE_URL ?>logout.php" class="nav-link text-danger-soft">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<!-- Page Content Wrapper -->
<div class="content-wrapper flex-grow-1 d-flex flex-column min-vh-100">
    <!-- Top Navbar -->
    <nav class="top-navbar navbar navbar-expand-lg px-4 py-2">
        <button class="btn btn-sm btn-sidebar-toggle me-3" id="sidebarToggle" type="button">
            <i class="bi bi-list fs-4"></i>
        </button>
        <span class="navbar-brand fw-semibold text-muted small d-none d-md-inline">
            <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
        </span>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-calendar3 me-1"></i>
                <?= date('D, d M Y') ?>
            </span>
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button"
                        data-bs-toggle="dropdown">
                    <span class="avatar-circle">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                    </span>
                    <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>settings.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content flex-grow-1 p-4">
