<?php
declare(strict_types=1);
// Session will be started in auth_check.php

/**
 * Admin Protection System
 * This file should be included at the top of all admin PHP files
 */

// Include auth_check for session validation
require_once __DIR__ . '/auth_check.php';

// Skip protection on login and logout pages
$current = basename($_SERVER['SCRIPT_NAME']);
if (in_array($current, ['login.php', 'logout.php'], true)) {
    return;
}

// If accessed directly, redirect to dashboard
if (in_array($current, ['protect.php', 'index.php'], true)) {
    header('Location: /FYP/FYP/Admin/Dashboard/dashboard.php');
    exit;
}
?> 