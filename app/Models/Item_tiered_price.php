<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_tiered_price extends Model
{
    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        "id",
        "merchant_id",
        "store_id",
        "qty_id",
        "item_id",
        "qty",
        "price",
        "created_at",
        "updated_at"
    ];
}
