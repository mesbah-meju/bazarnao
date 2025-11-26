<?php

use App\Http\Controllers\ClubPointController;

// Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::get('club-points/configuration', [ClubPointController::class, 'configure_index'])->name('club_points.configs');
    Route::get('club-points/index', [ClubPointController::class, 'index'])->name('club_points.index');
    Route::get('set-club-points', [ClubPointController::class, 'set_point'])->name('set_product_points');
    Route::post('set-club-points/store', [ClubPointController::class, 'set_products_point'])->name('set_products_point.store');
    Route::post('set-club-points-for-all_products/store', [ClubPointController::class, 'set_all_products_point'])->name('set_all_products_point.store');
    Route::get('set-club-points/{id}', [ClubPointController::class, 'set_point_edit'])->name('product_club_point.edit');
    Route::get('club-point-details/{id}', [ClubPointController::class, 'club_point_detail'])->name('club_point.details');
    Route::post('set-club-points/update/{id}', [ClubPointController::class, 'update_product_point'])->name('product_point.update');
    Route::post('club-point-convert-rate/store', [ClubPointController::class, 'convert_rate_store'])->name('point_convert_rate_store');
});

// FrontEnd
Route::group(['middleware' => ['user', 'verified']], function(){
    Route::get('earning-points', [ClubPointController::class, 'userpoint_index'])->name('earnng_point_for_user');
    Route::post('convert-point-into-wallet', [ClubPointController::class, 'convert_point_into_wallet'])->name('convert_point_into_wallet');
});


