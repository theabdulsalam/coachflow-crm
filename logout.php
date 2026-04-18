<?php
/**
 * Logout — destroy session and redirect to login
 */
define('BASE_URL', '/coachflow-crm/');

session_start();
session_unset();
session_destroy();

header('Location: ' . BASE_URL . 'login.php?logout=1');
exit;
