<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $primaryKey = 'shop_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'shop_name',
        'tagline',
        'owner_name',
        'address',
        'phone_1',
        'phone_2',
    ];

    public function memos()
    {
        return $this->hasMany(Memo::class, 'shop_id', 'shop_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'shop_id', 'shop_id');
    }

    public function instruments()
    {
        return $this->hasMany(ShopInstrument::class, 'shop_id', 'shop_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'shop_id', 'shop_id');
    }
}