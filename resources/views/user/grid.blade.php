@extends('layouts.admin')
@section('page-title')
    {{__('WhatsStore')}}
@endsection
@section('title')
    {{__('Store')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('User')}}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
            <a href="{{ route('store.customDomain') }}" class="btn btn-sm btn-primary btn-icon">
                {{__('Sub Domain')}}
            </a>
        </div>
        <div class="col-auto pe-0">
            <a href="{{ route('store.customDomain') }}" class="btn btn-sm btn-primary btn-icon">
                {{__('Custom Domain')}}
            </a>
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
@section('content')
    @if(\Auth::user()->type = 'super admin')
        <div class="row">
            @foreach($users as $user)
            <div class="col-md-4 col-xxl-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a href="#" data-size="md" data-url="{{ route('user.edit',$user->id) }}" data-ajax-popup="true" data-title="{{__('Edit User')}}"  class="dropdown-item"><i
                                            class="ti ti-edit"></i>
                                        <span>{{ __('Edit') }}</span></a>

                                    <a href="#" data-size="md" data-url="{{ route('plan.upgrade',$user->id) }}" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}"  class="dropdown-item"><i class="ti ti-trophy"></i>
                                        <span>{{ __('Upgrade Plan') }}</span></a>

                                    {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'. $user->id]) !!}
                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                           <i class="ti ti-trash"></i>@if($user->delete_status == 0){{__('Delete')}} @else {{__('Restore')}}@endif</a>
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" src="{{ asset(Storage::url("uploads/profile/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}" class="img-fluid rounded-circle card-avatar">
                        </div>

                        <h5 class="h6 mt-4 mb-0"> {{$user->name}}</h5>
                        <a href="#" class="d-block text-sm text-muted my-4"> {{$user->email}}</a>
                        <div class="card mb-0 mt-3">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="mb-0">{{$user->countProducts($user->id)}}</h6>
                                        <p class="text-muted text-sm mb-0">{{ __('Products')}}</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h6 class="mb-0">{{$user->countStores($user->id)}}</h6>
                                        <p class="text-muted text-sm mb-0">{{ __('Stores')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                            <div class="actions d-flex justify-content-between">
                                <span class="d-block text-sm text-muted"> {{__('Plan') }} : {{$user->currentPlan->name}}</span>
                            </div>
                            <div class="actions d-flex justify-content-between mt-1">
                                <span class="d-block text-sm text-muted">{{__('Plan Expired') }} : {{!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date):'Unlimited'}}</span>
                            </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
