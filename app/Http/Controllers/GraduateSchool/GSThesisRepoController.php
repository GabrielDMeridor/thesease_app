<?php

namespace App\Http\Controllers\GraduateSchool;

use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class GSThesisRepoController extends Controller
{
    // Display the upload form
    public function showThesisUploadForm()
    {
        $degreePrograms = [
            'Masteral' => ['MAEd', 'MA-Psych-CP', 'MBA', 'MS-CJ-Crim', 'MDS', 'MIT', 'MSPH', 'MPH', 'MS-MLS', 'MAN', 'MN'],
            'Doctorate' => ['PhD-CI-ELT', 'PhD-Ed-EM', 'PhD-Mgmt', 'DBA', 'DIT', 'DRPH-HPE']
        ];
    
        return view('graduateschool.thesis-repo.GSthesisrepo', compact('degreePrograms'));
    }
    
    
    // Handle the file upload
    public function uploadThesis(Request $request)
    {
        // Validation
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:10000', // PDF files only, max size 10MB
            'year_published' => 'required|integer|min:1900|max:' . date('Y'),
            'program' => 'required|string',
            'degree_type' => 'required|string'
        ]);

        // Store the file and get its path
        $filePath = $request->file('file')->store('theses', 'public');

        // Create the thesis record in the database
        Thesis::create([
            'title' => $request->title,
            'file_path' => $filePath,
            'year_published' => $request->year_published,
            'program' => $request->program,
            'degree_type' => $request->degree_type
        ]);

        return redirect()->back()->with('success', 'Thesis uploaded successfully!');
    }

    // Display the list of uploaded theses
    public function index(Request $request)
    {
        // Get filter inputs from the request
        $degreeType = $request->input('degree_type');
        $yearPublished = $request->input('year_published');

        // Start the query
        $query = Thesis::query();

        // Apply degree type filter if provided
        if ($degreeType) {
            $query->where('degree_type', $degreeType);
        }

        // Apply year published filter if provided
        if ($yearPublished) {
            $query->where('year_published', $yearPublished);
        }

        // Execute the query to get the filtered results
        $theses = $query->get();

        // Pass filter options to the view
        $years = Thesis::select('year_published')->distinct()->pluck('year_published')->sortDesc();
        return view('superadmin.thesis-repo.SAthesisrepo', compact('theses', 'degreeType', 'yearPublished', 'years'));
    }
}
