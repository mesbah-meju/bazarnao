<?php


use App\Http\Controllers\PosController;

Route::get('/pos/products', [PosController::class, 'search'])->name('pos.search_product');
Route::post('/add-to-cart-pos', [PosController::class, 'addToCart'])->name('pos.addToCart');
Route::post('/pos-product-scan', [PosController::class, 'product_scan'])->name('pos.product_scan');
Route::post('/update-quantity-cart-pos', [PosController::class, 'updateQuantity'])->name('pos.updateQuantity');
Route::post('/update-price-cart-pos', [PosController::class, 'updatePrice'])->name('pos.updatePrice');
Route::post('/remove-from-cart-pos', [PosController::class, 'removeFromCart'])->name('pos.removeFromCart');
Route::post('/remove-online-order-details', [PosController::class, 'RemoveOnlineOrderDetails'])->name('pos.RemoveOnlineOrderDetails');
Route::post('/clear-online-order-confirm', [PosController::class, 'ClearOnlineOrderConfirm'])->name('pos.ClearOnlineOrderConfirm');
Route::post('/get_shipping_address', [PosController::class, 'getShippingAddress'])->name('pos.getShippingAddress');
Route::post('/get_shipping_address_seller', [PosController::class, 'getShippingAddressForSeller'])->name('pos.getShippingAddressForSeller');
Route::post('/setDiscount', [PosController::class, 'setDiscount'])->name('pos.setDiscount');
Route::post('/setShipping', [PosController::class, 'setShipping'])->name('pos.setShipping');
Route::post('/set-shipping-address', [PosController::class, 'set_shipping_address'])->name('pos.set-shipping-address');
Route::post('/pos-order-summary', [PosController::class, 'get_order_summary'])->name('pos.getOrderSummary');
Route::post('/pos-order', [PosController::class, 'order_store'])->name('pos.order_place');
Route::get('/pos/gen-invoice/{id}', [PosController::class, 'gen_invoice'])->name('pos.gen_invoice');

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/pos', [PosController::class, 'index'])->name('poin-of-sales.index');
    Route::get('/create-barcode', [PosController::class, 'create_barcode'])->name('create_barcode');
    Route::post('/barcode_list', [PosController::class, 'barcode_list'])->name('barcode_list');
    Route::get('/scan-online-order', [PosController::class, 'scan_online_order'])->name('scan-online-order');
    Route::post('/online-orders-show', [PosController::class, 'online_orders_show'])->name('online_orders.show');
    Route::post('/purchase_withbarcode', [PosController::class, 'purchase_withbarcode'])->name('purchase.withbarcode');
    Route::post('/pos_amount_transfer', [PosController::class, 'pos_amount_transfer'])->name('pos_amount_transfer');
    Route::get('/pos_amount_transfer_accept/{id}', [PosController::class, 'pos_amount_transfer_accept'])->name('pos_amount_transfer_accept');
});

Route::get('products/lims_product_search', [PosController::class, 'limsProductSearch'])->name('product.search');
Route::post('pos/update_online_order_delivery_status/', [PosController::class, 'update_online_order_delivery_status'])->name('pos.update_online_order_delivery_status');
Route::get('/staff_pos_ledger', [PosController::class, 'staff_pos_ledger'])->name('staff_pos_ledger');
