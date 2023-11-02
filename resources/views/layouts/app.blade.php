
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('seo_title')</title>
    <meta name="description" content="@yield('meta_description')" />
    <meta name="keywords" content="@yield('meta_keywords')" />
    <meta name="robots" content="@yield('meta_robot')" />
    <meta name="google-site-verification" content="zlhIxB4c0EEiQYfF9l8ayzkRRPTkGe_DIyffVU424tE" />

	<meta name="facebook-domain-verification" content="y6mjhuodf8fm1u3k3rz9ygf5bzvtut" />

    @php
        $currentRouteName = Route::currentRouteName();
        $noCanonicalRoutes = [];
        $noIndexRoutes = ['search', 'profile.show', 'profile.edit', 'profile.password', 'profile.orders', 'profile.products', 'profile.notifications.index', 'login', 'wishlist.index', 'cart.index', 'cart.checkout', 'password.phone', 'password.phone.verify'];
        $noIndexWithParamsRoutes = ['category'];
        $requestURL = request()->url();
        $requestFullURL = request()->fullUrl();
        $noindexURL = false;
        if (
            in_array($currentRouteName, $noIndexRoutes) ||
            (in_array($currentRouteName, $noIndexWithParamsRoutes) && $requestURL != $requestFullURL) ||
            ($currentRouteName == 'home' && (request()->has('external_browser_redirect') || request()->has('gtm_debug')) )
        ) {
            $noindexURL = true;
        }
    @endphp
    @if ($noindexURL)
        <meta name="robots" content="noindex, follow" />
    @elseif (!in_array($currentRouteName, $noCanonicalRoutes))
        <link rel="canonical" href="{{ url()->current() }}">
    @endif

    @php
        $switcher = Helper::languageSwitcher();
    @endphp
    @foreach ($switcher->getValues() as $item)
    <link rel="alternate" hreflang="{{ $item->key }}-UZ" href="{{ $item->url }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $switcher->getMain()->url }}" />

    @php
		$htmlClass = [];
		$badEye = json_decode(request()->cookie('bad_eye'), true);
		if (is_array($badEye)) {
			foreach ($badEye as $key => $value) {
				if ($value != 'normal' && !in_array('bad-eye', $htmlClass)) {
					$htmlClass[] = 'bad-eye';
				}
				$htmlClass[] = 'bad-eye-' . $key . '-' . $value;
			}
		}
        $assetsVersion = env('ASSETS_VERSION', 1);
    @endphp

    <link rel="stylesheet" href="{{ asset('css/main.css?v=' . $assetsVersion) }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css?v=' . $assetsVersion) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="{{ asset('css/jquery.searchHistory.css') }}">


    {{-- <link rel="stylesheet" href="{{ asset('css/vendor.css?v=' . $assetsVersion) }}">
    <link rel="stylesheet" href="{{ asset('css/app.css?v=' . $assetsVersion) }}"> --}}

    @yield('styles')

    <link rel="icon" href="/ag-favicon.png" sizes="any">
    {{-- it was favicon.ico in the past --}}

    {{-- <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest"> --}}

    

    <link href="/jivosite/jivosite.css" rel="stylesheet">
    <script src="/jivosite/jivosite.js" type="text/javascript"></script>
    {!! setting('site.jivochat_code') !!}

    @include('codes.cook')
</head>
<body class="@yield('body_class')">

    @include('partials.svg')

    <x-header />

    @yield('content')

    {{-- 
    @if (!empty(auth()->user()->id))
        @if (auth()->user()->voucher == 'false')
            <div class="circle-button">
                <div class="circle-text">100</div>
            </div>
        @endif
    @else
        <div class="circle-button">
            <div class="circle-text">100</div>
        </div>
    @endif
    


    <div class="modal fade" id="VoucherExampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="VoucherExampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-body p-0 modal-div-center">
                <div class="modal-text-center text-center mt-3">
                    {{-- 
                    <h2 class="text-white m-0">Поздравляем!</h2>
                    <h5 class="text-white m-0">Вы выиграли ваучер на сумму:</h5>
                    --}}
                    {{-- 
                </div>
                <img src="{{ asset('img/voucher.png') }}" class="img-fluid" alt="">

                <div class="modal-button-center">
                    <a href="{{ route('voucher') }}" class="btn bg-white text-center">Получить ваучер</a>
                </div>
            </div>
            </div>
        </div>
    </div>
    --}}

    <x-footer />

    @yield('after_footer_blocks')

    @include('partials.preloader')

    <script src="{{ asset('js/main.js?v=' . $assetsVersion) }}"></script>
    <script src="{{ asset('js/custom.js?v=' . $assetsVersion) }}"></script>
    <script src="{{ asset('js/jquery.searchHistory.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>

    {{-- <script src="{{ asset('js/app.js?v=' . $assetsVersion) }}"></script> --}}

    @yield('scripts')
    <script> 

        $(document).ready(function(){
            // SWIPER FOR NAV CATALOGS
            var swiper = new Swiper(".header-swiper-container", {
            slidesPerView: "auto",
            freeMode: true,
            slideToClickedSlide: true,
            spaceBetween: 10,
            scrollbar: {
                el: ".swiper-scrollbar",
                draggable: true,
                dragSize: 100
            },
            mousewheel: true
            });
        });

        /*
        $(document).ready(function() {
            var count = localStorage.getItem('count') || 100; // Get the count from localStorage or set it to 15 if it doesn't exist
            $('.circle-text').text(count);

            if (count === 100) {
                $('.circle-button').hide(); // Hide the circle-button element if the countdown value is the initial value of 15
            }

            setInterval(function() {
                count--;
                if (count >= 0) {
                    $('.circle-text').text(count);
                    localStorage.setItem('count', count); // Save the updated count to localStorage
                    $('.circle-button').show(); // Show the circle-button element after the countdown value is loaded
                } else {
                    $('.circle-button').show(); // Hide the circle-button element after the countdown reaches 0

                    // Add a link to the login page and a Font Awesome icon inside the circle button
                    $('.circle-button').html('<a href="javascript:;" class="voucher-modal-trigger" data-toggle="modal" data-target="#VoucherExampleModalCenter" style="font-size:20px;"><i class="bi bi-gift text-white voucher-modal-trigger"></i></a>');
                }
            }, 1000);
        });

        $(document).ready(function() {
            $('.voucher-modal-overlay').hide();
            $('.voucher-modal-trigger').click(function(e) {
                e.preventDefault();
                $('.voucher-modal-overlay').show();
            });
        });
        */ 
        

        $(function () {
            $(".mysearchoneNewAll").keyup(function () {
                var that = this,
                value = $(this).val();

                let headerD = $('.header-d');
                let headerM = $('.header-m');
                let headerMultisearch = $('.multisearchContainer');

                $.ajax({
                    type: "POST",
                    url: "{{ route('multisearch') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "value": value
                    },
                    success: function(data){
                        console.log(data);
                        headerMultisearch.addClass('d-block');
                        $('.multisearchContainer').html(data);
                    }
                });

                setTimeout(() => {
                    if (value == '') {
                        headerMultisearch.removeClass('d-block');
                    }
                }, 3000);
            });
        });

        $('#sss').searchHistory({
            maxShowNum: 4, // max entries
            expires: 7, // 7 days
            input: '.history-input',
            cookieName: 'searchHistory',
            selected: function () { },
            beforeSend: function () { return true; },
            sendWhenSelect: true,
            actionByCall:false
        });

        $('#sss2').searchHistory({
            maxShowNum: 4, // max entries
            expires: 7, // 7 days
            input: '.history-input',
            cookieName: 'searchHistory',
            selected: function () { },
            beforeSend: function () { return true; },
            sendWhenSelect: true,
            actionByCall:false
        });
    
    function typeOne(){
        const words = [
            "Samsung A54",
            "Dyson Airwrap",
            "Mi band 7",
            "Parfum",
            "Ноутбук HP Pavilion",
            "Iphone 14",
            "Косметика",
            "Janeke расчёска"
        ];
        
        let i = 0;
        let timer;

        function typingEffect() {
            let word = words[i].split("");
            var loopTyping = function() {
                if (word.length > 0) {
                    document.getElementById('mysearchoneNewOne').placeholder += word.shift();
                } else {
                    deletingEffect();
                    return false;
                };
                timer = setTimeout(loopTyping, 100);
            };
            loopTyping();
        };

        function deletingEffect() {
            let word = words[i].split("");
            var loopDeleting = function() {
                if (word.length > 0) {
                    word.pop();
                    document.getElementById('mysearchoneNewOne').placeholder = word.join("");
                } else {
                    if (words.length > (i + 1)) {
                        i++;
                    } else {
                        i = 0;
                    };
                    typingEffect();
                    return false;
                };
                timer = setTimeout(loopDeleting, 5);
            };
            loopDeleting();
        };

        typingEffect();
    }

    function typeTwo(){
        const words = [
            "Samsung A54",
            "Dyson Airwrap",
            "Mi band 7",
            "Parfum",
            "Ноутбук HP Pavilion",
            "Iphone 14",
            "Косметика",
            "Janeke расчёска"
        ];
        
        let i = 0;
        let timer;

        function typingEffect() {
            let word = words[i].split("");
            var loopTyping = function() {
                if (word.length > 0) {
                    document.getElementById('mysearchtwoNewTwo').placeholder += word.shift();
                } else {
                    deletingEffect();
                    return false;
                };
                timer = setTimeout(loopTyping, 100);
            };
            loopTyping();
        };

        function deletingEffect() {
            let word = words[i].split("");
            var loopDeleting = function() {
                if (word.length > 0) {
                    word.pop();
                    document.getElementById('mysearchtwoNewTwo').placeholder = word.join("");
                } else {
                    if (words.length > (i + 1)) {
                        i++;
                    } else {
                        i = 0;
                    };
                    typingEffect();
                    return false;
                };
                timer = setTimeout(loopDeleting, 5);
            };
            loopDeleting();
        };

        typingEffect();
    }

    typeOne();
    typeTwo();

    $('.mysearchone').on('keyup', function() {
      var userInput = $(this).val();
      var sanitizedInput = userInput.replace("</", "&lt;").replace("/>", "&gt;").replace('"', "&quot;").replace("'", "&#039;");
      $(this).val(sanitizedInput);
    });
    </script>
    {!! setting('site.facebook_pixel_code') !!}
    
    {!! setting('site.google_analytics_code') !!}
    {!! setting('site.yandex_metrika_code') !!}
   
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-KXPZBT8');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KXPZBT8"
        height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
   
    @yield('microdata')

</body>
</html>
