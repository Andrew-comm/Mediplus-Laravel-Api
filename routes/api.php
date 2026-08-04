<?php

use App\Http\Controllers\Api\AuthController;

use App\Modules\Customers\Controllers\CustomerController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Inventory\Controllers\InventoryController;
use App\Modules\Medicines\Controllers\MedicineController;
use App\Modules\Payments\Controllers\PaymentController;
use App\Modules\Sales\Controllers\SaleController;
use App\Modules\StockMovements\Controllers\StockMovementController;
use App\Modules\Suppliers\Controllers\SupplierController;

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
    'stock-movements',
    StockMovementController::class
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

    Route::apiResource(

        'payments',

        PaymentController::class

    );

    Route::apiResource(

        'customers',

        CustomerController::class

    );

    Route::apiResource(
            'sales',
            SaleController::class
 );

    Route::get(
        '/inventory',
        [InventoryController::class,'index']
    );






});


