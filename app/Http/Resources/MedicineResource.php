<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

        'id'=>$this->id,

        'name'=>$this->name,

        'category'=>$this->category,

        'batch_number'=>$this->batch_number,

        'expiry_date'=>$this->expiry_date
            ? $this->expiry_date->format('d/m/Y')
            : null,

        'buying_price'=>$this->buying_price,

        'selling_price'=>$this->selling_price,

        'quantity'=>$this->quantity,

        'supplier_id'=>$this->supplier_id,

        'supplier'=>$this->whenLoaded(
            'supplierData'
        ),

    ];
    }
}
