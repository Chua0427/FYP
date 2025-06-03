<?php
declare(strict_types=1);

/**
 * Script to add authentication check to all Admin PHP files
 * This is a one-time script to update all PHP files in the Admin directory
 */

// Root directory
$adminDir = __DIR__ . '/Admin';

// Files to exclude
$excludeFiles = [
    'login.php',
    'logout.php',
    'auth_check.php'
];

// Auth check line to add
$authCheckLine = "require_once __DIR__ . '/../auth_check.php';\n";

/**
 * Add auth check to a PHP file
 * 
 * @param string $filePath Path to the file
 * @return bool True if file was modified, false otherwise
 */
function addAuthCheck(string $filePath): bool {
    global $authCheckLine, $excludeFiles;
    
    // Get filename
    $fileName = basename($filePath);
    
    // Skip excluded files
    if (in_array($fileName, $excludeFiles)) {
        echo "Skipping excluded file: $filePath\n";
        return false;
    }
    
    // Read file content
    $content = file_get_contents($filePath);
    
    // Check if file already has the auth check
    if (strpos($content, 'require_once') !== false && 
        strpos($content, 'auth_check.php') !== false) {
        echo "Auth check already exists in: $filePath\n";
        return false;
    }
    
    // Find the opening PHP tag
    $phpPos = strpos($content, '<?php');
    
    if ($phpPos !== false) {
        // Find the position after the opening PHP tag
        $insertPos = $phpPos + 5;
        
        // Find next non-whitespace character
        while (isset($content[$insertPos]) && 
               (ctype_space($content[$insertPos]) || $content[$insertPos] === "\n" || $content[$insertPos] === "\r")) {
            $insertPos++;
        }
        
        // Insert auth check line
        $newContent = substr($content, 0, $insertPos) . "\n" . $authCheckLine . substr($content, $insertPos);
        
        // Save the file
        file_put_contents($filePath, $newContent);
        
        echo "Added auth check to: $filePath\n";
        return true;
    }
    
    echo "No PHP opening tag found in: $filePath\n";
    return false;
}

/**
 * Process all PHP files in a directory recursively
 * 
 * @param string $dir Directory to process
 * @return void
 */
function processDirectory(string $dir): void {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            processDirectory($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            addAuthCheck($path);
        }
    }
}

// Start processing
echo "Starting to add auth check to Admin PHP files...\n";
processDirectory($adminDir);
echo "Finished adding auth check to Admin PHP files.\n";
?> 