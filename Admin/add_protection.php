<?php

// Remove circular reference
// require_once __DIR__ . '/../auth_check.php';
declare(strict_types=1);

/**
 * Simple script to add protection to all PHP files in the Admin directory
 * This script should be placed in the Admin directory
 */

// Protection line to add
$protectionLine = 'require_once __DIR__ . "/../protect.php";';

// Files to exclude
$excludeFiles = [
    'login.php',
    'logout.php',
    'protect.php',
    'add_protection.php'
];

// Current directory
$adminDir = __DIR__;

// Function to add protection to a PHP file
function addProtection($filePath, $protectionLine, $excludeFiles) {
    // Get filename
    $fileName = basename($filePath);
    
    // Skip excluded files
    if (in_array($fileName, $excludeFiles)) {
        echo "Skipping excluded file: $filePath<br>";
        return false;
    }
    
    // Read file content
    $content = file_get_contents($filePath);
    
    // Check if file already has protection
    if (strpos($content, 'protect.php') !== false) {
        echo "Protection already exists in: $filePath<br>";
        return false;
    }
    
    // Find the opening PHP tag
    $phpPos = strpos($content, '<?php');
    
    if ($phpPos !== false) {
        // Find the position after the opening PHP tag
        $insertPos = $phpPos + 5;
        
        // Insert protection line
        $newContent = substr($content, 0, $insertPos) . "\n" . $protectionLine . "\n" . substr($content, $insertPos);
        
        // Save the file
        file_put_contents($filePath, $newContent);
        
        echo "Added protection to: $filePath<br>";
        return true;
    }
    
    echo "No PHP opening tag found in: $filePath<br>";
    return false;
}

// Process a directory
function processDirectory($dir, $protectionLine, $excludeFiles) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            processDirectory($path, $protectionLine, $excludeFiles);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            addProtection($path, $protectionLine, $excludeFiles);
        }
    }
}

// Start processing
echo "<h1>Adding Protection to Admin PHP Files</h1>";
echo "<p>Starting to apply admin protection...</p>";

processDirectory($adminDir, $protectionLine, $excludeFiles);

echo "<p>Finished applying admin protection.</p>";
?> 