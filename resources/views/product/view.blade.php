@extends('layouts.admin')
@section('page-title')
    {{__('Product')}}
@endsection
@section('title')
    {{__('Product')}}
@endsection
@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
            <a href="{{route('product.edit',$product->id)}}" class="btn btn-sm btn-primary btn-icon" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Product')}}" ><i class="ti ti-pencil text-white"></i></a>
        </div> 
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-6">
          
            <div class="card">
                <div class="card-body">
                    <!-- Product title -->
                    <h5 class="h4">{{ $product->name }}</h5>
                    <!-- Rating -->
                    <div class="row align-items-center">
                        
                        <div class="col-sm-6 text-sm-right">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <span
                                        class="badge badge-pill badge-soft-info">{{ __('ID: #') }}{{ $product->SKU }}</span>
                                </li>
                                <li class="list-inline-item">
                                    @if ($product->enable_product_variant != 'on')
                                        @if ($product->quantity == 0)
                                            <span class="badge badge-pill badge-soft-danger">
                                                {{ __('Out of stock') }}
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-soft-success">
                                                {{ __('In stock') }}
                                            </span>
                                        @endif
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Description -->
                    {!! $product->description !!}
                </div>
            </div>
        </div>
       
        <div class="col-lg-6">
            @if ($product->enable_product_variant == 'on')
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <input type="hidden" id="product_id" value="{{ $product->id }}">
                            <input type="hidden" id="variant_id" value="">
                            <input type="hidden" id="variant_qty" value="">
                            @foreach ($product_variant_names as $key => $variant)
                                <div class="col-sm-6 mb-4 mb-sm-0">
                                    <span class="d-block h6 mb-0">
                                        <th>
                                            <label for="" class="col-form-label"> {{ $variant->variant_name }}</label>

                                        </th>
                                        <select name="product[{{$key}}]" id='choices-multiple-{{$key}}'  class="form-control multi-select  pro_variants_name{{$key}}">
                                        <option value="">{{ __('Select')  }}</option>
                                            @foreach ($variant->variant_options as $key => $values)
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
                            <span class="d-block h3 mb-0 variasion_price">
                                @if ($product->enable_product_variant == 'on')
                                    {{ \App\Models\Utility::priceFormat(0) }}
                                @else
                                    {{ \App\Models\Utility::priceFormat($product->price) }}
                                @endif

                            </span>
                            {{ !empty($product->product_taxs) ? $product->product_taxs->name : '' }}
                            {{ !empty($product->product_taxs->rate) ? $product->product_taxs->rate . '%' : '' }}
                        </div>
                        <div class="col-sm-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-icon">
                                <span class="btn-inner--icon variant_qty">
                                    @if ($product->enable_product_variant == 'on')
                                        0
                                    @else
                                        {{ $product->quantity }}
                                    @endif
                                </span>
                                <span class="btn-inner--text">
                                    {{ __('Total Avl.Quantity') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product images -->
            <div class="card">
                <div class="card-body">
                    @if (!empty($product->is_cover))
                        <a href="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                            data-fancybox="product">
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                class="img-center pro_max_width1">
                        </a>
                    @else
                        <a href="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                            data-fancybox="product">
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                class="img-center pro_max_width1">
                        </a>
                    @endif
                    <div class="row mt-4">
                        @foreach ($product_image as $key => $products)
                            <div class="col-4">
                                <div class="p-3 border rounded">
                                    @if (!empty($product_image[$key]->product_images))
                                        <a href="{{ asset(Storage::url('uploads/product_image/' . $product_image[$key]->product_images)) }}"
                                            class="stretched-link" data-fancybox="product">
                                            <img alt="Image placeholder"
                                                src="{{ asset(Storage::url('uploads/product_image/' . $product_image[$key]->product_images)) }}"
                                                class="img-fluid">
                                        </a>
                                    @else
                                        <a href="{{ asset(Storage::url('uploads/product_image/default.jpg')) }}"
                                            class="stretched-link" data-fancybox="product">
                                            <img alt="Image placeholder"
                                                src="{{ asset(Storage::url('uploads/product_image/default.jpg')) }}"
                                                class="img-fluid">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
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
                        console.log(data);
                        $('.variasion_price').html(data.price);
                        $('#variant_id').val(data.variant_id);
                        $('.variant_qty').html(data.quantity);
                    }
                });
            }
        });
    </script>
@endpush
