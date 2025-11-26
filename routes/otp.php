<?php

use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SmsController;

// Verification phone
Route::get('/verification', [OTPVerificationController::class, 'verification'])->name('verification');
Route::post('/verification', [OTPVerificationController::class, 'verify_phone'])->name('verification.submit');
Route::get('/verification/phone/code/resend', [OTPVerificationController::class, 'resend_verificcation_code'])->name('verification.phone.resend');

// Forgot password phone
Route::get('/password/phone/reset', [OTPVerificationController::class, 'show_reset_password_form'])->name('password.phone.form');
Route::post('/password/reset/submit', [OTPVerificationController::class, 'reset_password_with_code'])->name('password.update.phone');

// Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/otp-configuration', [OTPController::class, 'configure_index'])->name('otp.configconfiguration');
    Route::get('/otp-credentials-configuration', [OTPController::class, 'credentials_index'])->name('otp_credentials.index');
    Route::post('/otp-configuration/update/activation', [OTPController::class, 'updateActivationSettings'])->name('otp_configurations.update.activation');
    Route::post('/otp-credentials-update', [OTPController::class, 'update_credentials'])->name('update_credentials');

    // Messaging
    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::post('/sms-send', [SmsController::class, 'send'])->name('sms.send');
});
