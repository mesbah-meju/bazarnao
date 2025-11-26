<!DOCTYPE html>
@if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">

    <title>@yield('meta_title', get_setting('website_name').' | '.get_setting('site_motto'))</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description') )" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords') )">
    

    @yield('meta')

    @if(!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ get_setting('meta_title') }}">
    <meta itemprop="description" content="{{ get_setting('meta_description') }}">
    <meta itemprop="image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
    <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ get_setting('meta_title') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('home') }}" />
    <meta property="og:image" content="{{ uploaded_asset(get_setting('meta_image')) }}" />
    <meta property="og:description" content="{{ get_setting('meta_description') }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif

    <link rel="canonical" href="https://bazarnao.com/"/>

    <!-- Favicon -->
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if(App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">

    <script>
        var AIZ = AIZ || {};
        var INVOICE = INVOICE || {};
    </script>

    <style>
        /* Make the buttons appear more centered if needed */
        .d-flex.justify-content-center {
            display: flex;
            justify-content: center;
        }

        .btn-danger {
            background-color: red;
            border-color: red;
        }

        .btn-danger:hover {
            background-color: darkred;
            border-color: darkred;
        }

        .mt-2 {
            margin-top: 10px;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
        }

        .aiz-carousel .carousel-box {
            padding: 10px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .aiz-carousel .carousel-box .col-lg-6 img {
            transition: transform 0.3s ease;
        }

        .aiz-carousel .carousel-box .col-lg-6 img:hover {
            transform: scale(1.05);
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }



        :root {
            --primary: {{ get_setting("base_color", "#e62d04") }};
            --hov-primary: {{ get_setting("base_hov_color", "#c52907") }};
        }

        .sidenav {
            height: 100%;
            /* Full-height: remove this if you want "auto" height */
            width: 220px;
            /* Set the width of the sidebar */
            position: fixed;
            /* Fixed Sidebar (stay in place on scroll) */
            z-index: 1;
            /* Stay on top */
            top: 50px;
            /* Stay at the top */
            left: 0;
            background-color: #fff;
            overflow-x: hidden;
            /* Disable horizontal scroll */
            padding-top: 20px;
            border-right: 1px solid #ccc;
        }

        .rightsidenav {
            height: 100%;
            /* Full-height: remove this if you want "auto" height */
            width: 280px;
            /* Set the width of the sidebar */
            position: fixed;
            /* Fixed Sidebar (stay in place on scroll) */
            z-index: 1;
            /* Stay on top */
            top: 45px;
            /* Stay at the top */
            right: 0;
            background-color: #fff;
            overflow-x: hidden;
            /* Disable horizontal scroll */
            padding-top: 20px;
            border-left: 1px solid #ccc;
            margin-right: -280px;
        }

        .rightsidecart {
            cursor: pointer;
            position: fixed;
            width: 65px;
            height: 75px;
            background: #f5fceb;
            right: 0;
            top: calc(110px + 30%);
            box-shadow: 0 0 16px -1px rgb(0 0 0 / 75%);

            transition: .1s ease-in-out;
            z-index: 1;
        }

        .rightcarttop {
            height: 50px;
            background: #dba9c9;
            width: 100%;
        }

        .main_content {
            margin-left: 230px;
        }

        .footer_main_content {
            margin-left: 230px;
        }

        .rightsidenav .list-group-item {
            padding: 3px 2px;
        }

        .rightsidenav a {
            font-size: 11px;
        }

        @media screen and (max-width: 450px) {
            .sidenav {
                margin-left: -220px;
            }

            .main_content {
                margin-left: 0px;
            }

            .footer_main_content {
                margin-left: 0px;
            }

            .container-fluid {
                margin: 0px !important;
                padding: 15px !important;
            }

            .home-banner-area .container-fluid {
                margin: 0px !important;
                padding: 0px !important;
            }

            .logo-bar-area .container-fluid {
                margin: 0px !important;
                padding: 0px 15px !important;
            }

            .home-banner-area img {
                width: 100%;
                height: auto;
            }

            #whyshop {
                padding: 0px 15px;
            }

            .rightsidecart {
                display: none;
            }
        }
    </style>

    @if (App\Models\BusinessSetting::where('type', 'google_analytics')->first()->value == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ env("TRACKING_ID") }}');
    </script>
    @endif

    @if (App\Models\BusinessSetting::where('type', 'facebook_pixel')->first()->value == 1)
    <!-- Facebook Pixel Code -->
    <script>
        !function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ env("FACEBOOK_PIXEL_ID") }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}/&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Meta Pixel Code -->
    <script>
        !function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '932980007684341');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=932980007684341&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
    @endif

    @php
    echo get_setting('header_script');
    @endphp

</head>

<body>
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column">

        <!-- Header -->
        @include('frontend.inc.nav')
        @include('frontend.inc.leftsidebar')
        <div id="right_side_carts">
            @include('frontend.inc.rightsidebar')
        </div>
        <div class="main_content">
            @yield('content')
        </div>

        <div class="footer_main_content">
            @include('frontend.inc.footer')
        </div>

    </div>


    @if (get_setting('show_cookies_agreement') == 'on')
    <div class="aiz-cookie-alert shadow-xl">
        <div class="p-3 bg-dark rounded">
            <div class="text-white mb-3">
                @php
                echo get_setting('cookies_agreement_text');
                @endphp
            </div>
            <button class="btn btn-primary aiz-cookie-accepet">
                {{ translate('Ok. I Understood') }}
            </button>
        </div>
    </div>
    @endif

    @include('frontend.partials.modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="la-2x">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

    @yield('modal')

    <div class="modal fade" id="GuestCheckout">
        <div class="modal-dialog modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Login')}}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-1">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                @if (App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                <input type="text" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone')}}" name="email" id="email">
                                @else
                                <input type="email" class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                @endif
                                @if (App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                <span class="opacity-60">{{ translate('Use country code before number') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg" placeholder="{{ translate('Password')}}">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class=opacity-60>{{ translate('Remember Me') }}</span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}" class="text-reset opacity-60 fs-14">{{ translate('Forgot password?')}}</a>
                                </div>
                            </div>

                            <div class="mb-2">
                                <button type="submit" class="btn btn-primary btn-block fw-600">{{ translate('Login') }}</button>
                            </div>
                        </form>

                    </div>
                    <div class="text-center mb-1">
                        <p class="text-muted mb-0">{{ translate('Dont have an account?')}}</p>
                        <a href="{{ route('user.registration') }}">{{ translate('Register Now')}}</a>
                    </div>
                    @if(App\Models\BusinessSetting::where('type', 'google_login')->first()->value == 1 || App\Models\BusinessSetting::where('type', 'facebook_login')->first()->value == 1 || App\Models\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                    <div class="separator mb-3">
                        <span class="bg-white px-3 opacity-60">{{ translate('Or Login With')}}</span>
                    </div>
                    <ul class="list-inline social colored text-center mb-3">
                        @if (App\Models\BusinessSetting::where('type', 'facebook_login')->first()->value == 1)
                        <li class="list-inline-item">
                            <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                <i class="lab la-facebook-f"></i>
                            </a>
                        </li>
                        @endif
                        @if(App\Models\BusinessSetting::where('type', 'google_login')->first()->value == 1)
                        <li class="list-inline-item">
                            <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                <i class="lab la-google"></i>
                            </a>
                        </li>
                        @endif
                        @if (App\Models\BusinessSetting::where('type', 'twitter_login')->first()->value == 1)
                        <li class="list-inline-item">
                            <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                <i class="lab la-twitter"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                    @endif
                    @if (App\Models\BusinessSetting::where('type', 'guest_checkout_active')->first()->value == 1)
                    <div class="separator mb-3">
                        <span class="bg-white px-3 opacity-60">{{ translate('Or')}}</span>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('checkout.shipping_info') }}" class="btn btn-soft-primary">{{ translate('Guest Checkout')}}</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- SCRIPTS -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js') }}"></script>
    <script src="{{ static_asset('assets/js/invoice-core.js') }}"></script>
    <!--     <script src="{{ static_asset('assets/js/jquery-ui.js') }}"></script> -->


    @if (get_setting('facebook_chat') == 1)
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v3.3'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s);
            js.id = id;
            js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <div id="fb-root"></div>
    <!-- Your customer chat code -->
    <div class="fb-customerchat"
        attribution=setup_tool
        page_id="{{ env('FACEBOOK_PAGE_ID') }}">
    </div>
    @endif

    @foreach(session('flash_notification', collect())->toArray() as $message)
    <script>
        AIZ.plugins.notify('{{ $message["level"] }}', '{{ $message["message"] }}');
    </script>
    @endforeach


    @if (get_setting('facebook_comment') == 1)
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v9.0&appId={{ env('FACEBOOK_APP_ID') }}&autoLogAppEvents=1" nonce="ji6tXwgZ"></script>
    @endif
    <script>
        $('#list_menu').click(function() {

            if ($(this).hasClass("isActive")) {
                $(".main_content").css("margin-left", "230px");
                $(".footer_main_content").css("margin-left", "230px");
                $(".sidenav").css("margin-left", "0");
                $(this).removeClass('isActive');

            } else {
                $(".main_content").css("margin-left", "0");
                $(".footer_main_content").css("margin-left", "0");
                $(".sidenav").css("margin-left", "-220px");

                $(this).addClass('isActive');
            }

        })
        $('#list_menu_mobile').click(function() {

            if ($(this).hasClass("isActive")) {
                $(".main_content").css("margin-left", "0");
                $(".footer_main_content").css("margin-left", "0");
                $(".sidenav").css("margin-left", "-220px");

                $(this).removeClass('isActive');

            } else {
                $(".main_content").css("margin-left", "230px");
                $(".footer_main_content").css("margin-left", "230px");
                $(".sidenav").css("margin-left", "0");

                $(this).addClass('isActive');
            }

        })

        // $('#list_menu_right').on('click',function(){

        //   $(".main_content").css("margin-right", "280px");
        //     //$(".footer_main_content").css("margin-left", "0");
        //     $(".rightsidenav").css("margin-right", "0");
        //     $(this).css("display", "none");
        //  })
        // $('#rightnavclose').on('click',function(){

        //   $(".main_content").css("margin-right", "0");
        //     //$(".footer_main_content").css("margin-left", "0");
        //     $(".rightsidenav").css("margin-right", "-280px");
        //     $('#list_menu_right').css("display", "block");
        //  })

        function openrightCart() {
            $(".main_content").css("margin-right", "280px");
            //$(".footer_main_content").css("margin-left", "0");
            $(".rightsidenav").css("margin-right", "0");
            $('#list_menu_right').css("display", "none");
            $('#right_side_carts').addClass('open_cart');
            // $('.pro_list_block').css({
            //     'max-width': '33.3333%',
            //     'flex': '0 0 33.3333%'
            // });
            $('#discounted_products').removeClass('row-cols-xxl-5 row-cols-lg-4');
            $('#discounted_products').addClass('row-cols-xxl-4 row-cols-lg-3');
        }

        function closerightCart() {
            $(".main_content").css("margin-right", "0");
            //$(".footer_main_content").css("margin-left", "0");
            $(".rightsidenav").css("margin-right", "-280px");
            $('#list_menu_right').css("display", "block");
            $('#right_side_carts').removeClass('open_cart');
            $('.pro_list_block').removeAttr("style");
        }


        $(document).ready(function() {
            $('.category-nav-element').each(function(i, el) {
                $(el).on('mouseover', function() {
                    if (!$(el).find('.sub-cat-menu').hasClass('loaded')) {
                        $.post('{{ route("category.elements") }}', {
                                _token: AIZ.data.csrf,
                                id: $(el).data('id')
                            },
                            function(data) {
                                $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                            });
                    }
                });
            });
            // if ($('#lang-change').length > 0) {
            //  $('#lang-change .dropdown-menu a').each(function() {
            $('.lang-change').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var locale = $this.data('flag');
                $.post('{{ route("language.change") }}', {
                        _token: AIZ.data.csrf,
                        locale: locale
                    },
                    function(data) {
                        location.reload();
                    });

            });
            //    });
            //   }

            if ($('#currency-change').length > 0) {
                $('#currency-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e) {
                        e.preventDefault();
                        var $this = $(this);
                        var currency_code = $this.data('currency');
                        $.post('{{ route("currency.change") }}', {
                                _token: AIZ.data.csrf,
                                currency_code: currency_code
                            },
                            function(data) {
                                location.reload();
                            });

                    });
                });
            }
        });

        $('#searchm').on('keyup', function() {
            searchm();
        });

        $('#searchm').on('focus', function() {
            searchm();
        });

        function searchm() {
            var searchKey = $('#searchm').val();
            if (searchKey.length > 0) {
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route("search.ajax") }}', {
                        _token: AIZ.data.csrf,
                        search: searchKey
                    },
                    function(data) {
                        if (data == '0') {
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-contentm').html(null);
                            $('.typed-search-box .search-nothing').removeClass('d-none').html('Sorry, nothing found for <strong>"' + searchKey + '"</strong>');
                            $('.search-preloader').addClass('d-none');

                        } else {
                            $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                            $('#search-contentm').html(data);
                            $('.search-preloader').addClass('d-none');
                        }
                    });
            } else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        $('#search').on('keyup', function() {
            search();
        });

        $('#search').on('focus', function() {
            search();
        });

        function search() {
            var searchKey = $('#search').val();
            if (searchKey.length > 0) {
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route("search.ajax") }}', {
                        _token: AIZ.data.csrf,
                        search: searchKey
                    },
                    function(data) {
                        if (data == '0') {
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content').html(null);
                            $('.typed-search-box .search-nothing').removeClass('d-none').html('Sorry, nothing found for <strong>"' + searchKey + '"</strong>');
                            $('.search-preloader').addClass('d-none');

                        } else {
                            $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                            $('#search-content').html(data);
                            $('.search-preloader').addClass('d-none');
                        }
                    });
            } else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        function updateNavCart() {
            $.post('{{ route("cart.nav_cart") }}', {
                _token: AIZ.data.csrf
            },
            function(data) {
                $('#cart_items').html(data);
            });

            $.post('{{ route("cart.updateRightCart") }}', {
                _token: AIZ.data.csrf
            },
            function(data) {
                $('#right_side_carts').html(data);
                if ($('#right_side_carts').hasClass('open_cart') == true) {
                    openrightCart();
                } else {
                    closerightCart();
                }
                var to = $('#sightsidecarttotal').text();
                if (to == 1) {
                    openrightCart();
                }
            });
        }

        function removeFromCart(key, id) {
            $.post('{{ route("cart.removeFromCart") }}', {
                _token: AIZ.data.csrf,
                key: key
            },
            function(data) {
                updateNavCart();
                $('#cart-summary').html(data);
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) - 1);
                $('#cart-summary').html(data);

                $('#product_' + id).prev('button').attr('data-key', '');
                $('#product_' + id).val(0);
                $('#product_' + id).prev('button').attr('data-key', '');

                // Update the quantity display
                $('#productlist_' + id).text(0);
                $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + 0 + ',' + id + ')');
                $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + 0 + ',' + id + ')');
                $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + 0 + ',' + id + ')');

                $('#pro_cart_in_' + id).hide();
                $('#pro_add_to_cart_' + id).show();
                
                AIZ.plugins.notify('success', 'Item has been removed from cart');
            });
        }

        function addToCompare(id) {
            $.post('{{ route("compare.addToCompare") }}', {
                _token: AIZ.data.csrf,
                id: id
            },
            function(data) {
                $('#compare').html(data);
                AIZ.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html()) + 1);
            });
        }

        function addToWishList(id) {
            @if(Auth::check() && (Auth::user()->user_type == 'customer' || Auth::user()->user_type == 'seller'))
                $.post('{{ route("wishlists.store") }}', {
                    _token: AIZ.data.csrf,
                    id: id
                },
                function(data) {
                    if (data != 0) {
                        $('#wishlist').html(data.view);
                        
                        $('#option-choice-form_' + id + ' .aiz-p-hov-icon a.wishlist').removeClass('inactive');
                        $('#option-choice-form_' + id + ' .aiz-p-hov-icon a.wishlist').addClass('active');
                        $('#option-choice-form_' + id + ' .aiz-p-hov-icon a.wishlist').attr('onclick', 'removeFromWishlist(' + data.wid + ',' + id + ')');
                        $('#option-choice-form_' + id + ' .aiz-p-hov-icon a.wishlist').attr('data-title', '{{ translate("Remove from wishlist") }}');
                        $('#option-choice-form_' + id + ' .aiz-p-hov-icon a.wishlist').attr('data-original-title', '{{ translate("Remove from wishlist") }}');

                        AIZ.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    } else {
                        AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
            @else
                AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
            @endif
        }

        function removeFromWishlist(id, pid) {
            $.post('{{ route("wishlists.remove") }}', {
                _token: '{{ csrf_token() }}',
                id: id
            },
            function(data) {
                $('#wishlist').html(data);
                $('#option-choice-form_' + pid + ' .aiz-p-hov-icon a.wishlist').removeClass('active');
                $('#option-choice-form_' + pid + ' .aiz-p-hov-icon a.wishlist').addClass('inactive');
                $('#option-choice-form_' + pid + ' .aiz-p-hov-icon a.wishlist').attr('onclick', 'addToWishList(' + pid + ')');
                $('#option-choice-form_' + pid + ' .aiz-p-hov-icon a.wishlist').attr('data-title', '{{ translate("Add to wishlist") }}');
                $('#option-choice-form_' + pid + ' .aiz-p-hov-icon a.wishlist').attr('data-original-title', '{{ translate("Add to wishlist") }}');

                AIZ.plugins.notify('success', 'Item has been removed from wishlist');
            });
        }

        function showAddToCartModal(id) {
            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route("cart.showCartModal") }}', {
                _token: AIZ.data.csrf,
                id: id
            },
            function(data) {
                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }

        // $('#option-choice-form input').on('change', function() {
        //     getVariantPrice();
        // });

        // function getVariantPrice() {
        //     if ($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
        //         $.ajax({
        //             type: "POST",
        //             url: '{{ route("products.variant_price") }}',
        //             data: $('#option-choice-form').serializeArray(),
        //             success: function(data) {
        //                 if (data.status == true) {
        //                     $('.product-gallery-thumb .carousel-box').each(function(i) {
        //                         if ($(this).data('variation') && data.variation == $(this).data('variation')) {
        //                             $('.product-gallery-thumb').slick('slickGoTo', i);
        //                         }
        //                     })

        //                     $('#option-choice-form #chosen_price_div').removeClass('d-none');
        //                     $('#option-choice-form #chosen_price_div #chosen_price').html(data.price);
        //                     $('#available-quantity').html(data.quantity);
        //                 } else {
        //                     $('#option-choice-form input[name=quantity]').val(data.quantity)
        //                     AIZ.plugins.notify('warning', data.msg);
        //                 }
        //             }
        //         });
        //     }
        // }

        function checkAddToCartValidity() {
            var names = {};
            $('#option-choice-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });

            if ($('#option-choice-form input:radio:checked').length == count) {
                return true;
            }
            return false;
        }

        function addToCart() {
            if (checkAddToCartValidity()) {
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type: "POST",
                    url: '{{ route("cart.addToCart") }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data) {
                        $('#addToCart-modal-body').html(null);
                        $('.c-preloader').hide();
                        $('#modal-size').removeClass('modal-lg');
                        $('#addToCart-modal-body').html(data.view);
                        updateNavCart();
                        $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                    }
                });
            } else {
                AIZ.plugins.notify('warning', 'Please choose all the options');
            }
        }

        function buyNow() {
            if (checkAddToCartValidity()) {
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type: "POST",
                    url: '{{ route("cart.addToCart") }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data) {
                        if (data.status == 1) {
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                            // cartAnimation($('#prod_id').val());
                            window.location.replace("{{ route('cart') }}");
                        } else {
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.view);
                        }
                    }
                });
            } else {
                AIZ.plugins.notify('warning', 'Please choose all the options');
            }
        }

        //         function directAdd(id, currentQty, maxQty) {
        //     $('.c-preloader').show();

        //     // Check if the requested quantity exceeds the max quantity
        //     var requestedQty = currentQty + 1;
        //     if (requestedQty > maxQty) {
        //         AIZ.plugins.notify('warning', `You can only add a maximum of ${maxQty} of this product.`);
        //         $('.c-preloader').hide();
        //         return; // Exit if max quantity is reached
        //     }

        //     $.ajax({
        //         type: "POST",
        //         url: '{{ route('cart.addToCart') }}',
        //         data: $('#option-choice-form_' + id).serializeArray(),
        //         success: function(data) {
        //             if (data.status == 1) {
        //                 AIZ.plugins.notify('success', "{{ translate('Item has been added to cart list') }}");

        //                 // Update quantity display
        //                 $('#productlist_' + id).text(requestedQty);

        //                 // Update button actions for quantity changes
        //                 var key = Number($('#sightsidecarttotal').text());
        //                 $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + requestedQty + ', ' + id + ')');
        //                 $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + requestedQty + ', ' + id + ')');

        //                 updateNavCart();
        //             } else {
        //                 AIZ.plugins.notify('warning', data.msg);
        //             }
        //             $('.c-preloader').hide();
        //         }
        //     });

        //     $('#pro_cart_in_' + id).show();
        //     $('#pro_add_to_cart_' + id).hide();
        // }

        function show_purchase_history_details(order_id) {
            $('#order-details-modal-body').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route("purchase_history.details") }}', {
                _token: AIZ.data.csrf,
                order_id: order_id
            },
            function(data) {
                $('#order-details-modal-body').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
            });
        }

        function show_order_details(order_id) {
            $('#order-details-modal-body').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route("orders.details") }}', {
                _token: AIZ.data.csrf,
                order_id: order_id
            },
            function(data) {
                $('#order-details-modal-body').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
            });
        }

        function cartQuantityInitialize() {
            $('.btn-number').click(function(e) {
                e.preventDefault();

                fieldName = $(this).attr('data-field');
                type = $(this).attr('data-type');
                var input = $("input[name='" + fieldName + "']");
                var currentVal = parseInt(input.val());

                if (!isNaN(currentVal)) {
                    if (type == 'minus') {

                        if (currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }

                    } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        }

                    }
                } else {
                    input.val(0);
                }
            });

            $('.input-number').focusin(function() {
                $(this).data('oldValue', $(this).val());
            });

            $('.input-number').change(function() {

                minValue = parseInt($(this).attr('min'));
                maxValue = parseInt($(this).attr('max'));
                valueCurrent = parseInt($(this).val());

                name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    alert('Sorry, the minimum value was reached');
                    $(this).val($(this).data('oldValue'));
                }
                if (valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    alert('Sorry, the maximum value was reached');
                    $(this).val($(this).data('oldValue'));
                }


            });
            $(".input-number").keydown(function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        function imageInputInitialize() {
            $('.custom-input-file').each(function() {
                var $input = $(this),
                    $label = $input.next('label'),
                    labelVal = $label.html();

                $input.on('change', function(e) {
                    var fileName = '';

                    if (this.files && this.files.length > 1)
                        fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
                    else if (e.target.value)
                        fileName = e.target.value.split('\\').pop();

                    if (fileName)
                        $label.find('span').html(fileName);
                    else
                        $label.html(labelVal);
                });

                // Firefox bug fix
                $input
                    .on('focus', function() {
                        $input.addClass('has-focus');
                    })
                    .on('blur', function() {
                        $input.removeClass('has-focus');
                    });
            });
        }



        //          $('.add-to-cart').on('click', function () {
        //         var cart = $('#cart_items');
        //         var imgtodrag = $(this).parents('.aiz-card-box').find("img").eq(0);
        //         if (imgtodrag) {
        //             var imgclone = imgtodrag.clone()
        //                 .offset({
        //                 top: imgtodrag.offset().top,
        //                 left: imgtodrag.offset().left
        //             })
        //                 .css({
        //                 'opacity': '1',
        //                     'position': 'absolute',
        //                     'height': '170px',
        //                     'width': '170px',
        //                     'z-index': '1000'
        //             })
        //                 .appendTo($('body'))
        //                 .animate({
        //                 'top': cart.offset().top + 10,
        //                     'left': cart.offset().left + 10,
        //                     'width': 75,
        //                     'height': 75
        //             }, 1500, 'easeInOutExpo');

        //             // setTimeout(function () {
        //             //     cart.effect("shake", {
        //             //         times: 2
        //             //     }, 1000);
        //             // }, 2500);

        //             imgclone.animate({
        //                 'width': 0,
        //                     'height': 0
        //             }, function () {
        //                 $(this).detach()
        //             });
        //         }
        //     });

        //     function cartAnimation(id){
        //         var cart = $('#cart_items');
        //         var imgtodrag = $('#addtocart_'+id).parents('.aiz-card-box').find("img").eq(0);
        //         if (imgtodrag) {
        //             var imgclone = imgtodrag.clone()
        //                 .offset({
        //                 top: imgtodrag.offset().top,
        //                 left: imgtodrag.offset().left
        //             })
        //                 .css({
        //                 'opacity': '1',
        //                     'position': 'absolute',
        //                     'height': '170px',
        //                     'width': '170px',
        //                     'z-index': '9999999000'
        //             })
        //                 .appendTo($('body'))
        //                 .animate({
        //                 'top': cart.offset().top + 10,
        //                     'left': cart.offset().left + 10,
        //                     'width': 75,
        //                     'height': 75
        //             }, 1500, 'easeInOutExpo');

        //             // setTimeout(function () {
        //             //     cart.effect("shake", {
        //             //         times: 2
        //             //     }, 1000);
        //             // }, 2500);

        //             imgclone.animate({
        //                 'width': 0,
        //                     'height': 0
        //             }, function () {
        //                // $('#addtocart_'+id).detach()
        //             });
        //         }
        //     }
    </script>

    @yield('script')

    @php
    echo get_setting('footer_script');
    @endphp
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#testimonial-slider").owlCarousel({
                items: 3,
                itemsDesktop: [1000, 3],
                itemsDesktopSmall: [980, 2],
                itemsTablet: [768, 2],
                itemsMobile: [650, 1],
                pagination: true,
                navigation: false,
                slideSpeed: 1000,
                autoPlay: true
            });
        });

        function directAdd(id, checking = false) {

            $('.c-preloader').show();
            var proqty = parseInt($('#productlist_' + id).text());
            proqty = (!proqty) ? 0 : proqty;

            $.ajax({
                type: "POST",
                url: '{{ route("cart.addToCart") }}',
                data: $('#option-choice-form_' + id).serializeArray(),
                success: function(data) {
                    if (data.status == 1) {
                        AIZ.plugins.notify('success', "{{ translate('Item has been added to cart list') }}");
                        //     $('#addToCart').modal();
                        //     $('#addToCart-modal-body').html(null);
                        //    $('.c-preloader').hide();
                        //    $('#modal-size').removeClass('modal-lg');
                        //    $('#addToCart-modal-body').html(data.view);
                        if (!checking)
                            var key = Number($('#sightsidecarttotal').text());
                        else {
                            var key = Number(($('#productlist_' + id).parents('button').prev('button').attr('onclick').split(',')[0]).split('(')[1]);
                        }

                        $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                        $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                        $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (proqty + 1) + ',' + id + ')');
                        updateNavCart();
                        //cartAnimation(id);
                        //  $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())+1);
                    } else {
                        AIZ.plugins.notify('warning', data.msg);
                    }
                    $('.c-preloader').hide();
                }
            });

            if (proqty >= 1) {
                $('#productlist_' + id).text(proqty + 1);
            } else {
                $('#productlist_' + id).text(1);
            }

            $('#pro_cart_in_' + id).show();
            $('#pro_add_to_cart_' + id).hide();
        }

        function wait(milliseconds) {
            return new Promise(resolve => setTimeout(resolve, milliseconds));
        }

        async function updateQuantityAdd(key, element, id) {
            $('#productlist_' + id).text(element + 1);
            $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (element + 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (element + 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (element + 1) + ',' + id + ')');
            // Wait for 1 second before proceeding
            await wait(1000);

            $.post('{{ route("cart.updateQuantity") }}', {
                    _token: '{{ csrf_token() }}',
                    key: key,
                    quantity: (element + 1)
                },
                function(data) {
                    if (data == 0) {
                        // AIZ.plugins.notify('success', 'Item updated to your cart!');
                        updateNavCart();
                    } else {
                        AIZ.plugins.notify('warning', data);
                    }
                }
            );
        }

        async function updateQuantityPlus(key, element, id) {
            $('#productlist_' + id).text(element + 1);
            $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (element + 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (element + 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (element + 1) + ',' + id + ')');

            // Wait for 1 second before proceeding
            await wait(1000);

            $.post('{{ route("cart.updateQuantity") }}', {
                _token: '{{ csrf_token() }}',
                key: key,
                quantity: (element + 1)
            },
            function(data) {
                if (data == 0) {
                    // AIZ.plugins.notify('success', 'Item updated to your cart!');
                    updateNavCart();
                } else {
                    AIZ.plugins.notify('warning', data);
                } 
            });
        }

        async function updateQuantityMinus(key, element, id) {
            // Ensure the element does not go below 0
            if (element <= 0) {
                // You can choose to show a warning or handle this case accordingly
                AIZ.plugins.notify('warning', 'Quantity cannot be less than 0.');
                return; // Exit the function if the quantity is already 0
            }

            // Update the quantity display
            $('#productlist_' + id).text(element - 1);

            // Update the button for increasing and decreasing quantities
            $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (element - 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (element - 1) + ',' + id + ')');
            $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (element - 1) + ',' + id + ')');

            // Wait for 1 second before proceeding
            await wait(1000);

            // Send the AJAX request only if the quantity is greater than 0
            if (element > 0) {
                $.post('{{ route("cart.updateQuantity") }}', {
                        _token: '{{ csrf_token() }}',
                        key: key,
                        quantity: (element - 1)
                    },
                    function(data) {
                        updateNavCart();

                        // If the quantity is 1, handle the item removal from the cart
                        if (element == 1) {
                            removeFromCart(key);
                            $('#pro_cart_in_' + id).hide();
                            $('#pro_add_to_cart_' + id).show();
                        }
                    }
                );
            }
        }

        function showCheckoutModal() {
            $('#GuestCheckout').modal();
        }

        // async function directAddSingle(id, checking = false) {
        //     // Wait for 1 second before proceeding
        //     await wait(1000);

        //     $('.c-preloader').show();
        //     var proqty = parseInt($('#product_' + id).val());
        //     proqty = (!proqty) ? 0 : proqty;

        //     $.ajax({
        //         type: "POST",
        //         url: '{{ route("cart.addToCart") }}',
        //         data: $('#option-choice-form').serializeArray(),
        //         success: function(data) {
        //             if (data.status == 1) {
        //                 AIZ.plugins.notify('success', "{{ translate('Item has been added to cart list') }}");
        //                 if (!checking) {
        //                     var key = Number($('#sightsidecarttotal').text());
        //                 } else {
        //                     var key = Number(($('#product_' + id).prev('button').attr('onclick').split(',')[0]).split('(')[1]);
        //                 }

        //                 getVariantPrice();
        //                 updateNavCart();
        //                 // $('#product_' + id).val(proqty + 1);
        //                 $('#product_' + id).prev('button').attr('onclick', 'updateQuantityMinusSingle(' + key + ', ' + (proqty + 1) + ',' + id + ')');
        //                 $('#product_' + id).next('button').attr('onclick', 'updateQuantityPlusSingle(' + key + ', ' + (proqty + 1) + ',' + id + ')');
        //             } else {
        //                 AIZ.plugins.notify('warning', data.msg);
        //             }
        //             $('.c-preloader').hide();
        //         }
        //     });

        //     // if (proqty >= 1) {
        //     //     $('#product_' + id).val(proqty + 1);
        //     // } else {
        //     //     $('#product_' + id).val(1);
        //     // }

        //     $('#pro_cart_in_' + id).show();
        //     $('#pro_add_to_cart_' + id).hide();
        // }

        // async function updateQuantityPlusSingle(key, element, id) {
        //     getVariantPrice();
        //     // $('#product_' + id).val(element + 1);
        //     $('#product_' + id).prev('button').attr('onclick', 'updateQuantityMinusSingle(' + key + ', ' + (element + 1) + ',' + id + ')');
        //     $('#product_' + id).next('button').attr('onclick', 'updateQuantityPlusSingle(' + key + ', ' + (element + 1) + ',' + id + ')');

        //     $('#productlist_' + id).text(element + 1);
        //     $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (element + 1) + ',' + id + ')');
        //     $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (element + 1) + ',' + id + ')');
        //     $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (element + 1) + ',' + id + ')');
            
        //     await wait(1000);
        //     $.post('{{ route("cart.updateQuantity") }}', {
        //         _token: '{{ csrf_token() }}',
        //         key: key,
        //         quantity: (element + 1)
        //     },
        //     function(data) {
        //         if (data == 0) {
        //             updateNavCart();
        //         } else {
        //             AIZ.plugins.notify('warning', data);
        //         } 
        //     });
        // }

        // async function updateQuantityMinusSingle(key, element, id) {
        //     if (element <= 0) {
        //         AIZ.plugins.notify('warning', 'Quantity cannot be less than 0.');
        //         return;
        //     }

        //     getVariantPrice();
        //     // $('#product_' + id).val(element - 1);
        //     $('#product_' + id).next('button').attr('onclick', 'updateQuantityPlusSingle(' + key + ', ' + (element - 1) + ',' + id + ')');
        //     $('#product_' + id).prev('button').attr('onclick', 'updateQuantityMinusSingle(' + key + ', ' + (element - 1) + ',' + id + ')');

        //     $('#productlist_' + id).text(element - 1);
        //     $('#productlist_' + id).parents('button').attr('onclick', 'updateQuantityAdd(' + key + ', ' + (element - 1) + ',' + id + ')');
        //     $('#productlist_' + id).parents('button').next('button').attr('onclick', 'updateQuantityPlus(' + key + ', ' + (element - 1) + ',' + id + ')');
        //     $('#productlist_' + id).parents('button').prev('button').attr('onclick', 'updateQuantityMinus(' + key + ', ' + (element - 1) + ',' + id + ')');

        //     await wait(1000);
        //     if (element > 0) {
        //         $.post('{{ route("cart.updateQuantity") }}', {
        //             _token: '{{ csrf_token() }}',
        //             key: key,
        //             quantity: (element - 1)
        //         },
        //         function(data) {
        //             updateNavCart();

        //             if (element == 1) {
        //                 removeFromCart(key);
        //                 $('#pro_cart_in_' + id).hide();
        //                 $('#pro_add_to_cart_' + id).show();
        //             }
        //         });
        //     }
        // }
    </script>

    <script>
        window.addEventListener("pageshow", function (event) {
            var historyTraversal = event.persisted;
            var perf = window.performance;
            var perfEntries = perf && perf.getEntriesByType && perf.getEntriesByType("navigation");
            var perfEntryType = perfEntries && perfEntries[0] && perfEntries[0].type;
            var navigationType = perf && perf.navigation && perf.navigation.type;

            if (historyTraversal || perfEntryType === "back_forward" || navigationType === 2 ) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>