<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    // Controller constructor to apply middleware for permission-based access control.
    public function __construct()
    {
        $this->middleware('permission:view-roles')->only('index');
        $this->middleware('permission:create-roles')->only('create');
        $this->middleware('permission:edit-roles')->only('edit');
        $this->middleware('permission:delete-roles')->only('destroy');
    }

    // listing Role datastore
    public function index()
    {
        $pagename = 'Role';
        $breadcrumb = 'Role List';
        $role = Role::with('permissions')->get();

        // Load permissions grouped for the view (same grouping used in create/edit)
        $permission = Permission::orderBy('name', 'asc')->get()->groupBy(function ($permission) {
            $lastPart = explode('-', $permission->name);
            return ucfirst(end($lastPart));
        });

        return view('backend.role.index', compact('pagename', 'breadcrumb', 'role', 'permission'));
    }

    // view Role form
    public function create()
    {
        $pagename = 'Role';
        $breadcrumb = 'Role Create';

        $permission = Permission::orderBy('name', 'asc')->get()->groupBy(function ($permission) {
            $lastPart = explode('-', $permission->name);
            return ucfirst(end($lastPart));
        });
        return view('backend.role.index', compact('pagename', 'breadcrumb', 'permission'));
    }

    // store data in data base
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50|unique:roles,name|min:3',
            ]
        );

        if ($validator->fails()) {
            // Redirect back with input and errors
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // dd($request->permissiones);     // give variable name definr in blade file
        $role =  Role::create(['name' => $request->name]);

        if (!empty($request->permissiones)) {
            foreach ($request->permissiones as $rolename) {
                $role->givePermissionTo($rolename);
            }
        }

        return redirect()->route('role')->with('success', 'Role added successfully');
    }


    // view edit form
    public function edit($id)
    {
        $pagename = 'Role';
        $breadcrumb = 'Role Edit';
        $role = Role::with('permissions')->findOrFail($id);

        $hasepermission = $role->permissions->pluck('name');

        $permission = Permission::orderBy('name', 'asc')->get()->groupBy(function ($permission) {
            $lastPart = explode('-', $permission->name);
            return ucfirst(end($lastPart));
        });

        // Use index view with inline form for edit mode (same design as User module)
        $editRole = $role;
        $roles = Role::with('permissions')->get();
        return view('backend.role.index', compact('pagename', 'breadcrumb', 'editRole', 'permission', 'hasepermission'))->with('role', $roles);
    }

    // store update data in database
    public function update(Request $request, $id)
    {

        $role = Role::with('permissions')->findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50|unique:roles,name,' . $id . ',id|min:3',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role->name = $request->name;
        $role->save();

        if (!empty($request->permissiones)) {
            $role->syncPermissions($request->permissiones);
        } else {
            $role->syncPermissions([]);   //syncPermissions() is a method provided by the Spatie Laravel Permission package.
        }

        return redirect()->route('role')->with('success', 'Role update Successfully');
    }

    // use for delete
    public function destroy($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        // delete user from database
        if ($role->delete()) {
            return redirect()->route('role')->with('success', 'Permission deleted Successfully');
        }
    }
}
