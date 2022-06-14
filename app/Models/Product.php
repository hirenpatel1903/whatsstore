<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_categorie',
        'price',
        'quantity',
        'SKU',
        'product_tax',
        'product_display',
        'rating_display',
        'is_cover',
        'description',
        'created_by',
    ];

    public function categories()
    {
        return $this->hasOne('App\Models\ProductCategorie', 'id', 'product_categorie');
    }

    public function product_taxs()
    {
        return $this->hasOne('App\Models\ProductTax', 'id', 'product_tax');
    }

    public function product_rating()
    {
        $ratting    = Ratting::where('product_id', $this->id)->where('rating_view', 'on')->sum('ratting');
        $user_count = Ratting::where('product_id', $this->id)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }

        return $avg_rating;
    }

    public function product_category()
    {
        $result = ProductCategorie::whereIn('id', explode(',', $this->product_categorie))->get()->pluck('name')->toArray();

        return implode(', ', $result);
    }


    public static function possibleVariants($groups, $prefix = '')
    {
        $result = [];
        $group  = array_shift($groups);
        foreach($group as $selected)
        {
            if($groups)
            {
                $result = array_merge($result, self::possibleVariants($groups, $prefix . $selected . ' : '));
            }
            else
            {
                $result[] = $prefix . $selected;
            }
        }

        return $result;
    }


   public static function product_nm($product_name)
    {
        $taxArr  = explode(',', $product_name);
        $lead = 0;
       
        foreach($taxArr as $tax)
        {
            $store_id     = Store::find($tax);
           

            $lead = $store_id->name;
        }

        return $lead;
    }

 

    public static function product_cat($product_category)
    {
        $taxArr = explode(',', $product_category);

        $product_category = [];
        foreach($taxArr as $tax)
        {
            $taxesData = ProductCategorie::find($tax);
            $product_category[]   = !empty($taxesData) ? $taxesData->name : '';
        }

        return implode(',', $product_category);
    }


  public static function product_taxess($product_taxx)
    {
        $taxArr = explode(',', $product_taxx);

        $product_taxx = [];
        foreach($taxArr as $tax)
        {
            $taxesData = ProductTax::find($tax);
            $product_taxx[]   = !empty($taxesData) ? $taxesData->name : '';
        }

        return implode(',', $product_taxx);
    }





   public static function user_address_id_name($user_address)
    {
        $taxArr  = explode(',', $user_address);
        $lead = 0;
       
        foreach($taxArr as $tax)
        {
            $store_id     = UserDetail::find($tax);
            $lead = isset($store_id->email) ? $store_id->email : '';
    
        }

        return $lead;
    }

 

    public static function product_namee($product_category)
    {
        $taxArr = explode(',', $product_category);

        $product_category = [];
        foreach($taxArr as $tax)
        {
            $taxesData = Product::find($tax);
            $product_category[]   = !empty($taxesData) ? $taxesData->name : '';
        }

        return implode(',', $product_category);
    }


 public static function loc_name($location_name)
    {
        $taxArr = explode(',', $location_name);

        $product_category = [];
        foreach($taxArr as $tax)
        {
            $taxesData = Location::find($tax);
            $product_category[]   = !empty($taxesData) ? $taxesData->name : '';
        }

        return implode(',', $product_category);
    }


















}
