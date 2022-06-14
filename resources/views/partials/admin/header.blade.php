@php
    $logo         =asset(Storage::url('uploads/logo/'));
    if(\Auth::user()->type=="Super Admin")
    {
        $company_logo=Utility::get_superadmin_logo();
    }
    else
    {
        $company_logo=Utility::get_company_logo();
    }
    $user         = \Auth::user();
    $plan         = \App\Models\Plan::where('id', $user->plan)->first();
@endphp

<header class="dash-header transprent-bg">
    <div class="header-wrapper"><div class="me-auto dash-mob-drp">
  <ul class="list-unstyled">
    <li class="dash-h-item mob-hamburger">
      <a href="#!" class="dash-head-link" id="mobile-collapse">
        <div class="hamburger hamburger--arrowturn">
          <div class="hamburger-box">
            <div class="hamburger-inner"></div>
          </div>
        </div>
      </a>
    </li>
    <li class="dropdown dash-h-item drp-company">
        <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
            role="button" aria-haspopup="false" aria-expanded="false">
            <span class="theme-avtar">
                <img alt="#" width="35"
                    src="{{ asset(Storage::url('uploads/profile/' . (!empty(Auth::user()->avatar) ?Auth::user()->avatar : 'avatar.png'))) }}"
                    class="header-avtar">
            </span>
            <span class="hide-mob ms-2">Hi,{{ Auth::user()->name }}!
            </span>
            <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
        </a>
        <div class="dropdown-menu dash-h-dropdown">
            @if (Auth::user()->type == 'Owner')
                @foreach (Auth::user()->stores as $store)
                    @if ($store->is_active)
                        <a href="@if (Auth::user()->current_store == $store->id) # @else {{ route('change_store', $store->id) }} @endif"
                            title="{{ $store->name }}" class="dropdown-item">
                            <span>{{ $store->name }}</span>
                            @if (Auth::user()->current_store == $store->id)
                                <i class="fas fa-check px-2"></i>
                            @endif
                        </a>
                    @else
                        <a href="#" class="dropdown-item notify-item" title="{{ __('Locked') }}">
                            <i class="fas fa-lock"></i>
                            <span>{{ $store->name }}</span>
                            @if (isset($store->pivot->permission))
                                @if ($store->pivot->permission == 'Owner')
                                    <span
                                        class="badge badge-primary">{{ __($store->pivot->permission) }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Shared') }}</span>
                                @endif
                            @endif
                        </a>
                    @endif
                @endforeach
                <div class="dropdown-divider"></div>
                @auth('web')
                    @if (Auth::user()->type == 'Owner')
                        <a href="#" data-size="lg" data-url="{{ route('store-resource.create') }}"
                            data-ajax-popup="true" data-title="{{ __('Create New Store') }}"
                            class="dropdown-item">
                            <i class="ti ti-plus"></i></i><span>{{ __('Create New Store') }}</span>
                        </a>
                    @endif
                @endauth
                <hr>
            @endif

            <a href="{{ route('profile') }}" class="dropdown-item">
                <i class="ti ti-user"></i>
                <span>{{ __('My Profile') }}</span>
            </a>
            <a href="{{ route('logout') }}" class="dropdown-item"
                onclick="event.preventDefault();document.getElementById('frm-logout').submit();">
                <i class="ti ti-power"></i>
                <span>{{ __('Logout') }}</span>
            </a>
            <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf</form>
        </div>
    </li>
  </ul>
</div>
<div class="ms-auto">
  <ul class="list-unstyled">

    
   
    <li class="dropdown dash-h-item drp-language">
      <a
        class="dash-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ti ti-world nocolor"></i>
        <span class="drp-text hide-mob"> {{Str::upper($currantLang)}}</span>
        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
      </a>
      <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
        @foreach($languages as $language)
            <a href="{{route('change.language',$language)}}" class="dropdown-item @if($language == $currantLang) active-language @endif"">
              <span>{{Str::upper($language)}}</span>
            </a>
        @endforeach
      </div>
    </li>
  </ul>
</div>
</div>
  </header>