<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'email',
        'card_number',
        'card_exp_month',
        'card_exp_year',
        'user_address_id',
        'product_id',
        'plan_name',
        'plan_id',
        'price',
        'product',
        'price_currency',
        'txn_id',
        'payment_type',
        'payment_status',
        'status',
        'phone',
        'receipt',
        'user_id',
        'subscription_id',
        'payer_id',
        'payment_frequency',
        'created_by',
    ];


    public static function total_orders()
    {
        return Order::count();
    }

    public static function total_orders_price()
    {
        return Order::sum('price');
    }

    public function total_coupon_used()
    {
        return $this->hasOne('App\Models\UserCoupon', 'order', 'order_id');
    }
}
