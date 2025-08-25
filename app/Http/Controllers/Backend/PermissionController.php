<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $query = Permission::query();
        if ($q = request('q')) {
            $query->where('name', 'like', "%{$q}%");
        }
        $perPage = (int) request('per_page', 10);
        if ($perPage < 1) $perPage = 10;
        if ($perPage > 100) $perPage = 100;
        $permission = $query->paginate($perPage)->withQueryString();
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
        $validator = Validator::make(
            $request->all(),
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

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $permission = new Permission();
        $permission->name = $request->name;

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
        $query = Permission::query();
        if ($q = request('q')) {
            $query->where('name', 'like', "%{$q}%");
        }
        $perPage = (int) request('per_page', 10);
        if ($perPage < 1) $perPage = 10;
        if ($perPage > 100) $perPage = 100;
        $permissions = $query->paginate($perPage)->withQueryString();
        return view('backend.permission.index', compact('pagename', 'breadcrumb', 'editPermission'))->with('permission', $permissions);
    }

    // store update data in database
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            ],
            [
                'name.required' => 'Permission field is required.',
                'name.string'   => 'Permission must be a valid string.',
                'name.max'      => 'Permission may not be greater than 255 characters.',
                'name.unique'   => 'Permission is already taken.',
            ]
        );

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find and update the permission
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        // $permission->updated_by = Auth::id();

        if ($permission->save()) {
            return redirect()->route('permission')->with('success', 'Permission updated successfully.');
        } else {
            return redirect()->route('permission')->with('error', 'Something went wrong while saving data.');
        }
    }


    // use for delete
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // delete user from database
        if ($permission->delete()) {
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Failed to delete permission.'], 500);
    }
}
