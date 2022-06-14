
    {{ Form::open(array('route' => array('product.import'),'method'=>'post', 'enctype' => "multipart/form-data")) }}
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-6">
            {{Form::label('file',__('Download sample customer CSV file'),['class'=>'col-form-label'])}}
            <a href="{{asset(Storage::url('uploads/sample')).'/sample-product.xlsx'}}" class="btn btn-sm btn-primary btn-icon-only rounded-circl">
                <i class="fa fa-download"></i>
            </a>
        </div>
        <div class="col-md-12 mt-1">
            {{Form::label('file',__('Select CSV File'),['class'=>'col-form-label'])}}
            <div class="choose-file form-group">
                <label for="file" class="col-form-label">
                    <input type="file" class="form-control" name="file" id="file" data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
        </div>
    </div>

<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary add-variants">{{__('Upload')}}</button>
</div>
{{ Form::close() }}

           