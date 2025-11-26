<?php

use App\Http\Controllers\Api\V2\RefundRequestController;
use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\PasswordResetController;
use App\Http\Controllers\Api\V2\BrandController;
use App\Http\Controllers\Api\V2\CategoryController;
use App\Http\Controllers\Api\V2\BusinessSettingController;
use App\Http\Controllers\Api\V2\SubCategoryController;
use App\Http\Controllers\Api\V2\ColorController;
use App\Http\Controllers\Api\V2\CurrencyController;
use App\Http\Controllers\Api\V2\CustomerController;
use App\Http\Controllers\Api\V2\GeneralSettingController;
use App\Http\Controllers\Api\V2\HomeCategoryController;
use App\Http\Controllers\Api\V2\PurchaseHistoryController;
use App\Http\Controllers\Api\V2\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\V2\ProductController;
use App\Http\Controllers\Api\V2\CartController;
use App\Http\Controllers\Api\V2\CheckoutController;
use App\Http\Controllers\Api\V2\AddressController;
use App\Http\Controllers\Api\V2\PaymentTypesController;
use App\Http\Controllers\Api\V2\ReviewController;
use App\Http\Controllers\Api\V2\ShopController;
use App\Http\Controllers\Api\V2\SliderController;
use App\Http\Controllers\Api\V2\Ad2Controller;
use App\Http\Controllers\Api\V2\Ad3Controller;
use App\Http\Controllers\Api\V2\Ad1Controller;
use App\Http\Controllers\Api\V2\WishlistController;
use App\Http\Controllers\Api\V2\SettingsController;
use App\Http\Controllers\Api\V2\PolicyController;
use App\Http\Controllers\Api\V2\UserController;
use App\Http\Controllers\Api\V2\SupportTicketController;
use App\Http\Controllers\Api\V2\ShippingController;
use App\Http\Controllers\Api\V2\CouponController;
use App\Http\Controllers\Api\V2\BkashController;
use App\Http\Controllers\Api\V2\NagadController;
use App\Http\Controllers\Api\V2\SslCommerzController;
use App\Http\Controllers\Api\V2\WalletController;
use App\Http\Controllers\Api\V2\PaymentController;
use App\Http\Controllers\Api\V2\OrderController;
use App\Http\Controllers\Api\V2\ProfileController;
use App\Http\Controllers\Api\V2\FlashDealController;
use App\Http\Controllers\Api\V2\HappyHourController;
use App\Http\Controllers\Api\V2\BannerController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::middleware(['auth:sanctum', 'user', 'verified'])->group(function () {
        Route::post('refund-request-send/{id}', [RefundRequestController::class, 'request_store'])->name('refund_request_send');
        Route::get('refund-request', [RefundRequestController::class, 'vendor_index'])->name('vendor_refund_request');
        Route::get('sent-refund-request/{id}', [RefundRequestController::class, 'customer_index'])->name('customer_refund_request');
        Route::post('refund-request-vendor-approval', [RefundRequestController::class, 'request_approval_vendor'])->name('vendor_refund_approval');
        Route::get('refund-request/{id}', [RefundRequestController::class, 'refund_request_send_page'])->name('refund_request_send_page');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('reject-refund-request', [RefundRequestController::class, 'reject_refund_request'])->name('reject_refund_request');
        Route::get('refund-request-reason/{id}', [RefundRequestController::class, 'reason_view'])->name('reason_show');
        Route::get('refund-request-reject-reason/{id}', [RefundRequestController::class, 'reject_reason_view'])->name('reject_reason_show');
    });
});




Route::prefix('v2/auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('password/forget_request', [PasswordResetController::class, 'forgetRequest']);
    Route::post('password/confirm_reset', [PasswordResetController::class, 'confirmReset']);
    Route::post('password/resend_code', [PasswordResetController::class, 'resendCode']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });

    Route::post('resend_code', [AuthController::class, 'resendCode']);
    Route::post('confirm_code', [AuthController::class, 'confirmCode']);
});


Route::prefix('v2')->group(function () {
    Route::get('products/wishlist/{id}', [ProductController::class, 'wishListProduct']);
    Route::apiResource('banners', BannerController::class)->only(['index']);
    Route::get('/app-banner', [BannerController::class, 'app_banner'])->name('app_banner');
    Route::get('/referr-link/{slug}', [HomeController::class, 'referr_link'])->name('referr-link');
    Route::get('brands/top', [BrandController::class, 'top']);
    Route::apiResource('brands', BrandController::class)->only(['index']);
    Route::get('top/categories', [CategoryController::class, 'categories_top']);
    Route::apiResource('business-settings', BusinessSettingController::class)->only(['index']);
    Route::get('categories/featured', [CategoryController::class, 'featured']);
    Route::get('categories/home', [CategoryController::class, 'home']);
    Route::get('categories/top', [CategoryController::class, 'top']);
    Route::apiResource('categories', CategoryController::class)->only(['index']);
    Route::get('sub-categories/{id}', [SubCategoryController::class, 'index'])->name('subCategories.index');
    Route::apiResource('colors', ColorController::class)->only(['index']);
    Route::apiResource('currencies', CurrencyController::class)->only(['index']);
    Route::apiResource('customers', CustomerController::class)->only(['show']);
    Route::apiResource('general-settings', GeneralSettingController::class)->only(['index']);
    Route::apiResource('home-categories', HomeCategoryController::class)->only(['index']);
    Route::get('purchase-history/{id}', [PurchaseHistoryController::class, 'index']);
    Route::get('purchase-history-details/{id}', [PurchaseHistoryController::class, 'details'])->name('purchaseHistory.details');
    Route::get('purchase-history-items/{id}', [PurchaseHistoryController::class, 'items']);
    Route::get('filter/categories', [FilterController::class, 'categories']);
    Route::get('filter/brands', [FilterController::class, 'brands']);

    Route::get('products/admin', [ProductController::class, 'admin']);
    Route::get('products/seller/{id}', [ProductController::class, 'seller']);
    Route::get('products/category/{id}', [ProductController::class, 'category'])->name('api.products.category');
    Route::get('products/sub-category/{id}', [ProductController::class, 'subCategory'])->name('products.subCategory');
    Route::get('products/sub-sub-category/{id}', [ProductController::class, 'subSubCategory'])->name('products.subSubCategory');
    Route::get('products/brand/{id}', [ProductController::class, 'brand'])->name('api.products.brand');
    Route::get('products/todays-deal', [ProductController::class, 'todaysDeal']);
    Route::get('products/featured', [ProductController::class, 'featured']);

    Route::get('products/offer-products', [ProductController::class, 'featured']);
    Route::get('products/best-seller', [ProductController::class, 'bestSeller']);
    Route::get('products/related/{id}', [ProductController::class, 'related'])->name('products.related');
    Route::get('products/featured-from-seller/{id}', [ProductController::class, 'newFromSeller'])->name('products.featuredromSeller');
    Route::get('products/search', [ProductController::class, 'search']);
    Route::post('products/variant/price', [ProductController::class, 'variantPrice']);
    Route::get('products/home', [ProductController::class, 'home']);
    Route::get('products/offerList', [ProductController::class, 'offerList']);
    Route::get('products/discountProduct', [ProductController::class, 'discountProduct']);
    Route::apiResource('products', ProductController::class)->except(['store', 'update', 'destroy']);
    Route::get('cart-summary/{user_id}/{owner_id}', [CartController::class, 'summary'])->middleware('auth:sanctum');
    Route::post('carts/process', [CartController::class, 'process'])->middleware('auth:sanctum');
    Route::post('carts/add', [CartController::class, 'add'])->middleware('auth:sanctum');
    Route::post('carts/addMultiple', [CartController::class, 'addMultiple'])->middleware('auth:sanctum');
    Route::post('carts/change-quantity', [CartController::class, 'changeQuantity'])->middleware('auth:sanctum');
    Route::apiResource('carts', CartController::class)->only('destroy')->middleware('auth:sanctum');
    Route::post('carts/{user_id}', [CartController::class, 'getList'])->middleware('auth:sanctum');
    Route::post('cart-remove', [CartController::class, 'cartRemove'])->middleware('auth:sanctum');
    Route::post('coupon-apply', [CheckoutController::class, 'apply_coupon_code'])->middleware('auth:sanctum');
    Route::post('coupon-remove', [CheckoutController::class, 'remove_coupon_code'])->middleware('auth:sanctum');
    Route::post('update-address-in-cart', [AddressController::class, 'updateAddressInCart'])->middleware('auth:sanctum');
    Route::get('payment-types', [PaymentTypesController::class, 'getList']);
    Route::get('reviews/product/{id}', [ReviewController::class, 'index'])->name('api.reviews.index');
    Route::post('reviews/submit', [ReviewController::class, 'submit'])->name('api.reviews.submit');
    Route::get('shop/user/{id}', [ShopController::class, 'shopOfUser'])->middleware('auth:sanctum');
    Route::get('shops/details/{id}', [ShopController::class, 'info'])->name('shops.info');
    Route::get('shops/products/all/{id}', [ShopController::class, 'allProducts'])->name('shops.allProducts');
    Route::get('shops/products/top/{id}', [ShopController::class, 'topSellingProducts'])->name('shops.topSellingProducts');
    Route::get('shops/products/featured/{id}', [ShopController::class, 'featuredProducts'])->name('shops.featuredProducts');
    Route::get('shops/products/new/{id}', [ShopController::class, 'newProducts'])->name('shops.newProducts');
    Route::get('shops/brands/{id}', [ShopController::class, 'brands'])->name('shops.brands');
    Route::apiResource('shops', ShopController::class)->only('index');

    Route::apiResource('sliders', SliderController::class)->only('index');
    Route::apiResource('ad2', Ad2Controller::class)->only('index');
    Route::apiResource('ad3', Ad3Controller::class)->only('index');
    Route::apiResource('ad1', Ad1Controller::class)->only('index');

    Route::get('wishlists-check-product', [WishlistController::class, 'isProductInWishlist'])->middleware('auth:sanctum');
    Route::get('wishlists-add-product', [WishlistController::class, 'add'])->middleware('auth:sanctum');
    Route::get('wishlists-remove-product', [WishlistController::class, 'remove'])->middleware('auth:sanctum');
    Route::get('wishlists/{id}', [WishlistController::class, 'index'])->middleware('auth:sanctum');
    Route::apiResource('wishlists', WishlistController::class)->except(['index', 'update', 'show']);
    Route::apiResource('settings', SettingsController::class)->only('index');
    Route::get('policies/seller', [PolicyController::class, 'sellerPolicy'])->name('policies.seller');
    Route::get('policies/support', [PolicyController::class, 'supportPolicy'])->name('policies.support');
    Route::get('policies/return', [PolicyController::class, 'returnPolicy'])->name('policies.return');
    Route::get('user/info/{id}', [UserController::class, 'info'])->middleware('auth:sanctum');
    Route::post('user/info/update', [UserController::class, 'updateName'])->middleware('auth:sanctum');
    Route::post('user/info/update-info', [UserController::class, 'updateinfo'])->middleware('auth:sanctum');
    Route::get('user/shipping/address/{id}', [AddressController::class, 'addresses'])->middleware('auth:sanctum');
    Route::post('user/shipping/create', [AddressController::class, 'createShippingAddress'])->middleware('auth:sanctum');
    Route::post('user/shipping/update', [AddressController::class, 'updateShippingAddress'])->middleware('auth:sanctum');
    Route::post('user/shipping/make_default', [AddressController::class, 'makeShippingAddressDefault'])->middleware('auth:sanctum');
    Route::get('user/shipping/delete/{id}', [AddressController::class, 'deleteShippingAddress'])->middleware('auth:sanctum');
    Route::post('user/support/create', [SupportTicketController::class, 'store'])->middleware('auth:sanctum');
    Route::post('get-user-by-access_token', [UserController::class, 'getUserInfoByAccessToken']);
    Route::get('cities', [AddressController::class, 'getCities']);
    Route::get('countries', [AddressController::class, 'getCountries']);
    Route::post('shipping_cost', [ShippingController::class, 'shipping_cost'])->middleware('auth:sanctum');
    Route::post('coupon/apply', [CouponController::class, 'apply'])->middleware('auth:sanctum');


    Route::get('bkash/begin', [BkashController::class, 'begin'])->middleware('auth:sanctum');
    Route::get('bkash/api/webpage/{token}/{amount}', [BkashController::class, 'webpage'])->name('api.bkash.webpage');
    Route::any('bkash/api/checkout/{token}/{amount}', [BkashController::class, 'checkout'])->name('api.bkash.checkout');
    Route::any('bkash/api/execute/{token}', [BkashController::class, 'execute'])->name('api.bkash.execute');
    Route::any('bkash/api/fail', [BkashController::class, 'fail'])->name('api.bkash.fail');
    Route::any('bkash/api/success', [BkashController::class, 'success'])->name('api.bkash.success');
    Route::post('bkash/api/process', [BkashController::class, 'process'])->name('api.bkash.process');

    //nagad
    Route::get('nagad/begin', [NagadController::class, 'begin'])->middleware('auth:sanctum');
    Route::any('nagad/verify/{payment_type}', [NagadController::class, 'verify'])->name('app.nagad.callback_url');

    Route::post('nagad/process', [NagadController::class, 'process']);

    //ssl
    Route::get('sslcommerz/begin', [SslCommerzController::class, 'begin']);
    Route::post('sslcommerz/success', [SslCommerzController::class, 'payment_success']);
    Route::post('sslcommerz/fail', [SslCommerzController::class, 'payment_fail']);
    Route::post('sslcommerz/cancel', [SslCommerzController::class, 'payment_cancel']);

    Route::post('payments/pay/wallet', [WalletController::class, 'processPayment'])->middleware('auth:sanctum');
    Route::post('payments/pay/cod', [PaymentController::class, 'cashOnDelivery'])->middleware('auth:sanctum');

    Route::post('order/store', [OrderController::class, 'store'])->middleware('auth:sanctum');
    Route::get('profile/counters/{user_id}', [ProfileController::class, 'counters'])->middleware('auth:sanctum');
    Route::get('profile/getAreaCode', [ProfileController::class, 'getAreaCode']);
    Route::get('profile/getCreditInfo/{user_id}', [ProfileController::class, 'getCreditInfo'])->middleware('auth:sanctum');
    Route::post('profile/update', [ProfileController::class, 'update'])->middleware('auth:sanctum');
    Route::post('profile/update-image', [ProfileController::class, 'updateImage'])->middleware('auth:sanctum');
    Route::post('profile/updateCreditForm', [ProfileController::class, 'updateCreditForm'])->middleware('auth:sanctum');
    Route::post('profile/update-device-token', [ProfileController::class, 'update_device_token'])->middleware('auth:sanctum');
    Route::post('referr-apply', [ProfileController::class, 'apply_referr_code'])->middleware('auth:sanctum');
    Route::get('wallet/balance/{id}', [WalletController::class, 'balance'])->middleware('auth:sanctum');
    Route::get('wallet/history/{id}', [WalletController::class, 'walletRechargeHistory'])->middleware('auth:sanctum');
    Route::get('flash-deals', [FlashDealController::class, 'index']);
    Route::get('happy-hour', [FlashDealController::class, 'happy_hour']);
    Route::get('flash-deal-products/{id}', [FlashDealController::class, 'products']);

    Route::get('happy-hours', [HappyHourController::class, 'index']);
    Route::get('happy-hour-products/{id}', [HappyHourController::class, 'products']);

    Route::get('customer_review', [CustomerController::class, 'customer_review'])->name('customer_review');

    Route::get('ticketList/{id}', [SupportTicketController::class, 'index'])->middleware('auth:sanctum');
    Route::get('support_ticket/{id}/show', [SupportTicketController::class, 'admin_show'])->middleware('auth:sanctum');
    Route::post('support_ticket/reply', [SupportTicketController::class, 'admin_store'])->middleware('auth:sanctum');
    Route::get('cities-by-state/{state_id}', [AddressController::class, 'getCitiesByState']);
    Route::get('cart-count/{id}', [CartController::class, 'count'])->middleware('auth:sanctum');
});

Route::fallback(function () {
    return response()->json([
        'data' => [],
        'success' => false,
        'status' => 404,
        'message' => 'Invalid Route'
    ]);
});
