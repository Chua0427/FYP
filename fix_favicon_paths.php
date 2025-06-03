<?php
declare(strict_types=1);

// Source image path - using absolute path
$sourceImage = 'C:/xampp/htdocs/FYP/FYP/User/Header_and_Footer/img/vs.png';

// First destination (directory path as in the original script)
$destinationDir = 'C:/xampp/htdocs/favicon';
$destinationFile = $destinationDir . '/favicon.ico';

// Second destination (root path for default browser behavior)
$rootDestination = 'C:/xampp/htdocs/favicon.ico';

// Ensure source image exists
if (!file_exists($sourceImage)) {
    die('Source image not found at: ' . $sourceImage);
}

// Create destination directory if it doesn't exist
if (!is_dir($destinationDir)) {
    if (!mkdir($destinationDir, 0755, true)) {
        die('Failed to create destination directory: ' . $destinationDir);
    }
}

// Copy the file to first destination (directory)
$success1 = copy($sourceImage, $destinationFile);
echo $success1 ? "✓ Favicon successfully copied to: $destinationFile<br>" : 
                "❌ Failed to copy file to: $destinationFile<br>";

// Copy the file to root location (for default browser behavior)
$success2 = copy($sourceImage, $rootDestination);
echo $success2 ? "✓ Favicon successfully copied to: $rootDestination<br>" : 
                "❌ Failed to copy file to: $rootDestination<br>";

if ($success1 && $success2) {
    echo "<br>✅ Favicon has been successfully updated in both locations!";
}
?> 