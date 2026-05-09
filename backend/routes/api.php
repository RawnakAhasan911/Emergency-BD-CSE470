<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Api\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Anyone can access these, even guests who are not logged in.
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Feature 17 & 14: Homepage feed and live map data are public
// Route::get('/crimes/feed', [ReportController::class, 'feed']);
// Route::get('/crimes/map', [ReportController::class, 'mapData']);

/*
|--------------------------------------------------------------------------
| Protected User Routes
|--------------------------------------------------------------------------
| You must be logged in (Sanctum) to access these.
*/

// Note: I removed 'check.status' temporarily to ensure the frontend works. 
// You can add it back to the array once you build that specific middleware!
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // ==========================================
    // CITIZEN ROUTES
    // ==========================================
    // Submit a new emergency report (Matches your Dashboard.vue axios call)
    Route::post('/reports', [ReportController::class, 'store']); 
    
    // Feature 18: Request points from an admin
    Route::post('/points/request', [AdminController::class, 'requestPoints']); 

    // ==========================================
    // ADMIN ROUTES
    // ==========================================
    // Ideally, these go inside a 'role:admin' middleware group later.
    // For now, they are here so your AdminDashboard.vue table works perfectly.
    
    // Fetch pending reports for the Admin Table
    Route::get('/reports/pending', [ReportController::class, 'index']);
    // Fetch approved map markers
    Route::get('/reports/map', [ReportController::class, 'mapData']);
    
    // Feature 8: Admin Moderation Panel (Approve/Reject)
    Route::patch('/reports/{id}/status', [ReportController::class, 'updateStatus']);
    
    // Feature 4 & 10: Suspend or Reactivate a user
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleStatus']);
    
    // Feature 9: Give points to a user
    Route::post('/users/{id}/assign-points', [AdminController::class, 'assignPoints']);
    
    // Feature 20: Danger Zone (Hard Deletes)
    Route::delete('/reports/{id}', [AdminController::class, 'hardDeleteCrime']);
    Route::delete('/users/{id}', [AdminController::class, 'hardDeleteUser']);
    
    // User Management View
    Route::get('/users', [AdminController::class, 'indexUsers']);
});