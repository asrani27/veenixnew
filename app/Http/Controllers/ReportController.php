<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'movie_id' => 'required|exists:movie,id',
                'movie_title' => 'required|string|max:255',
                'issue_type' => 'required|in:video_not_playing,broken_link,poor_quality,audio_problem,subtitle_issue,other',
                'description' => 'required|string|min:10|max:1000',
                'email' => 'nullable|email|max:255',
            ]);

            $report = Report::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your report! We will look into this issue shortly.',
                'report' => $report
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Report submission error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your report. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $reports = Report::with('movie')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load('movie');
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update($validated);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Report updated successfully.'
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully.');
    }

    public function destroy(Request $request, Report $report)
    {
        $report->delete();

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully.'
            ]);
        }

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
