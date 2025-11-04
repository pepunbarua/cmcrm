<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorTypeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventNoteController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Vendors
    Route::resource('vendors', VendorController::class);

    // Leads
    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::get('/leads-import', [LeadController::class, 'importForm'])->name('leads.import.form');
    Route::post('/leads-preview', [LeadController::class, 'preview'])->name('leads.preview');
    Route::post('/leads-import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('/leads-template', [LeadController::class, 'downloadTemplate'])->name('leads.template');

    // Follow-ups
    Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::post('/follow-ups/{followUp}/update-status', [FollowUpController::class, 'updateStatus'])->name('follow-ups.update-status');
    Route::delete('/follow-ups/{followUp}', [FollowUpController::class, 'destroy'])->name('follow-ups.destroy');

    // Orders
    Route::resource('orders', OrderController::class);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Events
    Route::resource('events', EventController::class);
    
    // Event Notes
    Route::post('/event-notes', [EventNoteController::class, 'store'])->name('event-notes.store');
    Route::delete('/event-notes/{eventNote}', [EventNoteController::class, 'destroy'])->name('event-notes.destroy');
    
    // Team Members
    Route::resource('team', TeamMemberController::class)->middleware('permission:view team members');
    Route::patch('/team/{teamMember}/availability', [TeamMemberController::class, 'updateAvailability'])
        ->name('team.update-availability')
        ->middleware('permission:edit team members');
    
    // Deliverables
    Route::resource('deliverables', DeliverableController::class)->middleware('permission:view deliverables');
    Route::get('/deliverables/{deliverable}/download', [DeliverableController::class, 'download'])
        ->name('deliverables.download')
        ->middleware('permission:view deliverables');
    Route::patch('/deliverables/{deliverable}/status', [DeliverableController::class, 'updateStatus'])
        ->name('deliverables.update-status')
        ->middleware('permission:edit deliverables');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:view reports');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue')->middleware('permission:view reports');
    Route::get('/reports/events', [ReportController::class, 'events'])->name('reports.events')->middleware('permission:view reports');
    Route::get('/reports/team', [ReportController::class, 'team'])->name('reports.team')->middleware('permission:view reports');
    
    // Settings - Company Profile
    Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company')->middleware('permission:manage users');
    Route::post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company.update')->middleware('permission:manage users');
    
    // Settings - General
    Route::get('/settings/general', [SettingsController::class, 'general'])->name('settings.general')->middleware('permission:manage users');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update')->middleware('permission:manage users');
    
    // Settings - Vendor Types
    Route::resource('settings/vendor-types', VendorTypeController::class)->names([
        'index' => 'settings.vendor-types.index',
        'create' => 'settings.vendor-types.create',
        'store' => 'settings.vendor-types.store',
        'edit' => 'settings.vendor-types.edit',
        'update' => 'settings.vendor-types.update',
        'destroy' => 'settings.vendor-types.destroy',
    ])->middleware('permission:manage users');
    
    // User Management
    Route::resource('users', UserController::class)->middleware('permission:manage users');
    
    // Roles & Permissions
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:manage users');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:manage users');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:manage users');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:manage users');
});
