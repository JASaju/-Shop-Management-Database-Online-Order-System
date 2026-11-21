<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    protected $table = 'order_requests';

    protected $primaryKey = 'order_request_id';

    public $timestamps = false; // only created_at exists

    protected $fillable = [
        'customer_name',
        'phone',
        'items',        // JSON: [{medicine_id, name, unit, price, qty, line_total}]
        'total_amount',
        'status',       // pending | called | confirmed | cancelled
        'note',
        'shop_id',
        'delivery_staff_id',
        'memo_id',
    ];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function deliveryStaff()
    {
        return $this->belongsTo(Staff::class, 'delivery_staff_id', 'staff_id');
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'memo_id', 'memo_id');
    }
}