<?php

use App\Http\Controllers\RefundRequestController;

// Admin Panel
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/refund-request-all', [RefundRequestController::class, 'admin_index'])->name('refund_requests_all');
    Route::get('/refund-request-config', [RefundRequestController::class, 'refund_config'])->name('refund_time_config');
    Route::get('/paid-refund', [RefundRequestController::class, 'paid_index'])->name('paid_refund');
    Route::get('/rejected-refund', [RefundRequestController::class, 'rejected_index'])->name('rejected_refund');
    Route::get('/resolved-request', [RefundRequestController::class, 'resolved_request_index'])->name('resolved_request');
    Route::get('/resolved-refund', [RefundRequestController::class, 'resolved_index'])->name('resolved_refund');
    Route::post('/approve_refund_request', [RefundRequestController::class, 'approve_refund_request'])->name('approve_refund_request');
    Route::post('/delivered_refund_request', [RefundRequestController::class, 'delivered_refund_request'])->name('delivered_refund_request');
    Route::post('/delivery_man_refund_request', [RefundRequestController::class, 'delivery_man_refund_request'])->name('delivery_man_refund_request');
    Route::post('/assain_delivery_boy', [RefundRequestController::class, 'assain_delivery_boy'])->name('assain_delivery_boy');
    Route::post('/refund-request-pay', [RefundRequestController::class, 'refund_pay'])->name('refund_request_money_by_admin');
    Route::post('/refund-request-time-store', [RefundRequestController::class, 'refund_time_update'])->name('refund_request_time_config');
    Route::post('/refund-request-sticker-store', [RefundRequestController::class, 'refund_sticker_update'])->name('refund_sticker_config');
});

// FrontEnd User panel
Route::group(['middleware' => ['user', 'verified']], function () {
    Route::post('refund-request-send/{id}', [RefundRequestController::class, 'request_store'])->name('refund_request_send');
    Route::get('refund-request', [RefundRequestController::class, 'vendor_index'])->name('vendor_refund_request');
    Route::get('sent-refund-request', [RefundRequestController::class, 'customer_index'])->name('customer_refund_request');
    Route::post('refund-reuest-vendor-approval', [RefundRequestController::class, 'request_approval_vendor'])->name('vendor_refund_approval');
    Route::get('refund-request/{id}', [RefundRequestController::class, 'refund_request_send_page'])->name('refund_request_send_page');
});

Route::group(['middleware' => ['auth']], function () {
    Route::Post('/reject-refund-request', [RefundRequestController::class, 'reject_refund_request'])->name('reject_refund_request');
    Route::get('refund-request-reason/{id}', [RefundRequestController::class, 'reason_view'])->name('reason_show');
    Route::get('refund-request-reject-reason/{id}', [RefundRequestController::class, 'reject_reason_view'])->name('reject_reason_show');
});


