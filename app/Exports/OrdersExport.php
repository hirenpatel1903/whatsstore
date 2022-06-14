<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection,WithHeadings
{

 
protected $id;

 function __construct($id) {
        $this->id = $id;
 }


   public function collection()
    {
            
                $data = Order::where('user_id',$this->id)->get();

        foreach($data as $k => $Order)
        {
       
            unset($Order->created_by,$Order->shipping_data,$Order->discount_price,$Order->coupon_json,$Order->coupon,$Order->subscription_id,$Order->payer_id,$Order->payment_frequency,$Order->card_number,$Order->card_exp_month,$Order->card_exp_year,$Order->plan_name,$Order->product,$Order->plan_id,$Order->txn_id,$Order->receipt);
      
            $user_address                        = Product::user_address_id_name($Order->user_address_id);

            $product_name                        = Product::product_namee($Order->product_id);    
            $store_name                          = Product::product_nm($Order->user_id);

            $data[$k]["order_id"]                = $Order->order_id;
            $data[$k]["name"]                    = $Order->name;
            $data[$k]["email"]                   = $Order->email;
            $data[$k]["product_id"]              = $product_name;
            $data[$k]["price"]                   = $Order->price;   
            $data[$k]["price_currency"]          = $Order->price_currency;         
            $data[$k]["payment_type"]            =$Order->payment_type;
            $data[$k]["payment_status"]          =$Order->payment_status;
            $data[$k]["status"]                  =$Order->status;
            $data[$k]["phone"]                   =$Order->phone;
            $data[$k]["user_id"]                 =$store_name;
         
        }  

        return $data;
    }

     public function headings(): array
    {
        return [
        "ID",
        'order_id',
        'name',
        'email',
        'product_id',
        'Order Total',
        'price_currency',
        'payment_type',
        'payment_status',
        'status',
        'phone',
        'user_id',
        "Created At",
        "Updated At",
        ];
    }



}
