<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quantity_conversion extends Model
{
    public $primaryKey='conversion_id';
    protected $guarded = [];

    public function items(){
        return  $this->belongsTo(Item::class, "item_id");
    }
    public function srcQtyType(){
        return  $this->belongsTo(Qty_type::class, "initial_qty_id");
    }
    public function cnvQtyType(){
        return  $this->belongsTo(Qty_type::class, "converted_qty_id");
    }
}
