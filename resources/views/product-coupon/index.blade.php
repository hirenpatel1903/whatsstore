@extends('layouts.admin')
@section('page-title')
    {{__('Product Coupons')}}
@endsection
@section('title')
    {{__('Product Coupons')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Product Coupons') }}</li>
@endsection
@section('action-btn')

    <div class="row  m-1">
       
         

        <div class="col-auto pe-0">
             <a href="{{route('coupon.export')}} " class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
        </div>

         <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Import')}}" data-size="md" data-ajax-popup="true" data-title="{{__('Import Coupon CSV file')}}" data-url="{{route('coupon.file.import')}}">
                <i class="ti ti-file-import text-white"></i>
            </a>
        </div>

        <div class="col-auto pe-0">
               <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Coupon')}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Add Coupon')}}" data-url="{{route('product-coupon.create')}}">
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple ">
                            <thead>
                                <tr>
                                    <th> {{ __('Name') }}</th>
                                    <th> {{ __('Code') }}</th>
                                    <th> {{ __('Discount (%)') }}</th>
                                    <th> {{ __('Limit') }}</th>
                                    <th> {{ __('Used') }}</th>
                                    <th width="200px"> {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productcoupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->name }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        @if ($coupon->enable_flat == 'off')
                                            <td>{{ $coupon->discount . '%' }}</td>
                                        @endif
                                        @if ($coupon->enable_flat == 'on')
                                            <td>{{ $coupon->flat_discount . ' ' . '(Flat)' }}</td>
                                        @endif
                                        <td>{{ $coupon->limit }}</td>
                                        <td>{{ $coupon->product_coupon() }}</td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn bg-warning ms-2">
                                                    <a href="{{ route('product-coupon.show', $coupon->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                </div> 
                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ route('product-coupon.edit', [$coupon->id]) }}" data-ajax-popup="true" data-title="{{__('Edit')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>
                                           
                                           
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product-coupon.destroy', $coupon->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
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
    </div>
@endsection
