<div class="main-content">
    <section class="mh-100vh d-flex align-items-center bg-white" data-offset-top="#header-main">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="text-center">
                        <!-- Empty cart container -->
                        <div class="login-form">
                            {!! Form::open(array('route' => array('store.userstore', $slug),'class'=>'login-form-main'), ['method' => 'post']) !!}
                            <div class="form-group mt-2">
                            <label for="exampleInputEmail1" class="form-label float-left w-100 text-left">{{__('Full Name')}}</label>
                            <input name="name" class="form-control" type="text" required="required">
                        </div>
                        @error('name')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Email')}}</label>
                            <input name="email" class="form-control" type="email" required="required">
                        </div>
                        @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Number')}}</label>
                            <input name="phone_number" class="form-control" type="text" required="required">
                        </div>
                        @error('number')
                        <span class="error invalid-email text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Password')}}</label>
                            <input name="password" class="form-control" type="password"  required="required">
                        </div>
                        @error('password')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label float-left">{{__('Confirm Password')}}</label>
                            <input name="password_confirmation" class="form-control" type="password"  required="required">
                        </div>
                        @error('password_confirmation')
                        <span class="error invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="log_in_btn form-group  mb-3 d-flex align-items-center">
                            <button type="submit" class="sign-in-btn bg--gray">{{__('Register')}}</button>
                        </div>
                        <div class="float-left">
                            {{__('Already registered ?')}}
                        <a data-url="{{route('customer.loginform',$slug)}}" data-ajax-popup="true" data-title="{{__('Login')}}"  data-toggle="modal"  class="text-primary pb-4">{{__('Login')}}</a>
                        </div>
                        
                        {!! Form::close() !!}
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
