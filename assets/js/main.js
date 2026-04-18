/* ============================================================
   CoachFlow CRM — Main JavaScript v2.0
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // SIDEBAR TOGGLE
    // ============================================================
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper       = document.querySelector('.wrapper');
    const sidebar       = document.getElementById('sidebar');

    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                wrapper && wrapper.classList.toggle('sidebar-collapsed');
            }
        });
    }
    overlay.addEventListener('click', function () {
        sidebar && sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // ============================================================
    // TOOLTIPS (Bootstrap 5)
    // ============================================================
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        bootstrap.Tooltip.getOrCreateInstance(el, { trigger: 'hover' });
    });

    // ============================================================
    // TOAST NOTIFICATION SYSTEM
    // ============================================================
    window.showToast = function (message, type = 'success', duration = 3500) {
        const icons = { success: 'check-circle-fill', danger: 'exclamation-triangle-fill', info: 'info-circle-fill', warning: 'exclamation-circle-fill' };
        const id    = 'toast-' + Date.now();
        const bg    = { success: '#10b981', danger: '#ef4444', info: '#4361ee', warning: '#f59e0b' }[type] || '#4361ee';

        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        const html = `<div id="${id}" class="toast align-items-center text-white border-0 shadow" role="alert" style="background:${bg};border-radius:10px;">
            <div class="d-flex align-items-center p-3 gap-2">
                <i class="bi bi-${icons[type] || icons.info} fs-6"></i>
                <div class="flex-grow-1 fw-500" style="font-size:0.845rem;">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(id);
        el.style.transform = 'translateX(120%)';
        el.style.transition = 'transform 0.3s cubic-bezier(0.4,0,0.2,1)';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => { el.style.transform = 'translateX(0)'; });
        });

        const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: duration });
        toast.show();
        el.addEventListener('hidden.bs.toast', () => { el.style.transform = 'translateX(120%)'; setTimeout(() => el.remove(), 300); });
    };

    // Show stored flash messages
    const flash = document.body.dataset.flash;
    const flashType = document.body.dataset.flashType;
    if (flash) showToast(flash, flashType || 'success');

    // ============================================================
    // DELETE CONFIRMATION MODAL
    // ============================================================
    function createDeleteModal() {
        if (document.getElementById('globalDeleteModal')) return;
        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="globalDeleteModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
              <div class="modal-body text-center p-4">
                <div class="mb-3" style="font-size:2.5rem;">🗑️</div>
                <h6 class="fw-700 mb-1">Delete Lead?</h6>
                <p class="text-muted fs-13 mb-3" id="deleteModalMsg">This action cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                  <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                  <a href="#" id="deleteModalConfirmBtn" class="btn btn-danger btn-sm">Yes, Delete</a>
                </div>
              </div>
            </div>
          </div>
        </div>`);
    }
    createDeleteModal();

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-lead');
        if (!btn) return;
        e.preventDefault();
        const name = btn.dataset.name || 'this lead';
        const href = btn.href || btn.dataset.href;
        document.getElementById('deleteModalMsg').textContent = `"${name}" will be permanently removed.`;
        document.getElementById('deleteModalConfirmBtn').href = href;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('globalDeleteModal')).show();
    });

    // ============================================================
    // LEAD DETAILS MODAL
    // ============================================================
    document.querySelectorAll('.clickable-row').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('.btn, a, button')) return;
            const data = JSON.parse(this.dataset.lead || '{}');
            if (!data.id) return;
            openLeadModal(data);
        });
    });

    window.openLeadModal = function (d) {
        const statusClass = 'status-' + (d.status || '').replace(/ /g, '-');
        const initial     = (d.full_name || '?').charAt(0).toUpperCase();
        let modal = document.getElementById('leadViewModal');
        if (!modal) {
            document.body.insertAdjacentHTML('beforeend', `
            <div class="modal fade" id="leadViewModal" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Lead Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body" id="leadModalBody"></div>
                  <div class="modal-footer">
                    <a href="#" id="leadModalEditBtn" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit Lead</a>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`);
            modal = document.getElementById('leadViewModal');
        }

        const BASE = document.body.dataset.baseUrl || '/coachflow-crm/';
        document.getElementById('leadModalEditBtn').href = BASE + 'lead_form.php?id=' + d.id;

        document.getElementById('leadModalBody').innerHTML = `
          <div class="lead-profile-header">
            <div class="lead-avatar">${initial}</div>
            <div>
              <div class="fw-700 fs-6">${esc(d.full_name)}</div>
              <span class="status-badge ${statusClass}">${esc(d.status)}</span>
            </div>
          </div>
          <div>
            ${detailRow('bi-envelope',       'Email',       d.email)}
            ${detailRow('bi-telephone',      'Phone',       d.phone)}
            ${detailRow('bi-whatsapp',       'WhatsApp',    d.whatsapp)}
            ${detailRow('bi-geo-alt',        'Country',     d.country)}
            ${detailRow('bi-briefcase',      'Service',     d.service_interest)}
            ${detailRow('bi-megaphone',      'Source',      d.lead_source)}
            ${detailRow('bi-calendar-check', 'Follow-up',   d.next_followup_date || '—')}
            ${d.notes ? `<div class="detail-row"><span class="detail-label"><i class="bi bi-journal-text me-1"></i>Notes</span><span class="detail-value text-muted" style="font-size:0.82rem;">${esc(d.notes)}</span></div>` : ''}
          </div>`;

        bootstrap.Modal.getOrCreateInstance(modal).show();
    };

    function detailRow(icon, label, value) {
        if (!value) return '';
        return `<div class="detail-row"><span class="detail-label"><i class="bi ${icon} me-1"></i>${label}</span><span class="detail-value">${esc(value)}</span></div>`;
    }
    function esc(s) {
        if (!s) return '—';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ============================================================
    // AUTO-DISMISS ALERTS
    // ============================================================
    document.querySelectorAll('.alert-auto-dismiss').forEach(function (el) {
        setTimeout(function () {
            try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e){}
        }, 4500);
    });

    // ============================================================
    // FOLLOW-UP DATE COLOUR CODING
    // ============================================================
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('[data-followup-date]').forEach(function (el) {
        const date = el.dataset.followupDate;
        if (!date) return;
        if (date < today) {
            el.innerHTML = `<span class="overdue-badge"><i class="bi bi-exclamation-circle"></i>${date}</span>`;
        } else if (date === today) {
            el.innerHTML = `<span class="today-badge"><i class="bi bi-clock"></i>Today</span>`;
        } else {
            el.innerHTML = `<span class="text-muted fs-13">${date}</span>`;
        }
    });

    // ============================================================
    // CLICK TO COPY
    // ============================================================
    document.querySelectorAll('.copy-phone').forEach(function (el) {
        el.style.cursor = 'pointer';
        el.setAttribute('data-bs-toggle', 'tooltip');
        el.setAttribute('data-bs-title', 'Click to copy');
        el.addEventListener('click', function () {
            const text = this.textContent.trim();
            if (text && text !== '—') {
                navigator.clipboard.writeText(text).then(() => showToast('Copied: ' + text, 'info', 2500));
            }
        });
    });

    // ============================================================
    // ANIMATE STAT NUMBERS (count-up)
    // ============================================================
    function animateCount(el) {
        const target = parseInt(el.textContent.replace(/\D/g, ''), 10);
        if (isNaN(target) || target === 0) return;
        const suffix = el.textContent.replace(/[0-9]/g, '');
        let current  = 0;
        const step   = Math.ceil(target / 25);
        const timer  = setInterval(function () {
            current = Math.min(current + step, target);
            el.textContent = current + suffix;
            if (current >= target) clearInterval(timer);
        }, 40);
    }
    document.querySelectorAll('.stat-value[data-count]').forEach(animateCount);

    // ============================================================
    // PIPELINE BARS ANIMATE IN
    // ============================================================
    setTimeout(function () {
        document.querySelectorAll('.pipeline-bar-fill[data-width]').forEach(function (el) {
            el.style.width = el.dataset.width + '%';
        });
    }, 300);

    // ============================================================
    // CARD ANIMATE IN ON LOAD
    // ============================================================
    document.querySelectorAll('.stat-card, .card').forEach(function (el, i) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(10px)';
        el.style.transition = `opacity 0.3s ease ${i * 0.04}s, transform 0.3s ease ${i * 0.04}s`;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        });
    });

});
