<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SaleItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id'=>$this->id,


            'medicine'=>[

                'id'=>$this->medicine->id,

                'name'=>$this->medicine->name,

                'batch_number'=>
                $this->medicine->batch_number

            ],



            'quantity'=>
            $this->quantity,



            'price'=>
            $this->price,



            'subtotal'=>
            $this->subtotal


        ];

    }

}
