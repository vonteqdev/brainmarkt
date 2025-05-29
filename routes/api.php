<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AgencyController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\TeamMemberController;

// V1 API Routes
Route::prefix('v1')->group(function () {

    // *** INCLUDE BREEZE AUTH ROUTES HERE ***
    // This assumes Breeze created a 'routes/auth.php' with the necessary routes
    // like /register, /login, etc., without any further prefixing within that file.
    if (file_exists(__DIR__.'/auth.php')) { // Good practice to check if file exists
        require __DIR__.'/auth.php';
    } else {
        // Fallback or define them manually if routes/auth.php doesn't exist as expected
        // This part would only be needed if Breeze didn't create routes/auth.php
        // For example, if Breeze directly modified routes/web.php or another file
        // and you need to manually define API versions for those.
        // It's better to find where Breeze put them and include that file.
        // Log::warning('routes/auth.php not found for API v1 inclusion.');
    }

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user()->load(['agency', 'role.permissions']);
        })->name('api.v1.user'); // Consider namespacing route names too

        // Agency Management
        Route::get('/agency', [AgencyController::class, 'show'])->name('api.v1.agency.show');
        Route::put('/agency', [AgencyController::class, 'update'])->name('api.v1.agency.update');

        // Team Member Management
        Route::prefix('agency/team')->name('api.v1.agency.team.')->group(function () {
            Route::get('/', [TeamMemberController::class, 'index'])->name('index');
            Route::post('/invite', [TeamMemberController::class, 'invite'])->name('invite');
            Route::put('/{user}', [TeamMemberController::class, 'update'])->name('update');
            Route::delete('/{user}', [TeamMemberController::class, 'destroy'])->name('destroy');
        });

        // Client (Advertiser) Management
        Route::apiResource('clients', ClientController::class)->names('api.v1.clients');
    });
});
