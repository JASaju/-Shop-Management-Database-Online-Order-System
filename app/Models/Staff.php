<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $primaryKey = 'staff_id';

    public $timestamps = false; // only created_at exists, no updated_at

    protected $fillable = [
        'shop_id',
        'name',
        'designation',
        'phone',
        'nid_no',
        'address',
        'salary',
        'join_date',
        'is_active',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'join_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function memos()
    {
        return $this->hasMany(Memo::class, 'staff_id', 'staff_id');
    }
}
