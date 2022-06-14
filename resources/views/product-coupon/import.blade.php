
    {{ Form::open(array('route' => array('coupon.import'),'method'=>'post', 'enctype' => "multipart/form-data")) }}
    <div class="row">
        <div class="col-md-12 mb-6">
            {{Form::label('file',__('Download sample vendor CSV file'),['class'=>'form-control-label'])}}
            <a href="{{asset(Storage::url('uploads/sample')).'/coupen_.csv'}}" class="choose-file btn boxbtn fluid shadow-sm btn-primary rounded-6 d-inline-flex align-items-center">
                <i class="fa fa-download pr-2"></i> {{__('Download')}}
            </a>
        </div>
        <div class="col-md-12">
            {{Form::label('file',__('Select File'),['class'=>'form-control-label'])}}
           <div class="form-group">
                                  
                                    <input type="file" name="file" id="file" class="custom-input-file" data-filename="upload_file" required>
                                    <label for="file">
                                        <i class="fa fa-upload"></i>
                                        <span>{{__('Choose a file')}}</span>
                                    </label>
                                     <p class="upload_file"></p>
                                </div>
                          
        </div>
      <div class="form-group col-md-12 text-right">
            <button class="btn btn-sm btn-primary rounded-pill mr-auto" type="submit">{{ __('Upload') }}</button>
            <button class="btn btn-sm btn-primary rounded-pill mr-auto"  type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
        </div>
    </div>
    {{ Form::close() }}

           