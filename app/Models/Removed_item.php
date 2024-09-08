<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Removed_item extends Model
{
    public $fillable = ['removal_id', 'qty_id', 'item_id', 'user_id', 'note', 'removal_date', 'removal_time', 'quantity', 'created_at', 'updated_at'];
}
