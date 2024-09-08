<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qty_type extends Model
{
    //
    public $timestamps = false;
    public $primaryKey = 'qty_id';

    public function order(){
        return $this->hasMany(Order::class, "qty_id");
     }
}
