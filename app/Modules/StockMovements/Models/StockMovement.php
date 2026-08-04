<?php
namespace App\Modules\StockMovements\Models;

use App\Modules\Medicines\Models\Medicine;
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
