<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GSSFileUploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        // Validate the request based on the file type
        $request->validate([
            'file_type' => 'required|string',
            'file' => 'required|mimes:jpeg,jpg,png,pdf|max:25000',
        ]);

        $user = auth()->user();
        $fileType = $request->input('file_type');
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Determine the folder and attribute based on file type
        switch ($fileType) {
            case 'immigration_or_studentvisa':
                $filePath = 'public/immigrations';
                $user->immigration_or_studentvisa = $fileName;
                break;
            case 'routing_form_one':
                $filePath = 'public/routing_forms';
                $user->routing_form_one = $fileName;
                break;
            case 'manuscript':
                $filePath = 'public/manuscripts';
                $user->manuscript = $fileName;
                break;
            case 'adviser_appointment_form':
                $filePath = 'public/adviser_appointments';
                $user->adviser_appointment_form = $fileName;
                break;
            default:
                return redirect()->back()->with('error', 'Invalid file type.');
        }

        // Store the file and update the user record
        $file->storeAs($filePath, $fileName);
        $user->save();

        return redirect()->route('gssstudent.partialdashboard')->with('success', 'File uploaded successfully!');
    }
}
