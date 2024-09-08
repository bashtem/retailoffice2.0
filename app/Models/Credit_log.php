<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit_log extends Model
{
    public $fillable = ['credit_log_id', 'credit_order_id', 'user_id', 'credit_id', 'credit_log_status', 'old_credit', 'new_credit', 'credit_date', 'credit_time', 'created_at', 'updated_at'];
}
