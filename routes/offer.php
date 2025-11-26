<?php

use App\Http\Controllers\OfferController;
use App\Http\Controllers\RefundRequestController;

// Admin Panel
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::resource('offer', OfferController::class);
    Route::post('/offer/get_form', [OfferController::class, 'get_offer_form'])->name('offer.get_offer_form');
    Route::post('/offer/get_form_edit', [OfferController::class, 'get_offer_form_edit'])->name('offer.get_offer_form_edit');
    Route::get('/offer/destroy/{id}', [OfferController::class, 'destroy'])->name('offer.destroy');
});

// FrontEnd User panel
Route::group(['middleware' => ['user', 'verified']], function(){
    Route::post('refund-request-send/{id}', [RefundRequestController::class, 'request_store'])->name('refund_request_send');
    Route::get('refund-request', [RefundRequestController::class, 'vendor_index'])->name('vendor_refund_request');
    Route::get('sent-refund-request', [RefundRequestController::class, 'customer_index'])->name('customer_refund_request');
    Route::post('refund-reuest-vendor-approval', [RefundRequestController::class, 'request_approval_vendor'])->name('vendor_refund_approval');
    Route::get('refund-request/{id}', [RefundRequestController::class, 'refund_request_send_page'])->name('refund_request_send_page');
});

Route::group(['middleware' => ['auth']], function(){
    Route::post('/reject-refund-request',[RefundRequestController::class, 'reject_refund_request'])->name('reject_refund_request');
    Route::get('refund-request-reason/{id}', [RefundRequestController::class, 'reason_view'])->name('reason_show');
    Route::get('refund-request-reject-reason/{id}', [RefundRequestController::class, 'reject_reason_view'])->name('reject_reason_show');
});

