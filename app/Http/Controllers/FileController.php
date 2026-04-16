<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Serve private files stored in storage/app/private
     */
    public function servePrivateFile(Request $request)
    {
        $path = $request->query('path');
        
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // Allow access if:
        // 1. User is authenticated
        // 2. OR the request has a valid signature (e.g. temporary preview for guests)
        if (!Auth::check() && !$request->hasValidSignature()) {
            abort(403);
        }

        return Storage::disk('local')->response($path);
    }
}
