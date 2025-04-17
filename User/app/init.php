<?php
declare(strict_types=1);

/**
 * Application initialization file
 */

require_once '/xampp/htdocs/FYP/vendor/autoload.php';

// Initialize Monolog for logging
$logDir = __DIR__ . '/../logs';

// Create logs directory if it doesn't exist
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
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
