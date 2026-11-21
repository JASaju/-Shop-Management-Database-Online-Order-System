<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemoItem extends Model
{
    protected $primaryKey = 'item_id';

    public $timestamps = false;

    // total_price is a DB-generated (stored) column: quantity * unit_price
    // Don't put it in $fillable — MySQL will reject any insert/update that touches it.
    protected $fillable = [
        'memo_id',
        'medicine_id',
        'batch_id',
        'medicine_name',
        'quantity',
        'unit_price',
        'discount_percent',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'memo_id', 'memo_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    public function batch()
    {
        return $this->belongsTo(PurchaseItem::class, 'batch_id', 'batch_id');
    }
}
