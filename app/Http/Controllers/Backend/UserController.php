<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    // Controller constructor to apply middleware for permission-based access control.
    // public function __construct()
    // {
    //     $this->middleware('permission:view-user')->only('index');
    //     $this->middleware('permission:create-user')->only('create');
    //     $this->middleware('permission:edit-user')->only('edit');
    //     $this->middleware('permission:delete-user')->only('destroy');
    // }

    // listing user datastore
    public function index()
    {
        $pagename = 'User';
        $breadcrumb = 'User List';
        $user = User::all();
        $role = Role::all();
        return view('backend.user.index', compact('pagename', 'breadcrumb', 'user', 'role'));
    }



    // view user form
    public function create()
    {
        $pagename = 'User';
        $breadcrumb = 'User Create';
        $role = Role::all();
        // $role = Role::where('id', '!=', 1)->get();
        return view('backend.user.index', compact('pagename', 'breadcrumb', 'role'));
    }

    // store data in data base
    public function store(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required',
                'web_loging' => 'required',

            ],
            [
                // Custom validation error messages
                'name.required' => 'User Name field is required.',
                'name.string' => 'User Name must be a valid string.',
                'name.max' => 'User Name may not be greater than 255 characters.',

                'email.required' => 'Email field is required.',
                'email.email' => 'Please provide a valid Email address.',
                'email.max' => 'Email may not be greater than 255 characters.',
                'email.unique' => 'Email address is already taken.',

                'password.required' => 'Password field is required.',
                'password.string' => 'Password must be a valid string.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',

                'role_id.required' => 'Role selection is required.',
                'web_loging.required' => 'Loging Devices field is required.',

            ]
        );

        // Create new user instance
        $user = new user();
        $user->name = $validatedData['name'];
        $user->role_id = $validatedData['role_id'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->is_active = $request->input('is_active');
        $user->web_loging = $validatedData['web_loging'];

        // Save the user
        if ($user->save()) {
            $user->roles()->sync([$validatedData['role_id']]); // This replaces the old roles with the new role
            return redirect()->route('user')->with('success', 'User added successfully');
        } else {
            return redirect()->route('user')->with('error', 'Something went wrong while saving the User');
        }
    }


    // view edit form
    public function edit($id)
    {
        $pagename = 'User';
        $breadcrumb = 'User Edit';
        $user = User::findOrFail($id);
        // $role = Role::where('id', '!=', 1)->get();
        $role = Role::all();
        $hasRoles = $user->roles->pluck('id');
        // dd($hasRoles);
        // Use the index view with inline form for edit mode
        $editUser = $user;
        // Also provide the users list so the table renders alongside the edit form
        $users = User::all();
        // The view expects a variable named $user for the collection, so attach it while keeping $editUser for the single record
        return view('backend.user.index', compact('pagename', 'breadcrumb', 'editUser', 'role', 'hasRoles'))->with('user', $users);
    }

    // store update data in database
    public function update(Request $request, $id)
    {

        // Validate the request data
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role_id' => 'required',
            ],
            [
                // Custom validation error messages
                'name.required' => 'User Name field is required.',
                'name.string' => 'User Name must be a valid string.',
                'name.max' => 'User Name may not be greater than 255 characters.',

                'email.required' => 'Email field is required.',
                'email.email' => 'Please provide a valid Email address.',
                'email.max' => 'Email may not be greater than 255 characters.',
                'email.unique' => 'Email address is already taken.',

                'role_id.required' => 'Role selection is required.',
            ]
        );

        $user = user::findOrFail($id);
        $user->name = $validatedData['name'];
        if ($validatedData['role_id'] != 1) {
            $user->role_id = $validatedData['role_id'];
        }
        $user->email = $validatedData['email'];

        if (Auth::id() != $user->id) {
            $user->is_active = $request->input('is_active');
            $user->web_loging = $request->input('web_loging');
        }

        $user->save();
        if ($user->save()) {

            $user->roles()->sync([$validatedData['role_id']]); // This replaces the old roles with the new role

            return redirect()->route('user')->with('success', 'User update Successfully');
        } else {
            return redirect()->route('user')->with('error', "Something Wrong On Data Save");
        }
    }

    // use for delete
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
