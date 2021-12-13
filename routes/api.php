<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StorekeeperController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::post('auth/{provider}', [AuthController::class, 'authenticate'])->name('authenticate');

Route::post('user/', [UserController::class, 'factory'])->name('factoryUser');
Route::post('storekeeper/', [StorekeeperController::class, 'factory'])->name('factoryStorekeeper');

Route::group(['middleware' => ['jwt']], function() {
    Route::get('me/{provider}', [AuthController::class, 'me'])->name('me');

    Route::post('transaction', [TransactionController::class, 'realizeTransaction'])->name('transaction');
});

