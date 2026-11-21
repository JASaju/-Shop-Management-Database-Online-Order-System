<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $primaryKey = 'purchase_id';

    public $timestamps = false; // only created_at exists, no updated_at

    // due_amount is a DB-generated column: total_amount - discount_amount - paid_amount.
    // Not in $fillable on purpose.

    protected $fillable = [
        'invoice_no',
        'shop_id',
        'company_id',
        'purchase_date',
        'due_date',
        'total_amount',
        'discount_amount',
        'paid_amount',
        'note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
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

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id', 'purchase_id');
    }

    public function payments()
    {
        return $this->hasMany(CompanyPayment::class, 'purchase_id', 'purchase_id');
    }
}
