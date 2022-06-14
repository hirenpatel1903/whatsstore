{{Form::open(array('url'=>'location','method'=>'post'))}}
<div class="modal-body">
	<div class="row">
	    <div class="col-12">
	        <div class="form-group">
	            {{Form::label('name',__('Name'),array('class'=>'col-form-label')) }}
	            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
	        </div>
	    </div>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
</div>

{{Form::close()}}
