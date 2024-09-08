<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    public $primaryKey = 'merchant_id';

    public function stores(){
        return $this->hasMany(Merchant_store::class,'merchant_id');
    }
}
