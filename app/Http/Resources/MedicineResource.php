<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id'=>$this->id,

            'name'=>$this->name,

            'category'=>$this->category,

            'batch_number'=>$this->batch_number,

            'expiry_date'=>$this->expiry_date
                ? $this->expiry_date->format('Y-m-d')
                : null,


            'buying_price'=>
                (float)$this->buying_price,


            'selling_price'=>
                (float)$this->selling_price,


            'quantity'=>
                (int)$this->quantity,


            'supplier_id'=>
                $this->supplier_id,


            'supplier'=>$this->whenLoaded(
                'supplierData',
                function(){

                    return [

                        'id'=>$this->supplierData->id,

                        'name'=>$this->supplierData->name

                    ];

                }
            ),


        ];
    }
}
