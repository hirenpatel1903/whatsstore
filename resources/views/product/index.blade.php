@extends('layouts.admin')
@section('page-title')
    {{ __('Product') }}
@endsection
@section('title')
    {{ __('Product') }}
@endsection
@section('breadcrumb')
    
    <li class="breadcrumb-item active" aria-current="page">{{ __('Product') }}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
       
         

        <div class="col-auto pe-0">
             <a href="{{route('product.export',$store_id->id)}} " class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
        </div>

         <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Import')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Import customer CSV file')}}" data-url="{{route('product.file.import')}}">
                <i class="ti ti-file-import text-white"></i>
            </a>
        </div>
        
        <div class="col-auto pe-0">
             <a href="{{ route('product.grid') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Grid View')}}">
                <i class="ti ti-grid-dots text-white"></i>
            </a>
        </div>


        <div class="col-auto pe-0">
            <a href="{{ route('product.create') }}" class="btn btn-sm btn-primary btn-icon" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Product')}}" ><i class="ti ti-plus text-white"></i></a>
        </div> 
    </div>

@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Stock') }}</th>
                                    <th>{{ __('Created at') }}</th>
                                    <th class="text-right" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                    @if (!empty($product->is_cover))
                                                        <img alt="Image placeholder"
                                                            src="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                                            class="rounded-circle" alt="images">
                                                    @else
                                                        <img alt="Image placeholder"
                                                            src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                                            class="rounded-circle" alt="images">
                                                    @endif
                                                <div class="ms-3">
                                                    <a href="{{ route('product.show', $product->id) }}"
                                                        class="name h6 mb-0 text-sm">
                                                        {{ $product->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td> {{ !empty($product->product_category()) ? $product->product_category() : '-' }}
                                        </td>
                                        <td>
                                            @if ($product->enable_product_variant == 'on')
                                                {{ __('In Variant') }}
                                            @else
                                                {{ \App\Models\Utility::priceFormat($product->price) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->enable_product_variant == 'on')
                                                {{ __('In Variant') }}
                                            @else
                                                {{ $product->quantity }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->enable_product_variant == 'on')
                                                <span class="badge bg-success p-2 px-3 rounded">
                                                    {{ __('In Variant') }}
                                                </span>
                                            @else
                                                @if ($product->quantity == 0)
                                                    <span class="badge bg-danger p-2 px-3 rounded">
                                                        {{ __('Out of stock') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary p-2 px-3 rounded">
                                                        {{ __('In stock') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ \App\Models\Utility::dateFormat($product->created_at) }}
                                        </td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('product.show', $product->id) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-toggle="tooltip" data-original-title="{{ __('View') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('View') }}"><i
                                                            class="ti ti-eye text-white"></i></a>
                                                </div>

                                                <div class="action-btn btn-info ms-2">
                                                    <a href="{{ route('product.edit', $product->id) }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit') }}"><i
                                                            class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
                                                            <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                                               <span class="text-white"> <i class="ti ti-trash"></i></span>
                                                        {!! Form::close() !!}
                                                    </div>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
@endpush
