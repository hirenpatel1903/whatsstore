<!DOCTYPE html>
<html lang="en">
@php
    $userstore = \App\Models\UserStore::where('store_id', $store->id)->first();
    $settings   =\DB::table('settings')->where('name','company_favicon')->where('created_by', $userstore->user_id)->first();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ucfirst($store->name)}} - {{ucfirst($store->tagline)}}">
      <meta name="meta_data" content="{{ucfirst($store->meta_data)}}">



    <title>@yield('page-title') - {{($store->tagline) ?  $store->tagline : env('APP_NAME', ucfirst($store->name))}}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{asset(Storage::url('uploads/logo/').(!empty($settings->value)?$settings->value:'favicon.png'))}}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="{{asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')}}"><!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('assets/libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/swiper/dist/css/swiper.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/libs/animate.css/animate.min.css')}}">
    <!-- site CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/site.css')}}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css')}}" id="stylesheet')}}">
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js')}}"></script>
    <style>
        @stack('css-page')
            @media (min-width: 768px) {
            .header-account-page {
                height: 200px;
            }
        }
    </style>
</head>

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
<header class="header " id="header-main">
    <!-- Topbar -->
    <div id="navbar-top-main" class="navbar-top navbar-dark bg-dark border-bottom">
        <div class="container px-0">
            <div class="navbar-nav align-items-center">
                <a class="navbar-brand mr-lg-3 pt-0" href="{{route('store.slug',$store->slug)}}">
                    @if(!empty($store->logo))
                        <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$store->logo))}}" id="navbar-logo" style="height: 40px;">
                    @else
                        <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/logo.png'))}}" id="navbar-logo" style="height: 40px;">
                    @endif
                </a>
                <div class="navbar-nav align-items-lg-center">
                    <span class="nav-link navbar-text mr-3 text-lg">{{ucfirst($store->name)}}</span>
                </div>
                @if(!empty($page_slug_urls))
                    @foreach($page_slug_urls as $k=>$page_slug_url)
                        @if($page_slug_url->enable_page_header == 'on')
                            <ul class="navbar-nav align-items-lg-center">
                                <li class="nav-item ">
                                    @php($app_url = trim(env('APP_URL'), '/'))
                                    <a class="nav-link" href="{{$app_url . '/page-option/' . $page_slug_url->slug}}">{{ucfirst($page_slug_url->name)}}</a>
                                </li>
                            </ul>
                        @endif
                    @endforeach
                @endif
                @if($store->blog_enable == 'on')
                    <ul class="navbar-nav align-items-lg-center">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('store.blog',$store->slug)}}">{{__('Blog')}}</a>
                        </li>
                    </ul>
                @endif
                <div class="ml-auto">
                    <ul class="nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-sm mb-0"><i class="fas fa-globe-asia"></i>
                                    {{Str::upper($currantLang)}}
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                @foreach($languages as $language)
                                    <a href="{{route('change.languagestore',[$store->slug,$language])}}" class="dropdown-item @if($language == $currantLang) active-language @endif">
                                        <span> {{Str::upper($language)}}</span>
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>
<div class="main-content">
    <header class="pt-3 d-flex align-items-end">
        <!-- Header container -->
        <div class="container">
            <div class="row">
                <div class=" col-lg-12">
                    <!-- Salute + Small stats -->
                    <div class="row align-items-center mb-4 ">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <span class="h2 mb-0 text-dark d-block">{{__('My Cart')}}</span>
                            <span class="text-dark">{{__('Have a nice shopping')}}!</span>
                        </div>
                    </div>
                    <!-- Account navigation -->
                    <div class="d-flex">
                        <div class="btn-group btn-group-nav shadow" role="group" aria-label="Basic example">
                            <div class="btn-group" role="group">
                                <a href="{{route('store.cart',$store->slug)}}" class="btn btn-dark btn-icon border_r {{ (Request::segment(3) == 'cart')? 'active' :'' }}">
                                    <span class="btn-inner--icon"><i class="fas fa-shopping-cart"></i></span>
                                    <span class="btn-inner--text d-none d-md-inline-block">{{__('Cart')}}</span>
                                </a>
                                <a href="{{route('user-address.useraddress',$store->slug)}}" class="btn btn-dark btn-icon border_r {{ (Request::segment(3) == 'useraddress')? 'active' :'' }}">
                                    <span class="btn-inner--icon"><i class="fas fa-user"></i></span>
                                    <span class="btn-inner--text d-none d-md-inline-block">{{__('Customer')}}</span>
                                </a>
                                <a href="{{route('store-payment.payment',$store->slug)}}" class="btn btn-dark btn-icon border_r {{ (Request::segment(3) == 'userpayment')? 'active' :'' }}">
                                    <span class="btn-inner--icon"><i class="fas fa-credit-card"></i></span>
                                    <span class="btn-inner--text d-none d-md-inline-block">{{__('Payment')}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="slice slice-lg">
        @yield('content')
    </section>
</div>
<footer id="footer-main">
    <div class="footer footer-dark pt-4 pb-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="copyright text-sm font-weight-bold text-center text-md-left pt-1">
                        {{$store->footer_note}}
                    </div>
                    <ul class="nav mt-3 mt-md-0">
                        @if(!empty($store->email))
                            <li class="nav-item">
                                <a class="nav-link pl-0" href="{{$store->email}}" target="_blank">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store->whatsapp))
                            <li class="nav-item">
                                <a class="nav-link" href="{{$store->whatsapp}}" target=”_blank”>
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store->facebook))
                            <li class="nav-item">
                                <a class="nav-link" href="{{$store->facebook}}" target="_blank">
                                    <i class="fab fa-facebook-square"></i>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store->instagram))
                            <li class="nav-item">
                                <a class="nav-link" href="{{$store->instagram}}" target="_blank">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store->twitter))
                            <li class="nav-item">
                                <a class="nav-link" href="{{$store->twitter}}" target="_blank">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store->youtube))
                            <li class="nav-item">
                                <a class="nav-link" href="{{$store->youtube}}" target="_blank">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                        @if(!empty($page_slug_url))
                            @foreach($page_slug_urls as $k=>$page_slug_url)
                                @if($page_slug_url->enable_page_footer == 'on')
                                    <div class="nav-item ">
                                        @php($app_url = trim(env('APP_URL'), '/'))
                                        <a class="nav-link" href="{{$app_url . '/page-option/' . $page_slug_url->slug}}">{{ucfirst($page_slug_url->name)}}</a>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div>
                <h4 class="h4 font-weight-400 float-left modal-title" id="exampleModalLabel"></h4>
                <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal" aria-label="Close">{{__('Close')}}</a>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<!-- Core JS - includes jquery, bootstrap, popper, in-view and sticky-kit -->
<script src="{{asset('assets/js/site.core.js')}}"></script>
<!-- notify -->
<script type="text/javascript" src="{{ asset('assets/js/custom.js')}}"></script>

<script src="{{ asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<!-- Page JS -->
<script src="{{asset('assets/libs/swiper/dist/js/swiper.min.js')}}"></script>
<!-- site JS -->
<script src="{{asset('assets/js/site.js')}}"></script>
<!-- Demo JS - remove it when starting your project -->
<script src="{{asset('assets/js/demo.js')}}"></script>

@php
    $store_settings = \App\Models\Store::where('slug',$store->slug)->first();
@endphp

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{$store_settings->google_analytic}}"></script>
{!! $store_settings->storejs !!}
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', '{{ !empty($store_settings->google_analytic) }}');
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
@stack('script-page')
</body>

</html>

