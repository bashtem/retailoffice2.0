<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $fillable = ['cus_id', 'registered_by_user_id', 'cus_name', 'cus_mobile', 'cus_mail', 'cus_address', 'payment_id', 'created_at', 'updated_at'];
    public function credit(){
        return $this->hasOne(Credit::class, "cus_id", 'cus_id');
    }

    public function discount(){
        return $this->hasOne(Discount::class, 'cus_id','cus_id');
    }

    public function payment(){
        return $this->belongsTo(Payment_type::class, 'payment_id', 'payment_id');
    }
    
    public function registeredBy(){
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function creditOrders(){
        return $this->hasMany(Credit_order::class, 'cus_id', 'cus_id');
    }

    public function discountItems(){
        return $this->hasMany(Discount_item::class, 'cus_id', 'cus_id');
    }

}
