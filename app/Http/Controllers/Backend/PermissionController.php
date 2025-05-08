<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $item = 'Permission Management';
    protected $itemActive = 'Permissions';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Permissions List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Permissions Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Permissions Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Permissions Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Permissions View', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log activity
        addActivity('permission', 'view', 'User viewed permissions list', null);

        $permissions = Permission::all();
        return view('backend.permissions.index', compact('permissions'))
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        addActivity('permission', 'view_create_form', 'User viewed create permission form', null);

        return view('backend.permissions.create')
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'guard_name' => 'required',
        ]);

        try {
            Permission::create($request->all());

            // Log activity
            addActivity('permission', 'create', 'User created permission: ' . $request->name, null);

            return response()->json(['status' => 'success', 'message' => 'Permission created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create permission'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Log activity
        addActivity('permission', 'view', 'User viewed permission: ' . $id, null);

        try {
            $permission = Permission::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $permission
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the permission'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Log activity
        addActivity('permission', 'edit', 'User viewed edit form for permission: ' . $id, null);

        try {
            $permission = Permission::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $permission
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the permission'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'data.name' => 'required|unique:permissions,name,' . $id,
            'data.guard_name' => 'required|in:web,api',
        ], [
            'data.name.required' => 'Silakan masukkan nama izin.',
            'data.name.unique' => 'Nama izin ini sudah ada. Silakan pilih nama yang berbeda.',
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
                $oldName = $permission->name;
                $permission->update($decodedData);

                // Log activity
                addActivity('permission', 'update', 'User updated permission from "' . $oldName . '" to "' . $permission->name . '"', $permission->id);

                return response()->json(['status' => 'success', 'message' => 'Permission updated successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid data format'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update permission: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permissionName = $permission->name;
            $permissionId = $permission->id;

            $permission->delete();

            // Log activity
            addActivity('permission', 'delete', 'User deleted permission: ' . $permissionName, $permissionId);

            return response()->json([
                'status' => 'success',
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permission not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions data for DataTables.
     */
    public function data(Request $request)
    {
        $query = Permission::query();

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
        $permissions = $query->get();

        $data = $permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
                'created_at' => $permission->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $permission->updated_at->format('Y-m-d H:i:s'),
                'actions' => '
                    <button onclick="editPermission(' . $permission->id . ')" class="btn btn-sm btn-primary">Edit</button>
                    <button onclick="deletePermission(' . $permission->id . ')" class="btn btn-sm btn-danger">Delete</button>
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
}
