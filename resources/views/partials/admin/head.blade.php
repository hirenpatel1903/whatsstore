@php
    $logo=asset(Storage::url('uploads/logo/'));
   $favicon=\App\Models\Utility::getValByName('company_favicon');

   

    
@endphp
<head>
    <meta charset="utf-8" dir="{{$SITE_RTL == 'on'?'rtl':''}}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=  ">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="{{env('APP_NAME')}} - Online Whatsapp Store Builder">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title') - {{(\App\Models\Utility::getValByName('title_text')) ? \App\Models\Utility::getValByName('title_text') : config('app.name', 'WhatsStore')}}</title>
    <link rel="icon" href="{{asset(Storage::url('uploads/logo/')).'/favicon.png'}}" type="image/png">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/bootstrap-switch-button.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/flatpickr.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/dragula.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/style.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/main.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/plugins/datepicker-bs5.min.css')}}">
    <!-- font css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/animate.min.css')}}">

    <!-- vendor css -->
   
    <link rel="stylesheet" href="{{asset('assets/css/customizer.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/plugins/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{asset('custom/css/custom.css') }}">

    @if($SITE_RTL=='on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" id="main-style-link">
    @endif

    @stack('css-page')
</head>
