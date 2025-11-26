<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="d-block text-left">
                <?php if(get_setting('system_logo_white') != null): ?>
                <img class="mw-100" src="<?php echo e(uploaded_asset(get_setting('system_logo_white'))); ?>" class="brand-icon" alt="<?php echo e(get_setting('site_name')); ?>">
                <?php else: ?>
                <img class="mw-100" src="<?php echo e(static_asset('assets/img/logo.png')); ?>" class="brand-icon" alt="<?php echo e(get_setting('site_name')); ?>">
                <?php endif; ?>
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name="" placeholder="<?php echo e(translate('Search in menu')); ?>" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="aiz-side-nav-link">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Dashboard')); ?></span>
                    </a>
                </li>

                <!-- Accounts -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Accounts</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <!-- <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="#">
                                <span class="aiz-side-nav-text">Chart of Account</span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('account.coa_list')); ?>" class="aiz-side-nav-link">
                                    
                                        <span class="aiz-side-nav-text">List Accounts</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('accounts.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Tree Views</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">COA Tree Views</span>
                                    </a>
                                </li>
                            </ul>
                        </li> -->
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('chart-of-accounts.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Chart of Account')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('sub-accounts.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Sub Account List')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('predefined.accounts.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Predefined Accounts')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('financial-years.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Financial Year')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('opening-balances.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Opening Balance')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('debit-vouchers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Debit Voucher')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('credit-vouchers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Credit Voucher')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('contra-vouchers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Contra Voucher')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('journal-vouchers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Journal Voucher')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('bank-reconciliation.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Bank Reconciliation')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="javascript:void(0);" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Bank & Loan')); ?></span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('banks.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Banks')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('loans.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Loans')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('cash-transfers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Cash Transfer')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('supplier-payment.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Supplier Payment')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customer-receive.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Customer Receive')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('vouchers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Voucher Approval')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="javascript:void(0);" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Reports')); ?></span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('cash-book.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Cash Book')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('bank-book.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Bank Book')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('day-book.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Day Book')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('general-ledger.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('General Ledger')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('sub-ledger.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Sub Ledger')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('trial-balance.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Trial Balance')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('income-statement.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Income Statement (Monthly)')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('income-statement-yearly-report.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Income Statement (Yearly)')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('expenditure-statement.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Expenditure Statement')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('profit-loss.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Profit Loss')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('balance-sheet.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Balance Sheet')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('fixed-asset.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Fixed Asset Schedule')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('receipt-payment.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Receipt & Payment')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('bank-reconciliation.report')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Reconciliation Report')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('account.coa_print')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Print Chart of Accoutns')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- POS Addon-->
                <?php if(App\Models\Addon::where('unique_identifier', 'pos_system')->first() != null && App\Models\Addon::where('unique_identifier', 'pos_system')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-tasks aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('POS')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <?php
                            $staffs_role_id = \App\Models\Staff::join('users', 'staff.user_id', '=', 'users.id')
                                                ->leftJoin('roles', 'staff.role_id', '=', 'roles.id')
                                                ->where('users.id', Auth::user()->id)
                                                ->select('roles.id as roleId', 'roles.name as role_name')
                                                ->first();
                        ?>
                        <?php if(isset($staffs_role_id)): ?>
                            <?php if($staffs_role_id->role_name == "Sales Executive"): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('poin-of-sales.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('POS Sale')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('scan-online-order')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Online Order Scan')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(url('https://bazarnao.shop/create_barcode/')); ?>" target="_blank" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Create Barcode')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Product -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Products')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="<?php echo e(route('products.create')); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Add New product')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="<?php echo e(route('group_products.create')); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Add New Group product')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('products.all')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All Products')); ?></span>
                            </a>
                        </li>

                        <?php if(App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('products.seller')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['products.seller', 'products.seller.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Seller Products')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        

                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('product_bulk_upload.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Bulk Import')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('product_bulk_export.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Bulk Export')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('stock_upload')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Opening Stock Import')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('categories.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Category')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('brands.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Brand')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('attributes.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Attribute')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('reviews.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Product Reviews')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Sale -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Sales')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">

                        <?php if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('pending_orders.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['pending_orders.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Pending Orders')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('all_orders.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['all_orders.index', 'all_orders.show'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All Orders')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions))): ?>
                <!-- Purchase -->
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Purchase')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        <?php if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('purchase_orders.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All Purchase')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('purchase_orders.add')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['purchase', 'purchase'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('New Purchase')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('supplier.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['purchase', 'purchase'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All supplier')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('suppliers.coa')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['purchase', 'purchase'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Supplier Coa')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('transfer.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['purchase', 'purchase'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Transfer')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Refund addon -->
                <?php if(App\Models\Addon::where('unique_identifier', 'refund_request')->first() != null && App\Models\Addon::where('unique_identifier', 'refund_request')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-backward aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Refunds')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('refund_requests_all')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['refund_requests_all', 'reason_show'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Refund Requests')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('paid_refund')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Approved Refunds')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('rejected_refund')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Rejected Refunds')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('resolved_request')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Resolved Requests')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('resolved_refund')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Resolved Refunds')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('refund_time_config')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Refund Configuration')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>


                <!-- Customers -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user-friends aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Customers')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Customer list')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customers.coa')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Customers COA')); ?></span>
                            </a>
                        </li>
                        <?php if(App\Models\BusinessSetting::where('type', 'classified_product')->first()->value == 1): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('classified_products')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Classified Products')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customer_packages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Classified Packages')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Sellers -->
                <?php if((Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions))) && App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Sellers')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <?php
                            $sellers = App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                            ?>
                            <a href="<?php echo e(route('sellers.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All Seller')); ?></span>
                                <?php if($sellers > 0): ?><span class="badge badge-info"><?php echo e($sellers); ?></span> <?php endif; ?>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('sellers.coa')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sellers.coa'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Sellers COA')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('sellers.payment_histories')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Payouts')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('withdraw_requests_all')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Payout Requests')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('business_settings.vendor_commission')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Seller Commission')); ?></span>
                            </a>
                        </li>
                        <?php if(App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('seller_packages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Seller Packages')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('seller_verification_form.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Seller Verification Form')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('uploaded-invoice.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['uploaded-invoice.create'])); ?>">
                        <i class="las la-folder-open aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Uploaded Invoice')); ?></span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('uploaded-files.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['uploaded-files.create'])); ?>">
                        <i class="las la-folder-open aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Uploaded Files')); ?></span>
                    </a>
                </li>

                <!-- Reports -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-file-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Reports')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">

                        
                    <?php if(Auth::user()->user_type == 'admin' || in_array('51', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text"><?php echo e(translate('Accounts')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">

                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('credit_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['credit_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Credit Report')); ?></span>
                                </a>
                            </li>
                           
                        </ul>
                    </li>
                    <?php endif; ?>
                    


                     <?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('salesReport.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesReport.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Sales Report')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('POSsalesReport.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['POSsalesReport.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('POS Sales Report')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                   

                     
                     <?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Purchase Report')); ?></span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('product_wise_purchase_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_wise_purchase_report.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Purchase Report')); ?></span>
                                    </a>
                                </li>

                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('purchase_report_history.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['purchase_report_history.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Purchase Report History')); ?></span>
                                    </a>
                                </li>
                            
                                    
                            </ul>
                        </li>
                    <?php endif; ?>
                 

                    <?php if(Auth::user()->user_type == 'admin' || in_array('25', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customer_ledger.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['customer_ledger.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Customer Ledger')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('33', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('supplier_ledger.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['customer_ledger.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Supplier Ledger')); ?></span>
                            </a>
                        </li>
                     <?php endif; ?>

                    

                    
                    <?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text"><?php echo e(translate('Employee Performance')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('employee_performance.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['employee_performance.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Employee Performance')); ?></span>
                                    </a>
                                </li>
                           
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('single_employee_sales_performance.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['single_employee_sales_performance.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Single Employee Sales Performance')); ?></span>
                                    </a>
                                </li>
                           
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('employee_sales_performance_compare.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['employee_sales_performance_compare.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Employee Sales Performance Compare')); ?></span>
                                    </a>
                                </li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    

                     
                     <?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                     <li class="aiz-side-nav-item">
                         <a href="#" class="aiz-side-nav-link">
                             <span class="aiz-side-nav-text"><?php echo e(translate('Platform Sales Report')); ?></span>
                             <span class="aiz-side-nav-arrow"></span>
                         </a>
                         <ul class="aiz-side-nav-list level-2">
                             <li class="aiz-side-nav-item mx-2">
                                 <a href="<?php echo e(route('PlatformSalesReport.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['PlatformSalesReport.index'])); ?>">
                                     <span class="aiz-side-nav-text"><?php echo e(translate('Platform Sales Report')); ?></span>
                                 </a>
                             </li>

                             <li class="aiz-side-nav-item mx-2">
                                 <a href="<?php echo e(route('sales_by_platform.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sales_by_platform.index'])); ?>">
                                     <span class="aiz-side-nav-text"><?php echo e(translate('Sales By Platform Report')); ?></span>
                                 </a>
                             </li>
                         
                                 
                         </ul>
                     </li>
                 <?php endif; ?>
                 


                     
                     <?php if(Auth::user()->user_type == 'admin' || in_array('33', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                     <li class="aiz-side-nav-item">
                         <a href="#" class="aiz-side-nav-link">
                             <span class="aiz-side-nav-text"><?php echo e(translate('Product Transfer Report')); ?></span>
                             <span class="aiz-side-nav-arrow"></span>
                         </a>
                         <ul class="aiz-side-nav-list level-2">
                          
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('product_transfer_summery.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_transfer_summery.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Product Transfer Summary')); ?></span>
                                </a>
                            </li>
                            
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('transfer_list_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['transfer_list_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Product Transfer Details')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('fifo_transfer_list_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['fifo_transfer_list_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('FIFO Transfer Details')); ?></span>
                                </a>
                            </li>

                            <li class="aaiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('transfer_list_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['transfer_list_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Product Transfer Summary Report')); ?></span>
                                </a>
                            </li>
                         </ul>
                     </li>
                     <?php endif; ?>
                     

                       
                    <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text"><?php echo e(translate('Warehouse Base')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                          
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('warehouse_sales_compare.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['warehouse_sales_compare.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Warehouse Sales Compare')); ?></span>
                                </a>
                            </li>
                       
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('warehouse_yearly_sales_compare.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['warehouse_yearly_sales_compare.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Warehouse Yearly Sales Compare')); ?></span>
                                </a>
                            </li>
                           
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('warehouse_stock_summery.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['warehouse_stock_summery.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('WareHouse Stock Summery Report')); ?></span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    
                    
                    <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('product_wise_sales_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_wise_sales_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Product wise sales report')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            
                            <span class="aiz-side-nav-text"><?php echo e(translate('Product Sales History Report')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('product_fifo_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_fifo_report.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Fifo Sales History')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('product_history_yearly_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_history_yearly_report.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Details Sales History')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('product_history_compared_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['product_history_compared_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Compared Sales History')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('multiple_product_history_compared_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['multiple_product_history_compared_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Multiple Products Compared Sales History')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                      
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('sales_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sales_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Sales Summery Report')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item mx-2">
                                <a href="<?php echo e(route('detailed_sales_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['detailed_sales_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Detailed Sales Report')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        </ul>
                    </li>
                    
                    <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('sale_profit_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sale_profit_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Sales Profit report')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('daily_sale_profit_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['daily_sale_profit_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Daily Sales Profit')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('order_status_changer_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['order_status_changer_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Daily Order Activitis Report')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('customers_comments_complain')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['customers_comments_complain'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Comment/Complain')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                       
                       
                        <?php if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('wearhouse_wise_stock_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['wearhouse_wise_stock_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Products Stock Report')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>


                        <?php if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('monthly_stock_ledger_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['monthly_stock_ledger_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Monthly Products Stock Ledger Report')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('stock_closing')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['stock_closing'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Products Stock Closing')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('28', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('credit_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['credit_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Credit Report')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('29', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('order_duration_time.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['order_duration_time.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Order Duration Report')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('30', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('wish_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['wish_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Products wishlist')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                       

                        <?php if(Auth::user()->user_type == 'admin' || in_array('29', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('transfer_list_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['transfer_list_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Product Transfer Details')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                
                                <span class="aiz-side-nav-text"><?php echo e(translate('Group & Child Products')); ?></span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('group_product_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['group_product_report.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Group Products')); ?></span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('group_product_salesReport.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['group_product_salesReport.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Group Products Sales')); ?></span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="<?php echo e(route('group_child_product_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['group_child_product_report.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Child Products')); ?></span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('31', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('user_search_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['user_search_report.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('User Searches')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('35', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('coupon_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['coupon_report'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Coupon Usage')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('36', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('referral_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['referral_report'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Referral Usage')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!--Blog System-->
                <?php if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Blog System')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('blog.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['blog.create', 'blog.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All Posts')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('blog-category.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['blog-category.create', 'blog-category.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Categories')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <!-- marketing -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Marketing')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">

                        <?php if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('birthdaywishsms')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Birth Day Wish')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('flash_deals.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['flash_deals.index', 'flash_deals.create', 'flash_deals.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Flash deals')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('happy_hours.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['flash_deals.index'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Happy Hour')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('coupon.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['coupon.index','coupon.create','coupon.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Coupon')); ?></span>
                            </a>
                        </li>

                        <?php if(App\Models\Addon::where('unique_identifier', 'offer')->first() != null && App\Models\Addon::where('unique_identifier', 'offer')->first()->activated): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('offer.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offers')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('newsletters.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Newsletters')); ?></span>
                            </a>
                        </li>
                        <?php if(App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('sms.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Bulk SMS')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('subscribers.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Subscribers')); ?></span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('notifications.create')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Push Notification')); ?></span>
                            </a>
                        </li>

                    </ul>
                </li>
                <?php endif; ?>

                <!-- Support -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-link aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Support')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <?php
                        $support_ticket = DB::table('tickets')
                        ->where('viewed', 0)
                        ->select('id')
                        ->count();
                        ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('support_ticket.admin_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['support_ticket.admin_index', 'support_ticket.admin_show'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Ticket')); ?></span>
                                <?php if($support_ticket > 0): ?><span class="badge badge-info"><?php echo e($support_ticket); ?></span><?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php
                        $conversation = App\Models\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                        ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('conversations.admin_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['conversations.admin_index', 'conversations.admin_show'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Product Queries')); ?></span>
                                <?php if(count($conversation) > 0): ?>
                                <span class="badge badge-info"><?php echo e(count($conversation)); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Affiliate Addon -->
                <?php if(App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-link aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate System')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('affiliate.configs')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Registration Form')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('affiliate.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Configurations')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('affiliate.users')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['affiliate.users', 'affiliate_users.show_verification_request', 'affiliate_user.payment_history'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Users')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('refferals.users')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Referral Users')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('affiliate.withdraw_requests')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Withdraw Requests')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('affiliate.logs.admin')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Logs')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Offline Payment Addon-->
                <?php if(App\Models\Addon::where('unique_identifier', 'offline_payment')->first() != null && App\Models\Addon::where('unique_identifier', 'offline_payment')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-check-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Offline Payment System')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('manual_payment_methods.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['manual_payment_methods.index', 'manual_payment_methods.create', 'manual_payment_methods.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Manual Payment Methods')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('offline_wallet_recharge_request.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offline Wallet Recharge')); ?></span>
                            </a>
                        </li>
                        <?php if(App\Models\BusinessSetting::where('type', 'classified_product')->first()->value == 1): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('offline_customer_package_payment_request.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offline Customer Package Payments')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated): ?>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('offline_seller_package_payment_request.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offline Seller Package Payments')); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Paytm Addon -->
                <?php if(App\Models\Addon::where('unique_identifier', 'paytm')->first() != null && App\Models\Addon::where('unique_identifier', 'paytm')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-mobile-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Paytm Payment Gateway')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('paytm.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Set Paytm Credentials')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Club Point Addon-->
                <?php if(App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="lab la-btc aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Club Point System')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('club_points.configs')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Club Point Configurations')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('set_product_points')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['set_product_points', 'product_club_point.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Set Product Point')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('club_points.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['club_points.index', 'club_point.details'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('User Points')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <!--OTP addon -->
                <?php if(App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-phone aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('OTP System')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('otp.configconfiguration')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('OTP Configurations')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('otp_credentials.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Set OTP Credentials')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if(App\Models\Addon::where('unique_identifier', 'african_pg')->first() != null && App\Models\Addon::where('unique_identifier', 'african_pg')->first()->activated): ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-phone aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('African Payment Gateway Addon')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('african.configuration')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('African PG Configurations')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('african_credentials.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Set African PG Credentials')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>


                <!--Emergency Contact-->
                <?php if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item ">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Emergency Contact')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('fire_service.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['fire_service.create', 'fire_service.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Fire Service')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('police_station.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['police_station.create', 'police_station.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Police Station')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Website Setup -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-desktop aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Website Setup')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('website.header')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Header')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('website.footer')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Footer')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('website.pages')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Pages')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('website.appearance')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Appearance')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Setup & Configurations -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-dharmachakra aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Setup & Configurations')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('general_setting.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('General Settings')); ?></span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('activation.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Features activation')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('languages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Languages')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('currency.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Currency')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('smtp_settings.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('SMTP Settings')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('payment_method.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Payment Methods')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('file_system.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('File System Configuration')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('social_login.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Social media Logins')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('google_analytics.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Analytics Tools')); ?></span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="javascript:void(0);" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Facebook</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('facebook_chat.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Facebook Chat')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('facebook-comment')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Facebook Comment')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('google_recaptcha.index')); ?>" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Google reCAPTCHA')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('shipping_configuration.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Configuration')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('countries.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['countries.index','countries.edit','countries.update'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Countries')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('cities.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['cities.index','cities.edit','cities.update'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Cities')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('areas.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['areas.index','areas.edit','areas.update'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Areas')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('wearhouses.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['wearhouses.index','wearhouses.edit','wearhouses.update'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Wearhouse')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Staffs -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions))): ?>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user-tie aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Staffs')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('staffs.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('All staffs')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('staffs.coa')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Staffs Coa')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('roles.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Staff permissions')); ?></span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="<?php echo e(route('targets.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['target.index', 'target.create', 'target.edit'])); ?>">
                                <span class="aiz-side-nav-text"><?php echo e(translate('Staff Target')); ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Addon Manager -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions))): ?>
                <!-- <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('addons.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['addons.index', 'addons.create'])); ?>">
                        <i class="las la-wrench aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Addon Manager')); ?></span>
                    </a>
                </li> -->
                <?php endif; ?>
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar --><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/backend/inc/admin_sidenav.blade.php ENDPATH**/ ?>