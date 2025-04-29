<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SalesPersonApiController;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/verify-email', [AuthController::class, 'verify']);
Route::post('/resend-verification', [AuthController::class, 'resend']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-password-otp', [AuthController::class, 'verifyPasswordOTP']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::prefix('salespersons')->group(function () {
    Route::get('/', [SalesPersonApiController::class, 'index']);
    Route::post('/', [SalesPersonApiController::class, 'store']);
    Route::get('/{id}', [SalesPersonApiController::class, 'show']);
    Route::put('/{id}', [SalesPersonApiController::class, 'update']);
    Route::delete('/{id}', [SalesPersonApiController::class, 'destroy']);
    Route::get('/new-code', [SalesPersonApiController::class, 'getNewCode']);
})->middleware('auth:sanctum');
