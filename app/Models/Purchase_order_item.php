<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_order_item extends Model
{
   public $primaryKey ="purchase_item_id";
    public $fillable = [ 'purchase_item_id',
         'purchase_id', 
         'item_id', 
         'purchase_qty',
          'purchase_price', 
          'confirm_user_id', 
        'purchase_status',
           'confirm_date',
        'confirm_time',
           'created_at',
           'updated_at'
    ];

    public function items(){
       return $this->belongsTo(Item::class,"item_id");
    }
    public function Purchase_order(){
       return $this->belongsTo(Purchase_order::class,"purchase_id");
    }
    public function Purchase_excess_out(){
      return $this->hasOne(Purchase_excess_out::class,"purchase_id");
}
}
