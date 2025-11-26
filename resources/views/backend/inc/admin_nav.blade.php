<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between">
<div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
    <div class="d-xl-none d-flex">
        <div class="aiz-topbar-logo-wrap d-flex align-items-center justify-content-start">
            @php
            $logo = get_setting('header_logo');
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="d-block">
                @if($logo != null)
                <img src="{{ uploaded_asset($logo) }}" class="brand-icon" alt="{{ get_setting('website_name') }}">
                @else
                <img src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon" alt="{{ get_setting('website_name') }}">
                @endif
            </a>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-stretch flex-grow-1">
        <!-- <div class="d-flex justify-content-between align-items-stretch flex-grow-xl-1"> -->
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            <!-- Browse Website -->
            <div class="aiz-topbar-item mr-3">
                <div class="d-flex align-items-center">
                    <a class="btn btn-topbar has-transition btn-icon btn-circle btn-light p-0 hov-bg-primary hov-svg-white d-flex align-items-center justify-content-center"
                        href="{{ route('home')}}" target="_blank" data-toggle="tooltip" data-title="{{ translate('Browse Website') }}">
                        <i class="las la-globe"></i>
                    </a>
                </div>
            </div>

            <!-- POS -->
            @if (\App\Models\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'pos_system')->first()->activated)
            <?php
                $staffs_role_id = \App\Models\Staff::join('users', 'staff.user_id', '=', 'users.id')
                                    ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
                                    ->where('users.id', Auth::user()->id)
                                    ->select('roles.id as roleId', 'roles.name as role_name')
                                    ->first();
            ?>
            @isset($staffs_role_id)
                @if($staffs_role_id->role_name == "Sales Executive")
                    <div class="d-none d-md-flex justify-content-around align-items-center align-items-stretch ml-3">
                        <div class="aiz-topbar-item">
                            <div class="d-flex align-items-center">
                                <a class="btn btn-icon btn-circle btn-light" href="{{ route('poin-of-sales.index') }}" target="_blank" title="{{ translate('Open POS') }}">                
                                    <i class="las la-fax" style="font-size: 24px; color: #4285f4;"></i>
                                </a>
                                <div class="ml-2">
                                    <span class="d-block fs-16 fw-700 text-dark">{{ translate('POS') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endisset
        @endif

            
            <!-- Clear Cache -->
            <div class="aiz-topbar-item mr-3">
                <div class="d-flex align-items-center">
                    <a class="btn btn-topbar has-transition btn-icon btn-circle btn-light p-0 hov-bg-primary hov-svg-white d-flex align-items-center justify-content-center" 
                        href="{{ route('cache.clear') }}" data-toggle="tooltip" data-title="{{ translate('Clear Cache') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path id="_74846e5be5db5b666d3893933be03656" data-name="74846e5be5db5b666d3893933be03656" d="M7.719,8.911H8.9V10.1H7.719v1.185H6.539V10.1H5.36V8.911h1.18V7.726h1.18ZM5.36,13.652h1.18v1.185H5.36v1.185H4.18V14.837H3V13.652H4.18V12.467H5.36Zm13.626-2.763H10.138V10.3a1.182,1.182,0,0,1,1.18-1.185h2.36V2h1.77V9.111h2.36a1.182,1.182,0,0,1,1.18,1.185ZM18.4,18H16.044a9.259,9.259,0,0,0,.582-2.963.59.59,0,1,0-1.18,0A7.69,7.69,0,0,1,14.755,18H12.5a9.259,9.259,0,0,0,.582-2.963.59.59,0,1,0-1.18,0A7.69,7.69,0,0,1,11.216,18H8.958a22.825,22.825,0,0,0,1.163-5.926H18.99A19.124,19.124,0,0,1,18.4,18Z" transform="translate(-3 -2)" fill="#717580"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        {{-- <div class="d-flex d-md-flex justify-content-around align-items-center align-items-stretch">
            <div class="aiz-topbar-item">
                <div class="d-flex align-items-center">
                    <!-- <input type="text" class="form-control" style="width:300px" placeholder="Search by Customer ID/Phone"> -->
                    <form action="{{ route('customers.index') }}" method="GET">
                    <div class="input-group">
                        
                            <input type="text" name="search" class="form-control" style="width:300px;border-color:rgb(53,149,246)" placeholder="Search by Customer ID/Phone">
                            <div class="input-group-btn" style="margin-left:-2px;">
                                <button class="btn btn-primary" type="submit">
                                    <i class="las la-search"></i>
                                </button>
                            </div>
                       
                    </div>
                    </form>
                </div>
            </div>
        </div> --}}
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            @php
            $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', \App\Models\User::where('user_type', 'admin')->first()->id)
            ->where('orders.viewed', 0)
            ->select('orders.id')
            ->distinct()
            ->count();
            $sellers = \App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
            @endphp

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-1">
                            <span class=" position-relative d-inline-block">
                                <i class="las la-bell la-2x"></i>
                                @if($orders > 0 || $sellers > 0)
                                <span class="badge badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                @endif
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-lg py-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                        </div>
                        <ul class="list-group c-scrollbar-light overflow-auto" style="max-height:300px;">

                           
                            @if($sellers > 0)
                            <li class="list-group-item">
                                <a href="{{ route('sellers.index') }}" class="text-reset">
                                    <span class="ml-2">{{translate('New verification request(s)')}}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- language --}}
            @php
            if(Session::has('locale')){
            $locale = Session::get('locale', Config::get('app.locale'));
            }
            else{
            $locale = env('DEFAULT_LANGUAGE');
            }
            @endphp
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown " id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="{{ static_asset('assets/img/flags/'.$locale.'.png') }}" height="11">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">

                        @foreach (\App\Models\Language::all() as $key => $language)
                        <li>
                            <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" class="mr-2">
                                <span class="language">{{ $language->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                @php
                                $avatar_place = static_asset('assets/img/avatar-place.png')
                                @endphp
                                <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" onerror="this.onerror=null;this.src='{{ $avatar_place }}';">
                            </span>
                            {{-- <span class="d-none d-md-block">
                                <span class="d-block fw-500">{{Auth::user()->name}}</span>
                                <span class="d-block small opacity-60">{{Auth::user()->user_type}}</span>
                            </span> --}}
                            @php 
                                $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
                                $warehouseNames = $warehouseIds ? (\App\Models\Warehouse::whereIn('id', $warehouseIds)->pluck('name')) : collect([]);
                                $data['warehousearray'] = $warehouseNames;
                            @endphp

                            <span class="d-none d-md-block">
                                <span class="d-block fw-500">{{ Auth::user()->name }}</span>
                                <span class="d-block small opacity-60 text-capitalize">{{ Auth::user()->user_type }} 
                                    @if($warehouseNames->isNotEmpty())
                                    (<span>{{ $warehouseNames->implode(', ') }}</span>)
                                    @endif
                                </span>                        
                            </span>

                        
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                            <i class="las la-user-circle"></i>
                            <span>{{translate('Profile')}}</span>
                        </a>

                        <a href="{{ route('logout')}}" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i>
                            <span>{{translate('Logout')}}</span>
                        </a>
                    </div>
                </div>
            </div><!-- .aiz-topbar-item -->
        </div>
    </div>
</div><!-- .aiz-topbar -->