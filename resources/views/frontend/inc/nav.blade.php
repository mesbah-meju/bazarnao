<!-- Top Bar -->
<div class="d-none d-lg-block top-navbar bg-white border-bottom border-soft-secondary z-1035">
    <div class="container-fluid">

    </div>
</div>
<!-- END Top Bar -->
<header style="background: #dba9c9 !important;" class="@if(get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white border-bottom shadow-sm d-none d-lg-block">
    <div class="position-relative logo-bar-area z-1">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
            <div class="col-xl-2 d-none d-lg-block ml-5 mr-0" id="list_menu">
            <svg style="color:#AF3C87;" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
</svg>
            
            </div>
            
                <div class="col-auto col-xl-2 pl-0 pr-3 d-flex align-items-center">
                    <ul class="list-inline mt-3 social colored text-center">
                        
                        <li class="list-inline-item">
                            <div class="d-flex">
                            <a href="{{ route('contact.emergency_contact') }}" alt="Emergency Contacts"  target="_blank" class="">
                                <img src="{{ static_asset('assets/img/emergency.png') }}" alt="Emergency Contact" class=" h-30px">
                            </a>
                            <p style="font-size:6px; color:#fff;">Emergency <br>Contact</p>
                        </div>
                        </li>
                    </ul>
                    <a class="" href="{{ route('home') }}" style="padding:10px;">
                        @php
                       
                        $header_logo = get_setting('header_logo');
                        $header_height = '45';
                       
                        @endphp
                        @if($header_logo != null)
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-{{$header_height}}px" height="{{$header_height}}">
                        {{-- <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-{{$header_height}}px" height="{{$header_height}}"> --}}
                        @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-{{$header_height}}px" height="{{$header_height}}">
                        @endif
                    </a>

                    @if(Route::currentRouteName() != 'home')
                    <!-- <div class="d-none d-xl-block align-self-stretch category-menu-icon-box ml-auto mr-0">
                        <div class="h-100 d-flex align-items-center" id="category-menu-icon">
                            <div class="dropdown-toggle navbar-light bg-light h-40px w-50px pl-2 rounded border c-pointer">
                                <span class="navbar-toggler-icon"></span>
                            </div>
                        </div>
                    </div> -->
                    @endif
                </div>
                <!-- <div class="d-lg-none ml-auto mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div> -->

                <div class="flex-grow-1 front-header-search d-flex align-items-center">
                    <div class="flex-grow-1">
                        <form action="" method="GET" class="stop-propagation">
                            <div class="d-flex align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="border-0 border-lg form-control" id="search" name="q" placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="la la-search la-flip-horizontal fs-18"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px;overflow-y: auto;max-height: 500px;overflow-x: hidden;">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-content" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-lg-none ml-3 mr-0">
                    <div class="nav-search-box">
                        <a href="#" class="nav-box-link">
                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                        </a>
                    </div>
                </div>

                <!-- <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="compare">
                    <a href="blog" class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                            Blogs
                        </a>
                    </div>
                </div> -->

                <div class="d-none d-lg-block  align-self-stretch ml-3 mr-0" data-hover="dropdown">
                    <div class="nav-cart-box dropdown h-100" id="cart_items" style="margin-right:10px;">
                        @include('frontend.partials.cart')
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="wishlist">
                        @include('frontend.partials.wishlist')
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="dropdown" id="singin_block">
                    @include('frontend.partials.login')
                    </div>
                </div>

                

                <div class="d-none d-lg-block ml-3 mr-0" data-hover="dropdown">
                <div class="nav-cart-box dropdown h-100" id="header_lang" style="margin-right:10px;">
                
                   @if(get_setting('show_language_switcher') == 'on')
                        
                           @php
                           if(Session::has('locale')){
                           $locale = Session::get('locale', Config::get('app.locale'));
                           }
                           else{
                           $locale = 'en';
                           }
                           @endphp
                          
                               @foreach (App\Models\Language::all() as $key => $language)
                              
                                   <a data-flag="{{ $language->code }}" class="lang-change @if($locale == $language->code) active-lang @endif">
                                       <span class="language">{{ translate(strtoupper($language->code)) }}</span>
                       </a>  
                                @if($key == 0)
                                <span class="lang-change">|</span>
                                @endif   
                               @endforeach
                               
                       @endif
                </div>
                </div>

            </div>
        </div>
        <!-- @if(Route::currentRouteName() != 'home')
        <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3" id="hover-category-menu">
            <div class="container-fluid">
                <div class="row gutters-10 position-relative">
                    <div class="col-lg-3 position-static">
                        @include('frontend.partials.category_menu')
                    </div>
                </div>
            </div>
        </div>
        @endif -->
    </div>
    @if ( get_setting('header_menu_labels') != null )
    <div class="bg-white border-top border-gray-200 py-1">
        <div class="container-fluid">
            <ul class="list-inline mb-0 pl-0 mobile-hor-swipe text-center">
                @foreach (json_decode( get_setting('header_menu_labels'), true) as $key => $value)
                <li class="list-inline-item mr-0">
                    <a href="{{ json_decode( get_setting('header_menu_links'), true)[$key] }}" class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                        {{ $value }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</header>
<header class="@if(get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white border-bottom shadow-sm d-block d-lg-none">
    <div class="position-relative logo-bar-area z-1">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
            <div class=" mw-10 mr-0" id="list_menu_mobile" style="width: 20%;">
            <!-- <svg style="color:#AF3C87;" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
</svg> -->
<i style="font-size: 35px;font-weight: bold;color:#AF3C87;" class="las la-list-ul la-2x d-inline-block nav-box-icon"></i>
            
            </div>
            
                <div class="col-auto  pl-0 pr-0 d-flex align-items-center" style="width: 50%;">
                    <a class="" href="{{ route('home') }}" style="padding:0px 0px 10px 15px;">
                        @php
                        if($locale=='en'){
                        $header_logo = get_setting('header_logo');
                        $header_height = '45';
                        }else{
                        $header_logo = get_setting('header_logo_bangla');
                        $header_height = '60';
                        }
                        @endphp
                        @if($header_logo != null)
                        <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-40px h-md-{{$header_height}}px" height="{{$header_height}}">
                        @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-50px h-md-{{$header_height}}px" height="{{$header_height}}">
                        @endif
                    </a>

                    @if(Route::currentRouteName() != 'home')
                    <!-- <div class="d-none d-xl-block align-self-stretch category-menu-icon-box ml-auto mr-0">
                        <div class="h-100 d-flex align-items-center" id="category-menu-icon">
                            <div class="dropdown-toggle navbar-light bg-light h-40px w-50px pl-2 rounded border c-pointer">
                                <span class="navbar-toggler-icon"></span>
                            </div>
                        </div>
                    </div> -->
                    @endif
                </div>
                <!-- <div class="d-lg-none ml-auto mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div> -->

                <div class="flex-grow-1 front-header-search d-flex align-items-center">
                    <div class="flex-grow-1">
                        <form action="" method="GET" class="stop-propagation">
                            <div class="d-flex align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="border-0 border-lg form-control" id="searchm" name="q" placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="la la-search la-flip-horizontal fs-18"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px;overflow-y: auto;max-height: 500px;overflow-x: hidden;">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-contentm" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

                

                <!-- <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="compare">
                    <a href="blog" class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                            Blogs
                        </a>
                    </div>
                </div> -->

                <div class="d-block d-lg-block  align-self-stretch ml-3 mr-0" data-hover="dropdown" style="width: 15%;">
                    <div class="nav-cart-box dropdown h-100" id="cart_items" style="margin-right:10px;">
                        @include('frontend.partials.cart')
                    </div>
                </div>

                <!-- <div class="d-block d-lg-none ml-3 mr-0" style="width: 15%;">
                    <div class="nav-search-box">
                        <a href="#" class="nav-box-link">
                            <i style="font-size: 30px;font-weight: bold;" class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                        </a>
                    </div>
                </div> -->
                <div class="d-lg-none ml-auto mr-0" style="width: 15%;">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i style="font-size: 30px;font-weight: bold;color:#AE3C86" class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="wishlist">
                        @include('frontend.partials.wishlist')
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="dropdown" id="singin_block">
                    @include('frontend.partials.login')
                    </div>
                </div>

                

                <div class="d-none d-lg-block ml-3 mr-0" data-hover="dropdown">
                <div class="nav-cart-box dropdown h-100" id="header_lang" style="margin-right:10px;">
                
                   @if(get_setting('show_language_switcher') == 'on')
                        
                           @php
                           if(Session::has('locale')){
                           $locale = Session::get('locale', Config::get('app.locale'));
                           }
                           else{
                           $locale = 'en';
                           }
                           @endphp
                          
                               @foreach (App\Models\Language::all() as $key => $language)
                              
                                   <a data-flag="{{ $language->code }}" class="lang-change @if($locale == $language->code) active-lang @endif">
                                       <span class="language">{{ translate(strtoupper($language->code)) }}</span>
                       </a>  
                                @if($key == 0)
                                <span class="lang-change">|</span>
                                @endif   
                               @endforeach
                               
                       @endif
                </div>
                </div>

            </div>
        </div>
        <!-- @if(Route::currentRouteName() != 'home')
        <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3" id="hover-category-menu">
            <div class="container-fluid">
                <div class="row gutters-10 position-relative">
                    <div class="col-lg-3 position-static">
                        @include('frontend.partials.category_menu')
                    </div>
                </div>
            </div>
        </div>
        @endif -->
    </div>
    @if ( get_setting('header_menu_labels') != null )
    <div class="bg-white border-top border-gray-200 py-1">
        <div class="container-fluid">
            <ul class="list-inline mb-0 pl-0 mobile-hor-swipe text-center">
                @foreach (json_decode( get_setting('header_menu_labels'), true) as $key => $value)
                <li class="list-inline-item mr-0">
                    <a href="{{ json_decode( get_setting('header_menu_links'), true)[$key] }}" class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                        {{ $value }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</header>