<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{

    protected $fillable = [
        'name',
        'category',
        'batch_number',
        'expiry_date',
        'buying_price',
        'selling_price',
        'quantity',
        'supplier'

    ];

    protected $casts = [

        'expiry_date'=>'date',

        'buying_price'=>'decimal:2',

        'selling_price'=>'decimal:2',

        'quantity'=>'integer'

    ];



}

