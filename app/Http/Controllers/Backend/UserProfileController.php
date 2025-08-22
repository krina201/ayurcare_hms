<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserProfileController extends Controller
{
    /**
     * Display the profile edit form.
     */
    public function editpassword()
    {
        $pagename = 'Change Password';
        $breadcrumb = 'Edit Password';
        $user = Auth::user();

        return view('backend.user_profile.changepassword', compact('pagename', 'breadcrumb', 'user'));
    }
    public function editpasswordadmin($id)
    {
        $pagename = 'Change Password';
        $breadcrumb = 'Edit Password';
        $user = Auth::user();

        return view('backend.user_profile.changepasswordadmin', compact('pagename', 'breadcrumb', 'user', 'id'));
    }

    public function saveChangePassword(Request $request, $id)
    {

        $validatedData = $request->validate([
            'current_password' => 'required|string|current_password',   //This is a special Laravel validation rule that verifies if the given password matches the currently authenticated user's password.
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Current Password field is required.',
            'current_password.string' => 'Current Password must be a valid string.',
            'current_password.current_password' => 'Current Password does not match with Old Passwords',

            'password.required' => 'Password field is required.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = user::findOrFail($id);
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        if ($user->wasChanged('password')) {
            Log::info("User ID: {$user->id} changed their password at " . now());

            return redirect()->route('edit.password')->with('success', 'Password updated successfully.');
        } else {
            return redirect()->route('edit.password')->with('error', 'Failed to update Password.');
        }
    }


    public function saveChangePasswordAdmin(Request $request, $id)
    {

        $validatedData = $request->validate([
            //This is a special Laravel validation rule that verifies if the given password matches the currently authenticated user's password.
            'password' => 'required|string|min:8|confirmed',
        ], [


            'password.required' => 'Password field is required.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = user::findOrFail($id);
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        if ($user->wasChanged('password')) {
            Log::info("User ID: {$user->id} changed their password at " . now());

            return redirect()->back()->with('success', 'Password updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update Password.');
        }
    }

    // view edit form
    public function edit($id)
    {
        $pagename = 'User';
        $breadcrumb = 'User Edit';
        $user = User::findOrFail($id);
        $role = Role::where('id', '!=', 1)->get();
        // $role = Role::all();

        $hasRoles = $user->roles->pluck('id');
        // dd($hasRoles);
        return view('backend.user_profile.edit', compact('pagename', 'breadcrumb', 'user', 'role', 'hasRoles'));
    }

    // store update data in database
    public function update(Request $request, $id)
    {

        // Validate the request data
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id . 'id',
                'phone' => 'required|numeric|digits:10',
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'address' => 'required|string|max:255',

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

                'phone.required' => 'Please enter your Phone Number.',
                'phone.numeric' => 'Phone Number should be numeric.',
                'phone.digits' => 'Phone Number should be 10 digits.',

                'img.image' => 'File must be an Image.',
                'img.mimes' => 'Image must be of type: jpeg, png, jpg, gif, svg.',
                'img.max' => 'Image size must not exceed 2MB (2048 KB).',

                'address.required' => 'Address field is required.',
                'address.string' => 'Address must be a valid string.',
                'address.max' => 'Address may not be greater than 255 characters.',
            ]
        );

        $user = user::findOrFail($id);
        $user->name = $validatedData['name'];
        $user->role_id = $validatedData['role_id'];
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'];
        $user->address = $validatedData['address'];


        // Handle image upload
        if ($request->hasFile('img')) {
            // Delete the previous image if it exists
            File::delete(public_path('backend-assets/media/uploads/users/' . $user->img));

            // Upload the new image
            $img = $request->file('img');
            $extension = $img->getClientOriginalExtension();
            $imgName = rand() . '.' . $extension;
            $img->move('backend-assets/media/uploads/users', $imgName);

            // Assign the new image name 
            $user->img = $imgName;
        }

        $user->is_active = $request->input('is_active');
        $user->updated_by = Auth::id();

        $user->save();
        if ($user->save()) {

            $user->roles()->sync([$validatedData['role_id']]); // This replaces the old roles with the new role

            return redirect()->route('user-profile.edit')->with('success', 'User Profile update Successfully');
        } else {
            return redirect()->route('user-profile.edit')->with('error', "Something Wrong On Data Save");
        }
    }
}
