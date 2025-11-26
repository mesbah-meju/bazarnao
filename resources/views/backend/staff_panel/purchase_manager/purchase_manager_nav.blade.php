<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #A73986;">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                        aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                            <li class="nav-item @if(str_contains(url()->current(), '/staff')) {{'active'}} @endif">
                                <a class="nav-link" href="{{url('staff')}}">Daily Activity</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/purchase_list')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('purchase_list.index')}}">Purchase Approve</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/damage_list')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('damage_list.index')}}">Damage Acceptance</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/transfer_list')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('transfer_list.index')}}">Transfer Approve</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/operation_sales_report')) {{'active'}} @endif">
                            <a class="nav-link" href="{{route('operation_sales_report.index')}}">Sales Report</a>
                           </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/vendor_list')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('vendor_list.index')}}">Vendor Approval</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/supplier_ledger_for_purchase_manager')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('supplier_ledger_for_purchase_manager.index')}}">Supplier Ledger</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/operation_manager_stock_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('operation_manager_stock_report.index')}}">Stock Report</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/damage_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('damage_report.index')}}">Damage Report</a>
                            </li>
                            <li class="nav-item @if(str_contains(url()->current(), '/product_wise_purchase_report')) {{'active'}} @endif">
                                <a class="nav-link" href="{{route('product_wise_purchase_report.index')}}">Product Wise Purchase Report</a>
                            </li>
                        </ul>

                    </div>
                </nav>