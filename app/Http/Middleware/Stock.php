<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\EnumController as Enum;
use Illuminate\Support\Facades\Session;

class Stock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $d = new Collection(Session::get('userRole'));
        $role =  $d['user_role']['role_level'];
        if($role == Enum::STOCK){
            
            return $next($request);
        }

        return redirect('/');
    }
}
