<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\Client;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $client = Client::all();
        return view('home',compact('client'));
    }

    public function differentAccount(Request $request)
    {
        Auth::logout();
        Session::put('url.intended', $request->current_url);
        return redirect("login");

    }

    public function resetAuth(array $guards = null)
    {
        $guards = $guards ?: array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            $guard = $this->app['auth']->guard($guard);

            if ($guard instanceof \Illuminate\Auth\SessionGuard) {
                $guard->logout();
            }
        }

        $protectedProperty = new \ReflectionProperty($this->app['auth'], 'guards');
        $protectedProperty->setAccessible(true);
        $protectedProperty->setValue($this->app['auth'], []);

        $user = auth()->user();
        if ($user) {
            $user->tokens()->each(function ($token) {
                $token->revoke();
            });
        }
        
        return redirect('http://laravel_sso_client.test/logout');
    }
}
