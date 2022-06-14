
<form method="POST" action="{{ route('get.product.variants.possibilities') }}">
    @csrf
    <div class="card-body">
         <div class="form-group">
            <label for="variant_name" class="col-form-label">{{ __('Variant Name') }}</label>
            <input class="form-control" name="variant_name" type="text" id="variant_name" placeholder="{{ __('Variant Name, i.e Size, Color etc') }}">
        </div>
        <div class="form-group">
            <label for="variant_options" class="col-form-label">{{ __('Variant Options') }}</label>
            <input class="form-control" name="variant_options" type="text" id="variant_options" placeholder="{{ __('Variant Options separated by|pipe symbol, i.e Black|Blue|Red') }}">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn  btn-primary add-variants">{{__('Add Variants')}}</button>
    </div>
</form>
