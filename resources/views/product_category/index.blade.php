@extends('layouts.admin')
@section('page-title')
    {{__('Product Category')}}
@endsection
@section('title')
    {{__('Product Category')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Product Category') }}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create New Category')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Create New Category')}}" data-url="{{ route('product_categorie.create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    </div>
    
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple ">
                            <thead>
                                <tr>
                                  
                                    <th scope="col" class="sort" data-sort="name">{{ __('Categorie Name') }}
                                    </th>
                                    <th class="text-right" width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_categorys as $product_category)
                                    <tr data-name="{{ $product_category->name }}">
                                        <td>{{ $product_category->name }}</td>
                                        </td>
                                        <td class="Action text-end">
                                            <span>
                                                
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="md" data-url="{{ route('product_categorie.edit', $product_category->id) }}" data-ajax-popup="true" data-title="{{__('Edit Product Category')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Product Category')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product_categorie.destroy', $product_category->id],'id'=>'delete-form-'.$product_category->id]) !!}
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
        $(document).ready(function () {
            $(document).on('keyup', '.search-user', function () {
                var value = $(this).val();
                $('.employee_tableese tbody>tr').each(function (index) {
                    var name = $(this).attr('data-name').toLowerCase();
                    if (name.includes(value.toLowerCase())) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
        $('#search'). keydown(function (e) {
            if (e. keyCode == 13) {
                e. preventDefault();
                return false;
            }
        });
    </script>
@endpush
