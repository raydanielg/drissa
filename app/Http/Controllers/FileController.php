<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serve(Request $request, $path)
    {
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);
        $fileName = basename($path);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
