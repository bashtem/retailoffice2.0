<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer_item extends Model
{
    //
    public function conversion(){
        return $this->belongsTo(Quantity_conversion::class,'conversion_id','conversion_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
}
