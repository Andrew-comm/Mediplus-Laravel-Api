<?php


namespace App\Modules\StockMovements\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\StockMovements\Resources\StockMovementResource;

use App\Modules\StockMovements\Models\StockMovement;


class StockMovementController extends Controller
{


public function index()
{

    $movements = StockMovement::with('medicine')
        ->latest()
        ->get();


    return StockMovementResource::collection(
        $movements
    );

}



}
