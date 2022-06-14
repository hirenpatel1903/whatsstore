@extends('layouts.admin')
@php
    $profile=asset(Storage::url('uploads/profile/'));
@endphp
@section('page-title')
    {{__('Profile')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-400 mb-0">   {{__('Profile')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Profile')}}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#personal-info" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-cog pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Personal Info')}}</a>
                                <p class="mb-0 text-sm">{{__('Details about your personal information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#change-password" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-lock pt-1"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Change Password')}}</a>
                                <p class="mb-0 text-sm">{{__('Details about your account password change')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 order-lg-1">
            <div id="personal-info" class="tabs-card personal-info">
                {{Form::model($userDetail,array('route' => array('update.account'), 'method' => 'put', 'enctype' => "multipart/form-data"))}}
                <div class="card bg-gradient-warning hover-shadow-lg border-0">
                    <div class="card-body py-3">
                        <div class="row row-grid align-items-center">
                            <div class="col-lg-8">
                                <div class="media align-items-center">
                                    <a href="#" class="avatar avatar-lg rounded-circle mr-3">
                                        <img alt="Image placeholder" src="{{(!empty($userDetail->avatar))? $profile.'/'.$userDetail->avatar : $profile.'/avatar.png'}}">
                                    </a>
                                    <div class="media-body">
                                        <h5 class="text-white mb-0">{{$userDetail->name}}</h5>
                                        <div>
                                            <input type="file" name="profile" id="file-1" class="custom-input-file custom-input-file-link" data-multiple-caption="{count} files selected" multiple/>
                                            <label for="file-1">
                                                <span class="text-white">{{__('Change avatar')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @if(\Auth::user()->type=='client')
                                @php $client=$userDetail->clientDetail; @endphp
                                <div class="col-md-4">

                                    <div class="form-group">
                                        {{Form::label('name',__('Name'))}}
                                        {{Form::text('name',null,array('class'=>'form-control font-style'))}}
                                        @error('name')
                                        <span class="invalid-name" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('email',__('Email'))}}
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    {{Form::label('mobile',__('Mobile'))}}
                                    {{Form::number('mobile',$client->mobile,array('class'=>'form-control'))}}
                                    @error('mobile')
                                    <span class="invalid-mobile" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('address_1',__('Address').'1')}}
                                    {{Form::textarea('address_1', $client->address_1, ['class'=>'form-control','rows'=>'4'])}}
                                    @error('address_1')
                                    <span class="invalid-address_1" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('address_2',__('Address').'2')}}
                                    {{Form::textarea('address_2', $client->address_2, ['class'=>'form-control','rows'=>'4'])}}
                                    @error('address_2')
                                    <span class="invalid-address_2" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    {{Form::label('city',__('City'))}}
                                    {{Form::text('city',$client->city,array('class'=>'form-control'))}}
                                    @error('city')
                                    <span class="invalid-city" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('state',__('State'))}}
                                    {{Form::text('state',$client->state,array('class'=>'form-control'))}}
                                    @error('state')
                                    <span class="invalid-state" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('country',__('Country'))}}
                                    {{Form::text('country',$client->country,array('class'=>'form-control'))}}
                                    @error('country')
                                    <span class="invalid-country" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    {{Form::label('zip_code',__('Zip Code'))}}
                                    {{Form::text('zip_code',$client->zip_code,array('class'=>'form-control'))}}
                                    @error('zip_code')
                                    <span class="invalid-zip_code" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('name',__('Name'))}}
                                        {{Form::text('name',null,array('class'=>'form-control font-style'))}}
                                        @error('name')
                                        <span class="invalid-name" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{Form::label('email',__('Email'))}}
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                    </div>
                </div>
                {{Form::close()}}
            </div>
            <div id="change-password" class="tabs-card change-password d-none">
                <div class="card">
                    <div class="card-header">
                        <h5 class=" h6 mb-0">{{__('Change password')}}</h5>
                    </div>
                    {{Form::model($userDetail,array('route' => array('update.password',$userDetail->id), 'method' => 'put'))}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('current_password',__('Current Password'))}}
                                    {{Form::password('current_password',array('class'=>'form-control','placeholder'=>__('Enter Current Password')))}}
                                    @error('current_password')
                                    <span class="invalid-current_password" role="alert">
                                         <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('new_password',__('New Password'))}}
                                    {{Form::password('new_password',array('class'=>'form-control','placeholder'=>__('Enter New Password')))}}
                                    @error('new_password')
                                    <span class="invalid-new_password" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('confirm_password',__('Re-type New Password'))}}
                                    {{Form::password('confirm_password',array('class'=>'form-control','placeholder'=>__('Enter Re-type New Password')))}}
                                    @error('confirm_password')
                                    <span class="invalid-confirm_password" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script !src="">
        $(document).ready(function () {
            $('.custom-list-group-item').on('click', function () {
                var href = $(this).attr('data-href');
                if (href == '#personal-info')
                {
                    $('#personal-info').show();
                    $('#change-password').hide();
                }else
                {
                    $('#change-password').show();
                    $('#personal-info').hide();
                }
            });
        });
    </script>
@endpush

