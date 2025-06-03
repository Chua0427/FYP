<?php
declare(strict_types=1);

// Source image path - using absolute path
$sourceImage = 'C:/xampp/htdocs/FYP/FYP/User/Header_and_Footer/img/vs.png';

// Destination favicon path
$destinationDir = 'C:/xampp/htdocs/favicon';
$destinationFile = $destinationDir . '/favicon.ico';

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

// Copy the file
if (copy($sourceImage, $destinationFile)) {
    echo "Favicon successfully copied to: $destinationFile";
} else {
    echo "Failed to copy file to: $destinationFile";
}
?> 