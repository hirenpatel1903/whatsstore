@extends('layouts.admin')
@section('page-title')
    {{__('Coupons')}}
@endsection
@section('title')
 {{__('Coupons')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupons') }}</li>
@endsection

@section('action-btn')
    <div class="row  m-1"> 
        <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Coupon')}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Add Coupon')}}" data-url="{{route('coupons.create')}}">
                <i class="ti ti-plus text-white"></i>
            </a>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush
@section('content')
    <div class="row">
    <!-- [ basic-table ] start -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table pc-dt-simple">
                        <thead>
                            <tr>
                                <th> {{__('Name')}}</th>
                                <th> {{__('Code')}}</th>
                                <th> {{__('Discount (%)')}}</th>
                                <th> {{__('Limit')}}</th>
                                <th> {{__('Used')}}</th>
                                <th width="300px"> {{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->name }}</td>
                                <td>{{ $coupon->code }}</td>
                                <td>{{ $coupon->discount }}</td>
                                <td>{{ $coupon->limit }}</td>
                                <td>{{ $coupon->used_coupon() }}</td>
                                <td class="Action">
                                    <span>
                                        <div class="action-btn bg-warning ms-2">
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" data-title="{{__('View')}}" href="{{ route('coupons.show',$coupon->id) }}"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                ><i
                                                    class="ti ti-eye text-white"></i>
                                            </a>
                                        </div>

                                        <div class="action-btn btn-info ms-2">
                                            <a href="#" data-size="lg" data-url="{{route('coupons.edit',[$coupon->id])}}" data-ajax-popup="true" data-title="{{__('Edit Coupon')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Coupon')}}" ><i class="ti ti-pencil text-white"></i></a>
                                        </div>

                                        <div class="action-btn bg-danger ms-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['coupons.destroy', $coupon->id]]) !!}
                                                <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('Delete') }}">
                                                   <span class="text-white"> <i class="ti ti-trash"></i></span>
                                            {!! Form::close() !!}
                                        </div>
                                    </span>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- [ basic-table ] end -->
</div>
@endsection
