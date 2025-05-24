<?php
declare(strict_types=1);

/**
 * Application initialization file
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
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

// Create logger instance
$logger = new Logger('app');

// File handler that rotates daily, keeps 7 days of logs
$handler = new RotatingFileHandler($logDir . '/app.log', 7, \Monolog\Level::Debug);
$formatter = new LineFormatter(
    "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
    "Y-m-d H:i:s"
);
$handler->setFormatter($formatter);
$logger->pushHandler($handler);

// Make logger available globally
$GLOBALS['logger'] = $logger;

// Log application startup
$logger->info('Application initialized', ['php_version' => phpversion()]);

// Test email functionality at startup
if (!isset($GLOBALS['email_test_done'])) {
    $GLOBALS['email_test_done'] = true;
    $testEmailLog = $logDir . '/email_test.log';
    $mailResult = @mail('chiannchua05@gmail.com', 'PHP Init Email Test', 'This is a test from app/init.php', 'From: chiannchua05@gmail.com');
    $logger->info('Email test at initialization', ['result' => $mailResult ? 'SUCCESS' : 'FAILED']);
    file_put_contents($testEmailLog, date('[Y-m-d H:i:s]') . " Init mail test: " . ($mailResult ? "SUCCESS" : "FAILED") . PHP_EOL, FILE_APPEND);
}

// Session handling
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up the main application logger
$logger = new \Monolog\Logger('app');

// Add a daily rotating file handler for application logs
$logger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/app.log',
        7, // Keep logs for 7 days
        \Monolog\Level::Debug
    )
);

// Add a separate handler for errors
$logger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/error.log',
        14, // Keep error logs for 14 days
        \Monolog\Level::Error
    )
);

// Set up authentication logger
$authLogger = new \Monolog\Logger('auth');
$authLogger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/auth.log',
        30, // Keep auth logs for 30 days
        \Monolog\Level::Info
    )
);

// Set up payment logger
$paymentLogger = new \Monolog\Logger('payment');
$paymentLogger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler(
        $logDir . '/payment.log',
        90, // Keep payment logs for 90 days
        \Monolog\Level::Info
    )
);

// Set up an audit logger for sensitive operations
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

// Configure error handling
set_error_handler(function($severity, $message, $file, $line) use ($logger) {
    $logger->error('PHP Error', [
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'severity' => $severity
    ]);
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
