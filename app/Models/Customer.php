<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    public function memos()
    {
        return $this->hasMany(Memo::class, 'customer_id', 'customer_id');
    }

    public function ledger()
    {
        return $this->hasMany(CustomerLedger::class, 'customer_id', 'customer_id');
    }

    /** Current outstanding balance = dues recorded - payments received. */
    public function currentDue(): float
    {
        $due  = (float) $this->ledger()->where('entry_type', 'due')->sum('amount');
        $paid = (float) $this->ledger()->where('entry_type', 'payment')->sum('amount');
        return round($due - $paid, 2);
    }
}
