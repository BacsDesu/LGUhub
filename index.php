<?php
/**
 * MCR v3 - Local Civil Registrar Request Management System
 * Main entry point / router.
 *
 * This file wires together the segregated pieces:
 *   config/database.php  - DB connection + auto-migration
 *   includes/functions.php - shared helper functions
 *   includes/actions.php   - all POST/logout handlers (runs before any HTML)
 *   config/theme.php       - system settings + theme colors
 *   includes/header.php    - <head>, dynamic CSS vars, sidebar/topbar
 *   pages/*.php             - one file per page
 *   includes/footer.php    - closes layout, global JS, flush buffer
 */

ob_start();
session_start();

require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

// Page routing
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

require __DIR__ . '/includes/actions.php';

require __DIR__ . '/config/theme.php';

require __DIR__ . '/includes/header.php';

// Map ?page= values to page templates
$known_pages = [
    'login', 'dashboard', 'notifications', 'request_details',
    'recently_deleted', 'settings', 'create_request', 'view_requests',
    'documentation', 'departments', 'profile', 'change_password', 'users',
];

if ($page === 'login' && isLoggedIn()) {
    // Already logged in, login page not applicable
    header("Location: ?page=dashboard");
    exit();
} elseif ($page !== 'login' && !isLoggedIn()) {
    header("Location: ?page=login");
    exit();
} elseif (in_array($page, $known_pages, true)) {
    require __DIR__ . '/pages/' . $page . '.php';
} else {
    // Default / fallback
    if (!isLoggedIn()) {
        header("Location: ?page=login");
        exit();
    } else {
        header("Location: ?page=dashboard");
        exit();
    }
}

require __DIR__ . '/includes/footer.php';
