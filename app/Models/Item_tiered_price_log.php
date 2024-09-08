<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_tiered_price_log extends Model
{
    protected $fillable = [
        "id",
        "user_id",
        "tiered_price_id",
        "store_id",
        "qty_id",
        "item_id",
        "old_qty",
        "new_qty",
        "old_price",
        "new_price",
        "date",
        "time",
        "created_at",
        "updated_at"
    ];
}
