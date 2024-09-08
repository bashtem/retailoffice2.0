<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_confirm extends Model
{
    public $fillable =['confirm_id', 'order_id', 'user_id', 'confirm_date', 'confirm_time', 'created_at', 'updated_at'];
}
