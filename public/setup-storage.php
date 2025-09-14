<?php

// This script ensures that the QR code directories exist
// It's a simpler alternative to running 'php artisan storage:link'

// Define the directories we need
$directories = [
    __DIR__ . '/qrcodes',
    __DIR__ . '/storage',
    __DIR__ . '/storage/qrcodes'
];

// Create each directory if it doesn't exist
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "Created directory: $dir<br>";
        } else {
            echo "Failed to create directory: $dir<br>";
        }
    } else {
        echo "Directory already exists: $dir<br>";
    }
}

// Try to create a symlink from public/storage to storage/app/public if it doesn't exist
if (!file_exists(__DIR__ . '/storage') || !is_link(__DIR__ . '/storage')) {
    $target = __DIR__ . '/../storage/app/public';
    $link = __DIR__ . '/storage';
    
    // Remove the directory first if it exists but isn't a symlink
    if (file_exists($link) && !is_link($link)) {
        // Check if it's empty
        $files = scandir($link);
        if (count($files) <= 2) { // . and .. entries
            rmdir($link);
        } else {
            echo "The storage directory exists but is not a symlink and contains files. Cannot create symlink.<br>";
            exit;
        }
    }
    
    if (@symlink($target, $link)) {
        echo "Created symbolic link from $link to $target<br>";
    } else {
        echo "Failed to create symbolic link. This is common on shared hosting.<br>";
        echo "The system will fall back to using direct paths.<br>";
    }
}

echo "<p>Setup complete! You can now use the QR code functionality.</p>";
echo "<p><a href='/' class='btn btn-primary'>Return to home page</a></p>";