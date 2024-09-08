<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction_type extends Model
{
    protected $fillable=["trans_id","trans_desc"];
    public $timestamps = false;
}
