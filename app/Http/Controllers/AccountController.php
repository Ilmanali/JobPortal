<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobType;
use App\Models\SavedJob;
use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Log;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //This method will show user registration page
    public function registration()
    {
        return view('front.account.registration');
    }
    public function processRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed',
        ]);
        $user = User::create($data);
        if ($user) {
            return redirect()->route('account.login')->with('success', 'Registration successful!');
        }

    }
    //This method will show user login page
    public function login()
    {
        return view('front.account.login');
    }
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('account.profile');
        } else {
            return redirect()->route('account.login')->with('error', 'Incorrect email or password.');
        }
    }
    //data send profile page
    public function profile()
    {
        $id = Auth::user()->id;

        $user = User::find($id);
        // dd($user);
        return view('front.account.profile', [
            'user' => $user
        ]);
    }
    //profile update
    public function updateProfile(Request $request)
    {
        $id = Auth::id(); // Auth::user()->id ko Auth::id() se replace kar diya
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'designation' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:15', // Mobile validation bhi add kar diya
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->designation = $request->designation;
        $user->mobile = $request->mobile;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    //logout redirect login
    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }
    //profile image update and save
    public function updateProfilePic(Request $request)
    {
        $id = Auth::id();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve the user's current image
        $user = User::findOrFail($id);
        $oldImage = $user->image;

        // Handle the new image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ext = $image->getClientOriginalExtension();
            $imageName = $id . '_' . time() . '.' . $ext;
            $image->move(public_path('/profile_pic'), $imageName);

            // Update user's image field in the database
            $user->image = $imageName;
            $user->save();

            // Delete old image if it exists
            if ($oldImage) {
                $oldImagePath = public_path('/profile_pic/') . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Redirect back with success message
            return redirect()->back()->with('success', 'Profile updated Picture.');
        } else {
            // If no image file is found, redirect back with error message
            return redirect()->back()->with('error', 'No image file found.')->withInput();
        }
    }

    public function createJob()
    {
        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobTypes = JobType::orderBy('name', 'ASC')->where('status', 1)->get();

        return view('front.account.job.create', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
        ]);
    }
    public function saveJob(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'experience' => 'required',
            'company_name' => 'required|min:3|max:70',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save job to the database
        $job = new Job();
        $job->title = $request->title;
        $job->category_id = $request->category;
        $job->job_type_id = $request->jobType;
        $job->user_id = Auth::user()->id;
        $job->vacancy = $request->vacancy;
        $job->salary = $request->salary;
        $job->location = $request->location;
        $job->description = $request->description;
        $job->benefits = $request->benefits;
        $job->responsibility = $request->responsibility;
        $job->qualifications = $request->qualifications;
        $job->keywords = $request->keywords;
        $job->experience = $request->experience;
        $job->company_name = $request->company_name;
        $job->company_location = $request->company_location;
        $job->company_website = $request->company_website;
        // dd($job);
        $job->save();

        // Redirect or return response
        return redirect()->route('account.myJobs')->with('success', 'Job has been successfully saved.');
    }
    public function myJobs()
    {
        $jobs = Job::where('user_id', Auth::user()->id)
            ->with('jobType')->orderBy('created_at', 'DESC')
            ->paginate(3);
        return view('front.account.job.my-jobs', [
            'jobs' => $jobs
        ]);
    }

    public function editJob(Request $request, $id)
    {
        $categories = Category::orderBy('name', 'ASC')->where('status', 1)->get();
        $jobTypes = JobType::orderBy('name', 'ASC')->where('status', 1)->get();
        $job = Job::where([
            'user_id' => Auth::user()->id,
            'id' => $id
        ])->first();
        if ($job == null) {
            abort(404);
        }
        return view('front.account.job.edit', [
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'job' => $job
        ]);
    }
    public function updateJob(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'experience' => 'required',
            'company_name' => 'required|min:3|max:70',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Save job to the database
        $job = Job::find($id);
        $job->title = $request->title;
        $job->category_id = $request->category;
        $job->job_type_id = $request->jobType;
        $job->user_id = Auth::user()->id;
        $job->vacancy = $request->vacancy;
        $job->salary = $request->salary;
        $job->location = $request->location;
        $job->description = $request->description;
        $job->benefits = $request->benefits;
        $job->responsibility = $request->responsibility;
        $job->qualifications = $request->qualifications;
        $job->keywords = $request->keywords;
        $job->experience = $request->experience;
        $job->company_name = $request->company_name;
        $job->company_location = $request->company_location;
        $job->company_website = $request->company_website;
        $job->save();

        // Redirect or return response
        return redirect()->route('account.myJobs')->with('success', 'Job has been successfully update.');
    }
    public function deleteJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $job->delete();
        return redirect()->route('account.myJobs')->with('success', 'Job deleted successfully');
    }

    public function myJobApplications()
    {
        $jobApplications = JobApplication::where('user_id', Auth::user()
            ->id)->with('job', 'job.jobType')
            ->orderBy('created_at', 'Desc')
            ->paginate(10);
        // dd($job);
        return view("front.account.job.my-job-application", [
            'jobApplications' => $jobApplications
        ]);
    }
    public function savedJob()
    {
        $savedJobs = SavedJob::where('user_id', Auth::user()->id)
            ->with('job', 'job.jobType')
            ->orderBy('created_at', 'Desc')
            ->paginate(10);

        return view("front.account.job.saved-jobs", [
            'savedJobs' => $savedJobs
        ]);
    }
    public function updatePassword(Request $request)
    {
        $authUser = auth()->user();
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        if (!\Hash::check($request->input('old_password'), $authUser->password)) {
            return redirect()->back()
                ->withErrors(['old_password' => 'The provided password does not match our records.'])
                ->withInput();
        }
        dd($request->input('new_password'));
        $authUser->password = bcrypt($request->input('new_password'));
        $authUser->save();
        return redirect()->back()->with('success', 'Password changed successfully.');
    }




    public function forgotPassword()
    {
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = Str::random(60);
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        \DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        //Send Email Here
        $user = User::where('email', $request->email)->first();

        $mailData = [
            'token' => $token,
            'user' => $user,
            'subject' => 'You have requested to change your password'
        ];
        Mail::to($request->email)->send(new ResetPasswordEmail($mailData));
        return redirect()->route('account.forgotPassword')->with('success', 'Reset password email has been sent to your inbox');

    }
    public function resetPassword($tokenString)
    {
        $token = \DB::table('password_reset_tokens')->where('token', $tokenString)->first();
        if ($token == null) {
            return redirect()->route('account.forgotPassword')->with('error', 'Invalid token');
        }
        return view('front.account.reset-password', [
            'tokenString' => $tokenString
        ]);
    }
    public function processResetPassword(Request $request)
    {
        $token = \DB::table('password_reset_tokens')->where('token', $request->token)->first();
        if ($token == null) {
            return redirect()->route('account.forgotPassword')->with('error', 'Invalid token');
        }
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return redirect()->route('account.resetPassword', $request->token)->withErrors($validator);
        }
        User::where('email', $token->email)->update([
            'password' => Hash::make($request->new_password)
        ]);
        return redirect()->route('account.login')->with('success', 'You have have successfully changed your password');
    }

}
