<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\StakeholderCommunicationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminMiddleware;
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin routes
    Route::middleware(['admin'])->group(function () {
        // Settings routes
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        // User management routes
        Route::resource('users', UserController::class);
        
        // Activity log routes
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });
    
    // Stakeholder routes - accessible to both admin and regular users
    Route::middleware(['auth'])->group(function () {
        Route::resource('stakeholders', StakeholderController::class);
        
        // Stakeholder Communications routes
        Route::get('communications/report', [StakeholderCommunicationController::class, 'report'])
            ->name('stakeholder-communications.report');
        Route::get('communications/export', [StakeholderCommunicationController::class, 'export'])
            ->name('stakeholder-communications.export');
        Route::get('communications/export-csv', [App\Http\Controllers\ExportFallbackController::class, 'exportCsv'])
            ->name('stakeholder-communications.export-csv');
        Route::get('stakeholders/{stakeholder}/communications', [StakeholderCommunicationController::class, 'index'])
            ->name('stakeholder-communications.index');
        Route::get('stakeholders/{stakeholder}/communications/create', [StakeholderCommunicationController::class, 'create'])
            ->name('stakeholder-communications.create');
        Route::post('stakeholders/{stakeholder}/communications', [StakeholderCommunicationController::class, 'store'])
            ->name('stakeholder-communications.store');
        Route::get('stakeholders/{stakeholder}/communications/{communication}', [StakeholderCommunicationController::class, 'show'])
            ->name('stakeholder-communications.show');
        Route::get('stakeholders/{stakeholder}/communications/{communication}/edit', [StakeholderCommunicationController::class, 'edit'])
            ->name('stakeholder-communications.edit');
        Route::put('stakeholders/{stakeholder}/communications/{communication}', [StakeholderCommunicationController::class, 'update'])
            ->name('stakeholder-communications.update');
        Route::delete('stakeholders/{stakeholder}/communications/{communication}', [StakeholderCommunicationController::class, 'destroy'])
            ->name('stakeholder-communications.destroy');
    });
});

require __DIR__.'/auth.php';

Auth::routes();
