<x-guest-layout>
    <x-auth-card>
@extends('layouts.guest')
@section('page-title')
    {{__('Reset Password')}}
@endsection
@section('content')
<div class="col-xl-6">
        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{__('Reset Password')}}</h2>
            </div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="form-group">
                    {{Form::label('E-Mail Address',__('E-Mail Address'),array('class' => 'form-label'))}}
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                {{Form::label('Password',__('Password'),array('class' => 'form-label'))}}
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    {{Form::label('password-confirm',__('Confirm Password'),array('class' => 'form-label'))}}
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-block mt-2">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
</x-auth-card>
</x-guest-layout>
