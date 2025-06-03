<?php
declare(strict_types=1);

/**
 * Script to deploy admin protection to all Admin PHP files
 * This script adds the require_once statement to include protect.php
 */

// Root admin directory
$adminDir = __DIR__ . '/Admin';

// Files to exclude
$excludeFiles = [
    'login.php',
    'logout.php',
    'protect.php'
];

// Protection line to add
$protectionLine = "require_once __DIR__ . '/../protect.php';";

/**
 * Add protection to a PHP file
 * 
 * @param string $filePath Path to the file
 * @return bool True if file was modified, false otherwise
 */
function addProtection(string $filePath): bool {
    global $protectionLine, $excludeFiles;
    
    // Get filename
    $fileName = basename($filePath);
    
    // Skip excluded files
    if (in_array($fileName, $excludeFiles)) {
        echo "Skipping excluded file: $filePath\n";
        return false;
    }
    
    // Read file content
    $content = file_get_contents($filePath);
    
    // Check if file already has protection
    if (strpos($content, 'protect.php') !== false) {
        echo "Protection already exists in: $filePath\n";
        return false;
    }
    
    // Find the opening PHP tag
    $phpPos = strpos($content, '<?php');
    
    if ($phpPos !== false) {
        // Find the position after the opening PHP tag and any comments/whitespace
        $insertPos = $phpPos + 5;
        
        // Find the next non-whitespace character that's not a comment
        while (isset($content[$insertPos]) && 
              (ctype_space($content[$insertPos]) || 
              preg_match('/\/\/|\/\*|\*|#/', substr($content, $insertPos, 2)))) {
            // Skip to end of line for // comments
            if (substr($content, $insertPos, 2) === '//') {
                $nextEol = strpos($content, "\n", $insertPos);
                $insertPos = $nextEol !== false ? $nextEol + 1 : strlen($content);
                continue;
            }
            
            // Skip to end of comment block for /* */ comments
            if (substr($content, $insertPos, 2) === '/*') {
                $endComment = strpos($content, '*/', $insertPos);
                $insertPos = $endComment !== false ? $endComment + 2 : strlen($content);
                continue;
            }
            
            $insertPos++;
        }
        
        // Insert protection line
        $newContent = substr($content, 0, $insertPos) . "\n" . $protectionLine . "\n" . substr($content, $insertPos);
        
        // Save the file
        file_put_contents($filePath, $newContent);
        
        echo "Added protection to: $filePath\n";
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
    // Skip if directory doesn't exist
    if (!is_dir($dir)) {
        echo "Directory doesn't exist: $dir\n";
        return;
    }
    
    $files = scandir($dir);
    
    if ($files === false) {
        echo "Could not scan directory: $dir\n";
        return;
    }
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            processDirectory($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            addProtection($path);
        }
    }
}

// Start processing
echo "Starting to apply admin protection...\n";
echo "Admin directory: $adminDir\n";

if (!is_dir($adminDir)) {
    echo "Error: Admin directory not found at: $adminDir\n";
    exit(1);
}

processDirectory($adminDir);
echo "Finished applying admin protection.\n";
?> 