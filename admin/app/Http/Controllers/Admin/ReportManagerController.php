<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportManagerController extends Controller
{
    /**
     * Display a listing of report files.
     */
    public function index()
    {
        $disk = Storage::disk('reports');
        $files = $disk->files(); // Only gets files in the root, ignores subdirectories like 'archive'

        $reportFiles = [];
        foreach ($files as $file) {
            $reportFiles[] = (object) [
                'name' => $file,
                'size_kb' => round($disk->size($file) / 1024, 2),
                'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->format('d M, Y h:i A'),
            ];
        }

        // Convert to collection for easier view handling
        $reportFiles = collect($reportFiles);

        return view('admin.reports.index', compact('reportFiles'));
    }

    /**
     * Archive a specific report file.
     */
    public function archive(Request $request, $file)
    {
        $disk = Storage::disk('reports');

        if (!$disk->exists($file)) {
            return back()->with('error', "File '{$file}' not found.");
        }

        try {
            // Using copy and then delete as per the prompt requirements
            $disk->copy($file, "archive/{$file}");
            $disk->delete($file);

            return back()->with('success', "File '{$file}' successfully archived.");
        } catch (\Exception $e) {
            return back()->with('error', "Failed to archive '{$file}': " . $e->getMessage());
        }
    }

    /**
     * Delete files older than 30 days.
     */
    public function cleanup()
    {
        $disk = Storage::disk('reports');
        $files = $disk->files();
        $cutoffTimestamp = now()->subDays(30)->timestamp;

        $deletedCount = 0;

        foreach ($files as $file) {
            if ($disk->exists($file) && $disk->lastModified($file) < $cutoffTimestamp) {
                try {
                    $disk->delete($file);
                    $deletedCount++;
                } catch (\Exception $e) {
                    // Fail gracefully, log it, but continue to next file
                    Log::warning("Could not delete {$file} during cleanup: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', "Bulk cleanup completed. {$deletedCount} old file(s) deleted.");
    }

    /**
     * Delete a specific report file.
     */
    public function destroy(Request $request, $file)
    {
        $disk = Storage::disk('reports');

        if (!$disk->exists($file)) {
            return back()->with('error', "File '{$file}' not found.");
        }

        try {
            $disk->delete($file);
            return back()->with('success', "File '{$file}' successfully deleted.");
        } catch (\Exception $e) {
            return back()->with('error', "Failed to delete '{$file}': " . $e->getMessage());
        }
    }
}
