<?php

namespace App\Http\Controllers;


use App\Models\Coupon;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Session;
use Stripe;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductVariantOption;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchasedProducts;
use Illuminate\Http\RedirectResponse;
use App\Models\ProductCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class StripePaymentController extends Controller
{
    public $settings;
     public $currancy;
    public $currancy_symbol;

    public $stripe_secret;
    public $stripe_key;
    public $stripe_webhook_secret;

    public function index()
    {
        $objUser = \Auth::user();
        if($objUser->type == 'super admin')
        {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->get();
        }
        else
        {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
        }

        return view('order.index', compact('orders'));
    }

    public function stripe($code)
    {
        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
        $plan    = Plan::find($plan_id);
        if($plan)
        {
            $admin_payments_details = Utility::getAdminPaymentSetting();

            return view('plans/stripe', compact('plan', 'admin_payments_details'));
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function stripePost(Request $request, $slug)
    {
        if(Utility::CustomerAuthCheck($slug) == false)
        {

            return redirect()->back()->with('error', __('You need to login'));
        }
        else
        {
            $store = Store::where('slug', $slug)->first();
            $shipping = Shipping::where('store_id', $store->id)->first();
            if(!empty($shipping) && $store->enable_shipping == 'on')
            {
                if($request->shipping_price == '0.00')
                {
                    return redirect()->route('store.slug',$slug)->with('error', __('Please select shipping.'));
                }
            }
            if(empty($store))
            {
                return redirect()->route('store.slug',$slug)->with('error', __('Store not available.'));
            }
            $validator = \Validator::make(

                $request->all(), [
                                   'name' => 'required|max:120',
                                   'phone' => 'required',
                                   'billing_address' => 'required',
                               ]
            );
            if($validator->fails())
            {
                return redirect()->route('store.slug',$slug)->with('error', __('All field is required.'));
            }
            $userdetail = new UserDetail();

            $userdetail['store_id'] = $store->id;
            $userdetail['name']     = $request->name;
            $userdetail['email']    = $request->email;
            $userdetail['phone']    = $request->phone;

            $userdetail['custom_field_title_1'] = $request->custom_field_title_1;
            $userdetail['custom_field_title_2'] = $request->custom_field_title_2;
            $userdetail['custom_field_title_3'] = $request->custom_field_title_3;
            $userdetail['custom_field_title_4'] = $request->custom_field_title_4;


            $userdetail['billing_address']  = $request->billing_address;
            $userdetail['shipping_address'] = !empty($request->shipping_address) ? $request->shipping_address : '-';
            $userdetail['special_instruct'] = $request->special_instruct;
            $userdetail->save();
            $userdetail->id;

            $cart     = session()->get($slug);
            $products = $cart;
            $order_id = $request['order_id'];
            if(empty($cart))
            {
                return redirect()->route('store.slug',$slug)->with('error', __('Please add to product into cart.'));
            }
            $cust_details = [
                    "id" => $userdetail->id,
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                    "custom_field_title_1" => $request->custom_field_title_1,
                    "custom_field_title_2" => $request->custom_field_title_2,
                    "custom_field_title_3" => $request->custom_field_title_3,
                    "custom_field_title_4" => $request->custom_field_title_4,
                    "billing_address" => $request->billing_address,
                    "shipping_address" => $request->shipping_address,
                    "special_instruct" => $request->special_instruct,
                ];
            if(!empty($request->coupon_id))
            {
                $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
            }
            else
            {
                $coupon = '';
            }
            //dd($coupon);
            $store        = Store::where('slug', $slug)->first();
            $user_details = $cust_details;

            $store_payment_setting = Utility::getPaymentSetting($store->id);

            $objUser = \Auth::user();

            $total        = 0;
            $sub_tax      = 0;
            $sub_total    = 0;
            $total_tax    = 0;
            $product_name = [];
            $product_id   = [];
            $totalprice   = 0;
            $tax_name     = [];

            foreach($products['products'] as $key => $product)
            {
                if($product['variant_id'] == 0)
                {
                    $new_qty                = $product['originalquantity'] - $product['quantity'];
                    $product_edit           = Product::find($product['product_id']);
                    $product_edit->quantity = $new_qty;
                    $product_edit->save();

                    $tax_price = 0;
                    if(!empty($product['tax']))
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {
                            $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                        }
                    }
                    $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                    $product_name[] = $product['product_name'];
                    $product_id[]   = $product['id'];
                }
                elseif($product['variant_id'] != 0)
                {
                    $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                    $product_variant           = ProductVariantOption::find($product['variant_id']);
                    $product_variant->quantity = $new_qty;
                    $product_variant->save();

                    $tax_price = 0;
                    if(!empty($product['tax']))
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {
                            $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                        }
                    }
                    $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                    $product_name[] = $product['product_name'] . ' - ' . $product['variant_name'];
                    $product_id[]   = $product['id'];
                }
            }

            $coupon_id = null;
            $price     = $total + $total_tax;
            if($products)
            {
                if(!empty($request->shipping_id))
                {
                    $shipping = Shipping::find($request->shipping_id);
                    if(!empty($shipping))
                    {
                        $totalprice     = $totalprice + $shipping->price;
                        $shipping_name  = $shipping->name;
                        $shipping_price = $shipping->price;
                        $shipping_data  = json_encode(
                            [
                                'shipping_name' => $shipping_name,
                                'shipping_price' => $shipping_price,
                                'location_id' => $shipping->location_id,
                            ]
                        );
                    }
                }
                else
                {
                    $shipping_data = '';
                }

                $cart['cust_details'] = $cust_details;
                $cart['shipping_data'] = $shipping_data;
                $cart['product_id'] = $product_id;
                $cart['all_products'] = $products;


                try
                {

                    if($coupon != "")
                    {
                        if($coupon['enable_flat'] == 'off')
                        {
                            $discount_value = ($totalprice / 100) * $coupon['discount'];
                            $totalprice          = $totalprice - $discount_value;
                        }
                        else
                        {
                            $discount_value = $coupon['flat_discount'];
                            $totalprice          = $totalprice - $discount_value;
                        }

                    }

                    $price = number_format($total, 2);

                    //$shipping = Shipping::find($cart['shipping']['shipping_id']);
                    
                    

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
                    if($totalprice > 0.0)
                    {
                        $l_name = $store->name;
                        $stripe_formatted_price = in_array(
                                $this->currancy, [
                                                   'MGA',
                                                   'BIF',
                                                   'CLP',
                                                   'PYG',
                                                   'DJF',
                                                   'RWF',
                                                   'GNF',
                                                   'UGX',
                                                   'JPY',
                                                   'VND',
                                                   'VUV',
                                                   'XAF',
                                                   'KMF',
                                                   'KRW',
                                                   'XOF',
                                                   'XPF',
                                               ]
                            ) ? number_format($totalprice, 2, '.', '') : number_format($totalprice, 2, '.', '') * 100;

                            $return_url_parameters = function ($return_type){
                                return '&return_type=' . $return_type . '&payment_processor=stripe';
                            };
                        $cart['totalprice'] = $totalprice;
                        $cart['coupon_id'] = $request->coupon_id;  
                        $cart['coupon_json'] = json_encode($coupon);
                        $cart['dicount_price'] = $request->dicount_price;
                        $cart['currency_code'] = $store->currency_code;
                        $cart['user_id'] = $store['id'];
                        session()->put($slug, $cart);


                        //dd($stripe_formatted_price);    
                        Stripe\Stripe::setApiKey($store_payment_setting['stripe_secret']);
                        $data = \Stripe\Checkout\Session::create(
                                [
                                    'payment_method_types' => ['card'],
                                    'line_items' => [
                                        [
                                            'name' => $l_name,
                                            'description' => " Stripe payment of order - " . $orderID,
                                            'amount' => $stripe_formatted_price,
                                            'currency' => $store->currency_code,
                                            'quantity' => 1,
                                        ],
                                    ],
                                    'metadata' => [
                                        'order_id' => $orderID,
                                    ],
                                    'success_url' => route(
                                        'store.payment.stripe', [
                                                'slug' => $slug,
                                                $return_url_parameters('success'),
                                            ]
                                    ),
                                    'cancel_url' => route(
                                        'store.payment.stripe', [
                                                $slug,
                                                $return_url_parameters('cancel'),
                                            ]
                                    ),
                                ]
                            );
                        $data = $data ?? false;
                        try{
                                return new RedirectResponse($data->url);
                        }catch(\Exception $e)
                        {
                            return redirect()->route('store.slug',$slug)->with('error', __('Transaction has been failed!'));
                        }
                    }
                    else
                    {
                        $data['amount_refunded'] = 0;
                        $data['failure_code']    = '';
                        $data['paid']            = 1;
                        $data['captured']        = 1;
                        $data['status']          = 'succeeded';
                    }
                    
                    if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                    {   
                        $customer               = Auth::guard('customers')->user();
                        $order                  = new Order();
                        $order->order_id        = '#' . time();
                        $order->name            = $cust_details['name'];
                        $order->email           = $cust_details['email'];
                        $order->card_number     = '';
                        $order->card_exp_month  = '';
                        $order->card_exp_year   = '';
                        $order->status          = 'pending';
                        $order->phone           = $request->phone;
                        $order->user_address_id = $cust_details['id'];
                        $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
                        $order->product_id      = implode(',', $product_id);
                        $order->price           = $price;
                        $order->coupon          = $request->coupon_id;
                        $order->coupon_json     = json_encode($coupon);
                        $order->discount_price  = !empty($request->dicount_price) ? $request->dicount_price : '0';
                        $order->coupon          = $request->coupon_id;
                        $order->product         = json_encode($products);
                        $order->price_currency  = $store->currency_code;
                        $order->txn_id          = '';
                        $order->payment_type    = __('STRIPE');
                        $order->payment_status  = 'approved';
                        $order->receipt         = '';
                        $order->user_id         = $store['id'];
                        $order->customer_id     = $customer->id;
                        $order->save();


                        foreach($products['products'] as $k_pro => $product_id)
                        {
                            
                            $purchased_product = new PurchasedProducts();
                            $purchased_product->product_id  = $product_id['product_id'];
                            $purchased_product->customer_id = $customer->id;
                            $purchased_product->order_id   = $order->id;
                            $purchased_product->save();
                        }
                        $order_email = $order->email;
                    $order_id = Crypt::encrypt($order->id);
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Transaction has been failed.'));
                    
                    }
                }
                catch(\Exception $e)
                {
                    return redirect()->back()->with('error', __($e->getMessage()));
                }
            }
            else
            {
                return redirect()->route('store.slug',$slug)->with('error', __('Failed'));
            }
        }
    }
    public function getProductStatus(Request $request){
        $slug = $request->slug;
        $store        = Store::where('slug', $slug)->first();
        Session::forget('stripe_session');
        try
        {
            if($request->return_type == 'success')
            {
                $cart     = session()->get($request->slug);
                $cust_details = $cart['cust_details'];
                $shipping_data = $cart['shipping_data'];
                $product_id = $cart['product_id'];
                $totalprice = $cart['totalprice'];
                $coupon = $cart['coupon_json'];
                $products = $cart['all_products'];

                $customer               = Auth::guard('customers')->user();
                $order                  = new Order();
                $order->order_id        = '#' . time();
                $order->name            = $cust_details['name'];
                $order->email           = $cust_details['email'];
                $order->card_number     = '';
                $order->card_exp_month  = '';
                $order->card_exp_year   = '';
                $order->status          = 'pending';
                $order->phone           = $cust_details['phone'];
                $order->user_address_id = $cust_details['id'];
                $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
                $order->product_id      = implode(',', $product_id);
                $order->price           = $totalprice;
                $order->coupon          = $cart['coupon_id'];
                $order->coupon_json     = $coupon;
                $order->discount_price  = !empty($cart['dicount_price']) ? $cart['dicount_price'] : '0';
                $order->coupon          = $cart['coupon_id'];
                $order->product         = json_encode($products);
                $order->price_currency  = $cart['currency_code'];
                $order->txn_id          = '';
                $order->payment_type    = __('STRIPE');
                $order->payment_status  = 'approved';
                $order->receipt         = '';
                $order->user_id         = $cart['user_id'];
                $order->customer_id     = $customer->id;
                $order->save();

               
                foreach($products['products'] as $k_pro => $product_id)
                {
                    
                    $purchased_product = new PurchasedProducts();
                    $purchased_product->product_id  = $product_id['product_id'];
                    $purchased_product->customer_id = $customer->id;
                    $purchased_product->order_id   = $order->id;
                    $purchased_product->save();
                }

                $order_email = $order->email;
                $owner=User::find($store->created_by);
                $owner_email=$owner->email;
                $order_id = Crypt::encrypt($order->id);
                if(isset($store->mail_driver) && !empty($store->mail_driver))
                {
                    $dArr = [
                        'order_name' => $order->name,
                    ];
                    $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);
                    $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);
                }
                if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }
                session()->forget($request->slug);
                return redirect()->route(
                        'store-complete.complete', [
                                                     $request->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));

            }
            else
            {

                return redirect()->route('store.slug',$request->slug)->with('error', __('Transaction has been failed!'));
            }
        }
        catch(\Exception $exception)
        {
            return redirect()->route('store.slug',$request->slug)->with('error', $exception->getMessage());

        }
        

    }
    public function addPayment(Request $request)
    {
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan    = Plan::find($planID);

        if($plan)
        {
            try
            {
                $price = $plan->price;
                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if($price > 0.0)
                {
                    Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => env('CURRENCY'),
                            "source" => $request->stripeToken,
                            "description" => " Plan - " . $plan->name,
                            "metadata" => ["order_id" => $orderID],
                        ]
                    );
                }
                else
                {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';


                }
                if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
                {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if(!empty($shipping))
                    {
                        $shipping_name  = $shipping->name;
                        $shipping_price = $shipping->price;

                        $shipping_data = json_encode(
                            [
                                'shipping_name' => $shipping_name,
                                'shipping_price' => $shipping_price,
                                'location_id' => $cart['shipping']['location_id'],
                            ]
                        );
                    }
                    else
                    {
                        $shipping_data = '';
                    }
                }

                if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                {

                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'shipping_data' => !empty($shipping_data) ? $shipping_data : '',
                            'price' => $price,
                            'price_currency' => env('CURRENCY'),
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $objUser->id,
                        ]
                    );

                    if(!empty($request->coupon))
                    {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $objUser->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();

                        $usedCoupun = $coupons->used_coupon();
                        if($coupons->limit <= $usedCoupun)
                        {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }

                    }
                    if($data['status'] == 'succeeded')
                    {
                        $assignPlan = $objUser->assignPlan($plan->id);
                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
                        }
                        else
                        {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __('Your payment has failed.'));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
