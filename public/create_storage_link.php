<?php

// Get the paths
$targetPath = __DIR__ . '/../storage/app/public';
$linkPath = __DIR__ . '/storage';

// Check if the link already exists
if (file_exists($linkPath)) {
    echo "The storage link already exists!";
} else {
    // Create the symbolic link
    if (symlink($targetPath, $linkPath)) {
        echo "Storage link created successfully!";
    } else {
        echo "Failed to create the storage link. Try adjusting permissions or contact your hosting provider.";
    }
}