@extends('storefront.layout.theme1')
@section('page-title')
    {{__('Customer Home')}}
@endsection
@section('content')
@section('head-title')
    {{__('Welcome').', '.\Illuminate\Support\Facades\Auth::guard('customers')->user()->name}}
@endsection
@section('content')
    <!-- <div class="course-page hero-section">
        <div class="container-lg">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="course-page-text">
                        <div class="course-category">
                            <div class="course-back">
                                <a href="{{route('store.slug',$store->slug)}}">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                    {{__('Back to home')}}
                                </a>
                            </div>
                            <div class="design-span">
                                <span>{{!empty($course->category_id->name)?$course->category_id->name:''}}</span>
                            </div>
                        </div>
                        <div class="category-heading">
                            <h2>{{__('Products you purchased')}}</h2>
                            <p>{{__('Lorem Ipsum is simply dummy text of the printing and typesetting industry')}}. </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    @if($storethemesetting['enable_header_img'] == 'on')
        <section class="slice slice-xl bg-cover bg-size--cover home-banner" data-offset-top="#header-main" style="background-image: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['header_img'])?$storethemesetting['header_img']:'home-banner.png')))}}); background-position: center center;">
            <span class="mask bg-dark opacity-3"></span>
            <div class="container py-6">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="h1 text-white store-title">
                            {{__('Products you purchased')}}
                        </h2>
                        
                        <a href="{{route('store.slug',$store->slug)}}" class="btn btn-white btn-white btn-icon rounded-pill hover-translate-y-n3 mt-4" id="pro_scroll">
                            <span class="btn-inner--text">{{__('Back to home')}}</span>
                            <span class="btn-inner--icon"><i class="fas fa-angle-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <section class="slice slice-lg delimiter-bottom">
        <div class="container">
            <div class="col-lg-12">
            <div class="row">
                @if(!empty($purchased_products) && count($purchased_products) > 0)
                    @foreach($purchased_products as $product)
                        @if(in_array($product->id,Auth::guard('customers')->user()->purchasedProducts()))
                                
                                   <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                                                    <div class="card card-fluid card-product">
                                                        <div class="card-image">
                                                            
                                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">   
                                                                @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                                                @else
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <span class="static-rating static-rating-sm">
                                                                @if($store->enable_rating == 'on')
                                                                    @for($i =1;$i<=5;$i++)
                                                                        @php
                                                                            $icon = 'fa-star';
                                                                            $color = '';
                                                                            $newVal1 = ($i-0.5);
                                                                            if($product->product_rating() < $i && $product->product_rating() >= $newVal1)
                                                                            {
                                                                                $icon = 'fa-star-half-alt';
                                                                            }
                                                                            if($product->product_rating() >= $newVal1)
                                                                            {
                                                                                $color = 'text-warning';
                                                                            }
                                                                        @endphp
                                                                        <i class="star fas {{$icon .' '. $color}}"></i>
                                                                    @endfor
                                                                @endif
                                                            </span>
                                                            <h6>
                                                                <a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    {{$product->name}}
                                                                </a>
                                                            </h6>
                                                            <p class="text-sm">
                                                                <span class="td-gray">{{__('Category')}}:</span> {{$product->product_category()}}
                                                            </p>
                                                            <div class="product-price mt-3">
                                                            <span class="card-price t-black15">
                                                                @if($product->enable_product_variant == 'on')
                                                                    {{__('In variant')}}
                                                                @else
                                                                    {{\App\Models\Utility::priceFormat($product->price)}}
                                                                @endif
                                                            </span>
                                                                @if($product->enable_product_variant == 'on')
                                                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="action-item pcart-icon bg-primary">
                                                                        <i class="fas fa-shopping-basket"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="action-item pcart-icon bg-primary add_to_cart" data-id="{{$product->id}}">
                                                                        <i class="fas fa-shopping-basket"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="actions card-product-actions">
                                                            @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                                @if($wishlist[$product->id]['product_id'] != $product->id)
                                                                    <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$product->id}}" data-id="{{$purchased_products->id}}">
                                                                        <i class="far fa-heart"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="action-item wishlist-icon bg-light-gray" data-id="{{$product->id}}" disabled>
                                                                        <i class="fas fa-heart"></i>
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                    <i class="far fa-heart"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>     
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td colspan="7">
                            <div class="text-center">
                                <i class="fas fa-folder-open text-gray" style="font-size: 48px;"></i>
                                <h2>{{ __('Opps...') }}</h2>
                                <h6> {!! __('No data Found.') !!} </h6>
                            </div>
                        </td>
                    </tr>
                @endif
            </div>
        </div>
        </div>
    </section>
@endsection
@push('script-page')
    <script>
        $(".add_to_cart").click(function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var variants = [];
            $(".variant-selection").each(function (index, element) {
                variants.push(element.value);
            });

            if (jQuery.inArray('', variants) != -1) {
                show_toastr('Error', "{{ __('Please select all option.') }}", 'error');
                return false;
            }
            var variation_ids = $('#variant_id').val();

            $.ajax({
                url: '{{route('user.addToCart', ['__product_id',$store->slug,'variation_id'])}}'.replace('__product_id', id).replace('variation_id', variation_ids ?? 0),
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    variants: variants.join(' : '),
                },
                success: function (response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.success, 'success');
                        $("#shoping_counts").html(response.item_count);
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (result) {
                    console.log('error');
                }
            });
        });

        $(document).on('click', '.add_to_wishlist', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: '{{route('store.addtowishlist', [$store->slug,'__product_id'])}}'.replace('__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {

                    if (response.status == "Success") {

                        show_toastr('Success', response.message, 'success');
                        $('.wishlist_' + response.id).removeClass('add_to_wishlist');
                        $('.wishlist_' + response.id).html('<i class="fas fa-heart"></i>');
                        $('.wishlist_count').html(response.count);
                    } else {
                        console.log(response.status);
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (result) {
                }
            });
        });

        $(".productTab").click(function (e) {
            e.preventDefault();
            $('.productTab').removeClass('active')

        });

        $("#pro_scroll").click(function () {
            $('html, body').animate({
                scrollTop: $("#pro_items").offset().top
            }, 1000);
        });
    </script>
@endpush
