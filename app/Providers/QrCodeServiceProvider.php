<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('qrcode', function ($app) {
            return new \SimpleSoftwareIO\QrCode\Generator(
                $app['config']['simple-qrcode.default'],
                $app['config']['simple-qrcode.drivers']
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Force the QR code library to use GD driver instead of Imagick
        config(['simple-qrcode.default' => 'gd']);
    }
}
