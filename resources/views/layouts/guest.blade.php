
@php
    $SITE_RTL = env('SITE_RTL');
   
    $logo=asset(Storage::url('uploads/logo/'));
     $company_logo=Utility::get_superadmin_logo();
  
   
@endphp

<!DOCTYPE html>
<html lang="en" dir="{{$SITE_RTL == 'on'?'rtl':''}}">

<head>

   <title>@yield('page-title') - {{(\App\Models\Utility::getValByName('title_text')) ? \App\Models\Utility::getValByName('title_text') : config('app.name', 'WhatsStore')}}</title>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
    />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="Rajodiya Infotech" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{asset(Storage::url('uploads/logo/favicon.png'))}}" type="image/x-icon" />
    
    <!-- font css -->
    <link rel="stylesheet" href="{{asset('assets/fonts/tabler-icons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/fontawesome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/fonts/material.css')}}">
     <link rel="stylesheet" href="{{asset('assets/css/stylesheet.css') }}">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" id="main-style-link">

    <link rel="stylesheet" href="{{asset('assets/css/customizer.css')}}">
    <link rel="stylesheet" href="{{ asset('custom/libs/animate.css/animate.min.css')}}" id="stylesheet">
    <link rel="stylesheet" href="{{ asset('custom/css/custom.css')}}" id="stylesheet">
    @if($SITE_RTL=='on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css')}}" id="main-style-link">
       @else
         
         @if(Utility::getValByName('cust_darklayout')=='on')
          <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css')}}">
         @else
         <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}" id="main-style-link">
         @endif
    @endif
</head>

<body class="theme-3">
    <!-- [ auth-signup ] start -->
    <div class="auth-wrapper auth-v3">
        <div class="bg-auth-side bg-primary"></div>
        <div class="auth-content">
            <nav class="navbar navbar-expand-md navbar-light default">
          <div class="container-fluid pe-2">
            <a class="navbar-brand" href="#">
              <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}" alt="{{ config('app.name', 'LeadGo') }}" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
              <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link active" href="#">{{__('Support')}}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">{{__('Terms')}}</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">{{__('Privacy')}}</a>
                </li>
                <li class="nav-item">
                  @yield('language-bar')
                </li>
              </ul>
            </div>
          </div>
        </nav>
            <div class="card">
                <div class="row align-items-center text-start">
                      @yield('content')
                    <div class="col-xl-6 img-card-side">
                        <div class="auth-img-content">
                            <img src="{{asset('assets/images/auth/img-auth-3.svg')}}" alt="" class="img-fluid">
                            <h3 class="text-white mb-4 mt-5">“Attention is the new currency”</h3>
                            <p class="text-white">The more effortless the writing looks, the more effort the writer
                                actually put into the process.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="auth-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
                           <!-- {{__('Copyright')}} &copy; {{ (Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :config('app.name', 'LeadGo') }} {{date('Y')}}  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ auth-signup ] end -->

    <!-- Required Js -->
    <script src="{{asset('custom/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor-all.js')}}"></script>
    
    <script src="{{asset('assets/js/plugins/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/plugins/feather.min.js')}}"></script>
    <script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
        <script src="{{asset('custom/js/custom.js')}}"></script>
     
    <script>feather.replace();</script>
    <div class="pct-customizer">
      <div class="pct-c-btn">
        <button class="btn btn-primary" id="pct-toggler">
          <i data-feather="settings"></i>
        </button>
      </div>
      <div class="pct-c-content">
        <div class="pct-header bg-primary">
          <h5 class="mb-0 text-white f-w-500">Theme Customizer</h5>
        </div>
        <div class="pct-body">
          <h6 class="mt-2">
            <i data-feather="credit-card" class="me-2"></i>Primary color settings
          </h6>
          <hr class="my-2" />
          <div class="theme-color themes-color">
            <a href="#!" class="" data-value="theme-1"></a>
            <a href="#!" class="" data-value="theme-2"></a>
            <a href="#!" class="" data-value="theme-3"></a>
            <a href="#!" class="" data-value="theme-4"></a>
          </div>
          <h6 class="mt-4">
            <i data-feather="layout" class="me-2"></i>Sidebar settings
          </h6>
          <hr class="my-2" />
          <div class="form-check form-switch">
            <input
              type="checkbox"
              class="form-check-input"
              id="cust-theme-bg"
              checked
            />
            <label class="form-check-label f-w-600 pl-1" for="cust-theme-bg"
              >Transparent layout</label
            >
          </div>
          <h6 class="mt-4">
            <i data-feather="sun" class="me-2"></i>Layout settings
          </h6>
          <hr class="my-2" />
          <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="cust-darklayout" />
            <label class="form-check-label f-w-600 pl-1" for="cust-darklayout"
              >Dark Layout</label
            >
          </div>
        </div>
      </div>
    </div>
    <input type="checkbox" class="d-none" id="cust-theme-bg"  {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
<input type="checkbox" class="d-none" id="cust-darklayout" {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
    <script>
     $(document).ready(function() {            
             cust_darklayout();  
         });
      feather.replace();
      var pctoggle = document.querySelector("#pct-toggler");
      if (pctoggle) {
        pctoggle.addEventListener("click", function () {
          if (
            !document.querySelector(".pct-customizer").classList.contains("active")
          ) {
            document.querySelector(".pct-customizer").classList.add("active");
          } else {
            document.querySelector(".pct-customizer").classList.remove("active");
          }
        });
      }

      var themescolors = document.querySelectorAll(".themes-color > a");
      for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function (event) {
          var targetElement = event.target;
          if (targetElement.tagName == "SPAN") {
            targetElement = targetElement.parentNode;
          }
          var temp = targetElement.getAttribute("data-value");
          removeClassByPrefix(document.querySelector("body"), "theme-");
          document.querySelector("body").classList.add(temp);
        });
      }

      var custthemebg = document.querySelector("#cust-theme-bg");
      custthemebg.addEventListener("click", function () {
        if (custthemebg.checked) {
          document.querySelector(".dash-sidebar").classList.add("transprent-bg");
          document
            .querySelector(".dash-header:not(.dash-mob-header)")
            .classList.add("transprent-bg");
        } else {
          document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
          document
            .querySelector(".dash-header:not(.dash-mob-header)")
            .classList.remove("transprent-bg");
        }
      });

      function cust_darklayout() {
            var custdarklayout = document.querySelector("#cust-darklayout");
            // custdarklayout.addEventListener("click", function() {
         
            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.'logo-light.png'}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.'logo-dark.png'}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

         
        }
      function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
          let value = node.classList[i];
          if (value.startsWith(prefix)) {
            node.classList.remove(value);
          }
        }
      }
    </script>
    <script src="{{asset('custom/js/jquery.min.js')}}"></script>
    <script>
        var toster_pos="{{$SITE_RTL =='on' ?'left' : 'right'}}";
    </script>
    <script src="{{asset('custom/js/custom.js')}}"></script>
    @stack('script')
    @stack('custom-scripts')


</body>

</html>