<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection,WithHeadings
{


protected $id;

 function __construct($id) {
        $this->id = $id;
 }


    public function collection()
    {
            

            $data = Product::where('store_id',$this->id)->get();

        foreach($data as $k => $Product)
        {
            unset($Product->created_by,$Product->is_cover,$Product->downloadable_prodcut,$Product->enable_product_variant,$Product->variants_json,$Product->is_active,$Product->custom_field_1,$Product->custom_value_1,$Product->custom_field_2,$Product->custom_field_3,$Product->custom_field_4,$Product->custom_value_4,$Product->custom_value_2,$Product->custom_value_3);

            
            $store_name                          = Product::product_nm($Product->store_id);
            $product_cate                        = Product::product_cat($Product->product_categorie);    
            $product_tax                         = Product::product_taxess($Product->product_tax);
            $data[$k]["store_id"]                = $store_name;
            $data[$k]["name"]                    = $Product->name;
            $data[$k]["product_categorie"]       = $product_cate;
            $data[$k]["price"]                   = $Product->price;
            $data[$k]["quantity"]                = $Product->quantity;
            $data[$k]["SKU"]                     = $Product->SKU;
            $data[$k]["product_tax"]             = $product_tax;
            $data[$k]["product_display"]         = $Product->product_display;         
            $data[$k]["description"]           =$Product->description;
         
        }  

        return $data;
    }

     public function headings(): array
    {
        return [
        "ID",
        "store_id",
        'name',
        'product_categorie',
        'price',
        'quantity',
        'SKU',
        'product_tax',
        'product_display',
        'description',
        "Created At",
        "Updated At",
        ];
    }
}
