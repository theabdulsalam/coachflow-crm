<?php
/**
 * Database connection using PDO
 * Edit these credentials to match your hosting environment
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'coachflow_crm');
define('DB_USER', 'root');        // Change for production
define('DB_PASS', '');            // Change for production
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:40px;background:#fff0f0;color:#c0392b;border:1px solid #e74c3c;border-radius:8px;max-width:600px;margin:80px auto;">
        <h2>Database Connection Error</h2>
        <p>Could not connect to the database. Please check your credentials in <code>includes/db.php</code>.</p>
        <small>Error: ' . htmlspecialchars($e->getMessage()) . '</small>
    </div>');
}
