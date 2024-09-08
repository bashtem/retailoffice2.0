<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    public $fillable = ['credit_id', 'cus_mobile', 'available_credit', 'out_credit', 'created_at', 'updated_at'];
}
