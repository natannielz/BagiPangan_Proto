<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ClaimController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DonationController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/check-email', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
    $exists = \App\Models\User::where('email', $request->email)->exists();

    return response()->json([
        'available' => ! $exists,
        'message'   => $exists ? 'Email sudah terdaftar' : 'Email tersedia',
    ]);
})->middleware('throttle:api-general');

Route::prefix('v1')->middleware('throttle:api-general')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/donations', [DonationController::class, 'index']);
    Route::get('/donations/{donation}', [DonationController::class, 'show']);

    Route::middleware(['auth:sanctum', 'not_suspended'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/avatar', [ProfileController::class, 'avatar']);
        Route::get('/auth/me', [ProfileController::class, 'me']);

        Route::post('/donations/{donation}/claim', [ClaimController::class, 'claimDonation'])->middleware('throttle:api-claim');
        Route::post('/claims/{claim}/proof', [ClaimController::class, 'uploadProof'])->middleware('throttle:api-claim');
        Route::post('/claims/{claim}/verify', [ClaimController::class, 'verify'])->middleware('role:donor,admin');

        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->middleware('role:admin');
        Route::get('/dashboard/top-donors', [DashboardController::class, 'topDonors'])->middleware('role:admin');
    });
});
