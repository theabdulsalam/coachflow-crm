<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoachFlow CRM — Custom Lead Management for Coaches & Consultants</title>
    <meta name="description" content="A fully custom CRM system built for coaches and consultants. Track leads, follow-ups, and conversions in one professional dashboard.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        .showcase-nav {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(15,23,41,0.92); backdrop-filter: blur(12px);
            z-index: 999; padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .stat-pill {
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
            border-radius: 40px; padding: 0.5rem 1.25rem;
            color: rgba(255,255,255,0.8); font-size: 0.82rem; font-weight: 500;
        }
        .stat-pill strong { color: white; }
        .section-title { font-size: clamp(1.6rem, 4vw, 2.2rem); font-weight: 800; letter-spacing: -0.03em; }
        .section-sub { color: var(--text-muted); font-size: 1rem; max-width: 560px; margin: 0.5rem auto 0; line-height: 1.7; }

        /* Mockup screen */
        .browser-mockup {
            background: var(--surface);
            border-radius: 14px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3), 0 8px 24px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .browser-topbar {
            background: #1e2940;
            padding: 10px 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .browser-dot { width:11px;height:11px;border-radius:50%;flex-shrink:0; }
        .browser-url {
            flex:1; background:rgba(255,255,255,0.08); border-radius:6px;
            padding:4px 12px; color:rgba(255,255,255,0.45); font-size:0.72rem;
            font-family:'Inter',sans-serif;
        }

        /* Dashboard mockup inside */
        .mock-sidebar {
            width: 56px; background: #0f1729; height: 100%;
            display: flex; flex-direction: column; align-items: center;
            padding: 14px 0; gap: 12px;
        }
        .mock-icon { width:28px;height:28px;background:rgba(255,255,255,0.08);border-radius:7px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.5);font-size:0.75rem; }
        .mock-icon.active { background:#4361ee;color:white; }
        .mock-main { flex:1; padding:12px; background:#f4f6fb; overflow:hidden; }
        .mock-topbar { background:white;border-radius:7px;padding:8px 12px;margin-bottom:10px;display:flex;align-items:center;gap:8px; }
        .mock-stat { background:white;border-radius:8px;padding:10px;flex:1; }
        .mock-stat .val { font-size:1.1rem;font-weight:800;color:#111; }
        .mock-stat .lbl { font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af; }
        .mock-accent { width:100%;height:3px;border-radius:3px;margin-bottom:6px; }
        .mock-table-row { background:white;border-radius:6px;padding:7px 10px;margin-bottom:5px;display:flex;align-items:center;gap:8px; }
        .mock-badge { border-radius:20px;padding:2px 8px;font-size:0.6rem;font-weight:700; }

        /* Testimonial */
        .testimonial-card {
            background:var(--surface);border:1px solid var(--border);border-radius:14px;
            padding:1.5rem;box-shadow:var(--shadow-sm);
        }
        .testimonial-avatar {
            width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;
            font-weight:800;font-size:1rem;color:white;flex-shrink:0;
        }

        .pricing-card {
            background:var(--surface);border:2px solid var(--border);border-radius:16px;padding:2rem;
            transition:all 0.25s ease;
        }
        .pricing-card.featured { border-color:var(--blue);box-shadow:0 0 0 4px var(--blue-lt); }
        .pricing-card:hover { transform:translateY(-4px);box-shadow:var(--shadow-lg); }
        .price-tag { font-size:2.8rem;font-weight:900;letter-spacing:-0.04em;color:var(--text-primary); }
    </style>
</head>
<body style="background:#fff;">

<!-- Fixed Nav -->
<nav class="showcase-nav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#4361ee,#8b5cf6);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-briefcase-fill text-white" style="font-size:0.9rem;"></i>
            </div>
            <span style="font-weight:800;color:white;font-family:Inter,sans-serif;">CoachFlow <span style="color:#f59e0b;">CRM</span></span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="#features" class="text-decoration-none d-none d-md-inline" style="color:rgba(255,255,255,0.6);font-size:0.845rem;font-weight:500;">Features</a>
            <a href="#showcase" class="text-decoration-none d-none d-md-inline ms-3" style="color:rgba(255,255,255,0.6);font-size:0.845rem;font-weight:500;">Preview</a>
            <a href="login.php" class="btn btn-primary btn-sm ms-3" style="border-radius:8px;">
                <i class="bi bi-box-arrow-in-right me-1"></i> Live Demo
            </a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="showcase-hero" style="padding-top:6rem;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6">
                <div class="hero-badge mb-3">
                    <i class="bi bi-stars"></i> Portfolio Project — Built for Coaches & Consultants
                </div>
                <h1 class="hero-title mb-3">
                    Manage Leads.<br>Close More <span>Clients.</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    CoachFlow CRM is a fully custom lead management system designed specifically for coaches, consultants, and solo service businesses. Track every lead, follow-up, and conversion — in one clean dashboard.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="login.php" class="btn btn-warning fw-700 px-4 py-2" style="border-radius:10px;color:#1a1f36;font-size:0.925rem;">
                        <i class="bi bi-play-circle-fill me-2"></i> Try Live Demo
                    </a>
                    <a href="#features" class="btn btn-outline-light fw-600 px-4 py-2" style="border-radius:10px;font-size:0.925rem;">
                        <i class="bi bi-grid-fill me-2"></i> See Features
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <div class="stat-pill"><strong>15+</strong> Demo Leads</div>
                    <div class="stat-pill"><strong>8</strong> Pages Built</div>
                    <div class="stat-pill"><strong>PHP + MySQL</strong></div>
                    <div class="stat-pill"><i class="bi bi-shield-check me-1"></i><strong>Secure</strong></div>
                </div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="col-12 col-lg-6">
                <div class="browser-mockup" id="showcase">
                    <div class="browser-topbar">
                        <div class="browser-dot" style="background:#ff5f57;"></div>
                        <div class="browser-dot" style="background:#febc2e;"></div>
                        <div class="browser-dot" style="background:#28c840;"></div>
                        <div class="browser-url">yourdomain.com/coachflow-crm/dashboard.php</div>
                    </div>
                    <div style="display:flex;height:280px;overflow:hidden;">
                        <!-- Mini sidebar -->
                        <div class="mock-sidebar">
                            <div style="width:28px;height:28px;background:linear-gradient(135deg,#4361ee,#8b5cf6);border-radius:8px;"></div>
                            <div class="mock-icon active"><i class="bi bi-speedometer2"></i></div>
                            <div class="mock-icon"><i class="bi bi-people-fill"></i></div>
                            <div class="mock-icon"><i class="bi bi-calendar-check-fill"></i></div>
                            <div class="mock-icon"><i class="bi bi-bar-chart-fill"></i></div>
                        </div>
                        <!-- Main area -->
                        <div class="mock-main flex-grow-1" style="overflow:hidden;">
                            <div class="mock-topbar">
                                <div style="font-size:0.72rem;font-weight:700;color:#111;">Dashboard</div>
                                <div style="margin-left:auto;width:22px;height:22px;background:linear-gradient(135deg,#4361ee,#8b5cf6);border-radius:50%;"></div>
                            </div>
                            <!-- Stat row -->
                            <div style="display:flex;gap:6px;margin-bottom:8px;">
                                <div class="mock-stat"><div class="mock-accent" style="background:#4361ee;"></div><div class="val">15</div><div class="lbl">Total Leads</div></div>
                                <div class="mock-stat"><div class="mock-accent" style="background:#8b5cf6;"></div><div class="val">4</div><div class="lbl">New</div></div>
                                <div class="mock-stat"><div class="mock-accent" style="background:#f59e0b;"></div><div class="val">3</div><div class="lbl">Follow-ups</div></div>
                                <div class="mock-stat"><div class="mock-accent" style="background:#10b981;"></div><div class="val">2</div><div class="lbl">Won</div></div>
                            </div>
                            <!-- Lead rows -->
                            <?php
                            $mockLeads = [
                                ['Sarah Johnson',    '#10b981', 'Won',       'Instagram'],
                                ['Mohammed Al-Farsi','#4361ee', 'New',       'LinkedIn'],
                                ['Priya Sharma',     '#f59e0b', 'Follow-up', 'YouTube'],
                                ['James O\'Brien',   '#8b5cf6', 'Booked',    'Referral'],
                            ];
                            foreach ($mockLeads as [$name, $color, $status, $source]): ?>
                            <div class="mock-table-row">
                                <div style="width:20px;height:20px;background:<?= $color ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:0.55rem;font-weight:800;flex-shrink:0;">
                                    <?= strtoupper(substr($name,0,1)) ?>
                                </div>
                                <div style="flex:1;font-size:0.67rem;font-weight:600;color:#111;"><?= $name ?></div>
                                <div style="font-size:0.6rem;color:#9ca3af;"><?= $source ?></div>
                                <div class="mock-badge" style="background:<?= $color ?>20;color:<?= $color ?>;"><?= $status ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-3 opacity-50" style="color:rgba(255,255,255,0.5);font-size:0.78rem;">
                    <i class="bi bi-cursor-fill me-1"></i> Click "Live Demo" to explore the full system
                </p>
            </div>
        </div>
    </div>
</section>

<!-- PROBLEM / SOLUTION -->
<section style="background:#f8fafc;padding:4rem 0;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-12 col-md-6">
                <div class="card border-0 p-4" style="background:white;border-radius:16px;box-shadow:var(--shadow-md);">
                    <h6 class="fw-800 text-red mb-3"><i class="bi bi-x-circle-fill me-2"></i>Without a CRM</h6>
                    <?php $problems = ['Leads stored in WhatsApp, notes, and spreadsheets','Missing follow-ups because you forgot who to call','No visibility into your pipeline or conversion rate','Losing clients to faster-responding competitors','Hours wasted searching through old messages']; ?>
                    <?php foreach ($problems as $p): ?>
                    <div class="d-flex gap-2 mb-2" style="font-size:0.845rem;"><i class="bi bi-dash-circle text-red mt-1"></i><span><?= $p ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card border-0 p-4" style="background:white;border-radius:16px;border:2px solid var(--green-lt) !important;box-shadow:var(--shadow-md);">
                    <h6 class="fw-800 text-green mb-3"><i class="bi bi-check-circle-fill me-2"></i>With CoachFlow CRM</h6>
                    <?php $solutions = ['All leads in one organised, searchable database','Automated follow-up reminders — never miss a lead','Live dashboard showing your exact pipeline status','Instant access to full lead history and notes','Export your data anytime with one click']; ?>
                    <?php foreach ($solutions as $s): ?>
                    <div class="d-flex gap-2 mb-2" style="font-size:0.845rem;"><i class="bi bi-check-circle-fill text-green mt-1"></i><span><?= $s ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" style="padding:5rem 0;background:white;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge mb-3 px-3 py-2" style="background:var(--blue-lt);color:var(--blue);border-radius:20px;font-weight:700;">System Features</span>
            <h2 class="section-title">Everything you need to convert leads</h2>
            <p class="section-sub">Built specifically for coaches and consultants — not bloated enterprise software.</p>
        </div>

        <div class="row g-3">
            <?php
            $features = [
                ['bi-speedometer2',       'blue',   'Smart Dashboard',      'Real-time stats: total leads, follow-ups due, booked calls, converted clients — all at a glance with trend indicators.'],
                ['bi-people-fill',        'purple', 'Lead Management',      'Full CRM table with search, filters by status/source/service, pagination, and one-click lead profiles.'],
                ['bi-calendar-check-fill','orange', 'Follow-up Center',     'Never miss a follow-up again. Overdue, today, and upcoming sections with snooze and reschedule actions.'],
                ['bi-bar-chart-fill',     'green',  'Reports & Analytics',  'Conversion funnel, leads by source (donut), status (bar), monthly trend (line). Export CSV anytime.'],
                ['bi-shield-lock-fill',   'teal',   'Secure Login',         'Session-based authentication, bcrypt password hashing, PDO prepared statements, XSS protection throughout.'],
                ['bi-phone-fill',         'blue',   'Mobile Responsive',    'Collapsible sidebar, stacked layouts on mobile. Looks great on every screen from phone to 4K.'],
                ['bi-lightning-charge-fill','orange','Fast & Lightweight',  'No heavy frameworks. Loads fast on cPanel shared hosting. Pure PHP + MySQL + Bootstrap 5.'],
                ['bi-gear-fill',          'purple', 'Settings & Profile',   'Update profile, change password, manage CRM preferences, branding section with timezone support.'],
            ];
            foreach ($features as [$icon, $color, $title, $desc]): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon bg-<?= $color ?>-lt text-<?= $color ?>">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <h6 class="fw-800 mb-2"><?= $title ?></h6>
                    <p class="text-muted mb-0" style="font-size:0.82rem;line-height:1.6;"><?= $desc ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TECH STACK -->
<section style="background:#f8fafc;padding:3rem 0;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-800 mb-1">Built with Proven Technology</h3>
            <p class="text-muted">No complex dependencies. Works on any cPanel shared hosting account.</p>
        </div>
        <div class="row justify-content-center g-3">
            <?php
            $stack = [
                ['bi-filetype-php','#777bb4','PHP 8+',      'Core PHP — no framework needed'],
                ['bi-database-fill','#00758f','MySQL',       'Relational database, PDO'],
                ['bi-bootstrap-fill','#7952b3','Bootstrap 5','Responsive UI framework'],
                ['bi-filetype-js','#f7df1e','Vanilla JS',   'No jQuery, no bloat'],
                ['bi-graph-up','#ff6384','Chart.js',        'Beautiful charts CDN'],
                ['bi-server','#2e8b57','cPanel Ready',      'Shared hosting compatible'],
            ];
            foreach ($stack as [$icon, $color, $name, $desc]): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card text-center p-3 h-100" style="border-radius:12px;">
                    <i class="bi <?= $icon ?> mb-2" style="font-size:1.8rem;color:<?= $color ?>;"></i>
                    <div class="fw-700" style="font-size:0.845rem;"><?= $name ?></div>
                    <div class="text-muted" style="font-size:0.72rem;"><?= $desc ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section style="background:white;padding:4rem 0;">
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="fw-800 mb-1">What Clients Say</h3>
            <p class="text-muted">The kind of systems we build — and the results they deliver.</p>
        </div>
        <div class="row g-3">
            <?php
            $testimonials = [
                ['S','#4361ee','Sarah M., Business Coach, Dubai','Before this system, I was losing leads in WhatsApp threads. Now I have full visibility of my pipeline. I converted 3 extra clients in the first month.'],
                ['J','#10b981','James T., Executive Coach, London','The follow-up center alone paid for the whole system. I used to miss 40% of my follow-ups. Now I miss zero.'],
                ['A','#8b5cf6','Amina K., Life Coach, Nairobi','Finally a system that feels like it was made for my business — not a generic software with 200 features I don\'t need.'],
            ];
            foreach ($testimonials as [$init,$color,$name,$quote]): ?>
            <div class="col-12 col-md-4">
                <div class="testimonial-card">
                    <div class="d-flex gap-1 mb-3" style="color:#f59e0b;">
                        <?php for($i=0;$i<5;$i++) echo '<i class="bi bi-star-fill" style="font-size:0.8rem;"></i>'; ?>
                    </div>
                    <p class="text-secondary mb-3" style="font-size:0.875rem;line-height:1.7;">"<?= $quote ?>"</p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="testimonial-avatar" style="background:<?= $color ?>;"><?= $init ?></div>
                        <span style="font-size:0.82rem;font-weight:700;color:var(--text-primary);"><?= $name ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="showcase-cta">
    <div class="container">
        <div style="max-width:620px;margin:auto;">
            <h2 style="font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;color:white;letter-spacing:-0.03em;margin-bottom:1rem;">
                Need a Custom System Like This?
            </h2>
            <p style="color:rgba(255,255,255,0.75);font-size:1.05rem;line-height:1.7;margin-bottom:2rem;">
                I build tailored CRMs, dashboards, and business tools for coaches, consultants, and service businesses worldwide. Fast delivery. Clean code. No monthly SaaS fees.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="login.php" class="btn btn-warning fw-800 px-5 py-3" style="border-radius:12px;color:#1a1f36;font-size:1rem;">
                    <i class="bi bi-play-circle-fill me-2"></i> Try the Live Demo
                </a>
                <a href="mailto:hello@example.com" class="btn btn-outline-light fw-700 px-5 py-3" style="border-radius:12px;font-size:1rem;">
                    <i class="bi bi-envelope-fill me-2"></i> Get in Touch
                </a>
            </div>
            <p style="color:rgba(255,255,255,0.45);font-size:0.78rem;margin-top:1.5rem;">
                Demo credentials: admin@coachflow.com / password
            </p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer style="background:#0f1729;padding:1.5rem 0;text-align:center;">
    <div class="container">
        <p style="color:rgba(255,255,255,0.35);font-size:0.78rem;margin:0;">
            &copy; <?= date('Y') ?> <strong style="color:rgba(255,255,255,0.6);">CoachFlow CRM</strong>
            — Designed &amp; Built by <strong style="color:#4361ee;">Abdul Salam</strong>
            &nbsp;·&nbsp;
            <a href="login.php" style="color:#4361ee;text-decoration:none;">Live Demo</a>
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click',function(e){
        const target = document.querySelector(this.getAttribute('href'));
        if(target){ e.preventDefault(); target.scrollIntoView({behavior:'smooth',block:'start'}); }
    });
});

// Animate elements on scroll
const observer = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
        if(entry.isIntersecting){
            entry.target.style.opacity='1';
            entry.target.style.transform='translateY(0)';
        }
    });
},{threshold:0.1});

document.querySelectorAll('.feature-card,.testimonial-card,.pricing-card,.card').forEach(function(el,i){
    el.style.opacity='0';
    el.style.transform='translateY(20px)';
    el.style.transition=`opacity 0.4s ease ${i*0.05}s, transform 0.4s ease ${i*0.05}s`;
    observer.observe(el);
});
</script>
</body>
</html>
