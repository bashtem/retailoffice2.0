<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_price extends Model
{
    public $guarded = [];

    public function qty_type(){
        return $this->belongsTo(Qty_type::class,"qty_id");
    }
}
