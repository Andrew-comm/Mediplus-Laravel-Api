<?php

namespace App\Modules\Suppliers\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [

        'name',

        'email',

        'phone',

        'address'

    ];
}
