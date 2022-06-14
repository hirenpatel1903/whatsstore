@extends('layouts.admin')
@section('page-title')
    {{__('Plans')}}
@endsection
@php
    $dir= asset(Storage::url('uploads/plan'));
@endphp
@section('breadcrumb')
    
    <li class="breadcrumb-item active" aria-current="page">{{ __('Plans') }}</li>
@endsection
@section('title')
    {{__('Plans')}}
@endsection
@section('action-btn')
    @if(Auth::user()->type == 'super admin')
        @if((isset($admin_payments_setting['is_stripe_enabled']) && $admin_payments_setting['is_stripe_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paypal_enabled']) && $admin_payments_setting['is_paypal_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paystack_enabled']) && $admin_payments_setting['is_paystack_enabled'] == 'on')
            || (isset($admin_payments_setting['is_flutterwave_enabled']) && $admin_payments_setting['is_flutterwave_enabled'] == 'on')
            || (isset($admin_payments_setting['is_razorpay_enabled']) && $admin_payments_setting['is_razorpay_enabled'] == 'on')
            || (isset($admin_payments_setting['is_mercado_enabled']) && $admin_payments_setting['is_mercado_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paytm_enabled']) && $admin_payments_setting['is_paytm_enabled'] == 'on')
            || (isset($admin_payments_setting['is_mollie_enabled']) && $admin_payments_setting['is_mollie_enabled'] == 'on')
            || (isset($admin_payments_setting['is_skrill_enabled']) && $admin_payments_setting['is_skrill_enabled'] == 'on')
            || (isset($admin_payments_setting['is_coingate_enabled']) && $admin_payments_setting['is_coingate_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paymentwall_enabled']) && $admin_payments_setting['is_paymentwall_enabled'] == 'on')
        )
        <div class="row  m-1"> 
            <div class="col-auto pe-0">
                <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Plan')}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Add Plan')}}" data-url="{{route('plans.create')}}">
                    <i class="ti ti-plus text-white"></i>
                </a>
            </div>
        </div>
        @endif
    @endif
@endsection
@section('content')
    <div class="row">
        @foreach ($plans as $plan)
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s" style="
                                    visibility: visible;
                                    animation-delay: 0.2s;
                                    animation-name: fadeInUp;
                                  ">
                    <div class="card-body">
                        <span class="price-badge bg-primary">{{ $plan->name }}</span>
                        @if( \Auth::user()->type == 'super admin')
                            <div class="d-flex flex-row-reverse m-0 p-0 ">
                                <div class="action-btn bg-primary ms-2">
                                  <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Plan')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Edit Plan')}}" data-url="{{ route('plans.edit',$plan->id) }}"><i class="ti ti-pencil text-white"></i></a>
                                </div> 
                            </div>
                        @endif
                        @if (\Auth::user()->type == 'Owner' && \Auth::user()->plan == $plan->id)
                            <div class="d-flex flex-row-reverse m-0 p-0 ">
                                <span class="d-flex align-items-center ">
                                    <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                    <span class="ms-2">{{ __('Active') }}</span>
                                </span>
                            </div>
                        @endif
                        <h3 class="mb-4 f-w-600">
                            {{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}{{ $plan->price . ' / ' . __(\App\Models\Plan::$arrDuration[$plan->duration]) }}</small>
                            </h1>
                            <p class="mb-0">
                                {{ __('Trial : ') . $plan->trial_days . __(' Days') }}<br />
                            </p>
                            @if ($plan->description)
                                <p class="mb-0">
                                    {{ $plan->description }}<br />
                                </p>
                            @endif
                            <div class="row mt-1">
                                <ul class="list-unstyled my-5">
                                  <li>
                                     @if ($plan->enable_custdomain == 'on')
                                        <span class="theme-avtar">
                                          <i class="text-primary ti ti-circle-plus"></i
                                        ></span>
                                            {{ __('Custom Domain') }}                                        
                                    @else
                                        <span class="theme-avtar">
                                          <i class="text-danger ti ti-circle-plus"></i
                                        ></span>
                                       {{ __('Custom Domain') }}
                                        
                                    @endif
                                    </li>
                                    <li>
                                     @if ($plan->enable_custsubdomain == 'on')
                                        <span class="theme-avtar">
                                          <i class="text-primary ti ti-circle-plus"></i
                                        ></span>
                                            {{ __('Sub Domain') }}                                        
                                    @else
                                        <span class="theme-avtar">
                                          <i class="text-danger ti ti-circle-plus"></i
                                        ></span>
                                       {{ __('Sub Domain') }}
                                        
                                    @endif
                                    </li>
                                    <li>
                                     @if ($plan->shipping_method == 'on')
                                        <span class="theme-avtar">
                                          <i class="text-primary ti ti-circle-plus"></i
                                        ></span>
                                            {{ __('Shipping Domain') }}                                        
                                    @else
                                        <span class="theme-avtar">
                                          <i class="text-danger ti ti-circle-plus"></i
                                        ></span>
                                       {{ __('Shipping Domain') }}
                                        
                                    @endif
                                    </li>
                                </ul>                            
                            </div>

                            <div class="row mb-3">
                                <div class="col-6 text-center">
                                    @if ($plan->max_products == '-1')
                                        <span class="h5 mb-0">{{ __('Unlimited') }}</span>
                                    @else
                                        <span class="h5 mb-0">{{ $plan->max_products }}</span>
                                    @endif
                                    <span class="d-block text-sm">{{ __('Products') }}</span>
                                </div>
                                <div class="col-6 text-center">
                                    <span class="h5 mb-0">
                                        @if ($plan->max_stores == '-1')
                                            <span class="h5 mb-0">{{ __('Unlimited') }}</span>
                                        @else
                                            <span class="h5 mb-0">{{ $plan->max_stores }}</span>
                                        @endif
                                    </span>
                                    <span class="d-block text-sm">{{ __('Store') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                @if (\Auth::user()->type != 'super admin')
                                    @if (\Auth::user()->plan == $plan->id && date('Y-m-d') < \Auth::user()->plan_expire_date && \Auth::user()->is_trial_done != 1)
                                        <h5 class="h6 my-4">
                                            {{ __('Expired : ') }}
                                            {{ \Auth::user()->plan_expire_date? \App\Models\Utility::dateFormat(\Auth::user()->plan_expire_date): __('Unlimited') }}
                                        </h5>
                                    @elseif(\Auth::user()->plan == $plan->id && !empty(\Auth::user()->plan_expire_date) && \Auth::user()->plan_expire_date < date('Y-m-d'))
                                        <div class="col-12">
                                            <p class="server-plan font-weight-bold text-center mx-sm-5">
                                                {{ __('Expired') }}
                                            </p>
                                        </div>
                                    @else
                                        <div class="{{ $plan->id == 1 ? 'col-12' : 'col-8' }}">
                                            <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                class="btn  btn-primary d-flex justify-content-center align-items-center ">{{ __('Subscribe') }}
                                                <i class="fas fa-arrow-right m-1"></i></a>
                                            <p></p>
                                        </div>
                                    @endif
                                @endif
                                @if (\Auth::user()->type != 'super admin' && \Auth::user()->plan != $plan->id)
                                    @if ($plan->id != 1)
                                        @if (\Auth::user()->requested_plan != $plan->id)
                                            <div class="col-4">
                                                <a href="{{ route('send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                    class="btn btn-primary btn-icon m-1"
                                                    data-title="{{ __('Send Request') }}" data-toggle="tooltip">
                                                    <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                                                </a>
                                            </div>
                                        @else
                                            <div class="col-4">
                                                <a href="{{ route('request.cancel', \Auth::user()->id) }}"
                                                    class="btn btn-icon m-1 btn-danger"
                                                    data-title="{{ __('Cancle Request') }}" data-toggle="tooltip">
                                                    <span class="btn-inner--icon"><i class="fas fa-times"></i></span>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple ">
                            <thead>
                                <tr>
                                    <th> {{ __('Order Id') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Plan Name') }}</th>
                                    <th> {{ __('Price') }}</th>
                                    <th> {{ __('Payment Type') }}</th>
                                    <th> {{ __('Status') }}</th>
                                    <th> {{ __('Coupon') }}</th>
                                    <th> {{ __('Invoice') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                        <td>{{ $order->user_name }}</td>
                                        <td>{{ $order->plan_name }}</td>
                                        <td>{{ env('CURRENCY_SYMBOL') . $order->price }}</td>
                                        <td>{{ $order->payment_type }}</td>
                                        <td>
                                            @if ($order->payment_status == 'succeeded')
                                                <i class="mdi mdi-circle text-success"></i>
                                                {{ ucfirst($order->payment_status) }}
                                            @else
                                                <i class="mdi mdi-circle text-danger"></i>
                                                {{ ucfirst($order->payment_status) }}
                                            @endif
                                        </td>

                                        <td>{{ !empty($order->total_coupon_used)? (!empty($order->total_coupon_used->coupon_detail)? $order->total_coupon_used->coupon_detail->code: '-'): '-' }}
                                        </td>

                                        <td class="text-center">
                                            @if ($order->receipt != 'free coupon' && $order->payment_type == 'STRIPE')
                                                <a href="{{ $order->receipt }}" title="Invoice" target="_blank"
                                                    class=""><i class="fas fa-file-invoice"></i> </a>
                                            @elseif($order->receipt == 'free coupon')
                                                <p>{{ __('Used') . '100 %' . __('discount coupon code.') }}</p>
                                            @elseif($order->payment_type == 'Manually')
                                                <p>{{ __('Manually plan upgraded by super admin') }}</p>
                                            @else
                                                -
                                            @endif
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

@push('scripts')
    <script>
        $(document).ready(function () {
            var tohref = '';
            @if(Auth::user()->is_register_trial == 1)
                tohref = $('#trial_{{ Auth::user()->interested_plan_id }}').attr("href");
            @elseif(Auth::user()->interested_plan_id != 0)
                tohref = $('#interested_plan_{{ Auth::user()->interested_plan_id }}').attr("href");
            @endif

            if (tohref != '') {
                window.location = tohref;
            }
        });
    </script>
@endpush
