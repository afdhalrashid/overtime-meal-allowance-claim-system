<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\StaffDashboard;
use App\Livewire\Dashboard\ApproverDashboard;
use App\Livewire\Dashboard\HrDashboard;
use App\Livewire\Claims\ClaimForm;
use App\Livewire\Settings\SystemSettings;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        // dd($user->role);
        return match ($user->role) {
            'staff' => redirect()->route('staff.dashboard'),
            'approver' => redirect()->route('approver.dashboard'),
            'hr_admin', 'payroll' => redirect()->route('hr.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    // Staff routes
    Route::get('/staff/dashboard', StaffDashboard::class)
        ->middleware('role:staff')
        ->name('staff.dashboard');

    Route::get('/claims', \App\Livewire\Claims\ClaimList::class)
        ->middleware('role:staff')
        ->name('claims.index');

    Route::get('/claims/create', ClaimForm::class)
        ->middleware('role:staff')
        ->name('claims.create');

    Route::get('/claims/{claim}/edit', ClaimForm::class)
        ->middleware('role:staff')
        ->name('claims.edit');

    // Claim details route (accessible to all authenticated users)
    Route::get('/claims/{id}/details', \App\Livewire\Claims\ClaimDetails::class)
        ->name('claims.details');

    // Approver routes
    Route::get('/approver/dashboard', ApproverDashboard::class)
        ->middleware('role:approver')
        ->name('approver.dashboard');

    // HR and Payroll routes
    Route::get('/hr/dashboard', HrDashboard::class)
        ->middleware('role:hr_admin,payroll')
        ->name('hr.dashboard');

    // System Settings (HR Admin only)
    Route::get('/settings', SystemSettings::class)
        ->middleware('role:hr_admin')
        ->name('settings');

    // Audit Logs (HR Admin only)
    Route::get('/audit-logs', \App\Livewire\Admin\AuditLogList::class)
        ->middleware('role:hr_admin')
        ->name('audit.logs');

    // Document viewing route
    Route::get('/documents/{document}/view', [App\Http\Controllers\DocumentController::class, 'view'])
        ->name('documents.view');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
