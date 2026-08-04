<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Resources\StockMovementResource;

use App\Models\StockMovement;


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
