<style>
    .shoping_count:after {
        content: attr(value);
        font-size: 14px;
        background: #273444;
        border-radius: 50%;
        padding: 1px 5px 1px 4px;
        position: relative;
        left: -5px;
        top: -10px;
    }

    @media (min-width: 768px) {
        .header-account-page {
            height: 100px;
        }
    }
</style>
<body>
@php
    if(!empty(session()->get('lang')))
    {
        $currantLang = session()->get('lang');
    }else{
        $currantLang = $store->lang;
    }
    $languages=\App\Models\Utility::languages();
@endphp
<div class="main-content">
    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            {!! $products->description !!}
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            @if(!empty($products->custom_field_1) && !empty($products->custom_value_1))
                                <div class="cart-buttons">
                                    <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_1}} : </span> {{$products->custom_value_1}}</div>
                                </div>
                            @endif
                            @if(!empty($products->custom_field_2) && !empty($products->custom_value_2))
                                <div class="cart-buttons">
                                    <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_2}} : </span> {{$products->custom_value_2}}</div>
                                </div>
                            @endif
                            @if(!empty($products->custom_field_3) && !empty($products->custom_value_3))
                                <div class="cart-buttons">
                                    <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_3}} : </span> {{$products->custom_value_3}}</div>
                                </div>
                            @endif
                            @if(!empty($products->custom_field_4) && !empty($products->custom_value_4))
                                <div class="cart-buttons">
                                    <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_4}} : </span> {{$products->custom_value_4}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    @if($products->enable_product_variant =='on')
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <input type="hidden" id="product_id" value="{{$products->id}}">
                                    <input type="hidden" id="variant_id" value="">
                                    <input type="hidden" id="variant_qty" value="">
                                    @foreach($product_variant_names as $key => $variant)
                                        <div class="col-sm-6 mb-4 mb-sm-0">
                                            <span class="d-block h6 mb-0">
                                                <th><span>{{ $variant->variant_name }}</span></th>

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
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-sm-6 mb-4 mb-sm-0">
                                    <span class="d-block h3 mb-0 variation_price">
                                         @if($products->enable_product_variant =='on')
                                            {{\App\Models\Utility::priceFormat(0)}}
                                        @else
                                            {{\App\Models\Utility::priceFormat($products->price)}}
                                        @endif
                                    </span>
                                    <div class="mt-1">
                                        <ul class="list-inline mb-0">
                                            <li class="list-inline-item">
                                                <span class="badge badge-pill badge-soft-info">{{__('ID: #')}}{{$products->SKU}}</span>
                                            </li>
                                            <li class="list-inline-item">
                                                @if($products->quantity == 0)
                                                    <span class="badge badge-pill badge-soft-danger">
                                            {{__('Out of stock')}}
                                        </span>
                                                @else
                                                    <span class="badge badge-pill badge-soft-success">
                                            {{__('In stock')}}
                                        </span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-sm-right">
                                    <button type="button" class="btn active text-link">
                                    <span class="btn-inner--icon variant_qty">
                                        @if($products->enable_product_variant =='on')
                                            0
                                        @else
                                            {{$products->quantity}}
                                        @endif
                                    </span>
                                        <span class="btn-inner--text">
                                   {{__('Total Avl.Quantity')}}
                                </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product images -->
                    <div class="card">
                        <div class="card-body">
                            @if(!empty($products->is_cover))
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$products->is_cover))}}" class="img-fluid rounded prd_img_lg">
                            @else
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-fluid rounded prd_img_lg">
                            @endif
                            <div class="row mt-4">
                                @foreach($products_image as $key => $productss)
                                    <div class="col-4">
                                        <div class="p-3 border rounded">
                                            @if(!empty($products_image[$key]->product_images))
                                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/'.$products_image[$key]->product_images))}}" class="img-fluid prd_img_sm">
                                            @else
                                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" class="img-fluid prd_img_sm">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <div class="modal-title">
                    <h6 class="mb-0" id="modelCommanModelLabel"></h6>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<!-- Core JS - includes jquery, bootstrap, popper, in-view and sticky-kit -->
<script src="{{asset('custom/js/site.core.js')}}"></script>
<!-- notify -->
<script type="text/javascript" src="{{ asset('custom/js/custom.js')}}"></script>
<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{ asset('custom/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js')}}"></script>
<!-- Page JS -->
<script src="{{asset('custom/libs/swiper/dist/js/swiper.min.js')}}"></script>
<!-- Site JS -->
<script src="{{asset('custom/js/site.js')}}"></script>
<!-- Demo JS - remove it when starting your project -->
<script src="{{asset('custom/js/demo.js')}}"></script>
@php
    $store_settings = \App\Models\Store::where('slug',$store->slug)->first();
@endphp

<script async src="https://www.googletagmanager.com/gtag/js?id={{$store_settings->google_analytic}}"></script>

{!! $store_settings->storejs !!}

<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', '{{ $store_settings->google_analytic }}');
</script>




<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{ !empty($store_settings->facebook_pixel)}}');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id={{$store_settings->facebook_pixel}}&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->


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
                    $(".shoping_count").attr("value", response.item_count);
                } else {
                    show_toastr('Error', response.error, 'error');
                }
            },
            error: function (result) {
                console.log('error');
            }
        });
    });

    $(document).on('click', '.prd_img_sm', function () {
        var new_src = $(this).attr("src");
        $(".prd_img_lg").attr("src", new_src);
    });
    $(document).on('change', '#pro_variants_name', function () {
        var variants = [];
        $(".variant-selection").each(function (index, element) {
            variants.push(element.value);
        });
        if (variants.length > 0) {
            $.ajax({
                url: '{{route('get.products.variant.quantity')}}',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    variants: variants.join(' : '),
                    product_id: $('#product_id').val()
                },

                success: function (data) {
                    $('.variation_price').html(data.price);
                    $('#variant_id').val(data.variant_id);
                    $('.variant_qty').html(data.quantity);
                }
            });
        }
    });
</script>
@if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif
@if(Session::has('error'))
    <script>
        show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif
</body>
</html>

