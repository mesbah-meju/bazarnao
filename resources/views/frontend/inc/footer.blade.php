<style>
.copyrght a{
	color:#FEFF15;
}
</style>
 @php
                        if(Session::has('locale')){
                        $locale = Session::get('locale', Config::get('app.locale'));
                        }
                        else{
                        $locale = 'en';
                        }
                        @endphp

<section class="pb-8 pb-xl-3 text-dark" style="background:#EDEDED !important">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-xl-3 text-center text-md-left">
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="d-block">
                    
                    @php
                        if($locale=='en')
                        $footer_logo = get_setting('footer_logo');
                        else
                        $footer_logo = get_setting('footer_logo_bangla');
                        @endphp
                        @if($footer_logo != null)
                            <img class="lazyload" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($footer_logo) }}" alt="{{ env('APP_NAME') }}" height="44">
                        @else
                            <img class="lazyload" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" height="44">
                        @endif
                    </a>
                    <div class="my-3">
                        @php
                        if($locale=='en')
                            echo get_setting('about_us_description');
                            else
                            echo get_setting('about_us_description_bangla');
                        @endphp
                    </div>
                    <div class="d-inline-block d-md-block">
                    
                        <ul class="list-inline my-3 my-md-0 social colored text-center">
                    @if ( get_setting('facebook_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('twitter_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('instagram_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('youtube_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('linkedin_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                    </li>
                    @endif
                </ul>
                <ul class="footrt_app_icone list-inline social colored text-center">
                <li><a style="width:100%" target="_blank" href="https://apps.apple.com/us/app/apple-store/id1576185793"><img  src="{{ url('app.png') }}"></a></li>
                <li><a style="width:100%" target="_blank" href="https://play.google.com/store/apps/details?id=com.bazarnao.app"><img  src="{{ url('play.png') }}"></a></li>
                </ul>

                    </div>
                </div>
            </div>
            <div class="col-lg-3  col-md-3 mr-0">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600  pb-2 mb-1">
                        {{ translate('Contact Info') }}
                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                           <span class="d-block">{{ translate('Address') }}:</span>
                           <span class="d-block">{{ get_setting('contact_address') }}</span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block">{{translate('Phone')}}:</span>
                           <span class="d-block"><a href="tel:{{ get_setting('contact_phone') }}">{{ get_setting('contact_phone') }}</a></span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block">{{translate('Email')}}:</span>
                           <span class="d-block">
                               <a href="mailto:{{ get_setting('contact_email') }}" class="text-reset">{{ get_setting('contact_email')  }}</a>
                            </span>
                        </li>
						 <li class="mb-2">
                             <form class="form-inline" method="POST" action="{{ route('subscribers.store') }}">
                            @csrf
                            <div class="form-group" style="width:100%;margin-bottom:5px">
                                <input type="email"  style="width:100%" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-small" style="width:100%">
                                {{ translate('Subscribe') }}
                            </button>
                        </form>
                        </li>
                    </ul>
                </div>
            </div>
                 
            <div class="col-md-3 col-lg-5">
                <div class="row">
                    <div class="col-md-6">
                    <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600   pb-2 mb-1">
                        {{ translate('My Account') }}
                    </h4>
                    <ul class="list-unstyled">
                        @if (Auth::check())
                            <li class="mb-2">
                                <a class="text-reset" href="{{ route('logout') }}">
                                    {{ translate('Logout') }}
                                </a>
                            </li>
                        @else
                            <li class="mb-2">
                                <a class="text-reset" href="{{ route('user.login') }}">
                                    {{ translate('Login') }}
                                </a>
                            </li>
                        @endif
                        <li class="mb-2">
                            <a class="text-reset" href="{{ route('purchase_history.index') }}">
                                {{ translate('Order History') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="text-reset" href="{{ route('wishlists.index') }}">
                                {{ translate('My Wishlist') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="text-reset" href="{{ route('orders.track') }}">
                                {{ translate('Track Order') }}
                            </a>
                        </li>
                        <li class="mb-2">
                        <a href="blog" class="text-reset">
                        {{ translate('Blogs')}}
                        </a>
                        </li>
                        <li class="mb-2">
                        <a href="companyinfo" class="text-reset">
                        {{ translate('Company Information')}}
                        </a>
                        </li>
                        @if (App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                            <!--<li class="mb-2">
                                <a class="text-reset" href="{{ route('affiliate.apply') }}">{{ translate('Be an affiliate partner')}}</a>
                            </li> -->
                        @endif
                    </ul>
                </div>
                    </div>
                    <div class="col-md-6">   
                    <ul class="list-unstyled mt-4 text-center">
                        
                            <li class="mb-2">
                                <a class="text-reset" href="{{ route('terms') }}">
                                    {{ translate('Terms & conditions') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="{{ route('returnpolicy') }}">
                                    {{ translate('Return Policy') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="{{ route('privacypolicy') }}">
                                    {{ translate('Privacy Policy') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="faq">
                                    {{ translate('faq') }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="">
                                    About Bazar Nao
                                </a>
                            </li>
                    </ul>
                    </div>
                </div>
                
                @if (get_setting('vendor_system_activation') == 1)
                    <div class="text-center text-md-left mt-4">
                        <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                            {{ translate('Be a Seller') }}
                        </h4>
                        <a href="{{ route('shops.create') }}" class="btn btn-primary btn-sm shadow-md">
                            {{ translate('Apply Now') }}
                        </a>
                    </div>
                @endif
                <ul class="list-inline mb-0">
                        @if ( get_setting('payment_method_images') !=  null )
                            @foreach (explode(',', get_setting('payment_method_images')) as $key => $value)
                                <li class="list-inline-item">
                                    <img src="{{ uploaded_asset($value) }}" height="30" class="mw-100 h-auto" style="width:100%;">
                                </li>
                            @endforeach
                        @endif
						
                    </ul>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<!-- <footer class="pt-3 pb-7 pb-xl-3  text-light" style="background-color: #A73981;"> -->
<footer class=" text-light" style="background-color: #A73981;">
    <div class="container">
        <div class="row align-items-center">
             <div class="col-lg-12">
                <div class="text-center text-md-center copyrght">
                    @php
                        //echo get_setting('frontend_copyright_text');
                    @endphp
					<b>@ Bazar Nao || Design & Developed By <a target="_blank" href="https://fouraxiz.com">4axiz IT Ltd</a></b>
                </div>
            </div>
            <!-- <div class="col-lg-4">
                <ul class="list-inline my-3 my-md-0 social colored text-center">
                    @if ( get_setting('facebook_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('twitter_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('instagram_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('youtube_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('youtube_link') }}" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('linkedin_link') !=  null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('linkedin_link') }}" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                    </li>
                    @endif
                </ul>
            </div> -->
            <!-- <div class="col-lg-4">
                <div class="text-center text-md-right">
                    <ul class="list-inline mb-0">
                        @if ( get_setting('payment_method_images') !=  null )
                            @foreach (explode(',', get_setting('payment_method_images')) as $key => $value)
                                <li class="list-inline-item">
                                    <img src="{{ uploaded_asset($value) }}" height="30" class="mw-100 h-auto" style="max-height: 30px">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div> -->
        </div>
    </div>
</footer>


<div class="aiz-mobile-bottom-nav d-xl-none fixed-bottom bg-white shadow-lg border-top">
    <div class="d-flex justify-content-around align-items-center">
        <a href="{{ route('home') }}" class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['home'],'bg-soft-primary')}}">
            <i class="las la-home la-2x"></i><br>Home
        </a>
        <a href="{{ route('categories.all') }}" class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['categories.all'],'bg-soft-primary')}}">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-list-ul la-2x"></i><br>Category
            </span>
        </a>
        <!-- <a href="{{ route('cart') }}" class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['cart'],'bg-soft-primary')}}">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-shopping-cart la-2x"></i>
                @if(Session::has('cart'))
                    <span class="badge badge-circle badge-primary position-absolute absolute-top-right" id="cart_items_sidenav">{{ count(Session::get('cart'))}}</span>
                @else
                    <span class="badge badge-circle badge-primary position-absolute absolute-top-right" id="cart_items_sidenav">0</span>
                @endif
            </span>
        </a> -->
        <!-- <a href="Javascript:" class="text-reset flex-grow-1 text-center py-3 border-right">
            <span class="d-inline-block position-relative px-2">
            <img src="square_logo.png">
                
            </span>
        </a> -->
        <a href="tel:01305-687613" class="text-reset flex-grow-1 text-center py-3 border-right">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-phone-volume la-2x"></i><br>Call
            </span>
        </a>
        @if (Auth::check())
            @if(isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-reset flex-grow-1 text-center py-2">
                    <span class="avatar avatar-sm d-block mx-auto">
                        @if(Auth::user()->photo != null)
                            <img src="{{ custom_asset(Auth::user()->avatar_original)}}">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}">
                        @endif
                    </span>Account
                </a>
            @else
                <a href="javascript:void(0)" class="text-reset flex-grow-1 text-center py-2 mobile-side-nav-thumb" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav">
                    <span class="avatar avatar-sm d-block mx-auto">
                        @if(Auth::user()->photo != null)
                            <img src="{{ custom_asset(Auth::user()->avatar_original)}}">
                        @else
                            <img src="{{ static_asset('assets/img/avatar-place.png') }}">
                        @endif
                    </span>Account
                </a>
            @endif
        @else
            <a href="{{ route('user.login') }}" class="text-reset flex-grow-1 text-center py-2">
                <span class="avatar avatar-sm d-block mx-auto">
                    <img src="{{ static_asset('assets/img/avatar-place.png') }}">
                </span>Account
            </a>
        @endif
        
    </div>
</div>
@if (Auth::check() && !isAdmin())
    <div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
        <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb"></div>
        <div class="collapse-sidebar bg-white">
            @include('frontend.inc.user_side_nav')
        </div>
    </div>
@endif
