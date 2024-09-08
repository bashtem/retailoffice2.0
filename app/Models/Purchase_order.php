<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_order extends Model
{
  public $primaryKey = 'purchase_id';
  public  $fillable = ["qty_id",
                "supplier_id",
                "user_id",
                "payment_id",
                "purchase_time",
                "purchase_date",
                "purchase_note",
                "created_at",
                "updated_at"];

  public function Purchase_order_item(){
          return $this->hasMany(Purchase_order_item::class,"purchase_id");
  }

  public function Purchase_excess_out(){
        return $this->hasMany(Purchase_excess_out::class,"purchase_id");
  }

  public function Qty(){
          return $this->belongsTo(Qty_type::class,"qty_id");
  }
  public function Supplier(){
          return $this->belongsTo(Supplier::class,"supplier_id");
  }
  public function user(){
          return $this->belongsTo(User::class, 'user_id');
  }
  public function payment(){
          return $this->belongsTo(Payment_type::class,'payment_id','payment_id');
  }
}
