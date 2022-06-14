<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Store;
use App\Models\UserStore;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{

   use RegistersUsers;


    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    {
        $this->middleware('guest');
    }

    // public function create()
    // {
    //     return view('auth.register');
    // }



     public function showRegistrationForm($lang = 'en')
    {
        if($lang == '')
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }
        \App::setLocale($lang);
         if(Utility::getValByName('signup_button') == 'on'){
          return view('auth.register', compact('lang'));   
         }else{
            return abort('404', 'Page not found');
         }
        
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
            'store_name'=>'required|string|max:255',
                         
                        
        ]);
        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }else{
            $validation = [];
        }
        $this->validate($request, $validation);
        $settings = Utility::settings();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'Owner',
            'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
            'avatar' => 'avatar.png',
            'plan' => Plan::first()->id,
            'created_by' => 1,
        ]);



       $objStore = Store::create(
            [
                'created_by' => $user->id,
                'name' =>  $request->store_name,
                'email' => $request->email,
                'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo.png',
                'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                'currency_code' => !empty($settings->currency) ? $settings->currency : 'USD',
                'paypal_mode' => 'sandbox',
            ]
        );

        $objStore->enable_storelink = 'on';
        $objStore->store_theme      = 'style-grey-body.css';
        $objStore->content          = 'Hi,Welcome to {store_name},Your order is confirmed & your order no. is {order_no}Your order detail is:Name : {customer_name} Address : {billing_address} , {shipping_address}
                                            ~~~~~~~~~~~~~~~~
                                            {item_variable}
                                            ~~~~~~~~~~~~~~~~
                                            Qty Total : {qty_total}
                                            Sub Total : {sub_total}
                                            Discount Price : {discount_amount}
                                            Shipping Price : {shipping_amount}
                                            Tax : {item_tax}
                                            Total : {item_total}
                                            ~~~~~~~~~~~~~~~~~~
                                            To collect the order you need to show the receipt at the counter.
                                            Thanks {store_name}';
        $objStore->item_variable    = '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}';
        $objStore->save();

        $user ->current_store = $objStore->id;
        $user ->save();
        UserStore::create(
            [
                'user_id' => $user ->id,
                'store_id' => $objStore->id,
                'permission' => 'Owner',
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}




