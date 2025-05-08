<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $item = 'Role Management';
    protected $itemActive = 'Roles';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Roles List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Roles Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Roles Update', ['only' => ['edit', 'update', 'givePermissionTo']]);
        $this->middleware('permission:Roles Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Roles View', ['only' => ['show', 'roleHasPermission']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log activity
        addActivity('role', 'view', 'User viewed roles list', null);

        $roles = Role::all();
        return view('backend.roles.index', compact('roles'))
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        addActivity('role', 'view_create_form', 'User viewed create role form', null);

        return view('backend.roles.create')
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'required',
        ]);

        try {
            // Create the role
            $role = Role::create($request->all());

            // Log activity
            addActivity('role', 'create', 'User created role: ' . $role->name, $role->id);

            return response()->json(['status' => 'success', 'message' => 'Role created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create role'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            // Log activity
            addActivity('role', 'edit', 'User viewed edit form for role: ' . $role->name, $role->id);

            return response()->json([
                'status' => 'success',
                'data' => $role
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the role'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'data.name' => 'required|unique:roles,name,' . $id,
            'data.guard_name' => 'required|in:web,api',
        ], [
            'data.name.required' => 'Silakan masukkan nama peran.',
            'data.name.unique' => 'Nama peran ini sudah ada. Silakan pilih nama yang berbeda.',
            'data.guard_name.required' => 'Silakan masukkan nama guard.',
            'data.guard_name.in' => 'Nama guard harus web atau api.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ]);
        }

        try {
            $decodedData = json_decode(base64_decode($request->hash), true);

            if ($decodedData && isset($decodedData['name']) && isset($decodedData['guard_name'])) {
                $oldName = $role->name;
                $role->update($decodedData);
                // Log activity
                addActivity('role', 'update', 'User updated role from "' . $oldName . '" to "' . $role->name . '"', $role->id);
                return response()->json(['status' => 'success', 'message' => 'Role updated successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid data format'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->name;
            $roleId = $role->id;

            $role->delete();

            // Log activity
            addActivity('role', 'delete', 'User deleted role: ' . $roleName, $roleId);

            return response()->json([
                'status' => 'success',
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function data(Request $request)
    {
        $query = Role::query();

        // Get total count before filtering
        $totalRecords = $query->count();

        // Apply search filter if present
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('guard_name', 'like', "%{$searchValue}%");
            });
        }

        // Get filtered count
        $filteredRecords = $query->count();

        // Apply pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $query->offset($start)->limit($length);

        // Get paginated data
        $roles = $query->get();

        $data = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'created_at' => $role->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $role->updated_at->format('Y-m-d H:i:s'),
                'actions' => '
                    <button onclick="editRole(' . $role->id . ')" class="btn btn-sm btn-primary">Edit</button>
                    <button onclick="deleteRole(' . $role->id . ')" class="btn btn-sm btn-danger">Delete</button>
                '
            ];
        });

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function givePermissionTo(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
        $role->syncPermissions($permissions);

        // Log activity
        addActivity('role', 'assign_permissions', 'User assigned ' . count($permissions) . ' permissions to role: ' . $role->name, $role->id);

        return response()->json(['status' => 'success', 'message' => 'Permissions updated successfully']);
    }

    public function roleHasPermission($roleId)
    {
        $role = Role::findOrFail($roleId);

        // Log activity
        addActivity('role', 'view_permissions', 'User viewed permissions for role: ' . $role->name, $role->id);

        return response()->json([
            'permissions' => Permission::all(),
            'rolePermissions' => $role->permissions->pluck('id')->toArray(),
            'roleName' => $role->name
        ]);
    }
}
