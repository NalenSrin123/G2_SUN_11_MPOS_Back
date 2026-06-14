<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueReport extends Model
{
    //

    protected $table = 'revenue_report';
    protected $fillable = [
        'admin_id',
        'type',
        'total_revenue',
        'total_orders',
        'report_date',
    ];
}
