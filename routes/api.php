<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\SaleController;
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

Route::post(
'/register',
[AuthController::class,'register']
);

Route::post(
    '/login',
    [AuthController::class,'login']
);

Route::apiResource(

    'customers',

    CustomerController::class

);

Route::apiResource(
    'sales',
    SaleController::class
);


Route::middleware('auth:sanctum')->group(function () {


    Route::post(
        '/logout',
        [AuthController::class,'logout']
    );




    Route::get(
        '/user',
        [AuthController::class,'user']
    );

    Route::get(
        '/dashboard',
        [DashboardController::class,'index']
    );

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





    Route::apiResource(
        'suppliers',
        SupplierController::class
    );


});


