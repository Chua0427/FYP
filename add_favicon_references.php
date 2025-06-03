<?php
declare(strict_types=1);

// Define base directory for User files
$baseDir = __DIR__ . '/FYP/User';

// Define favicon include code
$faviconInclude = "<?php include_once dirname(__DIR__) . '/Header_and_Footer/favicon.php'; ?>";

// Function to add favicon include to a file
function addFaviconToFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Skip if file already includes favicon.php
    if (strpos($content, 'favicon.php') !== false) {
        echo "Skipping file (already has favicon reference): $filePath\n";
        return;
    }
    
    // Look for head section
    if (preg_match('/<head>(.*?)<\/head>/s', $content, $matches)) {
        // Add favicon include after opening head tag
        $newHeadContent = "<head>\n    " . $GLOBALS['faviconInclude'] . $matches[1] . "</head>";
        $content = str_replace($matches[0], $newHeadContent, $content);
        
        // Save modified file
        file_put_contents($filePath, $content);
        echo "Added favicon reference to: $filePath\n";
    } else {
        echo "No head section found in: $filePath\n";
    }
}

// Function to recursively scan directories
function scanDirectory($dir) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            scanDirectory($path);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php' && strpos($file, 'favicon.php') === false) {
            // Process PHP files except favicon.php itself
            addFaviconToFile($path);
        }
    }
}

// Start scanning
echo "Starting to add favicon references to PHP files in User directory...\n";
scanDirectory($baseDir);
echo "Done!\n";
?> 