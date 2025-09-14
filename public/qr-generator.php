<?php

require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Encoding\Encoding;

// Initialize Laravel's application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create directories for QR codes
$directories = [
    __DIR__ . '/qrcodes',
    __DIR__ . '/storage',
    __DIR__ . '/storage/qrcodes'
];

foreach ($directories as $directory) {
    if (!file_exists($directory)) {
        @mkdir($directory, 0755, true);
    }
}

// Get meeting ID from URL or generate sample QR code
$meetingId = $_GET['meeting_id'] ?? null;

if ($meetingId) {
    // Find visitor by meeting ID
    $visitor = \App\Models\Visitor::where('meeting_id', $meetingId)->first();
    
    if (!$visitor) {
        echo "<p>Meeting ID not found. Generating sample QR code instead.</p>";
        $url = url('/meetings/sample');
        $title = "Sample QR Code";
    } else {
        $url = url('/meetings/' . $visitor->meeting_id);
        $title = "QR Code for " . $visitor->full_name;
    }
} else {
    $url = url('/');
    $title = "Sample QR Code";
}

// Generate QR code
try {
    $qrCode = new QrCode(
        $url,
        new Encoding('UTF-8'),
        ErrorCorrectionLevel::High,
        300,
        10
    );
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Display QR code
    echo "<h1>{$title}</h1>";
    echo "<img src='data:image/png;base64," . base64_encode($result->getString()) . "' alt='QR Code'>";
    echo "<p>URL: {$url}</p>";
    
    // Save to file if needed
    if ($meetingId) {
        $fileName = 'qrcode_' . $meetingId . '.png';
        $directPath = __DIR__ . '/qrcodes/' . $fileName;
        file_put_contents($directPath, $result->getString());
        echo "<p>QR code saved to: /qrcodes/{$fileName}</p>";
        echo "<p>Direct URL: <a href='/qrcodes/{$fileName}'>/qrcodes/{$fileName}</a></p>";
    }
} catch (\Exception $e) {
    echo "<p>Error generating QR code: " . $e->getMessage() . "</p>";
}