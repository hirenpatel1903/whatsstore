@extends('layouts.admin')
@section('page-title')
    {{__('WhatsStore')}}
@endsection
@section('title')
    {{__('Store')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">{{ __('Stores') }}</li>
@endsection

@section('action-btn')
    <div class="row  m-1">
        <div class="col-auto pe-0">
            <a href="{{ route('store.subDomain') }}" class="btn btn-sm btn-primary btn-icon">
                {{__('Sub Domain')}}
            </a>
        </div>

        <div class="col-auto pe-0">
            <a href="{{ route('store.grid') }}" class="btn btn-sm btn-primary btn-icon">
                {{__('Grid View')}}
            </a>
        </div>

        <div class="col-auto pe-0">
            <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create New Store')}}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Store')}}" data-url="{{ route('store-resource.create') }}">
                <i class="ti ti-plus text-white"></i>
            </a>

        </div>
    </div>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('custom/libs/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('custom/libs/summernote/summernote-bs4.js')}}"></script>
@endpush
@section('content')
    <!-- Listing -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('User Name')}}</th>
                                    <th>{{ __('Email')}}</th>
                                    <th>{{ __('Stores')}}</th>
                                    <th>{{ __('Plan')}}</th>
                                    <th>{{ __('Created At')}}</th>
                                    <th>{{ __('Store Display')}}</th>
                                    <th>{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $usr)

                                    <tr>
                                        <td>{{ $usr->name }}</td>
                                        <td>{{ $usr->email }}</td>
                                        <td>{{ $usr->stores->count() }}</td>
                                        <td>{{ !empty($usr->currentPlan->name)?$usr->currentPlan->name:'-' }}</td>
                                        <td>{{\App\Models\Utility::dateFormat($usr->created_at)}}</td>
                                        <td>
                                            <div class="form-switch disabled-form-switch">
                                                

                                                <a href="#" data-size="md" data-url="{{ route('store-resource.edit.display',$usr->id) }}" data-ajax-popup="true" class="action-item" data-title="{{__('Are You Sure?')}}"  data-title="{{($usr->store_display == 1)?'Stores disable':'Store enable'}}">
                                                    <input type="checkbox" class="form-check-input" disabled="disabled" name="store_display" id="{{$usr->id}}" {{ ($usr->store_display == 1) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{$usr->id}}"></label>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn btn-primary ms-2">
                                                    <a href="#" data-size="lg" data-url="{{ route('store-resource.edit',$usr->id) }}" data-ajax-popup="true" data-title="{{__('Edit Store')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit Store')}}" ><i class="ti ti-pencil text-white"></i></a>
                                                </div>

                                                <div class="action-btn btn-info ms-2">
                                                    <a href="#" data-size="md" data-url="{{ route('plan.upgrade',$usr->id) }}" data-ajax-popup="true" data-title="{{__('Upgrade Plan')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Upgrade Plan')}}" ><i class="ti ti-trophy text-white"></i></a>
                                                </div>

                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['store-resource.destroy', $usr->id]]) !!}
                                                        <a href="#!" class="mx-3 btn btn-sm d-inline-flex align-items-center show_confirm">
                                                           <span class="text-white"> <i class="ti ti-trash"></i></span></a>
                                                    {!! Form::close() !!}
                                                </div>


                                                <div class="action-btn btn-warning ms-2">
                                                    <a href="#" data-size="md" data-url="{{route('user.reset',$usr->id)}}" data-ajax-popup="true" data-title="{{__('Reset Password')}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Reset Password')}}" ><i class="fas fa-key text-white"></i></a>
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
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function () {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
@endpush

