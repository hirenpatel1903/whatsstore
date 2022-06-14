<?php

namespace App\Imports;

use App\Models\ProductCoupon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductCouponsImport implements ToModel
{
   use Importable;   
    public function model(array $row)
    {
       
    }
}
