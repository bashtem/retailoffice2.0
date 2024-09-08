<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $fillable = ['order_id', 'merchant_id', 'store_id', 'order_no', 'cus_mobile', 'payment_id', 'qty_id', 'user_id', 'order_status', 'order_note', 'order_total_amount', 'cash_paid', 'order_total_qty', 'order_date', 'order_time', 'created_at', 'updated_at'];
    
    public function cus(){
        return $this->belongsTo(Customer::class,'cus_id','cus_id');
    }
    public function payment(){
        return $this->belongsTo(Payment_type::class,'payment_id','payment_id');
    }
    public function qty(){
        return $this->belongsTo(Qty_type::class,'qty_id','qty_id');
    }
    public function items(){
        return $this->hasMany(Order_item::class,'order_id','order_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function discount(){
        return $this->hasOne(Discount_log::class,'order_id','order_id');
    }
}
