<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer_order extends Model
{
    public $fillable = ['user_id_transfer','transfer_date','transfer_time','transfer_status', 'transfer_id', 'created_at', 'updated_at'];

    public function transfer_items(){
        return $this->hasMany(Transfer_item::class, 'transfer_id','transfer_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id_transfer');
    }
}
