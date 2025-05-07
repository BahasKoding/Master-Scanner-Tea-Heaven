<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ActivityController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HistorySaleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;

// Laravel Auth Routes
Auth::routes();

// Dashboard route (protected by auth middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route for displaying the success message after login
    Route::get('/home', function () {
        return view('index');
    })->name('home');

    //role 
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
    Route::post('roles/{role}/give-permission-to', [RoleController::class, 'givePermissionTo'])->name('roles.givePermissionTo');
    Route::get('roles-has-permission/{id}', [RoleController::class, 'roleHasPermission'])->name('roles.roleHasPermission');

    //permission
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('permissions/data', [PermissionController::class, 'data'])->name('permissions.data');

    //users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/data', [UserController::class, 'data'])->name('users.data');

    //menus
    Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    Route::get('menus/create', [MenuController::class, 'create'])->name('menus.create');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::get('menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
    Route::get('menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
    Route::post('menus/data', [MenuController::class, 'data'])->name('menus.data');


    // History Sales routes
    Route::get('history-sales', [HistorySaleController::class, 'index'])->name('history-sales.index');
    Route::post('history-sales', [HistorySaleController::class, 'store'])->name('history-sales.store');
    Route::get('history-sales/create', [HistorySaleController::class, 'create'])->name('history-sales.create');
    Route::get('history-sales/report', [HistorySaleController::class, 'report'])->name('history-sales.report');
    Route::post('history-sales/export', [HistorySaleController::class, 'export'])->name('history-sales.export');
    Route::get('history-sales/{historySale}', [HistorySaleController::class, 'show'])->name('history-sales.show');
    Route::get('history-sales/{historySale}/edit', [HistorySaleController::class, 'edit'])->name('history-sales.edit');
    Route::put('history-sales/{historySale}', [HistorySaleController::class, 'update'])->name('history-sales.update');
    Route::delete('history-sales/{historySale}', [HistorySaleController::class, 'destroy'])->name('history-sales.destroy');
    Route::post('history-sales/data', [HistorySaleController::class, 'data'])->name('history-sales.data');

    // Soft Delete routes
    Route::post('history-sales/{id}/restore', [HistorySaleController::class, 'restore'])->name('history-sales.restore');
    Route::delete('history-sales/{id}/force', [HistorySaleController::class, 'forceDelete'])->name('history-sales.force-delete');

    // Route untuk validasi no_resi
    Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('history-sales.validate-no-resi');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::get('/load-more-activities', [ActivityController::class, 'loadMoreActivities'])->name('load.more.activities');

    // New activity routes
    Route::get('/auth-activities', [ActivityController::class, 'getAuthActivities'])->name('auth.activities');
    Route::get('/user-activities/{userId}', [ActivityController::class, 'getUserActivities'])->name('user.activities');
    Route::get('/user/{userId}/activities', [ActivityController::class, 'showUserActivities'])->name('user.activities.show');

    Route::get('/alerts', function () {
        // Return a view named 'index' when accessing the root URL
        // return view('elements.ac_alert');
        return view('table.dt_advance');
    });

    Route::resource('suppliers', SupplierController::class);

    // Define a GET route with dynamic placeholders for route parameters.
    Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});

// Redirect root URL to login page
Route::get('/', function () {
    return redirect()->route('login');
});
