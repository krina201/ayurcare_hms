<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    // Controller constructor to apply middleware for permission-based access control.
    public function __construct()
    {
        $this->middleware('permission:view-permission')->only('index');
        $this->middleware('permission:create-permission')->only('create');
        $this->middleware('permission:edit-permission')->only('edit');
        $this->middleware('permission:delete-permission')->only('destroy');
    }


    // listing permission datastore
    public function index()
    {
        $pagename = 'Permission';
        $breadcrumb = 'Permission List';
        $permission = Permission::all();
        return view('backend.permission.index', compact('pagename', 'breadcrumb', 'permission'));
    }

    // view permission form
    public function create()
    {
        $pagename = 'Permission';
        $breadcrumb = 'Permission Create';
        return view('backend.permission.index', compact('pagename', 'breadcrumb'));
    }

    // store data in data base
    public function store(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255|unique:permissions,name',
            ],
            [
                'name.required' => 'Permission field is required.',
                'name.string' => 'Permission must be a valid string.',
                'name.max' => 'Permission may not be greater than 255 characters.',
                'name.max' => 'Permission is already taken.',
            ]
        );

        $permission = new Permission();
        $permission->name = $validatedData['name'];
        // $permission->created_by = Auth::id();
        // $permission->updated_by = Auth::id();

        // Save the permission
        if ($permission->save()) {
            return redirect()->route('permission')->with('success', 'Permission added successfully');
        } else {
            return redirect()->route('permission')->with('error', 'Something went wrong while saving the Permission');
        }
    }


    // view edit form
    public function edit($id)
    {
        $pagename = 'Permission';
        $breadcrumb = 'Permission Edit';
        $permission = Permission::findOrFail($id);

        // Use index view with inline form for edit mode (same design as User module)
        $editPermission = $permission;
        $permissions = Permission::all();
        return view('backend.permission.index', compact('pagename', 'breadcrumb', 'editPermission'))->with('permission', $permissions);
    }

    // store update data in database
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255|unique:permissions,name,' . $id . ',id',
            ],
            [
                'name.required' => 'Permission field is required.',
                'name.string' => 'Permission must be a valid string.',
                'name.max' => 'Permission may not be greater than 255 characters.',
                'name.unique' => 'Permission is already taken.',
            ]
        );
        $permission = Permission::findOrFail($id);
        $permission->name = $validatedData['name'];
        // $permission->updated_by = Auth::id();
        if ($permission->save()) {
            return redirect()->route('permission')->with('success', 'Permission update Successfully');
        } else {
            return redirect()->route('permission')->with('error', "Something Wrong On Data Save");
        }
    }

    // use for delete
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // delete user from database
        if ($permission->delete()) {
            return redirect()->route('permission')->with('success', 'Permission deleted Successfully');
        }
    }
}
