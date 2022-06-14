<div class="row align-items-center pb-2">
    <input type="hidden" id="product_id" value="{{$products->id}}">
    <input type="hidden" id="variant_id" value="">
    <input type="hidden" id="variant_qty" value="">
    @foreach($product_variant_names as $key => $variant)
        <div class="col-sm-6 mb-4 mb-sm-0">
            <span class="d-block h6 mb-0">
                <th><label class="control-label">{{ ucfirst($variant->variant_name) }}</label></th>
                <select name="product[{{$key}}]" id="pro_variants_name" class="form-control custom-select variant-selection  pro_variants_name{{$key}}">
                    <option value="">{{ __('Select')  }}</option>
                    @foreach($variant->variant_options as $key => $values)
                        <option value="{{$values}}">{{$values}}</option>
                    @endforeach
                </select>
            </span>
        </div>
    @endforeach
</div>
<div class="row align-items-center">
    <div class="col-sm-6 mb-4 mb-sm-0">
        <span class="d-block h3 mb-0 variation_price">
             @if($products->enable_product_variant =='on')
                {{\App\Models\Utility::priceFormat(0)}}
            @else
                {{\App\Models\Utility::priceFormat($products->price)}}
            @endif
        </span>
    </div>
    <div class="col-sm-6 text-sm-right product-detail">
        <a class="action-item add_to_cart_variant" data-toggle="tooltip" data-id="{{$products->id}}">
            <button type="button" class="btn btn-addcart btn-icon">
                <span class="btn-inner--icon grey-text">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span class="btn-inner--text grey-text">{{__('Add to cart')}}</span>
            </button>
        </a>
    </div>
</div>
