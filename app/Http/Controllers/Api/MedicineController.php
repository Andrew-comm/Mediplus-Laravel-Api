<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MedicineResource::collection(

            Medicine::with('supplierData')
            ->paginate(10)

        );
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(StoreMedicineRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $medicine = Medicine::create(
                $request->validated()
            );

            // Create opening stock movement
            if ($medicine->quantity > 0) {

              StockMovement::create([

                'medicine_id' => $medicine->id,

                'type' => 'opening',

                'direction' => 'IN',

                'quantity' => $medicine->quantity,

                'reference' => 'OPEN-' . $medicine->id,

                'remarks' => 'Initial stock when medicine was created'

            ]);

            }

            return new MedicineResource(
                $medicine->load('supplierData')
            );

        });
    }

    /**
     * Display the specified resource.
     */
   public function show(Medicine $medicine)
    {
        $medicine->load('supplierData');

        return new MedicineResource($medicine);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        return DB::transaction(function () use ($request, $medicine) {

            $oldQuantity = $medicine->quantity;

            $medicine->update($request->validated());

            $newQuantity = $medicine->quantity;

            $difference = $newQuantity - $oldQuantity;

            if ($difference != 0) {

                StockMovement::create([

                    'medicine_id' => $medicine->id,

                    'type' => 'adjustment',

                    'direction' => $difference > 0 ? 'IN' : 'OUT',

                    'quantity' => abs($difference),

                    'reference' => 'ADJ-' . now()->format('YmdHis'),

                    'remarks' => $difference > 0
                        ? 'Stock increased manually'
                        : 'Stock reduced manually'

                ]);

            }

            return new MedicineResource(
                $medicine->fresh()->load('supplierData')
            );

        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
           $medicine->delete();

    return response()->json([
        "message"=>"Medicine deleted"
    ]);
    }
    public function expiringSoon()
        {
            $medicines = Medicine::with('supplierData')
                ->whereBetween(
                    'expiry_date',
                    [
                        now(),
                        now()->addDays(30)
                    ]
                )
                ->get();


            return MedicineResource::collection(
                $medicines
            );
        }

public function lowStock()
{

    return MedicineResource::collection(

        Medicine::where(
            'quantity',
            '<',
            20
        )
        ->with('supplierData')
        ->get()

    );

}
public function expired()
{

    $medicines = Medicine::with('supplierData')
        ->where(
            'expiry_date',
            '<',
            now()
        )
        ->get();


    return MedicineResource::collection(
        $medicines
    );

}



}



