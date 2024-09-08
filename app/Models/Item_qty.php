<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Item_qty extends Model
{
    use \Awobaz\Compoships\Compoships;
    
    public $fillable = ['id', 'merchant_id', 'store_id', 'qty_id', 'item_id', 'quantity', 'created_at', 'updated_at'];

    public function item_purchases(){
        return $this->hasMany(Purchase_order_item::class, "item_id");
    }
    public function item_qty_log(){
        return $this->hasMany(Item_qty_log::class, "item_id");
    }

    public function item(){
        return $this->belongsTo(Item::class, "item_id");
    }
    public function itemPrice(){
        return $this->belongsTo(Item_price::class, "id");
    }
    public function itemTieredPrice(){
        return $this->hasMany(Item_tiered_price::class, ['item_id', 'store_id', 'qty_id'], ['item_id', 'store_id', 'qty_id']);
    }
    public function qty_type(){
        return $this->belongsTo(Qty_type::class, "qty_id");
    }    
}
