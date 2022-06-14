<form method="post" action="{{ route('product-coupon.store') }}" id="product-coupon-store">
    @csrf
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-12">
                {{Form::label('name',__('Name'),array('class'=>'col-form-label'))}}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>
            <div class=" col-md-12">
                <div class="form-switch">
                    <input type="checkbox" class="form-check-input" name="enable_flat" id="enable_flat">
                    {{Form::label('enable_flat',__('Flat Discount'),array('class'=>'form-check-label mb-3')) }}
                </div>
            </div>
            <div class="form-group col-md-6 nonflat_discount">
                {{Form::label('discount',__('Discount') ,array('class'=>'col-form-label')) }}
                {{Form::number('discount',null,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Discount')))}}
                <span class="small">{{__('Note: Discount in Percentage')}}</span>
            </div>
            <div class="form-group col-md-6 flat_discount" style="display: none;">
                {{Form::label('pro_flat_discount',__('Flat Discount') ,array('class'=>'col-form-label')) }}
                {{Form::number('pro_flat_discount',null,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Flat Discount')))}}
                <span class="small">{{__('Note: Discount in Value')}}</span>
            </div>
            <div class="form-group col-md-6">
                {{Form::label('limit',__('Limit') ,array('class'=>'col-form-label'))}}
                {{Form::number('limit',null,array('class'=>'form-control','placeholder'=>__('Enter Limit'),'required'=>'required'))}}
            </div>
            <div class="form-group col-md-12" id="auto">
                {{Form::label('limit',__('Code') ,array('class'=>'col-form-label'))}}
                <div class="input-group">
                    {{Form::text('code',null,array('class'=>'form-control','id'=>'auto-code','required'=>'required'))}}
                    <button class="btn btn-outline-secondary" type="button" id="code-generate"><i class="fa fa-history pr-1"></i>{{__(' Generate')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
    </div>
    
</form>

