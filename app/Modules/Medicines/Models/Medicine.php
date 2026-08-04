<?php

namespace App\Modules\Medicines\Models;
use App\Modules\Sales\Models\SaleItem;
use App\Modules\StockMovements\Models\StockMovement;
use App\Modules\Suppliers\Models\Supplier;
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
        'supplier_id'

    ];

    protected $casts = [

        'expiry_date'=>'date:d/m/Y',

        'buying_price'=>'decimal:2',

        'selling_price'=>'decimal:2',

        'quantity'=>'integer',
        'supplier_id' => 'integer'

    ];

   public function supplierData()
    {
        return $this->belongsTo(
            Supplier::class,
            'supplier_id'
        );
    }

    public function saleItems()
    {

        return $this->hasMany(
            SaleItem::class
        );

    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }



}

