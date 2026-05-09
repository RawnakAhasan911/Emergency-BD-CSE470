<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Feature 3: Get all registered citizens (excluding admins)
    public function indexUsers()
    {
        $users = User::where('role', 'citizen')->get(['id', 'name', 'email', 'status', 'points']);
        return response()->json($users);
    }

    // Feature 10: Block or Enable an account
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Swap between active and suspended
        $user->status = ($user->status === 'active') ? 'suspended' : 'active';
        $user->save();

        return response()->json([
            'message' => 'User ' . $user->name . ' is now ' . strtoupper($user->status),
            'new_status' => $user->status
        ]);
    }

    // Feature 9: Give points to a user
    public function assignPoints(Request $request, $id)
    {
        $request->validate(['points' => 'required|integer|min:1']);
        
        $user = User::findOrFail($id);
        $user->points += $request->points; // Add the new points to their current total
        $user->save();

        return response()->json([
            'message' => 'Added ' . $request->points . ' points to ' . $user->name,
            'total_points' => $user->points
        ]);
    }
}