<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    protected $table = 'customer_ledger';

    protected $primaryKey = 'ledger_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $fillable = [
        'shop_id',
        'customer_id',
        'memo_id',
        'entry_date',
        'entry_type',
        'amount',
        'method',
        'note',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'memo_id', 'memo_id');
    }
}
