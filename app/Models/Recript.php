<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recript extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_id',
        'issued_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
