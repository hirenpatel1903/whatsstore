<x-guest-layout>
    <x-auth-card>

@section('page-title')
    {{__('Reset Password')}}
@endsection

@section('language-bar')
    <div class="btn-group align-items-center">
            <select name="language" id="language" class="btn  btn-primary dropdown-toggle" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                @foreach(Utility::languages() as $language)
                    <option  @if($lang == $language) selected @endif value="{{ route('change.langPass',$language) }}">{{Str::upper($language)}}</option>
                @endforeach
            </select>
        </div>
@endsection
@section('content')
<div class="col-xl-6">
        <div class="card-body">
            <div class="">
                <h2 class="mb-3 f-w-600">{{__('Forgot Password')}}</h2>
            </div>
             @if(session('status'))
                <div class="alert alert-primary">
                    {{ session('status') }}
                </div>
            @endif
            <p class="mb-4 text-muted">{{__('We will send a link to reset your password')}}</p>
            <form method="POST" action="{{ route('password.email') }}">
            @csrf
                <div class="">
                    
                    <div class="form-group mb-3">
                        <label class="form-label">{{__('Email')}}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <small>{{ $message }}</small>
                        </span>
                        @enderror
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
                        <button type="submit" class="btn btn-primary btn-block mt-2" id="login_button">Send Password Reset Link</button>
                    </div>
                    
                   
                    <div class="my-4 text-xs text-muted text-center">
                        <p>{{__("Already have an account?")}} <a href="{{route('login',$lang)}}">{{__('Login')}}</a></p>
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
    </x-auth-card>
</x-guest-layout>
