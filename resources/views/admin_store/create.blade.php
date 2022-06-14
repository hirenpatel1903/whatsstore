{{Form::open(array('url'=>'store-resource','method'=>'post'))}}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('store_name',__('Store Name'),array('class'=>'col-form-label')) }}
                {{Form::text('store_name',null,array('class'=>'form-control','placeholder'=>__('Enter Store Name'),'required'=>'required'))}}
            </div>
        </div>
        @if(\Auth::user()->type == 'super admin')
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),array('class'=>'col-form-label')) }}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('email',__('Email'),array('class'=>'col-form-label')) }}
                {{Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('password',__('Password'),array('class'=>'col-form-label')) }}
                {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))}}
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
    </div>

{{Form::close()}}
