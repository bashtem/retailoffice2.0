<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount_item extends Model
{
    public $fillable = ['discount_item_id', 'cus_id', 'user_id_enabled', 'user_id_disabled', 'item_id', 'qty_id', 'item_qty', 'discount_amount', 'enabled_date', 'enabled_time', 'disabled_date', 'disabled_time', 'expiry_date', 'created_at', 'updated_at'];

    public function item(){
        return $this->belongsTo(Item::class, "item_id");
    }

    public function unit(){
        return $this->belongsTo(Qty_type::class, "qty_id");
    }
}
