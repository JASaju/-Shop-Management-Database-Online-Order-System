<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $primaryKey = 'company_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $fillable = [
        'company_name',
        'contact_person',
        'address',
        'phone_1',
        'phone_2',
        'email',
        'trade_license_no',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'company_id', 'company_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'company_id', 'company_id');
    }

    public function payments()
    {
        return $this->hasMany(CompanyPayment::class, 'company_id', 'company_id');
    }

    public function instruments()
    {
        return $this->hasMany(ShopInstrument::class, 'company_id', 'company_id');
    }
}
