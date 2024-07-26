<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class ApplicationsController extends Controller
{
    public function index()
    {
        $applications = JobApplication::orderBy('created_at', 'DESC')->with('job', 'user', 'employer')
            ->paginate(10);
        return view('admin.job-appliction.list', [
            'applications' => $applications
        ]);
        // dd($applications);
    }
    public function destroy($id)
    {
        $job = JobApplication::findOrFail($id);
        $job->delete();
        return redirect()->back()->with('success', 'JobApplications deleted successfully.');
    }
}
