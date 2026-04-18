    </main><!-- /main-content -->

    <!-- Footer -->
    <footer class="footer py-3 px-4 border-top">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <span class="text-muted small">
                &copy; <?= date('Y') ?> <strong>CoachFlow CRM</strong> &mdash; Built for Coaches &amp; Consultants
            </span>
            <span class="text-muted small">v1.0.0</span>
        </div>
    </footer>
</div><!-- /content-wrapper -->
</div><!-- /wrapper -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
<?php if (!empty($extraJs)) echo $extraJs; ?>
</body>
</html>
