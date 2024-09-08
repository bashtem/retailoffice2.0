<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock_movement extends Model
{
   protected $guarded = [];

   public function item(){
     return  $this->belongsTo(Item::class, "item_id");
   }

   public function qtyType(){
      return $this->belongsTo(Qty_type::class, "qty_id");
   }

   public function transferStore(){
      return $this->belongsTo(Merchant_store::class, "transferring_store_id");
   }

   public function receiveStore(){
      return $this->belongsTo(Merchant_store::class, "receiving_store_id");
   }

   public function user(){
      return $this->belongsTo(User::class, "user_id");
   }
}
