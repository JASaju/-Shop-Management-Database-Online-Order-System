<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $primaryKey = 'memo_id';

    public $timestamps = false;

    protected $fillable = [
        'memo_no',
        'shop_id',
        'staff_id',
        'delivery_staff_id',
        'customer_id',
        'customer_name',
        'customer_address',
        'memo_date',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'due_amount',
        'amount_in_words',
    ];

    protected $casts = [
        'memo_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(Staff::class, 'delivery_staff_id', 'staff_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(MemoItem::class, 'memo_id', 'memo_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'memo_id', 'memo_id');
    }
}