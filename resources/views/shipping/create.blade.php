{{Form::open(array('url'=>'shipping','method'=>'post'))}}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'),array('class'=>'form-control-label')) }}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('price',__('Price'),array('class'=>'form-control-label')) }}
                {{Form::text('price',null,array('class'=>'form-control','placeholder'=>__('Enter Price'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('Location',__('Location'),array('class'=>'form-control-label')) }}
                {!! Form::select('location[]', $locations, null,array('class' => 'form-control multi-select','id'=>'note1','data-toggle'=>'select','multiple')) !!}
            </div>
        </div>
    </div>
</div>

 <div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
</div>

{{Form::close()}}
