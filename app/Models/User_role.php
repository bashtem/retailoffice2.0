<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_role extends Model
{
    public $primaryKey = 'role_id';
    public $timestamps = false;
    
    public function user(){
        return $this->hasMany(User::class);
    }
}
