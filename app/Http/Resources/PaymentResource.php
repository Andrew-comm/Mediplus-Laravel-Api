<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PaymentResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id'=>$this->id,


            'amount'=>$this->amount,


            'payment_method'=>$this->payment_method,


            'payment_date'=>$this->payment_date,


            'reference_number'=>$this->reference_number,


            'notes'=>$this->notes,



            'sale'=>[

                'id'=>$this->sale->id,


                'invoice_number'=>
                $this->sale->invoice_number,


                'customer'=>[

                    'id'=>
                    $this->sale->customer->id,


                    'name'=>
                    $this->sale->customer->name,


                    'phone'=>
                    $this->sale->customer->phone

                ],



                'total_amount'=>
                $this->sale->total_amount,


                'paid_amount'=>
                $this->sale->payments->sum('amount'),



                'balance'=>
                $this->sale->total_amount -
                $this->sale->payments->sum('amount')


            ]

        ];

    }

}
