<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_category extends Model
{
    public $timestamps = false;
    public $primaryKey = 'cat_id';

    public function items(){
       return $this->hasMany(Item::class,'category_id');
    }
}
