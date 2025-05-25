<?php
declare(strict_types=1);

/**
 * Application initialization file
 */

// Set error reporting
error_reporting(E_ALL);

// Determine environment (production or development)
$isProduction = false;
if (isset($_SERVER['SERVER_NAME'])) {
    $isProduction = !in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
}

// Configure error display based on environment
ini_set('display_errors', $isProduction ? '0' : '1');
ini_set('log_errors', '1');

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Set up error log location
ini_set('error_log', $logDir . '/php_errors.log');

// Import necessary libraries
require_once '/xampp/htdocs/FYP/vendor/autoload.php';

// Initialize Monolog for structured logging
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

// Default log level based on environment
$defaultLogLevel = $isProduction ? \Monolog\Level::Warning : \Monolog\Level::Debug;

// Create logger instance with optimized configuration
$logger = new Logger('app');

// File handler that rotates daily, keeps 7 days of logs
$handler = new RotatingFileHandler($logDir . '/app.log', 7, $defaultLogLevel);
$formatter = new LineFormatter(
    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
    "Y-m-d H:i:s"
);
$handler->setFormatter($formatter);
$logger->pushHandler($handler);

// Make logger available globally
$GLOBALS['logger'] = $logger;

// Log application startup - only in development or at most once per day in production
if (!$isProduction || !file_exists($logDir . '/startup_' . date('Y-m-d') . '.flag')) {
    $logger->info('Application initialized', ['php_version' => phpversion()]);
    if ($isProduction) {
        touch($logDir . '/startup_' . date('Y-m-d') . '.flag');
    }
}

// Test email functionality at startup - only in development
if (!$isProduction && !isset($GLOBALS['email_test_done'])) {
    $GLOBALS['email_test_done'] = true;
    $testEmailLog = $logDir . '/email_test.log';
    $mailResult = @mail('chiannchua05@gmail.com', 'PHP Init Email Test', 'This is a test from app/init.php', 'From: chiannchua05@gmail.com');
    $logger->info('Email test at initialization', ['result' => $mailResult ? 'SUCCESS' : 'FAILED']);
    file_put_contents($testEmailLog, date('[Y-m-d H:i:s]') . " Init mail test: " . ($mailResult ? "SUCCESS" : "FAILED") . PHP_EOL, FILE_APPEND);
}

// Session handling
if (session_status() === PHP_SESSION_NONE) {
    // Configure session for better performance
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cache_limiter', $isProduction ? 'nocache' : 'private');
    session_start();
}

// Store that we've already started a session in a global
$GLOBALS['session_started'] = true;

// Set up specialized loggers with optimized settings

// Auth logger - critical for security, keep at INFO level
$authLogger = new \Monolog\Logger('auth');
$authLogger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/auth.log',
        30, // Keep auth logs for 30 days
        \Monolog\Level::Info
    )
);

// Payment logger - only log important payment events in production
$paymentLogger = new \Monolog\Logger('payment');
$paymentLogger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/payment.log',
        90, // Keep payment logs for 90 days
        $isProduction ? \Monolog\Level::Notice : \Monolog\Level::Info
    )
);

// Audit logger - keep at INFO level for compliance
$auditLogger = new \Monolog\Logger('audit');
$auditLogger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/audit.log',
        365, // Keep audit logs for a year
        \Monolog\Level::Info
    )
);

// Make loggers available globally
$GLOBALS['logger'] = $logger;
$GLOBALS['authLogger'] = $authLogger;
$GLOBALS['paymentLogger'] = $paymentLogger;
$GLOBALS['auditLogger'] = $auditLogger;
$GLOBALS['isProduction'] = $isProduction;

// Configure error handling - only log detailed info in development
set_error_handler(function($severity, $message, $file, $line) use ($logger, $isProduction) {
    if ($isProduction && ($severity == E_NOTICE || $severity == E_USER_NOTICE)) {
        return true; // Don't log notices in production
    }
    
    $logger->error('PHP Error', [
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'severity' => $severity
    ]);
    return false;
});

// Configure exception handling
set_exception_handler(function($exception) use ($logger) {
    $logger->error('Uncaught Exception', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});

// Load configuration
require_once __DIR__ . '/../../connect_db/config.php';

// Include utility classes
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

// Initialize authentication
Auth::init();
