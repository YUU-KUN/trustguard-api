<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BankController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


// All Authenticated can access
Route::middleware(['auth:api'])->group(function () {
    Route::get('profile', [AuthController::class, 'getProfile']);
    Route::resource('report', ReportController::class);
    
    // 
    Route::get('transaction', [TransactionController::class, 'getTransactions']);
    Route::get('transaction/{transaction_id}', [TransactionController::class, 'getTransactionDetail']);
    Route::get('available-fraud-transaction', [TransactionController::class, 'checkAvailableFraudTransaction']);

    Route::get('user-bank', [TransactionController::class, 'getUserBank']);
    Route::get('last-transfer', [TransactionController::class, 'getLastTransfer']);
    Route::post('get-rekening-status', [TransactionController::class, 'getRekeningStatus']);
    Route::post('transfer', [TransactionController::class, 'transfer']);
    Route::resource('bank', BankController::class);

    Route::get('platforms', function () {
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data bank',
            'data' => \App\Models\Platform::all()
        ], 200);
    });

    Route::get('product-categories', function () {
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data bank',
            'data' => \App\Models\ProductCategory::all()
        ], 200);
    });

    // Route::resource('notification', NotificationController::class);
});