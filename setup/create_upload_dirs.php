<?php
// Script to create upload directories with proper permissions

$projectRoot = realpath(__DIR__ . '/..');
$baseUploadDir = $projectRoot . '/uploads';
$popUploadDir = $baseUploadDir . '/proof_of_payment';

echo "Creating upload directories...\n";

// Create base uploads directory
if (!file_exists($baseUploadDir)) {
    if (mkdir($baseUploadDir, 0777, true)) {
        chmod($baseUploadDir, 0777);
        echo "Created base uploads directory: $baseUploadDir\n";
    } else {
        echo "Failed to create base uploads directory!\n";
    }
} else {
    chmod($baseUploadDir, 0777);
    echo "Base uploads directory already exists, updated permissions\n";
}

// Create proof_of_payment directory
if (!file_exists($popUploadDir)) {
    if (mkdir($popUploadDir, 0777, true)) {
        chmod($popUploadDir, 0777);
        echo "Created POP directory: $popUploadDir\n";
    } else {
        echo "Failed to create POP directory!\n";
    }
} else {
    chmod($popUploadDir, 0777);
    echo "POP directory already exists, updated permissions\n";
}

// Verify permissions
echo "\nVerifying permissions...\n";
echo "Base uploads dir permissions: " . substr(sprintf('%o', fileperms($baseUploadDir)), -4) . "\n";
echo "POP dir permissions: " . substr(sprintf('%o', fileperms($popUploadDir)), -4) . "\n";
echo "Base uploads dir writable: " . (is_writable($baseUploadDir) ? "Yes" : "No") . "\n";
echo "POP dir writable: " . (is_writable($popUploadDir) ? "Yes" : "No") . "\n";
