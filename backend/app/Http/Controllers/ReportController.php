<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate incoming data
        $request->validate([
            'type' => 'required|string',
            'intensity' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240', // Max 10MB
        ]);

        $evidencePath = null;

        // 2. Handle File Upload (Feature 17)
        if ($request->hasFile('evidence')) {
            // Saves to storage/app/public/evidence
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        // 3. Save to Database
        $report = Report::create([
            'user_id' => $request->user()->id, // Automatically links to logged-in user
            'type' => $request->type,
            'intensity' => $request->intensity,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'evidence_path' => $evidencePath,
            'is_anonymous' => filter_var($request->isAnonymous, FILTER_VALIDATE_BOOLEAN),
            'status' => 'pending' // Feature 8: Requires admin approval
        ]);

        return response()->json(['message' => 'Report queued for Admin verification.', 'report' => $report], 201);
    }
    // 1. Fetch all pending reports for the Admin
    public function index()
    {
        // We also fetch the user's name unless they chose to be anonymous
        $reports = Report::with('user')->where('status', 'pending')->get();
        return response()->json($reports);
    }

    // 2. Feature 8: Admin Verify/Reject action
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        
        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return response()->json(['message' => 'Incident marked as ' . strtoupper($request->status)]);
    }

    // Fetch all approved reports for the public live map
    public function mapData()
    {
        // We only send the necessary data to keep the app fast
        $reports = Report::where('status', 'approved')
            ->get(['id', 'latitude', 'longitude', 'type', 'intensity', 'is_anonymous']);
            
        return response()->json($reports);
    }

    public function requestPoints(Request $request)
    {
      // Feature 18: Logic to log a request for points
      // For now, we'll mark the user as having a "pending_points" status
      $user = $request->user();
      $user->update(['status' => 'pending_points']); // This signals the admin to check them

      return response()->json(['message' => 'Point request transmitted to High Command.']);
    }
}