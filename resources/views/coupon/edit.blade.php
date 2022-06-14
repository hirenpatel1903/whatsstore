<form method="post" action="{{ route('coupons.update', $coupon->id) }}">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">{{__('Name')}}</label>
                <input type="text" name="name" class="form-control" required value="{{$coupon->name}}">
            </div>

            <div class="form-group col-md-6">
                <label for="discount">{{__('Discount')}}</label>
                <input type="number" name="discount" class="form-control" required step="0.01" value="{{$coupon->discount}}">
                <span class="small">{{__('Note: Discount in Percentage')}}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="limit">{{__('Limit')}}</label>
                <input type="number" name="limit" class="form-control" required value="{{$coupon->limit}}">
            </div>

            <div class="form-group col-md-12" id="auto">
                <label for="code">{{__('Code')}}</label>
                <div class="input-group">
                    <input class="form-control" name="code" type="text" id="auto-code" value="{{$coupon->code}}">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text" id="code-generate"><i class="fa fa-history pr-1"></i> {{__('Generate')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary">{{__('Update')}}</button>
    </div>
   
</form>

