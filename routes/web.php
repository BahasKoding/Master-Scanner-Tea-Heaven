<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ActivityController;
// use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HistorySaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CatatanProduksiController;
use App\Http\Controllers\FinishedGoodsController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    // Scanner routes - khusus untuk scan dan input data
    Route::prefix('scanner')->name('scanner.')->group(function () {
        Route::get('/', [HistorySaleController::class, 'index'])->name('index');
        Route::post('/store', [HistorySaleController::class, 'store'])->name('store');
        Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('validate-no-resi');
    });
    // Sales Management routes - untuk CRUD data penjualan
    Route::prefix('sales-management')->name('sales-management.')->group(function () {
        Route::get('/', [HistorySaleController::class, 'management'])->name('index');
        Route::post('/', [HistorySaleController::class, 'store'])->name('store');
        Route::get('/create', [HistorySaleController::class, 'create'])->name('create');
        Route::get('/{id}', [HistorySaleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HistorySaleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HistorySaleController::class, 'update'])->name('update');
        Route::delete('/{id}', [HistorySaleController::class, 'destroy'])->name('destroy');
        Route::post('/data', [HistorySaleController::class, 'data'])->name('data');

        // Soft Delete routes
        Route::post('/{id}/restore', [HistorySaleController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [HistorySaleController::class, 'forceDelete'])->name('force-delete');
    });

    // Sales Report routes - untuk laporan penjualan
    Route::prefix('sales-report')->name('sales-report.')->group(function () {
        Route::get('/', [HistorySaleController::class, 'report'])->name('index');
        Route::post('/export', [HistorySaleController::class, 'export'])->name('export');
    });

    // History Sales routes with resource and additional actions (keep for backward compatibility)
    Route::prefix('history-sales')->name('history-sales.')->group(function () {
        Route::get('/', [HistorySaleController::class, 'index'])->name('index');
        Route::post('/', [HistorySaleController::class, 'store'])->name('store');
        Route::get('/create', [HistorySaleController::class, 'create'])->name('create');
        Route::get('/report', [HistorySaleController::class, 'report'])->name('report');
        Route::post('/export', [HistorySaleController::class, 'export'])->name('export');
        Route::get('/{id}', [HistorySaleController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [HistorySaleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [HistorySaleController::class, 'update'])->name('update');
        Route::delete('/{id}', [HistorySaleController::class, 'destroy'])->name('destroy');
        Route::post('/data', [HistorySaleController::class, 'data'])->name('data');

        // Soft Delete routes
        Route::post('/{id}/restore', [HistorySaleController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [HistorySaleController::class, 'forceDelete'])->name('force-delete');
    });

    // Validation route
    Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('history-sales.validate-no-resi');

    // Resource routes using Laravel's resource controller pattern
    Route::resource('products', ProductController::class);
    Route::resource('catatan-produksi', CatatanProduksiController::class);
    Route::get('get-bahan-baku-list', [CatatanProduksiController::class, 'getBahanBakuList'])->name('catatan-produksi.bahan-baku-list');
    Route::get('get-filtered-products-list', [CatatanProduksiController::class, 'getFilteredProductsList'])->name('catatan-produksi.filtered-products-list');

    // Finished Goods routes with custom parameter structure
    Route::prefix('finished-goods')->name('finished-goods.')->group(function () {
        Route::get('/', [FinishedGoodsController::class, 'index'])->name('index');
        Route::post('/', [FinishedGoodsController::class, 'store'])->name('store');
        Route::get('/{productId}/edit', [FinishedGoodsController::class, 'edit'])->name('edit');
        Route::put('/{productId}', [FinishedGoodsController::class, 'update'])->name('update');
        Route::post('/data', [FinishedGoodsController::class, 'data'])->name('data');
    });

    Route::resource('bahan-baku', BahanBakuController::class);

    // Purchase routes - untuk pembelian bahan baku
    Route::resource('purchase', PurchaseController::class);
    Route::post('purchase/data', [PurchaseController::class, 'data'])->name('purchase.data');

    // Inventory Bahan Baku routes - untuk manajemen inventory bahan baku
    Route::prefix('inventory-bahan-baku')->name('inventory-bahan-baku.')->group(function () {
        Route::get('/', [\App\Http\Controllers\InventoryBahanBakuController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\InventoryBahanBakuController::class, 'store'])->name('store');
        Route::get('/{bahanBakuId}/edit', [\App\Http\Controllers\InventoryBahanBakuController::class, 'edit'])->name('edit');
        Route::put('/{bahanBakuId}', [\App\Http\Controllers\InventoryBahanBakuController::class, 'update'])->name('update');
        Route::post('/data', [\App\Http\Controllers\InventoryBahanBakuController::class, 'data'])->name('data');
    });

    // Sticker routes
    Route::resource('stickers', StickerController::class);

    // Debug route for sticker testing
    Route::get('/test-sticker', function () {
        try {
            // Test database connection
            $dbTest = DB::connection()->getPdo();

            // Test if stickers table exists
            $tableExists = Schema::hasTable('stickers');

            // Test if products table has eligible products
            $eligibleProducts = \App\Models\Product::whereIn('label', [1, 2, 5])->get();

            // Test Sticker model
            $stickerModel = new \App\Models\Sticker();

            return response()->json([
                'database_connected' => $dbTest ? true : false,
                'stickers_table_exists' => $tableExists,
                'eligible_products_count' => $eligibleProducts->count(),
                'eligible_products' => $eligibleProducts->toArray(),
                'sticker_fillable' => $stickerModel->getFillable(),
                'sticker_table' => $stickerModel->getTable(),
                'csrf_token' => csrf_token()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    });

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
