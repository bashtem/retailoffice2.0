<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    public $fillable =['order_item_id', 'order_id', 'item_id', 'quantity', 'price', 'amount', 'vat', 'created_at', 'updated_at'];

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id','order_id');
    }
}
