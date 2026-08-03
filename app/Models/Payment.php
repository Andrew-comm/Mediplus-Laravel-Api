<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [

        'sale_id',

        'amount',

        'payment_method',

        'reference_number',

        'notes',

        'payment_date'

        ];
    public function sale()
    {
        return $this->belongsTo(
            Sale::class
        );
    }
}
