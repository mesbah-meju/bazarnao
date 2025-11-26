<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                @if(get_setting('system_logo_white') != null)
                <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @else
                <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @endif
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name="" placeholder="{{ translate('Search in menu') }}" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="{{route('admin.dashboard')}}" class="aiz-side-nav-link">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Dashboard')}}</span>
                    </a>
                </li>

                <!-- Accounts -->
                @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Accounts</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="#">
                                <span class="aiz-side-nav-text">Chart of Account</span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('chart_of_accounts.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">List Accounts</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('accounts.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Tree Views</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('account.subaccount') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Sub Account List</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('predefined.accounts') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Predefined Accounts</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('financial-years.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Financial Year</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('opening-balances.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Opening Balance</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('debit-vouchers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Debit Voucher</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('credit-vouchers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Credit Voucher</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('contra-vouchers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Contra Voucher</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('journal-vouchers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Journal Voucher</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('vouchers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Voucher Approval</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="#">
                                <span class="aiz-side-nav-text">Cash Transfer</span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('warehouse_cash_transfer.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">List</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('cash_transfer.add') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Create</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-file-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Reports') }}</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="#" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">General Ledger</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="#" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Cash Book</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('bank-book.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Bank Book</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('day-book.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Day Book</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('trial-balance.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Trial Balance</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('income-statement.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Income Statement</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="#" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Balance Sheet</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('expenditure-statement.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">Expenditure Statement</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- POS Addon-->
                @if (App\Models\Addon::where('unique_identifier', 'pos_system')->first() != null && App\Models\Addon::where('unique_identifier', 'pos_system')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-tasks aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('POS')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{route('poin-of-sales.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])}}">
                                <span class="aiz-side-nav-text">{{translate('POS Sale')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('scan-online-order')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Online Order Scan')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{url('https://bazarnao.shop/create_barcode/')}}" target="_blank" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Create Barcode')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif

                <!-- Product -->
                @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Products')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="{{route('products.create')}}">
                                <span class="aiz-side-nav-text">{{translate('Add New product')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a class="aiz-side-nav-link" href="{{route('group_products.create')}}">
                                <span class="aiz-side-nav-text">{{translate('Add New Group product')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('products.all')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('All Products') }}</span>
                            </a>
                        </li>

                        @if(App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{route('products.seller')}}" class="aiz-side-nav-link {{ areActiveRoutes(['products.seller', 'products.seller.edit']) }}">
                                <span class="aiz-side-nav-text">{{ translate('Seller Products') }}</span>
                            </a>
                        </li>
                        @endif

                        <li class="aiz-side-nav-item">
                            <a href="{{ route('product_bulk_upload.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Bulk Import') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('product_bulk_export.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Bulk Export')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('stock_upload')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Opening Stock Import')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('categories.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Category')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('brands.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Brand')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('attributes.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Attribute')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('reviews.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Product Reviews')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Sale -->
                @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Sales')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">

                        @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('pending_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['pending_orders.index'])}}">
                                <span class="aiz-side-nav-text">{{translate('Pending Orders')}}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('all_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['all_orders.index', 'all_orders.show'])}}">
                                <span class="aiz-side-nav-text">{{translate('All Orders')}}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
                <!-- Purchase -->
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Purchase')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        @if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{route('purchase_orders.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('All Purchase')}}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('purchase_orders.add') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase', 'purchase'])}}">
                                <span class="aiz-side-nav-text">{{translate('New Purchase')}}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('supplier.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase', 'purchase'])}}">
                                <span class="aiz-side-nav-text">{{translate('All supplier')}}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('transfer.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase', 'purchase'])}}">
                                <span class="aiz-side-nav-text">{{translate('Transfer')}}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Refund addon -->
                @if (App\Models\Addon::where('unique_identifier', 'refund_request')->first() != null && App\Models\Addon::where('unique_identifier', 'refund_request')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-backward aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Refunds') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{route('refund_requests_all')}}" class="aiz-side-nav-link {{ areActiveRoutes(['refund_requests_all', 'reason_show'])}}">
                                <span class="aiz-side-nav-text">{{translate('Refund Requests')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('paid_refund')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Approved Refunds')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('rejected_refund')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Rejected Refunds')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('resolved_request')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Resolved Requests')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('resolved_refund')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Resolved Refunds')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('refund_time_config')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Refund Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif


                <!-- Customers -->
                @if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user-friends aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Customers') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('customers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Customer list') }}</span>
                            </a>
                        </li>
                        @if(App\Models\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{route('classified_products')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Classified Products')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('customer_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Classified Packages') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Sellers -->
                @if((Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions))) && App\Models\BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1)
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Sellers') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            @php
                            $sellers = App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                            @endphp
                            <a href="{{ route('sellers.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])}}">
                                <span class="aiz-side-nav-text">{{ translate('All Seller') }}</span>
                                @if($sellers > 0)<span class="badge badge-info">{{ $sellers }}</span> @endif
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('sellers.payment_histories') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Payouts') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('withdraw_requests_all') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Payout Requests') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('business_settings.vendor_commission') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Seller Commission') }}</span>
                            </a>
                        </li>
                        @if (App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Seller Packages') }}</span>
                            </a>
                        </li>
                        @endif
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller_verification_form.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Seller Verification Form') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="aiz-side-nav-item">
                    <a href="{{ route('uploaded-invoice.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-invoice.create'])}}">
                        <i class="las la-folder-open aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Uploaded Invoice') }}</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('uploaded-files.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-files.create'])}}">
                        <i class="las la-folder-open aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                    </a>
                </li>

                <!-- Reports -->
                @if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-file-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Reports') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">

                        {{-- Accounts Start --}}
                    @if(Auth::user()->user_type == 'admin' || in_array('51', json_decode(Auth::user()->staff->role->permissions)))
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text">{{ translate('Accounts') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">

                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('credit_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['credit_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Credit Report') }}</span>
                                </a>
                            </li>
                           
                        </ul>
                    </li>
                    @endif
                    {{--End Accounts --}}


                     @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('salesReport.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['salesReport.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Sales Report') }}</span>
                            </a>
                        </li>
                    @endif

                    @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('POSsalesReport.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['POSsalesReport.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('POS Sales Report') }}</span>
                            </a>
                        </li>
                    @endif

                    {{-- @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('PlatformSalesReport.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['PlatformSalesReport.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Platform Sales Report') }}</span>
                            </a>
                        </li>
                    @endif --}}

                   

                    {{-- @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('product_wise_purchase_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_wise_purchase_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Purchase Report') }}</span>
                            </a>
                        </li>
                    @endif --}}

                     {{-- Platform Sales Report --}}
                     @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                    
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Purchase Report') }}</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('product_wise_purchase_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_wise_purchase_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Purchase Report') }}</span>
                                    </a>
                                </li>

                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('purchase_report_history.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase_report_history.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Purchase Report History') }}</span>
                                    </a>
                                </li>
                            
                                    
                            </ul>
                        </li>
                    @endif
                 {{--End Platform Sales Report --}}

                    @if(Auth::user()->user_type == 'admin' || in_array('25', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('customer_ledger.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_ledger.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Customer Ledger') }}</span>
                            </a>
                        </li>
                    @endif
                    @if(Auth::user()->user_type == 'admin' || in_array('33', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                            <a href="{{ route('supplier_ledger.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_ledger.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Supplier Ledger') }}</span>
                            </a>
                        </li>
                     @endif

                    {{-- @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('employee_performance.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['employee_performance.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Employee Performance') }}</span>
                            </a>
                        </li>
                    @endif --}}

                    {{-- Employee Performance --}}
                    @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text">{{ translate('Employee Performance') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('employee_performance.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['employee_performance.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Employee Performance') }}</span>
                                    </a>
                                </li>
                           
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('single_employee_sales_performance.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['single_employee_sales_performance.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Single Employee Sales Performance') }}</span>
                                    </a>
                                </li>
                           
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('employee_sales_performance_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['employee_sales_performance_compare.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Employee Sales Performance Compare') }}</span>
                                    </a>
                                </li>
                        </ul>
                    </li>
                    @endif
                    {{--End Employee Performance --}}

                     {{-- Platform Sales Report --}}
                     @if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))
                    
                     <li class="aiz-side-nav-item">
                         <a href="#" class="aiz-side-nav-link">
                             <span class="aiz-side-nav-text">{{ translate('Platform Sales Report') }}</span>
                             <span class="aiz-side-nav-arrow"></span>
                         </a>
                         <ul class="aiz-side-nav-list level-2">
                             <li class="aiz-side-nav-item mx-2">
                                 <a href="{{ route('PlatformSalesReport.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['PlatformSalesReport.index'])}}">
                                     <span class="aiz-side-nav-text">{{ translate('Platform Sales Report') }}</span>
                                 </a>
                             </li>

                             <li class="aiz-side-nav-item mx-2">
                                 <a href="{{ route('sales_by_platform.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sales_by_platform.index'])}}">
                                     <span class="aiz-side-nav-text">{{ translate('Sales By Platform Report') }}</span>
                                 </a>
                             </li>
                         
                                 
                         </ul>
                     </li>
                 @endif
                 {{--End Platform Sales Report --}}


                     {{-- Product Transfer --}}
                     @if(Auth::user()->user_type == 'admin' || in_array('33', json_decode(Auth::user()->staff->role->permissions)))
                    
                     <li class="aiz-side-nav-item">
                         <a href="#" class="aiz-side-nav-link">
                             <span class="aiz-side-nav-text">{{ translate('Product Transfer Report') }}</span>
                             <span class="aiz-side-nav-arrow"></span>
                         </a>
                         <ul class="aiz-side-nav-list level-2">
                          
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('product_transfer_summery.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_transfer_summery.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Product Transfer Summary') }}</span>
                                </a>
                            </li>
                            
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('transfer_list_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['transfer_list_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Product Transfer Details') }}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('fifo_transfer_list_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['fifo_transfer_list_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('FIFO Transfer Details') }}</span>
                                </a>
                            </li>

                            <li class="aaiz-side-nav-item mx-2">
                                <a href="{{ route('transfer_list_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['transfer_list_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Product Transfer Summary Report') }}</span>
                                </a>
                            </li>
                         </ul>
                     </li>
                     @endif
                     {{--End Product Transfer --}}

                       {{-- Warehouse Base --}}
                    @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                    
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <span class="aiz-side-nav-text">{{ translate('Warehouse Base') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                          
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_sales_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_sales_compare.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Warehouse Sales Compare') }}</span>
                                </a>
                            </li>
                       
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_yearly_sales_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_yearly_sales_compare.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Warehouse Yearly Sales Compare') }}</span>
                                </a>
                            </li>
                           
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_stock_summery.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_stock_summery.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('WareHouse Stock Summery Report') }}</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                    @endif
                    {{--End Warehouse Base --}}
                    
                    
                    @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('product_wise_sales_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_wise_sales_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Product wise sales report') }}</span>
                            </a>
                        </li>
                    @endif
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            {{-- <i class="las la-file-alt aiz-side-nav-icon"></i> --}}
                            <span class="aiz-side-nav-text">{{ translate('Product Sales History Report') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('product_history_yearly_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_history_yearly_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Details Sales History') }}</span>
                                    </a>
                                </li>
                            @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('product_history_compared_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_history_compared_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Compared Sales History') }}</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('multiple_product_history_compared_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['multiple_product_history_compared_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Multiple Products Compared Sales History') }}</span>
                                </a>
                            </li>
                        @endif
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('purchase_report_history.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase_report_history.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Purchase Report History') }}</span>
                                </a>
                            </li>
                        @endif --}}
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_sales_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_sales_compare.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Warehouse Sales Compare') }}</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_yearly_sales_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_yearly_sales_compare.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Warehouse Yearly Sales Compare') }}</span>
                                </a>
                            </li>
                        @endif --}}
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('sales_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sales_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Sales Summery Report') }}</span>
                                </a>
                            </li>
                        @endif
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('warehouse_stock_summery.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['warehouse_stock_summery.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('WareHouse Stock Summery Report') }}</span>
                                </a>
                            </li>
                        @endif --}}
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('29', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item mx-2">
                            <a href="{{ route('product_transfer_summery.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_transfer_summery.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Product Transfer Summary') }}</span>
                            </a>
                        </li>
                    @endif --}}
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('sales_by_platform.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sales_by_platform.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Sales By Platform Report') }}</span>
                                </a>
                            </li>
                        @endif --}}
                        {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('single_employee_sales_performance.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['single_employee_sales_performance.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Single Employee Sales Performance') }}</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('employee_sales_performance_compare.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['employee_sales_performance_compare.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Employee Sales Performance Compare') }}</span>
                                </a>
                            </li>
                        @endif --}}
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item mx-2">
                                <a href="{{ route('detailed_sales_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['detailed_sales_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Detailed Sales Report') }}</span>
                                </a>
                            </li>
                        @endif
                        </ul>
                    </li>
                    {{-- @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('product_history_yearly_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['product_history_yearly_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Product Sales History Report') }}</span>
                            </a>
                        </li>
                    @endif --}}
                    @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('sale_profit_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sale_profit_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Sales Profit report') }}</span>
                            </a>
                        </li>
                    @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('daily_sale_profit_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['daily_sale_profit_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Daily Sales Profit') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('order_status_changer_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['order_status_changer_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Daily Order Activitis Report') }}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('26', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{route('customers_comments_complain')}}" class="aiz-side-nav-link {{ areActiveRoutes(['customers_comments_complain'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Comment/Complain') }}</span>
                            </a>
                        </li>
                        @endif
                        <!-- <li class="aiz-side-nav-item">
                            <a href="{{ route('seller_sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_sale_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Seller Products Sale') }}</span>
                            </a>
                        </li> -->
                        @if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions)))
                        <!-- <li class="aiz-side-nav-item">
                                    <a href="{{ route('stock_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Products Stock Report') }}</span>
                                    </a>
                                </li> -->
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('wearhouse_wise_stock_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wearhouse_wise_stock_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Products Stock Report') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- @if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('wearhouse_wise_stock_ledger_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wearhouse_wise_stock_ledger_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Product wise stock ledger report') }}</span>
                                </a>
                            </li>
                        @endif -->


                        @if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('monthly_stock_ledger_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['monthly_stock_ledger_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Monthly Products Stock Ledger Report') }}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('27', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('stock_closing') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_closing'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Products Stock Closing') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('28', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('credit_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['credit_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Credit Report') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('29', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('order_duration_time.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['order_duration_time.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Order Duration Report') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('30', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('wish_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wish_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Products wishlist') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- @if(Auth::user()->name == "Super Admin")
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('transfer_list_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['transfer_list_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Product Transfer Summary Report') }}</span>
                            </a>
                        </li>
                        @else
                        <li class="aiz-side-nav-item" style="display:none">

                        </li>
                        @endif -->

                        @if(Auth::user()->user_type == 'admin' || in_array('29', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('transfer_list_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['transfer_list_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Product Transfer Details') }}</span>
                            </a>
                        </li>
                        @endif

                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                {{-- <i class="las la-file-alt aiz-side-nav-icon"></i> --}}
                                <span class="aiz-side-nav-text">{{ translate('Group & Child Products') }}</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('group_product_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['group_product_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Group Products') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('group_product_salesReport.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['group_product_salesReport.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Group Products Sales') }}</span>
                                    </a>
                                </li>
                                @endif
                                @if(Auth::user()->user_type == 'admin' || in_array('32', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item mx-2">
                                    <a href="{{ route('group_child_product_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['group_child_product_report.index'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Child Products') }}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @if(Auth::user()->user_type == 'admin' || in_array('31', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('user_search_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['user_search_report.index'])}}">
                                <span class="aiz-side-nav-text">{{ translate('User Searches') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('35', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('coupon_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['coupon_report'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Coupon Usage') }}</span>
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('36', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('referral_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['referral_report'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Referral Usage') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!--Blog System-->
                @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Blog System') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('blog.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog.create', 'blog.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('All Posts') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('blog-category.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog-category.create', 'blog-category.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Categories') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- marketing -->
                @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Marketing') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">

                        @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{route('birthdaywishsms')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Birth Day Wish') }}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('flash_deals.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['flash_deals.index', 'flash_deals.create', 'flash_deals.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Flash deals') }}</span>
                            </a>
                        </li>
                        @endif

                        <li class="aiz-side-nav-item">
                            <a href="{{route('coupon.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['coupon.index','coupon.create','coupon.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Coupon') }}</span>
                            </a>
                        </li>

                        @if (App\Models\Addon::where('unique_identifier', 'offer')->first() != null && App\Models\Addon::where('unique_identifier', 'offer')->first()->activated)
                        <li class="aiz-side-nav-item">
                            <a href="{{route('offer.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Offers') }}</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{route('newsletters.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Newsletters') }}</span>
                            </a>
                        </li>
                        @if (App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                        <li class="aiz-side-nav-item">
                            <a href="{{route('sms.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Bulk SMS') }}</span>
                            </a>
                        </li>
                        @endif
                        @endif
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('subscribers.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Subscribers') }}</span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="{{ route('notifications.create') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{ translate('Push Notification') }}</span>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                <!-- Support -->
                @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-link aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Support')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                        @php
                        $support_ticket = DB::table('tickets')
                        ->where('viewed', 0)
                        ->select('id')
                        ->count();
                        @endphp
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('support_ticket.admin_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['support_ticket.admin_index', 'support_ticket.admin_show'])}}">
                                <span class="aiz-side-nav-text">{{translate('Ticket')}}</span>
                                @if($support_ticket > 0)<span class="badge badge-info">{{ $support_ticket }}</span>@endif
                            </a>
                        </li>
                        @endif

                        @php
                        $conversation = App\Models\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                        @endphp
                        @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('conversations.admin_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['conversations.admin_index', 'conversations.admin_show'])}}">
                                <span class="aiz-side-nav-text">{{translate('Product Queries')}}</span>
                                @if (count($conversation) > 0)
                                <span class="badge badge-info">{{ count($conversation) }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Affiliate Addon -->
                @if (App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-link aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Affiliate System')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{route('affiliate.configs')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Affiliate Registration Form')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('affiliate.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Affiliate Configurations')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('affiliate.users')}}" class="aiz-side-nav-link {{ areActiveRoutes(['affiliate.users', 'affiliate_users.show_verification_request', 'affiliate_user.payment_history'])}}">
                                <span class="aiz-side-nav-text">{{translate('Affiliate Users')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('refferals.users')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Referral Users')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('affiliate.withdraw_requests')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Affiliate Withdraw Requests')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('affiliate.logs.admin')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Affiliate Logs')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif

                <!-- Offline Payment Addon-->
                @if (App\Models\Addon::where('unique_identifier', 'offline_payment')->first() != null && App\Models\Addon::where('unique_identifier', 'offline_payment')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-check-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Offline Payment System')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('manual_payment_methods.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['manual_payment_methods.index', 'manual_payment_methods.create', 'manual_payment_methods.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Manual Payment Methods')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('offline_wallet_recharge_request.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Offline Wallet Recharge')}}</span>
                            </a>
                        </li>
                        @if(App\Models\BusinessSetting::where('type', 'classified_product')->first()->value == 1)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('offline_customer_package_payment_request.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Offline Customer Package Payments')}}</span>
                            </a>
                        </li>
                        @endif
                        @if (App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() != null && App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('offline_seller_package_payment_request.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Offline Seller Package Payments')}}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @endif

                <!-- Paytm Addon -->
                @if (App\Models\Addon::where('unique_identifier', 'paytm')->first() != null && App\Models\Addon::where('unique_identifier', 'paytm')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-mobile-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Paytm Payment Gateway')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('paytm.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Set Paytm Credentials')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif

                <!-- Club Point Addon-->
                @if (App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="lab la-btc aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Club Point System')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('club_points.configs') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Club Point Configurations')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('set_product_points')}}" class="aiz-side-nav-link {{ areActiveRoutes(['set_product_points', 'product_club_point.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Set Product Point')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('club_points.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['club_points.index', 'club_point.details'])}}">
                                <span class="aiz-side-nav-text">{{translate('User Points')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif

                <!--OTP addon -->
                @if (App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-phone aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('OTP System')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('otp.configconfiguration') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('OTP Configurations')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('otp_credentials.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Set OTP Credentials')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif

                @if(App\Models\Addon::where('unique_identifier', 'african_pg')->first() != null && App\Models\Addon::where('unique_identifier', 'african_pg')->first()->activated)
                @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-phone aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('African Payment Gateway Addon')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('african.configuration') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('African PG Configurations')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('african_credentials.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Set African PG Credentials')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @endif


                <!--Emergency Contact-->
                @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item ">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-bullhorn aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Emergency Contact') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('fire_service.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['fire_service.create', 'fire_service.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Fire Service') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('police_station.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['police_station.create', 'police_station.edit'])}}">
                                <span class="aiz-side-nav-text">{{ translate('Police Station') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Website Setup -->
                @if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-desktop aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Website Setup')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('website.header') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Header')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('website.footer') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Footer')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('website.pages') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Pages')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('website.appearance') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Appearance')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Setup & Configurations -->
                @if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-dharmachakra aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Setup & Configurations')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{route('general_setting.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('General Settings')}}</span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="{{route('activation.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Features activation')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('languages.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Languages')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('currency.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Currency')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('bank.index')}}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Bank')}}</span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="{{ route('smtp_settings.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('SMTP Settings')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('payment_method.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Payment Methods')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('file_system.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('File System Configuration')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('social_login.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Social media Logins')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('google_analytics.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Analytics Tools')}}</span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="javascript:void(0);" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">Facebook</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-3">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('facebook_chat.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Facebook Chat')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('facebook-comment') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Facebook Comment')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="{{ route('google_recaptcha.index') }}" class="aiz-side-nav-link">
                                <span class="aiz-side-nav-text">{{translate('Google reCAPTCHA')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('shipping_configuration.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                <span class="aiz-side-nav-text">{{translate('Shipping Configuration')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('countries.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['countries.index','countries.edit','countries.update'])}}">
                                <span class="aiz-side-nav-text">{{translate('Shipping Countries')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('cities.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['cities.index','cities.edit','cities.update'])}}">
                                <span class="aiz-side-nav-text">{{translate('Shipping Cities')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('areas.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['areas.index','areas.edit','areas.update'])}}">
                                <span class="aiz-side-nav-text">{{translate('Areas')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('wearhouses.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['wearhouses.index','wearhouses.edit','wearhouses.update'])}}">
                                <span class="aiz-side-nav-text">{{translate('Wearhouse')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Staffs -->
                @if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-user-tie aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Staffs')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('staffs.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('All staffs')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('roles.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Staff permissions')}}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{route('targets.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['target.index', 'target.create', 'target.edit'])}}">
                                <span class="aiz-side-nav-text">{{translate('Staff Target')}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Addon Manager -->
                @if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions)))
                <!-- <li class="aiz-side-nav-item">
                    <a href="{{route('addons.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['addons.index', 'addons.create'])}}">
                        <i class="las la-wrench aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Addon Manager')}}</span>
                    </a>
                </li> -->
                @endif
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->