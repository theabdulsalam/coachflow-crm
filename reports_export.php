<?php
/**
 * Export all leads as CSV download
 */
define('BASE_URL', '/coachflow-crm/');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$leads = $pdo->query("SELECT id, full_name, email, phone, whatsapp, country, service_interest, lead_source, status, next_followup_date, notes, created_at, updated_at FROM leads ORDER BY created_at DESC")->fetchAll();

$filename = 'coachflow-leads-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
// BOM for Excel UTF-8
fputs($out, "\xEF\xBB\xBF");

// Headers
fputcsv($out, ['ID','Full Name','Email','Phone','WhatsApp','Country','Service Interest','Lead Source','Status','Next Follow-up','Notes','Created At','Updated At']);

foreach ($leads as $lead) {
    fputcsv($out, [
        $lead['id'],
        $lead['full_name'],
        $lead['email'],
        $lead['phone'],
        $lead['whatsapp'],
        $lead['country'],
        $lead['service_interest'],
        $lead['lead_source'],
        $lead['status'],
        $lead['next_followup_date'],
        $lead['notes'],
        $lead['created_at'],
        $lead['updated_at'],
    ]);
}
fclose($out);
exit;
