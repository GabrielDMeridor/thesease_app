<?php

namespace App\Http\Controllers\GSStudent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GSSFileUploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        $request->validate([
            'immigration_or_studentvisa' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'routing_form_one' => 'nullable|mimes:pdf|max:25000',
            'manuscript' => 'nullable|mimes:pdf|max:25000',
            'adviser_appointment_form' => 'nullable|mimes:pdf|max:25000',
        ]);

        $user = auth()->user();

        // Handle immigration file upload
        if ($request->hasFile('immigration_or_studentvisa')) {
            $file = $request->file('immigration_or_studentvisa');
            $immigrationFileName = $file->getClientOriginalName();
            $file->storeAs('public/immigrations', $immigrationFileName);
            $user->immigration_or_studentvisa = $immigrationFileName;
        }

        // Handle routing form upload
        if ($request->hasFile('routing_form_one')) {
            $file = $request->file('routing_form_one');
            $routingFileName = $file->getClientOriginalName();
            $file->storeAs('public/routing_forms', $routingFileName);
            $user->routing_form_one = $routingFileName;
        }

        // Handle manuscript upload
        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $manuscriptFileName = $file->getClientOriginalName();
            $file->storeAs('public/manuscripts', $manuscriptFileName);
            $user->manuscript = $manuscriptFileName;
        }

        // Handle adviser appointment form upload
        if ($request->hasFile('adviser_appointment_form')) {
            $file = $request->file('adviser_appointment_form');
            $adviserAppointmentFileName = $file->getClientOriginalName();
            $file->storeAs('public/adviser_appointments', $adviserAppointmentFileName);
            $user->adviser_appointment_form = $adviserAppointmentFileName;
        }

        $user->save();

        return redirect()->route('gssstudent.partialdashboard')->with('success', 'Files uploaded successfully!');
    }
}
