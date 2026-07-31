<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get(
    '/medicines/expired',
    [MedicineController::class,'expired']
);


Route::get(
    '/medicines/low-stock',
    [MedicineController::class,'lowStock']
);


Route::get(
    '/medicines/expiring-soon',
    [MedicineController::class,'expiringSoon']
);


Route::apiResource(
    'medicines',
    MedicineController::class
);


Route::get(
    'dashboard',
    [DashboardController::class, 'index']
);


Route::apiResource(
    'suppliers',
    SupplierController::class
);
