<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\RtnSubmission;
use App\Models\BonusRecognition;
use Illuminate\Http\Request;

class FacultyMemberController extends Controller
{
    /**
     * Display a listing of faculty members with their publications
     */
    public function index(Request $request)
    {
        // Get all faculty users (not just those with publications)
        $query = User::whereHas('roles', function($q) {
            $q->where('title', 'Faculty');
        });

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by college
        if ($request->has('college_id') && $request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        $facultyMembers = $query->with(['college', 'department'])
            ->withCount([
                'grants',  // Count all grants, not just approved
                'rtnSubmissions',  // Count all RTN submissions, not just approved
                'bonusRecognitions'  // Count all recognitions, not just approved
            ])
            ->selectRaw('users.*, (
                SELECT COUNT(DISTINCT id) 
                FROM publications 
                WHERE (submitted_by = users.id OR primary_author_id = users.id)
            ) as unique_publications_count')
            ->orderBy('name')
            ->paginate(20);

        return view('faculty-members.index', compact('facultyMembers'));
    }

    /**
     * Display a specific faculty member's profile and all their papers
     */
    public function show($id)
    {
        $user = User::with(['roles', 'college', 'department'])
            ->whereHas('roles', function($q) {
                $q->where('title', 'Faculty');
            })
            ->findOrFail($id);

        // Publications for this faculty member
        $publications = Publication::where(function($query) use ($user) {
                $query->where('submitted_by', $user->id)
                      ->orWhere('primary_author_id', $user->id);
            })
            ->with(['submitter', 'primaryAuthor', 'evidenceFiles'])
            ->orderBy('publication_year', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Grants submitted by this faculty member
        $grants = Grant::where('submitted_by', $user->id)
            ->with(['submitter', 'evidenceFiles'])
            ->orderByDesc('award_year')
            ->orderByDesc('created_at')
            ->get();

        // RTN submissions by this faculty member
        $rtnSubmissions = RtnSubmission::where('user_id', $user->id)
            ->with(['user', 'evidenceFiles'])
            ->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        // Bonus recognitions for this faculty member
        $bonusRecognitions = BonusRecognition::where('user_id', $user->id)
            ->with(['user', 'evidenceFiles'])
            ->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        return view('faculty-members.show', compact(
            'user',
            'publications',
            'grants',
            'rtnSubmissions',
            'bonusRecognitions'
        ));
    }

    /**
     * Download faculty member's CV as PDF
     */
    public function downloadCV($id)
    {
        $user = User::with(['roles', 'college', 'department'])
            ->whereHas('roles', function($q) {
                $q->where('title', 'Faculty');
            })
            ->findOrFail($id);

        // Get all publications for this faculty member
        $publications = Publication::where(function($query) use ($user) {
                $query->where('submitted_by', $user->id)
                      ->orWhere('primary_author_id', $user->id);
            })
            ->with(['submitter', 'primaryAuthor', 'evidenceFiles'])
            ->orderBy('publication_year', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all grants
        $grants = Grant::where('submitted_by', $user->id)
            ->with(['submitter', 'evidenceFiles'])
            ->orderByDesc('award_year')
            ->orderByDesc('created_at')
            ->get();

        // Get all RTN submissions
        $rtnSubmissions = RtnSubmission::where('user_id', $user->id)
            ->with(['user', 'evidenceFiles'])
            ->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        // Get all bonus recognitions
        $bonusRecognitions = BonusRecognition::where('user_id', $user->id)
            ->with(['user', 'evidenceFiles'])
            ->orderByDesc('year')
            ->orderByDesc('created_at')
            ->get();

        $pdf = \PDF::loadView('faculty-members.cv', compact(
            'user',
            'publications',
            'grants',
            'rtnSubmissions',
            'bonusRecognitions'
        ));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename
        $filename = str_replace(' ', '_', $user->name) . '_CV_' . date('Y-m-d') . '.pdf';

        // Download the PDF
        return $pdf->download($filename);
    }
}
