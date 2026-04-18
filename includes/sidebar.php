<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<nav id="sidebar" class="sidebar">
    <!-- Brand -->
    <a href="<?= BASE_URL ?>dashboard.php" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <div class="brand-text">CoachFlow <span>CRM</span></div>
            <div class="brand-tagline">Lead Management System</div>
        </div>
    </a>

    <div class="sidebar-section-label">Main Menu</div>

    <ul class="nav flex-column px-1 flex-grow-1">
        <li class="nav-item">
            <a href="<?= BASE_URL ?>dashboard.php"
               class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>leads.php"
               class="nav-link <?= in_array($currentPage, ['leads.php','lead_form.php']) ? 'active' : '' ?>">
                <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                <span>Leads</span>
                <?php
                $totalCount = $pdo->query("SELECT COUNT(*) FROM leads WHERE status='New'")->fetchColumn();
                if ($totalCount > 0): ?>
                    <span class="badge bg-primary ms-auto rounded-pill"><?= $totalCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>followups.php"
               class="nav-link <?= $currentPage === 'followups.php' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="bi bi-calendar-check-fill"></i></span>
                <span>Follow-ups</span>
                <?php
                $overdueCount = (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE next_followup_date <= CURDATE() AND status NOT IN ('Won','Lost')")->fetchColumn();
                if ($overdueCount > 0): ?>
                    <span class="badge bg-danger ms-auto rounded-pill"><?= $overdueCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>reports.php"
               class="nav-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="bi bi-bar-chart-fill"></i></span>
                <span>Reports</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-section-label" style="margin-top:auto;">Account</div>
    <ul class="nav flex-column px-1 mb-2">
        <li class="nav-item">
            <a href="<?= BASE_URL ?>settings.php"
               class="nav-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>
                <span>Settings</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>logout.php" class="nav-link logout-link">
                <span class="nav-icon"><i class="bi bi-box-arrow-left"></i></span>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<!-- ============================================================
     CONTENT WRAPPER
     ============================================================ -->
<div class="content-wrapper">

    <!-- Top Navbar -->
    <nav class="top-navbar">
        <button class="btn-sidebar-toggle" id="sidebarToggle" type="button" data-bs-toggle="tooltip" data-bs-title="Toggle sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="navbar-breadcrumb d-none d-md-flex align-items-center gap-1">
            <a href="<?= BASE_URL ?>dashboard.php" class="text-decoration-none text-muted">Home</a>
            <i class="bi bi-chevron-right" style="font-size:0.65rem;opacity:0.5;"></i>
            <span class="current"><?= htmlspecialchars($pageTitle ?? '') ?></span>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <!-- Date -->
            <span class="text-muted d-none d-lg-inline" style="font-size:0.78rem;">
                <i class="bi bi-calendar3 me-1"></i><?= date('D, d M Y') ?>
            </span>

            <!-- Notifications bell — follow-ups -->
            <?php if ($overdueCount > 0): ?>
            <a href="<?= BASE_URL ?>followups.php"
               class="btn btn-sm position-relative d-flex align-items-center justify-content-center"
               style="width:34px;height:34px;background:var(--red-lt);border:1px solid rgba(239,68,68,0.2);border-radius:8px;color:var(--red);"
               data-bs-toggle="tooltip" data-bs-title="<?= $overdueCount ?> overdue follow-up<?= $overdueCount>1?'s':'' ?>">
                <i class="bi bi-bell-fill"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;"><?= $overdueCount ?></span>
            </a>
            <?php endif; ?>

            <!-- Quick add -->
            <a href="<?= BASE_URL ?>lead_form.php"
               class="btn btn-primary btn-sm d-none d-md-flex align-items-center gap-1"
               style="border-radius:8px;font-size:0.8rem;">
                <i class="bi bi-plus-lg"></i> Add Lead
            </a>

            <!-- User dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 border"
                        style="border-radius:8px;background:white;border-color:var(--border)!important;font-size:0.8rem;font-weight:600;"
                        type="button" data-bs-toggle="dropdown">
                    <span class="avatar-circle" style="width:26px;height:26px;font-size:0.7rem;">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                    </span>
                    <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:10px;min-width:160px;">
                    <li class="px-3 py-2 border-bottom">
                        <div style="font-size:0.78rem;font-weight:700;color:var(--text-primary);"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                        <div style="font-size:0.72rem;color:var(--text-muted);"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></div>
                    </li>
                    <li><a class="dropdown-item" style="font-size:0.82rem;" href="<?= BASE_URL ?>settings.php"><i class="bi bi-person me-2 text-muted"></i>Profile</a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item text-danger" style="font-size:0.82rem;" href="<?= BASE_URL ?>logout.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
