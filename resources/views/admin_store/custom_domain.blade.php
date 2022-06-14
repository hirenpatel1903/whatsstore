@extends('layouts.admin')
@section('page-title')
    {{__('Custom Domain')}}
@endsection
@section('title')
    {{__('Domain')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('store-resource.index') }}">{{ __('Store') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Custom Domain') }}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
            <a href="{{ route('store.subDomain') }}" class="btn btn-sm btn-primary btn-icon">
                {{__('Sub Domain')}}
            </a>
        </div>
        <div class="col-auto pe-0">
            <div class="col-auto pe-0">
                <a href="{{ route('store.grid') }}" class="btn btn-sm btn-primary btn-icon" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Grid View')}}" ><i class="ti ti-grid-dots text-white"></i></a>
            </div>
        </div>
        <div class="col-auto pe-0">
            <div class="col-auto pe-0">
                <a href="{{ route('store-resource.index') }}" class="btn btn-sm btn-primary btn-icon" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('List View')}}" ><i class="fas fa-list text-white"></i></a>
            </div>
        </div>
        <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create New Store')}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Store')}}" data-url="{{route('store-resource.create')}}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    </div>
@endsection
@section('filter')
@endsection
@push('css-page')
@endpush
@section('content')
    <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <h6 class="my-2">{{__('If you\'re using cPanel or Plesk then you need to manually add below custom domain in your server with the same root directory as the script\'s installation. and user need to point their custom domain A record with your server IP '.$serverIp.'')}}</h6>
                <div class="table-responsive">
                    <table class="table mb-0 pc-dt-simple">
                        <thead>
                            <tr>
                                <th>{{ __('Custom Domain Name')}}</th>
                                <th>{{ __('Store Name')}}</th>
                                <th>{{ __('Email')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>
                                        {{$store->domains}}
                                    </td>
                                    <td>
                                        {{$store->name}}
                                    </td>
                                    <td>
                                        {{($store->email)}}
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
