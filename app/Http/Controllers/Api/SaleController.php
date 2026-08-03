<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SaleResource;


class SaleController extends Controller
{


    public function index()
    {

        $sales = Sale::with([
            'customer',
            'items.medicine',
            'payments'
        ])
        ->latest()
        ->get();


        return SaleResource::collection($sales);

    }





    public function store(Request $request)
    {


        $validated = $request->validate([

            'customer_id' =>
            'required|exists:customers,id',


            'payment_status' =>
            'required|in:pending,paid,partial',


            'items' =>
            'required|array|min:1',


            'items.*.medicine_id' =>
            'required|exists:medicines,id',


            'items.*.quantity' =>
            'required|integer|min:1'

        ]);





        return DB::transaction(function () use ($validated) {


            /*
             * Create sale
             *
             * Temporary invoice number
             * because database requires value
             */

           $sale = Sale::create([

            'invoice_number' => 'TEMP-'.uniqid(),

            'customer_id' => $validated['customer_id'],

            'total_amount' => 0,

            'paid_amount' => 0,

            'balance' => 0,

            'payment_status' => 'pending'

        ]);





            /*
             * Generate final invoice number
             */

            $sale->update([

                'invoice_number'=>
                'INV-'
                .
                str_pad(
                    $sale->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                )

            ]);






            $total = 0;





            foreach(
                $validated['items']
                as $item
            ){


                /*
                 * Lock medicine row
                 * to prevent selling same stock twice
                 */

                $medicine =
                Medicine::lockForUpdate()
                ->findOrFail(
                    $item['medicine_id']
                );





                if(
                    $medicine->quantity
                    <
                    $item['quantity']
                ){

                    throw new \Exception(

                        "Insufficient stock for "
                        .$medicine->name

                    );

                }





                $price =
                $medicine->selling_price;



                $subtotal =
                $price *
                $item['quantity'];





                SaleItem::create([


                    'sale_id'=>
                    $sale->id,


                    'medicine_id'=>
                    $medicine->id,


                    'quantity'=>
                    $item['quantity'],


                    'price'=>
                    $price,


                    'subtotal'=>
                    $subtotal


                ]);






                /*
                 * Reduce stock
                 */

                $medicine->decrement(

                    'quantity',

                    $item['quantity']

                );





                $total += $subtotal;


            }







            /*
             * Update final amount
             */

            $sale->update([

                'total_amount' => $total,

                'paid_amount' => 0,

                'balance' => $total,

                'payment_status' => 'pending'

            ]);




            return new SaleResource(

                $sale->fresh()
                ->load([
                    'customer',
                    'items.medicine'
                ])

            );



        });


    }








    public function show(Sale $sale)
    {

      return new SaleResource(

        $sale->load([
            'customer',
            'items.medicine',
            'payments'
        ])

    );

    }









    public function update(
        Request $request,
        Sale $sale
    )
    {


        $validated = $request->validate([

            'payment_status'=>
            'sometimes|in:pending,partial,paid'

        ]);



        $sale->update($validated);



        return new SaleResource(

            $sale->fresh()

        );


    }









    public function destroy(Sale $sale)
    {


        /*
         * Optional:
         * restore stock before deleting sale
         */

        foreach($sale->items as $item){


            Medicine::where(
                'id',
                $item->medicine_id
            )
            ->increment(
                'quantity',
                $item->quantity
            );


        }



        $sale->delete();



        return response()->json([

            'message'=>
            'Sale deleted successfully'

        ]);

    }


}
