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

Auth::routes();


// Login routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', function () {
    return view('index');
})->name('home');



Route::middleware(['auth', 'verified'])->group(function () {

    // Update the dashboard route with a name
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route::get('/', function () {
    //     // Return a view named 'index' when accessing the root URL
    //     return view('index');
    // });

    //role 
    route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
    Route::post('roles/{role}/give-permission-to', [RoleController::class, 'givePermissionTo'])->name('roles.givePermissionTo');
    route::get('roles-has-permission/{id}', [RoleController::class, 'roleHasPermission'])->name('roles.roleHasPermission');

    //permission
    route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    route::post('permissions/data', [PermissionController::class, 'data'])->name('permissions.data');

    //users
    route::get('users', [UserController::class, 'index'])->name('users.index');
    route::get('users/create', [UserController::class, 'create'])->name('users.create');
    route::post('users', [UserController::class, 'store'])->name('users.store');
    route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    route::post('users/data', [UserController::class, 'data'])->name('users.data');

    //menus
    route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    route::get('menus/create', [MenuController::class, 'create'])->name('menus.create');
    route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    route::get('menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
    route::get('menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
    route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
    route::post('menus/data', [MenuController::class, 'data'])->name('menus.data');


    // History Sales routes
    Route::get('history-sales', [HistorySaleController::class, 'index'])->name('history-sales.index');
    Route::post('history-sales', [HistorySaleController::class, 'store'])->name('history-sales.store');
    Route::get('history-sales/create', [HistorySaleController::class, 'create'])->name('history-sales.create');
    Route::get('history-sales/{historySale}', [HistorySaleController::class, 'show'])->name('history-sales.show');
    Route::get('history-sales/{historySale}/edit', [HistorySaleController::class, 'edit'])->name('history-sales.edit');
    Route::put('history-sales/{historySale}', [HistorySaleController::class, 'update'])->name('history-sales.update');
    Route::delete('history-sales/{historySale}', [HistorySaleController::class, 'destroy'])->name('history-sales.destroy');
    Route::post('history-sales/data', [HistorySaleController::class, 'data'])->name('history-sales.data');

    // Route untuk validasi no_resi
    Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('history-sales.validate-no-resi');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::get('/load-more-activities', [ActivityController::class, 'loadMoreActivities'])->name('load.more.activities');

    Route::get('/alerts', function () {
        // Return a view named 'index' when accessing the root URL
        // return view('elements.ac_alert');
        return view('table.dt_advance');
    });

    Route::resource('suppliers', SupplierController::class);

    // Define a GET route with dynamic placeholders for route parameters.
    Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);

    // Define a GET route with dynamic placeholders for route parameters.
    Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);

});
