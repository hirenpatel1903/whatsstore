{{Form::model($store, array('route' => array('store-resource.update', $store->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),['class'=>'col-form-label'])}}
                {{Form::text('name',$user->name,array('class'=>'form-control','placeholder'=>__('Enter Name')))}}
                @error('name')
                <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('store_name',__('Store Name'),['class'=>'col-form-label'])}}
                {{Form::text('store_name',$store->name,array('class'=>'form-control','placeholder'=>__('Store Name')))}}
                @error('store_name')
                <span class="invalid-store_name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('email',__('Email'),['class'=>'col-form-label'])}}
                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email')))}}
                @error('email')
                <span class="invalid-email" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
</div>
{{Form::close()}}
