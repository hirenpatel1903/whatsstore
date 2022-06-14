@extends('layouts.admin')
@section('page-title')
    {{__('Product')}}
@endsection
@section('title')
    {{__('Product')}}
@endsection
@section('breadcrumb')
@endsection
@section('action-btn')
    <div class="row  m-1">
        
        <div class="col-auto pe-0">
             <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('List View')}}">
                <i class="ti ti-list text-white"></i>
            </a>
        </div>


        <div class="col-auto pe-0">
            <a href="{{ route('product.create') }}" class="btn btn-sm btn-primary btn-icon" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Product')}}" ><i class="ti ti-plus text-white"></i></a>
        </div> 
    </div>
@endsection
@section('breadcrumb')
    
    <li class="breadcrumb-item active" aria-current="page">{{ __('Product') }}</li>
@endsection
@section('content')
    <div class="row">
        @foreach ($products as $product)
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card text-white text-center">
                    <div class="card-header border-0 pb-0">
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a href="{{ route('product.show', $product->id) }}" class="dropdown-item"><i
                                            class="ti ti-eye"></i>
                                        <span>{{ __('View') }}</span></a>
                                    <a href="{{ route('product.edit', $product->id) }}" class="dropdown-item"><i
                                            class="ti ti-edit"></i>
                                        <span>{{ __('Edit') }}</span></a>

                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]) !!}
                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                            <i class="ti ti-trash"></i><span>{{ __('Delete') }} </span> 

                                    </a>

                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (!empty($product->is_cover))
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                class="img-fluid card-avatar" alt="images">
                        @else
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                class="img-fluid card-avatar" alt="images">
                        @endif
                        <h4 class="text-primary mt-2"> <a
                                href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a></h4>
                        <h4 class="text-muted">
                            <small>{{ \App\Models\Utility::priceFormat($product->price) }}</small>
                        </h4>
                        @if ($product->quantity == 0)
                            <span class="badge bg-danger p-2 px-3 rounded">
                                {{ __('Out of stock') }}
                            </span>
                        @else
                            <span class="badge bg-primary p-2 px-3 rounded">
                                {{ __('In stock') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-md-3">
            <a href="{{ route('product.create') }}" class="btn-addnew-project" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Product')}}"><i class="ti ti-plus text-white"></i>
                <div class="bg-primary proj-add-icon">
                    <i class="ti ti-plus"></i>
                </div>
                <h6 class="mt-4 mb-2">New Product</h6>
                <p class="text-muted text-center">Click here to add New Product</p>
            </a>
        </div>

    </div>
@endsection
