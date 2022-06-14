<?php

namespace App\Exports;

use App\Models\Shipping;
use App\Models\Store;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShippingExport implements FromCollection,WithHeadings
{


    public function collection()

    {
         $user             = \Auth::user();
         $store_id         = Store::where('id', $user->current_store)->first();
         $data = Shipping::where('store_id',$store_id->id)->get();

        foreach($data as $k => $Shipping)
        {
            unset($Shipping->created_by,$Shipping->shipping_data);

            
             $store_name                          = Product::product_nm($Shipping->store_id);
             $location_name                       = Product::loc_name($Shipping->location_id);
            $data[$k]["name"]                     = $Shipping->name;
            $data[$k]["price"]                    = $Shipping->price;
            $data[$k]["location_id"]              =  $location_name ;
             $data[$k]["store_id"]                = $store_name;
         
        }  

        return $data;
    }


     public function headings(): array
    {
        return [
        "ID",
        'name',
        'price',
        'location_id',
         'store_id',
        "Created At",
        "Updated At",
        ];
    }
}


      