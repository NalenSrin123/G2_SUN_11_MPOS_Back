<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // public $timestamps = false;

    protected $fillable = [
        'order_id',
        'confirmed_by',
        'method',
        'status',
        'amount',
        'paid_at',
        'confirmed_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(Admin::class, 'confirmed_by');
    }

    public function receipt()
    {
        return $this->hasOne(Recript::class);
    }
}
