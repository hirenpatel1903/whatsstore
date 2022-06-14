
<div class="main-content bg-none">
    <section class="mh-100vh d-flex align-items-center bg-white" data-offset-top="#header-main">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="text-center">
                        <!-- Empty cart container -->
                        <div class="login-form">
                            {!! Form::open(array('route' => array('customer.login', $slug,(!empty($is_cart) && $is_cart==true)?$is_cart:false),'class'=>'login-form-main'),['method'=>'POST']) !!}
                            <div class="form-group mb-3">
                                <label for="exampleInputEmail1" class="form-label float-left mt-2">{{__('Email')}}</label>
                                {{Form::text('email',null,array('class'=>'form-control'))}}
                            </div>
                            <div class="form-group mb-3">
                                <label for="exampleInputPassword1" class="form-label float-left">{{__('Password')}}</label>
                                {{Form::password('password',array('class'=>'form-control','id'=>'exampleInputPassword1'))}}
                            </div>
                            <div class="log_in_btn form-group mt-2 mb-4 d-flex align-items-center text-left">
                                <button type="submit" class="sign-in-btn bg--gray">{{__('Sign In')}}</button>
                                
                            </div>
                            <div class="float-left">
                                {{__('Dont have account ?')}}
                                <a data-url="{{route('store.usercreate',$slug)}}" data-ajax-popup="true" data-title="{{__('Register')}}"  data-toggle="modal" class="login-form-main-a text-primary pt-2 mb-4">{{__('Register')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>