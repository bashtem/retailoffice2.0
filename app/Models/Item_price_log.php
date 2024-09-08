<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_price_log extends Model
{
    protected $fillable = [
        'id', 'user_id', 'store_id', 'item_id', 'qty_id', 'old_price', 'old_min_price', 'old_max_price', 'new_price', 'new_min_price', 'new_max_price', 'date', 'time', 'created_at', 'updated_at'
    ];
}
