<style>
    .product_item .product_details_icon {
        visibility: hidden;
    }

    .product_item:hover .product_details_icon {
        visibility: visible;
    }

    .product_details_active {
        color: #ffffff;
        font-size: 18px;
        margin-right: 5px;
    }

    .modal-lg {
        max-width: 70% !important;
    }

    @media only screen and (max-width: 991px) {
        .modal.fade.show {
            z-index: 9999;
        }
        .modal-lg {
            max-width: 100% !important;
        }       
    }
</style>
@php
    \App::setLocale(isset($store->lang) ? $store->lang : 'en');
@endphp
<div>
    @foreach($products as $key => $items)

        <div class="row collection-items {{ ($loop->iteration != 1) ? 'd-none' : ''  }} {{$loop->iteration-1}}{!!str_replace(' ','_',$key)!!} product_tableese">
            @foreach($items as $product)
                <div class="col-sm-6 col-md-6 col-xl-3 product_item" data-name="{{$product->name}}">
                    <div class="product-card">
                            <a href="#" data-size="lg" data-url="{{ $flag != 'my_orders' ? route('store.product.product_view',[$store->slug,$product->id]):route('store.product.product_order_view',[$product->id,Auth::guard('customers')->user()->id,$store->slug])}}" data-title="{{$product->name}}" data-ajax-popup="true" class="product_details_icon float-right"><i class="fas fa-eye product_details_active text_color"></i></a>
                        <div class="product-image">
                            <a href="#" data-size="lg" data-url="{{ $flag != 'my_orders' ? route('store.product.product_view',[$store->slug,$product->id]):route('store.product.product_order_view',[$product->id,Auth::guard('customers')->user()->id,$store->slug])}}" data-title="{{$product->name}}" data-ajax-popup="true">
                                @if(!empty($product->is_cover))
                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" width="70px">
                                @else
                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" width="70px">
                                @endif
                            </a>
                        </div>
                        <div class="product-detail">
                            <a href="#" data-size="lg" data-url="{{ $flag != 'my_orders' ? route('store.product.product_view',[$store->slug,$product->id]):route('store.product.product_order_view',[$product->id,Auth::guard('customers')->user()->id,$store->slug])}}" data-title="{{$product->name}}" data-ajax-popup="true">
                                <h4 class="title">{{$product->name}}</h4>
                            </a>
                            <p class="qty-item">{{(isset($product->categories) && !empty($product->categories)) ? $product->categories->name : ''}}</p>
                            <div class="item-spin">
                                @if($product->enable_product_variant == 'on')
                                    <p class="price-spin">{{__('In Variant')}}</p>
                                @else
                                    <p class="price-spin">{{\App\Models\Utility::priceFormat($product->price)}}</p>

                                @endif
                            </div>
                            @if($flag != 'my_orders')    
                                @if($product->enable_product_variant == 'on')
                                    <a href="#" class="btn btn-addcart" data-size="md" data-url="{{ route('store-variant.variant',[$store->slug,$product->id])}}" data-ajax-popup="true" data-title="{{__('Add Variant')}}">{{__('ADD TO CART')}}
                                        <svg class="cart-icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.70269 4.66667H5.14854H9.14854H11.5944L10.7583 10.1014C10.7082 10.4266 10.4284 10.6667 10.0994 10.6667H4.19771C3.86866 10.6667 3.58883 10.4266 3.5388 10.1014L2.70269 4.66667ZM9.81521 2.66667V3.33333H11.5944H13.1485C13.5167 3.33333 13.8152 3.63181 13.8152 4C13.8152 4.36819 13.5167 4.66667 13.1485 4.66667H12.928C12.9279 4.73342 12.9227 4.80113 12.9122 4.86941L12.0761 10.3041C11.926 11
                                                                                                                            .2798 11.0865 12 10.0994 12H4.19771C3.21057 12 2.37107 11.2798 2.22097 10.3041L1.38486 4.86941C1.37435 4.80113 1.3692 4.73342 1.36907 4.66667H1.14854C0.780349 4.66667 0.481873 4.36819 0.481873 4C0.481873 3.63181 0.780349 3.33333 1.14854 3.33333H2.70269H4.48187V2.66667C4.48187 1.19391 5.67578 0 7.14854 0C8.6213 0 9.81521 1.19391 9.81521 2.66667ZM5.81521 2.66667V3.33333H8.48187V2.66667C8.48187 1.93029 7.88492 1.33333 7.14854 1.33333C6.41216 1.33333 5.81521 1.93029 5.81521 2.66667ZM7.14854 9.33333C6.78035 9.33333 6.48187 9.03486 6.48187 8.66667V6.66667C6.48187 6.29848 6.78035 6 7.14854 6C7.51673 6 7.81521 6.29848 7.81521 6.66667V8.66667C7.81521 9.03486 7.51673 9.33333 7.14854 9.33333ZM4.48187 8.66667C4.48187 9.03486 4.78035 9.33333 5.14854 9.33333C5.51673 9.33333 5.81521 9.03486 5.81521 8.66667V6.66667C5.81521 6.29848 5.51673 6 5.14854 6C4.78035 6 4.48187 6.29848 4.48187 6.66667V8.66667ZM8.48187 8.66667C8.48187 9.03486 8.78035 9.33333 9.14854 9.33333C9.51673 9.33333 9.81521 9.03486 9.81521 8.66667V6.66667C9.81521 6.29848 9.51673 6 9.14854 6C8.78035 6 8.48187 6.29848 8.48187 6.66667V8.66667Z"
                                                  fill="white"/>
                                        </svg>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-addcart add_to_cart" data-id="{{$product->id}}">{{__('ADD TO CART')}}
                                        <svg class="cart-icon" width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.70269 4.66667H5.14854H9.14854H11.5944L10.7583 10.1014C10.7082 10.4266 10.4284 10.6667 10.0994 10.6667H4.19771C3.86866 10.6667 3.58883 10.4266 3.5388 10.1014L2.70269 4.66667ZM9.81521 2.66667V3.33333H11.5944H13.1485C13.5167 3.33333 13.8152 3.63181 13.8152 4C13.8152 4.36819 13.5167 4.66667 13.1485 4.66667H12.928C12.9279 4.73342 12.9227 4.80113 12.9122 4.86941L12.0761 10.3041C11.926 11
                                                                                                                            .2798 11.0865 12 10.0994 12H4.19771C3.21057 12 2.37107 11.2798 2.22097 10.3041L1.38486 4.86941C1.37435 4.80113 1.3692 4.73342 1.36907 4.66667H1.14854C0.780349 4.66667 0.481873 4.36819 0.481873 4C0.481873 3.63181 0.780349 3.33333 1.14854 3.33333H2.70269H4.48187V2.66667C4.48187 1.19391 5.67578 0 7.14854 0C8.6213 0 9.81521 1.19391 9.81521 2.66667ZM5.81521 2.66667V3.33333H8.48187V2.66667C8.48187 1.93029 7.88492 1.33333 7.14854 1.33333C6.41216 1.33333 5.81521 1.93029 5.81521 2.66667ZM7.14854 9.33333C6.78035 9.33333 6.48187 9.03486 6.48187 8.66667V6.66667C6.48187 6.29848 6.78035 6 7.14854 6C7.51673 6 7.81521 6.29848 7.81521 6.66667V8.66667C7.81521 9.03486 7.51673 9.33333 7.14854 9.33333ZM4.48187 8.66667C4.48187 9.03486 4.78035 9.33333 5.14854 9.33333C5.51673 9.33333 5.81521 9.03486 5.81521 8.66667V6.66667C5.81521 6.29848 5.51673 6 5.14854 6C4.78035 6 4.48187 6.29848 4.48187 6.66667V8.66667ZM8.48187 8.66667C8.48187 9.03486 8.78035 9.33333 9.14854 9.33333C9.51673 9.33333 9.81521 9.03486 9.81521 8.66667V6.66667C9.81521 6.29848 9.51673 6 9.14854 6C8.78035 6 8.48187 6.29848 8.48187 6.66667V8.66667Z"
                                                  fill="white"/>
                                        </svg>
                                    </a>
                                @endif
                            @endif    
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
