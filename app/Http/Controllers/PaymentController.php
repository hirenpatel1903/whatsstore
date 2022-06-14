<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Models\Coupon;
use App\Models\Mail\OrderMail;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\Utility;
use CoinGate\CoinGate;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mollie\Laravel\Facades\Mollie;
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;
use App\Models\UserDetail;
use App\Models\PurchasedProducts;
use App\Models\User;

class PaymentController extends Controller
{   
    //paystackPayment
    public function paystackPayment($slug, $code, $order_id)
    {
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
            $url = "https://api.paystack.co/transaction/verify/$code";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
                       'Authorization: Bearer ' . $store_payment_setting['paystack_secret_key'],
                   ]
            );
            $request = curl_exec($ch);
            curl_close($ch);
            if($request)
            {
                $result = json_decode($request, true);
            }
            $discount_price_order = !empty($response_data['dicount_price']) ? $response_data['dicount_price'] : '0';
            //!empty($response_data['dicount_price'] ? $response_data['dicount_price'] : '0';
            if(array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success'))
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
                if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }

                session()->forget($slug);
                $msg = redirect()->route(
                    'store-complete.complete', [
                                                 $store->slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

                

                return $msg;
            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //FlutterwavePayment
    public function flutterwavePayment($slug, $tran_id, $order_id)
    {
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

            $data = array(
                'txref' => $tran_id,
                'SECKEY' => $store_payment_setting['flutterwave_secret_key'],
                //secret key from pay button generated on rave dashboard
            );
           
            // make request to endpoint using unirest.
            $headers = array('Content-Type' => 'application/json');
            $body    = \Unirest\Request\Body::json($data);
            $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

            // Make `POST` request and handle response with unirest
            $response = \Unirest\Request::post($url, $headers, $body);

            $discount_price_order = !empty($response_data['dicount_price']) ? $response_data['dicount_price'] : '0';
            if($response->body->data->status === "successful" && $response->body->data->chargecode === "00")
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
                $order->payment_type    = __('Flutterwave');
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
                if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }
                 session()->forget($slug);
                $msg = redirect()->route(
                    'store-complete.complete', [
                                                 $store->slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

                return $msg;

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));

            }

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //RazerPayment
    public function razerpayPayment($slug, $pay_id, $order_id)
    {
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

            $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_USERPWD, $store_payment_setting['razorpay_public_key'] . ':' . $store_payment_setting['razorpay_secret_key']); // Input your Razorpay Key Id and Secret Id here
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch));
            // check that payment is authorized by razorpay or not

            if($response->status == 'authorized')
            {
                $discount_price_order = !empty($response_data['dicount_price']) ? $response_data['dicount_price'] : '0';
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
                $order->payment_type    = __('Razorpay');
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
                if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }
                session()->forget($slug);
                $msg = redirect()->route(
                    'store-complete.complete', [
                                                 $store->slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

                return $msg;

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));

            }

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //Mercado Pago Prepare Payment
    public function mercadopagoPayment($slug, Request $request)
    {
        if(Utility::CustomerAuthCheck($slug) == false)
        {
            return response()->json(
            [
                'status' => 'error',
                'success' => 'You need to login'
            ]
            );
        }
        else
        {
            $store = Store::where('slug', $slug)->first();
            $shipping = Shipping::where('store_id', $store->id)->first();
            if(!empty($shipping) && $store->enable_shipping == 'on')
            {
                if($request->shipping_price == '0.00')
                {
                    return response()->json(
                    [
                        'status' => 'error',
                        'success' => 'Please select shipping.'
                    ]
                    );
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
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => 'All field is required.'
                    ]
                    );
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
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => 'Please add to product into cart.'
                    ]
                    );
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

        $store = Store::where('slug', $slug)->first();

        if(\Auth::check() && Utility::CustomerAuthCheck($slug) == false)
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
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
            $cart['totalprice'] = $totalprice;
            $cart['coupon_id'] = $request->coupon_id;  
            $cart['coupon_json'] = json_encode($coupon);
            $cart['dicount_price'] = $request->dicount_price;
            $cart['currency_code'] = $store->currency_code;
            $cart['user_id'] = $store['id'];
            session()->put($slug, $cart);
            if(isset($cust_details) && !empty($cust_details))
            {

                $pdata['phone']   = isset($cust_details['phone']) ? $cust_details['phone'] : '';
                $pdata['email']   = isset($cust_details['email']) ? $cust_details['email'] : '';
                $pdata['user_id'] = '';
            }
            else
            {

                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }


            //$head_array = $request->headers;

           // $request = $request->all();

            \MercadoPago\SDK::setAccessToken($store_payment_setting['mercado_access_token']);
            try
            {
                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item              = new \MercadoPago\Item();
                $item->title       = $store->name . "Order";
                $item->quantity    = 1;
                $item->unit_price  = $totalprice;
                $preference->items = array($item);
                //                $coupons_id = $request->input('coupon_id');
                $success_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'success',
                                      ]
                );
                $failure_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'failure',
                                      ]
                );
                $pending_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'pending',
                                      ]
                );

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name    = $pdata['user_id'];
                $payer->email   = $pdata['email'];
                $payer->address = array(
                    "street_name" => '',
                );
                if($store_payment_setting['mercado_mode'] == 'live')
                {
                    $redirectUrl = $preference->init_point;
                }
                else
                {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return response()->json(
                    [
                        'status' => 'success',
                        'url' => $redirectUrl,
                    ]
                );
            }
            catch(Exception $e)
            {
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => $e->getMessage()
                    ]
                    );
                //return redirect()->back()->with('error', $e->getMessage());
            }

        }
        else
        {
            return response()->json(
                    [
                        'status' => 'error',
                        'success' => 'Please add Product to cart.'
                    ]
                    );
            //return redirect()->back()->with('error', __('Please add Product to cart.'));
        }
      }  
    }

    //Mercado Pago
    public function mercadopagoCallback($slug, Request $request)
    {
        $cart         = session()->get($slug);
        $store = Store::where('slug', $slug)->first();
        if(!empty($slug))
        {
            
            if($request->has('status'))
            {

                if($request->status == 'approved' && $request->flag == 'success')
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
                    $order->payment_type    = __('Mercado Pago');
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
                                
                                
                            return $msg;

                }
                else{
                    return redirect()->back()->with('error', __('Transaction Unsuccessful'));
                }

                            
                  }
        }
        else
        {
            session()->flash('error', 'Transaction Unsuccessful');

            return redirect()->back()->with('error', __('Transaction Unsuccessful'));
        }
    }

    //Paytm Prepare payment
    public function paytmOrder($slug, Request $request)
    {
        //dd($request->all());
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

            if(Utility::CustomerAuthCheck($slug))
            {
                $customer_data     = Auth::guard('customers')->user();
                $pdata['phone']   = $customer_data->phone_number;
                $pdata['email']   = $customer_data->email;
                $pdata['user_id'] = $customer_data->id;
            }
            else
            {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }
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
            $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
            $cart['totalprice'] = $totalprice;
            $cart['coupon_id'] = $request->coupon_id;  
            $cart['coupon_json'] = json_encode($coupon);
            $cart['dicount_price'] = $request->dicount_price;
            $cart['currency_code'] = $store->currency_code;
            $cart['user_id'] = $store['id'];
            session()->put($slug, $cart);
            config(
                [
                    'services.paytm-wallet.env' => $store_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $store_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $store_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $store_payment_setting['paytm_industry_type'],
                ]
            );
            $payment = PaytmWallet::with('receive');
            
            $payment->prepare(
                [
                    'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                    'user' => $pdata['user_id'],
                    'mobile_number' => $pdata['phone'],
                    'email' => $pdata['email'],
                    'amount' => $totalprice,
                    'callback_url' => route('paytm.callback', 'store=' . $slug),
                ]
            );

            return $payment->receive();

        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }
      }  
    }

    //Paytm Prepare payment
    public function paytmCallback(Request $request)
    {
       
        $slug     = $request->store;
        $store    = Store::where('slug', $slug)->first();
        //$products = '';
        $cart     = session()->get($slug);
        //dd($cart);
        if(\Auth::check() && Utility::CustomerAuthCheck($slug) == false)
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
        config(
                [
                    'services.paytm-wallet.env' => $store_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $store_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $store_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $store_payment_setting['paytm_industry_type'],
                ]
            );

            $transaction = PaytmWallet::with('receive');

            $response = $transaction->response();
            if($transaction->isSuccessful())
            {
                $cart     = session()->get($slug );
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
                $order->payment_type    = __('Paytm');
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
                session()->forget($slug);
                $msg =  redirect()->route(
                        'store-complete.complete', [
                                                     $slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));

                return $msg;

            }
            else if($transaction->isFailed())
            {
                return redirect()->route('store.slug',$slug)->with('error', __('Transaction Unsuccesfull'));
            }
            else if($transaction->isOpen())
            {
                //Transaction Open/Processing
                return redirect('/');
            }
            else
            {
                return redirect()->route('store.slug',$slug)->with('error', __('Payment not made'));
            }
    }

    //Mollie Prepare payment
    public function mollieOrder($slug, Request $request)
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
            $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
            $cart['totalprice'] = $totalprice;
            $cart['coupon_id'] = $request->coupon_id;  
            $cart['coupon_json'] = json_encode($coupon);
            $cart['dicount_price'] = $request->dicount_price;
            $cart['currency_code'] = $store->currency_code;
            $cart['user_id'] = $store['id'];
            session()->put($slug, $cart);
                if(Utility::CustomerAuthCheck($slug))
                {
                    $customer_data     = Auth::guard('customers')->user();
                    $pdata['phone']   = $customer_data->phone_number;
                    $pdata['email']   = $customer_data->email;
                    $pdata['user_id'] = $customer_data->id;
                }
                else
                {
                    $pdata['phone']   = '';
                    $pdata['email']   = '';
                    $pdata['user_id'] = '';
                }
                $request = $request->all();
                $mollie  = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($store_payment_setting['mollie_api_key']);
                //var_dump(intval($request['amount']));

                $payment = $mollie->payments->create(
                    [
                        "amount" => [
                            "currency" => "$store->currency_code",
                            "value" => number_format($totalprice, 2),
                        ],
                        "description" => "payment for product",
                        "redirectUrl" => route(
                            'mollie.callback', [
                                                 $store->slug,
                                                 $request['desc'],
                                             ]
                        ),

                    ]
                );

                session()->put('mollie_payment_id', $payment->id);

                return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);

            }
            else
            {
                return redirect()->back()->with('error', __('is deleted.'));
            }
        }    
    }

    //Mollie Callback payment
    public function mollieCallback($slug, $order_id, Request $request)
    {
        $store = Store::where('slug', $slug)->first();
        if(\Auth::check() && Utility::CustomerAuthCheck($slug) == false)
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($store_payment_setting['mollie_api_key']);

            if(session()->has('mollie_payment_id'))
            {
                $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                if($payment->isPaid())
                {
                    $cart     = session()->get($slug );
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
                    $order->payment_type    = __('Mollie');
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
                    session()->forget($slug);
                    $msg =  redirect()->route(
                            'store-complete.complete', [
                                                         $slug,
                                                         Crypt::encrypt($order->id),
                                                     ]
                        )->with('success', __('Transaction has been success'));

                    return $msg;

                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
            }
            else
            {
                session()->flash('warning', 'Payment not made!');
                return redirect()->back()->with('error', __('Payment not made!'));
            }
    }

    //skrillPayment Prepare payment
    public function skrillPayment($slug, Request $request)
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
                $coupon_id = null;
                //$price     = $total + $total_tax;
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
                if(Utility::CustomerAuthCheck($slug))
                {
                    $customer_data     = Auth::guard('customers')->user();
                    $pdata['phone']   = $customer_data->phone_number;
                    $pdata['email']   = $customer_data->email;
                    $pdata['user_id'] = $customer_data->id;
                }
                else
                {
                    $pdata['phone']   = '';
                    $pdata['email']   = '';
                    $pdata['user_id'] = '';
                }

                $cart['cust_details'] = $cust_details;
                $cart['shipping_data'] = $shipping_data;
                $cart['product_id'] = $product_id;
                $cart['all_products'] = $products;

                $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
                $cart['totalprice'] = $totalprice;
                $cart['coupon_id'] = $request->coupon_id;  
                $cart['coupon_json'] = json_encode($coupon);
                $cart['dicount_price'] = $request->dicount_price;
                $cart['currency_code'] = $store->currency_code;
                $cart['user_id'] = $store['id'];
                session()->put($slug, $cart);

                //$head_array = $request->headers;

                $request = $request->all();
                if(!empty($store->logo))
                {
                    $logo = asset(Storage::url('uploads/store_logo/' . $store->logo));
                }
                else
                {

                    $logo = asset(Storage::url('uploads/store_logo/logo.png'));
                }

                $skill               = new SkrillRequest();
                $skill->pay_to_email = $store_payment_setting['skrill_email'];
                $skill->return_url   = route('skrill.callback') . '?transaction_id=' . MD5($request['transaction_id']);
                $skill->cancel_url   = route('skrill.callback');
                $skill->logo_url     = $logo;

                // create object instance of SkrillRequest
                $skill->transaction_id  = MD5($request['transaction_id']); // generate transaction id
                $skill->amount          = $totalprice;
                $skill->currency        = $store->currency_code;
                $skill->language        = 'EN';
                $skill->prepare_only    = '1';
                $skill->merchant_fields = 'site_name, customer_email';
                $skill->site_name       = $store->name;
                $skill->customer_email  = $pdata['email'];

                // create object instance of SkrillClient
                $client = new SkrillClient($skill);
                $sid    = $client->generateSID(); //return SESSION ID

                // handle error
                $jsonSID = json_decode($sid);
                if($jsonSID != null && $jsonSID->code == "BAD_REQUEST")
                {
                    return redirect()->back()->with('error', $jsonSID->message);
                }


                // do the payment
                $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
                if($request['transaction_id'])
                {
                    $data = [
                        'amount' => $totalprice,
                        'trans_id' => MD5($request['transaction_id']),
                        'currency' => $store->currency_code,
                        'slug' => $store->slug,
                    ];
                    session()->put('skrill_data', $data);

                }

                return redirect($redirectUrl);
            }
            else
            {
                return redirect()->back()->with('error', __('Please add Product into cart.'));
            }
        }    


    }

    //skrillPayment Callback payment
    public function skrillCallback(Request $request)
    {
        if(session()->has('skrill_data'))
        {
            $get_data = session()->get('skrill_data');
            $slug     = $get_data['slug'];
            $store    = Store::where('slug', $slug)->first();
            //$products = '';
            $cart     = session()->get($slug);
            if(\Auth::check())
            {
                $store_payment_setting = Utility::getPaymentSetting();
            }
            else
            {
                $store_payment_setting = Utility::getPaymentSetting($store->id);
            }
            $cart     = session()->get($slug );
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
            $order->payment_type    = __('Skrill');
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
            session()->forget($slug);
            $msg =  redirect()->route(
                    'store-complete.complete', [
                                                 $slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

            return $msg;
        }
        else
        {
            session()->flash('error', 'Transaction Unsuccessful');
            return redirect()->back()->with('error', __('Transaction Unsuccessful.'));

        }
    }

    //Coingate Pago Prepare Payment
    public function coingatePayment($slug, Request $request)
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
            $totalprice = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->total_price)));
            $cart['totalprice'] = $totalprice;
            $cart['coupon_id'] = $request->coupon_id;  
            $cart['coupon_json'] = json_encode($coupon);
            $cart['dicount_price'] = $request->dicount_price;
            $cart['currency_code'] = $store->currency_code;
            $cart['user_id'] = $store['id'];
            session()->put($slug, $cart);
            if($products)
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
                $order->payment_type    = __('coingate');
                $order->payment_status  = 'pending';
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
                try
                {
                   
                    CoinGate::config(
                        array(
                            'environment' => $store_payment_setting['coingate_mode'],
                            // sandbox OR live
                            'auth_token' => $store_payment_setting['coingate_auth_token'],
                            'curlopt_ssl_verifypeer' => FALSE
                            // default is false
                        )
                    );

                    $post_params = array(
                        'order_id' => $order->id,
                        'price_amount' => $totalprice,
                        'price_currency' => $store['currency_code'],
                        'receive_currency' => $store['currency_code'],
                        'callback_url' => url('coingate/callback') . '?slug=' . $store->slug . '&order_id=' . $order->id,
                        'cancel_url' => url('/'),
                        'success_url' => route(
                            'coingate.callback', [
                                                         'slug' => $store->slug,
                                                         'order_id' => $order->id,
                                                     ]
                        ),
                        'title' => 'Order #' . time(),
                    );
                    $order_email=$order->email;
                    $order_name=$order->name;

                    $order       = \CoinGate\Merchant\Order::create($post_params);
                    
                    if($order)
                    {

                        session()->forget($slug);
                        return redirect($order->payment_url);
                    }
                    else
                    {

                        return redirect()->back()->with('error', __('opps something wren wrong.'));
                    }


                }
                catch(Exception $e)
                {
                     
                    return redirect()->back()->with('error', $e->getMessage());
                }
                $msg = redirect()->route(
                    'store-complete.complete', [
                                                 $store->slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

                session()->forget($slug);

                

                return $msg;
            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
         }   
    }

    //Coingate Pago
    public function coingateCallback(Request $request)
    {
        $slug = $request->slug;
        $store        = Store::where('slug', $slug)->first();
        if($request->has('order_id'))
        {
            $order = Order::where('id', $request->order_id)->first();
            if($order)
            {
                $order->payment_status = 'approved';
                $order->save();
                
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
                $msg = redirect()->route(
                    'store-complete.complete', [
                                                 $slug,
                                                 Crypt::encrypt($order->id),
                                             ]
                )->with('success', __('Transaction has been success'));

                session()->forget($slug);

                

                return $msg;
            }
        }
    }


    //Plan
    //Plan

    // Plan purchase  Payments methods
    public function paystackPlanGetPayment($code, $plan_id, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($plan_id));
        $plan                  = Plan::find($plan_id);

        if($plan)
        {

            // try
            // {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified
                $url = "https://api.paystack.co/transaction/verify/$code";
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, [
                           'Authorization: Bearer ' . $admin_payment_setting['paystack_secret_key'],
                       ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if($responce)
                {
                    $result = json_decode($responce, true);
                }
                if(array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success'))
                {
                    $status = $result['data']['status'];
                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
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
                   
                    $planorder->name      = $plan->name;
                    $planorder->plan_id        = $plan->id;
                    $planorder->price          = $result['data']['amount'] / 100;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $code;
                    $planorder->payment_type   = __('Paystack');
                    $planorder->payment_status = $result['data']['status'];
                    $planorder->receipt        = '';
                    $planorder->user_id        = $user->id;
                    $planorder->store_id       = $store_id;
                    $planorder->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    if($assignPlan['is_success'])
                    {


                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    }
                    else
                    {


                        return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                    }

                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }

            // }
            // catch(\Exception $e)
            // {
            //     return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            // }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Plan flutterwave  Payments methods
    public function flutterwavePlanGetPayment($code, $plan_id, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($plan_id));
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $data = array(
                'txref' => $code,
                'SECKEY' => $admin_payment_setting['flutterwave_secret_key'],
                //secret key from pay button generated on rave dashboard
            );

            // make request to endpoint using unirest.
            $headers = array('Content-Type' => 'application/json');
            $body    = \Unirest\Request\Body::json($data);
            $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

            // Make `POST` request and handle response with unirest
            $response = \Unirest\Request::post($url, $headers, $body);


            if($response->body->data->status === "successful" && $response->body->data->chargecode === "00")
            {

                if($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);
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
                $planorder->name           = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $response->body->data->amount;
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $response->body->data->txid;
                $planorder->payment_type   = __('Flutterwave ');
                $planorder->payment_status = $response->body->data->status;
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();

                $assignPlan = $user->assignPlan($plan->id);

                if($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                }
                else
                {


                    return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Plan razorpay  Payments methods
    public function razorpayPlanGetPayment($pay_id, $plan_id, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($plan_id));
        $plan                  = Plan::find($plan_id);

        if($plan)
        {

            try
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified
                $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_USERPWD, $admin_payment_setting['razorpay_public_key'] . ':' . $admin_payment_setting['razorpay_secret_key']); // Input your Razorpay Key Id and Secret Id here
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch));
                // check that payment is authorized by razorpay or not

                if($response->status == 'authorized')
                {

                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
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
                    $planorder->price          = $response->amount / 100;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $pay_id;
                    $planorder->payment_type   = __('Razorpay');
                    $planorder->payment_status = $response->status == 'authorized' ? 'success' : 'failed';
                    $planorder->receipt        = '';
                    $planorder->user_id        = $user->id;
                    $planorder->store_id       = $store_id;
                    $planorder->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    }
                    else
                    {


                        return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                    }

                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }

            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Mercado Plan PreparePayment
    public function mercadopagoPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(
                [
                    'status' => 'error',
                    'error' => $messages->first(),
                ]
            );
        }
        $plan = Plan::find($request->plan)->first();
        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            \MercadoPago\SDK::setAccessToken($admin_payment_setting['mercado_access_token']);
            try
            {
                $amount = (float)$request->total_price;
                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item              = new \MercadoPago\Item();
                $item->title       = "Plan : " . $plan->name;
                $item->quantity    = 1;
                $item->unit_price  = $amount;
                $preference->items = array($item);
                $coupons_id        = $request->input('coupon_id');
                $success_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'coupon_id=' . $coupons_id,
                                               'flag' => 'success',
                                           ]
                );
                $failure_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'flag' => 'failure',
                                           ]
                );
                $pending_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'flag' => 'pending',
                                           ]
                );

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name    = \Auth::user()->name;
                $payer->email   = \Auth::user()->email;
                $payer->address = array(
                    "street_name" => '',
                );
                if($admin_payment_setting['mercado_mode'] == 'live')
                {
                    $redirectUrl = $preference->init_point;
                }
                else
                {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return response()->json(
                    [
                        'status' => 'success',
                        'url' => $redirectUrl,
                    ]
                );
            }
            catch(Exception $e)
            {
                return response()->json(
                    [
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

    }

    // Mercado mercadopagoPaymentCallback
    public function mercadopagoPaymentCallback($plan, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Crypt::decrypt($plan);
        $plan                  = Plan::find($plan_id);
        if($plan)
        {
            $orderID = time();
            if($plan && $request->has('status'))
            {
                $price = $plan->price;
                if($request->status == 'approved' && $request->flag == 'success')
                {
                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $discount_value = ($price / 100) * $coupons->discount;

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
                            $price = $price - $discount_value;
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
                    $planorder->price          = $price;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $request->has('preference_id') ? $request->preference_id : '';
                    $planorder->payment_type   = __('Mercado Pago');
                    $planorder->payment_status = 'success';
                    $planorder->receipt        = '';
                    $planorder->user_id        = $user->id;
                    $planorder->store_id       = $store_id;
                    $planorder->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                    }
                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Paytm Plan PreparePayment
    public function paytmPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                               'mobile_number' => 'required|numeric',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            config(
                [
                    'services.paytm-wallet.env' => $admin_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $admin_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $admin_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $admin_payment_setting['paytm_industry_type'],
                ]
            );

            $payment = PaytmWallet::with('receive');

            $payment->prepare(
                [
                    'order' => $plan_id,
                    'user' => Auth::user()->id,
                    'mobile_number' => $request->mobile_number,
                    'email' => Auth::user()->email,
                    'amount' => $request->total_price,
                    'callback_url' => route('plan.paytm.callback', 'store=' . $store->slug),
                ]
            );

            return $payment->receive();

        }

    }

    public function paytmPlanGetPayment(Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = $request->ORDERID;
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            config(
                [
                    'services.paytm-wallet.env' => $admin_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $admin_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $admin_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $admin_payment_setting['paytm_industry_type'],
                ]
            );
            $transaction = PaytmWallet::with('receive');

            // To get raw response as array
            $response = $transaction->response();

            if($transaction->isSuccessful())
            {
                if($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);
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
                $planorder->price          = $response['TXNAMOUNT'];
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $response['MID'];
                $planorder->payment_type   = __('Razorpay');
                $planorder->payment_status = 'success';
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();

                $assignPlan = $user->assignPlan($plan->id);

                if($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Mollie Plan PreparePayment
    public function molliePaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(
                [
                    'status' => 'error',
                    'error' => $messages->first(),
                ]
            );
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($admin_payment_setting['mollie_api_key']);

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => env('CURRENCY'),
                        "value" => number_format($request->total_price, 2),
                    ],
                    "description" => $plan->name,
                    "redirectUrl" => route(
                        'plan.mollie.callback', [
                                                  $store->slug,
                                                  $request->plan_id,
                                              ]
                    ),

                ]
            );
            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);

        }

    }

    public function molliePlanGetPayment(Request $request, $slug, $plan_id)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($plan_id));
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            try
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($admin_payment_setting['mollie_api_key']);

                if(session()->has('mollie_payment_id'))
                {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if($payment->isPaid())
                    {
                        if($request->has('coupon_id') && $request->coupon_id != '')
                        {
                            $coupons = Coupon::find($request->coupon_id);
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
                        $planorder->price          = $payment->amount->value;
                        $planorder->price_currency = env('CURRENCY');
                        $planorder->txn_id         = $payment->id;
                        $planorder->payment_type   = __('Razorpay');
                        $planorder->payment_status = $payment->status == 'authorized' ? 'success' : 'failed';
                        $planorder->receipt        = '';
                        $planorder->user_id        = $user->id;
                        $planorder->store_id       = $store_id;
                        $planorder->save();

                        $assignPlan = $user->assignPlan($plan->id);

                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                        }
                        else
                        {


                            return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                        }

                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                    }

                    session()->forget('mollie_payment_id');


                }
                else
                {
                    session()->flash('error', 'Transaction Error');

                    return redirect('/');
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // skrill Plan PreparePayment
    public function skrillPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);
        $price   = $request->total_price;

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            if(!empty($store->logo))
            {
                $logo = asset(Storage::url('uploads/store_logo/' . $store->logo));
            }
            else
            {
                $logo = asset(Storage::url('uploads/store_logo/logo.png'));
            }

            $skill               = new SkrillRequest();
            $skill->pay_to_email = $admin_payment_setting['skrill_email'];
            $skill->return_url   = route('plan.skrill.callback') . '?transaction_id=' . MD5($request['transaction_id']);
            $skill->cancel_url   = route('plan.skrill.callback');
            $skill->logo_url     = $logo;

            // create object instance of SkrillRequest
            $skill->transaction_id  = MD5($request['transaction_id']); // generate transaction id
            $skill->amount          = $price;
            $skill->currency        = env('CURRENCY');
            $skill->language        = 'EN';
            $skill->prepare_only    = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name       = $store->name;
            $skill->customer_email  = Auth::user()->email;

            // create object instance of SkrillClient
            $client = new SkrillClient($skill);
            $sid    = $client->generateSID(); //return SESSION ID

            // handle error
            $jsonSID = json_decode($sid);
            if($jsonSID != null && $jsonSID->code == "BAD_REQUEST")
            {
                return redirect()->back()->with('error', $jsonSID->message);
            }


            // do the payment
            $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
            if($request['transaction_id'])
            {
                $data = [
                    'amount' => $price,
                    'trans_id' => MD5($request['transaction_id']),
                    'currency' => $store->currency_code,
                    'slug' => $store->slug,
                ];
                session()->put('skrill_data', $data);

            }

            return redirect($redirectUrl);


        }

    }

    public function skrillPlanGetPayment(Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = $request->ORDERID;
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            if(session()->has('skrill_data'))
            {
                $get_data = session()->get('skrill_data');
                $orderID  = time();

                if($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);
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
                $planorder->price          = isset($get_data['amount']) ? $get_data['amount'] : 0;
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $request->has('transaction_id') ? $request->transaction_id : '';;
                $planorder->payment_type   = __('Skrill');
                $planorder->payment_status = 'success';
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();

                $assignPlan = $user->assignPlan($plan->id);

                if($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                }
                else
                {


                    return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                }

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');

        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    //CoinGate
    public function coingatePaymentPrepare(Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);
        $price   = $request->total_price;

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            CoinGate::config(
                array(
                    'environment' => $admin_payment_setting['coingate_mode'],
                    // sandbox OR live
                    'auth_token' => $admin_payment_setting['coingate_auth_token'],
                    'curlopt_ssl_verifypeer' => FALSE
                    // default is false
                )
            );
            $post_params = array(
                'order_id' => time(),
                'price_amount' => $price,
                'price_currency' => env('CURRENCY'),
                'receive_currency' => env('CURRENCY'),
                'callback_url' => url('coingate-payment-plan') . '?plan_id=' . $plan->id . '&user_id=' . Auth::user()->id,
                'cancel_url' => route('plans.index'),
                'success_url' => url('coingate-payment-plan') . '?plan_id=' . $plan->id . '&user_id=' . Auth::user()->id,
                'title' => 'Order #' . time(),
            );

            $order = \CoinGate\Merchant\Order::create($post_params);
            if($order)
            {
                return redirect($order->payment_url);
            }
            else
            {
                return redirect()->back()->with('error', __('opps something wren wrong.'));
            }
        }
    }

    public function coingatePlanGetPayment(Request $request)
    {
        $user                  = Auth::user();
        $plan_id               = $request->plan_id;
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            try
            {
                $orderID = time();
                if($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);
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
                $planorder->txn_id         = '-';
                $planorder->payment_type   = __('CoinGAte');
                $planorder->payment_status = 'success';
                $planorder->receipt        = '';
                $planorder->user_id        = $user->id;
                $planorder->store_id       = $store_id;
                $planorder->save();

                $assignPlan = $user->assignPlan($plan->id);

                if($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                }
                else
                {


                    return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
