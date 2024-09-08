<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $fillable = ['discount_credit', 'updated_at','discount_id', 'cus_mobile', 'created_at'];
}
