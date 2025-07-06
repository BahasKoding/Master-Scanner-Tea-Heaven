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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseStickerController;
use App\Http\Controllers\InventoryBahanBakuController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::post('/validate-sku', [HistorySaleController::class, 'validateSku'])->name('validate-sku');
        Route::get('/search-products', [HistorySaleController::class, 'searchProducts'])->name('search-products');
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

    // Validation routes
    Route::post('/validate-no-resi', [HistorySaleController::class, 'validateNoResi'])->name('history-sales.validate-no-resi');
    Route::post('/validate-sku', [HistorySaleController::class, 'validateSku'])->name('history-sales.validate-sku');
    Route::get('/search-products', [HistorySaleController::class, 'searchProducts'])->name('history-sales.search-products');

    // Resource routes using Laravel's resource controller pattern
    Route::resource('products', ProductController::class);
    Route::post('products/export', [ProductController::class, 'exportExcel'])->name('products.export');
    Route::post('products/check-sku', [ProductController::class, 'checkSku'])->name('products.check-sku');
    Route::resource('catatan-produksi', CatatanProduksiController::class);
    Route::get('get-bahan-baku-list', [CatatanProduksiController::class, 'getBahanBakuList'])->name('catatan-produksi.bahan-baku-list');
    Route::get('get-filtered-products-list', [CatatanProduksiController::class, 'getFilteredProductsList'])->name('catatan-produksi.filtered-products-list');
    Route::post('catatan-produksi/sync', [CatatanProduksiController::class, 'sync'])->name('catatan-produksi.sync');
    Route::post('catatan-produksi/statistics', [CatatanProduksiController::class, 'statistics'])->name('catatan-produksi.statistics');
    Route::post('catatan-produksi/verify-consistency', [CatatanProduksiController::class, 'verifyConsistency'])->name('catatan-produksi.verify-consistency');

    // Finished Goods routes with custom parameter structure
    Route::prefix('finished-goods')->name('finished-goods.')->group(function () {
        Route::get('/', [FinishedGoodsController::class, 'index'])->name('index');
        Route::post('/', [FinishedGoodsController::class, 'store'])->name('store');
        Route::get('/{productId}/edit', [FinishedGoodsController::class, 'edit'])->name('edit');
        Route::put('/{productId}', [FinishedGoodsController::class, 'update'])->name('update');
        Route::post('/{productId}/reset', [FinishedGoodsController::class, 'reset'])->name('reset');
        Route::post('/data', [FinishedGoodsController::class, 'data'])->name('data');
        Route::post('/sync', [FinishedGoodsController::class, 'sync'])->name('sync');
        Route::post('/statistics', [FinishedGoodsController::class, 'statistics'])->name('statistics');
        Route::get('/low-stock', [FinishedGoodsController::class, 'lowStock'])->name('low-stock');
        Route::post('/verify-consistency', [FinishedGoodsController::class, 'verifyConsistency'])->name('verify-consistency');
    });

    Route::resource('bahan-baku', BahanBakuController::class);
    Route::post('bahan-baku/check-sku-induk', [BahanBakuController::class, 'checkSkuInduk'])->name('bahan-baku.check-sku-induk');

    // Purchase routes - untuk pembelian bahan baku
    Route::resource('purchase', PurchaseController::class);
    Route::post('purchase/data', [PurchaseController::class, 'data'])->name('purchase.data');
    Route::post('purchase/sync', [PurchaseController::class, 'syncPurchaseData'])->name('purchase.sync');
    Route::post('purchase/statistics', [PurchaseController::class, 'getStatistics'])->name('purchase.statistics');

    // Purchase Sticker routes - untuk pembelian sticker
    Route::resource('purchase-sticker', PurchaseStickerController::class);
    Route::post('purchase-sticker/data', [PurchaseStickerController::class, 'data'])->name('purchase-sticker.data');
    Route::get('purchase-sticker/get-sticker-data/{productId}', [PurchaseStickerController::class, 'getStickerData'])->name('purchase-sticker.get-sticker-data');

    // Inventory Bahan Baku routes - untuk manajemen inventory bahan baku
    Route::prefix('inventory-bahan-baku')->name('inventory-bahan-baku.')->group(function () {
        Route::get('/', [InventoryBahanBakuController::class, 'index'])->name('index');
        Route::post('/', [InventoryBahanBakuController::class, 'store'])->name('store');
        Route::get('/{bahanBakuId}/edit', [InventoryBahanBakuController::class, 'edit'])->name('edit');
        Route::put('/{bahanBakuId}', [InventoryBahanBakuController::class, 'update'])->name('update');
        Route::post('/{bahanBakuId}/reset', [InventoryBahanBakuController::class, 'reset'])->name('reset');
        Route::post('/data', [InventoryBahanBakuController::class, 'data'])->name('data');
        Route::post('/sync-all', [InventoryBahanBakuController::class, 'syncAll'])->name('sync-all');
        Route::post('/force-sync', [InventoryBahanBakuController::class, 'forceSync'])->name('force-sync');
        Route::post('/verify-consistency', [InventoryBahanBakuController::class, 'verifyConsistency'])->name('verify-consistency');
        Route::post('/inventory-status', [InventoryBahanBakuController::class, 'getInventoryStatus'])->name('inventory-status');
        Route::get('/low-stock/{threshold?}', [InventoryBahanBakuController::class, 'getLowStock'])->name('low-stock');
    });

    // Sticker routes
    Route::resource('stickers', StickerController::class);
    Route::post('stickers/export', [StickerController::class, 'export'])->name('stickers.export');

    /*
    |--------------------------------------------------------------------------
    | Reports Routes
    |--------------------------------------------------------------------------
    */

    // Purchase Report routes
    Route::prefix('reports/purchase')->name('reports.purchase.')->group(function () {
        Route::get('/', [ReportController::class, 'purchaseIndex'])->name('index');
        Route::post('/export', [ReportController::class, 'purchaseExport'])->name('export');
    });

    // Catatan Produksi Report routes
    Route::prefix('reports/catatan-produksi')->name('reports.catatan-produksi.')->group(function () {
        Route::get('/', [ReportController::class, 'catatanProduksiIndex'])->name('index');
        Route::post('/export', [ReportController::class, 'catatanProduksiExport'])->name('export');
    });

    // Scanner Report routes
    Route::prefix('reports/scanner')->name('reports.scanner.')->group(function () {
        Route::get('/', [ReportController::class, 'scannerIndex'])->name('index');
        Route::post('/export', [ReportController::class, 'scannerExport'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | Activity Monitoring Routes
    |--------------------------------------------------------------------------
    */

    // Activity routes - simplified
    Route::prefix('activity')->name('activity')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
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
    // Debug route to test dashboard
    // Route::get('/dashboard-test', function () {
    //     try {
    //         $controller = new \App\Http\Controllers\DashboardController();
    //         $request = new \Illuminate\Http\Request();
    //         return $controller->index($request);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //             'file' => $e->getFile(),
    //             'trace' => $e->getTraceAsString()
    //         ], 500);
    //     }
    // });
    // Define a GET route with dynamic placeholders for route parameters.
    Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});
