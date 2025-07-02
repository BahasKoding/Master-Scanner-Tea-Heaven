<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $item = 'Users Management';
    protected $itemActive = 'Users';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        // Apply permission middleware for user management
        $this->middleware('permission:Users List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Users Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Users Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Users Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Users View', ['only' => ['show']]);

        // Debug route removed for production
    }

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
        $currentUser = Auth::user();
        $userRoles = $currentUser->roles->pluck('name');

        if ($userRoles->contains('Super Admin')) {
            $roles = Role::all();
        } elseif ($userRoles->contains('Admin')) {
            $roles = Role::where('name', '!=', 'Super Admin')->get();
        } elseif ($userRoles->contains('Operator')) {
            $roles = Role::where('name', '!=', 'Super Admin')->where('name', '!=', 'Admin')->get();
        } else {
            // Default case if user has no recognized roles
            $roles = collect();
        }

        // Load users with their roles for the initial page load
        $users = User::with('roles')->get();

        Log::info('Index method - users count: ' . $users->count());

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
            // SECURITY: Prevent editing Super Admin users
            if ($user->hasRole('Super Admin')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Super Admin users cannot be edited for security reasons.'
                ], 403);
            }

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
            // SECURITY: Prevent updating Super Admin users
            if ($user->hasRole('Super Admin')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Super Admin users cannot be updated for security reasons.'
                ], 403);
            }

            Log::info('Update user request', [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            // Extract data from request
            $data = $request->input('data', $request->all());

            // Validate the request
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'role' => 'required',
            ];

            // Only add password validation if it's provided
            if (!empty($data['password'])) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $validatedData = validator($data, $rules, [
                'name.required' => 'The name field is required.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'The email has already been taken.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'role.required' => 'The role field is required.',
            ])->validate();

            // Update user data (except password)
            $user->fill([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
            ]);

            // Update password if provided
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            // Save the updated user data
            $user->save();

            // Sync roles if role is provided
            if (isset($validatedData['role'])) {
                $user->syncRoles([$validatedData['role']]);
            }

            // Record activity
            if (function_exists('addActivity')) {
                addActivity('user', 'update', 'User ' . $user->name . ' was updated by ' . Auth::user()->name, $user->id);
            }

            return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            // SECURITY: Prevent deleting Super Admin users
            if ($user->hasRole('Super Admin')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Super Admin users cannot be deleted for security reasons.'
                ], 403);
            }

            // Check if user is trying to delete themselves
            if (Auth::id() === $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot delete your own account.'
                ], 403);
            }

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
     * Debug method to directly query the database
     */
    public function debug()
    {
        try {
            // Get all users with their roles
            $users = User::with('roles')->get();

            // Get the currently logged in user
            $currentUser = Auth::user();
            $currentUserRoles = $currentUser->roles->pluck('name');

            // Generate the SQL query that would be used for filtering
            $query = User::with('roles');

            // Apply role-based filtering as in the data method
            if ($currentUserRoles->contains('Super Admin')) {
                // Super admin sees all users
            } elseif ($currentUserRoles->contains('Admin')) {
                $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                });
            } elseif ($currentUserRoles->contains('Operator')) {
                $query->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['Super Admin', 'Admin']);
                });
            }

            // Get the SQL query
            $queryStr = vsprintf(str_replace(['?'], ['\'%s\''], $query->toSql()), $query->getBindings());

            return response()->json([
                'users' => $users,
                'usersCount' => $users->count(),
                'currentUser' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'email' => $currentUser->email,
                    'roles' => $currentUserRoles
                ],
                'query' => $queryStr
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                $columnName = $request->columns[$columnIndex]['name'] ?? $request->columns[$columnIndex]['data'];
                $columnDirection = $request->order[0]['dir'];

                if ($columnName && $columnName != 'actions' && $columnName != 'roles' && $columnName != 'role') {
                    $query->orderBy($columnName, $columnDirection);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            // Filter users based on current user role
            $currentUser = Auth::user();
            $userRoles = $currentUser->roles->pluck('name');

            if ($userRoles->contains('Super Admin')) {
                // Super admin can see all users
            } elseif ($userRoles->contains('Admin')) {
                // Admin cannot see Super Admin users
                $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Super Admin');
                });
            } elseif ($userRoles->contains('Operator')) {
                // Operator cannot see Super Admin or Admin users
                $query->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['Super Admin', 'Admin']);
                });
            }

            // Execute the query with pagination
            $users = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = [];
            foreach ($users as $user) {
                $roleNames = $user->roles->pluck('name')->toArray();

                $data[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => implode(', ', $roleNames),
                    'roles' => $roleNames,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                ];
            }

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error in UserController data method', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching users data'
            ], 500);
        }
    }

    /**
     * Simple method to get users data directly for DataTables
     */
    public function getUsers(Request $request)
    {
        try {
            $users = User::with('roles')->get();

            $data = [];
            foreach ($users as $user) {
                $roleNames = $user->roles->pluck('name')->toArray();

                $data[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => implode(', ', $roleNames),
                    'roles' => $roleNames,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s')
                ];
            }

            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => (int)($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}
