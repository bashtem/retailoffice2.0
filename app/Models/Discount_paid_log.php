<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount_paid_log extends Model
{
    public $fillable = ['discount_paid_id', 'cus_id', 'user_id', 'paid_amount', 'date_paid', 'time_paid', 'created_at', 'updated_at'];
}
