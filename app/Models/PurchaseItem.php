<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table = 'purchase_items';

    protected $primaryKey = 'batch_id';

    public $timestamps = false; // only created_at exists, no updated_at

    // total_price is DB-generated: quantity * purchase_price - discount_amount. Not in $fillable.

    protected $fillable = [
        'purchase_id',
        'medicine_id',
        'batch_no',
        'mfg_date',
        'expiry_date',
        'quantity',
        'quantity_remaining',
        'purchase_price',
        'mrp',
        'discount_percent',
        'discount_amount',
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'decimal:2',
        'quantity_remaining' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function memoItems()
    {
        return $this->hasMany(MemoItem::class, 'batch_id', 'batch_id');
    }
}
