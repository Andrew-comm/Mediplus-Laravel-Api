<?php

namespace App\Modules\StockMovements\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class StockMovementResource extends JsonResource
{

   public function toArray(Request $request): array
{
    return [

        'id'=>$this->id,


        'medicine'=>[

            'id'=>$this->medicine->id,

            'name'=>$this->medicine->name,

            'batch_number'=>$this->medicine->batch_number

        ],


        'type'=>$this->type,


        'direction'=>$this->direction,


        'quantity'=>$this->quantity,


        'reference'=>$this->reference,


        'remarks'=>$this->remarks,


        'created_at'=>$this->created_at


    ];
}
}
