<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */


     use AuthenticatesUsers;

   protected $redirectTo = RouteServiceProvider::HOME;



    public function __construct()
    {
        // if(!file_exists(storage_path() . "/installed"))
        // {
        //     header('location:install');
        //     die;
        // }
        // $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {

    }


    public function create()
    {
        // return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation = [];
        }
        $this->validate($request, $validation);
        $request->authenticate();

        $request->session()->regenerate();
        $user=\Auth::user();
        if($user->type == 'Owner')
        {
            $store=Store::where('created_by',$user->creatorId())->first();

            if(isset($store->is_store_enabled)&& $store->is_store_enabled==0)
            {

                auth()->logout();
            }

            $plan=Plan::find($user->plan);
            if($plan)
            {
                if($plan->duration != 'Unlimited')
                {
                    $datetime1=new \Datetime($user->plan_expire_date);
                    $datetime2=new \Datetime(date('Y-m-d'));
                    $interval =$datetime2->diff($datetime1);
                    $days=$interval->format('%r%a');
                    if($days <= 0)
                    {
                        $user->assignPlan(1);

                        return redirect()->intended(RouteServiceProvider::HOME)->with('error',__('Your Plan is expired.'));
                    }
                }
               
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }



 public function showLoginForm($lang = '')
    {
        if($lang == '')
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }

    public function showLinkRequestForm($lang = '')
    {

        if($lang == '')
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        return view('auth.forgot-password', compact('lang'));
    }




}
