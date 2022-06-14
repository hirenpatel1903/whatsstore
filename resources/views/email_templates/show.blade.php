@extends('layouts.admin')
@section('page-title')
    {{ $emailTemplate->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('email_template.index') }}">{{ __('Email Templates') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $emailTemplate->name }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
@endpush
@section('content')
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header card-body">
                    <h5></h5>
                    {{Form::model($emailTemplate, array('route' => array('email_template.update', $emailTemplate->id), 'method' => 'PUT')) }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            {{Form::label('name',__('Name'),['class'=>'col-form-label text-dark'])}}
                            {{Form::text('name',null,array('class'=>'form-control font-style','disabled'=>'disabled'))}}
                        </div>
                        <div class="form-group col-md-12">
                            {{Form::label('from',__('From'),['class'=>'col-form-label text-dark'])}}
                            {{Form::text('from',null,array('class'=>'form-control font-style','required'=>'required'))}}
                        </div>
                        {{Form::hidden('lang',$currEmailTempLang->lang,array('class'=>''))}}
                        <div class="col-12 text-end">
                            <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="row text-xs">
                        <div class="col-3 pb-3">
                            <h6 class="font-weight-bold">{{__('Order')}}</h6>
                            <p class="mb-1">{{__('App Name')}} : <span class="pull-right text-primary">{app_name}</span></p>
                            <p class="mb-1">{{__('Order Name')}} : <span class="pull-right text-primary">{order_name}</span></p>
                            <p class="mb-1">{{__('Order Status')}} : <span class="pull-right text-primary">{order_status}</span></p>
                            <p class="mb-1">{{__('Order URL')}} : <span class="pull-right text-primary">{order_url}</span></p>
                            
                             <p class="mb-1">{{__('owner Name')}} : <span class="pull-right text-primary">{owner_name}</span></p>
                            <p class="mb-1">{{__('Order ID')}} : <span class="pull-right text-primary">{order_id}</span></p>
                            <p class="mb-1">{{__('Order Date')}} : <span class="pull-right text-primary">{order_date}</span></p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header card-body">
                    <h5></h5>
                    <div class="language-wrap">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 language-list-wrap">
                                <div class="language-list">
                                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                        @foreach($languages as $lang)
                                            <li class="text-sm font-weight-bold">
                                                <a href="{{route('manage.email.language',[$emailTemplate->id,$lang])}}" class="nav-link {{($currEmailTempLang->lang == $lang)?'active':''}}">{{Str::upper($lang)}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12 language-form-wrap">
                                {{Form::model($currEmailTempLang, array('route' => array('store.email.language',$currEmailTempLang->parent_id), 'method' => 'PUT')) }}
                                <div class="row">
                                    <div class="form-group col-12">
                                        {{Form::label('subject',__('Subject'),['class'=>'col-form-label text-dark'])}}
                                        {{Form::text('subject',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                    </div>
                                    <div class="form-group col-12">
                                        {{Form::label('content',__('Email Message'),['class'=>'col-form-label text-dark'])}}
                                        {{Form::textarea('content',$currEmailTempLang->content,array('class'=>'summernote-simple','required'=>'required'))}}
                                    </div>
                                    <div class="col-md-12 text-end">
                                            {{Form::hidden('lang',null)}}
                                            <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

