{{ Form::open(array('route' => array('test.send.mail'))) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('email', __('Email'),array('class'=>'col-form-label')) }}
            {{ Form::text('email', '', array('class' => 'form-control','required'=>'required')) }}
            @error('email')
            <span class="invalid-email" role="alert">
                <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
</div>
<div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Send')}}</button>
    </div>
{{ Form::close() }}

