@extends('layouts.admin')
@push('css-page')
@endpush
@push('script-page')
@endpush
@section('page-title')
    {{__('Language')}}
@endsection
@section('title')
 {{__('Language')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Language') }}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
       <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add New Language')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Add New Language')}}" data-url="{{route('create.language')}}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    
    @if($currantLang != (!empty(env('default_language')) ? env('default_language') : 'en'))
        <div class="col-auto pe-0">
            {!! Form::open(['method' => 'DELETE', 'route' => ['lang.destroy', $currantLang],'id'=>'delete-form-'.$currantLang]) !!}
                <a href="#!" class="btn btn-sm btn-danger btn-icon show_confirm">
                   <span class="text-white"> <i class="ti ti-trash"></i></span></a>
            {!! Form::close() !!}

        </div>
    @endif
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-3">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills flex-column " id="myTab4" role="tablist">
                        @foreach($languages as $lang)
                            <li class="nav-item">
                                <a href="{{route('manage.language',[$lang])}}" class="nav-link {{($currantLang == $lang)?'active':''}}">{{Str::upper($lang)}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-md-9">
            <div class="card card-fluid">
                <div class="card-body" style="position: relative;">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-labels-tab" data-bs-toggle="pill" href="#home" role="tab" aria-controls="pills-labels" aria-selected="true">{{__('Labels')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="pills-messages-tab" data-bs-toggle="pill" href="#profile" role="tab" aria-controls="messages" aria-selected="false">{{__('Messages')}}</a>
                        </li>

                    </ul>
                    <div class="tab-content">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <form method="post" action="{{route('store.language.data',[$currantLang])}}">
                                        @csrf
                                        <div class="row">
                                            @foreach($arrLabel as $label => $value)

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-control-label" for="example3cols1Input">{{$label}} </label>
                                                        <input type="text" class="form-control" name="label[{{$label}}]" value="{{$value}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="form-group col-12 text-end">
                                                <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <form method="post" action="{{route('store.language.data',[$currantLang])}}">
                                        @csrf
                                        <div class="row">
                                            @foreach($arrMessage as $fileName => $fileValue)
                                                <div class="col-lg-12">
                                                    <h5>{{ucfirst($fileName)}}</h5>
                                                </div>
                                                @foreach($fileValue as $label => $value)
                                                    @if(is_array($value))
                                                        @foreach($value as $label2 => $value2)
                                                            @if(is_array($value2))
                                                                @foreach($value2 as $label3 => $value3)
                                                                    @if(is_array($value3))
                                                                        @foreach($value3 as $label4 => $value4)
                                                                            @if(is_array($value4))
                                                                                @foreach($value4 as $label5 => $value5)
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}.{{$label5}}</label>
                                                                                            <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}][{{$label5}}]" value="{{$value5}}">
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            @else
                                                                                <div class="col-lg-6">
                                                                                    <div class="form-group">
                                                                                        <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}</label>
                                                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}]" value="{{$value4}}">
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        <div class="col-lg-6">
                                                                            <div class="form-group">
                                                                                <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}</label>
                                                                                <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}]" value="{{$value3}}">
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <div class="col-lg-6">
                                                                    <div class="form-group">
                                                                        <label>{{$fileName}}.{{$label}}.{{$label2}}</label>
                                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}]" value="{{$value2}}">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>{{$fileName}}.{{$label}}</label>
                                                                <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}]" value="{{$value}}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </div>
                                        <div class="form-group col-12 text-end">
                                            <input type="submit" value="{{__('Save')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                                        </div>
                                    </form>
                                </div>
                            </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection

