{{Form::open(array('url'=>'product_categorie','method'=>'post'))}}
<div class="card-body">
	<div class="row">
	    <div class="col-12">
	        <div class="form-group">
	            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Product Category'),'required'=>'required'))}}
	        </div>
	    </div>
	</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Save')}}</button>
</div>
{{Form::close()}}
