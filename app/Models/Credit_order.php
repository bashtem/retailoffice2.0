<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit_order extends Model
{
    public $fillable = ['credit_order_id','order_id', 'credit_id', 'cus_mobile', 'credit_order_status', 'date_paid', 'time_paid', 'created_at', 'updated_at'];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
