<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{

    protected $fillable = [

        'medicine_id',

        'type',

        'direction',

        'quantity',

        'reference',

        'remarks'

    ];



    public function medicine()
    {

        return $this->belongsTo(
            Medicine::class
        );

    }


}
