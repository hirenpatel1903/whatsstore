@php
    $logo=asset(Storage::url('uploads/logo/'));
        $users=\Auth::user();
        $currantLang = $users->currentLanguages();
        $languages=\App\Models\Utility::languages();
        $footer_text=isset(\App\Models\Utility::settings()['footer_text']) ? \App\Models\Utility::settings()['footer_text'] : '';

    $SITE_RTL = env('SITE_RTL');
    $setting = App\Models\Utility::colorset();    
    $color = 'theme-3';
    if (!empty($setting['color'])) {
        $color = $setting['color'];
    }



 
    if(\Auth::user()->type=="Super Admin")
    {
        $company_logo=Utility::get_superadmin_logo();
    }
    else
    {
        $company_logo=Utility::get_company_logo();
    }

    @endphp
<!DOCTYPE html>
<html lang="en" dir="{{$SITE_RTL == 'on'?'rtl':''}}">

@include('partials.admin.head')
<body class="{{$color}}">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ navigation menu ] start -->
  @include('partials.admin.menu')
  <!-- [ navigation menu ] end -->
  <!-- [ Header ] start -->
   @include('partials.admin.header')

  <!-- Modal -->
  
  <!-- [ Header ] end -->
</body>

<!-- [ Main Content ] start -->
@include('partials.admin.content')
<!-- [ Main Content ] end -->
<!-- 
 -->
<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="body">
               
                </div>
             
            </div>
        </div>
    </div>

<input type="checkbox" class="d-none" id="cust-theme-bg"  {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
<input type="checkbox" class="d-none" id="cust-darklayout" {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
<footer class="dash-footer">
  <div class="footer-wrapper">
    <div class="py-1">
      <span class="text-muted"
        >{{ $footer_text }}</span
      >
    </div>
  </div>
</footer>
@include('partials.admin.footer')


<script>
 
  $(document).ready(function() {            
            cust_theme_bg();
            cust_darklayout();  
        });

  

        feather.replace();
        var pctoggle = document.querySelector("#pct-toggler");
        if (pctoggle) {
            pctoggle.addEventListener("click", function() {
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

            c.addEventListener("click", function(event) {
                var targetElement = event.target;
                if (targetElement.tagName == "SPAN") {
                    targetElement = targetElement.parentNode;
                }
                var temp = targetElement.getAttribute("data-value");
                removeClassByPrefix(document.querySelector("body"), "theme-");
                document.querySelector("body").classList.add(temp);
            });
        }

        function cust_theme_bg() {
            var custthemebg = document.querySelector("#cust-theme-bg");
            // custthemebg.addEventListener("click", function() {

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
            // });
        }
        var custthemebg = document.querySelector("#cust-theme-bg");
        custthemebg.addEventListener("click", function() {
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
          @if(\Auth::user()->type=="super admin")
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

          @else 
            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

          @endif
        }

        var custdarklayout = document.querySelector("#cust-darklayout");
        custdarklayout.addEventListener("click", function() {
            @if(\Auth::user()->type=="super admin")
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

          @else 
            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{$logo.'/'.($company_logo)}}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

          @endif
        });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }

</script>


@if(App\Models\Utility::getValByName('gdpr_cookie') == 'on')
<script type="text/javascript">
    
    var defaults = {
    'messageLocales': {
        /*'en': 'We use cookies to make sure you can have the best experience on our website. If you continue to use this site we assume that you will be happy with it.'*/
        'en': "{{App\Models\Utility::getValByName('cookie_text')}}"
    },
    'buttonLocales': {
        'en': 'Ok'
    },
    'cookieNoticePosition': 'bottom',
    'learnMoreLinkEnabled': false,
    'learnMoreLinkHref': '/cookie-banner-information.html',
    'learnMoreLinkText': {
      'it': 'Saperne di più',
      'en': 'Learn more',
      'de': 'Mehr erfahren',
      'fr': 'En savoir plus'
    },
    'buttonLocales': {
      'en': 'Ok'
    },
    'expiresIn': 30,
    'buttonBgColor': '#d35400',
    'buttonTextColor': '#fff',
    'noticeBgColor': '#000000',
    'noticeTextColor': '#fff',
    'linkColor': '#009fdd'
};
</script>
<script src="{{ asset('custom/js/cookie.notice.js')}}"></script>
@endif

</body>

</html>