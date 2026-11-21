<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $primaryKey = 'medicine_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'company_id',
        'unit',
        'default_price',
        'default_discount_percent',
        'reorder_level',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    public function batches()
    {
        return $this->hasMany(\Illuminate\Database\Eloquent\Model::class);
    }

    public function memoItems()
    {
        return $this->hasMany(MemoItem::class, 'medicine_id', 'medicine_id');
    }

}
