<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        return route('home');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.nav.login'), route('login'), LinkItem::STATUS_INACTIVE));
        return view('auth.login', compact('breadcrumbs'));
    }

    public function username()
    {
        $request = request();
        // return 'email';
        if ($request->input('phone_number', '')) {
            $usernameField = 'phone_number';
        } else {
            $usernameField = 'email';
        }
        return $usernameField;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $username = $request->input($this->username());
        if ($this->username() == 'phone_number') {
            $username = Helper::reformatPhone($username);
        }
        return [$this->username() => $username, 'password' => $request->input('password')];
    }

    protected function validateLogin(Request $request)
    {
        $validationFields = [
            'password' => 'required|string',
        ];
        $validationFields[$this->username()] = 'required|string';
        // if ($this->username() == 'phone_number') {
        //     $validationFields[$this->username()] = 'required|regex:/^998\d{9}$/';
        // }
        $request->validate($validationFields);
    }
}
