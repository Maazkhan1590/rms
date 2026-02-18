<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit research work.');
        }

        return view('submissions.index');
    }
}
