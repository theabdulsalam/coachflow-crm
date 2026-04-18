/* ============================================================
   CoachFlow CRM — Main JavaScript
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Sidebar Toggle ----
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper       = document.querySelector('.wrapper');
    const sidebar       = document.getElementById('sidebar');

    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                wrapper.classList.toggle('sidebar-collapsed');
            }
        });
    }

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // ---- Delete Confirmation ----
    document.querySelectorAll('.btn-delete-lead').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const leadName = this.dataset.name || 'this lead';
            if (confirm('Are you sure you want to delete ' + leadName + '?\n\nThis action cannot be undone.')) {
                window.location.href = this.href;
            }
        });
    });

    // ---- Auto-dismiss alerts ----
    document.querySelectorAll('.alert.alert-auto-dismiss').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // ---- Search filter (client-side instant search) ----
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('tbody tr[data-searchable]').forEach(function (row) {
                const text = row.dataset.searchable.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // ---- Phone/WhatsApp copy ----
    document.querySelectorAll('.copy-phone').forEach(function (el) {
        el.style.cursor = 'pointer';
        el.title = 'Click to copy';
        el.addEventListener('click', function () {
            navigator.clipboard.writeText(this.textContent.trim()).then(function () {
                showToast('Copied to clipboard!');
            });
        });
    });

    // ---- Toast notification ----
    window.showToast = function (message, type = 'success') {
        const id = 'toast-' + Date.now();
        const bg  = type === 'success' ? 'bg-success' : 'bg-danger';
        const html = `<div id="${id}" class="toast align-items-center text-white ${bg} border-0" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body fw-semibold">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = 9999;
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3000 }).show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    };

    // ---- Follow-up date colour coding ----
    document.querySelectorAll('[data-followup-date]').forEach(function (el) {
        const date  = el.dataset.followupDate;
        const today = new Date().toISOString().split('T')[0];
        if (!date) return;
        if (date < today) {
            el.innerHTML = '<span class="overdue-badge"><i class="bi bi-exclamation-circle me-1"></i>' + date + '</span>';
        } else if (date === today) {
            el.innerHTML = '<span class="today-badge"><i class="bi bi-clock me-1"></i>Today</span>';
        }
    });
});
