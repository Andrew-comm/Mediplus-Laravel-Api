<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

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
        $medicine = Medicine::create(
        $request->validated()

    );

    // return response()->json($medicine,201);

    return new MedicineResource($medicine);
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
        $medicine->update(
        $request->validated()
    );


    return response() ->json([
        'message' => 'medicine updated successfully',

        'data'=>'$medicine'



    ]);
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



