{{Form::open(array('route'=>'plans.store','method'=>'post','enctype'=>'multipart/form-data'))}}
@csrf
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('name',__('Name'),array('class'=>'col-form-label')) }}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('price',__('Price'),array('class'=>'col-form-label')) }}
                <div class="input-group col-md-12">
                    <div class="input-group-text">{{ (env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$' }}</div>
                   {{Form::number('price',null,array('class'=>'form-control','id'=>'monthly_price','min'=>'0','placeholder'=>__('Enter Price'),'required'=>'required'))}}
               </div>
            </div>
        </div>
        <div class="col-md-6">
            {{ Form::label('image', __('Image'), ['class' => 'col-form-label']) }}
            <div class="choose-files">
                <label for="image">
                    <div class=" bg-primary logo_update"> <i
                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                    </div>
                    <input type="file" class="form-control file" name="image" id="image"
                        data-filename="logo_update">
                </label>
            </div>                                                   
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('duration', __('Duration')) }}
            {!! Form::select('duration', $arrDuration, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('max_stores',__('Maximum Store'),array('class'=>'col-form-label')) }}
                {{Form::number('max_stores',null,array('class'=>'form-control','id'=>'max_stores','placeholder'=>__('Enter Max Store'),'required'=>'required'))}}
                <span><small>{{__("Note: '-1' for Unlimited")}}</small></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{Form::label('max_products',__('Maximum Products Per Store'),array('class'=>'col-form-label')) }}
                {{Form::number('max_products',null,array('class'=>'form-control','id'=>'max_products','placeholder'=>__('Enter Max Products'),'required'=>'required'))}}
                <span><small>{{__("Note: '-1' for Unlimited")}}</small></span>
            </div>
        </div>
        <div class="col-6">
            <div class="custom-control form-switch pt-2">
                <input type="checkbox" class="form-check-input" name="enable_custdomain" id="enable_custdomain">
                <label class="custom-control-label form-check-label" for="enable_custdomain">{{__('Enable Domain')}}</label>
            </div>
        </div>
        <div class="col-6">
            <div class="custom-control form-switch pt-2">
                <input type="checkbox" class="form-check-input" name="enable_custsubdomain" id="enable_custsubdomain">
                <label class="custom-control-label form-check-label" for="enable_custsubdomain">{{__('Enable Sub Domain')}}</label>
            </div>
        </div>
        <div class="col-6">
            <div class="custom-control form-switch pt-2">
                <input type="checkbox" class="form-check-input" name="shipping_method" id="shipping_method">
                <label class="custom-control-label form-check-label" for="shipping_method">{{__('Enable Shipping Method')}}</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{Form::label('description',__('Description'),array('class'=>'col-form-label')) }}
                {{Form::textarea('description',null,array('class'=>'form-control','id'=>'description', 'rows' => 2,'placeholder'=>__('Enter Description')))}}
            </div>
        </div>
    </div>    
</div>

<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
{{Form::close()}}
