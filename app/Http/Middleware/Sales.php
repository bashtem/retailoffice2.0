<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\EnumController as Enum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Sales
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session = new Collection(Session::get('userRole'));
        $role =  $session['user_role']['role_level'];
        if($role == Enum::SALES){
            
            return $next($request);
        }

        return redirect('/cat');
    }
}