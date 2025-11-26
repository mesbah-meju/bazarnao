<?php

/*
|--------------------------------------------------------------------------
| Affiliate Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\AffiliateController;

// Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate.index');
    Route::post('/affiliate/affiliate_option_store', [AffiliateController::class, 'affiliate_option_store'])->name('affiliate.store');
    Route::get('/affiliate/configs', [AffiliateController::class, 'configs'])->name('affiliate.configs');
    Route::post('/affiliate/configs/store', [AffiliateController::class, 'config_store'])->name('affiliate.configs.store');
    Route::get('/affiliate/users', [AffiliateController::class, 'users'])->name('affiliate.users');
    Route::get('/affiliate/verification/{id}', [AffiliateController::class, 'show_verification_request'])->name('affiliate_users.show_verification_request');
    Route::get('/affiliate/approve/{id}', [AffiliateController::class, 'approve_user'])->name('affiliate_user.approve');
    Route::get('/affiliate/reject/{id}', [AffiliateController::class, 'reject_user'])->name('affiliate_user.reject');
    Route::post('/affiliate/approved', [AffiliateController::class, 'updateApproved'])->name('affiliate_user.approved');
    Route::post('/affiliate/payment_modal', [AffiliateController::class, 'payment_modal'])->name('affiliate_user.payment_modal');
    Route::post('/affiliate/pay/store', [AffiliateController::class, 'payment_store'])->name('affiliate_user.payment_store');
    Route::get('/affiliate/payments/show/{id}', [AffiliateController::class, 'payment_history'])->name('affiliate_user.payment_history');
    Route::get('/refferal/users', [AffiliateController::class, 'refferal_users'])->name('refferals.users');

    // Affiliate Withdraw Request
    Route::get('/affiliate/withdraw_requests', [AffiliateController::class, 'affiliate_withdraw_requests'])->name('affiliate.withdraw_requests');
    Route::post('/affiliate/affiliate_withdraw_modal', [AffiliateController::class, 'affiliate_withdraw_modal'])->name('affiliate_withdraw_modal');
    Route::post('/affiliate/withdraw_request/payment_store', [AffiliateController::class, 'withdraw_request_payment_store'])->name('withdraw_request.payment_store');
    Route::get('/affiliate/withdraw_request/reject/{id}', [AffiliateController::class, 'reject_withdraw_request'])->name('affiliate.withdraw_request.reject');
    Route::get('/affiliate/logs', [AffiliateController::class, 'affiliate_logs_admin'])->name('affiliate.logs.admin');
});

// FrontEnd
Route::get('/affiliate', [AffiliateController::class, 'apply_for_affiliate'])->name('affiliate.apply');
Route::post('/affiliate/store', [AffiliateController::class, 'store_affiliate_user'])->name('affiliate.store_affiliate_user');
Route::get('/affiliate/user', [AffiliateController::class, 'user_index'])->name('affiliate.user.index');
Route::get('/affiliate/user/payment_history', [AffiliateController::class, 'user_payment_history'])->name('affiliate.user.payment_history');
Route::get('/affiliate/user/withdraw_request_history', [AffiliateController::class, 'user_withdraw_request_history'])->name('affiliate.user.withdraw_request_history');
Route::get('/affiliate/payment/settings', [AffiliateController::class, 'payment_settings'])->name('affiliate.payment_settings');
Route::post('/affiliate/payment/settings/store', [AffiliateController::class, 'payment_settings_store'])->name('affiliate.payment_settings_store');

// Affiliate Withdraw Request
Route::post('/affiliate/withdraw_request/store', [AffiliateController::class, 'withdraw_request_store'])->name('affiliate.withdraw_request.store');
