<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogViewerController extends Controller
{
    public function index()
    {
        try {
            $logFile = storage_path('logs/products/products.log');
            $logs = [];

            if (File::exists($logFile)) {
                $fileContents = File::get($logFile);
                // Capture entire log entries or just headers for simplicity
                preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/', $fileContents, $matches);
                $logs = array_reverse($matches[0]);
            }

            return view('admin.logs.index', compact('logs'));
        } catch (\Exception $e) {
            Log::error('Admin\LogViewerController@index error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
