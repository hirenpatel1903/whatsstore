@extends('layouts.admin')
@section('page-title')
    {{__('Coupon Detail')}}
@endsection
@section('title')
  {{__('Coupon Detail')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">{{ __('Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupon Detail') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h4 class="my-2">{{ $coupon->code }}</h4>
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th aria-controls="selection-datatable" rowspan="1" colspan="1"
                                        aria-label=" User: activate to sort column ascending" style="width: 411px;"> User
                                    </th>
                                    <th aria-controls="selection-datatable" rowspan="1" colspan="1"
                                        aria-label=" Date: activate to sort column ascending" style="width: 642px;"> Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userCoupons as $userCoupon)
                                    <tr role="row" class="odd">
                                        <td>{{ !empty($userCoupon->name) ? $userCoupon->name : '' }}
                                        </td>
                                        <td>{{ $userCoupon->created_at }}</td>
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
