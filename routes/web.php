<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ActivityController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HistorySaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategorySupplierController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root route - redirect to login if not authenticated or dashboard if authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Laravel Auth Routes
Auth::routes();

// All authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/home', function () {
        return view('index');
    })->name('home');

    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    |--------------------------------------------------------------------------
    */

    // User routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/data', [UserController::class, 'data'])->name('data');
        Route::post('/get-users', [UserController::class, 'getUsers'])->name('get-users');
    });

    // Role routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::post('/data', [RoleController::class, 'data'])->name('data');
        Route::post('/{role}/give-permission-to', [RoleController::class, 'givePermissionTo'])->name('givePermissionTo');
        Route::get('-has-permission/{id}', [RoleController::class, 'roleHasPermission'])->name('roleHasPermission');
    });

    // Permission routes
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::post('/data', [PermissionController::class, 'data'])->name('data');
    });


    /*
    |--------------------------------------------------------------------------
    | Business Logic Routes
    |--------------------------------------------------------------------------
    */

    // History Sales routes with resource and additional actions
    Route::prefix('history-sales')->name('history-sales.')->group(function () {
        Route::get('/', [HistorySaleController::class, 'index'])->name('index');
        Route::post('/', [HistorySaleController::class, 'store'])->name('store');
        Route::get('/create', [HistorySaleController::class, 'create'])->name('create');
        Route::get('/report', [HistorySaleController::class, 'report'])->name('report');
        Route::post('/export', [HistorySaleController::class, 'export'])->name('export');
        Route::get('/{historySale}', [HistorySaleController::class, 'show'])->name('show');
        Route::get('/{historySale}/edit', [HistorySaleController::class, 'edit'])->name('edit');
        Route::put('/{historySale}', [HistorySaleController::class, 'update'])->name('update');
        Route::delete('/{historySale}', [HistorySaleController::class, 'destroy'])->name('destroy');
        Route::post('/data', [HistorySaleController::class, 'data'])->name('data');

        // Soft Delete routes
        Route::post('/{id}/restore', [HistorySaleController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [HistorySaleController::class, 'forceDelete'])->name('force-delete');
    });

    // Validation route
    Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('history-sales.validate-no-resi');

    // Resource routes using Laravel's resource controller pattern
    Route::resource('suppliers', SupplierController::class);
    Route::resource('category-suppliers', CategorySupplierController::class);
    Route::resource('category-products', CategoryProductController::class);
    Route::resource('labels', LabelController::class);

    // Additional route for listing suppliers by category
    Route::get('category-suppliers/{categorySupplier}/suppliers', [CategorySupplierController::class, 'listSuppliers'])
        ->name('category-suppliers.list-suppliers');

    // Additional route for listing products by label
    Route::get('labels/{label}/products', [LabelController::class, 'listProducts'])
        ->name('labels.list-products');

    // Development route for seeding labels (only for authenticated users)
    Route::post('labels/seed', [LabelController::class, 'runSeeder'])
        ->name('labels.seed');

    /*
    |--------------------------------------------------------------------------
    | Activity Monitoring Routes
    |--------------------------------------------------------------------------
    */

    // Activity routes grouped by feature
    Route::prefix('activity')->name('activity')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
        Route::get('/load-more', [ActivityController::class, 'loadMoreActivities'])->name('.load.more');
        Route::get('/auth', [ActivityController::class, 'getAuthActivities'])->name('.auth');
        Route::get('/user/{userId}', [ActivityController::class, 'getUserActivities'])->name('.user');
        Route::get('/user/{userId}/details', [ActivityController::class, 'showUserActivities'])->name('.user.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous Routes
    |--------------------------------------------------------------------------
    */

    // Alert demo route
    Route::get('/alerts', function () {
        return view('forms.form2_choices');
    });

    // Temporary testing routes
    Route::get('/test-users', function () {
        $users = \App\Models\User::with('roles')->get();
        return response()->json([
            'users' => $users,
            'count' => $users->count()
        ]);
    });

    // Define a GET route with dynamic placeholders for route parameters.
    Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});
