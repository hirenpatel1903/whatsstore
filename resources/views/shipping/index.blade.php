@extends('layouts.admin')
@section('page-title')
    {{__('Shipping')}}
@endsection
@section('title')
    {{__('Shipping/Location')}}
@endsection
@section('breadcrumb')
    
    <li class="breadcrumb-item active" aria-current="page">{{ __('Location/Shipping') }}</li>
@endsection
@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
             <a href="{{route('shipping.export')}} " class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-original-title="{{__('Export')}}"  >
                <i class="ti ti-file-export text-white"></i>
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-sm-4 col-md-4 col-xxl-3">
        <div class="p-2 card mt-2">
            <ul class="nav nav-pills nav-fill" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-user-tab-1" data-bs-toggle="pill"
                        data-bs-target="#pills-user-1" type="button"> <i
                            class="fas fa-location-arrow mx-2"></i>{{ __('Location') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-user-tab-2" data-bs-toggle="pill"
                        data-bs-target="#pills-user-2" type="button"> <i class="fas fa-shipping-fast mx-2"></i>
                        {{ __('Shipping') }}</button>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-xxl-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-user-1" role="tabpanel"
                        aria-labelledby="pills-user-tab-1">
                        <div class="d-flex justify-content-between">
                            <h3 class="mb-0">{{ __('Location') }}</h3>
                            <div class="pr-2">
                                <a href="#" data-size="md" data-url="{{ route('location.create') }}"
                                    data-ajax-popup="true" data-title="{{ __('Create New Location') }}"
                                    class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
                                    data-bs-placement="left" title="{{ __('Create New Location') }}"><i
                                        class="ti ti-plus"></i></a>
                            </div>
                        </div>
                        <div class="row mt-3">
                                <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table mb-0 pc-dt-simple" id="data1">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Created At') }}</th>
                                                <th width="200px">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($locations as $location)
                                                <tr data-name="{{ $location->name }}">
                                                    <td>{{ $location->name }}</td>
                                                    <td>{{ \App\Models\Utility::dateFormat($location->created_at) }}</td>
                                                    <td class="Action">
                                                        <span>
                                                            <div class="action-btn btn-primary ms-2">
                                                                <a href="#" data-size="md" data-url="{{ route('location.edit', $location->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit type')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>

                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['location.destroy', $location->id]]) !!}
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
                    <div class="tab-pane fade" id="pills-user-2" role="tabpanel" aria-labelledby="pills-user-tab-2">
                        <div class="d-flex justify-content-between">
                            <h3 class="mb-0"> {{ __('Shipping') }}</h3>
                            <div class="pr-2">
                                <a href="#" data-size="md" data-url="{{ route('shipping.create') }}"
                                    data-ajax-popup="true" data-title="{{ __('Create New Shipping') }}"
                                    class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
                                    data-bs-placement="left" title="{{ __('Create New Shipping') }}"><i
                                        class="ti ti-plus"></i></a>
                            </div>
                        </div>

                        <div class="row mt-3">
                                <div class="card-body table-border-style">
                                <div class="table-responsive">
                                    <table class="table mb-0 pc-dt-simple" id="data2">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Location') }}</th>
                                                <th>{{ __('Created At') }}</th>
                                                <th width="200px">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shippings as $shipping)
                                                <tr data-name="{{ $shipping->name }}">
                                                    <td>{{ $shipping->name }}</td>
                                                    <td>{{ \App\Models\Utility::priceFormat($shipping->price) }}</td>
                                                    <td>{{ !empty($shipping->locationName()) ? $shipping->locationName() : '-' }}
                                                    </td>
                                                    <td>{{ \App\Models\Utility::dateFormat($shipping->created_at) }}</td>
                                                    <td class="Action">
                                                        <span>
                                                             <div class="action-btn btn-primary ms-2">
                                                                <a href="#" data-size="md" data-url="{{ route('shipping.edit',$shipping->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit type')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                            </div>
                                                            
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['shipping.destroy', $shipping->id]]) !!}
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
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function () {
            $(document).on('keyup', '.search-user', function () {
                var value = $(this).val();
                $('.employee_tableese tbody>tr').each(function (index) {
                    var name = $(this).attr('data-name').toLowerCase();
                    if (name.includes(value.toLowerCase())) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
        $('#search'). keydown(function (e) {
            if (e. keyCode == 13) {
                e. preventDefault();
                return false;
            }
        });
    </script>
@endpush
