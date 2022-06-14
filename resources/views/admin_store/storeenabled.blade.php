
{{Form::model($users, array('route' => array('store-resource.display', $users->id), 'method' => 'PUT','enctype'=>'multipart/form-data')) }}
@csrf
@method('put')
<div>
	<p>This action can not be undone. Do you want to continue?</p>
	</div>
<div class="form-group text-right">
    <button class="btn btn-sm btn-primary btn-icon rounded-pill" value="{{$users->store_display}}" type="submit">{{ __('Yes') }}</button>
    <button class="btn btn-sm btn-danger btn-icon rounded-pill" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
</div>
{{Form::close()}}
