<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $item = 'Users Management';
    protected $itemActive = 'Users';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$userId = auth()->id();
        //$user = User::find($userId);
        // Menetapkan role
        //$user->assignRole('admin');
        //$roleNames = $user->getRoleNames();
        //return $roleNames;
        $currentUser = Auth::user()->getRoleNames();

        if ($currentUser->contains('Super Admin')) {
            $roles = Role::all();
        } elseif ($currentUser->contains('Admin')) {
            $roles = Role::where('name', '!=', 'Super Admin')->get();
        } elseif ($currentUser->contains('Operator')) {
            $roles = Role::where('name', '!=', 'Super Admin')->where('name', '!=', 'Admin')->get();
        }

        $users = User::get();

        return view('backend.users.index', compact('users', 'roles'))->with('item', $this->item)->with('itemActive', $this->itemActive);
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required',
            ], [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'The email has already been taken.',
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'role.required' => 'The role field is required.',
            ]);

            // Create user and set email_verified_at to current timestamp with Carbon format
            $user = User::create(array_merge(
                $request->only('name', 'email'),
                [
                    'password' => Hash::make($request->password),
                    'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
                ]
            ));
            $user->assignRole($request->role);

            // Record activity
            if (function_exists('addActivity')) {
                addActivity('user', 'create', 'User ' . $user->name . ' was created by ' . Auth::user()->name, $user->id);
            }

            return response()->json(['status' => 'success', 'message' => 'User created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }

    public function edit(User $user)
    {
        try {
            $data = User::with('roles')->findOrFail($user->id);
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the user'
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'data.name' => 'required|string|max:255',
                'data.email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'data.password' => 'nullable|string|min:8|confirmed',
                'data.role' => 'required',
            ], [
                'data.name.required' => 'The name field is required.',
                'data.email.required' => 'The email field is required.',
                'data.email.email' => 'Please enter a valid email address.',
                'data.email.max' => 'The email must not exceed 255 characters.',
                'data.email.unique' => 'The email has already been taken.',
                'data.password.min' => 'The password must be at least 8 characters.',
                'data.password.confirmed' => 'The password confirmation does not match.',
                'data.role.required' => 'The role field is required.',
            ]);

            // Update user data (except password)
            $user->fill([
                'name' => $validatedData['data']['name'],
                'email' => $validatedData['data']['email'],
            ]);

            // Update password if provided
            if (!empty($validatedData['data']['password'])) {
                $user->password = Hash::make($validatedData['data']['password']);
            }

            // Save the updated user data
            $user->save();

            // Sync roles if role is provided
            if (isset($validatedData['data']['role'])) {
                $user->syncRoles([$validatedData['data']['role']]);
            }

            // Record activity
            if (function_exists('addActivity')) {
                addActivity('user', 'update', 'User ' . $user->name . ' was updated by ' . Auth::user()->name, $user->id);
            }

            return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $userName = $user->name;
            $userId = $user->id;

            $user->delete();

            // Record activity
            if (function_exists('addActivity')) {
                addActivity('user', 'delete', 'User ' . $userName . ' was deleted by ' . Auth::user()->name, $userId);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            // Query dengan eager loading untuk roles
            $query = User::with('roles');

            // Get total count before filtering
            $totalRecords = $query->count();

            // Apply search filter if present
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%")
                        ->orWhere('email', 'like', "%{$searchValue}%");
                });
            }

            // Get filtered count
            $filteredRecords = $query->count();

            // Apply ordering
            if ($request->has('order') && !empty($request->order)) {
                $columnIndex = $request->order[0]['column'];
                $columnName = $request->columns[$columnIndex]['name'];
                $columnDirection = $request->order[0]['dir'];

                if ($columnName && $columnName != 'actions' && $columnName != 'roles') {
                    $query->orderBy($columnName, $columnDirection);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Apply pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            // Filter users based on current user role
            $currentUser = Auth::user()->getRoleNames();
            if ($currentUser->contains('Super Admin')) {
                // Super admin can see all users
            } elseif ($currentUser->contains('Admin')) {
                // Admin cannot see Super Admin users
                $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                });
            } elseif ($currentUser->contains('Operator')) {
                // Operator cannot see Super Admin or Admin users
                $query->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['Super Admin', 'Admin']);
                });
            }

            // Get paginated data with roles
            $users = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = $users->map(function ($user) {
                // Get roles as an array of strings
                $rolesArray = $user->roles->map(function ($role) {
                    return $role->name;
                })->toArray();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => implode(', ', $rolesArray),
                    'roles' => $user->roles->toArray(),
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('DataTables error: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching data: ' . $e->getMessage()
            ]);
        }
    }
}
