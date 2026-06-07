<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    public $timestamps = false;

    protected $fillable = [
        'table_number',
        'qr_code',
        'status',
    ];

    protected $casts = [
        'table_number' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
