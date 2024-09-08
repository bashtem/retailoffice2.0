<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount_log extends Model
{
    public $fillable = ['discount_log_id','order_id','total_discount','discount_status', 'date', 'time', 'created_at', 'updated_at'];
}
