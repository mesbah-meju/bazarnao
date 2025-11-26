<style>
.copyrght a{
	color:#FEFF15;
}
</style>
 <?php
                        if(Session::has('locale')){
                        $locale = Session::get('locale', Config::get('app.locale'));
                        }
                        else{
                        $locale = 'en';
                        }
                        ?>

<section class="pb-8 pb-xl-3 text-dark" style="background:#EDEDED !important">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-xl-3 text-center text-md-left">
                <div class="mt-4">
                    <a href="<?php echo e(route('home')); ?>" class="d-block">
                    
                    <?php
                        if($locale=='en')
                        $footer_logo = get_setting('footer_logo');
                        else
                        $footer_logo = get_setting('footer_logo_bangla');
                        ?>
                        <?php if($footer_logo != null): ?>
                            <img class="lazyload" src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>" data-src="<?php echo e(uploaded_asset($footer_logo)); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="44">
                        <?php else: ?>
                            <img class="lazyload" src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>" data-src="<?php echo e(static_asset('assets/img/logo.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="44">
                        <?php endif; ?>
                    </a>
                    <div class="my-3">
                        <?php
                        if($locale=='en')
                            echo get_setting('about_us_description');
                            else
                            echo get_setting('about_us_description_bangla');
                        ?>
                    </div>
                    <div class="d-inline-block d-md-block">
                    
                        <ul class="list-inline my-3 my-md-0 social colored text-center">
                    <?php if( get_setting('facebook_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('facebook_link')); ?>" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('twitter_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('twitter_link')); ?>" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('instagram_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('instagram_link')); ?>" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('youtube_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('youtube_link')); ?>" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('linkedin_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('linkedin_link')); ?>" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="footrt_app_icone list-inline social colored text-center">
                <li><a style="width:100%" target="_blank" href="https://apps.apple.com/us/app/apple-store/id1576185793"><img  src="<?php echo e(url('app.png')); ?>"></a></li>
                <li><a style="width:100%" target="_blank" href="https://play.google.com/store/apps/details?id=com.bazarnao.app"><img  src="<?php echo e(url('play.png')); ?>"></a></li>
                </ul>

                    </div>
                </div>
            </div>
            <div class="col-lg-3  col-md-3 mr-0">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600  pb-2 mb-1">
                        <?php echo e(translate('Contact Info')); ?>

                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                           <span class="d-block"><?php echo e(translate('Address')); ?>:</span>
                           <span class="d-block"><?php echo e(get_setting('contact_address')); ?></span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block"><?php echo e(translate('Phone')); ?>:</span>
                           <span class="d-block"><a href="tel:<?php echo e(get_setting('contact_phone')); ?>"><?php echo e(get_setting('contact_phone')); ?></a></span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block"><?php echo e(translate('Email')); ?>:</span>
                           <span class="d-block">
                               <a href="mailto:<?php echo e(get_setting('contact_email')); ?>" class="text-reset"><?php echo e(get_setting('contact_email')); ?></a>
                            </span>
                        </li>
						 <li class="mb-2">
                             <form class="form-inline" method="POST" action="<?php echo e(route('subscribers.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group" style="width:100%;margin-bottom:5px">
                                <input type="email"  style="width:100%" class="form-control" placeholder="<?php echo e(translate('Your Email Address')); ?>" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-small" style="width:100%">
                                <?php echo e(translate('Subscribe')); ?>

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
                        <?php echo e(translate('My Account')); ?>

                    </h4>
                    <ul class="list-unstyled">
                        <?php if(Auth::check()): ?>
                            <li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('logout')); ?>">
                                    <?php echo e(translate('Logout')); ?>

                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('user.login')); ?>">
                                    <?php echo e(translate('Login')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <a class="text-reset" href="<?php echo e(route('purchase_history.index')); ?>">
                                <?php echo e(translate('Order History')); ?>

                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="text-reset" href="<?php echo e(route('wishlists.index')); ?>">
                                <?php echo e(translate('My Wishlist')); ?>

                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="text-reset" href="<?php echo e(route('orders.track')); ?>">
                                <?php echo e(translate('Track Order')); ?>

                            </a>
                        </li>
                        <li class="mb-2">
                        <a href="blog" class="text-reset">
                        <?php echo e(translate('Blogs')); ?>

                        </a>
                        </li>
                        <li class="mb-2">
                        <a href="companyinfo" class="text-reset">
                        <?php echo e(translate('Company Information')); ?>

                        </a>
                        </li>
                        <?php if(App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated): ?>
                            <!--<li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('affiliate.apply')); ?>"><?php echo e(translate('Be an affiliate partner')); ?></a>
                            </li> -->
                        <?php endif; ?>
                    </ul>
                </div>
                    </div>
                    <div class="col-md-6">   
                    <ul class="list-unstyled mt-4 text-center">
                        
                            <li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('terms')); ?>">
                                    <?php echo e(translate('Terms & conditions')); ?>

                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('returnpolicy')); ?>">
                                    <?php echo e(translate('Return Policy')); ?>

                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="<?php echo e(route('privacypolicy')); ?>">
                                    <?php echo e(translate('Privacy Policy')); ?>

                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="text-reset" href="faq">
                                    <?php echo e(translate('faq')); ?>

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
                
                <?php if(get_setting('vendor_system_activation') == 1): ?>
                    <div class="text-center text-md-left mt-4">
                        <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                            <?php echo e(translate('Be a Seller')); ?>

                        </h4>
                        <a href="<?php echo e(route('shops.create')); ?>" class="btn btn-primary btn-sm shadow-md">
                            <?php echo e(translate('Apply Now')); ?>

                        </a>
                    </div>
                <?php endif; ?>
                <ul class="list-inline mb-0">
                        <?php if( get_setting('payment_method_images') !=  null ): ?>
                            <?php $__currentLoopData = explode(',', get_setting('payment_method_images')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-inline-item">
                                    <img src="<?php echo e(uploaded_asset($value)); ?>" height="30" class="mw-100 h-auto" style="width:100%;">
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
						
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
                    <?php
                        //echo get_setting('frontend_copyright_text');
                    ?>
					<b>@ Bazar Nao || Design & Developed By <a target="_blank" href="https://fouraxiz.com">4axiz IT Ltd</a></b>
                </div>
            </div>
            <!-- <div class="col-lg-4">
                <ul class="list-inline my-3 my-md-0 social colored text-center">
                    <?php if( get_setting('facebook_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('facebook_link')); ?>" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('twitter_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('twitter_link')); ?>" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('instagram_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('instagram_link')); ?>" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('youtube_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('youtube_link')); ?>" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('linkedin_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('linkedin_link')); ?>" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div> -->
            <!-- <div class="col-lg-4">
                <div class="text-center text-md-right">
                    <ul class="list-inline mb-0">
                        <?php if( get_setting('payment_method_images') !=  null ): ?>
                            <?php $__currentLoopData = explode(',', get_setting('payment_method_images')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-inline-item">
                                    <img src="<?php echo e(uploaded_asset($value)); ?>" height="30" class="mw-100 h-auto" style="max-height: 30px">
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div> -->
        </div>
    </div>
</footer>


<div class="aiz-mobile-bottom-nav d-xl-none fixed-bottom bg-white shadow-lg border-top">
    <div class="d-flex justify-content-around align-items-center">
        <a href="<?php echo e(route('home')); ?>" class="text-reset flex-grow-1 text-center py-3 border-right <?php echo e(areActiveRoutes(['home'],'bg-soft-primary')); ?>">
            <i class="las la-home la-2x"></i><br>Home
        </a>
        <a href="<?php echo e(route('categories.all')); ?>" class="text-reset flex-grow-1 text-center py-3 border-right <?php echo e(areActiveRoutes(['categories.all'],'bg-soft-primary')); ?>">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-list-ul la-2x"></i><br>Category
            </span>
        </a>
        <!-- <a href="<?php echo e(route('cart')); ?>" class="text-reset flex-grow-1 text-center py-3 border-right <?php echo e(areActiveRoutes(['cart'],'bg-soft-primary')); ?>">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-shopping-cart la-2x"></i>
                <?php if(Session::has('cart')): ?>
                    <span class="badge badge-circle badge-primary position-absolute absolute-top-right" id="cart_items_sidenav"><?php echo e(count(Session::get('cart'))); ?></span>
                <?php else: ?>
                    <span class="badge badge-circle badge-primary position-absolute absolute-top-right" id="cart_items_sidenav">0</span>
                <?php endif; ?>
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
        <?php if(Auth::check()): ?>
            <?php if(isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-reset flex-grow-1 text-center py-2">
                    <span class="avatar avatar-sm d-block mx-auto">
                        <?php if(Auth::user()->photo != null): ?>
                            <img src="<?php echo e(custom_asset(Auth::user()->avatar_original)); ?>">
                        <?php else: ?>
                            <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>">
                        <?php endif; ?>
                    </span>Account
                </a>
            <?php else: ?>
                <a href="javascript:void(0)" class="text-reset flex-grow-1 text-center py-2 mobile-side-nav-thumb" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav">
                    <span class="avatar avatar-sm d-block mx-auto">
                        <?php if(Auth::user()->photo != null): ?>
                            <img src="<?php echo e(custom_asset(Auth::user()->avatar_original)); ?>">
                        <?php else: ?>
                            <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>">
                        <?php endif; ?>
                    </span>Account
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo e(route('user.login')); ?>" class="text-reset flex-grow-1 text-center py-2">
                <span class="avatar avatar-sm d-block mx-auto">
                    <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>">
                </span>Account
            </a>
        <?php endif; ?>
        
    </div>
</div>
<?php if(Auth::check() && !isAdmin()): ?>
    <div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
        <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb"></div>
        <div class="collapse-sidebar bg-white">
            <?php echo $__env->make('frontend.inc.user_side_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/frontend/inc/footer.blade.php ENDPATH**/ ?>