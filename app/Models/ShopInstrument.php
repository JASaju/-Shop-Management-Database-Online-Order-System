<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopInstrument extends Model
{
    protected $table = 'shop_instruments';

    protected $primaryKey = 'instrument_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $fillable = [
        'shop_id',
        'instrument_name',
        'brand_model',
        'serial_no',
        'quantity',
        'purchase_date',
        'purchase_price',
        'company_id',
        'warranty_until',
        'condition_status',
        'note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_until' => 'date',
        'purchase_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
