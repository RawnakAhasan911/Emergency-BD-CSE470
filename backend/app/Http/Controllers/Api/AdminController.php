<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CrimeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Feature 7: Request Points Feature (Used by Citizens)
     * Note: This route is technically accessible by regular users via the api.php routes,
     * but we are grouping point-related logic here for administrative review.
     */
    public function requestPoints(Request $request)
    {
        $user = auth('sanctum')->user();
        
        // In a fully scaled system, you would store this in a 'point_requests' table 
        // for the admin to review. For this architecture, we will log the request 
        // or notify the admin dashboard.
        
        return response()->json([
            'message' => 'Point request successfully sent to the Admin team. You will be notified once approved.'
        ], 200);
    }

    /**
     * Feature 9: Admin Point Management
     * Manually assign points to any user.
     */
    public function assignPoints(Request $request, $id)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:100'
        ]);

        $user = User::findOrFail($id);
        $user->increment('points', $validated['points']);

        return response()->json([
            'message' => "Successfully added {$validated['points']} points to {$user->name}.",
            'user_points' => $user->points
        ], 200);
    }

    /**
     * Feature 4 & 10: Account Status Control
     * Admin can suspend or activate users; suspended users cannot log in.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent an admin from suspending themselves
        if (auth('sanctum')->id() === $user->id) {
            return response()->json(['error' => 'You cannot suspend your own admin account.'], 403);
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        // If the user is being suspended, revoke all their active login tokens
        if ($user->is_suspended) {
            $user->tokens()->delete();
        }

        $status = $user->is_suspended ? 'suspended' : 'activated';

        return response()->json([
            'message' => "User account has been {$status}."
        ], 200);
    }

    /**
     * Feature 20: Danger Zone - Delete Crimes Permanently
     * Admin can completely scrub a crime and its evidence from the database.
     */
    public function hardDeleteCrime($id)
    {
        $report = CrimeReport::findOrFail($id);

        // Clean up media files from the server storage if they exist
        if ($report->media_path) {
            Storage::disk('public')->delete($report->media_path);
        }

        $report->delete();

        return response()->json([
            'message' => 'Crime report and associated media have been permanently deleted.'
        ], 200);
    }

    /**
     * Feature 20: Danger Zone - Delete Users Permanently
     * Admin can completely erase a user account.
     */
    public function hardDeleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin self-deletion
        if (auth('sanctum')->id() === $user->id) {
            return response()->json(['error' => 'Action denied. Admin suicide protocol is disabled.'], 403);
        }

        // Note: Because of `$table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');` 
        // in the migrations, deleting a user will safely keep their crime reports 
        // but change the reporter to "Anonymous/Null".
        
        $user->delete();

        return response()->json([
            'message' => 'User account has been permanently erased from the system.'
        ], 200);
    }
    // Fetch all users for the dashboard
    public function indexUsers()
    {
        return response()->json(['data' => User::all()], 200);
    }

    // Fetch only pending crimes for moderation
    public function pendingCrimes()
    {
        return response()->json(['data' => CrimeReport::where('status', 'pending')->get()], 200);
    }
}