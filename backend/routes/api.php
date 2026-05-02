<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CrimeReportController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Anyone can access these, even guests who are not logged in.
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Feature 17 & 14: Homepage feed and live map data are public
Route::get('/crimes/feed', [CrimeReportController::class, 'feed']);
Route::get('/crimes/map', [CrimeReportController::class, 'mapData']);


/*
|--------------------------------------------------------------------------
| Protected User Routes
|--------------------------------------------------------------------------
| You must be logged in (Sanctum) AND your account must not be suspended.
*/

Route::middleware(['auth:sanctum', 'check.status'])->group(function () {
    
    // Feature 6, 8, 9, 10, 11: Submit a new crime report
    Route::post('/crimes/report', [CrimeReportController::class, 'store']); 
    
    // Feature 18: Request points from an admin
    Route::post('/points/request', [AdminController::class, 'requestPoints']); 

    /*
    |--------------------------------------------------------------------------
    | Admin-Only Routes
    |--------------------------------------------------------------------------
    | Protected by the CheckRole middleware ('role:admin').
    */
    
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        
        // Feature 8: Admin Moderation Panel (Approve/Reject)
        Route::post('/crimes/{id}/moderate', [CrimeReportController::class, 'moderate']);
        
        // Feature 4 & 10: Suspend or Reactivate a user
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus']);
        
        // Feature 9: Give points to a user
        Route::post('/users/{id}/assign-points', [AdminController::class, 'assignPoints']);
        
        // Feature 20: Danger Zone (Hard Deletes)
        Route::delete('/crimes/{id}', [AdminController::class, 'hardDeleteCrime']);
        Route::delete('/users/{id}', [AdminController::class, 'hardDeleteUser']);
        
        Route::get('/users', [AdminController::class, 'indexUsers']);
        Route::get('/crimes/pending', [AdminController::class, 'pendingCrimes']);
    
    });

});