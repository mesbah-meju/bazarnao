<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\WearhouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FlashDealController;
use App\Http\Controllers\HappyHourController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DownloadReportController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\CustomerBulkUploadController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\PickupPointController;
use App\Http\Controllers\FireServiceController;
use App\Http\Controllers\PoliceStationController;
use App\Http\Controllers\NotificationController;

Route::get('/clear-cache', [HomeController::class, 'clearCache'])->name('cache.clear');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [HomeController::class, 'admin_dashboard'])->name('admin.dashboard');
    Route::get('/staff', [HomeController::class, 'staff_dashboard'])->name('staff.dashboard');
    Route::get('/staffmycustomerslist', [HomeController::class, 'staff_customers'])->name('staffmycustomerslist');
    Route::get('/staff_customer_ledger', [HomeController::class, 'staff_customer_ledger'])->name('staff_customer_ledger');
    Route::get('/staff_customer_ledger_details', [HomeController::class, 'staff_customer_ledger_details'])->name('staff_customer_ledger_details');
    Route::get('/staff_sales_report', [HomeController::class, 'staff_sales_report'])->name('staff_sales_report');
    Route::get('/staff_product_sales_report', [HomeController::class, 'staff_product_sales_report'])->name('staff_product_sales_report');
    Route::get('/staff_product', [HomeController::class, 'staff_product'])->name('staff_product');
    Route::get('/staff_refund', [HomeController::class, 'staff_refund'])->name('staff_refund');
    Route::get('/customers_comments_complain/{type?}', [HomeController::class, 'customers_comments_complain'])->name('customers_comments_complain');
    Route::get('/staff_delivery_report', [HomeController::class, 'staff_delivery_report'])->name('staff_delivery_report');
    Route::get('/delivery_executive_ledger', [HomeController::class, 'delivery_executive_ledger'])->name('delivery_executive_ledger');
    Route::get('/delivery_executive_collection_payment', [HomeController::class, 'delivery_executive_collection_payment'])->name('delivery_executive_collection_payment.index');
    Route::post('/get_customer_service_order', [HomeController::class, 'get_customer_service_order']);
    Route::post('/get_customer_by_phone', [HomeController::class, 'get_customer_by_phone']);
    Route::post('/get_purchase_details', [HomeController::class, 'get_purchase_details']);
    Route::post('/activity_save', [HomeController::class, 'activity_save'])->name('staff.activity_save');
    Route::post('/customer_service_activity_save', [HomeController::class, 'customer_service_activity_save'])->name('staff.customer_service_activity_save');
    Route::post('/delivery_executive_ledger', [HomeController::class, 'fordelivery_executive_ledger'])->name('staff.delivery_executive_ledger');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {

    Route::resource('categories', CategoryController::class);
    Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::get('/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/featured', [CategoryController::class, 'updateFeatured'])->name('categories.featured');
    Route::post('/categories/featured_category_wise', [CategoryController::class, 'featured_category_wise'])->name('categories.featured_category_wise');

    Route::resource('brands', BrandController::class);
    Route::get('/brands/edit/{id}', [BrandController::class, 'edit'])->name('brands.edit');
    Route::get('/brands/destroy/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/products/import_translation', [ProductController::class, 'import_translation'])->name('products.import_translation');
    Route::get('/products/admin', [ProductController::class, 'admin_products'])->name('products.admin');
    Route::get('/products/seller', [ProductController::class, 'seller_products'])->name('products.seller');
    Route::get('/products/all', [ProductController::class, 'all_products'])->name('products.all');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/admin/{id}/edit', [ProductController::class, 'admin_product_edit'])->name('products.admin.edit');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::get('/products/seller/{id}/edit', [ProductController::class, 'seller_product_edit'])->name('products.seller.edit');
    Route::post('/products/todays_deal', [ProductController::class, 'updateTodaysDeal'])->name('products.todays_deal');
    Route::post('/products/featured', [ProductController::class, 'updateFeatured'])->name('products.featured');
    Route::post('/products/refundable', [ProductController::class, 'updateRefundable'])->name('products.refundable');
    Route::post('/products.outofstock', [ProductController::class, 'updateOutOfStock'])->name('products.outofstock');
    Route::post('/products/get_products_by_subcategory', [ProductController::class, 'get_products_by_subcategory'])->name('products.get_products_by_subcategory');

    //Group Product
    Route::get('/group-products/create', [ProductController::class, 'group_product_create'])->name('group_products.create');
    Route::post('/group-products/list', [ProductController::class, 'group_products_list'])->name('group_products.list');
    Route::post('/group-products/store', [ProductController::class, 'group_products_store'])->name('group_products.store');
    Route::get('/group-products/destroy/{id}', [ProductController::class, 'group_products_destroy'])->name('group_products.destroy');
    Route::get('/group-products/admin/{id}/edit', [ProductController::class, 'admin_group_products_edit'])->name('group_products.admin.edit');
    Route::post('/group-products/group_product_edit', [ProductController::class, 'group_product_edit'])->name('group_products.edit_list');
    Route::post('/group-products/update/{id}', [ProductController::class, 'group_products_update'])->name('group_products.update');


    Route::resource('customers', CustomerController::class);
    Route::get('customers_ban/{customer}', [CustomerController::class, 'ban'])->name('customers.ban');
    Route::get('/customers/login/{id}', [CustomerController::class, 'login'])->name('customers.login');
    Route::get('/customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::get('/customers/destroy/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/Coa/abd', [CustomerController::class, 'customers_coa'])->name('customers.coa');


    Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::post('/newsletter/send', [NewsletterController::class, 'send'])->name('newsletters.send');
    Route::post('/newsletter/test/smtp', [NewsletterController::class, 'testEmail'])->name('test.smtp');
    Route::get('/birthdaywishsms', [NewsletterController::class, 'birthdaywishsms'])->name('birthdaywishsms');
    Route::post('/birthdaysms_store', [NewsletterController::class, 'birthdaysms_store'])->name('birthdaysms_store');
    Route::get('/send_birthday_wish', [NewsletterController::class, 'send_birth_day_wish'])->name('send_birth_day_wish');

    Route::resource('profile', ProfileController::class);

    Route::post('/business-settings/update', [BusinessSettingsController::class, 'update'])->name('business_settings.update');
    Route::post('/business-settings/update/activation', [BusinessSettingsController::class, 'updateActivationSettings'])->name('business_settings.update.activation');
    Route::get('/general-setting', [BusinessSettingsController::class, 'general_setting'])->name('general_setting.index');
    Route::get('/activation', [BusinessSettingsController::class, 'activation'])->name('activation.index');
    Route::post('/payment-activation', [BusinessSettingsController::class, 'updatePaymentActivationSettings'])->name('payment.activation');
    Route::get('/payment-method', [BusinessSettingsController::class, 'payment_method'])->name('payment_method.index');
    Route::get('/file_system', [BusinessSettingsController::class, 'file_system'])->name('file_system.index');
    Route::get('/social-login', [BusinessSettingsController::class, 'social_login'])->name('social_login.index');
    Route::get('/smtp-settings', [BusinessSettingsController::class, 'smtp_settings'])->name('smtp_settings.index');
    Route::get('/google-analytics', [BusinessSettingsController::class, 'google_analytics'])->name('google_analytics.index');
    Route::get('/google-recaptcha', [BusinessSettingsController::class, 'google_recaptcha'])->name('google_recaptcha.index');


    //Facebook Settings
    Route::get('/facebook-chat', [BusinessSettingsController::class, 'facebook_chat'])->name('facebook_chat.index');
    Route::post('/facebook_chat', [BusinessSettingsController::class, 'facebook_chat_update'])->name('facebook_chat.update');
    Route::get('/facebook-comment', [BusinessSettingsController::class, 'facebook_comment'])->name('facebook-comment');
    Route::post('/facebook-comment', [BusinessSettingsController::class, 'facebook_comment_update'])->name('facebook-comment.update');
    Route::post('/facebook_pixel', [BusinessSettingsController::class, 'facebook_pixel_update'])->name('facebook_pixel.update');
    Route::post('/env_key_update', [BusinessSettingsController::class, 'env_key_update'])->name('env_key_update.update');
    Route::post('/payment_method_update', [BusinessSettingsController::class, 'payment_method_update'])->name('payment_method.update');
    Route::post('/google_analytics', [BusinessSettingsController::class, 'google_analytics_update'])->name('google_analytics.update');
    Route::post('/google_recaptcha', [BusinessSettingsController::class, 'google_recaptcha_update'])->name('google_recaptcha.update');
    Route::get('/currency', [CurrencyController::class, 'currency'])->name('currency.index');
    Route::post('/currency/update', [CurrencyController::class, 'updateCurrency'])->name('currency.update');
    Route::post('/your-currency/update', [CurrencyController::class, 'updateYourCurrency'])->name('your_currency.update');
    Route::get('/currency/create', [CurrencyController::class, 'create'])->name('currency.create');
    Route::post('/currency/store', [CurrencyController::class, 'store'])->name('currency.store');
    Route::post('/currency/currency_edit', [CurrencyController::class, 'edit'])->name('currency.edit');
    Route::post('/currency/update_status', [CurrencyController::class, 'update_status'])->name('currency.update_status');
    Route::get('/verification/form', [BusinessSettingsController::class, 'seller_verification_form'])->name('seller_verification_form.index');
    Route::post('/verification/form', [BusinessSettingsController::class, 'seller_verification_form_update'])->name('seller_verification_form.update');
    Route::get('/vendor_commission', [BusinessSettingsController::class, 'vendor_commission'])->name('business_settings.vendor_commission');
    Route::post('/vendor_commission_update', [BusinessSettingsController::class, 'vendor_commission_update'])->name('business_settings.vendor_commission.update');

    Route::resource('/languages', LanguageController::class);
    Route::post('/languages/{id}/update', [LanguageController::class, 'update'])->name('languages.update');
    Route::get('/languages/destroy/{id}', [LanguageController::class, 'destroy'])->name('languages.destroy');
    Route::post('/languages/update_rtl_status', [LanguageController::class, 'update_rtl_status'])->name('languages.update_rtl_status');
    Route::post('/languages/key_value_store', [LanguageController::class, 'key_value_store'])->name('languages.key_value_store');

    // Website setting
    Route::prefix('website')->group(function () {
        Route::view('/header', 'backend.website_settings.header')->name('website.header');
        Route::view('/footer', 'backend.website_settings.footer')->name('website.footer');
        Route::view('/pages', 'backend.website_settings.pages.index')->name('website.pages');
        Route::view('/appearance', 'backend.website_settings.appearance')->name('website.appearance');
        Route::resource('custom-pages', PageController::class);
        Route::get('/custom-pages/edit/{id}', [PageController::class, 'edit'])->name('custom-pages.edit');
        Route::get('/custom-pages/destroy/{id}', [PageController::class, 'destroy'])->name('custom-pages.destroy');
    });

    // Staff Roles
    Route::resource('roles', RoleController::class)->except('edit', 'destroy');
    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles/edit/{id}', 'edit')->name('roles.edit');
        Route::get('/roles/destroy/{id}', 'destroy')->name('roles.destroy');

        // Add Permissiom
        Route::post('/roles/add_permission', 'add_permission')->name('roles.permission');
    });

    Route::resource('staffs', StaffController::class);
    Route::get('/staffs/login/{id}', [StaffController::class, 'login'])->name('staffs.login');
    Route::get('/staffs/target/{id}', [StaffController::class, 'target'])->name('staffs.target');
    Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');
    Route::get('/staffs/coa/abc', [StaffController::class, 'staff_coa'])->name('staffs.coa');

    Route::resource('targets', TargetController::class);
    Route::get('/targets/destroy/{id}', [TargetController::class, 'destroy'])->name('targets.destroy');

    Route::resource('wearhouses', WearhouseController::class);
    Route::get('/wearhouses/edit/{id}', [WearhouseController::class, 'edit'])->name('wearhouses.edit');
    Route::get('/wearhouses/destroy/{id}', [WearhouseController::class, 'destroy'])->name('wearhouses.destroy');

    Route::resource('supplier', SupplierController::class);
    Route::get('/supplier/destroy/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    Route::get('/Suppliers/Coa/abd', [SupplierController::class, 'suppliers_coa'])->name('suppliers.coa');

    Route::resource('flash_deals', FlashDealController::class);
    Route::get('/flash_deals/edit/{id}', [FlashDealController::class, 'edit'])->name('flash_deals.edit');
    Route::get('/flash_deals/destroy/{id}', [FlashDealController::class, 'destroy'])->name('flash_deals.destroy');
    Route::post('/flash_deals/update_status', [FlashDealController::class, 'update_status'])->name('flash_deals.update_status');
    Route::post('/flash_deals/update_featured', [FlashDealController::class, 'update_featured'])->name('flash_deals.update_featured');
    Route::post('/flash_deals/product_discount', [FlashDealController::class, 'product_discount'])->name('flash_deals.product_discount');
    Route::post('/flash_deals/product_discount_edit', [FlashDealController::class, 'product_discount_edit'])->name('flash_deals.product_discount_edit');

    Route::resource('happy_hours', HappyHourController::class);
    Route::get('/happy_hours/edit/{id}', [HappyHourController::class, 'edit'])->name('happy_hours.edit');
    Route::get('/happy_hours/destroy/{id}', [HappyHourController::class, 'destroy'])->name('happy_hours.destroy');
    Route::post('/happy_hours/update_status', [HappyHourController::class, 'update_status'])->name('happy_hours.update_status');
    Route::post('/happy_hours/update_featured', [HappyHourController::class, 'update_featured'])->name('happy_hours.update_featured');
    Route::post('/happy_hours/product_discount', [HappyHourController::class, 'product_discount'])->name('happy_hours.product_discount');
    Route::post('/happy_hours/product_discount_edit', [HappyHourController::class, 'product_discount_edit'])->name('happy_hours.product_discount_edit');
    //Subscribers

    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/destroy/{id}', [SubscriberController::class, 'destroy'])->name('subscriber.destroy');
    Route::get('/purchase_orders', [OrderController::class, 'purchase_orders'])->name('purchase_orders.index');
    Route::get('/add_purchase', [OrderController::class, 'add_purchase'])->name('purchase_orders.add');
    Route::get('/updatePurchasePrice', [OrderController::class, 'updatePurchasePrice'])->name('updatePurchasePrice');
    Route::get('/puracher_edit/{id}', [OrderController::class, 'puracher_edit'])->name('puracher_edit');
    Route::get('purchase_delete/{id}/index', [OrderController::class, 'purchase_delete'])->name('purchase_delete.index');
    Route::post('/puracher_edit_store', [OrderController::class, 'puracher_edit_store'])->name('puracher_edit_store');
    Route::get('/purchase_orders_view/{id}', [OrderController::class, 'purchase_orders_view'])->name('purchase_orders_view');
    Route::get('/purchase_orders/get_details/{id}', [OrderController::class, 'get_purchase_details'])->name('purchase_orders.get_details');
    Route::post('/store_purchase', [OrderController::class, 'store_purchase'])->name('purchase_orders.store');
    Route::post('/orders/purchase_update_payment_status', [OrderController::class, 'purchase_update_payment_status'])->name('orders.purchase_update_payment_status');
    Route::get('/all_orders', [OrderController::class, 'all_orders'])->name('all_orders.index');
    Route::get('/pending_orders', [OrderController::class, 'pending_orders'])->name('pending_orders.index');
    Route::get('/all_orders/{id}/show', [OrderController::class, 'all_orders_show'])->name('all_orders.show');
    Route::get('/all_order_details/{id}/show', [OrderController::class, 'all_order_details'])->name('all_order_details.show');
    Route::get('/orders/destroy_po/{id}', [OrderController::class, 'destroy_po'])->name('orders.destroy_po');
    Route::post('/get_puracher_product', [OrderController::class, 'get_puracher_product'])->name('purchase_orders.get_puracher_product');

    // Inhouse Orders
    Route::get('/inhouse-orders', [OrderController::class, 'admin_orders'])->name('inhouse_orders.index');
    Route::get('/inhouse-orders/{id}/show', [OrderController::class, 'show'])->name('inhouse_orders.show');
    Route::post('/pay_to_seller', [CommissionController::class, 'pay_to_seller'])->name('commissions.pay_to_seller');

    //Reports
    Route::any('/customer_ledger_fix', [ReportController::class, 'customer_ledger_fix'])->name('customer_ledger.fix');
    Route::any('/supplier_ledger_fix', [ReportController::class, 'supplier_ledger_fix'])->name('supplier_ledger.fix');
    Route::any('/customer_ledger', [ReportController::class, 'customer_ledger'])->name('customer_ledger.index');
    Route::get('/customer_ledger_export', [DownloadReportController::class, 'customer_ledg_export'])->name('customer_ledger_export');
    Route::any('/group_child_product_export', [DownloadReportController::class, 'group_child_product_export'])->name('group_child_product_export');
    Route::any('/group_product_export', [DownloadReportController::class, 'group_product_export'])->name('group_product_export');
    Route::get('/product_sales_export', [DownloadReportController::class, 'product_sales_export'])->name('product_sales_export');
    Route::get('/order_status_changer_report_export', 'DownloadReportController@order_status_changer_report_export')->name('order_status_changer_report_export');
    Route::get('/sales_ledger_export', [DownloadReportController::class, 'sales_ledger_export'])->name('sales_ledger_export');
    Route::get('/pos_sales_ledger_export', [DownloadReportController::class, 'pos_sales_ledger_export'])->name('pos_sales_ledger_export');
    Route::get('/supplier_ledger_export', [DownloadReportController::class, 'supplier_ledger_export'])->name('supplier_ledger_export');
    Route::get('/customer-bulk-export', [DownloadReportController::class, 'customerexport'])->name('customer_bulk_export.index');

    Route::any('/customer_ledger_details', [ReportController::class, 'customer_ledger_details'])->name('customer_ledger_details.index');
    Route::any('/referral_details', [ReportController::class, 'referral_details'])->name('referral_details.index');
    Route::any('/supplier_ledger', [ReportController::class, 'supplier_ledger'])->name('supplier_ledger.index');
    Route::any('/supplier_ledger_details', [ReportController::class, 'supplier_ledger_details'])->name('supplier_ledger_details.index');
    Route::any('/salesReport', [ReportController::class, 'salesReport'])->name('salesReport.index');
    Route::any('/POSsalesReport', [ReportController::class, 'POSsalesReport'])->name('POSsalesReport.index');
    Route::get('/PlatformSalesReport/{type?}', [ReportController::class, 'PlatformSalesReport'])->name('PlatformSalesReport.index');
    Route::get('/transfer_list_report/{type?}', [ReportController::class, 'transfer_list_report'])->name('transfer_list_report.index');
    Route::get('/fifo_transfer_list_report/{type?}', [ReportController::class, 'fifo_transfer_list_report'])->name('fifo_transfer_list_report.index');
    Route::any('/employee_performance', [ReportController::class, 'employee_performance'])->name('employee_performance.index');
    Route::any('/employee_performance_delivery_executive', [ReportController::class, 'employee_performance'])->name('employee_performance.index2');
    Route::get('/stock_report', [ReportController::class, 'stock_report'])->name('stock_report.index');
    Route::get('/wearhouse_wise_stock_report', [ReportController::class, 'wearhouse_wise_stock_report'])->name('wearhouse_wise_stock_report.index');
    Route::get('/wearhouse_wise_stock_ledger_report', [ReportController::class, 'wearhouse_wise_stock_ledger_report'])->name('wearhouse_wise_stock_ledger_report.index');
    Route::get('/export_wearhouse_wise_stock_ledger_report', [DownloadReportController::class, 'export_wearhouse_wise_stock_ledger_report'])->name('export_wearhouse_wise_stock_ledger_report');
    Route::get('/monthly_stock_ledger_report', [ReportController::class, 'monthly_stock_ledger_report'])->name('monthly_stock_ledger_report.index');
    Route::get('/product_wise_fifo_report', [ReportController::class, 'product_wise_fifo_report'])->name('product_wise_fifo.report');

    Route::get('/export_monthly_stock_ledger_report', [DownloadReportController::class, 'export_monthly_stock_ledger_report'])->name('export_monthly_stock_ledger_report');
    Route::get('/stock_closing', [ReportController::class, 'stock_closing'])->name('stock_closing');
    Route::get('/product_stock_closing_details/{id}', [ReportController::class, 'product_stock_closing_details'])->name('product_closing.details');

    Route::get('/save_stock_closing', [ReportController::class, 'save_stock_closing'])->name('save_stock_closing');
    Route::get('/product_wise_sales_report/{excel?}', [ReportController::class, 'product_wise_sales_report'])->name('product_wise_sales_report.index');
    Route::get('/number_of_invoice', [ReportController::class, 'number_of_invoice'])->name('number_of_invoice');

    Route::get('/product_history/fifo', [ReportController::class, 'product_fifo_report'])->name('product_fifo_report.index');
    Route::get('/product_history/fifo-details', [ReportController::class, 'product_fifo_detail_report'])->name('product_fifo_detail_report.index');


    Route::get('/product_history/compared_report', [ReportController::class, 'product_history_compared_report'])->name('product_history_compared_report.index');
    Route::get('/product_history_report', [ReportController::class, 'product_history_report'])->name('product_history_report.index');
    Route::get('/product_history/multiple_compared_report', [ReportController::class, 'multiple_product_history_compared_report'])->name('multiple_product_history_compared_report.index');
    Route::get('/product_history/yearly_report', [ReportController::class, 'product_history_yearly_report'])->name('product_history_yearly_report.index');
    Route::get('/sales_history/product_wise_report', [ReportController::class, 'product_wise_sales_history_report'])->name('product_wise_sales_history_report.index');
    Route::get('/sales_history/product_wise_daily_report', [ReportController::class, 'product_wise_daily_sales_history_report'])->name('product_wise_daily_sales_history_report.index');
    Route::get('/product_sales_history/specific_day_report', [ReportController::class, 'product_specific_day_sales_history_report'])->name('product_specific_day_sales_history_report.index');
    Route::get('/report/purchase_report_history', [ReportController::class, 'purchase_report_history'])->name('purchase_report_history.index');
    Route::get('/report/warehouse_sales_compare/{type?}', [ReportController::class, 'warehouse_sales_compare'])->name('warehouse_sales_compare.index');
    Route::get('/report/warehouse_yearly_sales_compare/{type?}', [ReportController::class, 'warehouse_yearly_sales_compare'])->name('warehouse_yearly_sales_compare.index');
    Route::get('/report/warehouse/monthly-sales-report/{type?}', [ReportController::class, 'warehouse_monthly_sales_report'])->name('warehouse_monthly_sales_report');
    Route::get('/report/warehouse/warehouse_stock_summery/{type?}', [ReportController::class, 'warehouse_stock_summery'])->name('warehouse_stock_summery.index');
    Route::get('/monthly-warehouse-stock-summery/{year}', [ReportController::class, 'monthly_warehouse_stock_summery'])->name('monthly_warehouse_stock_summery.index');

    Route::get('/sales_report/yearly-sales-report', [ReportController::class, 'sales_report'])->name('sales_report.index');
    Route::get('/sales_report/monthly-sales-report', [ReportController::class, 'sales_report_monthly'])->name('sales_report_monthly.index');
    Route::get('/sales_report/sales_by_platform/{type?}', [ReportController::class, 'sales_by_platform'])->name('sales_by_platform.index');
    Route::any('/report/product_transfer_summery/{type?}', [ReportController::class, 'product_transfer_summery'])->name('product_transfer_summery.index');
    Route::any('/report/transfer_list_details/{type?}', [ReportController::class, 'transfer_list_details'])->name('transfer_list_details.index');
    Route::any('/report/fifo_transfer_list_details/{type?}', [ReportController::class, 'fifo_transfer_list_details'])->name('fifo_transfer_list_details.index');
    Route::any('/report/compared_report/single_employee_sales_performance/{type?}', [ReportController::class, 'single_employee_sales_performance'])->name('single_employee_sales_performance.index');
    Route::any('/report/compared_report/employee_sales_performance/{type?}', [ReportController::class, 'employee_sales_performance_compare'])->name('employee_sales_performance_compare.index');
    Route::any('/report/compared_report/employee_sales_performance_compare_per_year/{type?}', [ReportController::class, 'employee_sales_performance_compare_per_year'])->name('employee_sales_performance_compare_per_year.index');
    Route::get('/report/detailed_sales_report', [ReportController::class, 'detailed_sales_report'])->name('detailed_sales_report.index');

    Route::any('/sale_profit_report/{type?}', [ReportController::class, 'sale_profit_report'])->name('sale_profit_report.index');
    Route::any('/daily_sale_profit_report', [ReportController::class, 'daily_sale_profit_report'])->name('daily_sale_profit_report.index');
    Route::get('/order_status_changer_report_export', [DownloadReportController::class, 'order_status_changer_report_export'])->name('order_status_changer_report_export');

    Route::get('/order_status_changer_report', [ReportController::class, 'order_status_changer_report'])->name('order_status_changer_report.index');
    Route::get('/seller_sale_report', [ReportController::class, 'seller_sale_report'])->name('seller_sale_report.index');
    Route::get('/wish_report', [ReportController::class, 'wish_report'])->name('wish_report.index');
    // Route::get('/group_child_product_report', [ReportController::class, 'group_child_product_report'])->name('group_child_product_report.index');
    // Route::get('/group_product_report', [ReportController::class, 'group_product_report'])->name('group_product_report.index');
    Route::get('/parent_child_product_report/{type?}', [ReportController::class, 'group_child_product_report'])->name('group_child_product_report.index');
    Route::get('/group_product_report/{type?}', [ReportController::class, 'group_product_report'])->name('group_product_report.index');
    Route::any('group_product/salesReport', [ReportController::class, 'group_product_salesReport'])->name('group_product_salesReport.index');
    Route::get('/customerwishlish/{product_id}', [ReportController::class, 'customerwishlish'])->name('customerwishlish');
    Route::get('/user_search_report', [ReportController::class, 'user_search_report'])->name('user_search_report.index');
    Route::get('/credit_report', [ReportController::class, 'credit_report'])->name('credit_report.index');
    Route::get('/coupon_report', [ReportController::class, 'coupon_report'])->name('coupon_report.index');
    Route::get('/referral_report', [ReportController::class, 'referral_report'])->name('referral_report.index');
    Route::get('/order_duration_time', [ReportController::class, 'order_duration_time'])->name('order_duration_time.index');
    Route::get('/product_categories', [ReportController::class, 'getProducts'])->name('product.categories');

    //Blog Section
    Route::resource('blog-category', 'BlogCategoryController');
    Route::get('/blog-category/destroy/{id}', 'BlogCategoryController@destroy')->name('blog-category.destroy');
    Route::resource('blog', 'BlogController');
    Route::get('/blog/destroy/{id}', 'BlogController@destroy')->name('blog.destroy');
    Route::post('/blog/change-status', 'BlogController@change_status')->name('blog.change-status');

    // Blog Section
    Route::resource('blog-category', BlogCategoryController::class);
    Route::get('/blog-category/destroy/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-category.destroy');
    Route::resource('blog', BlogController::class);
    Route::get('/blog/destroy/{id}', [BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/change-status', [BlogController::class, 'change_status'])->name('blog.change-status');

    // Coupons
    Route::resource('coupon', CouponController::class);
    Route::post('/coupon/get_form', [CouponController::class, 'get_coupon_form'])->name('coupon.get_coupon_form');
    Route::post('/coupon/get_form_edit', [CouponController::class, 'get_coupon_form_edit'])->name('coupon.get_coupon_form_edit');
    Route::get('/coupon/destroy/{id}', [CouponController::class, 'destroy'])->name('coupon.destroy');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/published', [ReviewController::class, 'updatePublished'])->name('reviews.published');

    // Support Ticket
    Route::get('support_ticket/', [SupportTicketController::class, 'admin_index'])->name('support_ticket.admin_index');
    Route::get('support_ticket/{id}/show', [SupportTicketController::class, 'admin_show'])->name('support_ticket.admin_show');
    Route::post('support_ticket/reply', [SupportTicketController::class, 'admin_store'])->name('support_ticket.admin_store');


    // Conversation of Seller Customer
    Route::get('conversations', [ConversationController::class, 'admin_index'])->name('conversations.admin_index');
    Route::get('conversations/{id}/show', [ConversationController::class, 'admin_show'])->name('conversations.admin_show');
    Route::post('/sellers/profile_modal', [SellerController::class, 'profile_modal'])->name('sellers.profile_modal');
    Route::post('/sellers/approved', [SellerController::class, 'updateApproved'])->name('sellers.approved');
    Route::get('/sellers/coa/abc', [SellerController::class, 'seller_coa'])->name('sellers.coa');


    // Attributes
    Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'update']);
    Route::get('/attributes/edit/{id}', [AttributeController::class, 'edit'])->name('attributes.edit');
    Route::get('/attributes/destroy/{id}', [AttributeController::class, 'destroy'])->name('attributes.destroy');

    // Addons
    Route::resource('addons', AddonController::class)->except(['create', 'show', 'edit']);
    Route::post('/addons/activation', [AddonController::class, 'activation'])->name('addons.activation');

    // Customer Bulk Upload
    Route::get('/customer-bulk-upload/index', [CustomerBulkUploadController::class, 'index'])->name('customer_bulk_upload.index');
    Route::post('/bulk-user-upload', [CustomerBulkUploadController::class, 'user_bulk_upload'])->name('bulk_user_upload');
    Route::post('/bulk-customer-upload', [CustomerBulkUploadController::class, 'customer_bulk_file'])->name('bulk_customer_upload');
    Route::get('/user', [CustomerBulkUploadController::class, 'pdf_download_user'])->name('pdf.download_user');

    // Shipping Configuration
    Route::get('/shipping_configuration', [BusinessSettingsController::class, 'shipping_configuration'])->name('shipping_configuration.index');
    Route::post('/shipping_configuration/update', [BusinessSettingsController::class, 'shipping_configuration_update'])->name('shipping_configuration.update');


    // Countries
    Route::resource('countries', CountryController::class);
    Route::post('/countries/status', [CountryController::class, 'updateStatus'])->name('countries.status');

    // Cities
    Route::resource('cities', CityController::class)->except(['create', 'show', 'update', 'store']);
    Route::get('/cities/edit/{id}', [CityController::class, 'edit'])->name('cities.edit');
    Route::get('/cities/destroy/{id}', [CityController::class, 'destroy'])->name('cities.destroy');

    // Areas
    Route::resource('areas', AreaController::class)->except(['create', 'show', 'update', 'store']);
    Route::get('/areas/edit/{id}', [AreaController::class, 'edit'])->name('areas.edit');
    Route::get('/areas/destroy/{id}', [AreaController::class, 'destroy'])->name('areas.destroy');

    // Transfers
    Route::resource('transfer', TransferController::class)->except(['show']);
    Route::get('/transfer/index', [TransferController::class, 'index'])->name('transfer.show');
    Route::get('/transfer/create', [TransferController::class, 'create'])->name('transfer.create');
    Route::get('/transfer/edit/{id}', [TransferController::class, 'edit'])->name('transfer.edit');
    Route::post('/transfer/store', [TransferController::class, 'store'])->name('transfer.store');
    Route::post('/transfer/update/{id}', [TransferController::class, 'update'])->name('transfer.update');
    Route::get('/transfer/approve/{id}', [TransferController::class, 'approve'])->name('transfer.approve');
    Route::get('/transfer/destroy/{id}', [TransferController::class, 'destroy'])->name('transfer.destroy');

    // Purchases
    Route::resource('purchase', PurchaseController::class)->except(['create', 'show', 'update', 'store']);
    Route::get('/purchase/edit/{id}', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::get('/purchase/destroy/{id}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
    Route::get('/purchase/payment/{id}', [PurchaseController::class, 'payment'])->name('purchase.payment');
    Route::post('/purchase/payment/modal', [PurchaseController::class, 'paymentmodal'])->name('purchase.payment.modal');
    Route::get('/warehouse', [PurchaseController::class, 'warehouse'])->name('warehouse.index');
    Route::get('/warehouse/create', [PurchaseController::class, 'createWarehouse'])->name('warehouse.create');
    Route::post('/warehouse/store', [PurchaseController::class, 'storeWarehouse'])->name('warehouse.store');
    Route::get('/warehouse/{id}/edit', [PurchaseController::class, 'editWarehouse'])->name('warehouse.edit');
    Route::post('/warehouse/{id}/update', [PurchaseController::class, 'updateWarehouse'])->name('warehouse.update');



    // uploaded Invoice
    Route::any('/uploaded-invoice/invoice-info', 'AizUploadController@invoice_info')->name('uploaded-invoice.info');
    Route::get('/uploaded-invoice', 'AizUploadController@uploaded_invoice')->name('uploaded-invoice.index');
    Route::get('/uploaded-sales-invoice', 'AizUploadController@uploaded_sales_invoice')->name('uploaded-sales-invoice.index');
    // Define the routes to accept sales_file as a parameter
    Route::get('/uploaded-invoice/create', 'AizUploadController@uploaded_create')->name('uploaded-invoice.create');
    Route::get('/uploaded-invoice/sales/create', 'AizUploadController@uploaded_sales_create')->name('uploaded-sales-invoice.create');

    Route::get('/uploaded-invoice/destroy/{id}', 'AizUploadController@destroy')->name('uploaded-invoice.destroy');
    Route::get('/uploaded', 'AizUploadController@uploadinvoice')->name('uploaded-invoice.upload');

    // uploaded files
    Route::any('/uploaded-files/file-info', 'AizUploadController@file_info')->name('uploaded-files.info');
    Route::resource('/uploaded-files', 'AizUploadController');
    Route::get('/uploaded-files/destroy/{id}', 'AizUploadController@destroy')->name('uploaded-files.destroy');
    Route::get('/uploaded', 'AizUploadController@upload')->name('uploaded-files.upload');


    Route::post('/customer/wallet_refund', 'CustomerController@wallet_refund')->name('customer.wallet_refund');
    Route::post('/customer.creadit_due', 'CustomerController@creadit_due')->name('customer.creadit_due');
});

// Operation Manager Damage
Route::resource('damage', DamageController::class);
Route::get('/damage/edit/{id}', [DamageController::class, 'edit'])->name('damage.edit');
Route::post('/damage/update/{id}', [DamageController::class, 'update'])->name('damage.update');
Route::get('/damage/approve/{id}', [DamageController::class, 'approve'])->name('damage.approve');
Route::get('/damage/destroy/{id}', [DamageController::class, 'destroy'])->name('damage.destroy');
Route::get('/operation_manager_stock_report', [ReportController::class, 'operation_manager_stock_report'])->name('operation_manager_stock_report.index');
Route::get('/operation_sales_report', [ReportController::class, 'operation_sales_report'])->name('operation_sales_report.index');
Route::get('/operation_customer_report', [ReportController::class, 'operation_customer_report'])->name('operation_customer_report.index');

// Purchase Manager
Route::get('/purchase_list', [HomeController::class, 'purchase_list'])->name('purchase_list.index');
Route::get('/purchase_approve/{id}', [HomeController::class, 'purchase_approve'])->name('purchase_approve.index');
Route::get('/damage_approve/{id}', [HomeController::class, 'damage_approve'])->name('damage_approve.index');
Route::get('/damage_list', [HomeController::class, 'damage_list'])->name('damage_list.index');
Route::get('/vendor_list', [HomeController::class, 'vendor_list'])->name('vendor_list.index');
Route::get('/supplier_ledger_for_purchase_manager', [ReportController::class, 'supplier_ledger_for_purchase_manager'])->name('supplier_ledger_for_purchase_manager.index');

// Purchase Executive
Route::get('/purchase_list_for_purchase_executive', [HomeController::class, 'purchase_list_for_purchase_executive'])->name('purchase_list_for_purchase_executive.index');
Route::get('/supplier_ledger_for_purchase_executive', [ReportController::class, 'supplier_ledger_for_purchase_executive'])->name('supplier_ledger_for_purchase_executive.index');
Route::get('/damage_report', [ReportController::class, 'damage_report'])->name('damage_report.index');
Route::get('/product_wise_purchase_report/{type?}', [ReportController::class, 'product_wise_purchase_report'])->name('product_wise_purchase_report.index');
Route::get('/add_purchase_for_purchase_executive', [ReportController::class, 'add_purchase_for_purchase_executive'])->name('add_purchase_for_purchase_executive.index');
Route::get('/vendor_create', [SupplierController::class, 'create'])->name('vendor_create.index');
Route::get('/transfer_list', [TransferController::class, 'transfer_list'])->name('transfer_list.index');
Route::get('/add_transfer', [TransferController::class, 'add_transfer'])->name('add_transfer.index');
Route::get('/purchase_reject/{id}', [HomeController::class, 'purchase_reject'])->name('purchase_reject.index');

// Customer Service
Route::get('/cutomerservice_add_product', [HomeController::class, 'cutomerservice_add_product'])->name('cutomerservice_add_product.index');
Route::get('/cutomerservice_all_orders', [OrderController::class, 'cutomerservice_all_orders'])->name('cutomerservice_all_orders.index');
Route::get('/operation_manager_order', [OrderController::class, 'operation_manager_order'])->name('operation_manager_order.index');
Route::get('/staff_product_list', [ProductController::class, 'staff_product_list'])->name('staff_product_list.index')->middleware(['auth', 'admin']);
Route::get('/staff_product_edit/{id}/edit', [ProductController::class, 'staff_product_edit'])->name('staff_product_edit.index')->middleware(['auth', 'admin']);
Route::get('/staff_order_edit/{id}/edit', [OrderController::class, 'staff_order_edit'])->name('staff_order_edit.index')->middleware(['auth', 'admin']);
Route::get('/staff_order_show/{id}/show', [OrderController::class, 'staff_order_show'])->name('staff_order_show.show');
Route::post('/orders/update_warehouse', [OrderController::class, 'update_warehouse'])->name('orders.update_warehouse');
Route::post('/orders/update_delivery_boy', [OrderController::class, 'update_delivery_boy'])->name('orders.update_delivery_boy');

// Delivery Executive
Route::post('/get_delivery_ledger_by_order', [HomeController::class, 'get_delivery_ledger_by_order'])->middleware(['auth', 'admin']);
Route::get('/delivery_executive_due_collection', [HomeController::class, 'delivery_executive_due_collection'])->middleware(['auth', 'admin']);
Route::post('/due_collection', [HomeController::class, 'due_collection'])->name('due_collection.index')->middleware(['auth', 'admin']);

// Account Executive
Route::get('/delivery_payment_paid/{id}', [HomeController::class, 'delivery_payment_paid'])->name('delivery_payment_paid.index')->middleware(['auth', 'admin']);
Route::get('/account_activity_report', [HomeController::class, 'account_activity_report'])->name('account_activity_report.index')->middleware(['auth', 'admin']);


// Staff info
Route::resource('staffs', StaffController::class);
Route::get('/staffs/login/{id}', [StaffController::class, 'login'])->name('staffs.login');
Route::get('/staffs/target/{id}', [StaffController::class, 'target'])->name('staffs.target');
Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');

// Emergency Contact
// Fire Service
Route::resource('/fire_service', FireServiceController::class);
Route::get('/fire_service', [FireServiceController::class, 'index'])->name('fire_service.index');
Route::get('/fire_service/create', [FireServiceController::class, 'create'])->name('fire_service.create');
Route::get('/fire_service/edit/{id}', [FireServiceController::class, 'edit'])->name('fire_service.edit');
Route::post('/fire_service/update/{id}', [FireServiceController::class, 'update'])->name('fire_service.update');
Route::get('/fire_service/destroy/{id}', [FireServiceController::class, 'destroy'])->name('fire_service.destroy');

// Police Station
Route::resource('/police_station', PoliceStationController::class);
Route::get('/police_station', [PoliceStationController::class, 'index'])->name('police_station.index');
Route::get('/police_station/create', [PoliceStationController::class, 'create'])->name('police_station.create');
Route::get('/police_station/edit/{id}', [PoliceStationController::class, 'edit'])->name('police_station.edit');
Route::post('/police_station/update/{id}', [PoliceStationController::class, 'update'])->name('police_station.update');
Route::get('/police_station/destroy/{id}', [PoliceStationController::class, 'destroy'])->name('police_station.destroy');
//notification
Route::resource('/notifications', NotificationController::class);
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/destroy/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
Route::view('/server-config', 'backend.system.server_status')->name('system_server');

    // Route::get('/warehouse/cash_transfer/create', [NotificationController::class, 'add_warehouse_cash_transfer'])->name('cash_transfer.add');