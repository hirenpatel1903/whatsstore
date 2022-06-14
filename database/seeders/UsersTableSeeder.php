<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Store;
use App\Models\Utility;
use App\Models\UserStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin              = User::create(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('1234'),
                'type' => 'super admin',
                'lang' => 'en',
                'created_by' => 0,
            ]
        );
        $_ENV['CURRENCY_SYMBOL'] = '$';
        $_ENV['CURRENCY']        = 'USD ';

        $admin = User::create(
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'password' => Hash::make('1234'),
                'type' => 'Owner',
                'created_by' => $superAdmin->id,
            ]
        );

        $objStore             = Store::create(
            [
                'name' => 'My WhatsStore',
                'email' => 'owner@example.com',
                'created_by' => $admin->id,
                'tagline' => 'WhatsStore',
                'enable_storelink' => 'on',
                'content' => 'Hi,
Welcome to {store_name},
Your order is confirmed & your order no. is {order_no}
Your order detail is:
Name : {customer_name}
Address : {billing_address} , {shipping_address}
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
Thanks {store_name}',
                'item_variable' => '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}',
                'store_theme' => 'style-grey-body.css',
                'address' => 'india',
                'whatsapp' => '#',
                'facebook' => '#',
                'instagram' => '#',
                'twitter' => '#',
                'youtube' => '#',
                'footer_note' => '© 2020 WhatsStore. All rights reserved',
                'logo' => 'logo.png',
            ]
        );
        $admin->current_store = $objStore->id;
        $admin->save();

        UserStore::create(
            [
                'user_id' => $admin->id,
                'store_id' => $objStore->id,
                'permission' => 'Owner',
            ]
        );

        Utility::defaultEmail();
        Utility::userDefaultData();
    }
}
