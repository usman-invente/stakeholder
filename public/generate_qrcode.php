<?php

require __DIR__ . '/../vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

// This is a standalone script to generate QR codes on servers where you don't have terminal access
// Usage: Access this file via the browser with a meeting ID parameter, e.g., 
// https://yoursite.com/generate_qrcode.php?meeting_id=12345

// Initialize Laravel's application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get the meeting ID from the URL
$meetingId = $_GET['meeting_id'] ?? null;

if (!$meetingId) {
    die('Please provide a meeting_id parameter');
}

// Get the visitor from the database
$visitor = \App\Models\Visitor::where('meeting_id', $meetingId)->first();

if (!$visitor) {
    die('Meeting ID not found');
}

// Define paths
$meetingUrl = url('/meetings/' . $visitor->meeting_id);
$fileName = 'qrcode_' . $visitor->meeting_id . '.png';

// Try both storage paths
$storagePath = public_path('storage/qrcodes');
$directPath = public_path('qrcodes');

// Create the directories if they don't exist
if (!file_exists($storagePath)) {
    if (!@mkdir($storagePath, 0755, true)) {
        echo "Failed to create storage/qrcodes directory. Using direct path.<br>";
        $storagePath = null;
    }
}

if (!file_exists($directPath)) {
    if (!@mkdir($directPath, 0755, true)) {
        echo "Failed to create qrcodes directory.<br>";
        if (!$storagePath) {
            die("Could not create any QR code directory. Check permissions.");
        }
    }
}

// Determine which path to use
$path = $storagePath ? $storagePath . '/' . $fileName : $directPath . '/' . $fileName;

// Generate QR code
try {
    QrCode::format('png')
        ->size(300)
        ->errorCorrection('H')
        ->generate($meetingUrl, $path);
    
    echo "QR code generated successfully at: " . $path . "<br>";
    echo "QR Code URL: " . asset(str_replace(public_path(), '', $path)) . "<br>";
    echo "<img src='" . asset(str_replace(public_path(), '', $path)) . "' alt='QR Code'>";
} catch (Exception $e) {
    echo "Error generating QR code: " . $e->getMessage();
}