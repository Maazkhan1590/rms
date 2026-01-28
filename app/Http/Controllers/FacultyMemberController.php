<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Http\Request;

class FacultyMemberController extends Controller
{
    /**
     * Display a listing of faculty members with their publications
     */
    public function index(Request $request)
    {
        // Get all users who have submitted publications (Faculty role)
        $query = User::whereHas('roles', function($q) {
            $q->where('title', 'Faculty');
        })->where(function($q) {
            $q->whereHas('publications')
              ->orWhereHas('primaryAuthorPublications');
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

        $facultyMembers = $query->withCount([
            'publications' => function($q) {
                $q->where('status', 'approved');
            },
            'primaryAuthorPublications' => function($q) {
                $q->where('status', 'approved');
            }
        ])->orderBy('name')->paginate(20);

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

        // Get all publications by this user
        $publications = Publication::where(function($query) use ($user) {
            $query->where('submitted_by', $user->id)
                  ->orWhere('primary_author_id', $user->id);
        })
        ->with(['submitter', 'primaryAuthor', 'evidenceFiles'])
        ->orderBy('publication_year', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('faculty-members.show', compact('user', 'publications'));
    }
}
