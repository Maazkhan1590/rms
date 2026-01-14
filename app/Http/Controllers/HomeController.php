<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application home page with publications.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Publication::with(['submitter', 'primaryAuthor'])
            ->where('status', 'approved');

        // Handle search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('journal_name', 'like', "%{$search}%")
                  ->orWhere('conference_name', 'like', "%{$search}%");
            });
        }

        // Handle filter by type
        if ($request->has('type') && $request->type) {
            $query->where('publication_type', $request->type);
        }

        // Handle filter by year
        if ($request->has('year') && $request->year) {
            $query->where('publication_year', $request->year);
        }

        $publications = $query->latest('published_at')->paginate(12);

        // Get filter options
        $publicationTypes = Publication::where('status', 'approved')
            ->distinct()
            ->pluck('publication_type')
            ->filter()
            ->sort()
            ->values();

        $publicationYears = Publication::where('status', 'approved')
            ->distinct()
            ->pluck('publication_year')
            ->filter()
            ->sortDesc()
            ->values();

        return view('home', compact('publications', 'publicationTypes', 'publicationYears'));
    }
}
