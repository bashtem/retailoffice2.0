<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $primaryKey= 'item_id';
    protected $fillable = [
        'item_id', 'item_name', 'item_desc', 'category_id', 'default_qty_id', 'min_stock_level', 'manufacturer', 'reorder_level', 'created_at', 'updated_at'
    ];
    
    public function item_category(){
        return $this->belongsTo(Item_category::class, 'category_id');
    }

    public function item_purchases(){
        return $this->hasMany(Purchase_order_item::class);
    }

    public function item_qty(){
        return $this->hasMany(Item_qty::class,"item_id");
    }

    public function item_price(){
        return $this->hasMany(Item_price::class,"item_id");
    }

    public function orderItems(){
        return $this->hasMany(Order_item::class, 'item_id');
    }

    public function default_unit(){
        return $this->belongsTo(Qty_type::class, "default_qty_id");
    }

    public function conversion(){
        return $this->hasOne(Quantity_conversion::class, "item_id");
    }
}
