<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index()
    {
        $logFile = storage_path('logs/products.log');
        $logs = [];

        if (File::exists($logFile)) {
            $fileContents = File::get($logFile);
            // Capture entire log entries or just headers for simplicity
            preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/', $fileContents, $matches);
            $logs = array_reverse($matches[0]);
        }

        return view('admin.logs.index', compact('logs'));
    }
}
