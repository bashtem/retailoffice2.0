<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_excess_out extends Model
{
    public function Purchase_order_item(){
        return $this->belongsTo(Purchase_order_item::class,"purchase_item_id");
    }
}
