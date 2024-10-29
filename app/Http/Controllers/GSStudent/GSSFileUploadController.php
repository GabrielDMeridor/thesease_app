<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GSSFileUploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        $fileType = $request->input('upload_type');
        $file = $request->file("file.$fileType");
    
        if (!$file) {
            return redirect()->back()->with('error', 'No file selected for upload.');
        }
    
        // Define allowed MIME types based on file type
        $allowedMimes = match ($fileType) {
            'immigration_or_studentvisa' => ['jpeg', 'jpg', 'png'],
            'routing_form_one', 'manuscript', 'adviser_appointment_form' => ['pdf'],
            default => []
        };
    
        // Validate the file (size, MIME type)
        $request->validate([
            "file.$fileType" => ['required', 'file', 'mimes:' . implode(',', $allowedMimes), 'max:10240']
        ]);
    
        // Use the original file name, replacing spaces with underscores
        $fileName = preg_replace('/\s+/', '_', $file->getClientOriginalName());
    
        // Determine the storage path based on the file type
        $filePath = match ($fileType) {
            'immigration_or_studentvisa' => 'public/immigrations',
            'routing_form_one' => 'public/routing_forms',
            'manuscript' => 'public/manuscripts',
            'adviser_appointment_form' => 'public/adviser_appointments',
        };
    
        // Store the file with its original name
        $storedPath = $file->storeAs($filePath, $fileName);
    
        // Update the user's record with the file name
        $user = auth()->user();
        $user->$fileType = $fileName;
        $user->save();
    
        return redirect()->back()->with('success', 'File uploaded successfully!');
    }
    
    }
    
