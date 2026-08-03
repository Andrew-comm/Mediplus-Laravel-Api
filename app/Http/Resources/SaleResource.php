<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SaleResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        $paidAmount = $this->payments()
            ->sum('amount');


        return [

            'id'=>$this->id,


            'invoice_number'=>$this->invoice_number,


            'customer'=>[

                'id'=>$this->customer->id,

                'name'=>$this->customer->name,

                'phone'=>$this->customer->phone

            ],


            'items'=>
            SaleItemResource::collection(
                $this->items
            ),



            'total_amount'=>
            $this->total_amount,


            'paid_amount'=>
            $paidAmount,


            'balance'=>
            $this->total_amount - $paidAmount,



            'payment_status'=>
            $this->payment_status,



            'created_at'=>
            $this->created_at

        ];

    }

}
