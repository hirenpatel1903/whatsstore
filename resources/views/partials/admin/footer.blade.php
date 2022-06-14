<script src="{{asset('custom/js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
<script src="{{asset('assets/js/dash.js')}}"></script>
<script src="{{asset('assets/js/plugins/simple-datatables.js')}}"></script>
<script src="{{asset('assets/js/plugins/main.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/choices.min.js')}}"></script>
<script src="{{asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{asset('assets/js/plugins/dropzone-amd-module.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/bootstrap-switch-button.min.js')}}"></script>
<!-- Time picker -->
<script src="{{asset('assets/js/plugins/flatpickr.min.js')}}"></script>
<!-- datepicker -->
<script src="{{asset('assets/js/plugins/datepicker-full.min.js')}}"></script>
<script src="{{ asset('custom/js/letter.avatar.js')}}"></script>
<script type="text/javascript" src="{{ asset('custom/js/custom.js')}}"></script>


@if(Session::has('success'))
    <script>
        show_toastr('{{__('Success')}}', '{!! session('success') !!}', 'success');
    </script>
    {{ Session::forget('success') }}
@endif
@if(Session::has('error'))
    <script>
        show_toastr('{{__('Error')}}', '{!! session('error') !!}', 'error');
    </script>
    {{ Session::forget('error') }}
@endif
@stack('script-page')

