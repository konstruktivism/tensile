<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects linked to the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
// Fetch the projects linked to the authenticated user's organisation
        $projects = Auth::user()->organisation->projects;

// Return the view with the projects
        return view('projects', compact('projects'));

    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function read(Project $project)
    {

        // Ensure the authenticated user belongs to the same organisation as the project
        if (Auth::user()->organisation_id !== $project->organisation_id || Auth::user()->organisation_id === null) {
            abort(403, 'Unauthorized action.');
        }

        // Return the view with the project
        return view('project', compact('project'));
    }

}
