<?php
/**
 * Entry point — redirect to dashboard if logged in, otherwise login
 */
define('BASE_URL', '/coachflow-crm/');

session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
} else {
    header('Location: ' . BASE_URL . 'login.php');
}
exit;
