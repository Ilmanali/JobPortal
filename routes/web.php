<?php
use App\Http\Controllers\admin\ApplicationsController;
use App\Http\Middleware\CheckAdmin;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\JobsController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Middleware\ValidUser;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
Route::get('/jobs/detail/{id}', [JobController::class, 'detail'])->name('jobDetail');
Route::post('/apply-job', [JobController::class, 'applyJob'])->name('applyJob');
Route::post('/save-jobs', [JobController::class, 'saveJob'])->name('saveJobs');


Route::get('/forgot-password', [AccountController::class, 'forgotPassword'])->name('account.forgotPassword');
Route::post('/process/forgot-password', [AccountController::class, 'processForgotPassword'])->name('account.processForgotPassword');
Route::get('/reset-password/{token}', [AccountController::class, 'resetPassword'])->name('account.resetPassword');
Route::post('/process-reset-password', [AccountController::class, 'processResetPassword'])->name('account.processResetPassword');

Route::middleware([CheckAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/delete-user/{UserId}', [UserController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('/jobs', [JobsController::class, 'index'])->name('admin.jobs');
    Route::get('/jobs/edit/{id}', [JobsController::class, 'edit'])->name('admin.jobs.edit');
    Route::put('/jobs/{id}', [JobsController::class, 'update'])->name('admin.jobs.update');
    Route::delete('/delete-job/{id}', [JobsController::class, 'destroy'])->name('admin.deleteJob');
    Route::get('/jobs-application', [ApplicationsController::class, 'index'])->name('admin.jobApplication');
    Route::delete('/delete-job-application/{id}', [ApplicationsController::class, 'destroy'])->name('admin.deleteJobApplication');


});
Route::get('/account/registr', [AccountController::class, 'registration'])->name('account.registration');
Route::post('/account/process/registr', [AccountController::class, 'processRegistration'])->name('account.processRegistration');
Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
Route::post('/account/login', [AccountController::class, 'authenticate'])->name('account.authenticate');

Route::get('/account/logout', [AccountController::class, 'logout'])->name('account.logout');
// Route::group(['middleware' => ['IsValidUser']], function () {
Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
Route::Put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
Route::post('/update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');
Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
Route::get('/my-jobs', [AccountController::class, 'myJobs'])->name('account.myJobs');
Route::get('/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
Route::PUT('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
Route::delete('/delete-job/{jobId}', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
Route::get('/my-job-application', [AccountController::class, 'myJobApplications'])->name('account.myJobApplication');
Route::get('/saved-jobs', [AccountController::class, 'savedJob'])->name('account.savedJob');
Route::put('/account/update-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');


// });