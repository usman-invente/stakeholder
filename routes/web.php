<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\StakeholderCommunicationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User routes
    Route::resource('users', UserController::class);
    
    // Stakeholder routes
    Route::resource('stakeholders', StakeholderController::class);
    
    // Stakeholder Communications routes
    Route::get('communications/report', [StakeholderCommunicationController::class, 'report'])
        ->name('stakeholder-communications.report');
    Route::get('communications/export', [StakeholderCommunicationController::class, 'export'])
        ->name('stakeholder-communications.export');
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

require __DIR__.'/auth.php';

Auth::routes();
