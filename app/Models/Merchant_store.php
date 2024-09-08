<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant_store extends Model
{
    public $primaryKey = 'store_id';
    protected $guarded = [];

    public function merchant(){
        return $this->belongsTo(Merchant::class,'merchant_id');
    }
}
