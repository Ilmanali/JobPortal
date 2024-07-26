<?php

namespace App\Http\Controllers;

use App\Mail\jobNotificationEmail;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Mail;

class JobController extends Controller
{
    //This method will show jobs page
    public function index(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $jabTypes = JobType::where('status', 1)->get();

        $jobs = Job::where('status', 1);

        // Search using keywords
        if (!empty($request->keyword)) {
            $jobs = $jobs->where(function ($query) use ($request) {
                $query->orWhere('title', 'like', '%' . $request->keyword . '%');
            });
        }
        // Filter by location
        if (!empty($request->location)) {
            $jobs = $jobs->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by category
        if (!empty($request->category)) {
            $jobs = $jobs->where('category_id', $request->category);
        }

        // Filter by job types
        if (!empty($request->job_type)) {
            $jobs = $jobs->whereIn('job_type_id', $request->job_type);
        }
        // Filter by experience
        if (!empty($request->experience)) {
            $jobs = $jobs->where('experience', '>=', $request->experience);
        }

        $jobs = $jobs->with(['jobType', 'category']);
        if ($request->sort == '0') {
            $jobs = $jobs->orderBy('created_at', 'ASC');
        } else {
            $jobs = $jobs->orderBy('created_at', 'DESC');
        }

        $jobs = $jobs->paginate(9);

        return view("front.jobs", [
            'categories' => $categories,
            'jabTypes' => $jabTypes,
            'jobs' => $jobs
        ]);
    }
    public function detail($id)
    {
        $job = Job::where([
            'id' => $id,
            'status' => 1
        ])->with(['jobType', 'category'])->first();
        if ($job == null) {
            abort(404);
        }
        $count = 0;
        if (Auth::user()) {
            $count = SavedJob::where([
                'user_id' => Auth::user()->id,
                'job_id' => $id
            ])->count();
        }
        // fatch applications
        $applications = JobApplication::where('job_id', $id)->with('user')->get();
        // dd($applications);
        return view('front.jobDetail', [
            'job' => $job,
            'count' => $count,
            'applications' => $applications
        ]);
    }
    public function applyJob(Request $request)
    {
        $id = $request->id;
        $job = Job::where('id', $id)->first();

        if ($job == null) {
            $message = 'Job does not exist';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $employer_id = $job->user_id;
        if ($employer_id == Auth::user()->id) {
            $message = 'You cannot apply to your own job';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($jobApplicationCount > 0) {
            $message = 'You have already applied to this job';
            session()->flash('error', $message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();

        $employer = User::where('id', $employer_id)->first();

        if (is_null($employer) || is_null(Auth::user()) || is_null($job)) {
            return response()->json([
                'status' => false,
                'message' => 'Mail data contains null values'
            ]);
        }

        $mailData = [
            'employer' => $employer,
            'user' => Auth::user(),
            'job' => $job
        ];

        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));

        $message = 'You have successfully applied';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
    public function saveJob(Request $request)
    {
        $id = $request->id;
        $job = Job::find($id);

        if ($job === null) {
            session()->flash('error', 'Job not found');
            return response()->json([
                'status' => false,
                'message' => 'Job not found'
            ]);
        }

        // Check if user already saved the Job
        $count = SavedJob::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($count > 0) {
            session()->flash('error', 'You already saved this job');
            return response()->json([
                'status' => false,
                'message' => 'You already saved this job'
            ]);
        }

        // Save the job
        $savedJob = new SavedJob;
        $savedJob->job_id = $id;
        $savedJob->user_id = Auth::user()->id;
        $savedJob->save();

        session()->flash('success', 'You have successfully saved the job');
        return response()->json([
            'status' => true,
            'message' => 'You have successfully saved the job'
        ]);
    }


}
