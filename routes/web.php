<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\DonationModerationController as AdminDonationModerationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ClaimProofController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Donor\ClaimController as DonorClaimController;
use App\Http\Controllers\Donor\DashboardController as DonorDashboardController;
use App\Http\Controllers\Donor\DonationController as DonorDonationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Receiver\ClaimController as ReceiverClaimController;
use App\Http\Controllers\Receiver\DashboardController as ReceiverDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/suspended', 'auth.suspended')->name('suspended');

Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
Route::get('/donations/{donation}/photo', [DonationController::class, 'photo'])
    ->middleware(['signed'])
    ->name('donations.photo');

Route::get('/claims/{claim}/proof', [ClaimProofController::class, 'show'])
    ->middleware(['auth:sanctum,web', 'not_suspended', 'signed'])
    ->name('claims.proof');

Route::middleware(['auth', 'not_suspended', 'role:admin,donor,receiver'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

Route::get('/avatars/{user}', [ProfileController::class, 'showAvatar'])
    ->middleware(['auth:sanctum,web', 'not_suspended', 'signed'])
    ->name('avatars.show');

Route::prefix('donor')
    ->middleware(['auth', 'not_suspended', 'role:donor'])
    ->group(function () {
        Route::get('/dashboard', DonorDashboardController::class)->name('donor.dashboard');
        Route::get('/donations', [DonorDonationController::class, 'index'])->name('donor.donations.index');
        Route::get('/donations/create', [DonorDonationController::class, 'create'])->name('donor.donations.create');
        Route::post('/donations', [DonorDonationController::class, 'store'])->name('donor.donations.store');
        Route::get('/donations/{donation}/edit', [DonorDonationController::class, 'edit'])->name('donor.donations.edit');
        Route::put('/donations/{donation}', [DonorDonationController::class, 'update'])->name('donor.donations.update');
        Route::post('/donations/{donation}/cancel', [DonorDonationController::class, 'cancel'])->name('donor.donations.cancel');
        Route::get('/claims', [DonorClaimController::class, 'index'])->name('donor.claims.index');
        Route::post('/claims/{claim}/verify', [DonorClaimController::class, 'verify'])->name('donor.claims.verify');
    });

Route::prefix('receiver')
    ->middleware(['auth', 'not_suspended', 'role:receiver'])
    ->group(function () {
        Route::get('/dashboard', ReceiverDashboardController::class)->name('receiver.dashboard');
        Route::get('/claims', [ReceiverClaimController::class, 'index'])->name('receiver.claims');
        Route::post('/donations/{donation}/claim', [ReceiverClaimController::class, 'store'])->middleware('throttle:api-claim')->name('receiver.donations.claim');
        Route::get('/claims/{claim}/proof', [ReceiverClaimController::class, 'proofForm'])->name('receiver.claims.proof.form');
        Route::post('/claims/{claim}/proof', [ReceiverClaimController::class, 'uploadProof'])->middleware('throttle:api-claim')->name('receiver.claims.proof.upload');
    });

Route::prefix('admin')
    ->middleware(['auth', 'not_suspended', 'role:admin'])
    ->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('admin.users.unsuspend');
        Route::get('/donations', [AdminDonationModerationController::class, 'index'])->name('admin.donations');
        Route::post('/donations/{donation}/approve', [AdminDonationModerationController::class, 'approve'])->name('admin.donations.approve');
        Route::post('/donations/{donation}/reject', [AdminDonationModerationController::class, 'reject'])->name('admin.donations.reject');
        Route::resource('categories', AdminCategoryController::class)->except(['show'])->names('admin.categories');
        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('admin.audit-log');
        Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/reports/donations.csv', [AdminReportController::class, 'donationsCsv'])->name('admin.reports.donations.csv');
    });

require __DIR__.'/auth.php';
