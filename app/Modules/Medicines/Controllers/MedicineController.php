<?php

namespace App\Modules\Medicines\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Medicines\Requests\StoreMedicineRequest;
use App\Modules\Medicines\Requests\UpdateMedicineRequest;

use App\Modules\Medicines\Resources\MedicineResource;

use App\Modules\Medicines\Models\Medicine;

use App\Modules\StockMovements\Models\StockMovement;

use Illuminate\Support\Facades\DB;

use OpenApi\Attributes as OA;


class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/medicines",
        operationId: "getMedicines",
        summary: "Get all medicines",
        description: "Retrieve a paginated list of medicines including supplier information.",
        tags: ["Medicines"],

        responses: [
            new OA\Response(
                response: 200,
                description: "Medicines retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: "id",
                                        type: "integer",
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: "name",
                                        type: "string",
                                        example: "Amoxicillin"
                                    ),
                                    new OA\Property(
                                        property: "category",
                                        type: "string",
                                        example: "Antibiotic"
                                    ),
                                    new OA\Property(
                                        property: "batch_number",
                                        type: "string",
                                        example: "AMX-2026-001"
                                    ),
                                    new OA\Property(
                                        property: "expiry_date",
                                        type: "string",
                                        format: "date",
                                        example: "2027-12-31"
                                    ),
                                    new OA\Property(
                                        property: "buying_price",
                                        type: "number",
                                        format: "float",
                                        example: 100.00
                                    ),
                                    new OA\Property(
                                        property: "selling_price",
                                        type: "number",
                                        format: "float",
                                        example: 150.00
                                    ),
                                    new OA\Property(
                                        property: "quantity",
                                        type: "integer",
                                        example: 50
                                    ),
                                    new OA\Property(
                                        property: "supplier_id",
                                        type: "integer",
                                        example: 1
                                    )
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
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
    #[OA\Post(
        path: "/api/medicines",
        operationId: "createMedicine",
        summary: "Create a medicine",
        description: "Create a new medicine and automatically create an opening stock movement when the initial quantity is greater than zero.",
        tags: ["Medicines"],

        requestBody: new OA\RequestBody(
            required: true,
            description: "Medicine information",
            content: new OA\JsonContent(
                required: [
                    "name",
                    "category",
                    "batch_number",
                    "expiry_date",
                    "buying_price",
                    "selling_price",
                    "quantity",
                    "supplier_id"
                ],

                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Amoxicillin"
                    ),
                    new OA\Property(
                        property: "category",
                        type: "string",
                        example: "Antibiotic"
                    ),
                    new OA\Property(
                        property: "batch_number",
                        type: "string",
                        example: "AMX-2026-001"
                    ),
                    new OA\Property(
                        property: "expiry_date",
                        type: "string",
                        format: "date",
                        example: "2027-12-31"
                    ),
                    new OA\Property(
                        property: "buying_price",
                        type: "number",
                        format: "float",
                        example: 100.00
                    ),
                    new OA\Property(
                        property: "selling_price",
                        type: "number",
                        format: "float",
                        example: 150.00
                    ),
                    new OA\Property(
                        property: "quantity",
                        type: "integer",
                        example: 100
                    ),
                    new OA\Property(
                        property: "supplier_id",
                        type: "integer",
                        example: 1
                    )
                ]
            )
        ),

        responses: [
            new OA\Response(
                response: 201,
                description: "Medicine created successfully"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
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
    #[OA\Get(
        path: "/api/medicines/{medicine}",
        operationId: "getMedicine",
        summary: "Get a medicine",
        description: "Retrieve a single medicine by its ID.",
        tags: ["Medicines"],

        parameters: [
            new OA\Parameter(
                name: "medicine",
                description: "Medicine ID",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                ),
                example: 1
            )
        ],

        responses: [
            new OA\Response(
                response: 200,
                description: "Medicine retrieved successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Medicine not found"
            )
        ]
    )]
    public function show(Medicine $medicine)
    {
        $medicine->load('supplierData');

        return new MedicineResource($medicine);
    }


    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: "/api/medicines/{medicine}",
        operationId: "updateMedicine",
        summary: "Update a medicine",
        description: "Update an existing medicine. If the quantity changes, a stock adjustment movement is automatically created.",
        tags: ["Medicines"],

        parameters: [
            new OA\Parameter(
                name: "medicine",
                description: "Medicine ID",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                ),
                example: 1
            )
        ],

        requestBody: new OA\RequestBody(
            required: true,
            description: "Updated medicine information",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        example: "Amoxicillin"
                    ),
                    new OA\Property(
                        property: "category",
                        type: "string",
                        example: "Antibiotic"
                    ),
                    new OA\Property(
                        property: "batch_number",
                        type: "string",
                        example: "AMX-2026-001"
                    ),
                    new OA\Property(
                        property: "expiry_date",
                        type: "string",
                        format: "date",
                        example: "2027-12-31"
                    ),
                    new OA\Property(
                        property: "buying_price",
                        type: "number",
                        format: "float",
                        example: 105.00
                    ),
                    new OA\Property(
                        property: "selling_price",
                        type: "number",
                        format: "float",
                        example: 155.00
                    ),
                    new OA\Property(
                        property: "quantity",
                        type: "integer",
                        example: 80
                    ),
                    new OA\Property(
                        property: "supplier_id",
                        type: "integer",
                        example: 1
                    )
                ]
            )
        ),

        responses: [
            new OA\Response(
                response: 200,
                description: "Medicine updated successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Medicine not found"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function update(
        UpdateMedicineRequest $request,
        Medicine $medicine
    ) {
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
    #[OA\Delete(
        path: "/api/medicines/{medicine}",
        operationId: "deleteMedicine",
        summary: "Delete a medicine",
        description: "Delete a medicine from the system.",
        tags: ["Medicines"],

        parameters: [
            new OA\Parameter(
                name: "medicine",
                description: "Medicine ID",
                in: "path",
                required: true,
                schema: new OA\Schema(
                    type: "integer"
                ),
                example: 1
            )
        ],

        responses: [
            new OA\Response(
                response: 200,
                description: "Medicine deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Medicine not found"
            )
        ]
    )]
    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return response()->json([
            "message" => "Medicine deleted"
        ]);
    }


    /**
     * Get medicines expiring within 30 days.
     */
    #[OA\Get(
        path: "/api/medicines/expiring-soon",
        operationId: "getExpiringMedicines",
        summary: "Get medicines expiring soon",
        description: "Retrieve medicines whose expiry date falls within the next 30 days.",
        tags: ["Medicines"],

        responses: [
            new OA\Response(
                response: 200,
                description: "Expiring medicines retrieved successfully"
            )
        ]
    )]
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


    /**
     * Get medicines with low stock.
     */
    #[OA\Get(
        path: "/api/medicines/low-stock",
        operationId: "getLowStockMedicines",
        summary: "Get low stock medicines",
        description: "Retrieve medicines whose quantity is below 20 units.",
        tags: ["Medicines"],

        responses: [
            new OA\Response(
                response: 200,
                description: "Low stock medicines retrieved successfully"
            )
        ]
    )]
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


    /**
     * Get expired medicines.
     */
    #[OA\Get(
        path: "/api/medicines/expired",
        operationId: "getExpiredMedicines",
        summary: "Get expired medicines",
        description: "Retrieve medicines whose expiry date has already passed.",
        tags: ["Medicines"],

        responses: [
            new OA\Response(
                response: 200,
                description: "Expired medicines retrieved successfully"
            )
        ]
    )]
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
