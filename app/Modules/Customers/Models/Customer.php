<?php

namespace App\Modules\Customers\Models;

use App\Modules\Sales\Models\Sale;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
     protected $fillable = [

        'name',

        'phone',

        'email',

        'address'

    ];

    public function sales()
    {

        return $this->hasMany(
            Sale::class
        );

    }
}
