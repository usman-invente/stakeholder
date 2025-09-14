<?php

// Get the contents of app.php
$configFile = __DIR__ . '/../config/app.php';
$configContent = file_get_contents($configFile);

// Check if our service provider is already registered
if (strpos($configContent, 'App\Providers\QrCodeServiceProvider') === false) {
    // Look for the providers array
    $pattern = "/\s+'providers'\s+=>\s+\[(.*?)\s+\],/s";
    if (preg_match($pattern, $configContent, $matches)) {
        // Append our service provider to the end of the providers array
        $providersContent = $matches[1];
        $newProvidersContent = $providersContent . "\n        App\\Providers\\QrCodeServiceProvider::class,";
        $updatedContent = str_replace($providersContent, $newProvidersContent, $configContent);
        
        // Write the updated content back to the file
        file_put_contents($configFile, $updatedContent);
        echo "QrCodeServiceProvider successfully added to providers in config/app.php";
    } else {
        echo "Could not find the providers array in config/app.php";
    }
} else {
    echo "QrCodeServiceProvider is already registered in config/app.php";
}

// Create a simple-qrcode.php config file if it doesn't exist
$qrConfigFile = __DIR__ . '/../config/simple-qrcode.php';
if (!file_exists($qrConfigFile)) {
    $qrConfigContent = <<<'EOD'
<?php

return [
    'default' => 'gd',
    
    'drivers' => [
        'imagick' => [
            'path' => base_path('vendor/simple-qrcode/src/ImageHandlers/ImagickImageHandler.php'),
        ],
        'gd' => [
            'path' => base_path('vendor/simple-qrcode/src/ImageHandlers/GdImageHandler.php'),
        ],
    ],
];
EOD;
    
    file_put_contents($qrConfigFile, $qrConfigContent);
    echo "\nCreated simple-qrcode.php config file";
}