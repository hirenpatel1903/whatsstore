@extends('layouts.admin')
@php
    $dir= asset(Storage::url('uploads/plan'));
@endphp
@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script type="text/javascript">
        @if($plan->price > 0.0 && isset($admin_payments_details['is_stripe_enabled']) && $admin_payments_details['is_stripe_enabled']=='on')
        var stripe = Stripe('{{ $admin_payments_details['stripe_key'] }}');
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '14px',
                color: '#32325d',
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                    show_toastr('Error', result.error.message, 'error');
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }

        @endif
        function preparePayment(ele, payment) {
            var coupon = $(ele).closest('.row').find('.coupon').val();
            var amount = 0;
            $.ajax({
                url: '{{route('plan.prepare.amount')}}',
                datType: 'json',
                data: {
                    plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                    coupon: coupon
                },
                success: function (data) {

                    if (data.is_success == true) {
                        amount = data.price;
                        $('#coupon_use_id').val(data.coupon_id);
                        if (payment == 'paystack') {
                            payWithPaystack(amount);
                        }
                        if (payment == 'flutterwave') {
                            payWithRave(amount);
                        }
                        if (payment == 'razorpay') {
                            payRazorPay(amount);
                        }
                        if (payment == 'mercado') {
                            payMercado(amount);
                        }
                    } else {
                        show_toastr('Error', 'Paymenent request failed', 'error');
                    }

                }
            })
        }
        @if(isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled']=='on')
        function payWithPaystack(amount) {
            var coupon_id = $('#coupon_use_id').val();
            var paystack_callback = "{{ url('/paystack-plan') }}";
            var handler = PaystackPop.setup({
                key: '{{ $admin_payments_details['paystack_public_key']  }}',
                email: '{{Auth::user()->email}}',
                amount: amount * 100,
                currency: '{{env('CURRENCY')}}',
                ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                    1
                ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                metadata: {
                    custom_fields: [{
                        display_name: "Mobile Number",
                        variable_name: "mobile_number",
                    }]
                },

                callback: function (response) {
                    {{--console.log(paystack_callback +'/'+ response.reference + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}');--}}
                        window.location.href = paystack_callback + '/' + response.reference + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                },
                onClose: function () {
                    alert('window closed');
                }
            });
            handler.openIframe();

        }
        @endif
        @if(isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled']=='on')
        {{-- Flutterwave JAVASCRIPT FUNCTION --}}
        function payWithRave(amount) {
            var coupon_id = $('#coupon_use_id').val();
            var API_publicKey = '{{ $admin_payments_details['flutterwave_public_key']  }}';
            var nowTim = "{{ date('d-m-Y-h-i-a') }}";
            var flutter_callback = "{{ url('/flutterwave-plan') }}";
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: '{{Auth::user()->email}}',
                amount: amount,
                currency: '{{env('CURRENCY')}}',
                txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                {{ date('Y-m-d') }},
                meta: [{
                    metaname: "payment_id",
                    metavalue: "id"
                }],
                onclose: function () {
                },
                callback: function (response) {

                    var txref = response.tx.txRef;

                    if (
                        response.tx.chargeResponseCode == "00" ||
                        response.tx.chargeResponseCode == "0"
                    ) {
                        window.location.href = flutter_callback + '/' + txref + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                    } else {
                        // redirect to a failure page.
                    }
                    x.close(); // use this to close the modal immediately after payment.
                }
            });
        }
        @endif
        @if(isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled']=='on')
        {{-- Razorpay JAVASCRIPT FUNCTION --}}
        @php
            $logo         =asset(Storage::url('uploads/logo/'));
             if(\Auth::user()->type=="Super Admin")
                {
                    $company_logo=Utility::get_superadmin_logo();
                }
                else
                {
                    $company_logo=Utility::get_company_logo();
                }
        @endphp
        function payRazorPay(amount) {
            var razorPay_callback = '{{url('razorpay-plan')}}';
            var totalAmount = amount * 100;
            var coupon_id = $('#coupon_use_id').val();
            var options = {
                "key": "{{ $admin_payments_details['razorpay_public_key']  }}", // your Razorpay Key Id
                "amount": totalAmount,
                "name": 'Plan',
                "currency": '{{env('CURRENCY')}}',
                "description": "",
                "image": "{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}",
                "handler": function (response) {
                    window.location.href = razorPay_callback + '/' + response.razorpay_payment_id + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id;
                },
                "theme": {
                    "color": "#528FF0"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
        }
        @endif
        @if(isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled']=='on')
        {{-- Mercado JAVASCRIPT FUNCTION --}}
        function payMercado(amount) {
            var coupon_id = $('#coupon_use_id').val();
            var data = {
                coupon_id: coupon_id,
                total_price: amount,
                plan: {{$plan->id}},
            }
            console.log(data);
            $.ajax({
                url: '{{ route('mercadopago.prepare.plan') }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {
                        window.location.href = data.url;
                    } else {
                        show_toastr("Error", data.error, data["status"]);
                    }
                }
            });
        }

        @endif
        $(document).ready(function () {
            $(document).on('click', '.apply-coupon', function () {
                var ele = $(this);
                var coupon = ele.closest('.row').find('.coupon').val();

                $.ajax({
                    url: '{{route('apply.coupon')}}',
                    datType: 'json',
                    data: {
                        plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                        coupon: coupon
                    },
                    success: function (data) {
                        $('.final-price').text(data.final_price);
                        $('#final_price_pay').val(data.price);
                        $('#mollie_total_price').val(data.price);
                        $('#skrill_total_price').val(data.price);
                        $('#coingate_total_price').val(data.price);
                        $('#stripe_coupon, #paypal_coupon, #skrill_coupon,#coingate_coupon').val(coupon);
                        if (data.is_success == true) {
                            show_toastr('Success', data.message, 'success');
                        } else if (data.is_success == false) {
                            show_toastr('Error', data.message, 'error');
                        } else {
                            show_toastr('Error', 'Coupon code is required', 'error');
                        }
                    }
                })
            });
        });

    </script>
    <script>
       var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: '#useradd-sidenav',
        offset: 300,

    })
   $(".list-group-item").click(function(){
          $('.list-group-item').filter(function(){
                return this.href == id;
        }).parent().removeClass('text-primary');
    });
</script>

@endpush
@php
    $dir= asset(Storage::url('uploads/plan'));
    $dir_payment= asset(Storage::url('uploads/payments'));
@endphp
@section('page-title')
    {{__('Order Summary')}}
@endsection
@section('title')
    {{__('Order Summary')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">{{ __('Plan') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Order Summary') }}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row ">

        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="sticky-top" style="top:30px">
                        <div class="card">
                            <div class="list-group list-group-flush" id="useradd-sidenav">
                                @if(isset($admin_payments_details['is_stripe_enabled']) && $admin_payments_details['is_stripe_enabled']=='on')
                                    <a href="#stripe-payment" class="list-group-item list-group-item-action">{{__('Stripe')}} <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if(isset($admin_payments_details['is_paypal_enabled']) && $admin_payments_details['is_paypal_enabled']=='on')
                                    <a href="#paypal-payment" class="list-group-item list-group-item-action">{{__('Paypal')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if(isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled']=='on')
                                    <a href="#paystack-payment" class="list-group-item list-group-item-action">{{__('Paystack')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled']=='on')
                                    <a href="#flutterwave-payment" class="list-group-item list-group-item-action">{{__('Flutterwave')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled']=='on')
                                    <a href="#razorpay-payment" class="list-group-item list-group-item-action">{{__('Razorpay')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                
                                @if(isset($admin_payments_details['is_paytm_enabled']) && $admin_payments_details['is_paytm_enabled']=='on')
                                    <a href="#paytm-payment" class="list-group-item list-group-item-action">{{__('Paytm')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled']=='on')
                                    <a href="#mercado-payment" class="list-group-item list-group-item-action">{{__('Mercado Pago')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                                @if(isset($admin_payments_details['is_mollie_enabled']) && $admin_payments_details['is_mollie_enabled']=='on')
                                    <a href="#mollie-payment" class="list-group-item list-group-item-action">{{__('Mollie')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_skrill_enabled']) && $admin_payments_details['is_skrill_enabled']=='on')
                                    <a href="#skrill-payment" class="list-group-item list-group-item-action">{{__('Skrill')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_coingate_enabled']) && $admin_payments_details['is_coingate_enabled']=='on')
                                    <a href="#coingate-payment" class="list-group-item list-group-item-action">{{__('CoinGate')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif

                                @if(isset($admin_payments_details['is_paymentwall_enabled']) && $admin_payments_details['is_paymentwall_enabled']=='on')
                                    <a href="#paymentwall-payment" class="list-group-item list-group-item-action">{{__('Paymentwall')}}<div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                @endif
                            </div>
                        </div>
                            

                        <div class="mt-5">
                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s" style="
                                                                        visibility: visible;
                                                                        animation-delay: 0.2s;
                                                                        animation-name: fadeInUp;
                                                                      ">
                                <div class="card-body">
                                    <span class="price-badge bg-primary">{{ $plan->name }}</span>
                                    @if (\Auth::user()->type == 'Owner' && \Auth::user()->plan == $plan->id)
                                        <div class="d-flex flex-row-reverse m-0 p-0 ">
                                            <span class="d-flex align-items-center ">
                                                <i class="f-10 lh-1 fas fa-circle text-success"></i>
                                                <span class="ms-2">{{ __('Active') }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    <div class="text-end">
                                        <div class="">
                                            @if (\Auth::user()->type == 'super admin')
                                                <a title="Edit Plan" data-size="lg" href="#" class="action-item"
                                                    data-url="{{ route('plans.edit', $plan->id) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Edit Plan') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Plan') }}"><i class="fas fa-edit"></i></a>
                                            @endif
                                        </div>
                                    </div>

                                    <h3 class="mb-4 f-w-600  ">
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
                                            <ul class="plan-detail">
                                                @if ($plan->enable_custdomain == 'on')
                                                    <li>{{ __('Custom Domain') }}</li>
                                                @else
                                                    <div>{{ __('Custom Domain') }}</div>
                                                @endif
                                                @if ($plan->enable_custsubdomain == 'on')
                                                    <li>{{ __('Sub Domain') }}</li>
                                                @else
                                                    <div>{{ __('Sub Domain') }}</div>
                                                @endif
                                                @if ($plan->shipping_method == 'on')
                                                    <li>{{ __('Shipping Method') }}</li>
                                                @else
                                                    <div>{{ __('Shipping Method') }}</div>
                                                @endif
                                                @if ($plan->additional_page == 'on')
                                                    <li>{{ __('Additional Page') }}</li>
                                                @else
                                                    <div>{{ __('Additional Page') }}</div>
                                                @endif
                                                @if ($plan->blog == 'on')
                                                    <li>{{ __('Blog') }}</li>
                                                @else
                                                    <div>{{ __('Blog') }}</div>
                                                @endif
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
                                </div>
                            </div>
                        </div>


                    </div>
                    


                </div>
                <div class="col-xl-9">
                    @if(isset($admin_payments_details['is_stripe_enabled']) && $admin_payments_details['is_stripe_enabled']=='on')
                        <div class="card active" id="stripe-payment">
                            <div class="card-header">
                                <h5>{{('Stripe')}}</h5>
                            </div>
                            <div class="card-body">
                                <form role="form" action="{{ route('stripe.payment') }}" method="post" class="require-validation" id="payment-form">
                                    @csrf
                                    <div class="border p-3 mb-3 rounded stripe-payment-div">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="custom-radio">
                                                    <label class="font-16 font-weight-bold">{{__('Credit / Debit Card')}}</label>
                                                </div>
                                                <p class="mb-0 pt-1 text-sm">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}</p>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="card-name-on">{{__('Name on card')}}</label>
                                                    <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="{{\Auth::user()->name}}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="card-element"></div>
                                                <div id="card-errors" role="alert"></div>
                                            </div>
                                            <div class="col-md-10">
                                                <br>
                                                <div class="form-group">
                                                    <label for="stripe_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="stripe_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="text-sm-end mr-2">
                                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                    <button class="btn btn-print-invoice  btn-primary m-b-10 m-r-10" type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                        

                    @if(isset($admin_payments_details['is_paypal_enabled']) && $admin_payments_details['is_paypal_enabled']=='on')
                         <div id="paypal-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Paypal')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="submit" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_paystack_enabled']) && $admin_payments_details['is_paystack_enabled']=='on')
                        <div id="paystack-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Paystack')}}</h5>
                            </div>
                            <div class="card-body">
                                <div class="border p-3 mb-3 rounded payment-box">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <input type="button" onclick="preparePayment(this,'paystack')" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                </div>

                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_flutterwave_enabled']) && $admin_payments_details['is_flutterwave_enabled']=='on')
                        <div id="flutterwave-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Flutterwave')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="button" onclick="preparePayment(this,'flutterwave')" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_razorpay_enabled']) && $admin_payments_details['is_razorpay_enabled']=='on')
                        <div id="razorpay-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Razorpay')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <input type="button" onclick="preparePayment(this,'razorpay')" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_paytm_enabled']) && $admin_payments_details['is_paytm_enabled']=='on')
                        <div id="paytm-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Paytm')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('paytm.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                    <input type="hidden" name="total_price" id="paytm_total_price" value="{{$plan->price}}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="paypal_coupon">{{__('Mobile Number')}}</label>
                                                    <input type="text" id="mobile_number" name="mobile_number" class="form-control coupon" placeholder="{{ __('Enter Mobile Number') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="submit"  value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_mercado_enabled']) && $admin_payments_details['is_mercado_enabled']=='on')
                        <div id="mercado-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Mercado Pago')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('plan.pay.with.paypal') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">

                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-12 text-end">
                                        <input type="button" onclick="preparePayment(this,'mercado')" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_mollie_enabled']) && $admin_payments_details['is_mollie_enabled']=='on')
                        <div id="mollie-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Mollie')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('mollie.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                    <input type="hidden" name="total_price" id="mollie_total_price" value="{{$plan->price}}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="paypal_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-12 text-end">
                                        <input type="submit" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_skrill_enabled']) && $admin_payments_details['is_skrill_enabled']=='on')
                        <div id="skrill-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Skrill')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('skrill.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ date('Y-m-d') }}-{{ strtotime(date('Y-m-d H:i:s')) }}-payatm">
                                    <input type="hidden" name="order_id" value="{{str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, "100", STR_PAD_LEFT)}}">
                                    @php
                                        $skrill_data = [
                                            'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                            'user_id' => 'user_id',
                                            'amount' => 'amount',
                                            'currency' => 'currency',
                                        ];
                                        session()->put('skrill_data', $skrill_data);

                                    @endphp
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                    <input type="hidden" name="total_price" id="skrill_total_price" value="{{$plan->price}}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="skrill_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="skrill_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="submit" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>

                                </form>
                            </div>
                        </div>
                    @endif

                    @if(isset($admin_payments_details['is_coingate_enabled']) && $admin_payments_details['is_coingate_enabled']=='on')
                        <div id="skrill-payment" class="card">
                            <div class="card-header">
                                <h5>{{('CoinGate')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="payment-form" action="{{ route('coingate.prepare.plan') }}">
                                    @csrf
                                    <input type="hidden" name="counpon" id="coingate_coupon" value="">
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                    <input type="hidden" name="total_price" id="coingate_total_price" value="{{$plan->price}}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="coingate_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="coingate_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                             <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="submit" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                     @if(isset($admin_payments_details['is_paymentwall_enabled']) && $admin_payments_details['is_paymentwall_enabled']=='on')
                        <div id="paymentwall-payment" class="card">
                            <div class="card-header">
                                <h5>{{('Paymentwall')}}</h5>
                            </div>
                            <div class="card-body">
                                <form class="w3-container w3-display-middle w3-card-4" method="POST" id="" action="{{ route('paymentwall') }}">
                                    @csrf
                                    <input type="hidden" name="counpon" id="paymentwall_coupon" value="">
                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                    <input type="hidden" name="total_price" id="paymentwall_total_price" value="{{$plan->price}}" class="form-control">
                                    <div class="border p-3 mb-3 rounded payment-box">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="coingate_coupon" class="col-form-label">{{__('Coupon')}}</label>
                                                    <input type="text" id="paymentwall_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 coupon-apply-btn mb-2 mt-1">
                                                <a href="#" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10 apply-coupon">{{ __('Apply') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <input type="submit" id="save-btn" value="{{__('Pay Now')}}" class="btn btn-print-invoice  btn-primary m-b-10 m-r-10">
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                        

                </div> 
                <!-- [ sample-page ] end -->
            </div>
        </div>
    </div>
@endsection

