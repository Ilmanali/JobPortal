<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.users.list', [
            'users' => $users
        ]);
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', [
            'user' => $user
        ]);
    }
    public function update(Request $request, $id)
    {
        // $id = Auth::id(); // Auth::user()->id ko Auth::id() se replace kar diya
        $validator = validator::make($request->all(), [
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

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }
    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'user deleted successfully.');
    }
}
