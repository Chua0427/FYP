<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/auth_check.php';
declare(strict_types=1);
require_once __DIR__ . '/protect.php';

// If reached here, already authenticated - redirect to dashboard
header('Location: /FYP/FYP/Admin/Dashboard/dashboard.php');
exit;
