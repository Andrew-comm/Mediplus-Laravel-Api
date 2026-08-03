<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
protected $fillable = [

    'invoice_number',

    'customer_id',

    'total_amount',

    'paid_amount',

    'balance',

    'payment_status'

];

public function customer()
{

    return $this->belongsTo(
        Customer::class
    );

}



public function items()
{

    return $this->hasMany(
        SaleItem::class
    );

}

public function payments()
{
    return $this->hasMany(
        Payment::class
    );
}


}


