<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class LoginController extends Controller  implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
        ];
    }


    public function username()
    {
        return 'username';
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $credentials['status'] = 'ACTIVE';

        if (Auth::attempt($credentials)) {
            if (Auth::user()->user_agent != $request->header('User-Agent') && Auth::user()->user_agent != null) {
                Auth::logout();
                return redirect()->back()->withErrors([$this->username() => 'User Logged In on another Browser']);
            }
            // Authentication passed...
            User::where('user_id', Auth::user()->user_id)->update(['user_agent' => $request->header('User-Agent')]);
            $userRole = User::where('user_id', Auth::user()->user_id)->with('userRole')->first();
            Session::put('userRole', $userRole);
            Session::put('password', $request->password);
            return redirect('/');
        } else {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'password'))
                ->withErrors([
                    $this->username() => trans('auth.failed')
                ]);
        }
    }
}
