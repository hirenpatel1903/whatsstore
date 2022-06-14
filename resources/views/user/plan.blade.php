
@foreach($plans as $plan)

    <div class="list-group-item">
        <div class="row align-items-center">
            <div class="col-auto">
                <a href="#" class="avatar rounded-circle">
                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/plan')).'/'.$plan->image}}" style="width:50px;">
                </a>
            </div>
            <div class="col-auto ">
                <a href="#!" class="d-block h6 mb-0">{{$plan->name}}</a>
                <div>
                    <span class="text-sm">{{\App\Models\Utility::priceFormat($plan->price)}} {{' / '. $plan->duration}}</span>
                </div>
            </div>
            <div class="col-auto ">
                <a href="#!" class="d-block h6 mb-0">{{__('Stores')}}</a>
                <div>
                    <span class="text-sm">{{$plan->max_stores}}</span>
                </div>
            </div>
            <div class="col-auto ">
                <a href="#!" class="d-block h6 mb-0">{{__('Products')}}</a>
                <div>
                    <span class="text-sm">{{$plan->max_products}}</span>
                </div>
            </div>
            <div class="col-auto">
                @if($user->plan==$plan->id)
                    <span class="badge badge-soft-success mr-2">{{__('Active')}}</span>
                @else
                    <a href="{{route('plan.active',[$user->id,$plan->id])}}" class="btn btn-xs btn-secondary btn-icon" data-toggle="tooltip" data-original-title="{{__('Click to Upgrade Plan')}}">
                        <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
