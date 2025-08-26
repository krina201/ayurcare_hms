<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Validator;

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
    public function index(Request $request)
    {
        $pagename = 'User';
        $breadcrumb = 'User List';

        $user = User::all();
        $role = Role::all();

        return view('backend.user.index', compact('pagename', 'breadcrumb', 'user', 'role'));
    }

    // public function index()
    // {
    //     $pagename = 'User';
    //     $breadcrumb = 'User List';
    //     $query = User::query();

    //     // simple search across name and email using 'q' param
    //     if ($q = request('q')) {
    //         $query->where(function ($w) use ($q) {
    //             $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
    //         });
    //     }

    //     $perPage = (int) request('per_page', 10);
    //     if ($perPage < 1) $perPage = 10;
    //     if ($perPage > 100) $perPage = 100;

    //     $user = $query->paginate($perPage)->withQueryString();
    //     $role = Role::all();
    //     return view('backend.user.index', compact('pagename', 'breadcrumb', 'user', 'role'));
    // }


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
        $validator = Validator::make(
            $request->all(),
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

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

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
            LogHelper::logActivity('Insert', $user->id, 'user', $user->toArray());  // For Insert 

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

        $users = User::all();

        // The view expects a variable named $user for the collection, so attach it while keeping $editUser for the single record
        return view('backend.user.index', compact('pagename', 'breadcrumb', 'editUser', 'role', 'hasRoles'))->with('user', $users);
    }

    // store update data in database
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role_id' => 'required',
                'web_loging' => 'required',
                'is_active' => 'nullable|boolean',
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
                'web_loging.required' => 'Loging Devices field is required.',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role_id = $validatedData['role_id'];

        if (Auth::id() != $user->id) {
            // Use boolean casting for checkbox. If 'is_active' is not present, it will be false.
            $user->is_active = $request->boolean('is_active');
            $user->web_loging = $validatedData['web_loging'];
        }

        if ($user->save()) {
            // This replaces the old roles with the new role
            $user->roles()->sync($validatedData['role_id']);

            LogHelper::logActivity('Update', $user->id, 'user', $user->toArray());

            return redirect()->route('user')->with('success', 'User updated successfully.');
        } else {
            return redirect()->route('user')->with('error', "Something Wrong On Data Save");
        }
    }

    // use for delete
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->delete()) {
            LogHelper::logActivity('Delete', $user->id, 'user', $user->toArray());  // For delete 

            return redirect()->route('user')->with('success', 'User deleted successfully');
        }
    }
}
