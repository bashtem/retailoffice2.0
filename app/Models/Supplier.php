<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public $primaryKey = "sup_id";

    public function Purchase_order(){
        return $this->hasMany(Purchase_order::class, "supplier_id");
    }

    public function items(){
        return $this->hasManyThrough(Item::class, Purchase_order_item::class, 'supplier_id', 'item_id', 'sup_id', 'purchase_id');
    }
}