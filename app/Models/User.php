<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;

class User extends Authenticatable
{
    use Notifiable, SanctumHasApiTokens ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','username','phone','role','status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $primaryKey= 'user_id';

    public function userRole(): BelongsTo {
        return $this->belongsTo(User_role::class, 'role');
    }

    public function findForPassport($username){
        return $this->where('username', $username)->first();
    }

    public function userStore(){
        return $this->belongsTo(Merchant_store::class, 'store_id');
    }
    
}
