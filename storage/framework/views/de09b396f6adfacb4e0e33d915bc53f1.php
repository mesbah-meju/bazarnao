<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between">
<div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
    <div class="d-xl-none d-flex">
        <div class="aiz-topbar-logo-wrap d-flex align-items-center justify-content-start">
            <?php
            $logo = get_setting('header_logo');
            ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="d-block">
                <?php if($logo != null): ?>
                <img src="<?php echo e(uploaded_asset($logo)); ?>" class="brand-icon" alt="<?php echo e(get_setting('website_name')); ?>">
                <?php else: ?>
                <img src="<?php echo e(static_asset('assets/img/logo.png')); ?>" class="brand-icon" alt="<?php echo e(get_setting('website_name')); ?>">
                <?php endif; ?>
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
                        href="<?php echo e(route('home')); ?>" target="_blank" data-toggle="tooltip" data-title="<?php echo e(translate('Browse Website')); ?>">
                        <i class="las la-globe"></i>
                    </a>
                </div>
            </div>

            <!-- POS -->
            <?php if(\App\Models\Addon::where('unique_identifier', 'pos_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'pos_system')->first()->activated): ?>
            <?php
                $staffs_role_id = \App\Models\Staff::join('users', 'staff.user_id', '=', 'users.id')
                                    ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
                                    ->where('users.id', Auth::user()->id)
                                    ->select('roles.id as roleId', 'roles.name as role_name')
                                    ->first();
            ?>
            <?php if(isset($staffs_role_id)): ?>
                <?php if($staffs_role_id->role_name == "Sales Executive"): ?>
                    <div class="d-none d-md-flex justify-content-around align-items-center align-items-stretch ml-3">
                        <div class="aiz-topbar-item">
                            <div class="d-flex align-items-center">
                                <a class="btn btn-icon btn-circle btn-light" href="<?php echo e(route('poin-of-sales.index')); ?>" target="_blank" title="<?php echo e(translate('Open POS')); ?>">                
                                    <i class="las la-fax" style="font-size: 24px; color: #4285f4;"></i>
                                </a>
                                <div class="ml-2">
                                    <span class="d-block fs-16 fw-700 text-dark"><?php echo e(translate('POS')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

            
            <!-- Clear Cache -->
            <div class="aiz-topbar-item mr-3">
                <div class="d-flex align-items-center">
                    <a class="btn btn-topbar has-transition btn-icon btn-circle btn-light p-0 hov-bg-primary hov-svg-white d-flex align-items-center justify-content-center" 
                        href="<?php echo e(route('cache.clear')); ?>" data-toggle="tooltip" data-title="<?php echo e(translate('Clear Cache')); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                            <path id="_74846e5be5db5b666d3893933be03656" data-name="74846e5be5db5b666d3893933be03656" d="M7.719,8.911H8.9V10.1H7.719v1.185H6.539V10.1H5.36V8.911h1.18V7.726h1.18ZM5.36,13.652h1.18v1.185H5.36v1.185H4.18V14.837H3V13.652H4.18V12.467H5.36Zm13.626-2.763H10.138V10.3a1.182,1.182,0,0,1,1.18-1.185h2.36V2h1.77V9.111h2.36a1.182,1.182,0,0,1,1.18,1.185ZM18.4,18H16.044a9.259,9.259,0,0,0,.582-2.963.59.59,0,1,0-1.18,0A7.69,7.69,0,0,1,14.755,18H12.5a9.259,9.259,0,0,0,.582-2.963.59.59,0,1,0-1.18,0A7.69,7.69,0,0,1,11.216,18H8.958a22.825,22.825,0,0,0,1.163-5.926H18.99A19.124,19.124,0,0,1,18.4,18Z" transform="translate(-3 -2)" fill="#717580"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            <?php
            $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.seller_id', \App\Models\User::where('user_type', 'admin')->first()->id)
            ->where('orders.viewed', 0)
            ->select('orders.id')
            ->distinct()
            ->count();
            $sellers = \App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
            ?>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-1">
                            <span class=" position-relative d-inline-block">
                                <i class="las la-bell la-2x"></i>
                                <?php if($orders > 0 || $sellers > 0): ?>
                                <span class="badge badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-lg py-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0"><?php echo e(translate('Notifications')); ?></h6>
                        </div>
                        <ul class="list-group c-scrollbar-light overflow-auto" style="max-height:300px;">

                           
                            <?php if($sellers > 0): ?>
                            <li class="list-group-item">
                                <a href="<?php echo e(route('sellers.index')); ?>" class="text-reset">
                                    <span class="ml-2"><?php echo e(translate('New verification request(s)')); ?></span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            
            <?php
            if(Session::has('locale')){
            $locale = Session::get('locale', Config::get('app.locale'));
            }
            else{
            $locale = env('DEFAULT_LANGUAGE');
            }
            ?>
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown " id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="<?php echo e(static_asset('assets/img/flags/'.$locale.'.png')); ?>" height="11">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">

                        <?php $__currentLoopData = \App\Models\Language::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a href="javascript:void(0)" data-flag="<?php echo e($language->code); ?>" class="dropdown-item <?php if($locale == $language->code): ?> active <?php endif; ?>">
                                <img src="<?php echo e(static_asset('assets/img/flags/'.$language->code.'.png')); ?>" class="mr-2">
                                <span class="language"><?php echo e($language->name); ?></span>
                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                <?php
                                $avatar_place = static_asset('assets/img/avatar-place.png')
                                ?>
                                <img src="<?php echo e(uploaded_asset(Auth::user()->avatar_original)); ?>" onerror="this.onerror=null;this.src='<?php echo e($avatar_place); ?>';">
                            </span>
                            
                            <?php 
                                $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
                                $warehouseNames = $warehouseIds ? (\App\Models\Warehouse::whereIn('id', $warehouseIds)->pluck('name')) : collect([]);
                                $data['warehousearray'] = $warehouseNames;
                            ?>

                            <span class="d-none d-md-block">
                                <span class="d-block fw-500"><?php echo e(Auth::user()->name); ?></span>
                                <span class="d-block small opacity-60 text-capitalize"><?php echo e(Auth::user()->user_type); ?> 
                                    <?php if($warehouseNames->isNotEmpty()): ?>
                                    (<span><?php echo e($warehouseNames->implode(', ')); ?></span>)
                                    <?php endif; ?>
                                </span>                        
                            </span>

                        
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="<?php echo e(route('profile.index')); ?>" class="dropdown-item">
                            <i class="las la-user-circle"></i>
                            <span><?php echo e(translate('Profile')); ?></span>
                        </a>

                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i>
                            <span><?php echo e(translate('Logout')); ?></span>
                        </a>
                    </div>
                </div>
            </div><!-- .aiz-topbar-item -->
        </div>
    </div>
</div><!-- .aiz-topbar --><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/inc/admin_nav.blade.php ENDPATH**/ ?>