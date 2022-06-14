    @extends('layouts.guest')

@section('title')
    {{ __('Register') }}
@endsection

@section('language-bar')
    <div class="btn-group align-items-center">
        <select name="language" id="language" class="btn  btn-primary dropdown-toggle" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach(Utility::languages() as $language)
                <option  @if($lang == $language) selected @endif value="{{ route('register',$language) }}">{{Str::upper($language)}}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('content')
    <div class="col-xl-6">

        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{__('Register')}}</h2>
            </div>
            <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate="">
            @csrf
                <div class="">
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                            <span class="error invalid-name text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Store Name') }}</label>
                            <input id="store_name" type="text" class="form-control @error('store_name') is-invalid @enderror" name="store_name" value="{{ old('store_name') }}" required autocomplete="store_name">
                            @error('store_name')
                            <span class="error invalid-name text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                            <span class="error invalid-password text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label class="form-label">{{__('Confirm password')}}</label>
                            <input id="password-confirm" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                            @error('password_confirmation')
                                <span class="error invalid-password_confirmation text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                            {{-- <div class="form-group mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked"
                                        checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        {{ __('I accept the') }} <a href="#!"> {{ __('Term & condition') }}</a>
                                    </label>
                                </div>
                            </div> --}}

                        @if(env('RECAPTCHA_MODULE') == 'yes')
                            <div class="form-group col-lg-12 col-md-12 mt-3">
                                {!! NoCaptcha::display() !!}
                                @error('g-recaptcha-response')
                                <span class="error small text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        @endif
                        <div class="d-grid">
                            <button class="btn btn-primary btn-block mt-2" type="submit">{{ __('Register') }}</button>
                        </div>
                    
                   
                    <div class="my-4 text-xs text-muted text-left">
                        <p>
                            {{__("Already have an account?")}} <a href="{{route('login',$lang)}}">{{__('Sign in')}}</a>
                        </p>
                    </div>
                   
                </div>
            </form>
        </div>
    </div>
@endsection
@push('custom-scripts')
@if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
@endif
@endpush