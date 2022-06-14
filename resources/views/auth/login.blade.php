
@extends('layouts.guest')

@section('title')
    {{ __('Login') }}
@endsection

@section('language-bar')

        <div class="btn-group align-items-center">
            <select name="language" id="language" class="btn  btn-primary dropdown-toggle" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach(Utility::languages() as $language)
                    <option  @if($lang == $language) selected @endif value="{{ route('login',$language) }}">{{Str::upper($language)}}</option>
                @endforeach
            </select>
        </div>
        
@endsection

@section('content')
   

    <div class="col-xl-6">

        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{('Login')}}</h2>
            </div>
            <form method="POST" action="{{ route('login') }}" id="form_data" class="needs-validation" novalidate="">
            @csrf
                <div class="">
                    <div class="form-group mb-3">
                        <label class="form-label">{{__('Email')}}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-label">{{__('Password')}}</label>
                                </div>
                                
                        </div>
                      
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                         @error('password')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-4">
                        <div class="mb-3">
                            <div class="text-left">
                                @if (Route::has('change.langPass'))
                                    <a href="{{ route('change.langPass',$lang) }}" class="small text-muted text-underline--dashed border-primary">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                    </div>

                    @if(env('RECAPTCHA_MODULE') == 'yes')
                        <div class="form-group mb-3">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    @endif
                    

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button">{{__('Login')}}</button>
                    </div>
                    @if(Utility::getValByName('signup_button')=='on')
                   
                    <div class="my-4 text-xs text-muted text-center">
                        <p>{{__("Don't have an account?")}} <a href="{{route('register',$lang)}}">{{__('Register')}}</a></p>
                        
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>


@endsection
@push('custom-scripts')
<!-- <script src="{{asset('custom/libs/jquery/dist/jquery.min.js')}}"></script> -->
<script>
$(document).ready(function () {
  $("#form_data").submit(function (e) {
      $("#login_button").attr("disabled", true);
      return true;
  });
});
</script>
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush