<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPayment extends Model
{
    protected $table = 'company_payments';

    protected $primaryKey = 'payment_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $fillable = [
        'company_id',
        'purchase_id',
        'payment_date',
        'amount',
        'method',
        'reference_no',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'purchase_id');
    }
}
