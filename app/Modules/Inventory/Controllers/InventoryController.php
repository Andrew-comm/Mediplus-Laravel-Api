<?php


namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Medicines\Models\Medicine;

use App\Modules\StockMovements\Models\StockMovement;


class InventoryController extends Controller
{


public function index()
{
    $medicines = Medicine::with('supplierData')->get();

    return response()->json([

        'summary' => [

            'current_units' => $medicines->sum('quantity'),

            'stock_value' => $medicines->sum(function ($medicine) {
                return $medicine->quantity * $medicine->buying_price;
            }),

            'total_batches' => $medicines->count(),

            'last_updated' => optional(
                $medicines->sortByDesc('updated_at')->first()
            )->updated_at

        ],

        'items' => $medicines->map(function ($medicine) {

            return [

                'id' => $medicine->id,

                'name' => $medicine->name,

                'batch_number' => $medicine->batch_number,

                'supplier' => $medicine->supplierData?->name,

                'quantity' => $medicine->quantity,

                'buying_price' => $medicine->buying_price,

                'selling_price' => $medicine->selling_price,

                'stock_value' =>
                    $medicine->quantity * $medicine->buying_price,

                'expiry_date' => $medicine->expiry_date,

                'status' => match (true) {

                    $medicine->quantity == 0 => 'Out of Stock',

                    $medicine->quantity < 20 => 'Low Stock',

                    default => 'Available'

                }

            ];

        })

    ]);
}

public function summary()
{

    return response()->json([

        'totalMovements'=>StockMovement::count(),

        'stockIn'=>StockMovement::where(
            'direction',
            'IN'
        )->sum('quantity'),


        'stockOut'=>StockMovement::where(
            'direction',
            'OUT'
        )->sum('quantity'),


        'adjustments'=>StockMovement::where(
            'type',
            'adjustment'
        )->count()

    ]);

}



}
