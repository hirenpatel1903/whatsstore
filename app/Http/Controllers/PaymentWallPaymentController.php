<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\Store;
use App\Models\UserDetail;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\Order;
use App\Models\Shipping;


class PaymentWallPaymentController extends Controller
{
    public function index(Request $request){
        $data = $request->all();
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        return view('plans.paymentwall',compact('data','admin_payment_setting'));
    }
    public function paymenterror(Request $request,$flag){
        if($flag == 1){
            return redirect()->route("plans.index")->with('error', __('Transaction has been Successfull! '));
        }else{
                return redirect()->route("plans.index")->with('error', __('Transaction has been failed! '));
        }
    }
   public function planPayWithPaymentwall(Request $request,$plan_id)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan               = Plan::find($plan_id);
        if($plan)
        {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified
                
                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => $admin_payment_setting['paymentwall_private_key']
                ));

                $parameters = $_POST;
                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $plan->price,
                    'currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();
                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                        if ($charge->isCaptured()) {
                            if(isset($request->coupon) && $request->coupon != '' && $request->coupon != null)
                    {
                        $coupons = Coupon::where('code',$request->coupon);
                        if(!empty($coupons))
                        {
                            $userCoupon         = new UserCoupon();
                            $userCoupon->user   = $user->id;
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
                    }
                    $planorder                 = new PlanOrder();
                    $planorder->order_id       = $orderID;
                    $planorder->name           = $user->name;
                    $planorder->card_number    = '';
                    $planorder->card_exp_month = '';
                    $planorder->card_exp_year  = '';
                    $planorder->plan_name      = $plan->name;
                    $planorder->plan_id        = $plan->id;
                    $planorder->price          = $plan->price;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = '';
                    $planorder->payment_type   = __('Paymentwall');
                    $planorder->payment_status = 'success';
                    $planorder->receipt        = '';
                    $planorder->user_id        = $user->id;
                    $planorder->store_id       = $store_id;
                    $planorder->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    if($assignPlan['is_success'])
                    {
                        $res['flag'] = 1;
                        return $res;
                    }
                    else
                    {


                        $res['flag'] = 2;
                        return $res;
                    }

                }
                elseif($charge->isUnderReview()) {
                        $res['flag'] = 2;
                        return $res;
                    }
                }else {
                   $res['flag'] = 2;
                    return $res;
                }
        }
        else
        {
            $res['flag'] = 2;
            return $res;
        }

    }

    public function orderindex(Request $request,$slug){
        $data = $request->all();
        $store    = Store::where('slug', $slug)->first();
        $store_payment_setting = Utility::getPaymentSetting($store->id);
        return view('storefront.paymentwall',compact('data','store_payment_setting','store','slug'));
    }
    public function orderPayWithPaymentwall(Request $request,$slug){

        $store    = Store::where('slug', $slug)->first();
        $cart     = session()->get($slug);
        $response_data = $cart['response_data'];
        //dd($cart);
        if(\Auth::check() && Utility::CustomerAuthCheck($slug) == false)
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
            $userdetail = new UserDetail();

            $userdetail['store_id'] = $store->id;
            $userdetail['name']     = $response_data['name'];
            $userdetail['email']    = $response_data['email'];
            $userdetail['phone']    = $response_data['phone'];

            $userdetail['custom_field_title_1'] = $response_data['custom_field_title_1'];
            $userdetail['custom_field_title_2'] = $response_data['custom_field_title_2'];
            $userdetail['custom_field_title_3'] = $response_data['custom_field_title_3'];
            $userdetail['custom_field_title_4'] = $response_data['custom_field_title_4'];


            $userdetail['billing_address']  = $response_data['billing_address'];
            $userdetail['shipping_address'] = !empty($response_data['shipping_address']) ? $response_data['shipping_address'] : '-';
            $userdetail['special_instruct'] = $response_data['special_instruct'];
            $userdetail->save();
            $userdetail->id;
            $cust_details = [
                                "id" => $userdetail->id,
                                "name" => $response_data['name'],
                                "email" => $response_data['email'],
                                "phone" => $response_data['phone'],
                                "custom_field_title_1" => $response_data['custom_field_title_1'],
                                "custom_field_title_2" => $response_data['custom_field_title_2'],
                                "custom_field_title_3" => $response_data['custom_field_title_3'],
                                "custom_field_title_4" => $response_data['custom_field_title_4'],
                                "billing_address" => $response_data['billing_address'],
                                "shipping_address" => $response_data['shipping_address'],
                                "special_instruct" => $response_data['special_instruct'],
                            ];

        if(!empty($response_data['coupon_id']) || $response_data['coupon_id'] != null)
        {
            $coupon = ProductCoupon::where('id', $response_data['coupon_id'])->first();
        }
        else
        {
            $coupon = '';
        }
        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;
        $products = $response_data['all_products'];
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
        if(!empty($response_data['shipping_id']))
        {
            $shipping = Shipping::find($response_data['shipping_id']);
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


        if($products)
        {
            
            $result = array();
            //The parameter after verify/ is the transaction reference to be verified
            
            \Paymentwall_Config::getInstance()->set(array(
                'private_key' => $store_payment_setting['paymentwall_private_key']
            ));
            $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $cart['response_data']['total_price'])));
            $parameters = $_POST;
            $chargeInfo = array(
                'email' => $parameters['email'],
                'history[registration_date]' => '1489655092',
                'amount' => $totalprice,
                'currency' => !empty($store->currency_code) ? $store->currency_code : 'USD',
                'token' => $parameters['brick_token'],
                'fingerprint' => $parameters['brick_fingerprint'],
                'description' => 'Order #123'
            );

            $charge = new \Paymentwall_Charge();
            $charge->create($chargeInfo);
            $responseData = json_decode($charge->getRawResponseData(),true);
            $response = $charge->getPublicData();


            $discount_price_order = !empty($response_data['dicount_price']) ? $response_data['dicount_price'] : '0';
            if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                if ($charge->isCaptured()) {

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
                        $order->price           = $response_data['total_price'];
                        $order->coupon          = $response_data['coupon_id'];
                        $order->coupon_json     = json_encode($coupon);
                        $order->discount_price  = $discount_price_order;
                        $order->coupon          = $response_data['coupon_id'];
                        $order->product         = json_encode($products);
                        $order->price_currency  = $store->currency_code;
                        $order->txn_id          = '';
                        $order->payment_type    = __('Paystack');
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

                        session()->forget($slug);
                        $msg = redirect()->route(
                            'store-complete.complete', [
                                                         $store->slug,
                                                         Crypt::encrypt($order->id),
                                                     ]
                        )->with('success', __('Transaction has been success'));

                        

                    $res['flag'] = 1;
                    $res['slug'] = $slug;
                    $res['order_id'] = Crypt::encrypt($order->id);
                    return $res;

                }                            
                elseif($charge->isUnderReview()) {
                    $res['flag'] = 2;
                    $res['slug'] = $slug;
                    return $res;
                }
            }else {
               $res['flag'] = 2;
               $res['slug'] = $slug;
                return $res;
            }
            
        }
        else
        {
            $res['flag'] = 2;
            $res['slug'] = $slug;
            return $res;
        }
    }
    public function orderpaymenterror(Request $request,$flag,$slug){
        //dd($slug);
        if($flag == 1){
            return redirect()->route('store.slug',$slug)->with('error', __('Transaction has been Successfull! '));
        }else{
                return redirect()->route('store.slug',$slug)->with('error', __('Transaction has been failed! '));
        }
    }

}
