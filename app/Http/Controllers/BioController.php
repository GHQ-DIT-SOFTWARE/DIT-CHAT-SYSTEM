<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

class BioController extends Controller
{
    // Show all stored fingerprints
    public function index()
    {
        $files = [];
        $dir = public_path('fingerprints');
    
        if (file_exists($dir)) {
            $files = array_map(function ($file) {
                return asset('fingerprints/' . basename($file));
            }, glob($dir . '/*.{bmp,png,jpg,jpeg}', GLOB_BRACE));
        }
    
        return view('bio.index', compact('files'));
    }


    // Accepts base64 JSON data from scanner
    
    public function upload(Request $request)
    {
        // Log request for debugging
        \Log::info('Incoming fingerprint upload', [
            'files' => $request->files->all(),
        ]);
    
        $request->validate([
            'fingerprint' => 'required|file|mimes:bmp,png,jpg,jpeg|max:20480', // 20MB
        ]);
    
        $file = $request->file('fingerprint');
    
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file upload',
            ], 400);
        }
    
        $dir = public_path('fingerprints');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    
        $fileName = 'fingerprint_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $fileName);
    
        \Log::info("Fingerprint saved", [
            'path' => $dir . '/' . $fileName,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Fingerprint uploaded successfully',
            'file' => asset('fingerprints/' . $fileName),
        ]);
    }


}