<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\StakeholderCommunicationController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('main/dashboard', [App\Http\Controllers\DashboardController::class, 'mainDashboard'])
    ->middleware(['auth'])
    ->name('main.dashboard');

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
        // Test email route
        Route::get('/send-test-email', [MailTestController::class, 'sendTestEmail'])
            ->name('send.test.email');

        Route::resource('stakeholders', StakeholderController::class);
        Route::get('stakeholders-export', [StakeholderController::class, 'export'])
            ->name('stakeholders.export');
        Route::get('stakeholders-import', [StakeholderController::class, 'importForm'])
            ->name('stakeholders.import.form');
        Route::post('stakeholders-import', [StakeholderController::class, 'import'])
            ->name('stakeholders.import');

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

// Contract Management Routes - accessible to admin and contract_creator
Route::middleware(['auth', 'contract_creator'])->group(function () {
    Route::get('contracts/dashboard', [ContractController::class, 'dashboard'])
        ->name('contracts.dashboard');
    Route::get('contracts/reports', [App\Http\Controllers\ContractReportController::class, 'index'])
        ->name('contracts.reports');
    Route::get('contracts/reports/export', [App\Http\Controllers\ContractReportController::class, 'export'])
        ->name('contracts.reports.export');
    Route::get('contracts/reports/{year}/{month}', [App\Http\Controllers\ContractReportController::class, 'monthlyDetail'])
        ->name('contracts.reports.monthly');
    Route::get('contracts/generate-id', [ContractController::class, 'generateId'])
        ->name('contracts.generate-id');
    Route::get('contracts/{contract}/download', [ContractController::class, 'downloadDocument'])
        ->name('contracts.download');
    Route::resource('contracts', ContractController::class);
    
    // Department Management Routes (for adding more departments)
    Route::get('departments', [App\Http\Controllers\DepartmentController::class, 'index'])
        ->name('departments.index');
    Route::post('departments', [App\Http\Controllers\DepartmentController::class, 'store'])
        ->name('departments.store');
    Route::put('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'update'])
        ->name('departments.update');
    Route::post('departments/{department}/toggle', [App\Http\Controllers\DepartmentController::class, 'toggleStatus'])
        ->name('departments.toggle');
    Route::delete('departments/{department}', [App\Http\Controllers\DepartmentController::class, 'destroy'])
        ->name('departments.destroy');
});

require __DIR__ . '/auth.php';

Auth::routes();

// Visitor Registration System (Public Routes)
Route::get('/visitor', [VisitorController::class, 'showRegistrationForm'])->name('visitor.register');
Route::post('/visitor', [VisitorController::class, 'store'])->name('visitor.store');
Route::post('/form/update', [VisitorController::class, 'updateFormData'])->name('form.update');

// Meeting Details (Public Route)
Route::get('/meetings/{meetingId}', [App\Http\Controllers\MeetingController::class, 'show'])->name('meetings.show');

// Receptionist-only routes (Protected by receptionist middleware)
Route::middleware(['receptionist'])->group(function () {
    Route::get('/receptionist', [VisitorController::class, 'showReceptionistView'])->name('receptionist.view');
    
});

Route::get('/form/fetch/{sessionId}', [VisitorController::class, 'getFormData'])->name('form.fetch');
    Route::get('/form/active-sessions', [VisitorController::class, 'getActiveSessions'])->name('form.active-sessions');
    Route::post('/form/update-receptionist', [VisitorController::class, 'updateFormDataByReceptionist'])->name('form.update-receptionist');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::post('/visitors/{id}/checkout', [VisitorController::class, 'updateCheckout'])->name('visitors.checkout');
    Route::post('/visitors/{id}/follow-up', [VisitorController::class, 'updateFollowUp'])->name('visitors.follow-up');
    Route::delete('/visitors/{id}', [VisitorController::class, 'destroy'])->name('visitors.destroy');
