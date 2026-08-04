<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Payments\Requests\StorePaymentRequest;

use App\Modules\Payments\Resources\PaymentResource;

use App\Modules\Payments\Models\Payment;

use App\Modules\Sales\Models\Sale;

use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    /**
     * Display all payments.
     */
    public function index()
    {

        $payments = Payment::with([
            'sale.customer',
            'sale.payments'
        ])
        ->latest()
        ->get();


        return PaymentResource::collection($payments);

    }





    /**
     * Store payment.
     */
    public function store(StorePaymentRequest $request)
    {

        $validated = $request->validated();


        return DB::transaction(function () use ($validated) {


            $sale = Sale::with([
                'payments',
                'customer'
            ])
            ->findOrFail(
                $validated['sale_id']
            );



            /*
             * Calculate current paid amount
             */
            $alreadyPaid =
            $sale->payments
            ->sum('amount');



            /*
             * Calculate remaining balance
             */
            $balance =
            $sale->total_amount - $alreadyPaid;



            /*
             * Prevent over payment
             */
            if(
                $validated['amount'] > $balance
            ){

                return response()->json([

                    'message'=>
                    'Payment exceeds outstanding balance.'

                ],422);

            }





            /*
             * Create payment
             */
            $payment = Payment::create([

                'sale_id'=>
                $sale->id,


                'amount'=>
                $validated['amount'],


                'payment_method'=>
                $validated['payment_method'],


                'payment_date'=>
                $validated['payment_date'],


                'reference_number'=>
                $validated['reference_number'] ?? null,


                'notes'=>
                $validated['notes'] ?? null

            ]);







            /*
             * Update invoice status
             */
            $this->updateSaleStatus($sale);






            return new PaymentResource(

                $payment
                ->fresh()
                ->load([

                    'sale.customer',
                    'sale.payments'

                ])

            );


        });

    }








    /**
     * Show payment.
     */
    public function show(Payment $payment)
    {

        return new PaymentResource(

            $payment->load([

                'sale.customer',
                'sale.payments'

            ])

        );

    }








    /**
     * Update payment.
     */
    public function update(
        StorePaymentRequest $request,
        Payment $payment
    )
    {

        $validated =
        $request->validated();



        return DB::transaction(function () use (
            $payment,
            $validated
        ) {



            $sale = Sale::with('payments')
            ->findOrFail(
                $payment->sale_id
            );




            /*
             * Exclude current payment
             */
            $alreadyPaid =
            $sale->payments
            ->where(
                'id',
                '!=',
                $payment->id
            )
            ->sum('amount');





            $balance =
            $sale->total_amount
            -
            $alreadyPaid;






            if(
                $validated['amount']
                >
                $balance
            ){

                return response()->json([

                    'message'=>
                    'Payment exceeds outstanding balance.'

                ],422);

            }






            $payment->update([

                'amount'=>
                $validated['amount'],


                'payment_method'=>
                $validated['payment_method'],


                'payment_date'=>
                $validated['payment_date'],


                'reference_number'=>
                $validated['reference_number'] ?? null,


                'notes'=>
                $validated['notes'] ?? null

            ]);





            $this->updateSaleStatus($sale);





            return new PaymentResource(

                $payment
                ->fresh()
                ->load([

                    'sale.customer',
                    'sale.payments'

                ])

            );



        });


    }









    /**
     * Delete payment.
     */
    public function destroy(
        Payment $payment
    )
    {


        return DB::transaction(function () use ($payment) {



            $sale =
            Sale::with('payments')
            ->findOrFail(
                $payment->sale_id
            );



            $payment->delete();




            $this->updateSaleStatus($sale);




            return response()->json([

                'message'=>
                'Payment deleted successfully.'

            ]);



        });


    }










    /**
     * Update invoice payment status.
     */
    private function updateSaleStatus(
        Sale $sale
    ): void
    {


        /*
         * Reload payments
         */
        $sale->load('payments');



        $totalPaid =
        $sale->payments
        ->sum('amount');




        if(
            $totalPaid <= 0
        ){

            $status =
            'pending';


        }
        elseif(
            $totalPaid < $sale->total_amount
        ){

            $status =
            'partial';


        }
        else{

            $status =
            'paid';

        }




        $sale->update([

            'payment_status'=>
            $status

        ]);


    }


}
