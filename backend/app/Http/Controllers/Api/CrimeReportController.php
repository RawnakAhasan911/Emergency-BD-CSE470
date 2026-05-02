<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrimeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\CrimeStatusUpdated; // Ensure you have this event created for WebSockets/Pusher

class CrimeReportController extends Controller
{
    /**
     * Fetch latest approved crimes for the feed.
     */
    public function feed()
    {
        $crimes = CrimeReport::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return response()->json(['data' => $crimes], 200);
    }

    /**
     * Fetch approved crimes with coordinates for the map.
     */
    public function mapData()
    {
        $crimes = CrimeReport::where('status', 'approved')
            ->select('id', 'crime_type', 'intensity', 'latitude', 'longitude', 'area', 'city')
            ->get();
            
        return response()->json(['data' => $crimes], 200);
    }

    /**
     * Store a new crime report from a citizen or guest.
     */
    public function store(Request $request)
    {
        $user = auth('sanctum')->user(); // Can be null if guest mode is enabled

        // Deduct points if it is a registered citizen user
        if ($user && $user->role === 'citizen') {
            if ($user->points < 1) {
                return response()->json(['error' => 'Insufficient points. Please request more from an Admin.'], 402); 
            }
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'crime_type' => 'required|string',
            'intensity' => 'required|integer|min:1|max:5', // Feature 14: Keep track of crime type and intensity
            'description' => 'nullable|string|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_anonymous' => 'boolean', // Feature 12: User can keep themselves anonymous
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240', // Feature 17: Photo/video evidence (max 10MB)
            'media_link' => 'nullable|url' // Feature 17: News article link
        ]);

        DB::beginTransaction();
        try {
            $report = new CrimeReport($validated);
            $report->user_id = $user ? $user->id : null;
            $report->is_anonymous = $request->is_anonymous ?? false;
            
            // Feature 17: Secure Media Storage handling
            if ($request->hasFile('media')) {
                // Store in storage/app/public/crimes
                $report->media_path = $request->file('media')->store('crimes', 'public');
            }
            
            if ($request->filled('media_link')) {
                $report->media_link = $request->media_link;
            }

            // Status is 'pending' by default based on database migration
            $report->save();

            // Deduct 1 token upon successful submission
            if ($user && $user->role === 'citizen') {
                $user->decrement('points', 1);
            }

            DB::commit();
            return response()->json([
                'message' => 'Report submitted successfully and is awaiting Admin approval.', 
                'data' => $report
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Submission failed. Please try again.'], 500);
        }
    }

    /**
     * Admin Panel: Approve or Reject a crime report.
     * Feature 8: Only Admin can accept an entry from the user
     */
    public function moderate(Request $request, $id)
    {
        // This route should be protected by the CheckRole middleware in api.php
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $report = CrimeReport::findOrFail($id);
        $report->status = $validated['status'];
        $report->save();

        // Feature 15: Live map marker
        // If approved, trigger an event to broadcast to the Vue frontend (e.g., using Pusher/Laravel Reverb)
        if ($report->status === 'approved') {
            event(new CrimeStatusUpdated($report)); 
        }

        return response()->json([
            'message' => "Crime report has been {$validated['status']} successfully."
        ], 200);
    }
}